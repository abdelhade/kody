-- سند قبض/دفع متعدد + قيد متعدد كنوع مستقل
UPDATE `pro_tybes` SET `pname` = 'سند قبض متعدد' WHERE `id` = 21;
UPDATE `pro_tybes` SET `pname` = 'سند دفع متعدد' WHERE `id` = 22;

INSERT INTO `pro_tybes` (`id`, `pname`, `ptext`, `ptybe`, `info`)
SELECT 23, 'قيد متعدد', '', 23, NULL
WHERE NOT EXISTS (SELECT 1 FROM `pro_tybes` WHERE `id` = 23);
