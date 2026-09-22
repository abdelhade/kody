<?php
/**
 * Database update runner (uses MigrationRunner)
 * Open: run_db_update.php?confirm=yes
 */

if (!isset($_GET['confirm']) || $_GET['confirm'] !== 'yes') {
    die('⚠️ لتنفيذ تحديث قاعدة البيانات، افتح: run_db_update.php?confirm=yes');
}

include('includes/connect.php');
require_once __DIR__ . '/includes/MigrationRunner.php';

header('Content-Type: text/html; charset=utf-8');
echo '<!DOCTYPE html><html lang="ar" dir="rtl"><head><meta charset="UTF-8"><title>DB Update</title></head><body style="font-family:sans-serif;padding:20px;">';
echo '<h2>تحديث قاعدة البيانات</h2>';

$runner = new MigrationRunner($conn);
$statusBefore = $runner->status();
echo '<p>ناقص قبل التشغيل: <strong>' . (int) $statusBefore['pending'] . '</strong> / ' . (int) $statusBefore['total'] . '</p>';

$result = $runner->runPending();

foreach ($result['results'] as $row) {
    $ver = htmlspecialchars($row['version'] ?? '');
    if (!empty($row['ok'])) {
        $msg = !empty($row['skipped']) ? 'موجود مسبقاً (تخطي)' : ('تم — ' . (int) ($row['executed'] ?? 0) . ' استعلام');
        echo "<p style='color:green'>✅ <code>{$ver}</code> — {$msg}</p>";
    } else {
        echo "<p style='color:red'>❌ <code>{$ver}</code> — " . htmlspecialchars($row['error'] ?? '') . '</p>';
    }
}

$status = $runner->status();
echo '<hr><p><strong>' . htmlspecialchars($result['message']) . '</strong></p>';
echo '<p>الحالة الآن: مطبّق ' . (int) $status['applied'] . ' / ' . (int) $status['total'] . '</p>';
echo "<p><a href='dashboard.php'>الرئيسية</a> | <a href='setting.php'>الإعدادات</a></p>";
echo '</body></html>';

$conn->close();
