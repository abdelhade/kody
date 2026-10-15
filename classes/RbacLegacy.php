<?php
/**
 * الربط بين usr_pwrs القديم والصلاحيات الجديدة (مؤقت لحد حذف usr_pwrs).
 *
 * - permsFromRow(): صلاحيات دور لسه ما اتنقلش (قاعدة بيانات ما شغّلتش update/030).
 *
 * القواعد هي نفس اللي في tools/gen_rbac_sql.php، ومصدرها includes/rbac_catalog.php.
 */
final class RbacLegacy
{
    public const ACTIONS = ['view', 'add', 'edit', 'delete'];
    public const LEGACY_PREFIX = ['view' => 'show', 'add' => 'add', 'edit' => 'edit', 'delete' => 'delete'];

    private static ?array $catalog = null;

    /** @return array<string, array{label:string, sid:list<string>, items:list<array>}> */
    public static function catalog(): array
    {
        return self::$catalog ??= require dirname(__DIR__) . '/includes/rbac_catalog.php';
    }

    /**
     * كل صلاحية في الكتالوج مع مصدرها القديم.
     *
     * @return list<array{name:string, group:string, action:?string, legacy:string}>
     */
    public static function flatten(): array
    {
        $out = [];
        foreach (self::catalog() as $groupKey => $group) {
            foreach ($group['items'] as $item) {
                if (isset($item['crud'])) {
                    foreach (self::ACTIONS as $action) {
                        $out[] = ['name' => $item['crud'] . '.' . $action, 'group' => $groupKey, 'action' => $action, 'legacy' => $item['legacy']];
                    }
                } else {
                    $out[] = ['name' => $item['single'], 'group' => $groupKey, 'action' => null, 'legacy' => $item['legacy']];
                }
            }
        }
        return $out;
    }

    /**
     * صلاحيات دور من صف usr_pwrs (نفس نتيجة update/030).
     *
     * @param array<string, mixed> $row
     * @return array<string, string> name => group_key
     */
    public static function permsFromRow(array $row, int $roleId): array
    {
        $on = static fn (string $col): bool => (int) ($row[$col] ?? 0) === 1;
        $perms = [];

        foreach (self::flatten() as $p) {
            [$kind, $arg] = array_pad(explode(':', $p['legacy'], 2), 2, '');
            $cols = $arg === '' ? [] : explode('|', $arg);

            $granted = match ($kind) {
                'cols'  => $p['action'] !== null
                    && array_filter($cols, static fn ($c) => $on(self::LEGACY_PREFIX[$p['action']] . '_' . $c)) !== [],
                'col', 'any' => array_filter($cols, $on) !== [],
                'not'   => !$on($cols[0]),
                'all'   => true,
                'role1' => $roleId === 1,
                default => false,
            };
            if ($granted) {
                $perms[$p['name']] = $p['group'];
            }
        }

        // add / edit / delete من غير view ملهاش معنى
        foreach (array_keys($perms) as $name) {
            if (preg_match('/^(.+)\.(add|edit|delete)$/', $name, $m) && !isset($perms[$m[1] . '.view'])) {
                $perms[$m[1] . '.view'] = $perms[$name];
            }
        }
        return $perms;
    }
}
