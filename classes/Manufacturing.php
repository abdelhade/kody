<?php

require_once __DIR__ . '/InvoiceProcessor.php';
require_once __DIR__ . '/Can.php';

/**
 * تصنيع بسيط: وصفة على الصنف المجمع، وأمر يخصم الخامات ويزوّد المنتج في نفس المخزن.
 * بلا قيد محاسبي. تكلفة الوحدة = مجموع تكلفة الخامات ÷ الكمية المنتجة.
 */
final class Manufacturing
{
    public static function type(): int
    {
        return InvoiceProcessor::INVOICE_TYPES['MANUFACTURING'];
    }

    /** @return list<array{component_id:int,qty:float,iname:string,barcode:string,cost_price:float}> */
    public static function recipe(mysqli $conn, int $parentId): array
    {
        $stmt = $conn->prepare(
            'SELECT b.component_id, b.qty, i.iname, i.barcode, i.cost_price
             FROM item_bom b
             JOIN myitems i ON i.id = b.component_id AND i.isdeleted = 0
             WHERE b.parent_id = ? AND b.isdeleted = 0 AND b.qty > 0
             ORDER BY b.id'
        );
        $stmt->bind_param('i', $parentId);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        $out = [];
        foreach ($rows as $row) {
            $out[] = [
                'component_id' => (int) $row['component_id'],
                'qty' => (float) $row['qty'],
                'iname' => (string) $row['iname'],
                'barcode' => (string) ($row['barcode'] ?? ''),
                'cost_price' => (float) $row['cost_price'],
            ];
        }
        return $out;
    }

    /**
     * @param list<int> $componentIds
     * @param list<float|int|string> $qtys
     */
    public static function saveRecipe(mysqli $conn, int $parentId, array $componentIds, array $qtys): void
    {
        $parent = self::item($conn, $parentId);
        if ($parent === null) {
            throw new RuntimeException('الصنف غير موجود');
        }

        $lines = [];
        foreach ($componentIds as $i => $componentId) {
            $componentId = (int) $componentId;
            $qty = (float) ($qtys[$i] ?? 0);
            if ($componentId < 1 || $qty <= 0) {
                continue;
            }
            if ($componentId === $parentId) {
                throw new RuntimeException('الصنف المجمع لا يدخل في وصفته');
            }
            $component = self::item($conn, $componentId);
            if ($component === null) {
                throw new RuntimeException('صنف خام غير موجود');
            }
            if (!isset($lines[$componentId])) {
                $lines[$componentId] = 0.0;
            }
            $lines[$componentId] += $qty;
        }

        $conn->begin_transaction();
        try {
            $stmt = $conn->prepare('UPDATE item_bom SET isdeleted = 1 WHERE parent_id = ? AND isdeleted = 0');
            $stmt->bind_param('i', $parentId);
            $stmt->execute();
            $stmt->close();

            if ($lines !== []) {
                $ins = $conn->prepare('INSERT INTO item_bom (parent_id, component_id, qty) VALUES (?, ?, ?)');
                foreach ($lines as $componentId => $qty) {
                    $ins->bind_param('iid', $parentId, $componentId, $qty);
                    if (!$ins->execute()) {
                        throw new RuntimeException('تعذّر حفظ الوصفة');
                    }
                }
                $ins->close();
            }

            $flag = $lines === [] ? 0 : 1;
            $upd = $conn->prepare('UPDATE myitems SET is_assembly = ? WHERE id = ?');
            $upd->bind_param('ii', $flag, $parentId);
            $upd->execute();
            $upd->close();
            $conn->commit();
        } catch (Throwable $e) {
            $conn->rollback();
            if ($e instanceof RuntimeException) {
                throw $e;
            }
            throw new RuntimeException('تعذّر حفظ الوصفة');
        }
    }

