-- افتراضيات المستخدم: المورد وقائمة الأسعار (المخزن/الخزنة/العميل/الموظف موجودين من قبل)
ALTER TABLE `users`
  ADD COLUMN `def_supplier` INT NULL DEFAULT NULL COMMENT 'المورد الافتراضي (acc_head.id)',
  ADD COLUMN `def_price_list` INT NULL DEFAULT NULL COMMENT 'قائمة الأسعار الافتراضية (price_lists.id)';

INSERT IGNORE INTO `permissions` (`name`, `guard_name`, `label`, `group_key`, `group_name`, `entity_label`, `action`, `sort`, `created_at`, `updated_at`) VALUES
('defaults.change', 'web', 'تغيير الافتراضيات في الفواتير والكاشير والسندات', 'users', 'المستخدمين والأدوار', 'تغيير الافتراضيات في الفواتير والكاشير والسندات', NULL, 2750, NOW(), NOW());

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, 1
FROM `permissions` p
WHERE p.`name` = 'defaults.change' AND EXISTS (SELECT 1 FROM `roles` WHERE `id` = 1);
