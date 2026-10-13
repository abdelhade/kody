<?php
/**
 * تثبيت الإجراء المخزن sp_recalculate_costs من ملف SQL إذا لم يكن موجوداً أو تغيّر الملف
 *
 * @param mysqli $conn
 * @return void
 */
function ensureRecostProcedure(mysqli $conn): void {
    $checkCol = $conn->query("SHOW COLUMNS FROM myitems LIKE 'manual_price_edit'");
    if ($checkCol && $checkCol->num_rows === 0) {
        $conn->query("ALTER TABLE myitems ADD COLUMN manual_price_edit TINYINT(1) DEFAULT 0");
    }

    $spFile = __DIR__ . '/../db/sp_recalculate_costs.sql';
    if (!file_exists($spFile)) {
        return;
    }

    $sql  = file_get_contents($spFile);
    $hash = md5($sql);

    // بصمة الملف تُخزَّن في تعليق الإجراء لإعادة التثبيت تلقائياً عند تعديل الملف
    $res = $conn->query(
        "SELECT ROUTINE_COMMENT FROM information_schema.ROUTINES
         WHERE ROUTINE_SCHEMA = DATABASE() AND ROUTINE_NAME = 'sp_recalculate_costs'"
    );
    $row = $res ? $res->fetch_assoc() : null;
    if ($row && $row['ROUTINE_COMMENT'] === $hash) {
        return;
    }

    $sql   = preg_replace('/DELIMITER\s+[^\r\n]+/i', '', $sql);
    $parts = explode('//', $sql);
    foreach ($parts as $p) {
        $p = trim($p);
        if ($p !== '' && !$conn->query($p)) {
            throw new RuntimeException("Install failed: " . $conn->error);
        }
    }
    $conn->query("ALTER PROCEDURE sp_recalculate_costs COMMENT '" . $hash . "'");
}

/**
 * دالة إعادة حساب متوسط التكلفة والأرباح لجميع الأصناف والفواتير
 *
 * @param mysqli $conn
 * @return bool
 */
function recalculateAverageCosts(mysqli $conn): bool {
    try {
        ensureRecostProcedure($conn);

        if (!$conn->query("CALL sp_recalculate_costs()")) {
            throw new RuntimeException("Call failed: " . $conn->error);
        }

        // تنظيف أي نتائج إضافية ناتجة عن الإجراء المخزن (Stored Procedure)
        while ($conn->more_results() && $conn->next_result()) {
            $extra = $conn->use_result();
            if ($extra instanceof mysqli_result) {
                $extra->free();
            }
        }

        return true;
    } catch (Throwable $e) {
        error_log("Recost Error: " . $e->getMessage());
        return false;
    }
}
