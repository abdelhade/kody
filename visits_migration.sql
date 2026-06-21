-- Add visits columns to existing visits table
ALTER TABLE `visits`
  ADD COLUMN IF NOT EXISTS `gender`      ENUM('male','female')                       NOT NULL AFTER `id`,
  ADD COLUMN IF NOT EXISTS `age_group`   ENUM('under18','18_25','25_40','over40')    NOT NULL AFTER `gender`,
  ADD COLUMN IF NOT EXISTS `mode`        ENUM('solo','group')                        NOT NULL AFTER `age_group`,
  ADD COLUMN IF NOT EXISTS `start_time`  TIME                                        NOT NULL AFTER `mode`,
  ADD COLUMN IF NOT EXISTS `order_value` ENUM('under60','over60')                    NOT NULL AFTER `start_time`,
  ADD COLUMN IF NOT EXISTS `type`        ENUM('new','returning','regular')           NOT NULL AFTER `order_value`,
  ADD COLUMN IF NOT EXISTS `created_by`  INT UNSIGNED NOT NULL DEFAULT 0             AFTER `type`,
  ADD COLUMN IF NOT EXISTS `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `created_by`,
  ADD COLUMN IF NOT EXISTS `isdeleted`   TINYINT(1) NOT NULL DEFAULT 0               AFTER `created_at`;
