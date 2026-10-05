<?php

/**
 * يزامن وحدات صنف موجود.
 * الصف يُعرف بـ item_units.id. تغيير نوع الوحدة تحديث، والسطر الجديد إدراج، والمحذوف يُحذف
 * إلا إذا كان معامله مستخدماً في فاتورة (الفواتير تتعرّف على الوحدة بالمعامل).
 *
 * @param array<int, array{iu_id?:int, unit_id:int, u_val:mixed, barcode:string, cost:float, price1:float, price2:float, price3:float}> $rows
 */
function kody_sync_item_units(mysqli $conn, int $itemId, array $rows): void
{
    if ($itemId < 1 || $rows === []) {
        throw new RuntimeException('no_units');
    }

    $seenUnit = [];
    $seenVal = [];
    $seenBarcode = [];
    foreach ($rows as $i => $row) {
        $unitId = (int) $row['unit_id'];
        $uVal = number_format((float) $row['u_val'], 3, '.', '');
        if ($unitId < 1 || (float) $uVal <= 0) {
            throw new RuntimeException('invalid_unit');
        }
        if (isset($seenUnit[$unitId]) || isset($seenVal[$uVal])) {
            throw new RuntimeException('duplicate_unit');
        }
        $seenUnit[$unitId] = true;
        $seenVal[$uVal] = true;

        $barcode = trim((string) ($row['barcode'] ?? ''));
        if ($barcode !== '') {
            if (isset($seenBarcode[$barcode])) {
                throw new RuntimeException('duplicate_barcode');
            }
            $seenBarcode[$barcode] = true;
        }

        $rows[$i]['iu_id'] = (int) ($row['iu_id'] ?? 0);
        $rows[$i]['unit_id'] = $unitId;
        $rows[$i]['u_val'] = $uVal;
        $rows[$i]['barcode'] = $barcode;
        $rows[$i]['cost'] = (float) $row['cost'];
        $rows[$i]['price1'] = (float) $row['price1'];
        $rows[$i]['price2'] = (float) $row['price2'];
        $rows[$i]['price3'] = (float) $row['price3'];
    }

    $existing = [];
    $load = $conn->prepare('SELECT id, unit_id, u_val FROM item_units WHERE item_id = ?');
    $load->bind_param('i', $itemId);
    $load->execute();
    $loaded = $load->get_result();
    while ($r = $loaded->fetch_assoc()) {
        $existing[(int) $r['id']] = $r;
    }
    $load->close();

    $byUnit = [];
    foreach ($existing as $id => $r) {
        $byUnit[(int) $r['unit_id']] = $id;
    }

    $claimed = [];
    foreach ($rows as $i => $row) {
        $id = $row['iu_id'];
        if ($id > 0 && isset($existing[$id]) && !isset($claimed[$id])) {
            $claimed[$id] = true;
            continue;
        }
        $match = $byUnit[$row['unit_id']] ?? 0;
        if ($match > 0 && !isset($claimed[$match])) {
            $rows[$i]['iu_id'] = $match;
            $claimed[$match] = true;
            continue;
        }
        $rows[$i]['iu_id'] = 0;
    }

    $usedStmt = $conn->prepare(
        'SELECT id FROM fat_details WHERE item_id = ? AND ABS(u_val - ?) < 0.0001 AND COALESCE(isdeleted, 0) = 0 LIMIT 1'
    );
    $isUsed = static function (string $uVal) use ($usedStmt, $itemId): bool {
        $val = (float) $uVal;
        $usedStmt->bind_param('id', $itemId, $val);
        $usedStmt->execute();
        $usedStmt->store_result();
        $hit = $usedStmt->num_rows > 0;
        $usedStmt->free_result();
        return $hit;
    };

    try {
        foreach ($existing as $id => $r) {
            if (isset($claimed[$id])) {
                continue;
            }
            $oldVal = number_format((float) $r['u_val'], 3, '.', '');
            if ($isUsed($oldVal)) {
                throw new RuntimeException('unit_in_use');
            }
        }

        foreach ($rows as $row) {
            $id = (int) $row['iu_id'];
            if ($id < 1 || !isset($existing[$id])) {
                continue;
            }
            $oldVal = number_format((float) $existing[$id]['u_val'], 3, '.', '');
            if ($oldVal !== $row['u_val'] && $isUsed($oldVal)) {
                throw new RuntimeException('unit_in_use');
            }
        }
    } finally {
        $usedStmt->close();
    }

    $upd = $conn->prepare(
        'UPDATE item_units SET unit_id = ?, u_val = ?, unit_barcode = ?, cost_price = ?, price1 = ?, price2 = ?, price3 = ? WHERE id = ? AND item_id = ?'
    );
    $ins = $conn->prepare(
        'INSERT INTO item_units (item_id, unit_id, u_val, unit_barcode, cost_price, price1, price2, price3) VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
    );
    $del = $conn->prepare('DELETE FROM item_units WHERE id = ? AND item_id = ?');

    try {
        $kept = [];
        foreach ($rows as $row) {
            $id = (int) $row['iu_id'];
            $unitId = (int) $row['unit_id'];
            $uVal = $row['u_val'];
            $barcode = $row['barcode'];
            $cost = $row['cost'];
            $p1 = $row['price1'];
            $p2 = $row['price2'];
            $p3 = $row['price3'];

            if ($id > 0 && isset($existing[$id])) {
                $upd->bind_param('issddddii', $unitId, $uVal, $barcode, $cost, $p1, $p2, $p3, $id, $itemId);
                $upd->execute();
                $kept[$id] = true;
            } else {
                $ins->bind_param('iissdddd', $itemId, $unitId, $uVal, $barcode, $cost, $p1, $p2, $p3);
                $ins->execute();
                $kept[(int) $conn->insert_id] = true;
            }
        }

        foreach (array_keys($existing) as $id) {
            if (isset($kept[$id])) {
                continue;
            }
            $del->bind_param('ii', $id, $itemId);
            $del->execute();
        }
    } finally {
        $upd->close();
        $ins->close();
        $del->close();
    }
}

