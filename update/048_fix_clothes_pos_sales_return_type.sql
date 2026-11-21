-- مردود كاشير الملابس كان بيتسجل غلط كـ pro_tybe=10 (مردود مشتريات)
-- والصحيح 11 (مردود مبيعات). كمان ot_head كان acc1=عميل و acc2=مخزن بسبب نوع 10.
-- بعد التشغيل: من الإعدادات ← إعادة بناء قيود تكلفة المبيعات عشان القيد الرئيسي
-- (كان مدين المخزن بسعر البيع) يتصلح لحساب المبيعات ويتبني قيد التكلفة.

-- 1) رأس الفاتورة: نوع 11 + طرف العميل في acc2 والصندوق/المخزن في acc1
UPDATE `ot_head` h
INNER JOIN `acc_head` a2 ON a2.`id` = h.`acc2` AND IFNULL(a2.`is_stock`, 0) = 1
INNER JOIN `acc_head` a1 ON a1.`id` = h.`acc1` AND IFNULL(a1.`is_stock`, 0) = 0
LEFT JOIN (
    SELECT `op2`, MAX(`acc1`) AS `fund_id`
    FROM `ot_head`
    WHERE `pro_tybe` IN (1, 2) AND IFNULL(`isdeleted`, 0) = 0 AND IFNULL(`op2`, 0) > 0
    GROUP BY `op2`
) pay ON pay.`op2` = h.`id`
SET
    h.`pro_tybe` = 11,
    h.`journal_tybe` = 11,
    h.`acc2` = h.`acc1`,
    h.`acc1` = COALESCE(NULLIF(pay.`fund_id`, 0), NULLIF(h.`store_id`, 0), h.`acc1`)
WHERE h.`pro_tybe` = 10
  AND IFNULL(h.`isdeleted`, 0) = 0
  AND h.`info` LIKE '%مردود مبيعات%'
  AND h.`acc1` > 0
  AND h.`acc2` > 0
  AND h.`acc1` <> h.`acc2`;

-- لو acc2 مش مخزن (صيغة تانية) بس info مردود مبيعات ونوع 10: حوّل النوع فقط
UPDATE `ot_head`
SET `pro_tybe` = 11, `journal_tybe` = 11
WHERE `pro_tybe` = 10
  AND IFNULL(`isdeleted`, 0) = 0
  AND `info` LIKE '%مردود مبيعات%';

-- 2) تفاصيل الفاتورة
UPDATE `fat_details` fd
INNER JOIN `ot_head` h ON h.`id` = fd.`fatid`
SET fd.`pro_tybe` = 11, fd.`fat_tybe` = 11
WHERE h.`pro_tybe` = 11
  AND h.`info` LIKE '%مردود مبيعات%'
  AND (fd.`pro_tybe` = 10 OR fd.`fat_tybe` = 10);

-- 3) السندات المرتبطة اللي اتعملت قبض (1) بالغلط على مردود الملابس → دفع (2)
-- أولاً عكس القيود وهي لسه نوع 1، بعدين تحويل السند وعكس acc1/acc2
UPDATE `journal_entries` e
INNER JOIN `journal_heads` j ON j.`id` = e.`journal_id`
INNER JOIN `ot_head` v ON v.`id` = j.`op_id`
INNER JOIN `ot_head` inv ON inv.`id` = v.`op2`
SET
    e.`debit` = e.`credit`,
    e.`credit` = e.`debit`,
    e.`tybe` = IF(e.`tybe` = 0, 1, 0)
WHERE inv.`pro_tybe` = 11
  AND inv.`info` LIKE '%مردود مبيعات%'
  AND IFNULL(inv.`isdeleted`, 0) = 0
  AND v.`pro_tybe` = 1
  AND IFNULL(v.`isdeleted`, 0) = 0
  AND IFNULL(v.`op2`, 0) > 0
  AND IFNULL(j.`isdeleted`, 0) = 0
  AND IFNULL(e.`isdeleted`, 0) = 0
  AND j.`op2` = inv.`id`;

UPDATE `ot_head` v
INNER JOIN `ot_head` inv ON inv.`id` = v.`op2`
SET
    v.`pro_tybe` = 2,
    v.`journal_tybe` = 2,
    v.`acc1` = v.`acc2`,
    v.`acc2` = v.`acc1`,
    v.`info` = REPLACE(
        REPLACE(REPLACE(IFNULL(v.`info`, ''), 'سند قبض', 'سند دفع'), 'قبض كاش', 'دفع كاش'),
        'قبض صرافة', 'دفع صرافة'
    )
WHERE inv.`pro_tybe` = 11
  AND inv.`info` LIKE '%مردود مبيعات%'
  AND IFNULL(inv.`isdeleted`, 0) = 0
  AND v.`pro_tybe` = 1
  AND IFNULL(v.`isdeleted`, 0) = 0
  AND IFNULL(v.`op2`, 0) > 0;

UPDATE `journal_heads` j
INNER JOIN `ot_head` v ON v.`id` = j.`op_id`
INNER JOIN `ot_head` inv ON inv.`id` = v.`op2`
SET j.`details` = REPLACE(IFNULL(j.`details`, ''), 'سند قبض', 'سند دفع')
WHERE inv.`pro_tybe` = 11
  AND inv.`info` LIKE '%مردود مبيعات%'
  AND IFNULL(inv.`isdeleted`, 0) = 0
  AND v.`pro_tybe` = 2
  AND IFNULL(v.`isdeleted`, 0) = 0
  AND IFNULL(v.`op2`, 0) > 0
  AND IFNULL(j.`isdeleted`, 0) = 0
  AND IFNULL(j.`details`, '') LIKE '%سند قبض%';
