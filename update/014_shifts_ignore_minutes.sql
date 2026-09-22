-- تجاهل دقائق التبكير والتأخير في الورديات
ALTER TABLE `shifts`
  ADD COLUMN `ignore_early_in` TINYINT(1) DEFAULT 0 AFTER `earlylimit`;

ALTER TABLE `shifts`
  ADD COLUMN `ignore_late_out` TINYINT(1) DEFAULT 0 AFTER `ignore_early_in`;
