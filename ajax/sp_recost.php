<?php
header('Content-Type: application/json; charset=utf-8');
session_start();

if (!isset($_SESSION['userid'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'غير مصرح']);
    exit;
}

require_once __DIR__ . '/../includes/connect.php';

try {
    $t1 = microtime(true);

    // 1. التأكد من وجود عمود manual_price_edit في myitems
    $checkColumn = $conn->query("SHOW COLUMNS FROM myitems LIKE 'manual_price_edit'");
    if ($checkColumn && $checkColumn->num_rows == 0) {
        if (!$conn->query("ALTER TABLE myitems ADD COLUMN manual_price_edit TINYINT(1) DEFAULT 0")) {
            throw new RuntimeException('فشل إضافة عمود manual_price_edit');
        }
    }

    // 2. تحديث وتثبيت الإجراء المخزن sp_recalculate_costs من ملف SQL
    $spFile = __DIR__ . '/../db/sp_recalculate_costs.sql';
    if (!file_exists($spFile)) {
        throw new RuntimeException('ملف sp_recalculate_costs.sql غير موجود في مجلد db/');
    }

    $sql = file_get_contents($spFile);
    $sql = preg_replace('/DELIMITER\s+[^\r\n]+/i', '', $sql);
    $parts = explode('//', $sql);
    foreach ($parts as $p) {
        $p = trim($p);
        if (!empty($p)) {
            if (!$conn->query($p)) {
                throw new RuntimeException('فشل تثبيت الإجراء: ' . $conn->error);
            }
        }
    }

    // 3. استدعاء الإجراء المخزن
    if (!$conn->query("CALL sp_recalculate_costs()")) {
        throw new RuntimeException('فشل تشغيل الإجراء: ' . $conn->error);
    }

    while ($conn->more_results() && $conn->next_result()) {
        $extra = $conn->use_result();
        if ($extra instanceof mysqli_result) $extra->free();
    }

    $t2 = microtime(true);
    $ms = round(($t2 - $t1) * 1000, 1);

    echo json_encode([
        'success' => true,
        'message' => "تم تحديث وتثبيت الإجراء المخزن (Stored Procedure) وإعادة حساب التكاليف والأرباح بنجاح في {$ms} ملي ثانية!",
        'duration_ms' => $ms
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
