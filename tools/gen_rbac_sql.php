<?php
/**
 * يولّد update/028 و 029 و 030 من includes/rbac_catalog.php
 * التشغيل: php tools/gen_rbac_sql.php
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

$root    = dirname(__DIR__);
$catalog = require $root . '/includes/rbac_catalog.php';

const ACTIONS = ['view' => 'عرض', 'add' => 'إضافة', 'edit' => 'تعديل', 'delete' => 'حذف'];
const LEGACY_PREFIX = ['view' => 'show', 'add' => 'add', 'edit' => 'edit', 'delete' => 'delete'];

// القيم الافتراضية اللي connect.php كان بيضيف بيها الأعمدة دي وقت التشغيل
const RUNTIME_DEFAULTS = [
    'sid_visits' => 1, 'show_main_hr' => 1, 'is_fav_delivery' => 0, 'show_delivery' => 1,
    'add_delivery' => 1, 'edit_delivery' => 1, 'delete_delivery' => 1, 'sid_cards' => 1,
    'edit_user_passwords' => 0, 'prevent_negative_stock' => 0, 'allow_pos_delivery' => 1,
    'view_shift_expected' => 0,
];

function q(string $s): string
{
    return "'" . str_replace(["\\", "'"], ["\\\\", "''"], $s) . "'";
}

/** @return list<array{name:string,label:string,group_key:string,group_name:string,entity_label:string,action:?string,sort:int,legacy:string}> */
function flatten(array $catalog): array
{
    $out  = [];
    $sort = 0;
    foreach ($catalog as $groupKey => $group) {
        foreach ($group['items'] as $item) {
            if (isset($item['crud'])) {
                foreach (ACTIONS as $action => $actionLabel) {
                    $out[] = [
                        'name'         => $item['crud'] . '.' . $action,
                        'label'        => $actionLabel . ' ' . $item['label'],
                        'group_key'    => $groupKey,
                        'group_name'   => $group['label'],
                        'entity_label' => $item['label'],
                        'action'       => $action,
                        'sort'         => $sort += 10,
                        'legacy'       => $item['legacy'],
                    ];
                }
            } else {
                $out[] = [
                    'name'         => $item['single'],
                    'label'        => $item['label'],
                    'group_key'    => $groupKey,
                    'group_name'   => $group['label'],
                    'entity_label' => $item['label'],
                    'action'       => null,
                    'sort'         => $sort += 10,
                    'legacy'       => $item['legacy'],
                ];
            }
        }
    }
    return $out;
}

/**
 * شرط WHERE على usr_pwrs u للصلاحية، أو null لو المصدر all / role1.
 * @param list<string> $columns  بيتجمع فيه كل عمود قديم بنقرا منه
 */
function legacyCondition(array $perm, array &$columns): ?string
{
    [$kind, $arg] = array_pad(explode(':', $perm['legacy'], 2), 2, '');
    $cols = [];
    switch ($kind) {
        case 'cols':
            foreach (explode('|', $arg) as $suffix) {
                $cols[] = LEGACY_PREFIX[$perm['action']] . '_' . $suffix;
            }
            break;
        case 'col':
        case 'any':
            $cols = explode('|', $arg);
            break;
        case 'not':
            $columns[] = $arg;
            return "COALESCE(u.`$arg`, 0) = 0";
        case 'all':
        case 'role1':
            return null;
        default:
            throw new RuntimeException("legacy غير معروف: {$perm['legacy']}");
    }
    foreach ($cols as $c) {
        $columns[] = $c;
    }
    return '(' . implode(' OR ', array_map(fn($c) => "u.`$c` = 1", $cols)) . ')';
}

$perms = flatten($catalog);
$names = array_column($perms, 'name');
if (count($names) !== count(array_unique($names))) {
    throw new RuntimeException('فيه صلاحية متكررة في الكتالوج');
}

