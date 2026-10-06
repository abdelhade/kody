-- كمية المستند لأوامر الشراء والبيع وعروض الأسعار (non-stock — لا تدخل في رصيد المخزون)
ALTER TABLE `fat_details`
  ADD COLUMN `doc_qty` DOUBLE DEFAULT NULL AFTER `qty_out`;

-- ترحيل السطور القديمة: الكمية = القيمة ÷ (السعر - الخصم)
UPDATE `fat_details`
SET `doc_qty` = ROUND(`det_value` / ((`price` * `u_val`) - COALESCE(`discount`, 0)), 3) * `u_val`
WHERE `pro_tybe` IN (12, 13, 14)
  AND `doc_qty` IS NULL
  AND ((`price` * `u_val`) - COALESCE(`discount`, 0)) > 0;
