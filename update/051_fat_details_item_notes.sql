-- ملاحظة على مستوى سطر الصنف (كاشير / فواتير)
ALTER TABLE `fat_details`
  ADD COLUMN `notes` VARCHAR(255) DEFAULT NULL COMMENT 'ملاحظة على مستوى الصنف' AFTER `profit`;
