<?php

/**
 * سحب الأصناف من نفس أعمدة النموذج، من اليمين لليسار:
 * 0 اسم الصنف، 1 كود، 2 الوحدة الاساسية، 3 تكلفة الوحدة، 4 باركود،
 * 5 سعر بيع الوحدة، 6 معامل التحويل، 7 تكلفة الوحدة، 8 باركود الثانية،
 * 9 سعر بيع، 10 المجموعة، 11 التصنيف، 12 المكان، 13 حد الطلب، 14 الحد الاقصى
 */

if (PHP_SAPI !== 'cli') {
    include_once __DIR__ . '/../includes/connect.php';
}

require_once __DIR__ . '/../includes/item_unit_sync.php';

function kody_import_sheet_rows(string $path, string $ext): array
{
    $ext = strtolower($ext);
    if ($ext === 'csv') {
        return kody_import_csv_rows($path);
    }
    if ($ext === 'xlsx') {
        return kody_import_xlsx_rows($path);
    }
    throw new RuntimeException('bad_type');
}

function kody_import_csv_rows(string $path): array
{
    $raw = file_get_contents($path);
    if ($raw === false) {
        throw new RuntimeException('read_failed');
    }
    if (str_starts_with($raw, "\xEF\xBB\xBF")) {
        $raw = substr($raw, 3);
    }
    if (!mb_check_encoding($raw, 'UTF-8')) {
        $converted = iconv('Windows-1256', 'UTF-8//IGNORE', $raw);
        if ($converted !== false) {
            $raw = $converted;
        }
    }
    $firstLine = strtok($raw, "\r\n");
    $comma = substr_count((string) $firstLine, ',');
    $semi = substr_count((string) $firstLine, ';');
    $tab = substr_count((string) $firstLine, "\t");
    $delim = ',';
    if ($semi > $comma && $semi >= $tab) {
        $delim = ';';
    } elseif ($tab > $comma && $tab > $semi) {
        $delim = "\t";
    }
    $handle = fopen('php://memory', 'r+');
    fwrite($handle, $raw);
    rewind($handle);
    $rows = [];
    while (($row = fgetcsv($handle, 0, $delim)) !== false) {
        $rows[] = array_map(static fn($cell) => trim((string) $cell), $row);
    }
    fclose($handle);
    return $rows;
}

function kody_import_xlsx_rows(string $path): array
{
    $zip = new ZipArchive();
    if ($zip->open($path) !== true) {
        throw new RuntimeException('read_failed');
    }
    $shared = [];
    $sharedXml = $zip->getFromName('xl/sharedStrings.xml');
    if ($sharedXml !== false) {
        $shared = kody_import_shared_strings($sharedXml);
    }
    $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
    $zip->close();
    if ($sheetXml === false) {
        throw new RuntimeException('read_failed');
    }

    $dom = new DOMDocument();
    if (!$dom->loadXML($sheetXml)) {
        throw new RuntimeException('read_failed');
    }
    $rows = [];
    foreach ($dom->getElementsByTagName('row') as $rowNode) {
        $cells = [];
        foreach ($rowNode->getElementsByTagName('c') as $cell) {
            $ref = $cell->getAttribute('r');
            if (!preg_match('/^([A-Z]+)/', $ref, $match)) {
                continue;
            }
            $col = kody_import_col_index($match[1]);
            $type = $cell->getAttribute('t');
            $valueNode = $cell->getElementsByTagName('v')->item(0);
            if ($type === 's') {
                $value = $shared[(int) ($valueNode ? $valueNode->textContent : 0)] ?? '';
            } elseif ($type === 'inlineStr') {
                $value = '';
                foreach ($cell->getElementsByTagName('t') as $textNode) {
                    $value .= $textNode->textContent;
                }
            } else {
                $value = $valueNode ? $valueNode->textContent : '';
            }
            $cells[$col] = trim($value);
        }
        if ($cells === []) {
            continue;
        }
        $line = array_fill(0, max(array_keys($cells)) + 1, '');
        foreach ($cells as $index => $value) {
            $line[$index] = $value;
        }
        $rows[] = $line;
    }
    return $rows;
}

