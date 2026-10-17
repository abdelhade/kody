-- إنشاء جداول ميزة Pulse للتقييم اللحظي وأنواع التقييمات وسجلات التقييم
CREATE TABLE IF NOT EXISTS `pulse_types` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `category` ENUM('positive','negative') NOT NULL DEFAULT 'positive',
  `icon` VARCHAR(50) DEFAULT 'fas fa-star',
  `points` INT(11) DEFAULT 1,
  `isdeleted` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `pulse_logs` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `employee_id` INT(11) NOT NULL,
  `type_id` INT(11) NOT NULL,
  `category` ENUM('positive','negative') NOT NULL,
  `rating` INT(11) DEFAULT 5,
  `notes` TEXT,
  `recorded_by` INT(11) NOT NULL,
  `recorded_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_employee` (`employee_id`),
  KEY `idx_type` (`type_id`),
  KEY `idx_recorded_at` (`recorded_at`),
  KEY `idx_category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `settings`
  ADD COLUMN `showpulse` TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'تفعيل نظام Pulse للتقييم اللحظي';

INSERT INTO `pulse_types` (`id`, `name`, `category`, `icon`, `points`) VALUES
(1, 'الالتزام بالمواعيد', 'positive', 'fas fa-clock', 3),
(2, 'جودة العمل', 'positive', 'fas fa-award', 5),
(3, 'روح الفريق', 'positive', 'fas fa-users', 4),
(4, 'المبادرة', 'positive', 'fas fa-lightbulb', 5),
(5, 'خدمة العملاء', 'positive', 'fas fa-handshake', 4),
(6, 'النظافة والترتيب', 'positive', 'fas fa-broom', 2),
(7, 'التأخر', 'negative', 'fas fa-clock', -3),
(8, 'الإهمال', 'negative', 'fas fa-exclamation-triangle', -5),
(9, 'عدم التعاون', 'negative', 'fas fa-user-slash', -4),
(10, 'سوء التعامل', 'negative', 'fas fa-frown', -5)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`), `category` = VALUES(`category`), `icon` = VALUES(`icon`), `points` = VALUES(`points`);
