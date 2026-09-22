-- أعمدة جدول الزيارات
ALTER TABLE `visits` ADD COLUMN `gender` enum('male','female') NOT NULL DEFAULT 'male' AFTER `client`;
ALTER TABLE `visits` ADD COLUMN `age_group` enum('under18','18_25','25_40','over40') NOT NULL DEFAULT 'under18' AFTER `gender`;
ALTER TABLE `visits` ADD COLUMN `mode` enum('solo','group') NOT NULL DEFAULT 'solo' AFTER `age_group`;
ALTER TABLE `visits` ADD COLUMN `start_time` time NOT NULL DEFAULT '00:00:00' AFTER `mode`;
ALTER TABLE `visits` ADD COLUMN `end_time` time NOT NULL DEFAULT '00:00:00' AFTER `start_time`;
ALTER TABLE `visits` ADD COLUMN `order_value` enum('under60','over60') NOT NULL DEFAULT 'under60' AFTER `end_time`;
ALTER TABLE `visits` ADD COLUMN `type` enum('new','returning','regular') NOT NULL DEFAULT 'new' AFTER `order_value`;
ALTER TABLE `visits` ADD COLUMN `created_by` int(10) unsigned NOT NULL DEFAULT 0 AFTER `type`;
ALTER TABLE `visits` ADD COLUMN `created_at` datetime NOT NULL DEFAULT current_timestamp() AFTER `created_by`;
