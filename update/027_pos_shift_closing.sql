-- تقفيل مبيعات الكاشير: الشيفت = حركات المستخدم اللي لسه مش مربوطة بشيفت، وعند التقفيل تتربط برقمه

ALTER TABLE `ot_head`
  ADD COLUMN `shift_id` INT NOT NULL DEFAULT 0 COMMENT 'رقم الشيفت في closed_orders (0 = شيفت مفتوح، -1 = قبل نظام الشيفتات)';

ALTER TABLE `ot_head`
  ADD INDEX `idx_ot_head_user_shift` (`user`, `shift_id`);

ALTER TABLE `closed_orders`
  MODIFY `shift` VARCHAR(30) NOT NULL DEFAULT '';

ALTER TABLE `closed_orders`
  MODIFY `user` VARCHAR(50) DEFAULT NULL;

ALTER TABLE `closed_orders`
  MODIFY `info` VARCHAR(255) DEFAULT NULL;

ALTER TABLE `closed_orders`
  ADD COLUMN `user_id` INT NOT NULL DEFAULT 0 COMMENT 'رقم الكاشير';

ALTER TABLE `closed_orders`
  ADD COLUMN `opened_at` DATETIME DEFAULT NULL COMMENT 'أول حركة في الشيفت';

ALTER TABLE `closed_orders`
  ADD COLUMN `closed_at` DATETIME DEFAULT NULL COMMENT 'وقت التقفيل';

ALTER TABLE `closed_orders`
  ADD COLUMN `orders_count` INT NOT NULL DEFAULT 0 COMMENT 'عدد فواتير البيع';

ALTER TABLE `closed_orders`
  ADD COLUMN `total_receipts` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'سندات قبض مستقلة';

ALTER TABLE `closed_orders`
  ADD COLUMN `expected_total` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'إجمالي المتوقع في الخزائن';

ALTER TABLE `closed_orders`
  ADD COLUMN `counted_total` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'إجمالي المعدود';

ALTER TABLE `closed_orders`
  ADD INDEX `idx_closed_orders_user_id` (`user_id`);

CREATE TABLE IF NOT EXISTS `shift_payments` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `shift_id` INT NOT NULL COMMENT 'closed_orders.id',
  `account_id` INT NOT NULL COMMENT 'الخزنة/البنك من acc_head',
  `start_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'رصيد بداية الشيفت',
  `movement` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'صافي الحركة خلال الشيفت',
  `expected` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `counted` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `diff` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'المعدود - المتوقع',
  `crtime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_shift_payments_shift` (`shift_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `settings`
  ADD COLUMN `pos_blind_close` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'جرد أعمى: الكاشير ميشوفش المتوقع قبل التقفيل';

ALTER TABLE `usr_pwrs`
  ADD COLUMN `view_shift_expected` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'يشوف المتوقع في تقفيل الشيفت حتى مع الجرد الأعمى';

UPDATE `usr_pwrs` SET `view_shift_expected` = 1 WHERE `id` = 1 OR `rollname` = 'admin';

-- الحركات القديمة متدخلش في أول شيفت: اللي اتقفلت بالنظام القديم أو قبل النهارده
UPDATE `ot_head` SET `shift_id` = -1 WHERE `shift_id` = 0 AND (`closed` > 0 OR `crtime` < CURDATE());

UPDATE `ot_head` oh
  JOIN `users` u ON u.id = oh.`user`
  JOIN (SELECT `user`, MAX(`crtime`) AS last_close FROM `closed_orders` GROUP BY `user`) c ON c.`user` = u.uname
  SET oh.`shift_id` = -1
  WHERE oh.`shift_id` = 0 AND oh.`crtime` <= c.last_close;
