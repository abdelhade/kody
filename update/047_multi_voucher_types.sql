-- نوع العملية لسند القبض المتعدد وسند الدفع المتعدد
INSERT INTO `pro_tybes` (`id`, `pname`, `ptext`, `ptybe`, `info`)
SELECT 21, 'سند قبض متعدد', '', 21, NULL
WHERE NOT EXISTS (SELECT 1 FROM `pro_tybes` WHERE `id` = 21);

INSERT INTO `pro_tybes` (`id`, `pname`, `ptext`, `ptybe`, `info`)
SELECT 22, 'سند دفع متعدد', '', 22, NULL
WHERE NOT EXISTS (SELECT 1 FROM `pro_tybes` WHERE `id` = 22);
