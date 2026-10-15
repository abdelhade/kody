<?php
/**
 * حفظ الأدوار وصلاحياتها (roles / role_has_permissions).
 * بيستخدمه do/dosave_role.php و RolesSeeder.
 */
final class Roles
{
    /** بتفضل دايماً في الدور 1 وفي دور اللي بيعدّل، عشان محدش يقفل إدارة الأدوار على نفسه */
    public const LOCK_PERMISSIONS = ['roles.view', 'roles.edit'];

    /** @return list<string> أسماء صلاحيات الدور */
    public static function permissionNames(mysqli $conn, int $roleId): array
    {
        $stmt = $conn->prepare('SELECT p.`name` FROM `role_has_permissions` rhp JOIN `permissions` p ON p.`id` = rhp.`permission_id` WHERE rhp.`role_id` = ?');
        $stmt->bind_param('i', $roleId);
        $stmt->execute();
        $names = array_column($stmt->get_result()->fetch_all(), 0);
        $stmt->close();
        return $names;
    }

    /**
     * كل الصلاحيات مترتبة زي شاشة الدور.
     *
     * @return list<array{id:int, name:string, label:string, group_key:string, group_name:string, entity_label:string, action:?string}>
     */
    public static function allPermissions(mysqli $conn): array
    {
        $res = $conn->query(
            "SELECT `id`, `name`, `label`, `group_key`, `group_name`, `entity_label`, `action`
             FROM `permissions` WHERE `guard_name` = 'web' ORDER BY `sort`, `id`"
        );
        $out = [];
        while ($row = $res->fetch_assoc()) {
            $row['id'] = (int) $row['id'];
            $out[] = $row;
        }
        return $out;
    }

    /**
     * أسماء الصلاحيات ← ids. اسم مش موجود = استثناء (غلطة في الكود مش في البيانات).
     *
     * @param list<string> $names
     * @return list<int>
     */
    public static function idsFor(mysqli $conn, array $names): array
    {
        $map = [];
        foreach (self::allPermissions($conn) as $p) {
            $map[$p['name']] = $p['id'];
        }
        $ids = [];
        foreach (array_unique($names) as $name) {
            if (!isset($map[$name])) {
                throw new InvalidArgumentException('صلاحية غير معروفة: ' . $name);
            }
            $ids[] = $map[$name];
        }
        return $ids;
    }

    /**
     * بيستبدل صلاحيات الدور بالـ ids دي (اللي مش موجودة في permissions بتتجاهل).
     * أي add / edit / delete على كيان بتجيب معاها view بتاعته.
     *
     * @param list<int> $permissionIds
     */
    public static function syncPermissions(mysqli $conn, int $roleId, array $permissionIds): int
    {
        $byId = [];
        $byName = [];
        foreach (self::allPermissions($conn) as $p) {
            $byId[$p['id']] = $p;
            $byName[$p['name']] = $p['id'];
        }

        $selected = [];
        foreach ($permissionIds as $id) {
            $id = (int) $id;
            if (!isset($byId[$id])) {
                continue;
            }
            $selected[$id] = true;
            $p = $byId[$id];
            if (in_array($p['action'], ['add', 'edit', 'delete'], true)) {
                $view = substr($p['name'], 0, strrpos($p['name'], '.')) . '.view';
                if (isset($byName[$view])) {
                    $selected[$byName[$view]] = true;
                }
            }
        }

        $del = $conn->prepare('DELETE FROM `role_has_permissions` WHERE `role_id` = ?');
        $del->bind_param('i', $roleId);
        $del->execute();
        $del->close();

        if ($selected) {
            $ins = $conn->prepare('INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (?, ?)');
            foreach (array_keys($selected) as $pid) {
                $ins->bind_param('ii', $pid, $roleId);
                $ins->execute();
            }
            $ins->close();
        }
        return count($selected);
    }

    /** @return array{id:int, name:string, description:?string, is_active:int}|null */
    public static function find(mysqli $conn, int $roleId): ?array
    {
        $stmt = $conn->prepare('SELECT `id`, `name`, `description`, `is_active` FROM `roles` WHERE `id` = ? LIMIT 1');
        $stmt->bind_param('i', $roleId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row ?: null;
    }

    public static function nameTaken(mysqli $conn, string $name, int $exceptId = 0): bool
    {
        $stmt = $conn->prepare("SELECT 1 FROM `roles` WHERE `name` = ? AND `guard_name` = 'web' AND `id` <> ? LIMIT 1");
        $stmt->bind_param('si', $name, $exceptId);
        $stmt->execute();
        $taken = (bool) $stmt->get_result()->fetch_row();
        $stmt->close();
        return $taken;
    }

    public static function create(mysqli $conn, string $name, string $description, bool $active = true): int
    {
        $isActive = $active ? 1 : 0;
        $stmt = $conn->prepare(
            "INSERT INTO `roles` (`name`, `guard_name`, `description`, `is_active`, `created_at`, `updated_at`)
             VALUES (?, 'web', ?, ?, NOW(), NOW())"
        );
        $stmt->bind_param('ssi', $name, $description, $isActive);
        $stmt->execute();
        $id = (int) $conn->insert_id;
        $stmt->close();
        return $id;
    }

    public static function update(mysqli $conn, int $roleId, string $name, string $description, bool $active): void
    {
        $isActive = $active ? 1 : 0;
        $stmt = $conn->prepare('UPDATE `roles` SET `name` = ?, `description` = ?, `is_active` = ?, `updated_at` = NOW() WHERE `id` = ?');
        $stmt->bind_param('ssii', $name, $description, $isActive, $roleId);
        $stmt->execute();
        $stmt->close();
    }

    public static function usersCount(mysqli $conn, int $roleId): int
    {
        $stmt = $conn->prepare('SELECT COUNT(*) FROM `users` WHERE `userrole` = ? AND COALESCE(`isdeleted`, 0) <> 1');
        $stmt->bind_param('i', $roleId);
        $stmt->execute();
        $count = (int) $stmt->get_result()->fetch_row()[0];
        $stmt->close();
        return $count;
    }

    /**
     * هل المستخدم الحالي يقدر يدّي الدور ده لمستخدم؟ null = مسموح، غير كده رسالة الرفض.
     */
    public static function assignError(mysqli $conn, int $roleId): ?string
    {
        $role = self::find($conn, $roleId);
        if (!$role || (int) $role['is_active'] !== 1) {
            return 'الدور غير موجود أو معطّل';
        }
        return null;
    }

    /** @return list<array{id:int, name:string}> للقوائم المنسدلة في شاشات المستخدمين */
    public static function options(mysqli $conn, bool $activeOnly = true): array
    {
        $sql = "SELECT `id`, `name` FROM `roles` WHERE `guard_name` = 'web'" . ($activeOnly ? ' AND `is_active` = 1' : '') . ' ORDER BY `id`';
        $out = [];
        $res = $conn->query($sql);
        while ($row = $res->fetch_assoc()) {
            $out[] = ['id' => (int) $row['id'], 'name' => (string) $row['name']];
        }
        return $out;
    }

    public static function log(mysqli $conn, string $text): void
    {
        $stmt = $conn->prepare('INSERT INTO `process` (`type`) VALUES (?)');
        $stmt->bind_param('s', $text);
        $stmt->execute();
        $stmt->close();
    }
}
