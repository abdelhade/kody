<?php
include('../includes/connect.php');

$recostSuccess = false;

try {
    $checkColumn = $conn->query("SHOW COLUMNS FROM myitems LIKE 'manual_price_edit'");
    if ($checkColumn && $checkColumn->num_rows == 0) {
        if (!$conn->query("ALTER TABLE myitems ADD COLUMN manual_price_edit TINYINT(1) DEFAULT 0")) {
            throw new RuntimeException('alter_column');
        }
    }

    $res = $conn->query("SELECT * FROM fat_details WHERE isdeleted = 0 ORDER BY crtime");
    if ($res === false) {
        throw new RuntimeException('select_details');
    }

    // تحضير الاستعلامات المتكررة مرة واحدة
    $stmt_manual = $conn->prepare("SELECT manual_price_edit FROM myitems WHERE id = ?");
    $stmt_oldqty = $conn->prepare("SELECT SUM(qty_in) - SUM(qty_out) AS old_qty FROM fat_details WHERE isdeleted = 0 AND item_id = ? AND crtime < ?");
    $stmt_oldprice = $conn->prepare("SELECT cost_price FROM fat_details WHERE isdeleted = 0 AND pro_tybe = 4 AND item_id = ? AND crtime < ? ORDER BY crtime DESC LIMIT 1");
    $stmt_update_cost = $conn->prepare("UPDATE fat_details SET cost_price = ? WHERE id = ?");
    $stmt_update_sale = $conn->prepare("UPDATE fat_details SET cost_price = ?, profit = ? WHERE id = ?");

    while ($row = $res->fetch_assoc()) {
        $curqty = $row['qty_in'];
        $curprice = $row['price'];
        $crtime = $row['crtime'];
        $item = intval($row['item_id']);
        $id = intval($row['id']);

        $stmt_manual->bind_param("i", $item);
        $stmt_manual->execute();
        $manualResult = $stmt_manual->get_result();
        if ($manualResult && $manualResult->num_rows > 0) {
            $manualData = $manualResult->fetch_assoc();
            if (isset($manualData['manual_price_edit']) && $manualData['manual_price_edit'] == 1) {
                continue;
            }
        }

        $stmt_oldqty->bind_param("is", $item, $crtime);
        $stmt_oldqty->execute();
        $oldqtyRow = $stmt_oldqty->get_result()->fetch_assoc();
        $oldqty = $oldqtyRow['old_qty'] ?? 0;

        $stmt_oldprice->bind_param("is", $item, $crtime);
        $stmt_oldprice->execute();
        $oldpriceRow = $stmt_oldprice->get_result()->fetch_assoc();
        $oldprice = $oldpriceRow['cost_price'] ?? 0;

        $totalQty = (float) $curqty + (float) $oldqty;
        if ($totalQty != 0.0) {
            $new_cost = (($curprice * $curqty + $oldprice * $oldqty) / $totalQty);
        } else {
            $new_cost = (float) $curprice;
        }

        if ($row['pro_tybe'] == 4) {
            $cost_val = ($oldqty == 0) ? (float)$curprice : $new_cost;
            $stmt_update_cost->bind_param("di", $cost_val, $id);
            if (!$stmt_update_cost->execute()) {
                throw new RuntimeException('update_purchase');
            }
        }

        if ($row['pro_tybe'] == 3 || $row['pro_tybe'] == 9) {
            $profit = $row['qty_out'] * ($row['price'] - $oldprice);
            $cost_val = ($oldqty == 0) ? 0.0 : $new_cost;
            $stmt_update_sale->bind_param("ddi", $cost_val, $profit, $id);
            if (!$stmt_update_sale->execute()) {
                throw new RuntimeException('update_sale');
            }
        }
    }

    // إغلاق الاستعلامات المحضرة
    $stmt_manual->close();
    $stmt_oldqty->close();
    $stmt_oldprice->close();
    $stmt_update_cost->close();
    $stmt_update_sale->close();

    $recostSuccess = true;
} catch (Throwable $e) {
    $recostSuccess = false;
}

header('Location: ../myitems.php?recost=' . ($recostSuccess ? 'ok' : 'fail'));
exit;
