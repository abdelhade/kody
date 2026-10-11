-- إضافة فترات السماح وقواعد الخصم (وقت عادي / نص يوم / يوم) للورديات
ALTER TABLE `shifts`
  ADD COLUMN `grace1_action` VARCHAR(20) NOT NULL DEFAULT 'actual' AFTER `earlylimit`,
  ADD COLUMN `latelimit_2` INT DEFAULT 0 AFTER `grace1_action`,
  ADD COLUMN `earlylimit_2` INT DEFAULT 0 AFTER `latelimit_2`,
  ADD COLUMN `grace2_action` VARCHAR(20) NOT NULL DEFAULT 'half' AFTER `earlylimit_2`;
