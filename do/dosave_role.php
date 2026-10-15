<?php
require_once __DIR__ . '/../includes/connect.php';
require_once __DIR__ . '/../classes/Roles.php';

$back = static function (string $query): never {
    header('Location: ../roles.php?' . $query);
    exit;
};

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $back('');
}

$roleId = (int) ($_POST['id'] ?? 0);
Can::authorize($roleId > 0 ? 'roles.edit' : 'roles.add');

if (!hash_equals((string) ($_SESSION['csrf_token'] ?? ''), (string) ($_POST['csrf_token'] ?? ''))) {
    $back('error=' . urlencode('انتهت صلاحية الصفحة، افتحها تاني وحاول مرة أخرى'));
}

$name        = trim((string) ($_POST['name'] ?? ''));
$description = trim((string) ($_POST['description'] ?? ''));
$isActive    = !empty($_POST['is_active']);
$permIds     = array_map('intval', (array) ($_POST['perms'] ?? []));

// محدش يدّي صلاحية هو نفسه معندوش (الصلاحيات اللي في الدور أصلاً تفضل زي ما هي)
$current = $roleId > 0 ? array_flip(Roles::permissionNames($conn, $roleId)) : [];
$nameById = array_column(Roles::allPermissions($conn), 'name', 'id');
foreach ($permIds as $pid) {
    $permName = $nameById[$pid] ?? null;
    if ($permName !== null && !isset($current[$permName]) && !Can::check($permName)) {
        $back('error=' . urlencode('مش مسموح تدّي صلاحية انت معندكش: ' . $permName));
    }
}
if ($roleId === 1 || ($roleId > 0 && $roleId === Can::roleId())) {
    $permIds = array_merge($permIds, Roles::idsFor($conn, Roles::LOCK_PERMISSIONS));
}

if ($name === '' || mb_strlen($name) > 190) {
    $back('error=' . urlencode('اسم الدور مطلوب (190 حرف بحد أقصى)'));
}
if (Roles::nameTaken($conn, $name, $roleId)) {
    $back('error=' . urlencode('فيه دور تاني بنفس الاسم'));
}
if ($roleId > 0 && !Roles::find($conn, $roleId)) {
    $back('error=' . urlencode('الدور غير موجود'));
}
if ($roleId === 1 && !$isActive) {
    $back('error=' . urlencode('مينفعش تعطيل الدور الأساسي'));
}

$conn->begin_transaction();
try {
    if ($roleId > 0) {
        Roles::update($conn, $roleId, $name, $description, $isActive);
    } else {
        $roleId = Roles::create($conn, $name, $description, $isActive);
    }
    $count = Roles::syncPermissions($conn, $roleId, $permIds);
    Roles::log($conn, sprintf('save role #%d (%s): %d permissions by user %d', $roleId, $name, $count, Can::userId()));
    $conn->commit();
} catch (Throwable $e) {
    $conn->rollback();
    throw $e;
}

$back('msg=saved');