    /** @return array{id:int,pro_date:string,store_id:int,info:string,item_id:int,qty:float,lines:list<array{item_id:int,qty:float,iname:string,cost_price:float}>}|null */
    public static function loadOrder(mysqli $conn, int $id): ?array
    {
        $type = self::type();
        $stmt = $conn->prepare(
            'SELECT id, pro_date, store_id, info FROM ot_head
             WHERE id = ? AND pro_tybe = ? AND isdeleted = 0 LIMIT 1'
        );
        $stmt->bind_param('ii', $id, $type);
        $stmt->execute();
        $head = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if (!$head) {
            return null;
        }

        $stmt = $conn->prepare(
            'SELECT d.item_id, d.qty_in, d.qty_out, d.price, i.iname, i.cost_price
             FROM fat_details d
             JOIN myitems i ON i.id = d.item_id
             WHERE d.isdeleted = 0 AND (d.pro_id = ? OR d.fatid = ?)
             ORDER BY d.id'
        );
        $stmt->bind_param('ii', $id, $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $itemId = 0;
        $qty = 0.0;
        $lines = [];
        while ($row = $res->fetch_assoc()) {
            if ((float) $row['qty_in'] > 0) {
                $itemId = (int) $row['item_id'];
                $qty = (float) $row['qty_in'];
                continue;
            }
            if ((float) $row['qty_out'] <= 0) {
                continue;
            }
            $lines[] = [
                'item_id' => (int) $row['item_id'],
                'qty' => (float) $row['qty_out'],
                'iname' => (string) $row['iname'],
                'cost_price' => (float) $row['price'],
            ];
        }
        $stmt->close();
        if ($itemId < 1) {
            return null;
        }

        return [
            'id' => (int) $head['id'],
            'pro_date' => (string) $head['pro_date'],
            'store_id' => (int) $head['store_id'],
            'info' => (string) ($head['info'] ?? ''),
            'item_id' => $itemId,
            'qty' => $qty,
            'lines' => $lines,
        ];
    }

    public static function finishedItemId(mysqli $conn, int $orderId): int
    {
        $stmt = $conn->prepare(
            'SELECT item_id FROM fat_details
             WHERE isdeleted = 0 AND qty_in > 0 AND (pro_id = ? OR fatid = ?)
             ORDER BY id DESC LIMIT 1'
        );
        $stmt->bind_param('ii', $orderId, $orderId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return (int) ($row['item_id'] ?? 0);
    }

    /**
     * @param list<int> $componentIds
     * @param list<float|int|string> $qtys
     */
    public static function save(
        mysqli $conn,
        int $orderId,
        int $storeId,
        int $itemId,
        float $produceQty,
        string $proDate,
        string $note,
        array $componentIds,
        array $qtys,
        int $userId
    ): int {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $proDate)) {
            throw new RuntimeException('التاريخ غير صالح');
        }
        if ($produceQty <= 0) {
            throw new RuntimeException('كمية الإنتاج لازم تكون أكبر من صفر');
        }
        if (!self::isStore($conn, $storeId)) {
            throw new RuntimeException('اختار مخزن صالح');
        }
        $product = self::item($conn, $itemId);
        if ($product === null || (int) $product['is_assembly'] !== 1) {
            throw new RuntimeException('الصنف لازم يكون مجمع وله وصفة');
        }

        $allowed = [];
        foreach (self::recipe($conn, $itemId) as $line) {
            $allowed[$line['component_id']] = true;
        }
        if ($allowed === []) {
            throw new RuntimeException('الصنف المجمع ليس له وصفة');
        }

        $use = [];
        foreach ($componentIds as $i => $componentId) {
            $componentId = (int) $componentId;
            $qty = (float) ($qtys[$i] ?? 0);
            if ($componentId < 1 || $qty == 0.0) {
                continue;
            }
            if ($qty < 0) {
                throw new RuntimeException('كمية الخام لا تكون سالبة');
            }
            if ($componentId === $itemId || !isset($allowed[$componentId])) {
                throw new RuntimeException('الخام مش ضمن وصفة الصنف');
            }
            if (!isset($use[$componentId])) {
                $use[$componentId] = 0.0;
            }
            $use[$componentId] += $qty;
        }
        if ($use === []) {
            throw new RuntimeException('لازم خام واحد على الأقل بكمية أكبر من صفر');
        }

        $note = trim($note);
        if (mb_strlen($note) > 120) {
            $note = mb_substr($note, 0, 120);
        }

        $conn->begin_transaction();
        try {
            $touched = [$itemId];
            $oldItemId = 0;
            if ($orderId > 0) {
                $existing = self::loadOrder($conn, $orderId);
                if ($existing === null) {
                    throw new RuntimeException('أمر التصنيع غير موجود');
                }
                $oldItemId = (int) $existing['item_id'];
                $touched[] = $oldItemId;
                foreach ($existing['lines'] as $line) {
                    $touched[] = $line['item_id'];
                }
                $del = $conn->prepare('DELETE FROM fat_details WHERE pro_id = ? OR fatid = ?');
                $del->bind_param('ii', $orderId, $orderId);
                $del->execute();
                $del->close();
                self::syncQty($conn, $touched);
            }

            $preventNegative = !Can::check('stock.sell_negative');
            $totalCost = 0.0;
            $priced = [];
            foreach ($use as $componentId => $qty) {
                $component = self::item($conn, $componentId);
                if ($component === null) {
                    throw new RuntimeException('صنف خام غير موجود');
                }
                if ($preventNegative) {
                    $available = InvoiceProcessor::getRealStockQuantity($conn, $componentId, $storeId);
                    if ($available + 0.0000001 < $qty) {
                        throw new RuntimeException(
                            'رصيد ' . $component['iname'] . ' في المخزن (' . self::num($available) . ') أقل من المطلوب (' . self::num($qty) . ')'
                        );
                    }
                }
                $cost = (float) $component['cost_price'];
                $totalCost += $qty * $cost;
                $priced[] = ['id' => $componentId, 'qty' => $qty, 'cost' => $cost];
                $touched[] = $componentId;
            }
            $unitCost = $totalCost / $produceQty;

            $info = $product['iname'] . ' × ' . self::num($produceQty);
            if ($note !== '') {
                $info .= ' — ' . $note;
            }
            if (mb_strlen($info) > 200) {
                $info = mb_substr($info, 0, 200);
            }

            if ($orderId < 1) {
                $orderId = self::insertHeader($conn, $storeId, $proDate, $info, $totalCost, $userId);
            } else {
                self::updateHeader($conn, $orderId, $storeId, $proDate, $info, $totalCost);
            }

            self::insertLines($conn, $orderId, $storeId, $itemId, $produceQty, $unitCost, $priced);
            self::syncQty($conn, $touched);
            self::recalcItemCost($conn, $itemId);
            if ($oldItemId > 0 && $oldItemId !== $itemId) {
                self::recalcItemCost($conn, $oldItemId);
            }
            $conn->commit();
            return $orderId;
        } catch (Throwable $e) {
            $conn->rollback();
            if ($e instanceof RuntimeException) {
                throw $e;
            }
            throw new RuntimeException('تعذّر حفظ أمر التصنيع');
        }
    }

