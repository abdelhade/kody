<?php
require_once __DIR__ . '/../includes/connect.php';
require_once __DIR__ . '/../classes/Roles.php';

$back = static function (string $query): never {
    header('Location: ../roles.php?' . $query);
    exit;
};

$id = (int) ($_REQUEST['id'] ?? 0);
if ($id < 1 || !Roles::find($conn, $id)) {
    $back('error=' . urlencode('الدور غير موجود'));
}
if ($id === 1) {
    $back('error=' . urlencode('لا يمكن حذف الدور الإداري الرئيسي'));
}
if ($id === Can::roleId()) {
    $back('error=' . urlencode('لا يمكن حذف الدور بتاعك'));
}
if (Roles::usersCount($conn, $id) > 0) {
    $back('error=' . urlencode('لا يمكن حذف دور مرتبط بمستخدمين، انقلهم لدور تاني الأول'));
}

// role_has_permissions بيتمسح معاه (FK ON DELETE CASCADE)
$stmt = $conn->prepare('DELETE FROM `roles` WHERE `id` = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$stmt->close();

Roles::log($conn, 'delete role: ' . $id . ' by user ' . Can::userId());

$back('msg=deleted');