/**
 * الباركود مستخدم لصنف نشط أو لوحدة تابعة لصنف نشط.
 */
function kody_barcode_taken(mysqli $conn, string $barcode, int $excludeItemId = 0): bool
{
    $barcode = trim($barcode);
    if ($barcode === '') {
        return false;
    }

    $stmt = $conn->prepare('SELECT id FROM myitems WHERE barcode = ? AND id <> ? AND isdeleted = 0 LIMIT 1');
    $stmt->bind_param('si', $barcode, $excludeItemId);
    $stmt->execute();
    $stmt->store_result();
    $hit = $stmt->num_rows > 0;
    $stmt->close();
    if ($hit) {
        return true;
    }

    $stmt = $conn->prepare(
        'SELECT iu.id
         FROM item_units iu
         INNER JOIN myitems m ON m.id = iu.item_id
         WHERE iu.unit_barcode = ? AND iu.item_id <> ? AND m.isdeleted = 0 AND COALESCE(iu.isdeleted, 0) = 0
         LIMIT 1'
    );
    $stmt->bind_param('si', $barcode, $excludeItemId);
    $stmt->execute();
    $stmt->store_result();
    $hit = $stmt->num_rows > 0;
    $stmt->close();

    return $hit;
}

function kody_next_barcode(mysqli $conn): string
{
    $sql = "SELECT GREATEST(
                COALESCE((SELECT MAX(CAST(barcode AS UNSIGNED)) FROM myitems WHERE barcode REGEXP '^[0-9]+$' AND isdeleted = 0), 0),
                COALESCE((SELECT MAX(CAST(iu.unit_barcode AS UNSIGNED)) FROM item_units iu INNER JOIN myitems m ON m.id = iu.item_id WHERE iu.unit_barcode REGEXP '^[0-9]+$' AND m.isdeleted = 0 AND COALESCE(iu.isdeleted, 0) = 0), 0)
            ) AS max_barcode";
    $row = $conn->query($sql);
    $max = 0;
    if ($row && ($assoc = $row->fetch_assoc())) {
        $max = (int) $assoc['max_barcode'];
    }
    do {
        $max++;
        $candidate = (string) $max;
    } while (kody_barcode_taken($conn, $candidate));

    return $candidate;
}

/**
 * يملأ باركود الوحدات الفارغ ويتأكد من وحدة أساسية واحدة (معامل 1) ومن عدم التكرار.
 * باركود الوحدة الأساسية يجوز أن يطابق باركود الصنف.
 *
 * @param array<int, array<string, mixed>> $rows
 * @return array<int, array<string, mixed>>
 */
