<?php
session_start();
include('../includes/connect.php');

// التحقق من تسجيل الدخول
if (!isset($_SESSION['userid'])) {
    echo json_encode(['success' => false, 'error' => 'غير مسموح']);
    exit;
}

$user_id = $_SESSION['userid'];
$shift_date = date('Y-m-d');

try {
    // حساب مبيعات المستخدم الحالي لليوم
    $sales_query = "SELECT 
                        COUNT(*) as total_orders,
                        COALESCE(SUM(fat_net), 0) as total_sales
                    FROM ot_head 
                    WHERE DATE(pro_date) = '$shift_date' 
                    AND pro_tybe = 9 
                    AND isdeleted = 0
                    AND fat_net > 0
                    AND user = '$user_id'";
    
    $sales_result = $conn->query($sales_query);
    
    if (!$sales_result) {
        throw new Exception('خطأ في استعلام قاعدة البيانات');
    }
    
    $sales_data = $sales_result->fetch_assoc();
    
    $total_orders = intval($sales_data['total_orders'] ?? 0);
    $total_sales = floatval($sales_data['total_sales'] ?? 0);
    
    // جلب اسم المستخدم
    $user_query = $conn->query("SELECT aname FROM acc_head WHERE id = $user_id");
    $user_data = $user_query->fetch_assoc();
    $cashier_name = $user_data['aname'] ?? 'الكاشير';
    
    $response = [
        'success' => true,
        'data' => [
            'total_orders' => $total_orders,
            'total_sales' => number_format($total_sales, 2),
            'cashier_name' => $cashier_name,
            'shift_number' => date('Ymd') . '_' . $user_id
        ]
    ];
    
    echo json_encode($response);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'error' => 'خطأ في تحميل البيانات'
    ]);
}
?>