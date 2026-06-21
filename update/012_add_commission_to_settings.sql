-- عمولة الموظفين والمستخدمين في الإعدادات
ALTER TABLE `settings`
  ADD COLUMN `emp_commission` DOUBLE NOT NULL DEFAULT 0,
  ADD COLUMN `user_commission` DOUBLE NOT NULL DEFAULT 0;
