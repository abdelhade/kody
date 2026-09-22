<?php
/**
 * ajax/period_ops.php — عمليات تعدد المدد / قواعد البيانات
 */
header('Content-Type: application/json; charset=utf-8');
session_start();

if (!isset($_SESSION['userid'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'غير مصرح']);
    exit;
}

require_once __DIR__ . '/../includes/connect.php';
require_once __DIR__ . '/../includes/PeriodManager.php';

$action = $_POST['action'] ?? $_GET['action'] ?? 'list';

try {
    $pm = new PeriodManager($conn, $dbhost, $dbuser, $dbpass, $dbname);

    if ($action === 'list') {
        echo json_encode(['success' => true] + $pm->listPeriods());
        exit;
    }

    if ($action === 'switch') {
        $name = trim((string) ($_POST['db_name'] ?? ''));
        echo json_encode($pm->switchTo($name));
        exit;
    }

    if ($action === 'create') {
        $name = trim((string) ($_POST['db_name'] ?? ''));
        $label = trim((string) ($_POST['label'] ?? ''));
        $result = $pm->createFresh($name, $label);
        echo json_encode($result);
        exit;
    }

    if ($action === 'close') {
        $name = trim((string) ($_POST['db_name'] ?? ''));
        $label = trim((string) ($_POST['label'] ?? ''));
        if ($name === '') {
            throw new InvalidArgumentException('أدخل اسم قاعدة المدة الجديدة');
        }
        $result = $pm->closePeriod($name, $label);
        echo json_encode($result);
        exit;
    }

    echo json_encode(['success' => false, 'message' => 'إجراء غير صالح']);
} catch (Throwable $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