function kody_import_shared_strings(string $xmlString): array
{
    $dom = new DOMDocument();
    if (!$dom->loadXML($xmlString)) {
        return [];
    }
    $shared = [];
    foreach ($dom->getElementsByTagName('si') as $item) {
        $shared[] = $item->textContent;
    }
    return $shared;
}

function kody_import_col_index(string $letters): int
{
    $index = 0;
    $len = strlen($letters);
    for ($i = 0; $i < $len; $i++) {
        $index = $index * 26 + (ord($letters[$i]) - 64);
    }
    return $index - 1;
}

function kody_import_cell(array $row, int $index): string
{
    return trim((string) ($row[$index] ?? ''));
}

function kody_import_num(string $value): float
{
    $value = str_replace([',', ' '], '', trim($value));
    if ($value === '' || !is_numeric($value)) {
        return 0.0;
    }
    return (float) $value;
}

function kody_import_ensure_columns(mysqli $conn): void
{
    $have = [];
    $res = $conn->query('SHOW COLUMNS FROM myitems');
    while ($col = $res->fetch_assoc()) {
        $have[$col['Field']] = true;
    }
    if (!isset($have['order_limit'])) {
        $conn->query('ALTER TABLE myitems ADD COLUMN order_limit double NOT NULL DEFAULT 0');
    }
    if (!isset($have['max_qty'])) {
        $conn->query('ALTER TABLE myitems ADD COLUMN max_qty double NOT NULL DEFAULT 0');
    }
}

function kody_import_lookup_id(mysqli $conn, string $sql, string $name): int
{
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $name);
    $stmt->execute();
    $stmt->bind_result($id);
    $found = $stmt->fetch();
    $stmt->close();
    return $found ? (int) $id : 0;
}

function kody_import_unit_id(mysqli $conn, string $name): int
{
    $name = trim($name);
    if ($name === '') {
        $name = 'قطعه';
    }
    $id = kody_import_lookup_id($conn, 'SELECT id FROM myunits WHERE uname = ? LIMIT 1', $name);
    if ($id > 0) {
        return $id;
    }
    $stmt = $conn->prepare('INSERT INTO myunits (uname) VALUES (?)');
    $stmt->bind_param('s', $name);
    $stmt->execute();
    $id = (int) $stmt->insert_id;
    $stmt->close();
    return $id;
}

function kody_import_group_id(mysqli $conn, string $table, string $name): int
{
    $name = trim($name);
    if ($name === '' || !in_array($table, ['item_group', 'item_group2'], true)) {
        return 0;
    }
    $id = kody_import_lookup_id($conn, "SELECT id FROM {$table} WHERE gname = ? LIMIT 1", $name);
    if ($id > 0) {
        return $id;
    }
    $stmt = $conn->prepare("INSERT INTO {$table} (gname) VALUES (?)");
    $stmt->bind_param('s', $name);
    $stmt->execute();
    $id = (int) $stmt->insert_id;
    $stmt->close();
    return $id;
}

function kody_import_other_unit_id(mysqli $conn, int $baseUnitId): int
{
    $stmt = $conn->prepare('SELECT id FROM myunits WHERE id <> ? ORDER BY id LIMIT 1');
    $stmt->bind_param('i', $baseUnitId);
    $stmt->execute();
    $stmt->bind_result($id);
    $found = $stmt->fetch();
    $stmt->close();
    if ($found) {
        return (int) $id;
    }
    return kody_import_unit_id($conn, 'الوحدة الثانية');
}

function kody_import_norm_header(string $value): string
{
    $value = trim($value);
    $value = str_replace(['أ', 'إ', 'آ', 'ى'], ['ا', 'ا', 'ا', 'ي'], $value);
    $value = str_replace('ة', 'ه', $value);
    $value = preg_replace('/\s+/u', ' ', $value) ?? $value;
    return $value;
}

