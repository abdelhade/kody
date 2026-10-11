<?php
include('../includes/connect.php');

$recostSuccess = false;

try {
    // 1. التأكد من وجود عمود manual_price_edit في myitems
    $checkColumn = $conn->query("SHOW COLUMNS FROM myitems LIKE 'manual_price_edit'");
    if ($checkColumn && $checkColumn->num_rows == 0) {
        if (!$conn->query("ALTER TABLE myitems ADD COLUMN manual_price_edit TINYINT(1) DEFAULT 0")) {
            throw new RuntimeException('alter_column');
        }
    }

    // 2. التأكد من وجود الإجراء المخزن sp_recalculate_costs وإنشاؤه تلقائياً إذا لم يكن موجوداً
    $checkProc = $conn->query("SHOW PROCEDURE STATUS WHERE Db = DATABASE() AND Name = 'sp_recalculate_costs'");
    if (!$checkProc || $checkProc->num_rows == 0) {
        $spFile = __DIR__ . '/../db/sp_recalculate_costs.sql';
        if (file_exists($spFile)) {
            $sql = file_get_contents($spFile);
            $sql = preg_replace('/DELIMITER\s+[^\r\n]+/i', '', $sql);
            $parts = explode('//', $sql);
            foreach ($parts as $p) {
                $p = trim($p);
                if (!empty($p)) {
                    $conn->query($p);
                }
            }
        }
    }

    // 3. استدعاء الإجراء المخزن فائق السرعة
    if (!$conn->query("CALL sp_recalculate_costs()")) {
        throw new RuntimeException("Call failed: " . $conn->error);
    }

    // تنظيف النتائج الإضافية من الـ Stored Procedure
    while ($conn->more_results() && $conn->next_result()) {
        $extra = $conn->use_result();
        if ($extra instanceof mysqli_result) {
            $extra->free();
        }
    }

    $recostSuccess = true;
} catch (Throwable $e) {
    error_log("Recost Error: " . $e->getMessage());
    $recostSuccess = false;
}

header('Location: ../myitems.php?recost=' . ($recostSuccess ? 'ok' : 'fail'));
exit;
