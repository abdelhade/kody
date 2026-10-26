<?php
require_once __DIR__ . '/../includes/connect.php';
require_once __DIR__ . '/../classes/Can.php';

header('Content-Type: application/json; charset=utf-8');

Can::authorize('employees.edit');

$emp_id = isset($_POST['emp_id']) ? (int)$_POST['emp_id'] : 0;
$shift  = isset($_POST['shift'])  ? (int)$_POST['shift']  : 0;

if ($emp_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'معرف الموظف غير صالح']);
    exit;
}

$shift_val = $shift > 0 ? $shift : null;

$stmt = $conn->prepare("UPDATE `employees` SET `shift` = ? WHERE `id` = ? AND COALESCE(`isdeleted`, 0) != 1");
$stmt->bind_param('ii', $shift_val, $emp_id);
$ok = $stmt->execute();
$stmt->close();

if (!$ok) {
    echo json_encode(['success' => false, 'message' => 'خطأ في قاعدة البيانات']);
    exit;
}

$shift_name = null;
if ($shift_val) {
    $s = $conn->prepare("SELECT `name` FROM `shifts` WHERE `id` = ?");
    $s->bind_param('i', $shift_val);
    $s->execute();
    $s->bind_result($shift_name);
    $s->fetch();
    $s->close();
}

echo json_encode(['success' => true, 'shift_name' => $shift_name, 'message' => 'تم تحديث الوردية بنجاح']);
