<?php
ob_start();

error_reporting(E_ALL);
ini_set('display_errors', 0);

register_shutdown_function(function () {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
        while (ob_get_level()) {
            ob_end_clean();
        }
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => false,
            'error' => 'Fatal Error: ' . $error['message'] . ' on line ' . $error['line']
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
});

try {
    include(__DIR__ . '/../includes/ajax_header.php');
    require_once __DIR__ . '/../classes/ShiftCloser.php';

    if (!isset($_SESSION['userid'])) {
        throw new Exception('الرجاء تسجيل الدخول أولاً');
    }

    $user_id = (int) $_SESSION['userid'];
    $closer = new ShiftCloser($conn);
    $p = $closer->preview($user_id);
    $showExpected = ShiftCloser::canSeeExpected($rowstg ?? []);

    $accounts = [];
    foreach ($p['accounts'] as $acc) {
        $row = [
            'account_id' => $acc['account_id'],
            'name' => $acc['name'],
            'is_fund' => $acc['is_fund'],
            'is_primary' => $acc['account_id'] === $p['primary_fund'],
        ];
        if ($showExpected) {
            $row['start_amount'] = $acc['start_amount'];
            $row['movement'] = $acc['movement'];
            $row['expected'] = $acc['expected'];
        }
        $accounts[] = $row;
    }

    $data = [
        'blind' => !$showExpected,
        'cashier_name' => $p['cashier_name'] ?: 'الكاشير',
        'opened_at' => $p['opened_at'],
        'total_orders' => $p['orders_count'],
        'returns_count' => $p['returns_count'],
        'accounts' => $accounts,
    ];
    if ($showExpected) {
        $data += [
            'total_gross' => $p['sales_gross'],
            'total_discount' => $p['sales_discount'],
            'sales_net' => $p['sales_net'],
            'returns_total' => $p['returns_total'],
            'total_net' => $p['net_sales'],
            'expenses' => $p['expenses'],
            'receipts' => $p['receipts'],
            'start_cash' => $p['start_cash'],
        ];
    }

    while (ob_get_level()) {
        ob_end_clean();
    }
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => true, 'data' => $data], JSON_UNESCAPED_UNICODE);
    exit;
} catch (Throwable $e) {
    while (ob_get_level()) {
        ob_end_clean();
    }
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
    exit;
}
