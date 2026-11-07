-- قيد تكلفة البضاعة المباعة لفواتير المبيعات/الكاشير/مردود المبيعات
-- journal_heads.pro_tybe = 20 يميّز قيد التكلفة عن القيد الرئيسي للفاتورة (نفس op_id)
INSERT INTO `pro_tybes` (`id`, `pname`, `ptext`, `ptybe`, `info`)
SELECT 20, 'قيد تكلفة مبيعات', '', 20, NULL
WHERE NOT EXISTS (SELECT 1 FROM `pro_tybes` WHERE `id` = 20);

-- حساب فرعي تحت 42 (تكلفة البضاعة المباعة) لو مفيش حساب فرعي يتقيد عليه
INSERT INTO `acc_head` (`code`, `aname`, `parent_id`, `kind`, `is_basic`, `deletable`, `editable`)
SELECT '42001', 'تكلفة البضاعة المباعة', p.`id`, p.`kind`, 0, 0, 1
FROM `acc_head` p
WHERE p.`code` = '42'
  AND p.`isdeleted` = 0
  AND NOT EXISTS (
      SELECT 1 FROM (SELECT `code`, `is_basic`, `isdeleted` FROM `acc_head`) c
      WHERE c.`code` LIKE '42%' AND c.`is_basic` = 0 AND c.`isdeleted` = 0
  )
LIMIT 1;
