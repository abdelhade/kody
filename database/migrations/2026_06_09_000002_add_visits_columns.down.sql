-- Rollback visits columns
ALTER TABLE `visits`
  DROP COLUMN IF EXISTS `gender`,
  DROP COLUMN IF EXISTS `age_group`,
  DROP COLUMN IF EXISTS `mode`,
  DROP COLUMN IF EXISTS `start_time`,
  DROP COLUMN IF EXISTS `order_value`,
  DROP COLUMN IF EXISTS `type`,
  DROP COLUMN IF EXISTS `created_by`,
  DROP COLUMN IF EXISTS `created_at`,
  DROP COLUMN IF EXISTS `isdeleted`;
