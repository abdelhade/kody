<?php
include('includes/connect.php');

// Check if tables table exists
$check = $conn->query("SHOW TABLES LIKE 'tables'");
if ($check && $check->num_rows > 0) {
    echo "جدول الطاولات موجود<br>";
    
    // Get tables count
    $result = $conn->query("SELECT COUNT(*) as count FROM tables WHERE isdeleted = 0");
    $count = $result->fetch_assoc()['count'];
    echo "عدد الطاولات: " . $count . "<br>";
    
    // Show tables
    $result = $conn->query("SELECT * FROM tables WHERE isdeleted = 0 ORDER BY id ASC");
    if ($result && $result->num_rows > 0) {
        echo "<br>الطاولات المتاحة:<br>";
        while ($row = $result->fetch_assoc()) {
            echo $row['id'] . ' - ' . $row['tname'] . ' - الحالة: ' . ($row['table_case'] == 0 ? 'متاحة' : 'مشغولة') . '<br>';
        }
    }
} else {
    echo "جدول الطاولات غير موجود - سيتم إنشاؤه عند فتح الصفحة";
}
?>