// ---------------------------------------------------------------- 030
$legacyColumns = [];
$grants = [];
foreach ($perms as $p) {
    $cond = legacyCondition($p, $legacyColumns);
    $name = q($p['name']);
    if ($p['legacy'] === 'all') {
        $grants[] = "INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)\n"
            . "SELECT p.`id`, r.`id` FROM `roles` r JOIN `permissions` p ON p.`name` = $name AND p.`guard_name` = 'web';";
    } elseif ($p['legacy'] === 'role1') {
        $grants[] = "INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)\n"
            . "SELECT p.`id`, r.`id` FROM `roles` r JOIN `permissions` p ON p.`name` = $name AND p.`guard_name` = 'web' WHERE r.`id` = 1;";
    } else {
        $grants[] = "INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)\n"
            . "SELECT p.`id`, u.`id` FROM `usr_pwrs` u\n"
            . "JOIN `roles` r ON r.`id` = u.`id`\n"
            . "JOIN `permissions` p ON p.`name` = $name AND p.`guard_name` = 'web'\n"
            . "WHERE $cond;";
    }
}

$sql030 = <<<SQL
-- نقل الأدوار والصلاحيات من usr_pwrs للجداول الجديدة.
-- متولّد من includes/rbac_catalog.php بـ tools/gen_rbac_sql.php — متعدّلش يدوي.
-- الأدوار بتتنقل بنفس الـ id عشان users.userrole يفضل صح. الدور المحذوف بيتنقل لو لسه عليه مستخدمين
-- (النظام القديم كان بيحمّل صلاحياته عادي)، والاسم المكرر بياخد رقمه عشان UNIQUE (name, guard_name).

INSERT IGNORE INTO `roles` (`id`, `name`, `guard_name`, `description`, `is_active`, `created_at`, `updated_at`)
SELECT u.`id`,
       CASE
         WHEN NULLIF(TRIM(u.`rollname`), '') IS NULL THEN CONCAT('دور ', u.`id`)
         WHEN EXISTS (SELECT 1 FROM `usr_pwrs` u2
                      WHERE TRIM(u2.`rollname`) = TRIM(u.`rollname`) AND u2.`id` < u.`id`)
           THEN CONCAT(TRIM(u.`rollname`), ' #', u.`id`)
         ELSE TRIM(u.`rollname`)
       END,
       'web',
       u.`info`,
       COALESCE(u.`is_active`, 1),
       NOW(), NOW()
FROM `usr_pwrs` u
WHERE COALESCE(u.`isdeleted`, 0) = 0
   OR EXISTS (SELECT 1 FROM `users` us WHERE us.`userrole` = u.`id`);


SQL;
$sql030 .= implode("\n\n", $grants) . "\n\n";
$sql030 .= <<<SQL
-- قاعدة CRUD: أي دور معاه add / edit / delete لكيان ياخد view بتاعه
INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT v.`id`, x.`role_id`
FROM (
    SELECT DISTINCT rhp.`role_id`, SUBSTRING_INDEX(p.`name`, '.', 1) AS entity
    FROM `role_has_permissions` rhp
    JOIN `permissions` p ON p.`id` = rhp.`permission_id`
    WHERE p.`action` IN ('add', 'edit', 'delete')
) x
JOIN `permissions` v ON v.`name` = CONCAT(x.entity, '.view') AND v.`guard_name` = 'web';

SQL;

// ---------------------------------------------------------------- 028
$legacyColumns = array_values(array_unique($legacyColumns));
sort($legacyColumns);
$sql028 = "-- توحيد أعمدة usr_pwrs على كل قواعد البيانات قبل نقلها لـ role_has_permissions (030).\n"
    . "-- الأعمدة دي كان connect.php بيضيف بعضها وقت التشغيل، فممكن تكون ناقصة عند بعض العملاء.\n"
    . "-- متولّد من tools/gen_rbac_sql.php — متعدّلش يدوي.\n\n";
