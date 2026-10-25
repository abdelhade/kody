-- تحويل مخزني (من مخزن لمخزن) + صلاحيات
INSERT INTO `pro_tybes` (`id`, `pname`, `ptext`, `ptybe`, `info`)
SELECT 18, 'تحويل مخزني', '', 18, NULL
WHERE NOT EXISTS (SELECT 1 FROM `pro_tybes` WHERE `id` = 18);

INSERT IGNORE INTO `permissions` (`name`, `guard_name`, `label`, `group_key`, `group_name`, `entity_label`, `action`, `sort`, `created_at`, `updated_at`) VALUES
('store_transfers.view', 'web', 'عرض التحويلات المخزنية', 'inventory', 'المخزون', 'تحويل مخزني', 'view', 752, NOW(), NOW()),
('store_transfers.add', 'web', 'إضافة التحويلات المخزنية', 'inventory', 'المخزون', 'تحويل مخزني', 'add', 753, NOW(), NOW()),
('store_transfers.edit', 'web', 'تعديل التحويلات المخزنية', 'inventory', 'المخزون', 'تحويل مخزني', 'edit', 754, NOW(), NOW()),
('store_transfers.delete', 'web', 'حذف التحويلات المخزنية', 'inventory', 'المخزون', 'تحويل مخزني', 'delete', 755, NOW(), NOW());

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, 1
FROM `permissions` p
WHERE p.`name` IN (
    'store_transfers.view',
    'store_transfers.add',
    'store_transfers.edit',
    'store_transfers.delete'
)
AND p.`guard_name` = 'web'
AND EXISTS (SELECT 1 FROM `roles` WHERE `id` = 1);
