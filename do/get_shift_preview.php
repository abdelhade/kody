<?php
// منع عرض أي أخطاء PHP قد تفسد الـ JSON
error_reporting(0);
ini_set('display_errors', 0);

// بدء أوتبوت بفرنغ لمنع أي مسافات أو مخرجات غير مقصودة
ob_start();

session_start();
include('../includes/connect.php');

// تنظيف البفر للتأكد من عدم وجود أي شيء قبل الـ JSON
// هذا سيحذف أي مسافات بيضاء أو أخطاء تم طباعتها من ملف الاتصال
ob_clean();
header('Content-Type: application/json; charset=utf-8');

// التحقق من تسجيل الدخول
if (!isset($_SESSION['userid'])) {
    echo json_encode(['success' => false, 'error' => 'غير مسموح']);
    exit;
}

$user_id = $_SESSION['userid'];
// ضمان وجود التاريخ مع مراعاة توقيت الشيفت (يبدأ يوم جديد بعد 4 فجراً)
if (!isset($today)) {
    date_default_timezone_set('Africa/Cairo');
    $now = new DateTime();
    if ((int)$now->format('H') < 4) {
        $now->modify('-1 day');
    }
    $today = $now->format('Y-m-d');
}
$shift_date = $today;

try {
    // حساب مبيعات المستخدم الحالي لليوم
    $sales_stmt = $conn->prepare("SELECT 
                        COUNT(*) as total_orders,
                        COALESCE(SUM(fat_net), 0) as total_sales
                    FROM ot_head 
                    WHERE DATE(pro_date) = ? 
                    AND pro_tybe = 9 
                    AND isdeleted = 0
                    AND fat_net > 0
                    AND user = ?");
    
    if (!$sales_stmt) {
        throw new Exception('فشل في تحضير استعلام المبيعات');
    }
    
    $sales_stmt->bind_param("ss", $shift_date, $user_id);
    $sales_stmt->execute();
    $sales_result = $sales_stmt->get_result();
    
    if (!$sales_result) {
        throw new Exception('خطأ في تنفيذ الاستعلام');
    }
    
    $sales_data = $sales_result->fetch_assoc();
    $sales_stmt->close();
    
    $total_orders = intval($sales_data['total_orders'] ?? 0);
    $total_sales = floatval($sales_data['total_sales'] ?? 0);
    
    // جلب اسم المستخدم use Prepared Statement
    $user_stmt = $conn->prepare("SELECT aname FROM acc_head WHERE id = ?");
    if ($user_stmt) {
        $user_stmt->bind_param("i", $user_id);
        $user_stmt->execute();
        $user_result = $user_stmt->get_result();
        
        if ($user_result && $user_result->num_rows > 0) {
            $user_data = $user_result->fetch_assoc();
            $cashier_name = $user_data['aname'] ?? 'الكاشير';
        } else {
            $cashier_name = 'الكاشير';
        }
        $user_stmt->close();
    } else {
        $cashier_name = 'الكاشير'; // Fallback
    }
    
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
        'error' => 'خطأ: ' . $e->getMessage()
    ]);
}
?>