-- تعامل نقدي فقط: العميل/المورد لا يقبل الآجل في الفواتير
ALTER TABLE `acc_head`
  ADD COLUMN `cash_only` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'تعامل نقدي فقط - لا يقبل الآجل';

UPDATE `acc_head` SET `cash_only` = 1 WHERE `code` = '1221';
