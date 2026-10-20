-- تصحيح أسماء أنواع المردود في pro_tybes
-- الكود (InvoiceProcessor): 10 = مردود مشتريات، 11 = مردود مبيعات
UPDATE `pro_tybes` SET `pname` = 'فاتورة مردود مشتريات' WHERE `id` = 10;
UPDATE `pro_tybes` SET `pname` = 'فاتورة مردود مبيعات' WHERE `id` = 11;
