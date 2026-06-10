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
    include(__DIR__ . '/../classes/ShiftReport.php');

    if (!isset($_SESSION['userid'])) {
        throw new Exception('الرجاء تسجيل الدخول أولاً');
    }

    $user_id = (int) $_SESSION['userid'];
    $shift_date = isset($today) ? $today : ShiftReport::getBusinessDate();

    $report = new ShiftReport($conn, $user_id, $shift_date);
    $totals = $report->getTotals();

    $cashier_name = 'الكاشير';
    $user_stmt = $conn->prepare('SELECT uname FROM users WHERE id = ?');
    if ($user_stmt) {
        $user_stmt->bind_param('i', $user_id);
        $user_stmt->execute();
        $user_result = $user_stmt->get_result();
        if ($row = $user_result->fetch_assoc()) {
            $cashier_name = $row['uname'];
        }
        $user_stmt->close();
    }

    $start_cash = 0.00;
    $prev_shift_stmt = $conn->prepare('SELECT fund_after FROM closed_orders WHERE user = ? ORDER BY id DESC LIMIT 1');
    if ($prev_shift_stmt) {
        $prev_shift_stmt->bind_param('s', $cashier_name);
        $prev_shift_stmt->execute();
        $prev_res = $prev_shift_stmt->get_result();
        if ($prev_row = $prev_res->fetch_assoc()) {
            $start_cash = floatval($prev_row['fund_after']);
        }
        $prev_shift_stmt->close();
    }

    $response_data = [
        'success' => true,
        'data' => [
            'total_orders' => intval($totals['total_orders'] ?? 0),
            'total_gross' => floatval($totals['total_gross'] ?? 0),
            'total_discount' => floatval($totals['total_discount'] ?? 0),
            'total_net' => floatval($totals['total_net'] ?? 0),
            'start_cash' => $start_cash,
            'cashier_name' => $cashier_name,
            'shift_number' => date('Ymd', strtotime($shift_date)) . '_' . $user_id,
            'shift_date' => $shift_date,
        ]
    ];

    while (ob_get_level()) {
        ob_end_clean();
    }

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($response_data, JSON_UNESCAPED_UNICODE);
    exit;
} catch (Exception $e) {
    while (ob_get_level()) {
        ob_end_clean();
    }
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
    exit;
}
