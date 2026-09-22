<?php
/**
 * ajax/run_migrations.php — تطبيق migrations الناقصة
 */
header('Content-Type: application/json; charset=utf-8');
session_start();

if (!isset($_SESSION['userid'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'غير مصرح']);
    exit;
}

require_once __DIR__ . '/../includes/connect.php';
require_once __DIR__ . '/../includes/MigrationRunner.php';

$action = $_POST['action'] ?? $_GET['action'] ?? 'status';
$runner = new MigrationRunner($conn);

try {
    if ($action === 'status') {
        echo json_encode(['success' => true, 'status' => $runner->status()]);
        exit;
    }

    if ($action === 'run') {
        $result = $runner->runPending();
        echo json_encode([
            'success' => $result['success'],
            'message' => $result['message'],
            'results' => $result['results'],
            'status' => $runner->status(),
        ]);
        exit;
    }

    echo json_encode(['success' => false, 'message' => 'إجراء غير صالح']);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
