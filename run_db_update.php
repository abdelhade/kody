<?php
/**
 * Database update runner — migrations 009–012 + disc_pct
 * Open: run_db_update.php?confirm=yes
 */

if (!isset($_GET['confirm']) || $_GET['confirm'] !== 'yes') {
    die('⚠️ لتنفيذ تحديث قاعدة البيانات، افتح: run_db_update.php?confirm=yes');
}

include('includes/connect.php');

function run_migration_query(mysqli $conn, string $query): array
{
    try {
        if ($conn->query($query)) {
            return ['ok' => true, 'skipped' => false, 'error' => ''];
        }
        $error = $conn->error;
    } catch (mysqli_sql_exception $e) {
        $error = $e->getMessage();
    }

    if (
        stripos($error, 'Duplicate column') !== false ||
        stripos($error, 'already exists') !== false
    ) {
        return ['ok' => true, 'skipped' => true, 'error' => $error];
    }

    return ['ok' => false, 'skipped' => false, 'error' => $error];
}

header('Content-Type: text/html; charset=utf-8');
echo '<!DOCTYPE html><html lang="ar" dir="rtl"><head><meta charset="UTF-8"><title>DB Update</title></head><body style="font-family:sans-serif;padding:20px;">';
echo '<h2>تحديث قاعدة البيانات</h2>';

$files = [
    'update/009_add_calc_type_to_employees.sql' => 'نوع حساب الراتب (employees.calc_type)',
    'update/010_add_payroll_calcs.sql'          => 'حسابات الرواتب + أعمدة attdocs',
    'update/011_add_single_fp_rule_to_shifts.sql' => 'قاعدة البصمة الواحدة (shifts)',
    'update/012_add_commission_to_settings.sql'   => 'عمولة الموظفين والمستخدمين (settings)',
];

$success = 0;
$errors  = 0;

foreach ($files as $file => $label) {
    echo "<h3>{$label}</h3><p><code>{$file}</code></p>";

    if (!file_exists($file)) {
        echo "<p style='color:red'>❌ الملف غير موجود</p>";
        $errors++;
        continue;
    }

    $sql      = file_get_contents($file);
    $queries  = array_filter(array_map('trim', explode(';', $sql)));
    $executed = 0;
    $failed   = 0;

    foreach ($queries as $query) {
        if ($query === '' || strpos($query, '--') === 0) {
            continue;
        }

        $result = run_migration_query($conn, $query);
        if ($result['ok']) {
            if ($result['skipped']) {
                echo "<p style='color:orange'>⚠️ موجود مسبقاً: " . htmlspecialchars($result['error']) . "</p>";
            }
            $executed++;
        } else {
            echo "<p style='color:red'>❌ " . htmlspecialchars($result['error']) . "</p>";
            $failed++;
        }
    }

    if ($failed === 0) {
        echo "<p style='color:green'>✅ تم ({$executed} استعلام)</p>";
        $success++;
    } else {
        $errors++;
    }
}

echo '<h3>خصم النسبة على بنود الفاتورة (fat_details.disc_pct)</h3>';
$disc_result = run_migration_query(
    $conn,
    "ALTER TABLE fat_details ADD COLUMN disc_pct DECIMAL(10,2) DEFAULT 0.00 AFTER discount"
);
if ($disc_result['ok']) {
    if ($disc_result['skipped']) {
        echo "<p style='color:orange'>⚠️ العمود موجود مسبقاً</p>";
    } else {
        echo "<p style='color:green'>✅ تم</p>";
    }
    $success++;
} else {
    echo "<p style='color:red'>❌ " . htmlspecialchars($disc_result['error']) . "</p>";
    $errors++;
}

echo "<hr><p><strong>النتيجة:</strong> {$success} ناجح، {$errors} فاشل</p>";
echo "<p><a href='dashboard.php'>الرئيسية</a></p>";
echo '</body></html>';

$conn->close();