    /** يعيد متوسط تكلفة الصنف من حركاته (مشتريات / رصيد افتتاحي / تصنيع وارد). */
    public static function recalcItemCost(mysqli $conn, int $itemId): void
    {
        if ($itemId < 1) {
            return;
        }
        $stmt = $conn->prepare(
            'SELECT d.pro_tybe, COALESCE(d.qty_in, 0) AS qty_in, COALESCE(d.qty_out, 0) AS qty_out, COALESCE(d.price, 0) AS price
             FROM fat_details d
             LEFT JOIN ot_head o ON o.id = COALESCE(NULLIF(d.pro_id, 0), d.fatid)
             WHERE d.item_id = ? AND COALESCE(d.isdeleted, 0) = 0 AND COALESCE(o.isdeleted, 0) = 0
             ORDER BY COALESCE(NULLIF(o.pro_date, \'0000-00-00\'), DATE(d.crtime)),
                      (CASE WHEN d.pro_tybe = 14 THEN 0 WHEN d.pro_tybe IN (4, 12) THEN 1 ELSE 2 END),
                      d.crtime, d.id'
        );
        $stmt->bind_param('i', $itemId);
        $stmt->execute();
        $res = $stmt->get_result();
        $qty = 0.0;
        $cost = 0.0;
        $mfg = self::type();
        while ($row = $res->fetch_assoc()) {
            $tybe = (int) $row['pro_tybe'];
            $in = (float) $row['qty_in'];
            $out = (float) $row['qty_out'];
            $price = (float) $row['price'];
            if (($tybe === 14 || $tybe === 4 || $tybe === 12 || $tybe === $mfg) && $in > 0) {
                if ($qty > 0) {
                    $cost = (($cost * $qty) + ($price * $in)) / ($qty + $in);
                } else {
                    $cost = $price;
                }
            }
            $qty += $in - $out;
        }
        $stmt->close();
        if ($cost <= 0) {
            return;
        }
        $upd = $conn->prepare('UPDATE myitems SET cost_price = ? WHERE id = ?');
        $upd->bind_param('di', $cost, $itemId);
        $upd->execute();
        $upd->close();
        $upd = $conn->prepare('UPDATE item_units SET cost_price = ? * u_val WHERE item_id = ?');
        $upd->bind_param('di', $cost, $itemId);
        $upd->execute();
        $upd->close();
    }

    /** @param list<array{id:int,qty:float,cost:float}> $components */
    private static function insertLines(
        mysqli $conn,
        int $orderId,
        int $storeId,
        int $itemId,
        float $produceQty,
        float $unitCost,
        array $components
    ): void {
        $type = self::type();
        $stmt = $conn->prepare(
            'INSERT INTO fat_details (
                pro_tybe, pro_id, item_id, u_val, qty_in, qty_out, doc_qty, price,
                discount, disc_pct, det_value, fatid, fat_tybe, det_store, cost_price, profit
            ) VALUES (?, ?, ?, 1, ?, ?, ?, ?, 0, 0, ?, ?, ?, ?, ?, 0)'
        );
        if (!$stmt) {
            throw new RuntimeException('تعذّر تجهيز بنود التصنيع');
        }
        $lineItem = 0;
        $qtyIn = 0.0;
        $qtyOut = 0.0;
        $docQty = 0.0;
        $price = 0.0;
        $value = 0.0;
        $costCol = 0.0;
        $stmt->bind_param(
            'iiidddddiiid',
            $type,
            $orderId,
            $lineItem,
            $qtyIn,
            $qtyOut,
            $docQty,
            $price,
            $value,
            $orderId,
            $type,
            $storeId,
            $costCol
        );
        foreach ($components as $line) {
            $lineItem = (int) $line['id'];
            $qtyIn = 0.0;
            $qtyOut = (float) $line['qty'];
            $docQty = $qtyOut;
            $price = (float) $line['cost'];
            $value = $qtyOut * $price;
            $costCol = $price;
            if (!$stmt->execute()) {
                throw new RuntimeException('تعذّر حفظ خام التصنيع');
            }
        }
        $lineItem = $itemId;
        $qtyIn = $produceQty;
        $qtyOut = 0.0;
        $docQty = $produceQty;
        $price = $unitCost;
        $value = $produceQty * $unitCost;
        $costCol = $unitCost;
        if (!$stmt->execute()) {
            throw new RuntimeException('تعذّر حفظ المنتج المصنّع');
        }
        $stmt->close();
    }

    private static function insertHeader(
        mysqli $conn,
        int $storeId,
        string $proDate,
        string $info,
        float $total,
        int $userId
    ): int {
        $type = self::type();
        $proId = InvoiceProcessor::getNextInvoiceNumber($conn, $type);
        $stmt = $conn->prepare(
            'INSERT INTO ot_head (
                pro_id, pro_tybe, is_stock, is_journal, journal_tybe, info, pro_date,
                store_id, acc1, pro_value, fat_total, fat_net, user
            ) VALUES (?, ?, 1, 0, 0, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->bind_param(
            'iissiidddi',
            $proId,
            $type,
            $info,
            $proDate,
            $storeId,
            $storeId,
            $total,
            $total,
            $total,
            $userId
        );
        if (!$stmt->execute()) {
            throw new RuntimeException('تعذّر حفظ رأس أمر التصنيع');
        }
        $id = (int) $conn->insert_id;
        $stmt->close();
        return $id;
    }

    private static function updateHeader(
        mysqli $conn,
        int $orderId,
        int $storeId,
        string $proDate,
        string $info,
        float $total
    ): void {
        $type = self::type();
        $stmt = $conn->prepare(
            'UPDATE ot_head
             SET info = ?, pro_date = ?, store_id = ?, acc1 = ?, pro_value = ?, fat_total = ?, fat_net = ?
             WHERE id = ? AND pro_tybe = ? AND isdeleted = 0'
        );
        $stmt->bind_param(
            'ssiidddii',
            $info,
            $proDate,
            $storeId,
            $storeId,
            $total,
            $total,
            $total,
            $orderId,
            $type
        );
        if (!$stmt->execute() || $stmt->affected_rows < 0) {
            $stmt->close();
            throw new RuntimeException('تعذّر تحديث أمر التصنيع');
        }
        $stmt->close();
    }

    /** @param list<int> $ids */
    private static function syncQty(mysqli $conn, array $ids): void
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', $ids))));
        if ($ids === []) {
            return;
        }
        $in = implode(',', $ids);
        $conn->query(
            "UPDATE myitems mi SET itmqty = (
                SELECT COALESCE(SUM(qty_in) - SUM(qty_out), 0)
                FROM fat_details fd
                WHERE fd.item_id = mi.id AND fd.isdeleted = 0
            ) WHERE mi.id IN ($in)"
        );
    }

    /** @return array{id:int,iname:string,is_assembly:int,cost_price:float}|null */
    private static function item(mysqli $conn, int $id): ?array
    {
        $stmt = $conn->prepare(
            'SELECT id, iname, is_assembly, cost_price FROM myitems WHERE id = ? AND isdeleted = 0 LIMIT 1'
        );
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if (!$row) {
            return null;
        }
        return [
            'id' => (int) $row['id'],
            'iname' => (string) $row['iname'],
            'is_assembly' => (int) $row['is_assembly'],
            'cost_price' => (float) $row['cost_price'],
        ];
    }

    private static function isStore(mysqli $conn, int $id): bool
    {
        $stmt = $conn->prepare('SELECT id FROM acc_head WHERE id = ? AND is_stock = 1 AND isdeleted = 0 LIMIT 1');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $ok = (bool) $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $ok;
    }

    private static function num(float $n): string
    {
        $s = rtrim(rtrim(number_format($n, 4, '.', ''), '0'), '.');
        return $s === '' ? '0' : $s;
    }
}
