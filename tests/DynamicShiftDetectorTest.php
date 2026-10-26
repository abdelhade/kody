<?php

require_once __DIR__ . '/../classes/DynamicShiftDetector.php';

function assertEqual($expected, $actual, string $message): void
{
    if ($expected !== $actual) {
        echo "❌ FAILED: $message (Expected: " . var_export($expected, true) . ", Got: " . var_export($actual, true) . ")\n";
        exit(1);
    }
    echo "✅ PASSED: $message\n";
}

echo "=== Running DynamicShiftDetector Tests ===\n";

// 1. اختبار حساب الدقائق الدائرية
assertEqual(15, DynamicShiftDetector::circularDiffMinutes('23:45', '00:00'), '23:45 to 00:00 is 15 minutes');
assertEqual(15, DynamicShiftDetector::circularDiffMinutes('00:00', '23:45'), '00:00 to 23:45 is 15 minutes');
assertEqual(10, DynamicShiftDetector::circularDiffMinutes('08:00', '08:10'), '08:00 to 08:10 is 10 minutes');
assertEqual(60, DynamicShiftDetector::circularDiffMinutes('01:00', '24:00'), '01:00 to 24:00 (00:00) is 60 minutes');

// 2. اختبار تحديد الاتجاه والتأخير
$diffInfo1 = DynamicShiftDetector::calculateDiffAndDirection('08:15', '08:00');
assertEqual(15, $diffInfo1['diff'], '8:15 vs 8:00 is 15 mins diff');
assertEqual(true, $diffInfo1['is_late'], '8:15 is late for 8:00');

$diffInfo2 = DynamicShiftDetector::calculateDiffAndDirection('07:50', '08:00');
assertEqual(10, $diffInfo2['diff'], '7:50 vs 8:00 is 10 mins diff');
assertEqual(false, $diffInfo2['is_late'], '7:50 is early for 8:00');

// عبر منتصف الليل
$diffInfoMidnightLate = DynamicShiftDetector::calculateDiffAndDirection('00:10', '00:00');
assertEqual(10, $diffInfoMidnightLate['diff'], '00:10 vs 00:00 is 10 mins diff');
assertEqual(true, $diffInfoMidnightLate['is_late'], '00:10 is late for 00:00');

$diffInfoMidnightEarly = DynamicShiftDetector::calculateDiffAndDirection('23:50', '00:00');
assertEqual(10, $diffInfoMidnightEarly['diff'], '23:50 vs 00:00 is 10 mins diff');
assertEqual(false, $diffInfoMidnightEarly['is_late'], '23:50 is early for 00:00');

// 3. اختبار اختيار الوردية الأقرب
$shifts = [
    ['id' => 1, 'name' => 'صباحي', 'shiftstart' => '08:00:00', 'shiftend' => '16:00:00', 'instart' => '07:30:00', 'inend' => '08:30:00'],
    ['id' => 2, 'name' => 'مسائي', 'shiftstart' => '16:00:00', 'shiftend' => '00:00:00', 'instart' => '15:30:00', 'inend' => '16:30:00'],
    ['id' => 3, 'name' => 'ليلي',  'shiftstart' => '00:00:00', 'shiftend' => '08:00:00', 'instart' => '23:30:00', 'inend' => '00:30:00'],
];

// بصمة الساعة 08:10 -> الوردية الصباحية
$detected = DynamicShiftDetector::detectShift('08:10:00', $shifts);
assertEqual(1, $detected['id'], 'Punch at 08:10 detects morning shift');

// بصمة الساعة 15:45 -> الوردية المسائية
$detected = DynamicShiftDetector::detectShift('15:45:00', $shifts);
assertEqual(2, $detected['id'], 'Punch at 15:45 detects evening shift');

// بصمة الساعة 23:45 -> الوردية الليلية (قبل منتصف الليل بربع ساعة)
$detected = DynamicShiftDetector::detectShift('23:45:00', $shifts);
assertEqual(3, $detected['id'], 'Punch at 23:45 detects night shift (00:00 start)');

// 4. اختبار فك التعادل الحاسم: يرجح التأخير في الحضور!
// وردية 1 تبدأ 08:00
// وردية 2 تبدأ 16:00
// البصمة الساعة 12:00 بالضبط (متساوية المسافة: 4 ساعات بعد الوردية 1 و 4 ساعات قبل الوردية 2)
$detectedTie = DynamicShiftDetector::detectShift('12:00:00', [$shifts[0], $shifts[1]]);
assertEqual(1, $detectedTie['id'], 'Tie-break at 12:00 favors late attendance for morning shift (08:00)');

// تعادل عبر منتصف الليل:
// وردية أ تبدأ 20:00
// وردية ب تبدأ 04:00
// البصمة الساعة 00:00 (4 ساعات تأخير عن أ، و 4 ساعات تبكير عن ب)
$shiftsCross = [
    ['id' => 10, 'name' => 'شفت أ', 'shiftstart' => '20:00:00', 'shiftend' => '04:00:00', 'instart' => '', 'inend' => ''],
    ['id' => 20, 'name' => 'شفت ب', 'shiftstart' => '04:00:00', 'shiftend' => '12:00:00', 'instart' => '', 'inend' => ''],
];
$detectedTieMidnight = DynamicShiftDetector::detectShift('00:00:00', $shiftsCross);
assertEqual(10, $detectedTieMidnight['id'], 'Tie-break at midnight favors late attendance for 20:00 shift');

echo "\n🎉 ALL TESTS PASSED SUCCESSFULLY!\n";
