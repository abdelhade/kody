-- البيع والشراء لكل الحسابات (عملاء وموردين) من الإعدادات
ALTER TABLE `settings`
  ADD COLUMN `allow_all_accounts` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'السماح بالبيع والشراء لجميع الحسابات';
