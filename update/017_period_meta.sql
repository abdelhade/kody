-- جدول وصف المدة المالية لكل قاعدة
CREATE TABLE IF NOT EXISTS `period_meta` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `db_name` VARCHAR(64) NOT NULL,
  `label` VARCHAR(120) NOT NULL,
  `parent_db` VARCHAR(64) DEFAULT NULL,
  `successor_db` VARCHAR(64) DEFAULT NULL,
  `is_closed` TINYINT(1) NOT NULL DEFAULT 0,
  `closed_at` DATETIME DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_db_name` (`db_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
