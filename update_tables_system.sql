-- =====================================================
-- تحديثات نظام الطاولات (Tables / POS)
-- يُشغّل عبر run_table_updates.php
-- =====================================================

-- جدول الطاولات
CREATE TABLE IF NOT EXISTS `tables` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tname` varchar(255) NOT NULL,
  `table_case` int(11) NOT NULL DEFAULT 0,
  `crtime` datetime DEFAULT current_timestamp(),
  `mdtime` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `isdeleted` tinyint(1) NOT NULL DEFAULT 0,
  `branch` varchar(255) DEFAULT NULL,
  `tatnet` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- أعمدة ot_head المطلوبة لنظام الطاولات والسداد
ALTER TABLE `ot_head` ADD COLUMN `table_id` int(11) DEFAULT NULL COMMENT 'رقم الطاولة';
ALTER TABLE `ot_head` ADD COLUMN `order_type` enum('dine_in','takeaway','delivery','table') DEFAULT 'takeaway';
ALTER TABLE `ot_head` ADD COLUMN `receipt_number` varchar(50) DEFAULT NULL;
ALTER TABLE `ot_head` ADD COLUMN `fat_net` double DEFAULT 0;
ALTER TABLE `ot_head` ADD COLUMN `paid_amount` decimal(15,2) DEFAULT 0.00;
ALTER TABLE `ot_head` ADD COLUMN `remaining_amount` decimal(15,2) DEFAULT 0.00;
ALTER TABLE `ot_head` ADD COLUMN `payment_status` enum('unpaid','partial','paid','refunded') DEFAULT 'unpaid';
ALTER TABLE `ot_head` ADD COLUMN `invoice_status` enum('draft','completed','cancelled') DEFAULT 'completed';
ALTER TABLE `ot_head` ADD COLUMN `waiter_id` int(11) DEFAULT NULL COMMENT 'معرف الويتر';
ALTER TABLE `ot_head` ADD COLUMN `order_status` enum('draft','active','completed','cancelled') DEFAULT 'active' COMMENT 'حالة الطلب';
ALTER TABLE `ot_head` ADD COLUMN `payment_method` varchar(20) DEFAULT 'cash' COMMENT 'طريقة الدفع';
ALTER TABLE `ot_head` ADD COLUMN `payment_notes` text DEFAULT NULL COMMENT 'ملاحظات الدفع';
ALTER TABLE `ot_head` ADD COLUMN `payment_date` datetime DEFAULT NULL COMMENT 'تاريخ الدفع';

-- فهارس لتحسين الأداء
ALTER TABLE `ot_head` ADD KEY `idx_table` (`table_id`);
ALTER TABLE `ot_head` ADD KEY `idx_order_type` (`order_type`);
ALTER TABLE `ot_head` ADD KEY `idx_payment_status` (`payment_status`);
ALTER TABLE `ot_head` ADD KEY `idx_isdeleted` (`isdeleted`);
ALTER TABLE `ot_head` ADD KEY `waiter_id` (`waiter_id`);
