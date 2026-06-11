-- بصمة واحدة: نص يوم (half) أو إلغاء اليوم (cancel)
ALTER TABLE `shifts`
  ADD COLUMN `single_fp_rule` VARCHAR(20) NOT NULL DEFAULT 'half' AFTER `workingdays`;