function kody_import_header_map(array $headerRow): array
{
    $exact = [
        'اسم الصنف' => 'iname',
        'الاسم' => 'iname',
        'اسم' => 'iname',
        'كود' => 'code',
        'الكود' => 'code',
        'كود الصنف' => 'code',
        'الوحده الاساسيه' => 'unit',
        'الوحده' => 'unit',
        'وحده' => 'unit',
        'تكلفه الوحده' => 'cost',
        'التكلفه' => 'cost',
        'سعر التكلفه' => 'cost',
        'سعر الشراء' => 'cost',
        'باركود' => 'barcode',
        'الباركود' => 'barcode',
        'باركود الصنف' => 'barcode',
        'سعر بيع الوحده' => 'price',
        'سعر البيع' => 'price',
        'سعر بيع' => 'price',
        'قطاعي' => 'price',
        'معامل التحويل' => 'factor',
        'المعامل' => 'factor',
        'باركود الثانيه' => 'barcode2',
        'باركود ثاني' => 'barcode2',
        'المجموعه' => 'group',
        'التصنيف' => 'category',
        'الصنيف' => 'category',
        'المكان' => 'place',
        'حد الطلب' => 'order',
        'الحد الاقصي' => 'max',
        'الكميه' => 'qty',
        'جمله' => 'wholesale',
        'الجمله' => 'wholesale',
        'السوق' => 'market',
        'سعر السوق' => 'market',
    ];
    $repeat = [
        'cost' => 'cost2',
        'price' => 'sell2',
        'barcode' => 'barcode2',
    ];

    $map = [];
    foreach ($headerRow as $index => $cell) {
        $label = kody_import_norm_header((string) $cell);
        if ($label === '' || $label === '#' || !isset($exact[$label])) {
            continue;
        }
        $key = $exact[$label];
        if (isset($map[$key]) && isset($repeat[$key]) && !isset($map[$repeat[$key]])) {
            $key = $repeat[$key];
        }
        if (!isset($map[$key])) {
            $map[$key] = (int) $index;
        }
    }
    return $map;
}

function kody_import_mapped(array $row, array $map, string $key): string
{
    if (!isset($map[$key])) {
        return '';
    }
    return kody_import_cell($row, $map[$key]);
}

/**
 * @return array{ok:int, skipped:int, errors:array<int, string>}
 */
