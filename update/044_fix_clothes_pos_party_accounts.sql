-- إصلاح فواتير كاشير الملابس القديمة: العميل كان في acc1 والمخزن في acc2
-- الصيغة القياسية: acc2 = العميل، acc1 = الصندوق (من سند القبض إن وُجد)
UPDATE `ot_head` h
INNER JOIN `acc_head` a2 ON a2.`id` = h.`acc2` AND IFNULL(a2.`is_stock`, 0) = 1
INNER JOIN `acc_head` a1 ON a1.`id` = h.`acc1` AND IFNULL(a1.`is_stock`, 0) = 0 AND IFNULL(a1.`is_fund`, 0) = 0
LEFT JOIN (
    SELECT `op2`, MAX(`acc1`) AS `fund_id`
    FROM `ot_head`
    WHERE `pro_tybe` = 1 AND IFNULL(`isdeleted`, 0) = 0 AND IFNULL(`op2`, 0) > 0
    GROUP BY `op2`
) pay ON pay.`op2` = h.`id`
SET
    h.`acc2` = h.`acc1`,
    h.`acc1` = COALESCE(NULLIF(pay.`fund_id`, 0), h.`acc1`)
WHERE h.`pro_tybe` = 9
  AND IFNULL(h.`isdeleted`, 0) = 0
  AND IFNULL(h.`store_id`, 0) > 0
  AND h.`acc2` = h.`store_id`
  AND h.`acc1` <> h.`store_id`;
