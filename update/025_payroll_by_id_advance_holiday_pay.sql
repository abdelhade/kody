-- الرواتب: ربط الإنتاجية والاحتسابات برقم الموظف، السلف القصيرة، وأجر الإجازات المدفوعة

ALTER TABLE `productions`
  ADD COLUMN `emp_id` INT(11) DEFAULT NULL AFTER `date`;

ALTER TABLE `productions`
  ADD KEY `emp_date` (`emp_id`, `date`);

UPDATE `productions` p
  JOIN `employees` e ON e.`name` = p.`emp_name`
  SET p.`emp_id` = e.`id`
  WHERE p.`emp_id` IS NULL OR p.`emp_id` = 0;

UPDATE `payroll_calcs` pc
  JOIN `employees` e ON e.`name` = pc.`emp_name`
  SET pc.`emp_id` = e.`id`
  WHERE pc.`emp_id` IS NULL OR pc.`emp_id` = 0;

ALTER TABLE `payroll_calcs`
  MODIFY `calc_tybe` TINYINT(4) NOT NULL DEFAULT 1 COMMENT '1=مكافأة 2=تأمين 3=ضريبة 4=خصم 5=سلفة قصيرة';

ALTER TABLE `attdocs`
  ADD COLUMN `holiday_pay` DOUBLE NOT NULL DEFAULT 0 COMMENT 'أجر الإجازات المدفوعة (راتب شهري)' AFTER `entitle`;

ALTER TABLE `attdocs`
  ADD COLUMN `advance` DOUBLE NOT NULL DEFAULT 0 COMMENT 'سلف قصيرة' AFTER `deduction`;

CREATE TABLE IF NOT EXISTS `entitles` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `tybe` VARCHAR(50) NOT NULL,
  `info` VARCHAR(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
