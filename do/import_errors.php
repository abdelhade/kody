<?php

require __DIR__ . '/items_template.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['login'])) {
    header('Location: ../index.php');
    exit;
}

$report = $_SESSION['import_report'] ?? null;
$errors = is_array($report['errors'] ?? null) ? $report['errors'] : [];

$rows = [['الصف', 'اسم الصنف', 'الكود', 'الباركود', 'سبب الخطأ']];
foreach ($errors as $error) {
    if (!is_array($error)) {
        $rows[] = ['', '', '', '', (string) $error];
        continue;
    }
    $rows[] = [
        (string) ($error['row'] ?? ''),
        (string) ($error['name'] ?? ''),
        (string) ($error['code'] ?? ''),
        (string) ($error['barcode'] ?? ''),
        (string) ($error['reason'] ?? ''),
    ];
}
if (count($rows) === 1) {
    $rows[] = ['', '', '', '', 'لا توجد أصناف بها خطأ'];
}

$binary = kody_plain_xlsx($rows, 'اصناف بها خطأ');
$filename = 'اصناف_لم_ترفع.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="import_errors.xlsx"; filename*=UTF-8\'\'' . rawurlencode($filename));
header('Content-Length: ' . strlen($binary));
header('Cache-Control: no-store');
echo $binary;
exit;
