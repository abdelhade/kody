<?php
require_once __DIR__ . '/../includes/connect.php';
require_once __DIR__ . '/../classes/Roles.php';

$fail = static function (string $message): never {
    echo '<script>alert(' . json_encode($message, JSON_UNESCAPED_UNICODE) . '); history.back();</script>';
    exit;
};

$uname    = trim((string) ($_POST['uname'] ?? ''));
$password = (string) ($_POST['password'] ?? '');
$userrole = (int) ($_POST['userrole'] ?? 0);
$isWaiter = isset($_POST['is_waiter']) ? 1 : 0;

if ($uname === '') {
    $fail('اسم المستخدم مطلوب');
}
if (($err = Roles::assignError($conn, $userrole)) !== null) {
    $fail($err);
}
$hashpass = md5($password);

$img = '';
if (!empty($_FILES['img']['name']) && ($_FILES['img']['size'] ?? 0) > 100) {
    $ext = strtolower(pathinfo((string) $_FILES['img']['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg', 'png', 'gif', 'jpeg'], true)) {
        $fail('الملف المحمل ليس صوره او امتداد غير مسموح به');
    }
    if ($_FILES['img']['size'] > 2000000) {
        $fail('الصورة أكبر من 2 ميجا');
    }
    $base = preg_replace('/[^A-Za-z0-9_\-]/', '_', pathinfo((string) $_FILES['img']['name'], PATHINFO_FILENAME));
    $img = $base . rand(1, 1000000) . '.' . $ext;
    move_uploaded_file($_FILES['img']['tmp_name'], __DIR__ . '/../uploads/' . $img);
}

$stmt = $conn->prepare('INSERT INTO users (uname, password, userrole, img, is_waiter) VALUES (?, ?, ?, ?, ?)');
$stmt->bind_param('ssisi', $uname, $hashpass, $userrole, $img, $isWaiter);
$stmt->execute();
$newUserId = (int) $stmt->insert_id;
$stmt->close();
UserDefaults::save($conn, $newUserId, $_POST);

Roles::log($conn, 'add user ' . $uname . ' role ' . $userrole);

header('location:../users.php');
