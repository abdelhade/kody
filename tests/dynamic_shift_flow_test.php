<?php

require_once __DIR__ . '/../includes/connect.php';
require_once __DIR__ . '/../classes/DynamicShiftDetector.php';
require_once __DIR__ . '/../includes/payroll_engine.php';

echo "=== Running Dynamic Shift End-to-End Test ===\n";

// 1. فحص وجود جدول مجموعات الورديات
$res = $conn->query("SHOW TABLES LIKE 'shift_groups'");
if (!$res || $res->num_rows === 0) {
    echo "❌ Table shift_groups not found\n";
    exit(1);
}
echo "✅ Table shift_groups exists\n";

$resItems = $conn->query("SHOW TABLES LIKE 'shift_group_items'");
if (!$resItems || $resItems->num_rows === 0) {
    echo "❌ Table shift_group_items not found\n";
    exit(1);
}
echo "✅ Table shift_group_items exists\n";

// 2. فحص الأعمدة الجديدة
$empCols = $conn->query("SHOW COLUMNS FROM employees LIKE 'shift_group_id'")->fetch_assoc();
if (!$empCols) {
    echo "❌ Column employees.shift_group_id missing\n";
    exit(1);
}
echo "✅ Column employees.shift_group_id exists\n";

$attlogCols = $conn->query("SHOW COLUMNS FROM attlog LIKE 'shift_id'")->fetch_assoc();
if (!$attlogCols) {
    echo "❌ Column attlog.shift_id missing\n";
    exit(1);
}
echo "✅ Column attlog.shift_id exists\n";

$attCols = $conn->query("SHOW COLUMNS FROM attandance LIKE 'detected_shift_id'")->fetch_assoc();
if (!$attCols) {
    echo "❌ Column attandance.detected_shift_id missing\n";
    exit(1);
}
echo "✅ Column attandance.detected_shift_id exists\n";

// 3. اختبار جلب الورديات التابعة لمجموعة
// إنشاء مجموعة تجريبية في transaction والتراجع عنها
$conn->begin_transaction();
try {
    $conn->query("INSERT INTO shift_groups (name, info, max_detect_window) VALUES ('مجموعة اختبار', 'وصف', 720)");
    $grpId = $conn->insert_id;

    // إنشاء وردية تجريبية داخل الـ transaction
    $conn->query("INSERT INTO shifts (name, shiftstart, shiftend, isdeleted) VALUES ('وردية اختبار', '08:00:00', '16:00:00', 0)");
    $shift1Id = $conn->insert_id;

    $conn->query("INSERT INTO shift_group_items (group_id, shift_id, sort) VALUES ($grpId, $shift1Id, 1)");

    $loaded = DynamicShiftDetector::getGroupShifts($conn, $grpId);
    if (empty($loaded) || (int)$loaded[0]['id'] !== $shift1Id) {
        throw new Exception("Failed to load group shifts via DynamicShiftDetector");
    }
    echo "✅ DynamicShiftDetector::getGroupShifts works properly\n";

    $conn->rollback();
    echo "✅ Transaction rollback cleanly completed\n";
} catch (Exception $e) {
    $conn->rollback();
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n🎉 ALL INTEGRATION TESTS PASSED!\n";
