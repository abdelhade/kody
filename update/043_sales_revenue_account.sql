-- حساب فرعي تحت 3111 (المبيعات) عشان قيود المبيعات متتقيدش على إيرادات التأجير (32%)
-- الاسم مختلف عن الأب «المبيعات» لأن aname عليه UNIQUE
INSERT INTO `acc_head` (`code`, `aname`, `parent_id`, `kind`, `is_basic`, `deletable`, `editable`)
SELECT '311101', 'مبيعات التشغيل', p.`id`, p.`kind`, 0, 0, 1
FROM `acc_head` p
WHERE p.`code` = '3111'
  AND p.`isdeleted` = 0
  AND NOT EXISTS (
      SELECT 1 FROM (SELECT `code`, `is_basic`, `isdeleted` FROM `acc_head`) c
      WHERE c.`code` LIKE '3111%' AND c.`is_basic` = 0 AND c.`isdeleted` = 0
  )
LIMIT 1;
