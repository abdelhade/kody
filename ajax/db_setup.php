<?php
// ajax/db_setup.php - Database Setup Backend (Single Source of Truth Controller)
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../includes/load_env.php';
require_once __DIR__ . '/../includes/db_name.php';
require_once __DIR__ . '/../includes/PeriodManager.php';
require_once __DIR__ . '/../includes/MigrationRunner.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$dbhost = env('DB_HOST', 'localhost');
$dbuser = env('DB_USER', 'root');
$dbpass = env('DB_PASS', '');

mysqli_report(MYSQLI_REPORT_OFF);

// مفتوح من غير دخول للتجهيز أول مرة بس (مفيش قاعدة بيانات). لو فيه قاعدة شغّالة لازم system.manage
$probe = @new mysqli($dbhost, $dbuser, $dbpass);
$hasDatabase = !$probe->connect_error && kody_select_existing_db($probe) !== null;
$probe->close();
if ($hasDatabase) {
    require_once __DIR__ . '/../classes/Can.php';
    Can::authorize('system.manage');
}

$action = $_POST['action'] ?? '';

try {
    $pm = PeriodManager::fromEnv();

    if ($action === 'create') {
        $requested = trim((string) ($_POST['db_name'] ?? ''));
        $label = trim((string) ($_POST['label'] ?? ''));
        if ($requested === '') {
            echo json_encode(['success' => false, 'message' => 'أدخل اسم قاعدة البيانات']);
            exit;
        }

        $result = $pm->createFresh($requested, $label);
        echo json_encode($result);
        exit;
    }

    if ($action === 'restore') {
        if (!isset($_FILES['backup_file'])) {
            echo json_encode(['success' => false, 'message' => 'لم يتم رفع أي ملف']);
            exit;
        }

        $file = $_FILES['backup_file'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'message' => 'خطأ في رفع الملف: ' . $file['error']]);
            exit;
        }

        $requested = trim((string) ($_POST['db_name'] ?? ''));
        $label = trim((string) ($_POST['label'] ?? ''));
        if ($requested === '') {
            echo json_encode(['success' => false, 'message' => 'أدخل اسم قاعدة البيانات المراد الاستعادة إليها']);
            exit;
        }

        $result = $pm->restoreBackup($requested, $file['tmp_name'], $label);
        echo json_encode($result);
        exit;
    }

    if ($action === 'migrate') {
        $conn = @new mysqli($dbhost, $dbuser, $dbpass);
        if ($conn->connect_error) {
            echo json_encode(['success' => false, 'message' => 'فشل الاتصال: ' . $conn->connect_error]);
            exit;
        }
        $selected = kody_select_existing_db($conn);
        if ($selected === null) {
            echo json_encode(['success' => false, 'message' => 'قاعدة البيانات غير موجودة']);
            exit;
        }
        $runner = new MigrationRunner($conn);
        $result = $runner->runPending();
        echo json_encode([
            'success' => $result['success'],
            'message' => $result['message'],
            'results' => $result['results'],
            'status' => $runner->status(),
        ]);
        $conn->close();
        exit;
    }

    echo json_encode(['success' => false, 'message' => 'إجراء غير صالح']);
} catch (Throwable $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
