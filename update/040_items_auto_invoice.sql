-- صلاحية: صنف جديد تلقائي في الفاتورة عند عدم وجود نتائج بحث
INSERT IGNORE INTO `permissions` (`name`, `guard_name`, `label`, `group_key`, `group_name`, `entity_label`, `action`, `sort`, `created_at`, `updated_at`) VALUES
('items.auto_invoice', 'web', 'صنف جديد تلقائي في الفاتورة', 'inventory', 'المخزون', 'الأصناف', NULL, 625, NOW(), NOW());

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, 1
FROM `permissions` p
WHERE p.`name` = 'items.auto_invoice'
  AND p.`guard_name` = 'web'
  AND EXISTS (SELECT 1 FROM `roles` WHERE `id` = 1);