function kody_import_items(mysqli $conn, array $rows, int $userId): array
{
    kody_import_ensure_columns($conn);

    $ok = 0;
    $skipped = 0;
    $errors = [];
    $map = [];
    $headerAt = null;
    foreach ($rows as $index => $row) {
        if (!is_array($row)) {
            continue;
        }
        $try = kody_import_header_map($row);
        if (isset($try['iname'])) {
            $map = $try;
            $headerAt = $index;
            break;
        }
    }
    if ($headerAt === null) {
        $preview = [];
        foreach (array_slice($rows[0] ?? [], 0, 15) as $cell) {
            $cell = trim((string) $cell);
            if ($cell !== '') {
                $preview[] = $cell;
            }
        }
        $shown = $preview === [] ? 'فارغ' : implode(' | ', $preview);
        return [
            'ok' => 0,
            'skipped' => 0,
            'errors' => [[
                'row' => 1,
                'name' => '',
                'code' => '',
                'barcode' => '',
                'reason' => 'الملف لا يحتوي عمود اسم الصنف أو الاسم. أول صف: ' . $shown,
            ]],
        ];
    }

    $nextCode = (int) ($conn->query('SELECT COALESCE(MAX(code), 0) AS c FROM myitems')->fetch_assoc()['c'] ?? 0);

    foreach ($rows as $rowIndex => $row) {
        if (!is_array($row) || $rowIndex <= $headerAt) {
            continue;
        }
        $lineNo = $rowIndex + 1;
        $iname = kody_import_mapped($row, $map, 'iname');
        if ($iname === '' || $iname === '#' || $iname === 'اسم الصنف' || $iname === 'الاسم') {
            continue;
        }

        $codeRaw = kody_import_mapped($row, $map, 'code');
        $unitName = kody_import_mapped($row, $map, 'unit');
        $cost = kody_import_num(kody_import_mapped($row, $map, 'cost'));
        $barcode = kody_import_mapped($row, $map, 'barcode');
        $price = kody_import_num(kody_import_mapped($row, $map, 'price'));
        $factor = kody_import_num(kody_import_mapped($row, $map, 'factor'));
        $cost2 = kody_import_num(kody_import_mapped($row, $map, 'cost2'));
        $barcode2 = kody_import_mapped($row, $map, 'barcode2');
        $price2sell = kody_import_num(kody_import_mapped($row, $map, 'sell2'));
        $groupName = kody_import_mapped($row, $map, 'group');
        $categoryName = kody_import_mapped($row, $map, 'category');
        $place = kody_import_mapped($row, $map, 'place');
        $orderLimit = kody_import_num(kody_import_mapped($row, $map, 'order'));
        $maxQty = kody_import_num(kody_import_mapped($row, $map, 'max'));
        $qty = kody_import_num(kody_import_mapped($row, $map, 'qty'));
        $wholesale = kody_import_num(kody_import_mapped($row, $map, 'wholesale'));
        $market = kody_import_num(kody_import_mapped($row, $map, 'market'));
        $price2 = $wholesale > 0 ? $wholesale : 0.0;

        $code = $codeRaw === '' ? 0 : (int) $codeRaw;
        if ($code < 1) {
            $nextCode++;
            $code = $nextCode;
        } elseif ($code > $nextCode) {
            $nextCode = $code;
        }

        try {
            $conn->begin_transaction();

            $existingId = kody_import_lookup_id($conn, 'SELECT id FROM myitems WHERE iname = ? LIMIT 1', $iname);
            if ($barcode !== '' && kody_import_barcode_taken($conn, $barcode, $existingId)) {
                throw new RuntimeException('duplicate_barcode');
            }
            if ($barcode2 !== '' && ($barcode2 === $barcode || kody_import_barcode_taken($conn, $barcode2, $existingId))) {
                throw new RuntimeException('duplicate_barcode');
            }

            $group1 = kody_import_group_id($conn, 'item_group', $groupName);
            $group2 = kody_import_group_id($conn, 'item_group2', $categoryName);

            if ($existingId > 0) {
                $itemId = $existingId;
                $stmt = $conn->prepare(
                    'UPDATE myitems SET code = ?, barcode = ?, info = ?, cost_price = ?, price1 = ?, price2 = ?, market_price = ?, group1 = ?, group2 = ?, order_limit = ?, max_qty = ?, itmqty = ?, isdeleted = 0, user = ? WHERE id = ?'
                );
                $stmt->bind_param(
                    'issddddiidddii',
                    $code,
                    $barcode,
                    $place,
                    $cost,
                    $price,
                    $price2,
                    $market,
                    $group1,
                    $group2,
                    $orderLimit,
                    $maxQty,
                    $qty,
                    $userId,
                    $itemId
                );
                $stmt->execute();
                $stmt->close();
            } else {
                $stmt = $conn->prepare(
                    'INSERT INTO myitems (iname, code, barcode, info, cost_price, price1, price2, market_price, group1, group2, order_limit, max_qty, itmqty, user) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
                );
                $stmt->bind_param(
                    'sissddddiidddi',
                    $iname,
                    $code,
                    $barcode,
                    $place,
                    $cost,
                    $price,
                    $price2,
                    $market,
                    $group1,
                    $group2,
                    $orderLimit,
                    $maxQty,
                    $qty,
                    $userId
                );
                $stmt->execute();
                $itemId = (int) $stmt->insert_id;
                $stmt->close();
            }

            $baseUnitId = kody_import_unit_id($conn, $unitName);
            $unitRows = [[
                'unit_id' => $baseUnitId,
                'u_val' => 1,
                'barcode' => $barcode,
                'cost' => $cost,
                'price1' => $price,
                'price2' => $price2,
                'price3' => $market,
            ]];
            if ($factor > 0 && abs($factor - 1) > 0.0001) {
                $unitRows[] = [
                    'unit_id' => kody_import_other_unit_id($conn, $baseUnitId),
                    'u_val' => $factor,
                    'barcode' => $barcode2,
                    'cost' => $cost2,
                    'price1' => $price2sell,
                    'price2' => 0,
                    'price3' => 0,
                ];
            }

            kody_sync_item_units($conn, $itemId, $unitRows);
            $conn->commit();
            $ok++;
        } catch (Throwable $e) {
            $conn->rollback();
            $skipped++;
            $errors[] = [
                'row' => $lineNo,
                'name' => $iname,
                'code' => $codeRaw,
                'barcode' => $barcode,
                'reason' => kody_import_error_text($e),
            ];
        }
    }

    return ['ok' => $ok, 'skipped' => $skipped, 'errors' => $errors];
}

