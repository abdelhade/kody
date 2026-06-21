-- Payroll calculations (bonus, insurance, tax, deduction) + attdocs summary columns

CREATE TABLE IF NOT EXISTS `payroll_calcs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `snd_id` int(11) NOT NULL,
  `calc_tybe` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1=bonus 2=insurance 3=tax 4=deduction',
  `date` date NOT NULL,
  `emp_id` int(11) DEFAULT NULL,
  `emp_name` varchar(100) DEFAULT NULL,
  `amount` double NOT NULL DEFAULT 0,
  `percent` double NOT NULL DEFAULT 0,
  `info` varchar(250) DEFAULT NULL,
  `info2` varchar(250) DEFAULT NULL,
  `user` varchar(50) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `snd_id` (`snd_id`),
  KEY `emp_date` (`emp_id`, `date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `attdocs`
  ADD COLUMN IF NOT EXISTS `bonus` double NOT NULL DEFAULT 0 AFTER `entitle`,
  ADD COLUMN IF NOT EXISTS `insurance` double NOT NULL DEFAULT 0 AFTER `bonus`,
  ADD COLUMN IF NOT EXISTS `tax` double NOT NULL DEFAULT 0 AFTER `insurance`,
  ADD COLUMN IF NOT EXISTS `deduction` double NOT NULL DEFAULT 0 AFTER `tax`,
  ADD COLUMN IF NOT EXISTS `net_pay` double NOT NULL DEFAULT 0 AFTER `deduction`;
