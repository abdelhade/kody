-- مستند طباعة باركود (أصناف فقط، بدون قيد محاسبي) + صلاحيات
INSERT INTO `pro_tybes` (`id`, `pname`, `ptext`, `ptybe`, `info`)
SELECT 19, 'مستند طباعة باركود', '', 19, NULL
WHERE NOT EXISTS (SELECT 1 FROM `pro_tybes` WHERE `id` = 19);

INSERT IGNORE INTO `permissions` (`name`, `guard_name`, `label`, `group_key`, `group_name`, `entity_label`, `action`, `sort`, `created_at`, `updated_at`) VALUES
('barcode_docs.view', 'web', 'عرض مستندات طباعة الباركود', 'purchases', 'المشتريات', 'مستند طباعة باركود', 'view', 1280, NOW(), NOW()),
('barcode_docs.add', 'web', 'إضافة مستندات طباعة الباركود', 'purchases', 'المشتريات', 'مستند طباعة باركود', 'add', 1290, NOW(), NOW()),
('barcode_docs.edit', 'web', 'تعديل مستندات طباعة الباركود', 'purchases', 'المشتريات', 'مستند طباعة باركود', 'edit', 1300, NOW(), NOW()),
('barcode_docs.delete', 'web', 'حذف مستندات طباعة الباركود', 'purchases', 'المشتريات', 'مستند طباعة باركود', 'delete', 1310, NOW(), NOW());

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, 1
FROM `permissions` p
WHERE p.`name` IN (
    'barcode_docs.view',
    'barcode_docs.add',
    'barcode_docs.edit',
    'barcode_docs.delete'
)
AND p.`guard_name` = 'web'
AND EXISTS (SELECT 1 FROM `roles` WHERE `id` = 1);
