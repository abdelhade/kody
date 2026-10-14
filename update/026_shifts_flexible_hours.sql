-- وردية بساعات مرنة: بدون مواعيد دخول/خروج، الحساب بعدد الساعات المطلوبة يوميًا
ALTER TABLE `shifts`
  ADD COLUMN `is_flexible` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'ساعات مرنة';

ALTER TABLE `shifts`
  ADD COLUMN `required_hours` DECIMAL(5,2) NOT NULL DEFAULT 8.00 COMMENT 'الساعات المطلوبة يوميًا (مرن)';

ALTER TABLE `shifts`
  ADD COLUMN `day_cutoff` TIME NOT NULL DEFAULT '06:00:00' COMMENT 'بداية يوم العمل: البصمة قبلها تتبع اليوم السابق (مرن)';

ALTER TABLE `shifts`
  ADD COLUMN `dup_fp_minutes` INT NOT NULL DEFAULT 2 COMMENT 'البصمات المتتالية خلال هذه الدقائق تُعد بصمة واحدة (مرن)';