function kody_import_error_text(Throwable $e): string
{
    return match ($e->getMessage()) {
        'duplicate_barcode' => 'الباركود مستخدم',
        'duplicate_unit' => 'الوحدة أو المعامل مكرر',
        'unit_in_use' => 'الوحدة مستخدمة في فاتورة',
        'invalid_unit' => 'بيانات الوحدة غير صالحة',
        'no_units' => 'لا توجد وحدة',
        default => $e->getMessage(),
    };
}

function kody_import_barcode_taken(mysqli $conn, string $barcode, int $exceptItemId): bool
{
    $stmt = $conn->prepare('SELECT id FROM myitems WHERE barcode = ? AND id <> ? LIMIT 1');
    $stmt->bind_param('si', $barcode, $exceptItemId);
    $stmt->execute();
    $stmt->store_result();
    $taken = $stmt->num_rows > 0;
    $stmt->close();
    if ($taken) {
        return true;
    }
    $stmt = $conn->prepare('SELECT id FROM item_units WHERE unit_barcode = ? AND item_id <> ? LIMIT 1');
    $stmt->bind_param('si', $barcode, $exceptItemId);
    $stmt->execute();
    $stmt->store_result();
    $taken = $stmt->num_rows > 0;
    $stmt->close();
    return $taken;
}

if (PHP_SAPI === 'cli') {
    return;
}

if (!isset($_SESSION['login'])) {
    header('Location: ../index.php');
    exit;
}

if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    header('Location: ../add_item.php?error=import_failed');
    exit;
}

$ext = strtolower(pathinfo((string) $_FILES['file']['name'], PATHINFO_EXTENSION));
if (!in_array($ext, ['xlsx', 'csv'], true)) {
    header('Location: ../add_item.php?error=import_type');
    exit;
}

try {
    $rows = kody_import_sheet_rows($_FILES['file']['tmp_name'], $ext);
    $userId = (int) ($_SESSION['userid'] ?? 1);
    $result = kody_import_items($conn, $rows, $userId);
} catch (Throwable $e) {
    $_SESSION['import_report'] = [
        'ok' => 0,
        'failed' => 0,
        'errors' => [[
            'row' => '',
            'name' => '',
            'code' => '',
            'barcode' => '',
            'reason' => $e->getMessage(),
        ]],
    ];
    header('Location: ../add_item.php?imported=0&import_skipped=0');
    exit;
}

$_SESSION['import_report'] = [
    'ok' => (int) $result['ok'],
    'failed' => (int) $result['skipped'],
    'errors' => $result['errors'],
];
header('Location: ../add_item.php?imported=' . (int) $result['ok'] . '&import_skipped=' . (int) $result['skipped']);
exit;