function kody_prepare_unit_barcodes(mysqli $conn, string $itemBarcode, array $rows, int $excludeItemId = 0): array
{
    $itemBarcode = trim($itemBarcode);
    $reserved = [];
    if ($itemBarcode !== '') {
        $reserved[$itemBarcode] = true;
    }

    $baseCount = 0;
    foreach ($rows as $i => $row) {
        $uVal = number_format((float) ($row['u_val'] ?? 0), 3, '.', '');
        $isBase = $uVal === '1.000';
        if ($isBase) {
            $baseCount++;
        }

        $barcode = trim((string) ($row['barcode'] ?? ''));
        if ($barcode === '' && $isBase) {
            $barcode = $itemBarcode;
        }
        $rows[$i]['barcode'] = $barcode;
        $rows[$i]['_is_base'] = $isBase;
    }

    $baseBarcode = $itemBarcode;
    foreach ($rows as $row) {
        if (!empty($row['_is_base']) && $row['barcode'] !== '') {
            $baseBarcode = $row['barcode'];
            break;
        }
    }
    $addend = kody_unit_barcode_addend($conn);
    $secondFilled = false;
    $used = $reserved;
    foreach ($rows as $row) {
        if ($row['barcode'] !== '') {
            $used[$row['barcode']] = true;
        }
    }
    foreach ($rows as $i => $row) {
        if (!empty($row['_is_base']) || $row['barcode'] !== '') {
            continue;
        }
        if (!$secondFilled && $addend !== '0' && $baseBarcode !== '' && ctype_digit($baseBarcode)) {
            $next = kody_add_barcode_number($baseBarcode, $addend);
            if (isset($used[$next])) {
                throw new RuntimeException('duplicate_barcode');
            }
            $rows[$i]['barcode'] = $next;
            $used[$next] = true;
            $secondFilled = true;
            continue;
        }
        $next = kody_make_unit_barcode($conn, $baseBarcode, (int) $i, $used, $excludeItemId);
        $rows[$i]['barcode'] = $next;
        $used[$next] = true;
    }

    foreach ($rows as $i => $row) {
        $isBase = !empty($row['_is_base']);
        $barcode = trim((string) $row['barcode']);
        if ($barcode === '' || strlen($barcode) > 20 || strlen($itemBarcode) > 25) {
            throw new RuntimeException('barcode_length');
        }

        $sharesItemBarcode = $isBase && $barcode === $itemBarcode;
        if (isset($reserved[$barcode]) && !$sharesItemBarcode) {
            throw new RuntimeException('duplicate_barcode');
        }
        if (!$sharesItemBarcode && kody_barcode_taken($conn, $barcode, $excludeItemId)) {
            throw new RuntimeException('duplicate_barcode');
        }

        $reserved[$barcode] = true;
        $rows[$i]['barcode'] = $barcode;
        unset($rows[$i]['_is_base']);
    }

    if ($baseCount !== 1) {
        throw new RuntimeException('no_base_unit');
    }

    return $rows;
}

function kody_unit_barcode_addend(mysqli $conn): string
{
    require_once __DIR__ . '/barcode_design.php';
    $col = $conn->query("SHOW COLUMNS FROM settings LIKE 'barcode_design'");
    if (!$col || $col->num_rows === 0) {
        return '0';
    }
    $row = $conn->query('SELECT barcode_design FROM settings LIMIT 1');
    $assoc = $row ? $row->fetch_assoc() : null;
    $design = kody_barcode_design_load(is_array($assoc) ? $assoc : []);
    $add = preg_replace('/\D/', '', (string) ($design['unit_barcode_add'] ?? ''));
    if ($add === '' || preg_match('/^0+$/', $add)) {
        return '0';
    }
    return substr($add, 0, 10);
}

function kody_add_barcode_number(string $barcode, string $addend): string
{
    $barcode = preg_replace('/\D/', '', $barcode);
    $addend = preg_replace('/\D/', '', $addend);
    if ($barcode === '') {
        $barcode = '0';
    }
    if ($addend === '') {
        $addend = '0';
    }
    $i = strlen($barcode) - 1;
    $j = strlen($addend) - 1;
    $carry = 0;
    $out = '';
    while ($i >= 0 || $j >= 0 || $carry > 0) {
        $sum = $carry;
        if ($i >= 0) {
            $sum += ord($barcode[$i]) - 48;
        }
        if ($j >= 0) {
            $sum += ord($addend[$j]) - 48;
        }
        $out = (string) ($sum % 10) . $out;
        $carry = intdiv($sum, 10);
        $i--;
        $j--;
    }
    $out = ltrim($out, '0');
    return $out === '' ? '0' : $out;
}

function kody_make_unit_barcode(mysqli $conn, string $seed, int $index, array $reserved, int $excludeItemId): string
{
    $seed = $seed !== '' ? $seed : '0';
    $max = 0;
    $row = $conn->query("SELECT COALESCE(MAX(CAST(barcode AS UNSIGNED)), 0) AS max_barcode FROM myitems WHERE barcode REGEXP '^[0-9]+$'");
    if ($row && ($assoc = $row->fetch_assoc())) {
        $max = (int) $assoc['max_barcode'];
    }
    for ($n = 0; $n < 200; $n++) {
        $prefixed = '99' . $index . ($n === 0 ? '' : (string) $n) . $seed;
        $candidate = strlen($prefixed) <= 20 ? $prefixed : (string) ($max + $n + 1);
        if ($candidate === '' || strlen($candidate) > 20 || isset($reserved[$candidate])) {
            continue;
        }
        if (kody_barcode_taken($conn, $candidate, $excludeItemId)) {
            continue;
        }
        return $candidate;
    }

    throw new RuntimeException('duplicate_barcode');
}
