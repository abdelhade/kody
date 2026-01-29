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
    // Include connection - suppress errors for include to avoid buffer pollution
    $connectPath = __DIR__ . '/../includes/connect.php';
    if (!file_exists($connectPath)) {
        throw new Exception("ملف الاتصال غير موجود");
    }
    
    // Use output buffering to trap any output from connect.php
    ob_start();
    include($connectPath);
    ob_end_clean(); // Discard noise from connect.php

    if (!isset($_SESSION['userid'])) {
        throw new Exception('الرجاء تسجيل الدخول أولاً');
    }

    if (!isset($conn) || $conn->connect_error) {
        throw new Exception('فشل الاتصال بقاعدة البيانات');
    }

    // Force UTF-8
    $conn->set_charset("utf8mb4");

    $user_id = $_SESSION['userid'];

    // Shift date logic
    if (!isset($today)) {
        date_default_timezone_set('Africa/Cairo');
        $now = new DateTime();
        if ((int)$now->format('H') < 4) {
            $now->modify('-1 day');
        }
        $today = $now->format('Y-m-d');
    }
    $shift_date = $today;

    $sql = "SELECT 
                COUNT(*) as total_orders,
                COALESCE(SUM(fat_net), 0) as total_sales
            FROM ot_head 
            WHERE DATE(pro_date) = ? 
            AND pro_tybe = 9 
            AND isdeleted = 0
            AND fat_net > 0
            AND `user` = ?";

    $sales_stmt = $conn->prepare($sql);
    
    if (!$sales_stmt) {
        throw new Exception('خطأ في تحضير الاستعلام: ' . $conn->error);
    }
    
    $sales_stmt->bind_param("si", $shift_date, $user_id);
    
    if (!$sales_stmt->execute()) {
        throw new Exception('خطأ في تنفيذ الاستعلام: ' . $sales_stmt->error);
    }

    $sales_result = $sales_stmt->get_result();
    $sales_data = $sales_result->fetch_assoc();
    $sales_stmt->close();
    
    $total_orders = intval($sales_data['total_orders'] ?? 0);
    $total_sales = floatval($sales_data['total_sales'] ?? 0);
    
    // Get cashier name
    $cashier_name = 'الكاشير';
    $user_stmt = $conn->prepare("SELECT aname FROM acc_head WHERE id = ?");
    if ($user_stmt) {
        $user_stmt->bind_param("i", $user_id);
        $user_stmt->execute();
        $user_result = $user_stmt->get_result();
        if ($row = $user_result->fetch_assoc()) {
            $cashier_name = $row['aname'];
        }
        $user_stmt->close();
    }
    
    $response_data = [
        'success' => true,
        'data' => [
            'total_orders' => $total_orders,
            'total_sales' => number_format($total_sales, 2),
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