<?php
/**
 * ملف اختبار نظام Logging
 */

// بدء الجلسة
session_start();

// محاكاة بيانات المستخدم
$_SESSION['userid'] = 1;
$_SESSION['login'] = 'test_user';

// تحميل نظام Logging
require_once 'includes/simple_logger.php';

echo "<h2>اختبار نظام Logging</h2>";

// اختبار تسجيل حدث عادي
if (isset($logger)) {
    $logger->log('info', 'Test logging message', ['test' => true]);
    echo "<p>✅ تم تسجيل رسالة تجريبية</p>";
    
    // اختبار تسجيل مالي
    $logger->logFinancial('test_operation', 100.50, 1, 2, ['description' => 'Test financial operation']);
    echo "<p>✅ تم تسجيل عملية مالية تجريبية</p>";
    
    // اختبار تسجيل نظام
    $logger->logSystem('test', 'test_action', ['component' => 'test_system']);
    echo "<p>✅ تم تسجيل عملية نظام تجريبية</p>";
    
    // اختبار تسجيل تسجيل الدخول
    $logger->logLogin('test_user', true);
    echo "<p>✅ تم تسجيل تسجيل الدخول التجريبي</p>";
    
    // اختبار الحصول على السجلات
    $logs = $logger->getLogs('info', 5, 0);
    echo "<p>✅ تم الحصول على " . count($logs) . " سجل</p>";
    
    echo "<h3>آخر 5 سجلات:</h3>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>التاريخ</th><th>المستوى</th><th>الرسالة</th><th>المستخدم</th></tr>";
    
    foreach ($logs as $log) {
        echo "<tr>";
        echo "<td>" . $log['timestamp'] . "</td>";
        echo "<td>" . $log['level'] . "</td>";
        echo "<td>" . htmlspecialchars($log['message']) . "</td>";
        echo "<td>" . htmlspecialchars($log['username']) . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
} else {
    echo "<p>❌ فشل في تحميل نظام Logging</p>";
}

echo "<h3>✅ انتهى الاختبار!</h3>";
echo "<p><a href='index.php'>العودة للصفحة الرئيسية</a></p>";
?>
