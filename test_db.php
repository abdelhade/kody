<?php
// ملف اختبار قاعدة البيانات
$conn = new mysqli("localhost", "root", "", "focus");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "اتصال ناجح بقاعدة البيانات focus<br>";

// التحقق من وجود جدول acc_head
$result = $conn->query("SHOW TABLES LIKE 'acc_head'");
if ($result->num_rows > 0) {
    echo "جدول acc_head موجود<br>";
    
    // عرض بنية الجدول
    $structure = $conn->query("DESCRIBE acc_head");
    echo "<h3>بنية جدول acc_head:</h3>";
    echo "<table border='1'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = $structure->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "<td>" . $row['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // عرض عدد العملاء الموجودين
    $count = $conn->query("SELECT COUNT(*) as count FROM acc_head WHERE code LIKE '122%' AND isdeleted = 0");
    $count_row = $count->fetch_assoc();
    echo "<br>عدد العملاء الموجودين: " . $count_row['count'];
    
} else {
    echo "جدول acc_head غير موجود!<br>";
    
    // عرض جميع الجداول الموجودة
    $tables = $conn->query("SHOW TABLES");
    echo "<h3>الجداول الموجودة في قاعدة البيانات:</h3>";
    echo "<ul>";
    while ($table = $tables->fetch_array()) {
        echo "<li>" . $table[0] . "</li>";
    }
    echo "</ul>";
}

$conn->close();
?>