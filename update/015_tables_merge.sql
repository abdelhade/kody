-- أعمدة دمج الطاولات
ALTER TABLE `tables`
  ADD COLUMN `parent_table_id` INT DEFAULT NULL COMMENT 'الطاولة الرئيسية في حالة الدمج';

ALTER TABLE `tables`
  ADD COLUMN `is_merged` TINYINT(1) DEFAULT 0 COMMENT 'هل الطاولة مدمجة';
