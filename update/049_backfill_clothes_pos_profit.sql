-- إعادة حساب ربح بنود كاشير الملابس (بيع 9 / مردود 11) اللي اتحفظت بـ profit = 0
-- ثم تجميعها على ot_head.profit

-- بيع: (qty_out × (price − cost)) − discount
UPDATE `fat_details` fd
INNER JOIN `ot_head` h ON h.`id` = fd.`fatid`
SET fd.`profit` = (fd.`qty_out` * (fd.`price` - IFNULL(fd.`cost_price`, 0))) - IFNULL(fd.`discount`, 0)
WHERE h.`pro_tybe` = 9
  AND IFNULL(h.`isdeleted`, 0) = 0
  AND IFNULL(fd.`isdeleted`, 0) = 0
  AND fd.`qty_out` > 0
  AND IFNULL(fd.`profit`, 0) = 0
  AND IFNULL(fd.`cost_price`, 0) > 0;

-- مردود مبيعات: عكس ربح البيع
UPDATE `fat_details` fd
INNER JOIN `ot_head` h ON h.`id` = fd.`fatid`
SET fd.`profit` = -((fd.`qty_in` * (fd.`price` - IFNULL(fd.`cost_price`, 0))) - IFNULL(fd.`discount`, 0))
WHERE h.`pro_tybe` = 11
  AND IFNULL(h.`isdeleted`, 0) = 0
  AND IFNULL(fd.`isdeleted`, 0) = 0
  AND fd.`qty_in` > 0
  AND IFNULL(fd.`profit`, 0) = 0
  AND IFNULL(fd.`cost_price`, 0) > 0
  AND (h.`info` LIKE '%مردود مبيعات%' OR h.`info` LIKE '%نوع الطلب: مردود%');

-- تجميع ربح الرأس من البنود
UPDATE `ot_head` h
INNER JOIN (
    SELECT `fatid`, SUM(IFNULL(`profit`, 0)) AS `tprofit`
    FROM `fat_details`
    WHERE IFNULL(`isdeleted`, 0) = 0
    GROUP BY `fatid`
) d ON d.`fatid` = h.`id`
SET h.`profit` = d.`tprofit`
WHERE h.`pro_tybe` IN (9, 11)
  AND IFNULL(h.`isdeleted`, 0) = 0;
