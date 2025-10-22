-- ==========================================
-- Migration: 003 - Add Missing Columns
-- Purpose: إضافة أعمدة مفقودة للجداول الموجودة
-- Date: 2025-10-17
-- Safe to run: YES (إضافة فقط)
-- ==========================================

USE hrmsnat;

-- ==========================================
-- 1. Add isdeleted to fat_details
-- ==========================================

ALTER TABLE `fat_details`
  ADD COLUMN IF NOT EXISTS `isdeleted` TINYINT(1) NOT NULL DEFAULT 0 AFTER `crtime`,
  ADD INDEX IF NOT EXISTS `idx_isdeleted` (`isdeleted`);

-- ==========================================
-- 2. Enhance ot_head table
-- ==========================================

ALTER TABLE `ot_head`
  ADD COLUMN IF NOT EXISTS `table_id` INT NULL COMMENT 'رقم الطاولة' AFTER `branch_id`,
  ADD COLUMN IF NOT EXISTS `order_type` ENUM('dine_in', 'takeaway', 'delivery', 'table') DEFAULT 'takeaway' AFTER `table_id`,
  ADD COLUMN IF NOT EXISTS `payment_status` ENUM('unpaid', 'partial', 'paid', 'refunded') DEFAULT 'unpaid' AFTER `fat_tax_per`,
  ADD COLUMN IF NOT EXISTS `invoice_status` ENUM('draft', 'completed', 'cancelled') DEFAULT 'completed' AFTER `payment_status`,
  ADD COLUMN IF NOT EXISTS `paid_amount` DECIMAL(15,2) DEFAULT 0 AFTER `fat_tax_per`,
  ADD COLUMN IF NOT EXISTS `remaining_amount` DECIMAL(15,2) DEFAULT 0 AFTER `paid_amount`,
  ADD COLUMN IF NOT EXISTS `isdeleted` TINYINT(1) DEFAULT 0 AFTER `user`,
  ADD INDEX IF NOT EXISTS `idx_table` (`table_id`),
  ADD INDEX IF NOT EXISTS `idx_order_type` (`order_type`),
  ADD INDEX IF NOT EXISTS `idx_payment_status` (`payment_status`),
  ADD INDEX IF NOT EXISTS `idx_isdeleted` (`isdeleted`);

-- ==========================================
-- 3. Enhance myitems table
-- ==========================================

ALTER TABLE `myitems`
  ADD COLUMN IF NOT EXISTS `track_stock` TINYINT(1) DEFAULT 1 COMMENT 'تتبع المخزون؟' AFTER `isdeleted`,
  ADD COLUMN IF NOT EXISTS `reorder_level` DECIMAL(10,2) DEFAULT 0 COMMENT 'حد إعادة الطلب' AFTER `track_stock`,
  ADD COLUMN IF NOT EXISTS `is_active` TINYINT(1) DEFAULT 1 AFTER `reorder_level`,
  ADD COLUMN IF NOT EXISTS `tax_percentage` DECIMAL(5,2) DEFAULT 0 AFTER `is_active`;

-- ==========================================
-- 4. Enhance imgs table
-- ==========================================

ALTER TABLE `imgs`
  ADD COLUMN IF NOT EXISTS `is_primary` TINYINT(1) DEFAULT 0 COMMENT 'صورة رئيسية؟' AFTER `itemid`,
  ADD COLUMN IF NOT EXISTS `sort_order` INT DEFAULT 0 AFTER `is_primary`,
  ADD INDEX IF NOT EXISTS `idx_is_primary` (`itemid`, `is_primary`);

-- ==========================================
-- 5. Add settings for POS
-- ==========================================

ALTER TABLE `settings`
  ADD COLUMN IF NOT EXISTS `def_pos_store` INT NULL COMMENT 'المخزن الافتراضي للPOS' AFTER `showtsk`,
  ADD COLUMN IF NOT EXISTS `def_pos_employee` INT NULL COMMENT 'الموظف الافتراضي للPOS' AFTER `def_pos_store`,
  ADD COLUMN IF NOT EXISTS `def_pos_client` INT NULL COMMENT 'العميل الافتراضي للPOS' AFTER `def_pos_employee`,
  ADD COLUMN IF NOT EXISTS `def_pos_fund` INT NULL COMMENT 'الصندوق الافتراضي للPOS' AFTER `def_pos_client`,
  ADD COLUMN IF NOT EXISTS `pos_auto_print` TINYINT(1) DEFAULT 0 COMMENT 'طباعة تلقائية' AFTER `def_pos_fund`,
  ADD COLUMN IF NOT EXISTS `pos_show_stock` TINYINT(1) DEFAULT 1 COMMENT 'عرض الكمية المتوفرة' AFTER `pos_auto_print`,
  ADD COLUMN IF NOT EXISTS `pos_allow_negative_stock` TINYINT(1) DEFAULT 0 COMMENT 'السماح بالسالب' AFTER `pos_show_stock`;

-- ==========================================
-- 6. Add branch support
-- ==========================================

ALTER TABLE `myitems`
  ADD COLUMN IF NOT EXISTS `branch_id` INT NULL COMMENT 'الفرع' AFTER `user`;

ALTER TABLE `item_group`
  ADD COLUMN IF NOT EXISTS `isdeleted` TINYINT(1) DEFAULT 0 AFTER `user`;

ALTER TABLE `item_group2`
  ADD COLUMN IF NOT EXISTS `isdeleted` TINYINT(1) DEFAULT 0 AFTER `user`;

ALTER TABLE `item_group3`
  ADD COLUMN IF NOT EXISTS `isdeleted` TINYINT(1) DEFAULT 0 AFTER `user`;

-- ==========================================
-- 7. Add receipt numbering
-- ==========================================

ALTER TABLE `ot_head`
  ADD COLUMN IF NOT EXISTS `receipt_number` VARCHAR(50) UNIQUE AFTER `pro_num`;

SELECT 'Missing columns added successfully!' as status;

