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
