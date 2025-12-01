<?php
header('Content-Type: application/json; charset=utf-8');
include('../includes/connect.php');

try {
    if (!isset($_POST['action']) || !isset($_POST['table_id'])) {
        throw new Exception('المعاملات مفقودة');
    }
    
    $table_id = intval($_POST['table_id']);
    $action = $_POST['action'];
    
    if ($table_id <= 0) {
        throw new Exception('رقم الطاولة غير صحيح');
    }
    
    if ($action == 'clear') {
        // جلب اسم الطاولة
        $table_query = "SELECT tname FROM tables WHERE id = $table_id";
        $table_result = $conn->query($table_query);
        
        if ($table_result && $table_result->num_rows > 0) {
            $table_name = $table_result->fetch_assoc()['tname'];
            
            // حذف الطلب النشط للطاولة
            $conn->query("DELETE FROM ot_head WHERE info LIKE '%$table_name%' AND pro_tybe = 9");
            
            // حذف أصناف الطلب
            $conn->query("DELETE fd FROM fat_details fd 
                         JOIN ot_head oh ON fd.pro_id = oh.id 
                         WHERE oh.info LIKE '%$table_name%' AND oh.pro_tybe = 9");
        }
        
        // تفريغ الطاولة
        $sql = "UPDATE tables SET table_case = 0 WHERE id = $table_id";
        $message = 'تم تفريغ الطاولة بنجاح';
    } elseif ($action == 'activate') {
        $sql = "UPDATE tables SET table_case = 1 WHERE id = $table_id";
        $message = 'تم تشغيل الطاولة بنجاح';
    } else {
        throw new Exception('عملية غير صحيحة');
    }
    
    if (!$conn->query($sql)) {
        throw new Exception('خطأ في قاعدة البيانات: ' . $conn->error);
    }
    
    echo json_encode([
        'success' => true,
        'message' => $message,
        'table_id' => $table_id
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>