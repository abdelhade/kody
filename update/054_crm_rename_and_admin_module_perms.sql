-- تسمية مجموعة CRM في شاشة الأدوار + منح دور الأدمن صلاحيات الكروت/التأجير/العيادة الناقصة
UPDATE `permissions`
SET `group_name` = 'CRM', `updated_at` = NOW()
WHERE `group_key` = 'crm' AND `guard_name` = 'web';

-- دور الأدمن (1) بياخد كل صلاحيات المجموعات دي لو ناقصة
INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, 1
FROM `permissions` p
WHERE p.`guard_name` = 'web'
  AND p.`group_key` IN ('cards', 'rents', 'clinics')
  AND EXISTS (SELECT 1 FROM `roles` WHERE `id` = 1);
