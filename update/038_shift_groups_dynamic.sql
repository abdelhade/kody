-- 038_shift_groups_dynamic.sql
-- مجموعات الورديات والتعرف التلقائي الديناميكي على الوردية بناءً على وقت البصمة

CREATE TABLE IF NOT EXISTS `shift_groups` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL COMMENT 'اسم مجموعة الورديات',
  `info` TEXT DEFAULT NULL COMMENT 'ملاحظات وتفاصيل المجموعة',
  `max_detect_window` INT NOT NULL DEFAULT 720 COMMENT 'أقصى نافذة للدقائق للتعرف التلقائي',
  `isdeleted` TINYINT(1) NOT NULL DEFAULT 0,
  `tenant` INT NOT NULL DEFAULT 0,
  `branch` INT NOT NULL DEFAULT 0,
  `crtime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_shift_groups_tenant` (`tenant`, `isdeleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `shift_group_items` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `group_id` INT NOT NULL COMMENT 'رقم مجموعة الورديات',
  `shift_id` INT NOT NULL COMMENT 'رقم الوردية من shifts',
  `sort` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_group_shift_unique` (`group_id`, `shift_id`),
  KEY `idx_shift_group_items_group` (`group_id`),
  KEY `idx_shift_group_items_shift` (`shift_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `employees`
  ADD COLUMN `shift_group_id` INT NULL DEFAULT NULL COMMENT 'مجموعة الورديات للتعرف التلقائي الديناميكي';

ALTER TABLE `attlog`
  ADD COLUMN `shift_id` INT NULL DEFAULT NULL COMMENT 'الوردية المستنتجة أو المطبقة لليوم';

ALTER TABLE `attandance`
  ADD COLUMN `detected_shift_id` INT NULL DEFAULT NULL COMMENT 'الوردية المستنتجة عند تسجيل البصمة';
