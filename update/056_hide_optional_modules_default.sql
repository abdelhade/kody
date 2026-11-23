-- افتراضي القواعد الجديدة: إخفاء التأجير / الكروت / العيادة / الزيارات
-- (ما بنغيّرش قيم showrent/showclinc للقواعد الموجودة)

ALTER TABLE `settings`
  MODIFY COLUMN `showrent` INT NOT NULL DEFAULT 0,
  MODIFY COLUMN `showclinc` INT(1) NOT NULL DEFAULT 0;

ALTER TABLE `settings`
  ADD COLUMN `showcards` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'إظهار قائمة الكروت' AFTER `showclinc`,
  ADD COLUMN `showvisits` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'إظهار الزيارات في نقاط البيع' AFTER `showcards`;
