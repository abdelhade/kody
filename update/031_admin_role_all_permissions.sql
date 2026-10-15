-- دور الأدمن (1) هو دور النظام: بياخد كل الصلاحيات، مهما كان صف usr_pwrs القديم بتاعه
INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, 1
FROM `permissions` p
WHERE EXISTS (SELECT 1 FROM `roles` WHERE `id` = 1);
