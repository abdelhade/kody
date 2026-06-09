<?php
// Prevent any output before we are ready
ob_start();

error_reporting(E_ALL);
ini_set('display_errors', 0); // Keep 0 to prevent noise in JSON response

session_start();

// Valid shutdown handler to catch fatal errors
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error && ($error['type'] === E_ERROR || $error['type'] === E_PARSE || $error['type'] === E_CORE_ERROR || $error['type'] === E_COMPILE_ERROR)) {
        // Clear buffer
        while (ob_get_level()) ob_end_clean();
        
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => false, 'error' => 'Fatal Error: ' . $error['message'] . ' on line ' . $error['line']]);
        exit;
    }
});

try {
    // Include connection
    $connectPath = __DIR__ . '/../includes/connect.php';
    if (!file_exists($connectPath)) {
        throw new Exception("ملف الاتصال غير موجود");
    }
    
    // Include ShiftReport Class
    $classPath = __DIR__ . '/../classes/ShiftReport.php';
    if (!file_exists($classPath)) {
        throw new Exception("ملف كلاس التقرير غير موجود");
    }
    
    // Use output buffering to trap any output from includes
    ob_start();
    include($connectPath);
    include($classPath);
    ob_end_clean(); // Discard noise

    if (!isset($_SESSION['userid'])) {
        throw new Exception('الرجاء تسجيل الدخول أولاً');
    }

    if (!isset($conn) || $conn->connect_error) {
        throw new Exception('فشل الاتصال بقاعدة البيانات');
    }

    // Force UTF-8
    $conn->set_charset("utf8mb4");

    $user_id = $_SESSION['userid'];

    // Create ShiftReport instance
    $report = new ShiftReport($conn, $user_id);
    
    // Get Totals using the unified logic (respects time boundaries)
    $totals = $report->getTotals();
    
    // Get cashier name
    $cashier_name = 'الكاشير';
    $user_stmt = $conn->prepare("SELECT uname FROM users WHERE id = ?");
    if ($user_stmt) {
        $user_stmt->bind_param("i", $user_id);
        $user_stmt->execute();
        $user_result = $user_stmt->get_result();
        if ($row = $user_result->fetch_assoc()) {
            $cashier_name = $row['uname'];
        }
        $user_stmt->close();
    }
    
    $start_cash = 0.00;
    // جلب آخر قيمة fund_after للشيفت السابق لهذا الكاشير
    $prev_shift_stmt = $conn->prepare("SELECT fund_after FROM closed_orders WHERE user = ? ORDER BY id DESC LIMIT 1");
    if ($prev_shift_stmt) {
        $prev_shift_stmt->bind_param("s", $cashier_name);
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
            'shift_number' => date('Ymd') . '_' . $user_id
        ]
    ];

    // Final buffer clean
    while (ob_get_level()) ob_end_clean();
    
    header('Content-Type: application/json; charset=utf-8');
    
    $json = json_encode($response_data, JSON_UNESCAPED_UNICODE);
    
    if ($json === false) {
        throw new Exception('JSON Encode Error: ' . json_last_error_msg());
    }
    
    echo $json;
    exit;

} catch (Exception $e) {
    while (ob_get_level()) ob_end_clean();
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
    exit;
}
?>