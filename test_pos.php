<?php
// تفعيل عرض الأخطاء
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h2>🧪 اختبار ملفات POS</h2>";

// 1. اختبار header.php
echo "<h3>📄 اختبار header.php:</h3>";
try {
    ob_start();
    include 'includes/header.php';
    $header_output = ob_get_clean();
    echo "<p style='color:green'>✅ header.php تم تحميله بنجاح</p>";
} catch (Exception $e) {
    echo "<p style='color:red'>❌ خطأ في header.php: " . $e->getMessage() . "</p>";
} catch (Error $e) {
    echo "<p style='color:red'>❌ خطأ فادح في header.php: " . $e->getMessage() . "</p>";
}

// 2. اختبار pos_barcode.php
echo "<h3>💳 اختبار pos_barcode.php:</h3>";
try {
    // قراءة محتوى الملف بدلاً من تنفيذه
    $pos_content = file_get_contents('pos_barcode.php');
    if ($pos_content === false) {
        echo "<p style='color:red'>❌ لا يمكن قراءة pos_barcode.php</p>";
    } else {
        echo "<p style='color:green'>✅ pos_barcode.php يمكن قراءته - الحجم: " . strlen($pos_content) . " بايت</p>";
        
        // البحث عن أخطاء شائعة
        if (strpos($pos_content, '<?php') === false) {
            echo "<p style='color:red'>❌ pos_barcode.php لا يحتوي على <?php</p>";
        }
        
        // اختبار syntax
        $temp_file = tempnam(sys_get_temp_dir(), 'pos_test');
        file_put_contents($temp_file, $pos_content);
        
        $output = shell_exec("php -l $temp_file 2>&1");
        if (strpos($output, 'No syntax errors') !== false) {
            echo "<p style='color:green'>✅ pos_barcode.php لا يحتوي على أخطاء syntax</p>";
        } else {
            echo "<p style='color:red'>❌ خطأ syntax في pos_barcode.php: " . htmlspecialchars($output) . "</p>";
        }
        
        unlink($temp_file);
    }
} catch (Exception $e) {
    echo "<p style='color:red'>❌ خطأ في اختبار pos_barcode.php: " . $e->getMessage() . "</p>";
}

// 3. اختبار الجلسة
echo "<h3>🔐 اختبار الجلسة:</h3>";
if (session_status() == PHP_SESSION_NONE) {
    session_start();
    echo "<p style='color:green'>✅ تم بدء الجلسة</p>";
} else {
    echo "<p style='color:orange'>⚠️ الجلسة مفعلة مسبقاً</p>";
}

// 4. اختبار المتغيرات المطلوبة
echo "<h3>🔧 اختبار المتغيرات:</h3>";
if (isset($_SESSION)) {
    echo "<p style='color:green'>✅ \$_SESSION متاح</p>";
} else {
    echo "<p style='color:red'>❌ \$_SESSION غير متاح</p>";
}

echo "<p><strong>انتهى الاختبار</strong></p>";
?>