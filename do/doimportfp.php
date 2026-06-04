<?php
session_start();

require '../vendor/autoload.php';

// ── تحميل PhpSpreadsheet يدوياً (بدون Composer) ──
spl_autoload_register(function ($className) {
    // PhpOffice\PhpSpreadsheet\xxx → ../PhpSpreadsheet/xxx.php
    $prefix = 'PhpOffice\\PhpSpreadsheet\\';
    if (strpos($className, $prefix) !== 0) return;
    $relative = str_replace('\\', DIRECTORY_SEPARATOR, substr($className, strlen($prefix)));
    $file = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'PhpSpreadsheet' . DIRECTORY_SEPARATOR . $relative . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as SpreadsheetDate;

// ═══════════════════════════════════════════════════════════════════
//  التحقق من الملف
// ═══════════════════════════════════════════════════════════════════
if (!isset($_FILES['sheet']) || $_FILES['sheet']['error'] !== UPLOAD_ERR_OK) {
    die('<div style="color:red;font-family:Arial;direction:rtl;padding:20px;">
         لم يتم رفع الملف بشكل صحيح.</div>');
}

$tmpPath  = $_FILES['sheet']['tmp_name'];
$origName = $_FILES['sheet']['name'];
$ext      = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
$fileSize = $_FILES['sheet']['size'];

if (!in_array($ext, ['xls', 'xlsx'])) {
    die('<div style="color:red;font-family:Arial;direction:rtl;padding:20px;">
         الملف غير صالح — يُقبل xls و xlsx فقط.</div>');
}

if ($fileSize > 20 * 1024 * 1024) {
    die('<div style="color:red;font-family:Arial;direction:rtl;padding:20px;">
         حجم الملف يتجاوز 20 ميجا.</div>');
}

// ═══════════════════════════════════════════════════════════════════
//  تحميل الـ Spreadsheet 
//  بعض الأجهزة تصدر ملفات XML أو HTML أو CSV بمسماة xls.
//  لأن PhpSpreadsheet يعتمد جزئياً على الامتداد للتعرف، سنقوم بنسخ 
//  الملف المؤقت (.tmp) ليكون بامتداده الأصلي (.xls أو .xlsx)
// ═══════════════════════════════════════════════════════════════════
$spreadsheet = null;
$lastError   = '';

// إنشاء مسار مؤقت بالامتداد الصحيح
$tempDir      = sys_get_temp_dir();
$realExtPath  = $tempDir . DIRECTORY_SEPARATOR . uniqid('fp_') . '.' . $ext;

$filePreview = '';
if (move_uploaded_file($tmpPath, $realExtPath) || copy($tmpPath, $realExtPath)) {
    try {
        // نستخدم IOFactory::load وهو سيتعرف تلقائياً على النوع 
        $spreadsheet = IOFactory::load($realExtPath);
    } catch (\Exception $e) {
        $lastError = $e->getMessage();
        // قراءة أول 500 حرف من الملف لمعرفة صيغته الحقيقية للـ Debug
        $filePreview = @file_get_contents($realExtPath, false, null, 0, 500);
    }
    
    @unlink($realExtPath);
} else {
    $lastError = "تعذر معالجة الملف المؤقت.";
}

if ($spreadsheet === null) {
    die('<div style="color:red;font-family:Tahoma,Arial;direction:rtl;padding:20px;">
         <b>فشل قراءة الملف (صيغة غير مدعومة)</b><br>
         جهاز البصمة قام بتصدير الملف بصيغة Excel قديمة جداً (Raw BIFF) لا تدعمها الأنظمة الحديثة مباشرة.<br><br>
         <b>الحل البسيط:</b><br>
         1. افتح الملف <code>11.xls</code> باستخدام برنامج <b>Microsoft Excel</b> على جهازك.<br>
         2. من قائمة ملف (File) اختر <b>حفظ باسم (Save As)</b>.<br>
         3. اختر الصيغة الحديثة <b>Excel Workbook (*.xlsx)</b>.<br>
         4. قم برفع الملف الجديد (xlsx) هنا وسيعمل بنجاح 100%.
         </div>');
}

// لتسريع القراءة (اختياري، رغم أننا لم نحدد Reader معين هنا)
$worksheet = $spreadsheet->getActiveSheet();



// ═══════════════════════════════════════════════════════════════════
//  دوال مساعدة: كشف الأعمدة (عربي / لاتيني / مشفر)
// ═══════════════════════════════════════════════════════════════════
function normalizeHeader(string $h): string
{
    // إزالة BOM وأحرف التحكم
    $h = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $h);
    $h = str_replace(["\xEF\xBB\xBF", "\xFF\xFE", "\xFE\xFF"], '', $h);
    $h = trim($h);
    
    // تصحيح التشفير (Mojibake): أجهزة ZKTeco تصدر الملف بترميز Windows-1256 (عربي)
    // لكن الإكسيل يقرأه كـ Latin1، فتظهر حروف غريبة مثل "ÑÞã ÇáÈÕãå"
    if (preg_match('/[\xC0-\xFF]/', mb_convert_encoding($h, 'ISO-8859-1', 'UTF-8'))) {
        $bytes = @mb_convert_encoding($h, 'ISO-8859-1', 'UTF-8');
        // نستخدم iconv لأن mbstring قد لا تدعم CP1256 في بعض إصدارات PHP
        $fixed = @iconv('Windows-1256', 'UTF-8//IGNORE', $bytes);
        if ($fixed) {
            $h = $fixed;
        }
    }
    
    return mb_strtolower(trim($h), 'UTF-8');
}

function resolveColumnName(string $header): ?string
{
    $map = [
        // رقم الجهاز / AC-No
        'ac-no'          => 'device_id',
        'ac no'          => 'device_id',
        'acno'           => 'device_id',
        'رقم الجهاز'    => 'device_id',
        'رقم البصمة'    => 'device_id',
        'رقم البصمه'    => 'device_id',
        'كود الجهاز'    => 'device_id',
        'device id'      => 'device_id',
        'device_id'      => 'device_id',
        'id'             => 'device_id',

        // رقم الموظف
        'no'             => 'emp_no',
        'emp no'         => 'emp_no',
        'emp_no'         => 'emp_no',
        'employee no'    => 'emp_no',
        'رقم الموظف'    => 'emp_no',
        'الرقم'          => 'emp_no',
        'كود الموظف'    => 'emp_no',
        'serial'         => 'emp_no',

        // الاسم
        'name'           => 'name',
        'الاسم'          => 'name',
        'اسم الموظف'    => 'name',
        'employee name'  => 'name',

        // التوقيت
        'time'           => 'time',
        'الوقت'          => 'time',
        'الميعاد'        => 'time',
        'datetime'       => 'time',
        'date/time'      => 'time',
        'date time'      => 'time',
        'تاريخ الوقت'   => 'time',
        'التوقيت'        => 'time',
        'check time'     => 'time',

        // أخرى
        'state'          => 'state',
        'الحالة'         => 'state',
        'نوع الحركة'    => 'state',
        'new state'      => 'new_state',
        'exception'      => 'exception',
        'الاستثناء'      => 'exception',
        'operation'      => 'operation',
        'العملية'        => 'operation',
    ];

    return $map[normalizeHeader($header)] ?? null;
}

// ═══════════════════════════════════════════════════════════════════
//  قراءة سطر الرأس وبناء خريطة الأعمدة
// ═══════════════════════════════════════════════════════════════════
$headerRow = [];
$colMap    = [];   // internal_name => column_index (0-based)

$firstRow = $worksheet->getRowIterator(1)->current();
$ci = 0;
foreach ($firstRow->getCellIterator() as $cell) {
    $raw         = (string)($cell->getValue() ?? '');
    $headerRow[] = $raw;
    $internal    = resolveColumnName($raw);
    if ($internal && !isset($colMap[$internal])) {
        $colMap[$internal] = $ci;
    }
    $ci++;
}

// التحقق من الأعمدة الإلزامية
if (!isset($colMap['device_id']) || !isset($colMap['time'])) {
    echo '<!DOCTYPE html><html lang="ar" dir="rtl"><head><meta charset="utf-8">
          <link rel="stylesheet" href="../assets/plugins/bootstrap/css/bootstrap.min.css"></head>
          <body style="font-family:Tahoma;padding:30px;">';
    echo '<div class="alert alert-danger">';
    echo '<h5>⚠️ لم يتم التعرف على الأعمدة الأساسية في الملف</h5>';
    echo '<p>الأعمدة المكتشفة في السطر الأول:</p><ul>';
    foreach ($headerRow as $h) {
        echo '<li>' . htmlspecialchars($h ?: '(فارغ)') . '</li>';
    }
    echo '</ul>';
    echo '<p><strong>المطلوب:</strong> عمود <b>رقم الجهاز</b> (AC-No / رقم الجهاز) وعمود <b>الوقت</b> (Time / الوقت)</p>';
    echo '</div>';
    echo '<a href="../importfplog.php" class="btn btn-secondary">← رجوع</a>';
    echo '</body></html>';
    die;
}

// ═══════════════════════════════════════════════════════════════════
//  قاعدة البيانات
// ═══════════════════════════════════════════════════════════════════
include '../includes/connect.php';
$conn->set_charset('utf8mb4');
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

$userId = $_SESSION['userid'] ?? 1;

// ═══════════════════════════════════════════════════════════════════
//  حلقة المعالجة
// ═══════════════════════════════════════════════════════════════════
$imported    = 0;
$skipped     = 0;
$missingEmps = [];   // device_id => ['name','emp_no','count']
$errors      = [];

// Prepared statement للإدراج
$stmtIns = $conn->prepare(
    "INSERT INTO attandance (employee, fptybe, fpdate, time, user, fromwhere)
     VALUES (?, ?, ?, ?, ?, '1')"
);

foreach ($worksheet->getRowIterator(2) as $row) {
    $cells = [];
    foreach ($row->getCellIterator() as $cell) {
        $cells[] = $cell->getValue();
    }

    $deviceId = trim((string)($cells[$colMap['device_id']] ?? ''));
    $empNoRaw = isset($colMap['emp_no']) ? trim((string)($cells[$colMap['emp_no']] ?? '')) : '';
    $nameRaw  = isset($colMap['name'])   ? trim((string)($cells[$colMap['name']]   ?? '')) : '';
    $timeRaw  = $cells[$colMap['time']] ?? '';

    // تجاهل الصفوف الفارغة
    if ($deviceId === '' && $timeRaw === '') {
        continue;
    }

    // ── تحويل التوقيت ───────────────────────────────────────────
    $formattedDate = '';
    $formattedTime = '';

    if (is_numeric($timeRaw) && $timeRaw > 0) {
        // Excel serial date
        try {
            $dt            = SpreadsheetDate::excelToDateTimeObject((float)$timeRaw);
            $formattedDate = $dt->format('Y-m-d');
            $formattedTime = $dt->format('H:i');
        } catch (\Exception $e) {
            $errors[] = "صف " . $row->getRowIndex() . ": خطأ في تحويل التاريخ ($timeRaw)";
            $skipped++;
            continue;
        }
    } elseif ($timeRaw !== '' && $timeRaw !== null) {
        $timeStr = trim((string)$timeRaw);
        
        // تصحيح "ص" و "م" سواء كانت بالعربي أو مشفرة (Õ = ص, ã = م)
        $timeStr = str_replace(['Õ', 'ص'], ' AM', $timeStr);
        $timeStr = str_replace(['ã', 'م'], ' PM', $timeStr);
        
        // مسح المسافات الزائدة لو وجدت
        $timeStr = preg_replace('/\s+/', ' ', $timeStr);
        $timeStr = trim($timeStr);

        $parsed  = false;
        $formats = [
            'd/m/Y h:i:s A', 'd/m/Y h:i A',
            'd/m/Y H:i:s', 'd/m/Y H:i',
            'm/d/Y h:i:s A', 'm/d/Y h:i A',
            'm/d/Y H:i:s', 'm/d/Y H:i',
            'd-m-Y h:i:s A', 'd-m-Y h:i A',
            'd-m-Y H:i:s', 'd-m-Y H:i',
            'Y-m-d h:i:s A', 'Y-m-d h:i A',
            'Y-m-d H:i:s', 'Y-m-d H:i',
            'n/j/Y H:i',   'n/j/Y h:i A',
        ];
        foreach ($formats as $fmt) {
            $dtObj = DateTime::createFromFormat($fmt, $timeStr);
            if ($dtObj) {
                $formattedDate = $dtObj->format('Y-m-d');
                $formattedTime = $dtObj->format('H:i');
                $parsed = true;
                break;
            }
        }
        if (!$parsed) {
            $ts = strtotime($timeStr);
            if ($ts !== false) {
                $formattedDate = date('Y-m-d', $ts);
                $formattedTime = date('H:i', $ts);
            } else {
                $errors[] = "صف " . $row->getRowIndex() . ": لم يُتعرف على التوقيت «{$timeStr}»";
                $skipped++;
                continue;
            }
        }
    } else {
        $errors[] = "صف " . $row->getRowIndex() . ": التوقيت فارغ";
        $skipped++;
        continue;
    }

    // ── البحث عن الموظف بـ basma_id ─────────────────────────────
    $stmtEmp = $conn->prepare(
        "SELECT id, name, shift FROM employees WHERE basma_id = ? AND isdeleted != 1 LIMIT 1"
    );
    $stmtEmp->bind_param('s', $deviceId);
    $stmtEmp->execute();
    $rowemp = $stmtEmp->get_result()->fetch_assoc();
    $stmtEmp->close();

    if ($rowemp === null) {
        if (!isset($missingEmps[$deviceId])) {
            $missingEmps[$deviceId] = [
                'device_id' => $deviceId,
                'emp_no'    => $empNoRaw,
                'name'      => $nameRaw,
                'count'     => 0,
            ];
        }
        $missingEmps[$deviceId]['count']++;
        $skipped++;
        continue;
    }

    $empDbId = $rowemp['id'];
    $shiftId = $rowemp['shift'];
    $fptype  = '5'; // غير محدد افتراضياً

    // ── تحديد نوع البصمة ─────────────────────────────────────────
    if ($shiftId) {
        $stmtShft = $conn->prepare("SELECT * FROM shifts WHERE id = ? LIMIT 1");
        $stmtShft->bind_param('i', $shiftId);
        $stmtShft->execute();
        $rowshft = $stmtShft->get_result()->fetch_assoc();
        $stmtShft->close();

        if ($rowshft) {
            if ($formattedTime >= $rowshft['instart'] && $formattedTime <= $rowshft['inend']) {
                $fptype = '1'; // حضور
            } elseif ($formattedTime >= $rowshft['outstart'] && $formattedTime <= $rowshft['outend']) {
                $fptype = '2'; // انصراف
            }
        }
    }

    // ── إدراج السجل ──────────────────────────────────────────────
    $stmtIns->bind_param('isssi', $empDbId, $fptype, $formattedDate, $formattedTime, $userId);
    if ($stmtIns->execute()) {
        $imported++;
    } else {
        $errors[] = "صف " . $row->getRowIndex() . ": DB Error — " . $stmtIns->error;
        $skipped++;
    }
}

$stmtIns->close();
$conn->close();

// ═══════════════════════════════════════════════════════════════════
//  صفحة النتائج
// ═══════════════════════════════════════════════════════════════════
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>نتيجة الاستيراد</title>
    <link rel="stylesheet" href="../assets/plugins/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/plugins/fontawesome-free/css/all.min.css">
    <style>
        body { font-family: 'Tahoma', Arial, sans-serif; background: #f4f6f9; padding: 20px; }
        .result-wrap { max-width: 860px; margin: 0 auto; }
        .stat-box { border-radius: 12px; padding: 18px; color: #fff; text-align: center; }
        .stat-box h2 { font-size: 2.4rem; font-weight: 700; margin: 0; }
        .stat-box p  { margin: 4px 0 0; font-size: 0.85rem; opacity: 0.9; }
        .bg-ok   { background: linear-gradient(135deg,#28a745,#20c997); }
        .bg-skip { background: linear-gradient(135deg,#fd7e14,#ffc107); color:#333; }
        .bg-miss { background: linear-gradient(135deg,#dc3545,#c82333); }
        .missing-card {
            background: #fff;
            border: 1px solid #f5c6cb;
            border-right: 4px solid #dc3545;
            border-radius: 8px;
            padding: 12px 16px;
            margin-bottom: 10px;
        }
        .missing-card strong { font-size: 1rem; }
        .error-list li { font-size: 0.82rem; }
    </style>
</head>
<body>
<div class="result-wrap">

    <div class="card shadow">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0"><i class="fas fa-file-excel me-2"></i>نتيجة استيراد ملف البصمة</h5>
        </div>
        <div class="card-body">

            <!-- إحصائيات -->
            <div class="row g-3 mb-4">
                <div class="col-4">
                    <div class="stat-box bg-ok">
                        <h2><?= $imported ?></h2>
                        <p>سجل مُستورد</p>
                    </div>
                </div>
                <div class="col-4">
                    <div class="stat-box bg-skip">
                        <h2><?= $skipped ?></h2>
                        <p>سجل مُتخطّى</p>
                    </div>
                </div>
                <div class="col-4">
                    <div class="stat-box bg-miss">
                        <h2><?= count($missingEmps) ?></h2>
                        <p>موظف غير موجود</p>
                    </div>
                </div>
            </div>

            <!-- أخطاء فنية -->
            <?php if (!empty($errors)): ?>
            <div class="alert alert-warning py-2">
                <strong>تحذيرات:</strong>
                <ul class="error-list mb-0 mt-1">
                    <?php foreach (array_slice($errors, 0, 15) as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                    <?php if (count($errors) > 15): ?>
                        <li class="text-muted">... و <?= count($errors)-15 ?> تحذير آخر</li>
                    <?php endif; ?>
                </ul>
            </div>
            <?php endif; ?>

            <!-- الموظفون الناقصون -->
            <?php if (!empty($missingEmps)): ?>
            <div class="mt-3">
                <h6 class="text-danger fw-bold mb-2">
                    <i class="fas fa-user-times me-1"></i>
                    الموظفون غير الموجودين في قاعدة البيانات
                    <span class="badge bg-danger ms-1"><?= count($missingEmps) ?></span>
                </h6>
                <p class="text-muted small">
                    السجلات المرتبطة بهؤلاء لم تُستورد. أضفهم ثم أعد رفع الملف.
                </p>

                <?php foreach ($missingEmps as $me): ?>
                <div class="missing-card d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <strong><?= htmlspecialchars($me['name'] ?: '(بدون اسم في الملف)') ?></strong>
                        <div class="text-muted small mt-1">
                            رقم الجهاز: <code><?= htmlspecialchars($me['device_id']) ?></code>
                            <?php if ($me['emp_no']): ?>
                                &nbsp;|&nbsp; رقم في الملف: <code><?= htmlspecialchars($me['emp_no']) ?></code>
                            <?php endif; ?>
                            &nbsp;|&nbsp; سجلات: <strong class="text-danger"><?= $me['count'] ?></strong>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="../add_employee.php?basmaid=<?= urlencode($me['device_id']) ?>&name=<?= urlencode($me['name']) ?>"
                           class="btn btn-success btn-sm" target="_blank">
                            <i class="fas fa-user-plus me-1"></i>إضافة
                        </a>
                        <a href="../employees.php?q=<?= urlencode($me['device_id']) ?>"
                           class="btn btn-outline-secondary btn-sm" target="_blank">
                            <i class="fas fa-search me-1"></i>بحث
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- نجاح تام -->
            <?php if (empty($missingEmps) && $imported > 0 && empty($errors)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle me-1"></i>
                تم استيراد جميع السجلات بنجاح!
            </div>
            <?php elseif ($imported === 0 && empty($missingEmps)): ?>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-1"></i>
                لم يتم استيراد أي سجل. تأكد من محتوى الملف.
            </div>
            <?php endif; ?>

            <!-- أزرار -->
            <div class="mt-4 d-flex gap-2 flex-wrap">
                <a href="../importfplog.php" class="btn btn-primary">
                    <i class="fas fa-upload me-1"></i>استيراد ملف آخر
                </a>
                <a href="../manualattandance.php" class="btn btn-outline-secondary">
                    <i class="fas fa-list me-1"></i>سجل الحضور
                </a>
                <?php if (!empty($missingEmps)): ?>
                <a href="../employees.php" class="btn btn-outline-danger">
                    <i class="fas fa-users me-1"></i>إدارة الموظفين
                </a>
                <?php endif; ?>
            </div>

        </div>
    </div>

</div>
</body>
</html>
