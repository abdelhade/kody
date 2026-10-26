<?php

/**
 * DynamicShiftDetector — محرك التعرف التلقائي على الورديات (Auto-Detect / Dynamic Shifts)
 * 
 * يستنتج الوردية المستهدفة للموظف بناءً على وقت البصمة ومجموعة الورديات المرتبط بها الموظف.
 * 
 * الخوارزمية الرياضية:
 * Shift = argmin |PunchInTime - ShiftStartTime|
 * مع مراعاة:
 * 1. الدوران الدائري للوقت على مدار 24 ساعة (Circular 1440 minutes).
 * 2. فك التعادل: عند تساوي الفارق بين ورديتين، يرجح التأخير في الحضور للوردية السابقة.
 * 3. الأولوية لنوافذ الدخول الرسمية (instart - inend).
 */
class DynamicShiftDetector
{
    /**
     * تحويل الوقت (H:i أو H:i:s) إلى دقائق من بداية اليوم (0 - 1439).
     */
    public static function timeToMinutes(string $timeStr): int
    {
        $timeStr = trim($timeStr);
        if ($timeStr === '') {
            return 0;
        }
        $parts = explode(':', $timeStr);
        $hours = isset($parts[0]) ? (int) $parts[0] : 0;
        $minutes = isset($parts[1]) ? (int) $parts[1] : 0;
        return (($hours % 24) * 60) + ($minutes % 60);
    }

    /**
     * حساب الفارق الزمني المطلق بالدقائق الدائرية على مدار 24 ساعة (1440 دقيقة).
     */
    public static function circularDiffMinutes(string $time1, string $time2): int
    {
        $m1 = self::timeToMinutes($time1);
        $m2 = self::timeToMinutes($time2);
        $diff = abs($m1 - $m2);
        return min($diff, 1440 - $diff);
    }

    /**
     * حساب الفارق الزمني والاتجاه (هل البصمة بعد موعد الوردية / تأخير، أم قبله / تبكير).
     * 
     * @return array{diff: int, is_late: bool, signed_diff: int}
     */
    public static function calculateDiffAndDirection(string $punchTime, string $targetTime): array
    {
        $p = self::timeToMinutes($punchTime);
        $t = self::timeToMinutes($targetTime);

        // الفارق الخطي
        $diff = $p - $t;

        // ضبط الدوران حول منتصف الليل (إذا كان الفارق يتجاوز 12 ساعة = 720 دقيقة)
        if ($diff > 720) {
            $diff -= 1440;
        } elseif ($diff < -720) {
            $diff += 1440;
        }

        return [
            'diff'        => abs($diff),
            'is_late'     => ($diff >= 0), // البصمة تمت عند أو بعد بداية الوردية (تأخير)
            'signed_diff' => $diff,
        ];
    }

    /**
     * فحص ما إذا كان وقت البصمة يقع داخل نافذة زمنية معينة (مع مراعاة عبور منتصف الليل).
     */
    public static function isTimeInWindow(string $time, ?string $start, ?string $end): bool
    {
        if ($start === null || $start === '' || $end === null || $end === '') {
            return false;
        }

        $t = self::timeToMinutes($time);
        $s = self::timeToMinutes($start);
        $e = self::timeToMinutes($end);

        if ($s <= $e) {
            return ($t >= $s && $t <= $e);
        }

        // عبور منتصف الليل (مثال: من 23:00 إلى 02:00)
        return ($t >= $s || $t <= $e);
    }

