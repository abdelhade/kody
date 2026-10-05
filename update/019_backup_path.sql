-- مسار حفظ النسخ الاحتياطية في الإعدادات
ALTER TABLE `settings`
  ADD COLUMN `backup_path` VARCHAR(500) NOT NULL DEFAULT '';
