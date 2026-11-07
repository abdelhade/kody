<?php
/**
 * ajax/rebuild_cost_journals.php — إعادة بناء قيود تكلفة المبيعات بأثر رجعي
 */
header('Content-Type: application/json; charset=utf-8');
session_start();

if (!isset($_SESSION['userid'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'غير مصرح']);
    exit;
}

require_once __DIR__ . '/../includes/connect.php';
require_once __DIR__ . '/../classes/InvoiceProcessor.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'طريقة طلب غير مسموحة']);
    exit;
}

set_time_limit(0);

try {
    $t1 = microtime(true);
    $result = InvoiceProcessor::rebuildAllCostOfSalesJournals($conn);
    $sec = round(microtime(true) - $t1, 1);

    echo json_encode([
        'success' => true,
        'message' => 'تم إعادة بناء قيود تكلفة المبيعات بأثر رجعي في ' . $sec . ' ثانية'
            . "\nفواتير تمت مراجعتها: " . $result['invoices']
            . "\nقيود تكلفة قائمة: " . $result['journals']
            . "\nإجمالي التكلفة: " . number_format($result['total_cost'], 2)
            . "\nفواتير اتصحّح قيدها الرئيسي (كان بيقيد المخزن بقيمة البيع): " . $result['main_fixed'],
        'result' => $result,
    ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