    /**
     * تصنيف نوع البصمة: حضور (1) أم انصراف (2) باستخدام منطق التسلسل (Sequence Logic).
     *
     * المنطق:
     * 1. فحص النوافذ الرسمية (instart-inend / outstart-outend) أولاً.
     * 2. إذا كانت البصمة تقع في كلا النافذتين أو خارجهما → نستخدم التسلسل:
     *    - لا توجد بصمات سابقة في دورة يوم العمل = حضور (أول بصمة).
     *    - توجد بصمات سابقة = انصراف (بصمة لاحقة).
     *
     * هذا يحل edge case: الموظف ليلي يبصم انصراف 08:00 بينما شيفت آخر يبدأ 08:00.
     * النوافذ مش هتحسمها، لكن التسلسل يحسمها: لو قبلها بصمة حضور = هذه انصراف.
     *
     * @param mysqli $conn
     * @param int $empId رقم الموظف
     * @param string $punchDate تاريخ البصمة Y-m-d
     * @param string $punchTime وقت البصمة H:i أو H:i:s
     * @param array<string, mixed> $detectedShift الوردية المستنتجة
     * @return string '1' حضور، '2' انصراف، '5' غير محدد
     */
    public static function classifyPunchType(
        mysqli $conn,
        int $empId,
        string $punchDate,
        string $punchTime,
        array $detectedShift
    ): string {
        // المرحلة 1: فحص النوافذ الرسمية
        $inWindow = !empty($detectedShift['instart']) && !empty($detectedShift['inend'])
            ? self::isTimeInWindow($punchTime, $detectedShift['instart'], $detectedShift['inend'])
            : false;

        $outWindow = !empty($detectedShift['outstart']) && !empty($detectedShift['outend'])
            ? self::isTimeInWindow($punchTime, $detectedShift['outstart'], $detectedShift['outend'])
            : false;

        // حضور وانصراف في نفس الوقت = ثغرة في الإعداد، نلجأ للتسلسل
        // حضور فقط = حضور مؤكد
        if ($inWindow && !$outWindow) {
            return '1';
        }

        // انصراف فقط = انصراف مؤكد
        if ($outWindow && !$inWindow) {
            return '2';
        }

        // المرحلة 2: منطق التسلسل — هل يوجد بصمة سابقة في دورة يوم العمل؟
        // نحسب من day_cutoff: البصمات من (تاريخ أمس بعد cutoff) أو (اليوم قبل البصمة الحالية)
        $dayCutoff = (string) ($detectedShift['day_cutoff'] ?? '06:00:00');

        // تحديد بداية دورة اليوم: لو البصمة قبل cutoff، الدورة بدأت من أمس
        $punchMinutes = self::timeToMinutes($punchTime);
        $cutoffMinutes = self::timeToMinutes($dayCutoff);

        if ($punchMinutes < $cutoffMinutes) {
            // البصمة بعد منتصف الليل وقبل cutoff → الدورة من أمس بعد cutoff
            $cycleStartDate = date('Y-m-d', strtotime($punchDate . ' -1 day'));
            $cycleStartTime = $dayCutoff;
            $cycleEndDate   = $punchDate;
            $cycleEndTime   = $punchTime;
        } else {
            // البصمة في نفس اليوم بعد cutoff → الدورة من اليوم بعد cutoff
            $cycleStartDate = $punchDate;
            $cycleStartTime = $dayCutoff;
            $cycleEndDate   = $punchDate;
            $cycleEndTime   = $punchTime;
        }

        // البحث عن أي بصمة سابقة لهذا الموظف في نفس الدورة وقبل البصمة الحالية
        $eDate1  = $conn->real_escape_string($cycleStartDate);
        $eDate2  = $conn->real_escape_string($cycleEndDate);
        $eTime1  = $conn->real_escape_string($cycleStartTime);
        $eTime2  = $conn->real_escape_string($cycleEndTime);
        $empIdInt = (int) $empId;

        $prevFp = $conn->query("
            SELECT COUNT(*) AS cnt FROM attandance
            WHERE employee = $empIdInt
              AND isdeleted = 0
              AND (
                (fpdate = '$eDate1' AND fpdate = '$eDate2' AND time >= '$eTime1' AND time < '$eTime2')
                OR (fpdate = '$eDate1' AND '$eDate1' < '$eDate2' AND time >= '$eTime1')
                OR (fpdate = '$eDate2' AND '$eDate1' < '$eDate2' AND time < '$eTime2')
              )
        ")->fetch_assoc();

        if (!empty($prevFp['cnt']) && (int)$prevFp['cnt'] > 0) {
            return '2'; // توجد بصمات سابقة → هذه بصمة انصراف
        }

        // أول بصمة في الدورة → حضور
        if ($inWindow || $outWindow) {
            return $outWindow ? '2' : '1'; // إذا كانت في نافذة واحدة فقط
        }

        // الأقرب لبداية أو نهاية الوردية كـ fallback أخير
        $diffStart = self::circularDiffMinutes($punchTime, $detectedShift['shiftstart'] ?? '00:00:00');
        $diffEnd   = self::circularDiffMinutes($punchTime, $detectedShift['shiftend'] ?? '00:00:00');
        return ($diffStart <= $diffEnd) ? '1' : '2';
    }

    /**
     * استنتاج الوردية المستهدفة لبصمة الدخول (Punch-In).
     * 
     * @param string $punchTime وقت البصمة (مثلاً '08:15:00')
     * @param array<array<string, mixed>> $shifts قائمة الورديات المتاحة في المجموعة
     * @param int $maxAllowedDiff الحد الأقصى للفارق بالدقائق (افتراضياً 720 دقيقة = 12 ساعة)
     * @return array<string, mixed>|null الوردية المختارة أو null إذا لم تتوفر ورديات
     */
    public static function detectShift(string $punchTime, array $shifts, int $maxAllowedDiff = 720): ?array
    {
        if (empty($shifts)) {
            return null;
        }

        // أولاً: فحص التطابق المباشر داخل نافذة الدخول الرسمية (instart إلى inend)
        $windowMatches = [];
        foreach ($shifts as $shift) {
            if (!empty($shift['instart']) && !empty($shift['inend'])) {
                if (self::isTimeInWindow($punchTime, $shift['instart'], $shift['inend'])) {
                    $windowMatches[] = $shift;
                }
            }
        }

        // إذا كان هناك تطابق وحيد داخل النافذة الرسمية، نختاره فوراً
        if (count($windowMatches) === 1) {
            return $windowMatches[0];
        }

        // إذا كان هناك أكثر من وردية تطابقت في النافذة، نحدد الأقرب بينها
        $candidates = !empty($windowMatches) ? $windowMatches : $shifts;

        $bestShift = null;
        $minDiff = PHP_INT_MAX;
        $bestIsLate = false;

        foreach ($candidates as $shift) {
            if (empty($shift['shiftstart'])) {
                continue;
            }

            $info = self::calculateDiffAndDirection($punchTime, $shift['shiftstart']);
            $diff = $info['diff'];
            $isLate = $info['is_late'];

            if ($diff < $minDiff) {
                $minDiff = $diff;
                $bestShift = $shift;
                $bestIsLate = $isLate;
            } elseif ($diff === $minDiff) {
                // قاعدة فك التعادل: يرجح التأخير في الحضور (عن وردية سابقة بدأت) على القدوم المبكر لوردية قادمة
                if ($isLate && !$bestIsLate) {
                    $bestShift = $shift;
                    $bestIsLate = true;
                }
            }
        }

        if ($bestShift !== null && $minDiff <= $maxAllowedDiff) {
            return $bestShift;
        }

        return $bestShift ?: $shifts[0];
    }

    /**
     * استنتاج الوردية المستهدفة لبصمة الخروج (في حال كانت البصمة الوحيدة المسجلة لليوم).
     * 
     * @param string $punchTime وقت بصمة الخروج
     * @param array<array<string, mixed>> $shifts
     * @param int $maxAllowedDiff
     * @return array<string, mixed>|null
     */
    public static function detectShiftForPunchOut(string $punchTime, array $shifts, int $maxAllowedDiff = 720): ?array
    {
        if (empty($shifts)) {
            return null;
        }

        // فحص نافذة الانصراف الرسمية
        $windowMatches = [];
        foreach ($shifts as $shift) {
            if (!empty($shift['outstart']) && !empty($shift['outend'])) {
                if (self::isTimeInWindow($punchTime, $shift['outstart'], $shift['outend'])) {
                    $windowMatches[] = $shift;
                }
            }
        }

        if (count($windowMatches) === 1) {
            return $windowMatches[0];
        }

        $candidates = !empty($windowMatches) ? $windowMatches : $shifts;

        $bestShift = null;
        $minDiff = PHP_INT_MAX;

        foreach ($candidates as $shift) {
            if (empty($shift['shiftend'])) {
                continue;
            }

            $diff = self::circularDiffMinutes($punchTime, $shift['shiftend']);
            if ($diff < $minDiff) {
                $minDiff = $diff;
                $bestShift = $shift;
            }
        }

        return $bestShift ?: $shifts[0];
    }

    /**
     * جلب جميع الورديات التابعة لمجموعة ورديات معينة.
     * 
     * @return list<array<string, mixed>>
     */
    public static function getGroupShifts(mysqli $conn, int $groupId): array
    {
        if ($groupId <= 0) {
            return [];
        }

        $stmt = $conn->prepare("
            SELECT s.* 
            FROM shift_group_items sgi
            INNER JOIN shifts s ON s.id = sgi.shift_id
            WHERE sgi.group_id = ? AND (s.isdeleted = 0 OR s.isdeleted IS NULL)
            ORDER BY sgi.sort ASC, s.id ASC
        ");
        if (!$stmt) {
            return [];
        }

        $stmt->bind_param('i', $groupId);
        $stmt->execute();
        $result = $stmt->get_result();
        $shifts = [];
        while ($row = $result->fetch_assoc()) {
            $shifts[] = $row;
        }
        $stmt->close();

        return $shifts;
    }
}
