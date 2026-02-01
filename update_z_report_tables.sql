-- Add new columns to closed_orders table for detailed Z-Report
-- columns for payment methods breakdown
ALTER TABLE `closed_orders` ADD COLUMN IF NOT EXISTS `total_cash` DECIMAL(10,2) DEFAULT 0.00;
ALTER TABLE `closed_orders` ADD COLUMN IF NOT EXISTS `total_visa` DECIMAL(10,2) DEFAULT 0.00;
ALTER TABLE `closed_orders` ADD COLUMN IF NOT EXISTS `total_discount` DECIMAL(10,2) DEFAULT 0.00;
ALTER TABLE `closed_orders` ADD COLUMN IF NOT EXISTS `total_returns` DECIMAL(10,2) DEFAULT 0.00;
ALTER TABLE `closed_orders` ADD COLUMN IF NOT EXISTS `start_cash` DECIMAL(10,2) DEFAULT 0.00; -- العهدة المستلمة
ALTER TABLE `closed_orders` ADD COLUMN IF NOT EXISTS `actual_cash` DECIMAL(10,2) DEFAULT 0.00; -- النقدية الفعلية في الدرج
ALTER TABLE `closed_orders` ADD COLUMN IF NOT EXISTS `actual_visa` DECIMAL(10,2) DEFAULT 0.00; -- اجمالي الفيزا الفعلي
ALTER TABLE `closed_orders` ADD COLUMN IF NOT EXISTS `deficit` DECIMAL(10,2) DEFAULT 0.00; -- العجز أو الزيادة
ALTER TABLE `closed_orders` ADD COLUMN IF NOT EXISTS `status` TINYINT(1) DEFAULT 1; -- 1: Closed, 0: Pending review
ALTER TABLE `closed_orders` ADD COLUMN IF NOT EXISTS `json_details` TEXT; -- For storing extra details/breakdown if needed

-- Create settings table for Z-Report config if needed (optional)
-- INSERT IGNORE INTO settings ...
