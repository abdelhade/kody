<?php
/**
 * EntitlesSeeder - أنواع الاستحقاق (employees.ent_tybe) المصدر الوحيد لها.
 * بيتشغّل تلقائيًا بعد كل تحديث لقاعدة البيانات من MigrationRunner، وآمن للتكرار.
 */

class EntitlesSeeder
{
    public const HOURS_ONLY = 1;
    public const HOURS_DAILY_EXTRA = 2;
    public const HOURS_PERIOD_EXTRA = 3;
    public const ATTENDANCE_DAYS = 4;
    public const PRODUCTION = 5;

    /**
     * @return array<int, array{id:int, tybe:string, info:string}>
     */
    public static function getPresets(): array
    {
        return [
            self::HOURS_ONLY => [
                'id' => self::HOURS_ONLY,
                'tybe' => 'بالساعات فقط',
                'info' => 'احتساب الراتب بناءً على الساعات الفعلية فقط',
            ],
            self::HOURS_DAILY_EXTRA => [
                'id' => self::HOURS_DAILY_EXTRA,
                'tybe' => 'ساعات عمل وإضافي يومي',
                'info' => 'احتساب ساعات العمل بالإضافة إلى الإضافي اليومي',
            ],
            self::HOURS_PERIOD_EXTRA => [
                'id' => self::HOURS_PERIOD_EXTRA,
                'tybe' => 'ساعات عمل وإضافي خلال الفترة',
                'info' => 'احتساب ساعات العمل بالإضافة إلى الإضافي التراكمي خلال الفترة',
            ],
            self::ATTENDANCE_DAYS => [
                'id' => self::ATTENDANCE_DAYS,
                'tybe' => 'بناءً على الحضور',
                'info' => 'احتساب الراتب بناءً على عدد أيام الحضور',
            ],
            self::PRODUCTION => [
                'id' => self::PRODUCTION,
                'tybe' => 'بالإنتاجية',
                'info' => 'احتساب الراتب بناءً على إجمالي الإنتاجية',
            ],
        ];
    }

    public static function isValid(int $id): bool
    {
        return isset(self::getPresets()[$id]);
    }

    public static function normalize(int $id): int
    {
        return self::isValid($id) ? $id : self::HOURS_ONLY;
    }

    /**
     * @return array{ok:bool, seeded:int, error:string}
     */
    public static function seed(mysqli $conn): array
    {
        try {
            $stmt = $conn->prepare(
                'INSERT INTO `entitles` (`id`, `tybe`, `info`) VALUES (?, ?, ?)
                 ON DUPLICATE KEY UPDATE `tybe` = VALUES(`tybe`), `info` = VALUES(`info`)'
            );
            if (!$stmt) {
                return ['ok' => false, 'seeded' => 0, 'error' => $conn->error];
            }

            $seeded = 0;
            foreach (self::getPresets() as $row) {
                $id = $row['id'];
                $tybe = $row['tybe'];
                $info = $row['info'];
                $stmt->bind_param('iss', $id, $tybe, $info);
                if ($stmt->execute()) {
                    $seeded++;
                }
            }
            $stmt->close();
        } catch (mysqli_sql_exception $e) {
            return ['ok' => false, 'seeded' => 0, 'error' => $e->getMessage()];
        }

        return ['ok' => true, 'seeded' => $seeded, 'error' => ''];
    }

    /**
     * القائمة من الجدول، ولو فاضي يرجع الافتراضي.
     *
     * @return list<array{id:int, tybe:string, info:string}>
     */
    public static function getList(mysqli $conn): array
    {
        $list = [];
        try {
            $res = $conn->query('SELECT id, tybe, info FROM entitles ORDER BY id');
            while ($res && ($row = $res->fetch_assoc())) {
                $list[] = $row;
            }
        } catch (mysqli_sql_exception $e) {
            $list = [];
        }
        return $list ?: array_values(self::getPresets());
    }
}
