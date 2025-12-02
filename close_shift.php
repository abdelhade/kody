<?php
session_start();
include('includes/connect.php');

// التحقق من تسجيل الدخول
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$shift_date = date('Y-m-d');
$shift_time = date('H:i:s');

try {
    // حساب إجمالي المبيعات لليوم
    $sales_query = "SELECT 
                        COUNT(*) as total_orders,
                        SUM(fat_net) as total_sales,
                        SUM(fat_disc) as total_discount
                    FROM ot_head 
                    WHERE DATE(pro_date) = '$shift_date' 
                    AND pro_tybe = 9 
                    AND isdeleted = 0";
    
    $sales_result = $conn->query($sales_query);
    $sales_data = $sales_result->fetch_assoc();
    
    $total_orders = $sales_data['total_orders'] ?? 0;
    $total_sales = $sales_data['total_sales'] ?? 0;
    $total_discount = $sales_data['total_discount'] ?? 0;
    
    // جلب اسم المستخدم
    $user_query = "SELECT username FROM users WHERE id = '$user_id'";
    $user_result = $conn->query($user_query);
    $username = $user_result ? $user_result->fetch_assoc()['username'] : 'Unknown';
    
    // إدراج سجل إغلاق الشيفت
    $shift_number = date('Ymd') . '_' . $user_id;
    $insert_query = "INSERT INTO closed_orders 
                     (shift, date, user, endtime, total_sales, expenses, exp_notes, cash, fund_after, info) 
                     VALUES 
                     ('$shift_number', '$shift_date', '$username', '$shift_time', '$total_sales', '0', 'إغلاق تلقائي', '$total_sales', '$total_sales', 'إغلاق شيفت تلقائي - عدد الطلبات: $total_orders')";
    
    if ($conn->query($insert_query)) {
        $_SESSION['success_message'] = 'تم إغلاق الشيفت بنجاح - إجمالي المبيعات: ' . number_format($total_sales, 2) . ' ج.م';
    } else {
        $_SESSION['error_message'] = 'حدث خطأ أثناء إغلاق الشيفت';
    }
    
} catch (Exception $e) {
    $_SESSION['error_message'] = 'حدث خطأ: ' . $e->getMessage();
}

// إعادة التوجيه إلى صفحة الجلسات المغلقة
header('Location: closed_sessions.php');
exit;
?>