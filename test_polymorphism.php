<?php
/**
 * ملف اختبار للنظام الجديد القائم على polymorphism
 * Test file for the new polymorphism-based system
 */

// محاكاة قاعدة البيانات للاختبار
$mockConnection = null;

// تضمين الفئات
require_once 'classes/InvoiceElementFactory.php';

echo "<h1>اختبار النظام الجديد للفواتير باستخدام Polymorphism</h1>";

// اختبار إنشاء عناصر مختلفة
$testTypes = [
    3 => 'فاتورة مبيعات',
    4 => 'فاتورة مشتريات',
    10 => 'مردود مشتريات',
    11 => 'مردود مبيعات'
];

foreach ($testTypes as $type => $name) {
    echo "<h2>اختبار: {$name} (النوع: {$type})</h2>";
    
    try {
        // إنشاء جميع العناصر
        $elements = InvoiceElementFactory::createAllElements($type, false, null, $mockConnection);
        
        echo "<h3>العناصر المُنشأة بنجاح:</h3><ul>";
        foreach ($elements as $elementName => $element) {
            $className = get_class($element);
            echo "<li><strong>{$elementName}</strong>: {$className}</li>";
        }
        echo "</ul>";
        
        // اختبار التحقق من صحة البيانات
        $validationErrors = InvoiceElementFactory::validateAllElements($elements);
        
        if (empty($validationErrors)) {
            echo "<p style='color: green;'>✓ جميع العناصر صالحة</p>";
        } else {
            echo "<p style='color: orange;'>⚠ أخطاء التحقق:</p><ul>";
            foreach ($validationErrors as $elementName => $errors) {
                echo "<li><strong>{$elementName}</strong>: " . implode(', ', $errors) . "</li>";
            }
            echo "</ul>";
        }
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ خطأ: " . $e->getMessage() . "</p>";
    }
    
    echo "<hr>";
}

// اختبار وضع التعديل
echo "<h2>اختبار وضع التعديل</h2>";
$editData = [
    'id' => 123,
    'pro_tybe' => 3,
    'acc2' => 1,
    'store_id' => 2,
    'emp_id' => 3,
    'pro_date' => '2024-01-01',
    'fat_total' => 1000,
    'pro_value' => 950
];

try {
    $editElements = InvoiceElementFactory::createAllElements(3, true, $editData, $mockConnection);
    echo "<p style='color: green;'>✓ تم إنشاء عناصر وضع التعديل بنجاح</p>";
    
    foreach ($editElements as $elementName => $element) {
        echo "<p><strong>{$elementName}</strong>: " . ($element->isEditMode() ? 'وضع التعديل' : 'وضع الإضافة') . "</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ خطأ في وضع التعديل: " . $e->getMessage() . "</p>";
}

echo "<h3>مبادئ Polymorphism المطبقة:</h3>";
echo "<ul>";
echo "<li><strong>الوراثة (Inheritance):</strong> جميع العناصر ترث من InvoiceElementBase</li>";
echo "<li><strong>التغليف (Encapsulation):</strong> كل عنصر يحتوي على بياناته ووظائفه الخاصة</li>";
echo "<li><strong>تعدد الأشكال (Polymorphism):</strong> نفس الواجهة (render, validate) لعناصر مختلفة</li>";
echo "<li><strong>التجريد (Abstraction):</strong> دوال مجردة يجب تنفيذها في كل فئة فرعية</li>";
echo "<li><strong>Factory Pattern:</strong> إنشاء العناصر بطريقة منظمة ومرنة</li>";
echo "</ul>";

echo "<p><strong>الفوائد المحققة:</strong></p>";
echo "<ul>";
echo "<li>كود أكثر تنظيماً وقابلية للصيانة</li>";
echo "<li>سهولة إضافة أنواع فواتير جديدة</li>";
echo "<li>فصل المنطق والعرض</li>";
echo "<li>أمان أكبر مع Prepared Statements</li>";
echo "<li>تحقق من صحة البيانات موحد</li>";
echo "<li>إعادة استخدام الكود</li>";
echo "</ul>";
?>