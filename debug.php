<?php
// تفعيل عرض الأخطاء
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h2>🔍 تشخيص النظام</h2>";

// 1. اختبار PHP
echo "<p>✅ PHP يعمل - الإصدار: " . phpversion() . "</p>";

// 2. اختبار قاعدة البيانات
echo "<h3>📊 اختبار قاعدة البيانات:</h3>";
try {
    $dbhost = '127.0.0.1';
    $dbuser = 'u173148011_focua';
    $dbpass = 'AbAbAb@1234';
    $dbname = 'u173148011_focus';
    
    $conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
    
    if ($conn->connect_error) {
        echo "<p style='color:red'>❌ خطأ في الاتصال: " . $conn->connect_error . "</p>";
    } else {
        echo "<p style='color:green'>✅ الاتصال بقاعدة البيانات نجح</p>";
        
        // اختبار جدول settings
        $result = $conn->query("SELECT COUNT(*) as count FROM settings");
        if ($result) {
            $row = $result->fetch_assoc();
            echo "<p style='color:green'>✅ جدول settings موجود - عدد السجلات: " . $row['count'] . "</p>";
        } else {
            echo "<p style='color:red'>❌ جدول settings غير موجود: " . $conn->error . "</p>";
        }
    }
} catch (Exception $e) {
    echo "<p style='color:red'>❌ خطأ: " . $e->getMessage() . "</p>";
}

// 3. اختبار الملفات المطلوبة
echo "<h3>📁 اختبار الملفات:</h3>";
$files = [
    'includes/connect.php',
    'includes/simple_logger.php',
    'includes/header.php',
    'pos_barcode.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "<p style='color:green'>✅ $file موجود</p>";
    } else {
        echo "<p style='color:red'>❌ $file مفقود</p>";
    }
}

// 4. اختبار تحميل connect.php
echo "<h3>🔗 اختبار تحميل connect.php:</h3>";
try {
    include 'includes/connect.php';
    echo "<p style='color:green'>✅ تم تحميل connect.php بنجاح</p>";
    
    if (isset($rowstg) && $rowstg) {
        echo "<p style='color:green'>✅ إعدادات النظام تم تحميلها</p>";
    } else {
        echo "<p style='color:orange'>⚠️ إعدادات النظام فارغة</p>";
    }
} catch (Exception $e) {
    echo "<p style='color:red'>❌ خطأ في تحميل connect.php: " . $e->getMessage() . "</p>";
}

echo "<p><strong>انتهى التشخيص</strong></p>";
?>