-- النوع 14 في الكود = عرض سعر (InvoiceProcessor::OFFER)
UPDATE `pro_tybes` SET `pname` = 'عرض سعر' WHERE `id` = 14;

INSERT INTO `pro_tybes` (`id`, `pname`, `ptext`, `ptybe`, `info`)
SELECT 14, 'عرض سعر', NULL, 14, NULL
WHERE NOT EXISTS (SELECT 1 FROM `pro_tybes` WHERE `id` = 14);
