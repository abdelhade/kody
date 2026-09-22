-- خصم النسبة على بنود الفاتورة
ALTER TABLE `fat_details`
  ADD COLUMN `disc_pct` DECIMAL(10,2) DEFAULT 0.00 AFTER `discount`;