$sql028 .= "ALTER TABLE `usr_pwrs` ADD COLUMN `rollname` VARCHAR(255) NULL;\n";
$sql028 .= "ALTER TABLE `usr_pwrs` ADD COLUMN `info` VARCHAR(255) NULL;\n";
$sql028 .= "ALTER TABLE `usr_pwrs` ADD COLUMN `is_active` TINYINT(1) NOT NULL DEFAULT 1;\n";
$sql028 .= "ALTER TABLE `usr_pwrs` ADD COLUMN `isdeleted` TINYINT(1) NOT NULL DEFAULT 0;\n";
foreach ($legacyColumns as $c) {
    $default = RUNTIME_DEFAULTS[$c] ?? 0;
    $sql028 .= "ALTER TABLE `usr_pwrs` ADD COLUMN `$c` INT(11) NOT NULL DEFAULT $default;\n";
}
$sql028 .= "\n-- كان بيتضاف وقت التشغيل من connect.php\n";
$sql028 .= "ALTER TABLE `acc_head` ADD COLUMN `price_list` INT NOT NULL DEFAULT 1;\n";

// ---------------------------------------------------------------- 029
$rows = [];
foreach ($perms as $p) {
    $rows[] = sprintf(
        "(%s, 'web', %s, %s, %s, %s, %s, %d, NOW(), NOW())",
        q($p['name']), q($p['label']), q($p['group_key']), q($p['group_name']),
        q($p['entity_label']), $p['action'] === null ? 'NULL' : q($p['action']), $p['sort']
    );
}

$sql029 = <<<SQL
-- جداول الأدوار والصلاحيات (نفس شكل spatie/laravel-permission عشان Laravel يقرا منها)
-- + كل صلاحيات النظام. متولّد من includes/rbac_catalog.php — متعدّلش يدوي.

CREATE TABLE IF NOT EXISTS `roles` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `guard_name` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`, `guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `permissions` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `guard_name` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`, `guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `role_has_permissions` (
  `permission_id` BIGINT(20) UNSIGNED NOT NULL,
  `role_id` BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (`permission_id`, `role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `roles` ADD COLUMN `description` VARCHAR(255) NULL COMMENT 'وصف الدور';
ALTER TABLE `roles` ADD COLUMN `is_active` TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'الدور المعطّل مالوش صلاحيات';

ALTER TABLE `permissions` ADD COLUMN `label` VARCHAR(150) NOT NULL DEFAULT '' COMMENT 'الاسم في شاشة الدور';
ALTER TABLE `permissions` ADD COLUMN `group_key` VARCHAR(30) NOT NULL DEFAULT '' COMMENT 'قسم القائمة الجانبية';
ALTER TABLE `permissions` ADD COLUMN `group_name` VARCHAR(100) NOT NULL DEFAULT '';
ALTER TABLE `permissions` ADD COLUMN `entity_label` VARCHAR(150) NOT NULL DEFAULT '' COMMENT 'اسم الكيان (صف في جدول CRUD)';
ALTER TABLE `permissions` ADD COLUMN `action` VARCHAR(10) NULL COMMENT 'view/add/edit/delete للكيانات، NULL للأفعال';
ALTER TABLE `permissions` ADD COLUMN `sort` INT NOT NULL DEFAULT 0;

INSERT INTO `permissions` (`name`, `guard_name`, `label`, `group_key`, `group_name`, `entity_label`, `action`, `sort`, `created_at`, `updated_at`) VALUES

SQL;
$sql029 .= implode(",\n", $rows) . "\n";
$sql029 .= "ON DUPLICATE KEY UPDATE `label` = VALUES(`label`), `group_key` = VALUES(`group_key`), `group_name` = VALUES(`group_name`),\n"
    . "  `entity_label` = VALUES(`entity_label`), `action` = VALUES(`action`), `sort` = VALUES(`sort`), `updated_at` = NOW();\n";

file_put_contents($root . '/update/028_usr_pwrs_legacy_columns.sql', $sql028);
file_put_contents($root . '/update/029_rbac_tables.sql', $sql029);
file_put_contents($root . '/update/030_rbac_copy_from_usr_pwrs.sql', $sql030);

echo 'permissions: ' . count($perms) . PHP_EOL;
echo 'legacy columns: ' . count($legacyColumns) . PHP_EOL;
