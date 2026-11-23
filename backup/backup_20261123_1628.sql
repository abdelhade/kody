DROP TABLE IF EXISTS acc_groups;

CREATE TABLE `acc_groups` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `aname` varchar(40) NOT NULL,
  `acc_type` int(1) NOT NULL,
  `parent_id` int(1) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `code` varchar(30) DEFAULT NULL,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `aname` (`aname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE IF EXISTS acc_head;

CREATE TABLE `acc_head` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL,
  `deletable` int(11) DEFAULT 1,
  `editable` int(1) NOT NULL DEFAULT 1,
  `aname` varchar(50) NOT NULL,
  `phone` varchar(200) DEFAULT NULL,
  `address` varchar(200) DEFAULT NULL,
  `e_mail` varchar(100) DEFAULT NULL,
  `constant` int(11) NOT NULL DEFAULT 0,
  `is_stock` int(1) NOT NULL DEFAULT 0,
  `is_fund` int(11) DEFAULT 0,
  `rentable` int(11) DEFAULT NULL,
  `parent_id` int(1) NOT NULL,
  `nature` int(1) DEFAULT NULL,
  `kind` int(1) DEFAULT NULL,
  `is_basic` int(1) NOT NULL,
  `start_balance` decimal(3,0) NOT NULL DEFAULT 0,
  `credit` decimal(3,0) NOT NULL DEFAULT 0,
  `debit` decimal(3,0) NOT NULL DEFAULT 0,
  `balance` decimal(12,3) NOT NULL DEFAULT 0.000,
  `secret` int(1) NOT NULL DEFAULT 0,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `info` varchar(250) DEFAULT NULL,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  `cash_only` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'تعامل نقدي فقط - لا يقبل الآجل',
  `price_list` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  UNIQUE KEY `aname` (`aname`)
) ENGINE=InnoDB AUTO_INCREMENT=276 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO acc_head VALUES ("1","1","0","0","الاصول",NULL,NULL,NULL,"0","0","0",NULL,"0","1","1","1","0","0","0","0.000","0","2023-11-25 02:55:49","2024-11-16 22:16:48",NULL,"0","0","0","0","1");
INSERT INTO acc_head VALUES ("2","2","0","0","الخصوم",NULL,NULL,NULL,"0","0","0",NULL,"0","1","1","1","0","0","0","0.000","0","2023-11-26 04:10:59","2024-11-16 22:16:50",NULL,"0","0","0","0","1");
INSERT INTO acc_head VALUES ("3","22","0","0","حقوق الملكية",NULL,NULL,NULL,"0","0","0",NULL,"2","1","1","1","0","0","0","0.000","0","2023-11-26 04:14:04","2024-11-16 22:16:53",NULL,"0","0","0","0","1");
INSERT INTO acc_head VALUES ("4","3","0","0","الايرادات",NULL,NULL,NULL,"0","0","0",NULL,"0","1","2","1","0","0","0","0.000","0","2023-11-26 04:14:29","2024-06-11 05:16:29",NULL,"0","0","0","0","1");
INSERT INTO acc_head VALUES ("5","4","0","0","المصروفات",NULL,NULL,NULL,"0","0","0",NULL,"0","1","2","1","0","0","0","0.000","0","2023-11-26 04:14:59","2024-06-11 05:16:35",NULL,"0","0","0","0","1");
INSERT INTO acc_head VALUES ("6","11","0","0","الاصول الثابته",NULL,NULL,NULL,"0","0","0",NULL,"1","1","1","1","0","0","0","0.000","0","2023-11-26 04:38:23","2024-06-11 05:16:38",NULL,"0","0","0","0","1");
INSERT INTO acc_head VALUES ("7","12","0","0","الاصول المتداوله",NULL,NULL,NULL,"0","0","0",NULL,"1",NULL,"1","1","0","0","0","0.000","0","2023-11-26 04:45:08","2024-06-11 05:16:42",NULL,"0","0","0","0","1");
INSERT INTO acc_head VALUES ("8","21","0","0","الخصوم المتداولة",NULL,NULL,NULL,"0","0","0",NULL,"2",NULL,"1","1","0","0","0","0.000","0","2023-11-26 04:45:47","2024-06-11 05:16:46",NULL,"0","0","0","0","1");
INSERT INTO acc_head VALUES ("9","221","0","0","الشركاء",NULL,NULL,NULL,"0","0","0",NULL,"3",NULL,"1","1","0","0","0","0.000","0","2023-11-26 04:46:20","2024-06-11 05:16:52",NULL,"0","0","0","0","1");
INSERT INTO acc_head VALUES ("10","222","0","0","ارباح غير موزعة",NULL,NULL,NULL,"0","0","0",NULL,"3",NULL,"1","1","0","0","0","0.000","0","2023-11-26 04:47:03","2024-11-16 22:23:26",NULL,"0","0","0","0","1");
INSERT INTO acc_head VALUES ("11","223","0","1","ارباح غير موزعة لفترات سابقة",NULL,NULL,NULL,"0","0","0",NULL,"3",NULL,"1","0","0","0","0","0.000","0","2023-11-26 04:47:50","2025-10-28 11:51:19",NULL,"0","0","0","0","1");
INSERT INTO acc_head VALUES ("13","31","0","0","ايرادات المبيعات",NULL,NULL,NULL,"0","0","0",NULL,"4",NULL,"2","1","0","0","0","0.000","0","2023-11-26 21:37:49","2024-11-16 22:23:31",NULL,"0","0","0","0","1");
INSERT INTO acc_head VALUES ("14","32","0","0","ايرادات غير تشغيليه",NULL,NULL,NULL,"0","0","0",NULL,"4",NULL,"2","1","0","0","0","0.000","0","2023-11-26 21:38:15","2024-11-16 22:23:33",NULL,"0","0","0","0","1");
INSERT INTO acc_head VALUES ("15","41","0","0","تكاليف المبيعات",NULL,NULL,NULL,"0","0","0","0","5",NULL,"2","1","0","0","0","0.000","0","2023-11-26 21:39:10","2024-11-16 22:23:36",NULL,"0","0","0","0","1");
INSERT INTO acc_head VALUES ("16","42","0","0","تكلفه البضاعه المباعه",NULL,NULL,NULL,"0","0","0",NULL,"5",NULL,"2","1","0","0","0","0.000","0","2023-11-26 21:39:49","2024-06-11 05:17:12",NULL,"0","0","0","0","1");
INSERT INTO acc_head VALUES ("17","43","0","0","رواتب و اجور",NULL,NULL,NULL,"0","0","0",NULL,"5",NULL,"2","1","0","0","0","0.000","0","2023-11-26 21:40:07","2024-06-11 05:17:16",NULL,"0","0","0","0","1");
INSERT INTO acc_head VALUES ("18","121","0","0","الصناديق",NULL,NULL,NULL,"0","0","0",NULL,"7",NULL,"1","1","0","0","0","0.000","0","2023-12-08 12:50:49","2024-06-11 05:17:18",NULL,"0","0","0","0","1");
INSERT INTO acc_head VALUES ("19","122","0","0","العملاء",NULL,NULL,NULL,"0","0","0",NULL,"7",NULL,"1","1","0","0","0","0.000","0","2023-12-08 12:52:13","2024-06-11 05:17:22",NULL,"0","0","0","0","1");
INSERT INTO acc_head VALUES ("20","123","0","0","المخزون",NULL,NULL,NULL,"0","0","0",NULL,"7",NULL,"1","1","0","0","0","0.000","0","2023-12-08 12:52:51","2024-06-11 05:17:27",NULL,"0","0","0","0","1");
INSERT INTO acc_head VALUES ("21","1211","0","1","الصندوق الافتراضي",NULL,NULL,NULL,"0","0","1",NULL,"18",NULL,"1","0","0","0","0","8060.000","0","2023-12-09 10:46:52","2026-11-23 15:59:43",NULL,"0","0","0","0","1");
INSERT INTO acc_head VALUES ("24","1221","0","0","العميل النقدي",NULL,NULL,NULL,"0","0","0","0","19",NULL,"1","0","0","0","0","0.000","0","2023-12-28 01:25:46","2026-11-18 16:00:23",NULL,"0","0","0","1","1");
INSERT INTO acc_head VALUES ("27","123001","0","0","المخزن الرئيسي",NULL,NULL,NULL,"0","1","0","0","20",NULL,"1","0","0","0","0","-726.000","0","2023-12-28 03:35:35","2026-11-23 15:59:43",NULL,"0","0","0","0","1");
INSERT INTO acc_head VALUES ("29","2211","0","0","الشريك الرئيسي",NULL,NULL,NULL,"0","0","0",NULL,"9",NULL,"1","0","0","0","0","0.000","0","2023-12-30 02:12:22","2026-11-19 14:15:37",NULL,"0","0","0","0","1");
INSERT INTO acc_head VALUES ("33","211","0","0","الموردين",NULL,NULL,NULL,"0","0","0",NULL,"8",NULL,"1","1","0","0","0","0.000","0","2024-01-22 05:41:26","2024-06-11 05:17:55",NULL,"0","0","0","0","1");
INSERT INTO acc_head VALUES ("34","212","0","1","الدائنين الاخرين",NULL,NULL,NULL,"0","0","0",NULL,"8",NULL,"1","1","0","0","0","0.000","0","2024-01-22 05:42:08","2026-11-18 16:00:23",NULL,"0","0","0","0","1");
INSERT INTO acc_head VALUES ("35","213","0","0","الموظفين",NULL,NULL,NULL,"0","0","0",NULL,"8",NULL,"1","1","0","0","0","0.000","0","2024-01-22 05:42:29","2024-06-11 05:18:01",NULL,"0","0","0","0","1");
INSERT INTO acc_head VALUES ("36","2111","0","0","المورد الافتراضي",NULL,NULL,NULL,"0","0","0",NULL,"33",NULL,"1","0","0","0","0","0.000","0","2024-01-23 06:17:26","2026-11-18 16:00:23",NULL,"0","0","0","0","1");
INSERT INTO acc_head VALUES ("37","124","0","0","البنوك",NULL,NULL,NULL,"0","0","0",NULL,"7",NULL,"1","1","0","0","0","0.000","0","2024-01-23 06:22:23","2024-06-11 05:18:07",NULL,"0","0","0","0","1");
INSERT INTO acc_head VALUES ("38","125","0","0","مدينين آخرين",NULL,NULL,NULL,"0","0","0",NULL,"7",NULL,"1","1","0","0","0","0.000","0","2024-01-23 06:30:11","2024-06-11 05:18:13",NULL,"0","0","0","0","1");
INSERT INTO acc_head VALUES ("39","1241","0","1","البنك الافتراضي",NULL,NULL,NULL,"0","0","0",NULL,"37",NULL,"1","0","0","0","0","111.000","0","2024-01-23 06:32:21","2026-11-19 14:15:37",NULL,"0","0","0","0","1");
INSERT INTO acc_head VALUES ("40","44","0","0","مصروفات عامه ",NULL,NULL,NULL,"0","0","0",NULL,"5",NULL,"2","1","0","0","0","0.000","0","2024-01-23 06:34:08","2024-06-11 05:18:24",NULL,"0","0","0","0","1");
INSERT INTO acc_head VALUES ("55","112","0","0","اصول قابله للتأجير",NULL,NULL,NULL,"0","0","0","0","6",NULL,"1","1","0","0","0","0.000","0","2024-02-20 05:16:57","2024-06-11 05:18:30",NULL,"0","0","0","0","1");
INSERT INTO acc_head VALUES ("86","411","0","0","صافي المشتريات",NULL,NULL,NULL,"0","0","0","0","15",NULL,"2","1","0","0","0","0.000","0","2024-03-08 00:56:24","2024-06-11 05:18:46",NULL,"0","0","0","0","1");
INSERT INTO acc_head VALUES ("89","4111","0","0","المشتربات",NULL,NULL,NULL,"0","0","0","0","86",NULL,"2","1","0","0","0","0.000","0","2024-03-08 21:37:09","2024-06-11 05:18:53",NULL,"0","0","0","0","1");
INSERT INTO acc_head VALUES ("90","4112","0","0","مردود المشتريات",NULL,NULL,NULL,"0","0","0","0","86",NULL,"2","1","0","0","0","0.000","0","2024-03-08 21:38:25","2024-06-11 05:18:56",NULL,"0","0","0","0","1");
INSERT INTO acc_head VALUES ("91","41103","0","1","خصم مسموح به",NULL,NULL,NULL,"0","0","0","0","86",NULL,"2","0","0","0","0","0.000","0","2024-03-08 21:43:40","2026-11-18 16:00:23",NULL,"0","0","0","0","1");
INSERT INTO acc_head VALUES ("92","311","0","0","صافي المبيعات",NULL,NULL,NULL,"0","0","0","0","13",NULL,"2","1","0","0","0","0.000","0","2024-03-08 21:48:15","2024-06-11 05:19:02",NULL,"0","0","0","0","1");
INSERT INTO acc_head VALUES ("93","3111","0","0","المبيعات",NULL,NULL,NULL,"0","0","0","0","92",NULL,"2","1","0","0","0","0.000","0","2024-03-08 21:49:07","2024-06-11 05:19:05",NULL,"0","0","0","0","1");
INSERT INTO acc_head VALUES ("94","3112","0","0","مردود المبيعات",NULL,NULL,NULL,"0","0","0","0","92",NULL,"2","1","0","0","0","0.000","0","2024-03-08 21:50:03","2024-06-11 05:19:07",NULL,"0","0","0","0","1");
INSERT INTO acc_head VALUES ("95","3113","0","0","خصومات تشغيل",NULL,NULL,NULL,"0","0","0","0","92",NULL,"2","1","0","0","0","0.000","0","2024-03-08 21:54:56","2024-06-11 05:19:10",NULL,"0","0","0","0","1");
INSERT INTO acc_head VALUES ("97","31131","0","1","خصم مكتسب",NULL,NULL,NULL,"0","0","0","0","95",NULL,"2","0","0","0","0","0.000","0","2024-03-10 00:38:24","2026-11-19 14:15:37",NULL,"0","0","0","0","1");
INSERT INTO acc_head VALUES ("98","321","0","0","ايرادات من تأجير أصول",NULL,NULL,NULL,"0","0","0","0","14",NULL,"2","1","0","0","0","0.000","0","2024-03-14 14:58:05","2024-06-11 05:19:27",NULL,"0","0","0","0","1");
INSERT INTO acc_head VALUES ("99","32101","0","0","ايرادات من التأجير",NULL,NULL,NULL,"0","0","0","0","98",NULL,"2","0","0","0","0","0.000","0","2024-03-14 15:01:19","2026-11-21 15:29:52",NULL,"0","0","0","0","1");
INSERT INTO acc_head VALUES ("131","213001","0","1","الموظف 1",NULL,NULL,NULL,"0","0","0","0","35",NULL,"1","0","0","0","0","0.000","0","2024-06-18 09:41:56","2026-11-18 16:00:23",NULL,"0","0","0","0","1");
INSERT INTO acc_head VALUES ("148","122001","0","0","العميل الافتراضي","","",NULL,"0","0","0","0","19",NULL,"1","0","0","0","0","11.000","0","2024-06-23 01:26:46","2026-11-23 15:59:43",NULL,"0","0","0","0","1");
INSERT INTO acc_head VALUES ("274","42001","0","1","تكلفة البضاعة المباعة",NULL,NULL,NULL,"0","0","0",NULL,"16",NULL,"2","0","0","0","0","726.000","0","2026-11-11 16:56:59","2026-11-23 15:59:43",NULL,"0","0","0","0","1");
INSERT INTO acc_head VALUES ("275","311101","0","1","مبيعات التشغيل",NULL,NULL,NULL,"0","0","0",NULL,"93",NULL,"2","0","0","0","0","-8182.000","0","2026-11-16 12:20:22","2026-11-23 15:59:43",NULL,"0","0","0","0","1");



DROP TABLE IF EXISTS allowances;

CREATE TABLE `allowances` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `info` varchar(250) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `tybe` int(1) NOT NULL,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE IF EXISTS analisys;

CREATE TABLE `analisys` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client` int(11) NOT NULL,
  `lap` int(11) NOT NULL,
  `name` varchar(250) DEFAULT NULL,
  `comment` varchar(250) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `img` varchar(250) DEFAULT NULL,
  `info` varchar(250) DEFAULT NULL,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE IF EXISTS attandance;

CREATE TABLE `attandance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee` int(11) NOT NULL,
  `fptybe` int(1) NOT NULL,
  `fpdate` date NOT NULL DEFAULT current_timestamp(),
  `time` time NOT NULL DEFAULT current_timestamp(),
  `user` int(1) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `isdeleted` tinyint(1) NOT NULL DEFAULT 0,
  `fromwhere` varchar(10) DEFAULT NULL,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  `detected_shift_id` int(11) DEFAULT NULL COMMENT 'الوردية المستنتجة عند تسجيل البصمة',
  PRIMARY KEY (`id`),
  KEY `employee` (`employee`),
  CONSTRAINT `attandance_ibfk_1` FOREIGN KEY (`employee`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=758 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO attandance VALUES ("757","138","1","2026-02-03","10:00:00","1","2026-02-03 14:27:07","1",NULL,"0","0",NULL);



DROP TABLE IF EXISTS attdocs;

CREATE TABLE `attdocs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `empid` int(11) NOT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `fromdate` date DEFAULT NULL,
  `todate` date NOT NULL,
  `alldays` double NOT NULL,
  `workdays` double NOT NULL,
  `exphours` double NOT NULL,
  `accualhours` double NOT NULL,
  `attdays` int(11) NOT NULL,
  `absdays` int(11) NOT NULL,
  `holidays` int(11) NOT NULL,
  `earlyminits` double NOT NULL,
  `entitle` double NOT NULL DEFAULT 0,
  `holiday_pay` double NOT NULL DEFAULT 0 COMMENT 'أجر الإجازات المدفوعة (راتب شهري)',
  `bonus` double NOT NULL DEFAULT 0,
  `insurance` double NOT NULL DEFAULT 0,
  `tax` double NOT NULL DEFAULT 0,
  `deduction` double NOT NULL DEFAULT 0,
  `advance` double NOT NULL DEFAULT 0 COMMENT 'سلف قصيرة',
  `net_pay` double NOT NULL DEFAULT 0,
  `info` varchar(250) DEFAULT NULL,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=139 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO attdocs VALUES ("138","138","2026-02-03 14:47:33","2026-02-03 14:47:33","2007-02-22","2026-12-12","7234","6200","37200","3","1","1","1034","0","0.57","0","0","0","0","0","0","0"," احتساب الرواتب من يوم 2007-02-22 الي يوم 2026-12-12 بنظام الاستحقاق بالساعات فقط","0","0","0");



DROP TABLE IF EXISTS attlog;

CREATE TABLE `attlog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee` int(1) NOT NULL,
  `day` date NOT NULL,
  `starttime` time DEFAULT NULL,
  `endtime` time DEFAULT NULL,
  `fpin` time DEFAULT NULL,
  `fpout` time DEFAULT NULL,
  `defhours` double DEFAULT NULL,
  `curhours` double DEFAULT NULL,
  `dueforhour` double DEFAULT NULL,
  `realdue` double DEFAULT NULL,
  `statue` int(1) NOT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `info` int(11) DEFAULT NULL,
  `attdoc` int(1) DEFAULT NULL,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  `shift_id` int(11) DEFAULT NULL COMMENT 'الوردية المستنتجة أو المطبقة لليوم',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE IF EXISTS barcodes;

CREATE TABLE `barcodes` (
  `id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `barcode` varchar(25) NOT NULL,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `barcode` (`barcode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE IF EXISTS book_tybes;

CREATE TABLE `book_tybes` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `name` varchar(25) NOT NULL,
  `value` double DEFAULT NULL,
  `qty` int(1) NOT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE IF EXISTS booking_cards;

CREATE TABLE `booking_cards` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client` varchar(100) NOT NULL,
  `barcode` varchar(50) NOT NULL,
  `rtybe` varchar(20) NOT NULL,
  `rcost` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `remain` int(11) NOT NULL,
  `bcase` int(11) NOT NULL,
  `fromdate` date NOT NULL,
  `todate` date NOT NULL,
  `crtime` datetime NOT NULL DEFAULT current_timestamp(),
  `isdeleted` int(1) NOT NULL DEFAULT 0,
  `user` int(1) NOT NULL DEFAULT 0,
  `bransh` int(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE IF EXISTS calls;

CREATE TABLE `calls` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject` varchar(100) NOT NULL,
  `call_type` int(1) NOT NULL DEFAULT 1,
  `call_date` date NOT NULL DEFAULT current_timestamp(),
  `call_time` time NOT NULL DEFAULT current_timestamp(),
  `duration` varchar(100) DEFAULT NULL,
  `client_id` int(11) NOT NULL DEFAULT 1,
  `emp_comment` varchar(250) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `next_date` date NOT NULL DEFAULT current_timestamp(),
  `next_time` time NOT NULL DEFAULT current_timestamp(),
  `mod_comment` varchar(250) DEFAULT NULL,
  `mod_rate` int(1) NOT NULL DEFAULT 5,
  `user_id` int(11) NOT NULL DEFAULT 1,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE IF EXISTS cases;

CREATE TABLE `cases` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `cname` varchar(50) NOT NULL,
  `info` varchar(100) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE IF EXISTS chances;

CREATE TABLE `chances` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `client` varchar(50) DEFAULT NULL,
  `cname` varchar(50) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `cdate` date DEFAULT NULL,
  `important` int(1) DEFAULT NULL,
  `expected` double NOT NULL DEFAULT 0,
  `tybe` int(1) NOT NULL DEFAULT 1,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE IF EXISTS chances_tybes;

CREATE TABLE `chances_tybes` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `cname` varchar(50) NOT NULL,
  `info` varchar(50) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cname` (`cname`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO chances_tybes VALUES ("1","جديد",NULL,"2023-11-28 03:20:13","0","0","0");
INSERT INTO chances_tybes VALUES ("2","تم الاتفاق",NULL,"2023-11-28 03:27:21","0","0","0");
INSERT INTO chances_tybes VALUES ("3","دفع عربون",NULL,"2023-11-28 03:27:21","0","0","0");
INSERT INTO chances_tybes VALUES ("4","صفقه تامه",NULL,"2023-11-28 03:27:42","0","0","0");



DROP TABLE IF EXISTS cities;

CREATE TABLE `cities` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `cname` varchar(150) NOT NULL,
  `info` varchar(150) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user` int(1) NOT NULL DEFAULT 1,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cname` (`cname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE IF EXISTS clients;

CREATE TABLE `clients` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `phone2` varchar(150) DEFAULT NULL,
  `address` varchar(250) DEFAULT NULL,
  `address2` varchar(150) DEFAULT NULL,
  `address3` varchar(150) DEFAULT NULL,
  `city` int(11) DEFAULT NULL,
  `height` double DEFAULT NULL,
  `weight` double DEFAULT NULL,
  `dateofbirth` date DEFAULT NULL,
  `ref` varchar(20) DEFAULT NULL,
  `diseses` varchar(200) DEFAULT NULL,
  `info` varchar(200) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `imgs` varchar(250) DEFAULT NULL,
  `jop` varchar(50) DEFAULT NULL,
  `gender` int(1) DEFAULT NULL,
  `drugs` varchar(250) DEFAULT NULL,
  `seriousdes` varchar(250) DEFAULT NULL,
  `familydes` varchar(250) DEFAULT NULL,
  `allergy` varchar(250) DEFAULT NULL,
  `temp` varchar(9) DEFAULT NULL,
  `pressure` varchar(9) DEFAULT NULL,
  `diabetes` varchar(9) DEFAULT NULL,
  `brate` varchar(9) DEFAULT NULL,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO clients VALUES ("10","محمد علي","",NULL,"",NULL,NULL,"0","0","0",NULL,"","","","2026-11-23 14:48:50","2026-11-23 14:48:50",NULL,NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"0","0","0");



DROP TABLE IF EXISTS closed_orders;

CREATE TABLE `closed_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shift` varchar(30) NOT NULL DEFAULT '',
  `user` varchar(50) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `strttime` datetime DEFAULT NULL,
  `endtime` time DEFAULT NULL,
  `total_sales` double NOT NULL DEFAULT 0,
  `delevery` double NOT NULL DEFAULT 0,
  `tables` double NOT NULL DEFAULT 0,
  `takeaway` double NOT NULL DEFAULT 0,
  `expenses` double NOT NULL DEFAULT 0,
  `fund_before` double NOT NULL DEFAULT 0,
  `fund_after` double NOT NULL DEFAULT 0,
  `exp_notes` varchar(30) DEFAULT NULL,
  `cash` double NOT NULL DEFAULT 0,
  `info` varchar(255) DEFAULT NULL,
  `crtime` datetime NOT NULL DEFAULT current_timestamp(),
  `mdtime` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `info2` varchar(20) DEFAULT NULL,
  `tenant` int(11) NOT NULL DEFAULT 1,
  `branch` int(11) NOT NULL DEFAULT 1,
  `total_cash` decimal(10,2) DEFAULT 0.00,
  `total_visa` decimal(10,2) DEFAULT 0.00,
  `total_discount` decimal(10,2) DEFAULT 0.00,
  `total_returns` decimal(10,2) DEFAULT 0.00,
  `start_cash` decimal(10,2) DEFAULT 0.00,
  `actual_cash` decimal(10,2) DEFAULT 0.00,
  `actual_visa` decimal(10,2) DEFAULT 0.00,
  `deficit` decimal(10,2) DEFAULT 0.00,
  `status` tinyint(1) DEFAULT 1,
  `json_details` text DEFAULT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0 COMMENT 'رقم الكاشير',
  `opened_at` datetime DEFAULT NULL COMMENT 'أول حركة في الشيفت',
  `closed_at` datetime DEFAULT NULL COMMENT 'وقت التقفيل',
  `orders_count` int(11) NOT NULL DEFAULT 0 COMMENT 'عدد فواتير البيع',
  `total_receipts` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'سندات قبض مستقلة',
  `expected_total` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'إجمالي المتوقع في الخزائن',
  `counted_total` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'إجمالي المعدود',
  PRIMARY KEY (`id`),
  KEY `idx_closed_orders_user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO closed_orders VALUES ("1","1","admin","2026-11-14","2026-11-14 21:22:30","21:23:20","66","0","0","0","0","0","0",NULL,"66","","2026-11-14 21:23:20","2026-11-14 21:23:20",NULL,"1","1","66.00","0.00","0.00","0.00","0.00","66.00","0.00","0.00","1",NULL,"1","2026-11-14 21:22:30","2026-11-14 21:23:20","2","0.00","66.00","66.00");
INSERT INTO closed_orders VALUES ("2","2","admin","2026-11-22","2026-11-14 21:23:43","19:52:54","7804","0","0","0","0","0","0",NULL,"0","","2026-11-22 19:52:54","2026-11-22 19:52:54",NULL,"1","1","7399.00","111.00","0.00","283.00","0.00","0.00","0.00","-7510.00","1",NULL,"1","2026-11-14 21:23:43","2026-11-22 19:52:54","100","0.00","7510.00","0.00");



DROP TABLE IF EXISTS cost_centers;

CREATE TABLE `cost_centers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cname` varchar(100) NOT NULL,
  `info` varchar(200) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cname` (`cname`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO cost_centers VALUES ("1","المركز الافتراضي",NULL,"2024-01-19 03:17:02","2024-01-19 03:17:02","0","0","0");



DROP TABLE IF EXISTS criminals;

CREATE TABLE `criminals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cname` varchar(200) NOT NULL,
  `nickname` varchar(200) NOT NULL,
  `dateofbirth` date DEFAULT NULL,
  `jop` varchar(200) NOT NULL,
  `station` varchar(111) DEFAULT NULL,
  `mname` varchar(200) NOT NULL,
  `crmaddress` varchar(200) NOT NULL,
  `idcardnum` varchar(200) NOT NULL,
  `scar` int(11) NOT NULL,
  `otherdetails` varchar(200) NOT NULL,
  `prtnrs` varchar(200) NOT NULL,
  `crmstyle` int(11) DEFAULT NULL,
  `dngrs` int(11) DEFAULT NULL,
  `fesh` int(11) DEFAULT NULL,
  `karta` int(11) DEFAULT NULL,
  `entry` int(11) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NULL DEFAULT current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE IF EXISTS crm_style;

CREATE TABLE `crm_style` (
  `id` int(11) NOT NULL,
  `cname` varchar(200) NOT NULL,
  `info` varchar(200) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NULL DEFAULT current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE IF EXISTS ctp;

CREATE TABLE `ctp` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `cname` varchar(50) NOT NULL,
  `type` varchar(100) DEFAULT NULL,
  `number` varchar(100) DEFAULT NULL,
  `info` varchar(100) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE IF EXISTS cvs;

CREATE TABLE `cvs` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `userid` int(1) NOT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `name` varchar(50) NOT NULL,
  `degree` varchar(50) DEFAULT NULL,
  `address` varchar(100) DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(50) NOT NULL,
  `skills` text DEFAULT NULL,
  `exp1` varchar(250) DEFAULT NULL,
  `exp2` varchar(250) DEFAULT NULL,
  `exp3` varchar(250) DEFAULT NULL,
  `lastsalary` varchar(50) NOT NULL,
  `expsalary` varchar(50) NOT NULL DEFAULT '0',
  `referances` text DEFAULT NULL,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`,`email`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO cvs VALUES ("1","1","2026-02-03 15:15:52","2026-02-03 15:15:52","e","rfvgg","NO. 342 - London Oxford Street, 012 United Kingdom","2026-02-03","12345678912","e@e.com","reresvv","efwe","wfwsf","sdvdvd","","","","0","0","0");



DROP TABLE IF EXISTS delivery_clients;

CREATE TABLE `delivery_clients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_name` varchar(255) NOT NULL COMMENT 'Customer name',
  `phone` varchar(20) NOT NULL COMMENT 'Phone number',
  `address` text NOT NULL COMMENT 'Address',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Created date',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Updated date',
  `isdeleted` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Is deleted',
  PRIMARY KEY (`id`),
  UNIQUE KEY `phone` (`phone`),
  KEY `isdeleted` (`isdeleted`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Delivery clients table';




DROP TABLE IF EXISTS departments;

CREATE TABLE `departments` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `info` varchar(250) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO departments VALUES ("8","IT","Information technology 
","2026-01-28 13:58:58","0","0","0");



DROP TABLE IF EXISTS drugs;

CREATE TABLE `drugs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(40) NOT NULL,
  `purpose` varchar(200) DEFAULT NULL,
  `effectivematerial` varchar(200) DEFAULT NULL,
  `sideeffects` text DEFAULT NULL,
  `info` varchar(250) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user` int(11) NOT NULL DEFAULT 1,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE IF EXISTS emp_allowences;

CREATE TABLE `emp_allowences` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `empid` int(1) NOT NULL,
  `allowid` int(1) NOT NULL,
  `value` double NOT NULL,
  `info` int(1) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE IF EXISTS emp_kbis;

CREATE TABLE `emp_kbis` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `emp_id` int(1) NOT NULL,
  `kbi_id` int(1) DEFAULT NULL,
  `kbi_weight` decimal(10,2) DEFAULT NULL,
  `kbi_rate` decimal(10,2) DEFAULT NULL,
  `kbi_sum` decimal(10,2) DEFAULT NULL,
  `user` int(11) DEFAULT 1,
  `crtime` datetime DEFAULT current_timestamp(),
  `mdtime` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `isdeleted` int(1) DEFAULT 0,
  `tenant` int(1) DEFAULT 0,
  `branch` int(1) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE IF EXISTS emplog;

CREATE TABLE `emplog` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `employee` int(1) NOT NULL,
  `date` date NOT NULL,
  `chkin` time DEFAULT NULL,
  `chkout` time DEFAULT NULL,
  `addin` time DEFAULT NULL,
  `addout` time DEFAULT NULL,
  `latecost` double DEFAULT NULL,
  `earlcost` double DEFAULT NULL,
  `absent` int(11) DEFAULT NULL,
  `holiday` int(11) DEFAULT NULL,
  `deducation` double DEFAULT NULL,
  `additional` double DEFAULT NULL,
  `user` int(11) NOT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE IF EXISTS employee_operations;

CREATE TABLE `employee_operations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `operation_id` int(11) NOT NULL,
  `status` varchar(50) DEFAULT 'assigned',
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `completed_at` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `operation_id` (`operation_id`),
  CONSTRAINT `employee_operations_ibfk_1` FOREIGN KEY (`operation_id`) REFERENCES `hr_operations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE IF EXISTS employees;

CREATE TABLE `employees` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `basma_id` int(11) DEFAULT NULL,
  `basma_name` varchar(50) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `info` varchar(250) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `imgs` varchar(250) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `number` varchar(13) DEFAULT NULL,
  `active` int(1) NOT NULL DEFAULT 1,
  `dateofbirth` date DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `country` int(1) DEFAULT NULL,
  `address` varchar(250) DEFAULT NULL,
  `address2` varchar(250) DEFAULT NULL,
  `town` int(1) DEFAULT NULL,
  `jop` int(1) DEFAULT NULL,
  `department` int(1) DEFAULT NULL,
  `joptybe` int(1) DEFAULT NULL,
  `joplevel` int(1) DEFAULT NULL,
  `dateofhire` date DEFAULT NULL,
  `dateofend` date DEFAULT NULL,
  `shift` int(1) DEFAULT NULL,
  `vacancy` int(1) DEFAULT NULL,
  `holiday` int(1) DEFAULT NULL,
  `salary` decimal(11,2) DEFAULT NULL,
  `calc_type` varchar(20) NOT NULL DEFAULT 'monthly',
  `password` varchar(250) DEFAULT NULL,
  `education` varchar(100) DEFAULT NULL,
  `skills` varchar(200) DEFAULT NULL,
  `hour_extra` decimal(10,2) NOT NULL DEFAULT 0.00,
  `day_extra` decimal(10,2) NOT NULL DEFAULT 0.00,
  `ent_tybe` int(11) NOT NULL DEFAULT 1,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  `shift_group_id` int(11) DEFAULT NULL COMMENT 'مجموعة الورديات للتعرف التلقائي الديناميكي',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=139 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO employees VALUES ("138","1","nour","نور ابراهيم","dd","2026-01-28 14:51:44",NULL,"nour.ibrahim.dev@gmail.com","0101111111111","1","1997-09-28","0",NULL,"","","4","10","8","0","6","2026-01-15","2026-03-31","12",NULL,NULL,"7000.00","monthly","1234",NULL,NULL,"1.50","1.50","1","0","0","0",NULL);



DROP TABLE IF EXISTS entitles;

CREATE TABLE `entitles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tybe` varchar(50) NOT NULL,
  `info` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO entitles VALUES ("1","بالساعات فقط","احتساب الراتب بناءً على الساعات الفعلية فقط");
INSERT INTO entitles VALUES ("2","ساعات عمل وإضافي يومي","احتساب ساعات العمل بالإضافة إلى الإضافي اليومي");
INSERT INTO entitles VALUES ("3","ساعات عمل وإضافي خلال الفترة","احتساب ساعات العمل بالإضافة إلى الإضافي التراكمي خلال الفترة");
INSERT INTO entitles VALUES ("4","بناءً على الحضور","احتساب الراتب بناءً على عدد أيام الحضور");
INSERT INTO entitles VALUES ("5","بالإنتاجية","احتساب الراتب بناءً على إجمالي الإنتاجية");



DROP TABLE IF EXISTS extras;

CREATE TABLE `extras` (
  `id` int(11) NOT NULL,
  `empid` int(1) NOT NULL,
  `info` varchar(100) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `val` double NOT NULL,
  `tybe` int(1) NOT NULL,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE IF EXISTS fat_details;

CREATE TABLE `fat_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pro_tybe` int(11) DEFAULT NULL,
  `det_store` int(11) DEFAULT 1,
  `pro_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT 0,
  `u_val` decimal(10,3) NOT NULL DEFAULT 1.000,
  `qty_in` double DEFAULT 0,
  `qty_out` double DEFAULT 0,
  `doc_qty` double DEFAULT NULL,
  `price` double DEFAULT 0,
  `cost_price` double(12,2) DEFAULT NULL,
  `stock_value` double(12,2) DEFAULT NULL,
  `discount` double DEFAULT NULL,
  `disc_pct` decimal(10,2) DEFAULT 0.00,
  `plus` double DEFAULT NULL,
  `det_value` double DEFAULT 0,
  `profit` float NOT NULL DEFAULT 0,
  `notes` varchar(255) DEFAULT NULL COMMENT 'ملاحظة على مستوى الصنف',
  `fatid` int(11) DEFAULT NULL,
  `fat_tybe` int(11) NOT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`),
  CONSTRAINT `fat_details_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `myitems` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=261 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO fat_details VALUES ("1","9","27","1","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11",NULL,"1","9","2026-11-14 21:22:30","0","0","0");
INSERT INTO fat_details VALUES ("2","9","27","1","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22",NULL,"1","9","2026-11-14 21:22:30","0","0","0");
INSERT INTO fat_details VALUES ("3","9","27","3","1","1.000","0","3","3","11","0.00",NULL,"0","0.00",NULL,"33","33",NULL,"3","9","2026-11-14 21:22:53","0","0","0");
INSERT INTO fat_details VALUES ("4","9","27","5","1","1.000","0","12","12","11","0.00",NULL,"0","0.00",NULL,"132","132",NULL,"5","9","2026-11-14 21:23:44","0","0","0");
INSERT INTO fat_details VALUES ("5","9","27","7","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11",NULL,"7","9","2026-11-14 22:48:12","0","0","0");
INSERT INTO fat_details VALUES ("6","9","27","7","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22",NULL,"7","9","2026-11-14 22:48:12","0","0","0");
INSERT INTO fat_details VALUES ("7","9","27","9","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11",NULL,"9","9","2026-11-14 22:54:20","0","0","0");
INSERT INTO fat_details VALUES ("8","9","27","11","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11",NULL,"11","9","2026-11-14 23:33:14","0","0","0");
INSERT INTO fat_details VALUES ("9","9","27","11","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22",NULL,"11","9","2026-11-14 23:33:14","0","0","0");
INSERT INTO fat_details VALUES ("10","9","27","13","1","1.000","0","2","2","11","0.00",NULL,"0","0.00",NULL,"22","22",NULL,"13","9","2026-11-16 11:54:26","0","0","0");
INSERT INTO fat_details VALUES ("11","9","27","13","2","1.000","0","2","2","22","0.00",NULL,"0","0.00",NULL,"44","44",NULL,"13","9","2026-11-16 11:54:26","0","0","0");
INSERT INTO fat_details VALUES ("12","9","27","16","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11",NULL,"16","9","2026-11-16 12:20:30","0","0","0");
INSERT INTO fat_details VALUES ("13","9","27","16","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22",NULL,"16","9","2026-11-16 12:20:30","0","0","0");
INSERT INTO fat_details VALUES ("14","9","27","18","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11",NULL,"18","9","2026-11-16 12:20:58","0","0","0");
INSERT INTO fat_details VALUES ("15","9","27","18","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22",NULL,"18","9","2026-11-16 12:20:58","0","0","0");
INSERT INTO fat_details VALUES ("16","9","27","20","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11",NULL,"20","9","2026-11-16 12:25:30","0","0","0");
INSERT INTO fat_details VALUES ("17","9","27","20","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22",NULL,"20","9","2026-11-16 12:25:30","0","0","0");
INSERT INTO fat_details VALUES ("18","9","27","22","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11",NULL,"22","9","2026-11-16 12:27:19","0","0","0");
INSERT INTO fat_details VALUES ("19","9","27","22","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22",NULL,"22","9","2026-11-16 12:27:19","0","0","0");
INSERT INTO fat_details VALUES ("20","9","27","24","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11",NULL,"24","9","2026-11-16 12:29:22","0","0","0");
INSERT INTO fat_details VALUES ("21","9","27","24","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22",NULL,"24","9","2026-11-16 12:29:22","0","0","0");
INSERT INTO fat_details VALUES ("22","9","27","26","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11",NULL,"26","9","2026-11-16 12:29:35","0","0","0");
INSERT INTO fat_details VALUES ("23","9","27","26","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22",NULL,"26","9","2026-11-16 12:29:35","0","0","0");
INSERT INTO fat_details VALUES ("24","9","27","28","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11",NULL,"28","9","2026-11-16 12:30:46","0","0","0");
INSERT INTO fat_details VALUES ("25","9","27","28","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22",NULL,"28","9","2026-11-16 12:30:46","0","0","0");
INSERT INTO fat_details VALUES ("26","9","27","30","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11",NULL,"30","9","2026-11-16 12:31:21","0","0","0");
INSERT INTO fat_details VALUES ("27","9","27","30","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22",NULL,"30","9","2026-11-16 12:31:21","0","0","0");
INSERT INTO fat_details VALUES ("28","9","27","30","5","1.000","0","1","1","50","0.00",NULL,"0","0.00",NULL,"50","50",NULL,"30","9","2026-11-16 12:31:21","0","0","0");
INSERT INTO fat_details VALUES ("29","9","27","30","4","1.000","0","1","1","20","0.00",NULL,"0","0.00",NULL,"20","20",NULL,"30","9","2026-11-16 12:31:21","0","0","0");
INSERT INTO fat_details VALUES ("30","9","27","32","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11",NULL,"32","9","2026-11-16 12:35:34","0","0","0");
INSERT INTO fat_details VALUES ("31","9","27","32","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22",NULL,"32","9","2026-11-16 12:35:34","0","0","0");
INSERT INTO fat_details VALUES ("32","9","27","34","5","1.000","0","1","1","50","0.00",NULL,"0","0.00",NULL,"50","50",NULL,"34","9","2026-11-16 12:53:40","0","0","0");
INSERT INTO fat_details VALUES ("33","9","27","37","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11",NULL,"37","9","2026-11-16 12:54:41","0","0","0");
INSERT INTO fat_details VALUES ("34","9","27","37","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22",NULL,"37","9","2026-11-16 12:54:41","0","0","0");
INSERT INTO fat_details VALUES ("35","9","27","39","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11",NULL,"39","9","2026-11-16 12:59:37","0","0","0");
INSERT INTO fat_details VALUES ("36","9","27","39","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22",NULL,"39","9","2026-11-16 12:59:37","0","0","0");
INSERT INTO fat_details VALUES ("37","9","27","41","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11",NULL,"41","9","2026-11-16 12:59:58","0","0","0");
INSERT INTO fat_details VALUES ("38","9","27","41","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22",NULL,"41","9","2026-11-16 12:59:58","0","0","0");
INSERT INTO fat_details VALUES ("39","9","27","43","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11",NULL,"43","9","2026-11-16 13:00:28","0","0","0");
INSERT INTO fat_details VALUES ("40","9","27","43","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22",NULL,"43","9","2026-11-16 13:00:28","0","0","0");
INSERT INTO fat_details VALUES ("41","9","27","45","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11",NULL,"45","9","2026-11-16 13:37:59","0","0","0");
INSERT INTO fat_details VALUES ("42","9","27","45","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22",NULL,"45","9","2026-11-16 13:37:59","0","0","0");
INSERT INTO fat_details VALUES ("43","9","27","47","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11",NULL,"47","9","2026-11-16 13:40:22","0","0","0");
INSERT INTO fat_details VALUES ("44","9","27","47","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22",NULL,"47","9","2026-11-16 13:40:22","0","0","0");
INSERT INTO fat_details VALUES ("45","9","27","49","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11",NULL,"49","9","2026-11-16 13:50:55","0","0","0");
INSERT INTO fat_details VALUES ("46","9","27","49","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22",NULL,"49","9","2026-11-16 13:50:55","0","0","0");
INSERT INTO fat_details VALUES ("47","9","27","51","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11",NULL,"51","9","2026-11-16 13:51:27","0","0","0");
INSERT INTO fat_details VALUES ("48","9","27","51","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22",NULL,"51","9","2026-11-16 13:51:27","0","0","0");
INSERT INTO fat_details VALUES ("49","9","27","53","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11",NULL,"53","9","2026-11-16 13:52:05","0","0","0");
INSERT INTO fat_details VALUES ("50","9","27","53","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22",NULL,"53","9","2026-11-16 13:52:05","0","0","0");
INSERT INTO fat_details VALUES ("51","9","27","55","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22",NULL,"55","9","2026-11-16 13:56:26","0","0","0");
INSERT INTO fat_details VALUES ("52","9","27","57","5","1.000","0","1","1","50","0.00",NULL,"0","0.00",NULL,"50","50",NULL,"57","9","2026-11-16 13:58:12","0","0","0");
INSERT INTO fat_details VALUES ("53","9","27","57","4","1.000","0","1","1","20","0.00",NULL,"0","0.00",NULL,"20","20",NULL,"57","9","2026-11-16 13:58:12","0","0","0");
INSERT INTO fat_details VALUES ("54","9","27","59","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11",NULL,"59","9","2026-11-16 14:00:21","0","0","0");
INSERT INTO fat_details VALUES ("55","9","27","59","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22",NULL,"59","9","2026-11-16 14:00:21","0","0","0");
INSERT INTO fat_details VALUES ("56","9","27","61","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11",NULL,"61","9","2026-11-16 14:00:46","0","0","0");
INSERT INTO fat_details VALUES ("57","9","27","61","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22",NULL,"61","9","2026-11-16 14:00:46","0","0","0");
INSERT INTO fat_details VALUES ("58","9","27","63","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22",NULL,"63","9","2026-11-16 14:02:35","0","0","0");
INSERT INTO fat_details VALUES ("59","9","27","65","1","1.000","0","2","2","11","0.00",NULL,"0","0.00",NULL,"22","22",NULL,"65","9","2026-11-16 14:03:15","0","0","0");
INSERT INTO fat_details VALUES ("60","9","27","65","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22",NULL,"65","9","2026-11-16 14:03:15","0","0","0");
INSERT INTO fat_details VALUES ("61","9","27","67","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11",NULL,"67","9","2026-11-16 14:10:43","0","0","0");
INSERT INTO fat_details VALUES ("62","9","27","67","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22",NULL,"67","9","2026-11-16 14:10:43","0","0","0");
INSERT INTO fat_details VALUES ("63","9","27","69","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11",NULL,"69","9","2026-11-16 14:14:20","0","0","0");
INSERT INTO fat_details VALUES ("64","9","27","69","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22",NULL,"69","9","2026-11-16 14:14:20","0","0","0");
INSERT INTO fat_details VALUES ("65","9","27","71","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11",NULL,"71","9","2026-11-16 14:19:32","0","0","0");
INSERT INTO fat_details VALUES ("66","9","27","71","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22",NULL,"71","9","2026-11-16 14:19:32","0","0","0");
INSERT INTO fat_details VALUES ("67","9","27","73","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11",NULL,"73","9","2026-11-16 14:19:45","0","0","0");
INSERT INTO fat_details VALUES ("68","9","27","73","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22",NULL,"73","9","2026-11-16 14:19:45","0","0","0");
INSERT INTO fat_details VALUES ("69","9","27","75","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11",NULL,"75","9","2026-11-16 14:20:41","0","0","0");
INSERT INTO fat_details VALUES ("70","9","27","75","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22",NULL,"75","9","2026-11-16 14:20:41","0","0","0");
INSERT INTO fat_details VALUES ("71","9","27","77","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11",NULL,"77","9","2026-11-16 14:25:02","0","0","0");
INSERT INTO fat_details VALUES ("72","9","27","77","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22",NULL,"77","9","2026-11-16 14:25:02","0","0","0");
INSERT INTO fat_details VALUES ("73","9","27","79","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11",NULL,"79","9","2026-11-16 14:25:18","0","0","0");
INSERT INTO fat_details VALUES ("74","9","27","79","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22",NULL,"79","9","2026-11-16 14:25:18","0","0","0");
INSERT INTO fat_details VALUES ("75","9","27","81","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11",NULL,"81","9","2026-11-16 14:32:27","0","0","0");
INSERT INTO fat_details VALUES ("76","9","27","81","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22",NULL,"81","9","2026-11-16 14:32:27","0","0","0");
INSERT INTO fat_details VALUES ("77","9","27","83","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11",NULL,"83","9","2026-11-16 14:32:50","0","0","0");
INSERT INTO fat_details VALUES ("78","9","27","83","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22",NULL,"83","9","2026-11-16 14:32:50","0","0","0");
INSERT INTO fat_details VALUES ("79","9","27","85","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11",NULL,"85","9","2026-11-16 14:35:41","0","0","0");
INSERT INTO fat_details VALUES ("80","9","27","85","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22",NULL,"85","9","2026-11-16 14:35:41","0","0","0");
INSERT INTO fat_details VALUES ("81","9","27","87","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11",NULL,"87","9","2026-11-16 14:37:04","0","0","0");
INSERT INTO fat_details VALUES ("82","9","27","87","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22",NULL,"87","9","2026-11-16 14:37:04","0","0","0");
INSERT INTO fat_details VALUES ("83","9","27","89","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11",NULL,"89","9","2026-11-16 14:41:12","0","0","0");
INSERT INTO fat_details VALUES ("84","9","27","89","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22",NULL,"89","9","2026-11-16 14:41:12","0","0","0");
INSERT INTO fat_details VALUES ("85","9","27","91","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11",NULL,"91","9","2026-11-16 14:41:34","0","0","0");
INSERT INTO fat_details VALUES ("86","9","27","91","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22",NULL,"91","9","2026-11-16 14:41:34","0","0","0");
INSERT INTO fat_details VALUES ("87","9","27","93","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11",NULL,"93","9","2026-11-16 14:41:38","0","0","0");
INSERT INTO fat_details VALUES ("88","9","27","93","4","1.000","0","1","1","20","0.00",NULL,"0","0.00",NULL,"20","20",NULL,"93","9","2026-11-16 14:41:38","0","0","0");
INSERT INTO fat_details VALUES ("89","9","27","93","5","1.000","0","1","1","50","0.00",NULL,"0","0.00",NULL,"50","50",NULL,"93","9","2026-11-16 14:41:38","0","0","0");
INSERT INTO fat_details VALUES ("90","9","27","95","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11",NULL,"95","9","2026-11-16 14:42:25","0","0","0");
INSERT INTO fat_details VALUES ("91","9","27","95","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22",NULL,"95","9","2026-11-16 14:42:25","0","0","0");
INSERT INTO fat_details VALUES ("92","9","27","97","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11",NULL,"97","9","2026-11-16 14:42:34","0","0","0");
INSERT INTO fat_details VALUES ("93","9","27","97","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22",NULL,"97","9","2026-11-16 14:42:34","0","0","0");
INSERT INTO fat_details VALUES ("94","9","27","97","4","1.000","0","1","1","20","0.00",NULL,"0","0.00",NULL,"20","20",NULL,"97","9","2026-11-16 14:42:34","0","0","0");
INSERT INTO fat_details VALUES ("95","9","27","99","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11",NULL,"99","9","2026-11-16 14:43:00","0","0","0");
INSERT INTO fat_details VALUES ("96","9","27","99","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22",NULL,"99","9","2026-11-16 14:43:00","0","0","0");
INSERT INTO fat_details VALUES ("97","9","27","99","4","1.000","0","1","1","20","0.00",NULL,"0","0.00",NULL,"20","20",NULL,"99","9","2026-11-16 14:43:00","0","0","0");
INSERT INTO fat_details VALUES ("98","9","27","101","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11",NULL,"101","9","2026-11-16 14:43:46","0","0","0");
INSERT INTO fat_details VALUES ("99","9","27","101","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22",NULL,"101","9","2026-11-16 14:43:46","0","0","0");
INSERT INTO fat_details VALUES ("100","9","27","103","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11",NULL,"103","9","2026-11-16 14:45:32","0","0","0");
INSERT INTO fat_details VALUES ("101","9","27","103","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22",NULL,"103","9","2026-11-16 14:45:32","0","0","0");
INSERT INTO fat_details VALUES ("102","9","27","105","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11",NULL,"105","9","2026-11-16 14:46:18","0","0","0");
INSERT INTO fat_details VALUES ("103","9","27","105","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22",NULL,"105","9","2026-11-16 14:46:18","0","0","0");
INSERT INTO fat_details VALUES ("104","9","27","107","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11",NULL,"107","9","2026-11-16 14:54:50","0","0","0");
INSERT INTO fat_details VALUES ("105","9","27","107","4","1.000","0","1","1","20","0.00",NULL,"0","0.00",NULL,"20","20",NULL,"107","9","2026-11-16 14:54:50","0","0","0");
INSERT INTO fat_details VALUES ("106","9","27","109","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22",NULL,"109","9","2026-11-16 14:55:15","0","0","0");
INSERT INTO fat_details VALUES ("107","9","27","109","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11",NULL,"109","9","2026-11-16 14:55:15","0","0","0");
INSERT INTO fat_details VALUES ("108","9","27","111","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11",NULL,"111","9","2026-11-16 14:55:58","0","0","0");
INSERT INTO fat_details VALUES ("109","9","27","111","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22",NULL,"111","9","2026-11-16 14:55:58","0","0","0");
INSERT INTO fat_details VALUES ("110","9","27","113","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11",NULL,"113","9","2026-11-16 14:57:30","0","0","0");
INSERT INTO fat_details VALUES ("111","9","27","113","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22",NULL,"113","9","2026-11-16 14:57:30","0","0","0");
INSERT INTO fat_details VALUES ("112","9","27","115","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11",NULL,"115","9","2026-11-16 14:58:10","0","0","0");
INSERT INTO fat_details VALUES ("113","9","27","115","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22",NULL,"115","9","2026-11-16 14:58:10","0","0","0");
INSERT INTO fat_details VALUES ("114","9","27","117","4","1.000","0","1","1","20","0.00",NULL,"0","0.00",NULL,"20","20",NULL,"117","9","2026-11-16 14:58:26","0","0","0");
INSERT INTO fat_details VALUES ("115","9","27","117","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22",NULL,"117","9","2026-11-16 14:58:26","0","0","0");
INSERT INTO fat_details VALUES ("116","9","27","119","5","1.000","0","1","1","50","0.00",NULL,"0","0.00",NULL,"50","50",NULL,"119","9","2026-11-16 14:59:05","0","0","0");
INSERT INTO fat_details VALUES ("117","9","27","121","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11",NULL,"121","9","2026-11-16 15:07:25","0","0","0");
INSERT INTO fat_details VALUES ("118","9","27","121","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22",NULL,"121","9","2026-11-16 15:07:25","0","0","0");
INSERT INTO fat_details VALUES ("119","9","27","123","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11",NULL,"123","9","2026-11-16 15:07:47","0","0","0");
INSERT INTO fat_details VALUES ("120","9","27","123","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22",NULL,"123","9","2026-11-16 15:07:47","0","0","0");
INSERT INTO fat_details VALUES ("121","9","27","125","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11",NULL,"125","9","2026-11-16 15:08:15","0","0","0");
INSERT INTO fat_details VALUES ("122","9","27","125","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22",NULL,"125","9","2026-11-16 15:08:15","0","0","0");
INSERT INTO fat_details VALUES ("123","9","27","127","1","1.000","0","2","2","11","0.00",NULL,"0","0.00",NULL,"22","22",NULL,"127","9","2026-11-16 15:11:38","0","0","0");
INSERT INTO fat_details VALUES ("124","9","27","127","2","1.000","0","2","2","22","0.00",NULL,"0","0.00",NULL,"44","44",NULL,"127","9","2026-11-16 15:11:38","0","0","0");
INSERT INTO fat_details VALUES ("125","9","27","127","4","1.000","0","1","1","20","0.00",NULL,"0","0.00",NULL,"20","20",NULL,"127","9","2026-11-16 15:11:38","0","0","0");
INSERT INTO fat_details VALUES ("126","9","27","129","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11",NULL,"129","9","2026-11-16 15:11:45","0","0","0");
INSERT INTO fat_details VALUES ("127","9","27","129","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22",NULL,"129","9","2026-11-16 15:11:45","0","0","0");
INSERT INTO fat_details VALUES ("128","9","27","131","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22",NULL,"131","9","2026-11-16 15:11:50","0","0","0");
INSERT INTO fat_details VALUES ("129","9","27","131","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11",NULL,"131","9","2026-11-16 15:11:50","0","0","0");
INSERT INTO fat_details VALUES ("130","9","27","133","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11",NULL,"133","9","2026-11-16 15:21:52","0","0","0");
INSERT INTO fat_details VALUES ("131","9","27","133","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22",NULL,"133","9","2026-11-16 15:21:52","0","0","0");
INSERT INTO fat_details VALUES ("132","9","27","135","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11",NULL,"135","9","2026-11-16 15:32:46","0","0","0");
INSERT INTO fat_details VALUES ("133","9","27","135","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22",NULL,"135","9","2026-11-16 15:32:46","0","0","0");
INSERT INTO fat_details VALUES ("134","9","27","137","5","1.000","0","1","1","50","0.00",NULL,"0","0.00",NULL,"50","50",NULL,"137","9","2026-11-16 15:33:57","0","0","0");
INSERT INTO fat_details VALUES ("135","9","27","139","5","1.000","0","2","2","50","0.00",NULL,"0","0.00",NULL,"100","100",NULL,"139","9","2026-11-16 15:35:10","0","0","0");
INSERT INTO fat_details VALUES ("136","9","27","139","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11",NULL,"139","9","2026-11-16 15:35:10","0","0","0");
INSERT INTO fat_details VALUES ("137","9","27","139","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22",NULL,"139","9","2026-11-16 15:35:10","0","0","0");
INSERT INTO fat_details VALUES ("138","9","27","141","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11",NULL,"141","9","2026-11-16 15:35:25","0","0","0");
INSERT INTO fat_details VALUES ("139","9","27","143","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11",NULL,"143","9","2026-11-16 15:36:04","0","0","0");
INSERT INTO fat_details VALUES ("140","9","27","143","5","1.000","0","1","1","50","0.00",NULL,"0","0.00",NULL,"50","50",NULL,"143","9","2026-11-16 15:36:04","0","0","0");
INSERT INTO fat_details VALUES ("141","9","27","145","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11",NULL,"145","9","2026-11-16 15:45:41","0","0","0");
INSERT INTO fat_details VALUES ("142","9","27","145","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22",NULL,"145","9","2026-11-16 15:45:41","0","0","0");
INSERT INTO fat_details VALUES ("143","9","27","147","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11",NULL,"147","9","2026-11-16 21:04:41","0","0","0");
INSERT INTO fat_details VALUES ("144","9","27","147","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22",NULL,"147","9","2026-11-16 21:04:41","0","0","0");
INSERT INTO fat_details VALUES ("145","9","27","149","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11",NULL,"149","9","2026-11-16 21:05:44","0","0","0");
INSERT INTO fat_details VALUES ("146","9","27","149","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22",NULL,"149","9","2026-11-16 21:05:44","0","0","0");
INSERT INTO fat_details VALUES ("147","9","27","151","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11",NULL,"151","9","2026-11-16 21:07:00","0","0","0");
INSERT INTO fat_details VALUES ("148","9","27","151","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22",NULL,"151","9","2026-11-16 21:07:00","0","0","0");
INSERT INTO fat_details VALUES ("149","9","27","151","5","1.000","0","1","1","50","0.00",NULL,"0","0.00",NULL,"50","50",NULL,"151","9","2026-11-16 21:07:00","0","0","0");
INSERT INTO fat_details VALUES ("150","9","27","151","4","1.000","0","1","1","20","0.00",NULL,"0","0.00",NULL,"20","20",NULL,"151","9","2026-11-16 21:07:00","0","0","0");
INSERT INTO fat_details VALUES ("151","9","27","153","5","1.000","0","1","1","50","0.00",NULL,"0","0.00",NULL,"50","50",NULL,"153","9","2026-11-16 21:15:13","0","0","0");
INSERT INTO fat_details VALUES ("152","9","27","153","4","1.000","0","1","1","20","0.00",NULL,"0","0.00",NULL,"20","20",NULL,"153","9","2026-11-16 21:15:13","0","0","0");
INSERT INTO fat_details VALUES ("153","9","27","153","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11",NULL,"153","9","2026-11-16 21:15:13","0","0","0");
INSERT INTO fat_details VALUES ("154","9","27","155","4","1.000","0","1","1","20","0.00",NULL,"0","0.00",NULL,"20","20",NULL,"155","9","2026-11-16 21:15:17","0","0","0");
INSERT INTO fat_details VALUES ("155","9","27","155","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11",NULL,"155","9","2026-11-16 21:15:17","0","0","0");
INSERT INTO fat_details VALUES ("156","9","27","157","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11",NULL,"157","9","2026-11-18 15:39:22","0","0","0");
INSERT INTO fat_details VALUES ("157","9","27","157","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22",NULL,"157","9","2026-11-18 15:39:22","0","0","0");
INSERT INTO fat_details VALUES ("158","9","27","159","6","1.000","0","1",NULL,"20","0.00",NULL,"0","0.00",NULL,"20","20",NULL,"159","9","2026-11-18 15:40:03","0","0","0");
INSERT INTO fat_details VALUES ("159","9","27","159","5","1.000","0","1",NULL,"50","0.00",NULL,"0","0.00",NULL,"50","50",NULL,"159","9","2026-11-18 15:40:03","0","0","0");
INSERT INTO fat_details VALUES ("160","9","27","161","6","1.000","0","1",NULL,"20","0.00",NULL,"0","0.00",NULL,"20","20",NULL,"161","9","2026-11-18 15:40:13","0","0","0");
INSERT INTO fat_details VALUES ("161","9","27","161","5","1.000","0","1",NULL,"50","0.00",NULL,"0","0.00",NULL,"50","50",NULL,"161","9","2026-11-18 15:40:13","0","0","0");
INSERT INTO fat_details VALUES ("162","9","27","163","5","1.000","0","1",NULL,"50","0.00",NULL,"0","0.00",NULL,"50","50",NULL,"163","9","2026-11-18 15:40:23","0","0","0");
INSERT INTO fat_details VALUES ("163","9","27","163","4","1.000","0","1",NULL,"20","0.00",NULL,"0","0.00",NULL,"20","20",NULL,"163","9","2026-11-18 15:40:23","0","0","0");
INSERT INTO fat_details VALUES ("164","9","27","165","6","1.000","0","1",NULL,"20","0.00",NULL,"0","0.00",NULL,"20","20",NULL,"165","9","2026-11-18 15:40:46","0","0","0");
INSERT INTO fat_details VALUES ("165","9","27","165","5","1.000","0","1",NULL,"50","0.00",NULL,"0","0.00",NULL,"50","50",NULL,"165","9","2026-11-18 15:40:46","0","0","0");
INSERT INTO fat_details VALUES ("166","9","27","167","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11",NULL,"167","9","2026-11-18 16:04:31","0","0","0");
INSERT INTO fat_details VALUES ("167","9","27","167","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22",NULL,"167","9","2026-11-18 16:04:31","0","0","0");
INSERT INTO fat_details VALUES ("168","9","27","169","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22",NULL,"169","9","2026-11-18 16:05:32","0","0","0");
INSERT INTO fat_details VALUES ("169","9","27","169","4","1.000","0","1","1","20","0.00",NULL,"0","0.00",NULL,"20","20",NULL,"169","9","2026-11-18 16:05:32","0","0","0");
INSERT INTO fat_details VALUES ("170","9","27","171","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11",NULL,"171","9","2026-11-18 16:06:12","0","0","0");
INSERT INTO fat_details VALUES ("171","9","27","171","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22",NULL,"171","9","2026-11-18 16:06:12","0","0","0");
INSERT INTO fat_details VALUES ("172","9","27","173","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11",NULL,"173","9","2026-11-21 12:50:35","0","0","0");
INSERT INTO fat_details VALUES ("173","9","27","173","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22",NULL,"173","9","2026-11-21 12:50:35","0","0","0");
INSERT INTO fat_details VALUES ("174","9","27","175","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11",NULL,"175","9","2026-11-21 12:50:38","0","0","0");
INSERT INTO fat_details VALUES ("175","9","27","175","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22",NULL,"175","9","2026-11-21 12:50:38","0","0","0");
INSERT INTO fat_details VALUES ("176","9","27","175","4","1.000","0","1","1","20","0.00",NULL,"0","0.00",NULL,"20","20",NULL,"175","9","2026-11-21 12:50:38","0","0","0");
INSERT INTO fat_details VALUES ("177","9","27","177","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11",NULL,"177","9","2026-11-21 12:50:43","0","0","0");
INSERT INTO fat_details VALUES ("178","9","27","177","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22",NULL,"177","9","2026-11-21 12:50:43","0","0","0");
INSERT INTO fat_details VALUES ("179","9","27","177","4","1.000","0","1","1","20","0.00",NULL,"0","0.00",NULL,"20","20",NULL,"177","9","2026-11-21 12:50:43","0","0","0");
INSERT INTO fat_details VALUES ("180","9","27","177","5","1.000","0","1","1","50","0.00",NULL,"0","0.00",NULL,"50","50",NULL,"177","9","2026-11-21 12:50:43","0","0","0");
INSERT INTO fat_details VALUES ("181","9","27","179","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11",NULL,"179","9","2026-11-21 12:50:48","0","0","0");
INSERT INTO fat_details VALUES ("182","9","27","179","2","1.000","0","2","2","22","0.00",NULL,"0","0.00",NULL,"44","44",NULL,"179","9","2026-11-21 12:50:48","0","0","0");
INSERT INTO fat_details VALUES ("183","9","27","181","6","1.000","0","1",NULL,"20","0.00",NULL,"0","0.00",NULL,"20","20",NULL,"181","9","2026-11-21 12:52:58","0","0","0");
INSERT INTO fat_details VALUES ("184","9","27","181","5","1.000","0","1",NULL,"50","0.00",NULL,"0","0.00",NULL,"50","50",NULL,"181","9","2026-11-21 12:52:58","0","0","0");
INSERT INTO fat_details VALUES ("185","9","27","181","4","1.000","0","1",NULL,"20","0.00",NULL,"0","0.00",NULL,"20","20",NULL,"181","9","2026-11-21 12:52:58","0","0","0");
INSERT INTO fat_details VALUES ("186","11","27","183","6","1.000","1","0",NULL,"20","0.00",NULL,"0","0.00",NULL,"20","-20",NULL,"183","11","2026-11-21 12:53:05","0","0","0");
INSERT INTO fat_details VALUES ("187","11","27","183","5","1.000","1","0",NULL,"50","0.00",NULL,"0","0.00",NULL,"50","-50",NULL,"183","11","2026-11-21 12:53:05","0","0","0");
INSERT INTO fat_details VALUES ("188","11","27","183","4","1.000","1","0",NULL,"20","0.00",NULL,"0","0.00",NULL,"20","-20",NULL,"183","11","2026-11-21 12:53:05","0","0","0");
INSERT INTO fat_details VALUES ("189","23","27",NULL,"1","1.000","103","0","103","11","11.00",NULL,NULL,"0.00",NULL,"1133","0",NULL,NULL,"23","2026-11-21 15:46:52","0","0","0");
INSERT INTO fat_details VALUES ("190","23","27",NULL,"2","1.000","95","0","95","22","22.00",NULL,NULL,"0.00",NULL,"2090","0",NULL,NULL,"23","2026-11-21 15:46:52","0","0","0");
INSERT INTO fat_details VALUES ("191","23","27",NULL,"3","1.000","30","0","30","0","0.00",NULL,NULL,"0.00",NULL,"0","0",NULL,NULL,"23","2026-11-21 15:46:52","0","0","0");
INSERT INTO fat_details VALUES ("192","23","27",NULL,"6","1.000","18","0","18","0","0.00",NULL,NULL,"0.00",NULL,"0","0",NULL,NULL,"23","2026-11-21 15:46:52","0","0","0");
INSERT INTO fat_details VALUES ("193","23","27",NULL,"5","1.000","36","0","36","0","0.00",NULL,NULL,"0.00",NULL,"0","0",NULL,NULL,"23","2026-11-21 15:46:52","0","0","0");
INSERT INTO fat_details VALUES ("194","23","27",NULL,"4","1.000","31","0","31","0","0.00",NULL,NULL,"0.00",NULL,"0","0",NULL,NULL,"23","2026-11-21 15:46:52","0","0","0");
INSERT INTO fat_details VALUES ("195","23","27",NULL,"1","1.000","0","5","5","11","11.00",NULL,NULL,"0.00",NULL,"55","0",NULL,NULL,"23","2026-11-21 15:47:20","0","0","0");
INSERT INTO fat_details VALUES ("196","23","27",NULL,"1","1.000","14995","0","14995","11","11.00",NULL,NULL,"0.00",NULL,"164945","0",NULL,NULL,"23","2026-11-21 15:48:58","0","0","0");
INSERT INTO fat_details VALUES ("197","23","27",NULL,"2","1.000","100","0","100","22","22.00",NULL,NULL,"0.00",NULL,"2200","0",NULL,NULL,"23","2026-11-21 15:48:58","0","0","0");
INSERT INTO fat_details VALUES ("198","9","27","185","6","1.000","0","1",NULL,"20","0.00",NULL,"0","0.00",NULL,"20","20",NULL,"185","9","2026-11-21 15:50:38","0","0","0");
INSERT INTO fat_details VALUES ("199","9","27","185","5","1.000","0","1",NULL,"50","0.00",NULL,"0","0.00",NULL,"50","50",NULL,"185","9","2026-11-21 15:50:38","0","0","0");
INSERT INTO fat_details VALUES ("200","9","27","187","5","1.000","0","6",NULL,"50","0.00",NULL,"0","0.00",NULL,"300","300",NULL,"187","9","2026-11-21 17:13:55","0","0","0");
INSERT INTO fat_details VALUES ("201","9","27","187","4","1.000","0","1",NULL,"20","0.00",NULL,"0","0.00",NULL,"20","20",NULL,"187","9","2026-11-21 17:13:55","0","0","0");
INSERT INTO fat_details VALUES ("202","9","27","187","3","1.000","0","1",NULL,"0","0.00",NULL,"0","0.00",NULL,"0","0",NULL,"187","9","2026-11-21 17:13:55","0","0","0");
INSERT INTO fat_details VALUES ("203","9","27","187","2","1.000","0","1",NULL,"22","0.00",NULL,"0","0.00",NULL,"22","22",NULL,"187","9","2026-11-21 17:13:55","0","0","0");
INSERT INTO fat_details VALUES ("204","9","27","187","1","1.000","0","1",NULL,"11","0.00",NULL,"0","0.00",NULL,"11","11",NULL,"187","9","2026-11-21 17:13:55","0","0","0");
INSERT INTO fat_details VALUES ("205","9","27","189","6","1.000","0","1",NULL,"20","0.00",NULL,"0","0.00",NULL,"20","20",NULL,"189","9","2026-11-21 17:16:41","0","0","0");
INSERT INTO fat_details VALUES ("206","9","27","189","5","1.000","0","1",NULL,"50","0.00",NULL,"0","0.00",NULL,"50","50",NULL,"189","9","2026-11-21 17:16:41","0","0","0");
INSERT INTO fat_details VALUES ("207","9","27","189","4","1.000","0","1",NULL,"20","0.00",NULL,"0","0.00",NULL,"20","20",NULL,"189","9","2026-11-21 17:16:41","0","0","0");
INSERT INTO fat_details VALUES ("208","11","27","191","6","1.000","1","0",NULL,"20","0.00",NULL,"0","0.00",NULL,"20","-20",NULL,"191","11","2026-11-21 17:35:13","0","0","0");
INSERT INTO fat_details VALUES ("209","11","27","191","5","1.000","1","0",NULL,"50","0.00",NULL,"0","0.00",NULL,"50","-50",NULL,"191","11","2026-11-21 17:35:13","0","0","0");
INSERT INTO fat_details VALUES ("210","9","27","193","5","1.000","0","1",NULL,"50","0.00",NULL,"0","0.00",NULL,"50","50",NULL,"193","9","2026-11-21 17:35:57","0","0","0");
INSERT INTO fat_details VALUES ("211","11","27","195","6","1.000","1","0",NULL,"20","0.00",NULL,"0","0.00",NULL,"20","-20",NULL,"195","11","2026-11-21 17:36:10","0","0","0");
INSERT INTO fat_details VALUES ("212","11","27","195","5","1.000","1","0",NULL,"50","0.00",NULL,"0","0.00",NULL,"50","-50",NULL,"195","11","2026-11-21 17:36:10","0","0","0");
INSERT INTO fat_details VALUES ("213","11","27","195","4","1.000","1","0",NULL,"20","0.00",NULL,"0","0.00",NULL,"20","-20",NULL,"195","11","2026-11-21 17:36:10","0","0","0");
INSERT INTO fat_details VALUES ("214","11","27","195","2","1.000","1","0",NULL,"22","0.00",NULL,"0","0.00",NULL,"22","-22",NULL,"195","11","2026-11-21 17:36:10","0","0","0");
INSERT INTO fat_details VALUES ("215","11","27","195","1","1.000","1","0",NULL,"11","0.00",NULL,"0","0.00",NULL,"11","-11",NULL,"195","11","2026-11-21 17:36:10","0","0","0");
INSERT INTO fat_details VALUES ("216","9","27","197","1","1.000","0","3","3","11","0.00",NULL,"0","0.00",NULL,"33","33",NULL,"197","9","2026-11-22 19:12:43","0","0","0");
INSERT INTO fat_details VALUES ("217","9","27","197","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22",NULL,"197","9","2026-11-22 19:12:43","0","0","0");
INSERT INTO fat_details VALUES ("218","9","27","197","6","1.000","0","1","1","20","0.00",NULL,"0","0.00",NULL,"20","20",NULL,"197","9","2026-11-22 19:12:43","0","0","0");
INSERT INTO fat_details VALUES ("219","9","27","197","5","1.000","0","1","1","50","0.00",NULL,"0","0.00",NULL,"50","50",NULL,"197","9","2026-11-22 19:12:43","0","0","0");
INSERT INTO fat_details VALUES ("220","9","27","199","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11",NULL,"199","9","2026-11-22 19:13:04","0","0","0");
INSERT INTO fat_details VALUES ("221","9","27","199","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22",NULL,"199","9","2026-11-22 19:13:04","0","0","0");
INSERT INTO fat_details VALUES ("222","9","27","199","6","1.000","0","1","1","20","0.00",NULL,"0","0.00",NULL,"20","20",NULL,"199","9","2026-11-22 19:13:04","0","0","0");
INSERT INTO fat_details VALUES ("223","9","27","199","5","1.000","0","1","1","50","0.00",NULL,"0","0.00",NULL,"50","50",NULL,"199","9","2026-11-22 19:13:04","0","0","0");
INSERT INTO fat_details VALUES ("224","9","27","203","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11","","203","9","2026-11-22 19:24:59","0","0","0");
INSERT INTO fat_details VALUES ("225","9","27","203","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22","","203","9","2026-11-22 19:24:59","0","0","0");
INSERT INTO fat_details VALUES ("226","9","27","205","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11","","205","9","2026-11-22 19:31:48","0","0","0");
INSERT INTO fat_details VALUES ("227","9","27","205","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22","","205","9","2026-11-22 19:31:48","0","0","0");
INSERT INTO fat_details VALUES ("228","9","27","207","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11","","207","9","2026-11-22 19:34:35","0","0","0");
INSERT INTO fat_details VALUES ("229","9","27","207","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22","","207","9","2026-11-22 19:34:35","0","0","0");
INSERT INTO fat_details VALUES ("230","9","27","209","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11","","209","9","2026-11-22 19:35:12","0","0","0");
INSERT INTO fat_details VALUES ("231","9","27","209","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22","","209","9","2026-11-22 19:35:12","0","0","0");
INSERT INTO fat_details VALUES ("232","9","27","211","1","1.000","0","3","3","11","0.00",NULL,"0","0.00",NULL,"33","33","","211","9","2026-11-22 19:44:00","0","0","0");
INSERT INTO fat_details VALUES ("233","9","27","211","2","1.000","0","3","3","22","0.00",NULL,"0","0.00",NULL,"66","66","","211","9","2026-11-22 19:44:00","0","0","0");
INSERT INTO fat_details VALUES ("234","9","27","211","4","1.000","0","10","10","20","0.00",NULL,"0","0.00",NULL,"200","200","","211","9","2026-11-22 19:44:00","0","0","0");
INSERT INTO fat_details VALUES ("235","9","27","211","5","1.000","0","16","16","50","0.00",NULL,"0","0.00",NULL,"800","800","","211","9","2026-11-22 19:44:00","0","0","0");
INSERT INTO fat_details VALUES ("236","9","27","211","6","1.000","0","11","11","20","0.00",NULL,"0","0.00",NULL,"220","220","","211","9","2026-11-22 19:44:00","0","0","0");
INSERT INTO fat_details VALUES ("237","9","27","211","3","1.000","0","11","11","152","0.00",NULL,"0","0.00",NULL,"1672","1672","","211","9","2026-11-22 19:44:00","0","0","0");
INSERT INTO fat_details VALUES ("238","9","27","213","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11","","213","9","2026-11-22 19:44:30","0","0","0");
INSERT INTO fat_details VALUES ("239","9","27","213","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22","","213","9","2026-11-22 19:44:30","0","0","0");
INSERT INTO fat_details VALUES ("240","9","27","215","1","1.000","0","1","1","11","0.00",NULL,"0","0.00",NULL,"11","11","","215","9","2026-11-22 20:26:45","0","0","0");
INSERT INTO fat_details VALUES ("241","9","27","215","2","1.000","0","1","1","22","0.00",NULL,"0","0.00",NULL,"22","22","","215","9","2026-11-22 20:26:45","0","0","0");
INSERT INTO fat_details VALUES ("248","9","27","217","1","1.000","0","8","8","11","0.00",NULL,"0","0.00",NULL,"88","88","","217","9","2026-11-22 22:31:09","0","0","0");
INSERT INTO fat_details VALUES ("249","9","27","217","2","1.000","0","3","3","22","0.00",NULL,"0","0.00",NULL,"66","66","","217","9","2026-11-22 22:31:09","0","0","0");
INSERT INTO fat_details VALUES ("250","9","27","218","2","1.000","0","2","2","22","0.00",NULL,"0","0.00",NULL,"44","44","","218","9","2026-11-22 22:31:21","0","0","0");
INSERT INTO fat_details VALUES ("251","9","27","222","2","1.000","0","1","1","22","22.00",NULL,"0","0.00",NULL,"22","0","","222","9","2026-11-23 15:43:10","0","0","0");
INSERT INTO fat_details VALUES ("252","9","27","222","1","1.000","0","1","1","11","11.00",NULL,"0","0.00",NULL,"11","0","","222","9","2026-11-23 15:43:10","0","0","0");
INSERT INTO fat_details VALUES ("253","9","27","224","1","1.000","0","1","1","11","11.00",NULL,"0","0.00",NULL,"11","0","","224","9","2026-11-23 15:43:49","0","0","0");
INSERT INTO fat_details VALUES ("254","9","27","224","2","1.000","0","1","1","22","22.00",NULL,"0","0.00",NULL,"22","0","","224","9","2026-11-23 15:43:49","0","0","0");
INSERT INTO fat_details VALUES ("255","9","27","226","1","1.000","0","1","1","11","11.00",NULL,"0","0.00",NULL,"11","0","","226","9","2026-11-23 15:44:14","0","0","0");
INSERT INTO fat_details VALUES ("256","9","27","226","2","1.000","0","1","1","22","22.00",NULL,"0","0.00",NULL,"22","0","","226","9","2026-11-23 15:44:14","0","0","0");
INSERT INTO fat_details VALUES ("257","9","27","228","1","1.000","0","1","1","11","11.00",NULL,"0","0.00",NULL,"11","0","","228","9","2026-11-23 15:45:26","0","0","0");
INSERT INTO fat_details VALUES ("258","9","27","228","2","1.000","0","1","1","22","22.00",NULL,"0","0.00",NULL,"22","0","","228","9","2026-11-23 15:45:26","0","0","0");
INSERT INTO fat_details VALUES ("259","9","27","230","1","1.000","0","1","1","11","11.00",NULL,"0","0.00",NULL,"11","0","","230","9","2026-11-23 15:59:43","0","0","0");
INSERT INTO fat_details VALUES ("260","9","27","230","5","1.000","0","3","3","70","0.00",NULL,"0","0.00",NULL,"210","210","","230","9","2026-11-23 15:59:43","0","0","0");



DROP TABLE IF EXISTS fat_tybes;

CREATE TABLE `fat_tybes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fname` varchar(200) NOT NULL,
  `info` varchar(200) DEFAULT NULL,
  `crttime` timestamp NOT NULL DEFAULT current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO fat_tybes VALUES ("1","فاتورة مبيعات",NULL,"2024-01-29 18:39:27","0","0","0");
INSERT INTO fat_tybes VALUES ("2","فاتورة مشنريات",NULL,"2024-01-29 18:41:22","0","0","0");
INSERT INTO fat_tybes VALUES ("3","فاتورة مردود مبيعات",NULL,"2024-03-06 17:25:41","0","0","0");
INSERT INTO fat_tybes VALUES ("4","فاتورة مردود مشتريات",NULL,"2024-03-06 17:26:30","0","0","0");
INSERT INTO fat_tybes VALUES ("5","اذن تسليم بضاعه",NULL,"2024-03-06 17:26:30","0","0","0");
INSERT INTO fat_tybes VALUES ("6","اذن استلام بضاعه",NULL,"2024-03-06 17:26:57","0","0","0");
INSERT INTO fat_tybes VALUES ("7","اذن تسليم بضاعه",NULL,"2024-03-06 17:26:57","0","0","0");
INSERT INTO fat_tybes VALUES ("8","اذن حجز",NULL,"2024-03-06 17:29:32","0","0","0");
INSERT INTO fat_tybes VALUES ("9","امر بيع",NULL,"2024-03-06 17:29:32","0","0","0");
INSERT INTO fat_tybes VALUES ("10","امر شراء",NULL,"2024-03-06 17:29:32","0","0","0");
INSERT INTO fat_tybes VALUES ("11","فاتورة تصنيع حر",NULL,"2024-03-06 17:29:32","0","0","0");
INSERT INTO fat_tybes VALUES ("12","تصنيع نموذجي",NULL,"2024-03-06 17:29:32","0","0","0");



DROP TABLE IF EXISTS fats;

CREATE TABLE `fats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fat_id` int(11) NOT NULL,
  `zanka_id` int(11) NOT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1012 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE IF EXISTS fptybes;

CREATE TABLE `fptybes` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO fptybes VALUES ("1","حضور","2023-08-01 01:57:14",NULL,"0","0");
INSERT INTO fptybes VALUES ("2","انصراف","2023-08-01 01:57:14",NULL,"0","0");
INSERT INTO fptybes VALUES ("3","حضور اضافي","2023-08-01 01:57:42",NULL,"0","0");
INSERT INTO fptybes VALUES ("4","انصراف اضافي","2023-08-01 01:58:34",NULL,"0","0");
INSERT INTO fptybes VALUES ("5","invalid","2023-08-10 07:45:50",NULL,"0","0");



DROP TABLE IF EXISTS hiringcontracts;

CREATE TABLE `hiringcontracts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `employee` int(11) NOT NULL,
  `jop` int(11) NOT NULL,
  `jopdescription` varchar(250) DEFAULT NULL,
  `joprule1` text DEFAULT NULL,
  `joprule2` text DEFAULT NULL,
  `joprule3` text DEFAULT NULL,
  `joprule4` text DEFAULT NULL,
  `workhours` int(11) DEFAULT NULL,
  `inorderhours` int(11) DEFAULT NULL,
  `workdaysoff` int(11) DEFAULT NULL,
  `salary` int(11) DEFAULT NULL,
  `salaryraise` int(11) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `user` int(11) NOT NULL,
  `info` varchar(250) DEFAULT NULL,
  `startcontract` date DEFAULT NULL,
  `endcontract` date DEFAULT NULL,
  `type` int(11) NOT NULL,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE IF EXISTS holidays;

CREATE TABLE `holidays` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(10) NOT NULL,
  `info` text DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE IF EXISTS hr_operation_steps;

CREATE TABLE `hr_operation_steps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `operation_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `step_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `operation_id` (`operation_id`),
  CONSTRAINT `hr_operation_steps_ibfk_1` FOREIGN KEY (`operation_id`) REFERENCES `hr_operations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE IF EXISTS hr_operations;

CREATE TABLE `hr_operations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
  CONSTRAINT `hr_operations_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `hr_operations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE IF EXISTS imgs;

CREATE TABLE `imgs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `iname` text NOT NULL,
  `cname` int(11) DEFAULT NULL,
  `itemid` int(11) NOT NULL,
  `size` int(11) NOT NULL,
  `clprofile` int(11) DEFAULT NULL,
  `img_date` date DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE IF EXISTS imporfplog;

CREATE TABLE `imporfplog` (
  `id` int(1) DEFAULT NULL,
  `basma_id` int(11) NOT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE IF EXISTS inventory_audit_log;

CREATE TABLE `inventory_audit_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `audit_session` varchar(50) NOT NULL,
  `item_id` int(11) NOT NULL,
  `item_name` varchar(255) DEFAULT NULL,
  `item_barcode` varchar(100) DEFAULT NULL,
  `qty_before` decimal(15,2) NOT NULL,
  `qty_actual` decimal(15,2) NOT NULL,
  `qty_diff` decimal(15,2) NOT NULL,
  `fat_detail_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `user_name` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_session` (`audit_session`),
  KEY `idx_item` (`item_id`),
  KEY `idx_date` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO inventory_audit_log VALUES ("1","AUD-20261121-154652-89f0b7","1","تجريبي","1","-93.00","10.00","103.00","189","1","admin","","2026-11-21 15:46:52");
INSERT INTO inventory_audit_log VALUES ("2","AUD-20261121-154652-89f0b7","2","تجريبي 2","2","-75.00","20.00","95.00","190","1","admin","","2026-11-21 15:46:52");
INSERT INTO inventory_audit_log VALUES ("3","AUD-20261121-154652-89f0b7","3","ساندوتش فراخ","3","0.00","30.00","30.00","191","1","admin","","2026-11-21 15:46:52");
INSERT INTO inventory_audit_log VALUES ("4","AUD-20261121-154652-89f0b7","6","ساندوتش فراخ كرانشي","6","-3.00","15.00","18.00","192","1","admin","","2026-11-21 15:46:52");
INSERT INTO inventory_audit_log VALUES ("5","AUD-20261121-154652-89f0b7","5","ساندوتش لحم","5","-16.00","20.00","36.00","193","1","admin","","2026-11-21 15:46:52");
INSERT INTO inventory_audit_log VALUES ("6","AUD-20261121-154652-89f0b7","4","قهوة تركي","4","-15.00","16.00","31.00","194","1","admin","","2026-11-21 15:46:52");
INSERT INTO inventory_audit_log VALUES ("7","AUD-20261121-154720-c8ff71","1","تجريبي","1","10.00","5.00","-5.00","195","1","admin","","2026-11-21 15:47:20");
INSERT INTO inventory_audit_log VALUES ("8","AUD-20261121-154858-883320","1","تجريبي","1","5.00","15000.00","14995.00","196","1","admin","","2026-11-21 15:48:58");
INSERT INTO inventory_audit_log VALUES ("9","AUD-20261121-154858-883320","2","تجريبي 2","2","20.00","120.00","100.00","197","1","admin","","2026-11-21 15:48:58");



DROP TABLE IF EXISTS item_bom;

CREATE TABLE `item_bom` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL COMMENT 'الصنف المجمع',
  `component_id` int(11) NOT NULL COMMENT 'الصنف الخام',
  `qty` double NOT NULL DEFAULT 0 COMMENT 'الكمية لكل وحدة واحدة من المنتج',
  `isdeleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_item_bom_parent` (`parent_id`),
  KEY `idx_item_bom_component` (`component_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO item_bom VALUES ("1","6","1","15","0");
INSERT INTO item_bom VALUES ("2","6","2","10","0");



DROP TABLE IF EXISTS item_group;

CREATE TABLE `item_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gname` varchar(100) NOT NULL,
  `info` varchar(200) DEFAULT NULL,
  `parent` int(1) DEFAULT 0,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user` int(1) NOT NULL DEFAULT 0,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  `supplier_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `gname` (`gname`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO item_group VALUES ("1","مجموعه 1",NULL,"0","2026-11-14 21:34:37","2026-11-14 21:34:37","0","0","0","0","36");
INSERT INTO item_group VALUES ("2","مجموعه 2",NULL,"0","2026-11-14 21:34:50","2026-11-14 21:34:50","0","0","0","0","36");



DROP TABLE IF EXISTS item_group2;

CREATE TABLE `item_group2` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gname` varchar(100) NOT NULL,
  `info` varchar(100) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user` int(11) NOT NULL DEFAULT 0,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `gname` (`gname`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO item_group2 VALUES ("10","ساندوتش",NULL,"2026-11-14 21:39:37","2026-11-14 21:39:37","0","0","0","0");
INSERT INTO item_group2 VALUES ("11","مشروبات",NULL,"2026-11-14 21:39:44","2026-11-14 21:39:44","0","0","0","0");



DROP TABLE IF EXISTS item_group3;

CREATE TABLE `item_group3` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gname` varchar(100) NOT NULL,
  `info` varchar(200) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user` int(1) NOT NULL DEFAULT 0,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `gname` (`gname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE IF EXISTS item_units;

CREATE TABLE `item_units` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `unit_id` int(11) NOT NULL,
  `u_val` decimal(10,3) NOT NULL,
  `def_sale` int(11) NOT NULL DEFAULT 0,
  `def_buy` int(11) NOT NULL DEFAULT 0,
  `def_stock` int(11) NOT NULL DEFAULT 0,
  `cost_price` int(11) NOT NULL,
  `price1` decimal(10,3) NOT NULL DEFAULT 0.000,
  `price2` decimal(10,3) NOT NULL DEFAULT 0.000,
  `price3` decimal(10,3) NOT NULL DEFAULT 0.000,
  `price4` decimal(10,3) DEFAULT 0.000,
  `unit_barcode` varchar(20) NOT NULL,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO item_units VALUES ("1","1","2","1.000","0","0","0","11","11.000","0.000","0.000","0.000","1","0","0","0");
INSERT INTO item_units VALUES ("2","2","2","1.000","0","0","0","22","22.000","0.000","0.000","0.000","2","0","0","0");
INSERT INTO item_units VALUES ("3","3","2","1.000","0","0","0","0","0.000","0.000","0.000","0.000","3","0","0","0");
INSERT INTO item_units VALUES ("4","4","2","1.000","0","0","0","0","20.000","0.000","0.000","0.000","4","0","0","0");
INSERT INTO item_units VALUES ("5","5","2","1.000","0","0","0","0","50.000","0.000","0.000","0.000","5","0","0","0");
INSERT INTO item_units VALUES ("6","6","2","1.000","0","0","0","0","20.000","0.000","0.000","0.000","6","0","0","0");



DROP TABLE IF EXISTS joplevels;

CREATE TABLE `joplevels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `info` varchar(250) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO joplevels VALUES ("2","junior","","2026-01-28 14:02:31","0","0","0");
INSERT INTO joplevels VALUES ("3","fresh","","2026-01-28 14:02:49","0","0","0");
INSERT INTO joplevels VALUES ("4","fresh","س","2026-01-28 14:34:11","0","0","0");
INSERT INTO joplevels VALUES ("5","mid level","","2026-01-28 14:34:35","0","0","0");
INSERT INTO joplevels VALUES ("6","senior","","2026-01-28 14:34:43","0","0","0");



DROP TABLE IF EXISTS joprules;

CREATE TABLE `joprules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `info` varchar(250) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE IF EXISTS jops;

CREATE TABLE `jops` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `info` varchar(250) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE IF EXISTS joptybes;

CREATE TABLE `joptybes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `info` text DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE IF EXISTS journal_entries;

CREATE TABLE `journal_entries` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `journal_id` int(1) NOT NULL,
  `account_id` int(1) NOT NULL,
  `debit` int(11) NOT NULL DEFAULT 0,
  `credit` int(11) NOT NULL DEFAULT 0,
  `tybe` int(1) NOT NULL,
  `info` varchar(150) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `op2` int(11) DEFAULT 0,
  `op_id` int(11) DEFAULT 0,
  `isdeleted` tinyint(1) DEFAULT 0,
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `account_id` (`account_id`),
  KEY `journal_id` (`journal_id`),
  CONSTRAINT `journal_entries_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `acc_head` (`id`),
  CONSTRAINT `journal_entries_ibfk_2` FOREIGN KEY (`journal_id`) REFERENCES `journal_heads` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=710 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO journal_entries VALUES ("1","1","148","33","0","0",NULL,"2026-11-14 21:22:30","0","1","0","2026-11-14 21:22:30","0","0");
INSERT INTO journal_entries VALUES ("2","1","275","0","33","1",NULL,"2026-11-14 21:22:30","0","1","0","2026-11-21 15:29:52","0","0");
INSERT INTO journal_entries VALUES ("3","2","21","33","0","0",NULL,"2026-11-14 21:22:30","1","0","0","2026-11-14 21:22:30","0","0");
INSERT INTO journal_entries VALUES ("4","2","148","0","33","1",NULL,"2026-11-14 21:22:30","1","0","0","2026-11-14 21:22:30","0","0");
INSERT INTO journal_entries VALUES ("7","4","148","33","0","0",NULL,"2026-11-14 21:22:53","0","3","0","2026-11-14 21:22:53","0","0");
INSERT INTO journal_entries VALUES ("8","4","275","0","33","1",NULL,"2026-11-14 21:22:53","0","3","0","2026-11-21 15:29:52","0","0");
INSERT INTO journal_entries VALUES ("9","5","21","33","0","0",NULL,"2026-11-14 21:22:53","3","0","0","2026-11-14 21:22:53","0","0");
INSERT INTO journal_entries VALUES ("10","5","148","0","33","1",NULL,"2026-11-14 21:22:53","3","0","0","2026-11-14 21:22:53","0","0");
INSERT INTO journal_entries VALUES ("13","7","148","132","0","0",NULL,"2026-11-14 21:23:43","0","5","0","2026-11-14 21:23:43","0","0");
INSERT INTO journal_entries VALUES ("14","7","275","0","132","1",NULL,"2026-11-14 21:23:43","0","5","0","2026-11-21 15:29:52","0","0");
INSERT INTO journal_entries VALUES ("15","8","21","132","0","0",NULL,"2026-11-14 21:23:43","5","0","0","2026-11-14 21:23:43","0","0");
INSERT INTO journal_entries VALUES ("16","8","148","0","132","1",NULL,"2026-11-14 21:23:44","5","0","0","2026-11-14 21:23:44","0","0");
INSERT INTO journal_entries VALUES ("19","10","148","33","0","0",NULL,"2026-11-14 22:48:12","0","7","0","2026-11-14 22:48:12","0","0");
INSERT INTO journal_entries VALUES ("20","10","275","0","33","1",NULL,"2026-11-14 22:48:12","0","7","0","2026-11-21 15:29:52","0","0");
INSERT INTO journal_entries VALUES ("21","11","21","33","0","0",NULL,"2026-11-14 22:48:12","7","0","0","2026-11-14 22:48:12","0","0");
INSERT INTO journal_entries VALUES ("22","11","148","0","33","1",NULL,"2026-11-14 22:48:12","7","0","0","2026-11-14 22:48:12","0","0");
INSERT INTO journal_entries VALUES ("25","13","148","11","0","0",NULL,"2026-11-14 22:54:20","0","9","0","2026-11-14 22:54:20","0","0");
INSERT INTO journal_entries VALUES ("26","13","275","0","11","1",NULL,"2026-11-14 22:54:20","0","9","0","2026-11-21 15:29:52","0","0");
INSERT INTO journal_entries VALUES ("27","14","21","11","0","0",NULL,"2026-11-14 22:54:20","9","0","0","2026-11-14 22:54:20","0","0");
INSERT INTO journal_entries VALUES ("28","14","148","0","11","1",NULL,"2026-11-14 22:54:20","9","0","0","2026-11-14 22:54:20","0","0");
INSERT INTO journal_entries VALUES ("31","16","148","33","0","0",NULL,"2026-11-14 23:33:14","0","11","0","2026-11-14 23:33:14","0","0");
INSERT INTO journal_entries VALUES ("32","16","275","0","33","1",NULL,"2026-11-14 23:33:14","0","11","0","2026-11-21 15:29:52","0","0");
INSERT INTO journal_entries VALUES ("33","17","21","22","0","0",NULL,"2026-11-14 23:33:14","11","0","0","2026-11-14 23:33:14","0","0");
INSERT INTO journal_entries VALUES ("34","17","148","0","22","1",NULL,"2026-11-14 23:33:14","11","0","0","2026-11-14 23:33:14","0","0");
INSERT INTO journal_entries VALUES ("37","19","148","66","0","0",NULL,"2026-11-16 11:54:26","0","13","0","2026-11-16 11:54:26","0","0");
INSERT INTO journal_entries VALUES ("38","19","275","0","66","1",NULL,"2026-11-16 11:54:26","0","13","0","2026-11-21 15:29:52","0","0");
INSERT INTO journal_entries VALUES ("39","20","21","66","0","0",NULL,"2026-11-16 11:54:26","13","0","0","2026-11-16 11:54:26","0","0");
INSERT INTO journal_entries VALUES ("40","20","148","0","66","1",NULL,"2026-11-16 11:54:26","13","0","0","2026-11-16 11:54:26","0","0");
INSERT INTO journal_entries VALUES ("43","23","148","33","0","0",NULL,"2026-11-16 12:20:30","0","16","0","2026-11-16 12:20:30","0","0");
INSERT INTO journal_entries VALUES ("44","23","275","0","33","1",NULL,"2026-11-16 12:20:30","0","16","0","2026-11-16 12:20:30","0","0");
INSERT INTO journal_entries VALUES ("45","24","21","33","0","0",NULL,"2026-11-16 12:20:30","16","0","0","2026-11-16 12:20:30","0","0");
INSERT INTO journal_entries VALUES ("46","24","148","0","33","1",NULL,"2026-11-16 12:20:30","16","0","0","2026-11-16 12:20:30","0","0");
INSERT INTO journal_entries VALUES ("49","26","148","33","0","0",NULL,"2026-11-16 12:20:58","0","18","0","2026-11-16 12:20:58","0","0");
INSERT INTO journal_entries VALUES ("50","26","275","0","33","1",NULL,"2026-11-16 12:20:58","0","18","0","2026-11-16 12:20:58","0","0");
INSERT INTO journal_entries VALUES ("51","27","21","33","0","0",NULL,"2026-11-16 12:20:58","18","0","0","2026-11-16 12:20:58","0","0");
INSERT INTO journal_entries VALUES ("52","27","148","0","33","1",NULL,"2026-11-16 12:20:58","18","0","0","2026-11-16 12:20:58","0","0");
INSERT INTO journal_entries VALUES ("55","29","148","33","0","0",NULL,"2026-11-16 12:25:30","0","20","0","2026-11-16 12:25:30","0","0");
INSERT INTO journal_entries VALUES ("56","29","275","0","33","1",NULL,"2026-11-16 12:25:30","0","20","0","2026-11-16 12:25:30","0","0");
INSERT INTO journal_entries VALUES ("57","30","21","33","0","0",NULL,"2026-11-16 12:25:30","20","0","0","2026-11-16 12:25:30","0","0");
INSERT INTO journal_entries VALUES ("58","30","148","0","33","1",NULL,"2026-11-16 12:25:30","20","0","0","2026-11-16 12:25:30","0","0");
INSERT INTO journal_entries VALUES ("61","32","148","33","0","0",NULL,"2026-11-16 12:27:19","0","22","0","2026-11-16 12:27:19","0","0");
INSERT INTO journal_entries VALUES ("62","32","275","0","33","1",NULL,"2026-11-16 12:27:19","0","22","0","2026-11-16 12:27:19","0","0");
INSERT INTO journal_entries VALUES ("63","33","21","33","0","0",NULL,"2026-11-16 12:27:19","22","0","0","2026-11-16 12:27:19","0","0");
INSERT INTO journal_entries VALUES ("64","33","148","0","33","1",NULL,"2026-11-16 12:27:19","22","0","0","2026-11-16 12:27:19","0","0");
INSERT INTO journal_entries VALUES ("67","35","148","33","0","0",NULL,"2026-11-16 12:29:22","0","24","0","2026-11-16 12:29:22","0","0");
INSERT INTO journal_entries VALUES ("68","35","275","0","33","1",NULL,"2026-11-16 12:29:22","0","24","0","2026-11-16 12:29:22","0","0");
INSERT INTO journal_entries VALUES ("69","36","21","33","0","0",NULL,"2026-11-16 12:29:22","24","0","0","2026-11-16 12:29:22","0","0");
INSERT INTO journal_entries VALUES ("70","36","148","0","33","1",NULL,"2026-11-16 12:29:22","24","0","0","2026-11-16 12:29:22","0","0");
INSERT INTO journal_entries VALUES ("73","38","148","33","0","0",NULL,"2026-11-16 12:29:35","0","26","0","2026-11-16 12:29:35","0","0");
INSERT INTO journal_entries VALUES ("74","38","275","0","33","1",NULL,"2026-11-16 12:29:35","0","26","0","2026-11-16 12:29:35","0","0");
INSERT INTO journal_entries VALUES ("75","39","21","33","0","0",NULL,"2026-11-16 12:29:35","26","0","0","2026-11-16 12:29:35","0","0");
INSERT INTO journal_entries VALUES ("76","39","148","0","33","1",NULL,"2026-11-16 12:29:35","26","0","0","2026-11-16 12:29:35","0","0");
INSERT INTO journal_entries VALUES ("79","41","148","33","0","0",NULL,"2026-11-16 12:30:46","0","28","0","2026-11-16 12:30:46","0","0");
INSERT INTO journal_entries VALUES ("80","41","275","0","33","1",NULL,"2026-11-16 12:30:46","0","28","0","2026-11-16 12:30:46","0","0");
INSERT INTO journal_entries VALUES ("81","42","21","33","0","0",NULL,"2026-11-16 12:30:46","28","0","0","2026-11-16 12:30:46","0","0");
INSERT INTO journal_entries VALUES ("82","42","148","0","33","1",NULL,"2026-11-16 12:30:46","28","0","0","2026-11-16 12:30:46","0","0");
INSERT INTO journal_entries VALUES ("85","44","148","103","0","0",NULL,"2026-11-16 12:31:21","0","30","0","2026-11-16 12:31:21","0","0");
INSERT INTO journal_entries VALUES ("86","44","275","0","103","1",NULL,"2026-11-16 12:31:21","0","30","0","2026-11-16 12:31:21","0","0");
INSERT INTO journal_entries VALUES ("87","45","21","103","0","0",NULL,"2026-11-16 12:31:21","30","0","0","2026-11-16 12:31:21","0","0");
INSERT INTO journal_entries VALUES ("88","45","148","0","103","1",NULL,"2026-11-16 12:31:21","30","0","0","2026-11-16 12:31:21","0","0");
INSERT INTO journal_entries VALUES ("91","47","148","33","0","0",NULL,"2026-11-16 12:35:34","0","32","0","2026-11-16 12:35:34","0","0");
INSERT INTO journal_entries VALUES ("92","47","275","0","33","1",NULL,"2026-11-16 12:35:34","0","32","0","2026-11-16 12:35:34","0","0");
INSERT INTO journal_entries VALUES ("93","48","21","33","0","0",NULL,"2026-11-16 12:35:34","32","0","0","2026-11-16 12:35:34","0","0");
INSERT INTO journal_entries VALUES ("94","48","148","0","33","1",NULL,"2026-11-16 12:35:34","32","0","0","2026-11-16 12:35:34","0","0");
INSERT INTO journal_entries VALUES ("97","50","148","50","0","0",NULL,"2026-11-16 12:53:40","0","34","0","2026-11-16 12:53:40","0","0");
INSERT INTO journal_entries VALUES ("98","50","275","0","50","1",NULL,"2026-11-16 12:53:40","0","34","0","2026-11-16 12:53:40","0","0");
INSERT INTO journal_entries VALUES ("99","51","21","20","0","0",NULL,"2026-11-16 12:53:40","34","0","0","2026-11-16 12:53:40","0","0");
INSERT INTO journal_entries VALUES ("100","51","148","0","20","1",NULL,"2026-11-16 12:53:40","34","0","0","2026-11-16 12:53:40","0","0");
INSERT INTO journal_entries VALUES ("101","52","39","30","0","0",NULL,"2026-11-16 12:53:40","34","0","0","2026-11-16 12:53:40","0","0");
INSERT INTO journal_entries VALUES ("102","52","148","0","30","1",NULL,"2026-11-16 12:53:40","34","0","0","2026-11-16 12:53:40","0","0");
INSERT INTO journal_entries VALUES ("103","53","148","33","0","0",NULL,"2026-11-16 12:54:41","0","37","0","2026-11-16 12:54:41","0","0");
INSERT INTO journal_entries VALUES ("104","53","275","0","33","1",NULL,"2026-11-16 12:54:41","0","37","0","2026-11-16 12:54:41","0","0");
INSERT INTO journal_entries VALUES ("105","54","21","33","0","0",NULL,"2026-11-16 12:54:41","37","0","0","2026-11-16 12:54:41","0","0");
INSERT INTO journal_entries VALUES ("106","54","148","0","33","1",NULL,"2026-11-16 12:54:41","37","0","0","2026-11-16 12:54:41","0","0");
INSERT INTO journal_entries VALUES ("109","56","148","33","0","0",NULL,"2026-11-16 12:59:37","0","39","0","2026-11-16 12:59:37","0","0");
INSERT INTO journal_entries VALUES ("110","56","275","0","33","1",NULL,"2026-11-16 12:59:37","0","39","0","2026-11-16 12:59:37","0","0");
INSERT INTO journal_entries VALUES ("111","57","21","33","0","0",NULL,"2026-11-16 12:59:37","39","0","0","2026-11-16 12:59:37","0","0");
INSERT INTO journal_entries VALUES ("112","57","148","0","33","1",NULL,"2026-11-16 12:59:37","39","0","0","2026-11-16 12:59:37","0","0");
INSERT INTO journal_entries VALUES ("115","59","148","33","0","0",NULL,"2026-11-16 12:59:58","0","41","0","2026-11-16 12:59:58","0","0");
INSERT INTO journal_entries VALUES ("116","59","275","0","33","1",NULL,"2026-11-16 12:59:58","0","41","0","2026-11-16 12:59:58","0","0");
INSERT INTO journal_entries VALUES ("117","60","21","33","0","0",NULL,"2026-11-16 12:59:58","41","0","0","2026-11-16 12:59:58","0","0");
INSERT INTO journal_entries VALUES ("118","60","148","0","33","1",NULL,"2026-11-16 12:59:58","41","0","0","2026-11-16 12:59:58","0","0");
INSERT INTO journal_entries VALUES ("121","62","148","33","0","0",NULL,"2026-11-16 13:00:28","0","43","0","2026-11-16 13:00:28","0","0");
INSERT INTO journal_entries VALUES ("122","62","275","0","33","1",NULL,"2026-11-16 13:00:28","0","43","0","2026-11-16 13:00:28","0","0");
INSERT INTO journal_entries VALUES ("123","63","21","33","0","0",NULL,"2026-11-16 13:00:28","43","0","0","2026-11-16 13:00:28","0","0");
INSERT INTO journal_entries VALUES ("124","63","148","0","33","1",NULL,"2026-11-16 13:00:28","43","0","0","2026-11-16 13:00:28","0","0");
INSERT INTO journal_entries VALUES ("127","65","148","33","0","0",NULL,"2026-11-16 13:37:59","0","45","0","2026-11-16 13:37:59","0","0");
INSERT INTO journal_entries VALUES ("128","65","275","0","33","1",NULL,"2026-11-16 13:37:59","0","45","0","2026-11-16 13:37:59","0","0");
INSERT INTO journal_entries VALUES ("129","66","21","33","0","0",NULL,"2026-11-16 13:37:59","45","0","0","2026-11-16 13:37:59","0","0");
INSERT INTO journal_entries VALUES ("130","66","148","0","33","1",NULL,"2026-11-16 13:37:59","45","0","0","2026-11-16 13:37:59","0","0");
INSERT INTO journal_entries VALUES ("133","68","148","33","0","0",NULL,"2026-11-16 13:40:22","0","47","0","2026-11-16 13:40:22","0","0");
INSERT INTO journal_entries VALUES ("134","68","275","0","33","1",NULL,"2026-11-16 13:40:22","0","47","0","2026-11-16 13:40:22","0","0");
INSERT INTO journal_entries VALUES ("135","69","21","33","0","0",NULL,"2026-11-16 13:40:22","47","0","0","2026-11-16 13:40:22","0","0");
INSERT INTO journal_entries VALUES ("136","69","148","0","33","1",NULL,"2026-11-16 13:40:22","47","0","0","2026-11-16 13:40:22","0","0");
INSERT INTO journal_entries VALUES ("139","71","148","33","0","0",NULL,"2026-11-16 13:50:55","0","49","0","2026-11-16 13:50:55","0","0");
INSERT INTO journal_entries VALUES ("140","71","275","0","33","1",NULL,"2026-11-16 13:50:55","0","49","0","2026-11-16 13:50:55","0","0");
INSERT INTO journal_entries VALUES ("141","72","21","33","0","0",NULL,"2026-11-16 13:50:55","49","0","0","2026-11-16 13:50:55","0","0");
INSERT INTO journal_entries VALUES ("142","72","148","0","33","1",NULL,"2026-11-16 13:50:55","49","0","0","2026-11-16 13:50:55","0","0");
INSERT INTO journal_entries VALUES ("145","74","148","33","0","0",NULL,"2026-11-16 13:51:27","0","51","0","2026-11-16 13:51:27","0","0");
INSERT INTO journal_entries VALUES ("146","74","275","0","33","1",NULL,"2026-11-16 13:51:27","0","51","0","2026-11-16 13:51:27","0","0");
INSERT INTO journal_entries VALUES ("147","75","21","33","0","0",NULL,"2026-11-16 13:51:27","51","0","0","2026-11-16 13:51:27","0","0");
INSERT INTO journal_entries VALUES ("148","75","148","0","33","1",NULL,"2026-11-16 13:51:27","51","0","0","2026-11-16 13:51:27","0","0");
INSERT INTO journal_entries VALUES ("151","77","148","33","0","0",NULL,"2026-11-16 13:52:05","0","53","0","2026-11-16 13:52:05","0","0");
INSERT INTO journal_entries VALUES ("152","77","275","0","33","1",NULL,"2026-11-16 13:52:05","0","53","0","2026-11-16 13:52:05","0","0");
INSERT INTO journal_entries VALUES ("153","78","21","33","0","0",NULL,"2026-11-16 13:52:05","53","0","0","2026-11-16 13:52:05","0","0");
INSERT INTO journal_entries VALUES ("154","78","148","0","33","1",NULL,"2026-11-16 13:52:05","53","0","0","2026-11-16 13:52:05","0","0");
INSERT INTO journal_entries VALUES ("157","80","148","22","0","0",NULL,"2026-11-16 13:56:26","0","55","0","2026-11-16 13:56:26","0","0");
INSERT INTO journal_entries VALUES ("158","80","275","0","22","1",NULL,"2026-11-16 13:56:26","0","55","0","2026-11-16 13:56:26","0","0");
INSERT INTO journal_entries VALUES ("159","81","21","22","0","0",NULL,"2026-11-16 13:56:26","55","0","0","2026-11-16 13:56:26","0","0");
INSERT INTO journal_entries VALUES ("160","81","148","0","22","1",NULL,"2026-11-16 13:56:26","55","0","0","2026-11-16 13:56:26","0","0");
INSERT INTO journal_entries VALUES ("163","83","148","70","0","0",NULL,"2026-11-16 13:58:12","0","57","0","2026-11-16 13:58:12","0","0");
INSERT INTO journal_entries VALUES ("164","83","275","0","70","1",NULL,"2026-11-16 13:58:12","0","57","0","2026-11-16 13:58:12","0","0");
INSERT INTO journal_entries VALUES ("165","84","21","70","0","0",NULL,"2026-11-16 13:58:12","57","0","0","2026-11-16 13:58:12","0","0");
INSERT INTO journal_entries VALUES ("166","84","148","0","70","1",NULL,"2026-11-16 13:58:12","57","0","0","2026-11-16 13:58:12","0","0");
INSERT INTO journal_entries VALUES ("167","85","148","33","0","0",NULL,"2026-11-16 14:00:21","0","59","0","2026-11-16 14:00:21","0","0");
INSERT INTO journal_entries VALUES ("168","85","275","0","33","1",NULL,"2026-11-16 14:00:21","0","59","0","2026-11-16 14:00:21","0","0");
INSERT INTO journal_entries VALUES ("169","86","21","33","0","0",NULL,"2026-11-16 14:00:21","59","0","0","2026-11-16 14:00:21","0","0");
INSERT INTO journal_entries VALUES ("170","86","148","0","33","1",NULL,"2026-11-16 14:00:21","59","0","0","2026-11-16 14:00:21","0","0");
INSERT INTO journal_entries VALUES ("173","88","148","33","0","0",NULL,"2026-11-16 14:00:46","0","61","0","2026-11-16 14:00:46","0","0");
INSERT INTO journal_entries VALUES ("174","88","275","0","33","1",NULL,"2026-11-16 14:00:46","0","61","0","2026-11-16 14:00:46","0","0");
INSERT INTO journal_entries VALUES ("175","89","21","33","0","0",NULL,"2026-11-16 14:00:46","61","0","0","2026-11-16 14:00:46","0","0");
INSERT INTO journal_entries VALUES ("176","89","148","0","33","1",NULL,"2026-11-16 14:00:46","61","0","0","2026-11-16 14:00:46","0","0");
INSERT INTO journal_entries VALUES ("179","91","148","22","0","0",NULL,"2026-11-16 14:02:35","0","63","0","2026-11-16 14:02:35","0","0");
INSERT INTO journal_entries VALUES ("180","91","275","0","22","1",NULL,"2026-11-16 14:02:35","0","63","0","2026-11-16 14:02:35","0","0");
INSERT INTO journal_entries VALUES ("181","92","21","22","0","0",NULL,"2026-11-16 14:02:35","63","0","0","2026-11-16 14:02:35","0","0");
INSERT INTO journal_entries VALUES ("182","92","148","0","22","1",NULL,"2026-11-16 14:02:35","63","0","0","2026-11-16 14:02:35","0","0");
INSERT INTO journal_entries VALUES ("185","94","148","44","0","0",NULL,"2026-11-16 14:03:15","0","65","0","2026-11-16 14:03:15","0","0");
INSERT INTO journal_entries VALUES ("186","94","275","0","44","1",NULL,"2026-11-16 14:03:15","0","65","0","2026-11-16 14:03:15","0","0");
INSERT INTO journal_entries VALUES ("187","95","21","44","0","0",NULL,"2026-11-16 14:03:15","65","0","0","2026-11-16 14:03:15","0","0");
INSERT INTO journal_entries VALUES ("188","95","148","0","44","1",NULL,"2026-11-16 14:03:15","65","0","0","2026-11-16 14:03:15","0","0");
INSERT INTO journal_entries VALUES ("191","97","148","33","0","0",NULL,"2026-11-16 14:10:43","0","67","0","2026-11-16 14:10:43","0","0");
INSERT INTO journal_entries VALUES ("192","97","275","0","33","1",NULL,"2026-11-16 14:10:43","0","67","0","2026-11-16 14:10:43","0","0");
INSERT INTO journal_entries VALUES ("193","98","21","33","0","0",NULL,"2026-11-16 14:10:43","67","0","0","2026-11-16 14:10:43","0","0");
INSERT INTO journal_entries VALUES ("194","98","148","0","33","1",NULL,"2026-11-16 14:10:43","67","0","0","2026-11-16 14:10:43","0","0");
INSERT INTO journal_entries VALUES ("197","100","148","33","0","0",NULL,"2026-11-16 14:14:20","0","69","0","2026-11-16 14:14:20","0","0");
INSERT INTO journal_entries VALUES ("198","100","275","0","33","1",NULL,"2026-11-16 14:14:20","0","69","0","2026-11-16 14:14:20","0","0");
INSERT INTO journal_entries VALUES ("199","101","21","33","0","0",NULL,"2026-11-16 14:14:20","69","0","0","2026-11-16 14:14:20","0","0");
INSERT INTO journal_entries VALUES ("200","101","148","0","33","1",NULL,"2026-11-16 14:14:20","69","0","0","2026-11-16 14:14:20","0","0");
INSERT INTO journal_entries VALUES ("203","103","148","33","0","0",NULL,"2026-11-16 14:19:32","0","71","0","2026-11-16 14:19:32","0","0");
INSERT INTO journal_entries VALUES ("204","103","275","0","33","1",NULL,"2026-11-16 14:19:32","0","71","0","2026-11-16 14:19:32","0","0");
INSERT INTO journal_entries VALUES ("205","104","21","33","0","0",NULL,"2026-11-16 14:19:32","71","0","0","2026-11-16 14:19:32","0","0");
INSERT INTO journal_entries VALUES ("206","104","148","0","33","1",NULL,"2026-11-16 14:19:32","71","0","0","2026-11-16 14:19:32","0","0");
INSERT INTO journal_entries VALUES ("209","106","148","33","0","0",NULL,"2026-11-16 14:19:45","0","73","0","2026-11-16 14:19:45","0","0");
INSERT INTO journal_entries VALUES ("210","106","275","0","33","1",NULL,"2026-11-16 14:19:45","0","73","0","2026-11-16 14:19:45","0","0");
INSERT INTO journal_entries VALUES ("211","107","21","33","0","0",NULL,"2026-11-16 14:19:45","73","0","0","2026-11-16 14:19:45","0","0");
INSERT INTO journal_entries VALUES ("212","107","148","0","33","1",NULL,"2026-11-16 14:19:45","73","0","0","2026-11-16 14:19:45","0","0");
INSERT INTO journal_entries VALUES ("215","109","148","33","0","0",NULL,"2026-11-16 14:20:41","0","75","0","2026-11-16 14:20:41","0","0");
INSERT INTO journal_entries VALUES ("216","109","275","0","33","1",NULL,"2026-11-16 14:20:41","0","75","0","2026-11-16 14:20:41","0","0");
INSERT INTO journal_entries VALUES ("217","110","21","33","0","0",NULL,"2026-11-16 14:20:41","75","0","0","2026-11-16 14:20:41","0","0");
INSERT INTO journal_entries VALUES ("218","110","148","0","33","1",NULL,"2026-11-16 14:20:41","75","0","0","2026-11-16 14:20:41","0","0");
INSERT INTO journal_entries VALUES ("221","112","148","33","0","0",NULL,"2026-11-16 14:25:02","0","77","0","2026-11-16 14:25:02","0","0");
INSERT INTO journal_entries VALUES ("222","112","275","0","33","1",NULL,"2026-11-16 14:25:02","0","77","0","2026-11-16 14:25:02","0","0");
INSERT INTO journal_entries VALUES ("223","113","21","33","0","0",NULL,"2026-11-16 14:25:02","77","0","0","2026-11-16 14:25:02","0","0");
INSERT INTO journal_entries VALUES ("224","113","148","0","33","1",NULL,"2026-11-16 14:25:02","77","0","0","2026-11-16 14:25:02","0","0");
INSERT INTO journal_entries VALUES ("227","115","148","33","0","0",NULL,"2026-11-16 14:25:18","0","79","0","2026-11-16 14:25:18","0","0");
INSERT INTO journal_entries VALUES ("228","115","275","0","33","1",NULL,"2026-11-16 14:25:18","0","79","0","2026-11-16 14:25:18","0","0");
INSERT INTO journal_entries VALUES ("229","116","21","33","0","0",NULL,"2026-11-16 14:25:18","79","0","0","2026-11-16 14:25:18","0","0");
INSERT INTO journal_entries VALUES ("230","116","148","0","33","1",NULL,"2026-11-16 14:25:18","79","0","0","2026-11-16 14:25:18","0","0");
INSERT INTO journal_entries VALUES ("233","118","148","33","0","0",NULL,"2026-11-16 14:32:27","0","81","0","2026-11-16 14:32:27","0","0");
INSERT INTO journal_entries VALUES ("234","118","275","0","33","1",NULL,"2026-11-16 14:32:27","0","81","0","2026-11-16 14:32:27","0","0");
INSERT INTO journal_entries VALUES ("235","119","21","33","0","0",NULL,"2026-11-16 14:32:27","81","0","0","2026-11-16 14:32:27","0","0");
INSERT INTO journal_entries VALUES ("236","119","148","0","33","1",NULL,"2026-11-16 14:32:27","81","0","0","2026-11-16 14:32:27","0","0");
INSERT INTO journal_entries VALUES ("239","121","148","33","0","0",NULL,"2026-11-16 14:32:50","0","83","0","2026-11-16 14:32:50","0","0");
INSERT INTO journal_entries VALUES ("240","121","275","0","33","1",NULL,"2026-11-16 14:32:50","0","83","0","2026-11-16 14:32:50","0","0");
INSERT INTO journal_entries VALUES ("241","122","21","33","0","0",NULL,"2026-11-16 14:32:50","83","0","0","2026-11-16 14:32:50","0","0");
INSERT INTO journal_entries VALUES ("242","122","148","0","33","1",NULL,"2026-11-16 14:32:50","83","0","0","2026-11-16 14:32:50","0","0");
INSERT INTO journal_entries VALUES ("245","124","148","33","0","0",NULL,"2026-11-16 14:35:41","0","85","0","2026-11-16 14:35:41","0","0");
INSERT INTO journal_entries VALUES ("246","124","275","0","33","1",NULL,"2026-11-16 14:35:41","0","85","0","2026-11-16 14:35:41","0","0");
INSERT INTO journal_entries VALUES ("247","125","21","33","0","0",NULL,"2026-11-16 14:35:41","85","0","0","2026-11-16 14:35:41","0","0");
INSERT INTO journal_entries VALUES ("248","125","148","0","33","1",NULL,"2026-11-16 14:35:41","85","0","0","2026-11-16 14:35:41","0","0");
INSERT INTO journal_entries VALUES ("251","127","148","33","0","0",NULL,"2026-11-16 14:37:04","0","87","0","2026-11-16 14:37:04","0","0");
INSERT INTO journal_entries VALUES ("252","127","275","0","33","1",NULL,"2026-11-16 14:37:04","0","87","0","2026-11-16 14:37:04","0","0");
INSERT INTO journal_entries VALUES ("253","128","21","33","0","0",NULL,"2026-11-16 14:37:04","87","0","0","2026-11-16 14:37:04","0","0");
INSERT INTO journal_entries VALUES ("254","128","148","0","33","1",NULL,"2026-11-16 14:37:04","87","0","0","2026-11-16 14:37:04","0","0");
INSERT INTO journal_entries VALUES ("257","130","148","33","0","0",NULL,"2026-11-16 14:41:12","0","89","0","2026-11-16 14:41:12","0","0");
INSERT INTO journal_entries VALUES ("258","130","275","0","33","1",NULL,"2026-11-16 14:41:12","0","89","0","2026-11-16 14:41:12","0","0");
INSERT INTO journal_entries VALUES ("259","131","21","33","0","0",NULL,"2026-11-16 14:41:12","89","0","0","2026-11-16 14:41:12","0","0");
INSERT INTO journal_entries VALUES ("260","131","148","0","33","1",NULL,"2026-11-16 14:41:12","89","0","0","2026-11-16 14:41:12","0","0");
INSERT INTO journal_entries VALUES ("263","133","148","33","0","0",NULL,"2026-11-16 14:41:34","0","91","0","2026-11-16 14:41:34","0","0");
INSERT INTO journal_entries VALUES ("264","133","275","0","33","1",NULL,"2026-11-16 14:41:34","0","91","0","2026-11-16 14:41:34","0","0");
INSERT INTO journal_entries VALUES ("265","134","21","33","0","0",NULL,"2026-11-16 14:41:34","91","0","0","2026-11-16 14:41:34","0","0");
INSERT INTO journal_entries VALUES ("266","134","148","0","33","1",NULL,"2026-11-16 14:41:34","91","0","0","2026-11-16 14:41:34","0","0");
INSERT INTO journal_entries VALUES ("269","136","148","81","0","0",NULL,"2026-11-16 14:41:38","0","93","0","2026-11-16 14:41:38","0","0");
INSERT INTO journal_entries VALUES ("270","136","275","0","81","1",NULL,"2026-11-16 14:41:38","0","93","0","2026-11-16 14:41:38","0","0");
INSERT INTO journal_entries VALUES ("271","137","21","81","0","0",NULL,"2026-11-16 14:41:38","93","0","0","2026-11-16 14:41:38","0","0");
INSERT INTO journal_entries VALUES ("272","137","148","0","81","1",NULL,"2026-11-16 14:41:38","93","0","0","2026-11-16 14:41:38","0","0");
INSERT INTO journal_entries VALUES ("275","139","148","33","0","0",NULL,"2026-11-16 14:42:25","0","95","0","2026-11-16 14:42:25","0","0");
INSERT INTO journal_entries VALUES ("276","139","275","0","33","1",NULL,"2026-11-16 14:42:25","0","95","0","2026-11-16 14:42:25","0","0");
INSERT INTO journal_entries VALUES ("277","140","21","33","0","0",NULL,"2026-11-16 14:42:25","95","0","0","2026-11-16 14:42:25","0","0");
INSERT INTO journal_entries VALUES ("278","140","148","0","33","1",NULL,"2026-11-16 14:42:25","95","0","0","2026-11-16 14:42:25","0","0");
INSERT INTO journal_entries VALUES ("281","142","148","53","0","0",NULL,"2026-11-16 14:42:34","0","97","0","2026-11-16 14:42:34","0","0");
INSERT INTO journal_entries VALUES ("282","142","275","0","53","1",NULL,"2026-11-16 14:42:34","0","97","0","2026-11-16 14:42:34","0","0");
INSERT INTO journal_entries VALUES ("283","143","21","53","0","0",NULL,"2026-11-16 14:42:34","97","0","0","2026-11-16 14:42:34","0","0");
INSERT INTO journal_entries VALUES ("284","143","148","0","53","1",NULL,"2026-11-16 14:42:34","97","0","0","2026-11-16 14:42:34","0","0");
INSERT INTO journal_entries VALUES ("287","145","148","53","0","0",NULL,"2026-11-16 14:43:00","0","99","0","2026-11-16 14:43:00","0","0");
INSERT INTO journal_entries VALUES ("288","145","275","0","53","1",NULL,"2026-11-16 14:43:00","0","99","0","2026-11-16 14:43:00","0","0");
INSERT INTO journal_entries VALUES ("289","146","21","53","0","0",NULL,"2026-11-16 14:43:00","99","0","0","2026-11-16 14:43:00","0","0");
INSERT INTO journal_entries VALUES ("290","146","148","0","53","1",NULL,"2026-11-16 14:43:00","99","0","0","2026-11-16 14:43:00","0","0");
INSERT INTO journal_entries VALUES ("293","148","148","33","0","0",NULL,"2026-11-16 14:43:46","0","101","0","2026-11-16 14:43:46","0","0");
INSERT INTO journal_entries VALUES ("294","148","275","0","33","1",NULL,"2026-11-16 14:43:46","0","101","0","2026-11-16 14:43:46","0","0");
INSERT INTO journal_entries VALUES ("295","149","21","33","0","0",NULL,"2026-11-16 14:43:46","101","0","0","2026-11-16 14:43:46","0","0");
INSERT INTO journal_entries VALUES ("296","149","148","0","33","1",NULL,"2026-11-16 14:43:46","101","0","0","2026-11-16 14:43:46","0","0");
INSERT INTO journal_entries VALUES ("299","151","148","33","0","0",NULL,"2026-11-16 14:45:32","0","103","0","2026-11-16 14:45:32","0","0");
INSERT INTO journal_entries VALUES ("300","151","275","0","33","1",NULL,"2026-11-16 14:45:32","0","103","0","2026-11-16 14:45:32","0","0");
INSERT INTO journal_entries VALUES ("301","152","21","33","0","0",NULL,"2026-11-16 14:45:32","103","0","0","2026-11-16 14:45:32","0","0");
INSERT INTO journal_entries VALUES ("302","152","148","0","33","1",NULL,"2026-11-16 14:45:32","103","0","0","2026-11-16 14:45:32","0","0");
INSERT INTO journal_entries VALUES ("305","154","148","33","0","0",NULL,"2026-11-16 14:46:18","0","105","0","2026-11-16 14:46:18","0","0");
INSERT INTO journal_entries VALUES ("306","154","275","0","33","1",NULL,"2026-11-16 14:46:18","0","105","0","2026-11-16 14:46:18","0","0");
INSERT INTO journal_entries VALUES ("307","155","21","33","0","0",NULL,"2026-11-16 14:46:18","105","0","0","2026-11-16 14:46:18","0","0");
INSERT INTO journal_entries VALUES ("308","155","148","0","33","1",NULL,"2026-11-16 14:46:18","105","0","0","2026-11-16 14:46:18","0","0");
INSERT INTO journal_entries VALUES ("311","157","148","31","0","0",NULL,"2026-11-16 14:54:50","0","107","0","2026-11-16 14:54:50","0","0");
INSERT INTO journal_entries VALUES ("312","157","275","0","31","1",NULL,"2026-11-16 14:54:50","0","107","0","2026-11-16 14:54:50","0","0");
INSERT INTO journal_entries VALUES ("313","158","21","31","0","0",NULL,"2026-11-16 14:54:50","107","0","0","2026-11-16 14:54:50","0","0");
INSERT INTO journal_entries VALUES ("314","158","148","0","31","1",NULL,"2026-11-16 14:54:50","107","0","0","2026-11-16 14:54:50","0","0");
INSERT INTO journal_entries VALUES ("317","160","148","33","0","0",NULL,"2026-11-16 14:55:15","0","109","0","2026-11-16 14:55:15","0","0");
INSERT INTO journal_entries VALUES ("318","160","275","0","33","1",NULL,"2026-11-16 14:55:15","0","109","0","2026-11-16 14:55:15","0","0");
INSERT INTO journal_entries VALUES ("319","161","21","33","0","0",NULL,"2026-11-16 14:55:15","109","0","0","2026-11-16 14:55:15","0","0");
INSERT INTO journal_entries VALUES ("320","161","148","0","33","1",NULL,"2026-11-16 14:55:15","109","0","0","2026-11-16 14:55:15","0","0");
INSERT INTO journal_entries VALUES ("323","163","148","33","0","0",NULL,"2026-11-16 14:55:58","0","111","0","2026-11-16 14:55:58","0","0");
INSERT INTO journal_entries VALUES ("324","163","275","0","33","1",NULL,"2026-11-16 14:55:58","0","111","0","2026-11-16 14:55:58","0","0");
INSERT INTO journal_entries VALUES ("325","164","21","33","0","0",NULL,"2026-11-16 14:55:58","111","0","0","2026-11-16 14:55:58","0","0");
INSERT INTO journal_entries VALUES ("326","164","148","0","33","1",NULL,"2026-11-16 14:55:58","111","0","0","2026-11-16 14:55:58","0","0");
INSERT INTO journal_entries VALUES ("329","166","148","33","0","0",NULL,"2026-11-16 14:57:30","0","113","0","2026-11-16 14:57:30","0","0");
INSERT INTO journal_entries VALUES ("330","166","275","0","33","1",NULL,"2026-11-16 14:57:30","0","113","0","2026-11-16 14:57:30","0","0");
INSERT INTO journal_entries VALUES ("331","167","21","33","0","0",NULL,"2026-11-16 14:57:30","113","0","0","2026-11-16 14:57:30","0","0");
INSERT INTO journal_entries VALUES ("332","167","148","0","33","1",NULL,"2026-11-16 14:57:30","113","0","0","2026-11-16 14:57:30","0","0");
INSERT INTO journal_entries VALUES ("335","169","148","33","0","0",NULL,"2026-11-16 14:58:10","0","115","0","2026-11-16 14:58:10","0","0");
INSERT INTO journal_entries VALUES ("336","169","275","0","33","1",NULL,"2026-11-16 14:58:10","0","115","0","2026-11-16 14:58:10","0","0");
INSERT INTO journal_entries VALUES ("337","170","21","33","0","0",NULL,"2026-11-16 14:58:10","115","0","0","2026-11-16 14:58:10","0","0");
INSERT INTO journal_entries VALUES ("338","170","148","0","33","1",NULL,"2026-11-16 14:58:10","115","0","0","2026-11-16 14:58:10","0","0");
INSERT INTO journal_entries VALUES ("341","172","148","42","0","0",NULL,"2026-11-16 14:58:26","0","117","0","2026-11-16 14:58:26","0","0");
INSERT INTO journal_entries VALUES ("342","172","275","0","42","1",NULL,"2026-11-16 14:58:26","0","117","0","2026-11-16 14:58:26","0","0");
INSERT INTO journal_entries VALUES ("343","173","21","42","0","0",NULL,"2026-11-16 14:58:26","117","0","0","2026-11-16 14:58:26","0","0");
INSERT INTO journal_entries VALUES ("344","173","148","0","42","1",NULL,"2026-11-16 14:58:26","117","0","0","2026-11-16 14:58:26","0","0");
INSERT INTO journal_entries VALUES ("347","175","148","50","0","0",NULL,"2026-11-16 14:59:05","0","119","0","2026-11-16 14:59:05","0","0");
INSERT INTO journal_entries VALUES ("348","175","275","0","50","1",NULL,"2026-11-16 14:59:05","0","119","0","2026-11-16 14:59:05","0","0");
INSERT INTO journal_entries VALUES ("349","176","21","50","0","0",NULL,"2026-11-16 14:59:05","119","0","0","2026-11-16 14:59:05","0","0");
INSERT INTO journal_entries VALUES ("350","176","148","0","50","1",NULL,"2026-11-16 14:59:05","119","0","0","2026-11-16 14:59:05","0","0");
INSERT INTO journal_entries VALUES ("351","177","148","33","0","0",NULL,"2026-11-16 15:07:25","0","121","0","2026-11-16 15:07:25","0","0");
INSERT INTO journal_entries VALUES ("352","177","275","0","33","1",NULL,"2026-11-16 15:07:25","0","121","0","2026-11-16 15:07:25","0","0");
INSERT INTO journal_entries VALUES ("353","178","21","33","0","0",NULL,"2026-11-16 15:07:25","121","0","0","2026-11-16 15:07:25","0","0");
INSERT INTO journal_entries VALUES ("354","178","148","0","33","1",NULL,"2026-11-16 15:07:25","121","0","0","2026-11-16 15:07:25","0","0");
INSERT INTO journal_entries VALUES ("357","180","148","33","0","0",NULL,"2026-11-16 15:07:46","0","123","0","2026-11-16 15:07:46","0","0");
INSERT INTO journal_entries VALUES ("358","180","275","0","33","1",NULL,"2026-11-16 15:07:46","0","123","0","2026-11-16 15:07:46","0","0");
INSERT INTO journal_entries VALUES ("359","181","21","33","0","0",NULL,"2026-11-16 15:07:46","123","0","0","2026-11-16 15:07:46","0","0");
INSERT INTO journal_entries VALUES ("360","181","148","0","33","1",NULL,"2026-11-16 15:07:47","123","0","0","2026-11-16 15:07:47","0","0");
INSERT INTO journal_entries VALUES ("363","183","148","33","0","0",NULL,"2026-11-16 15:08:15","0","125","0","2026-11-16 15:08:15","0","0");
INSERT INTO journal_entries VALUES ("364","183","275","0","33","1",NULL,"2026-11-16 15:08:15","0","125","0","2026-11-16 15:08:15","0","0");
INSERT INTO journal_entries VALUES ("365","184","21","33","0","0",NULL,"2026-11-16 15:08:15","125","0","0","2026-11-16 15:08:15","0","0");
INSERT INTO journal_entries VALUES ("366","184","148","0","33","1",NULL,"2026-11-16 15:08:15","125","0","0","2026-11-16 15:08:15","0","0");
INSERT INTO journal_entries VALUES ("369","186","148","86","0","0",NULL,"2026-11-16 15:11:38","0","127","0","2026-11-16 15:11:38","0","0");
INSERT INTO journal_entries VALUES ("370","186","275","0","86","1",NULL,"2026-11-16 15:11:38","0","127","0","2026-11-16 15:11:38","0","0");
INSERT INTO journal_entries VALUES ("371","187","21","86","0","0",NULL,"2026-11-16 15:11:38","127","0","0","2026-11-16 15:11:38","0","0");
INSERT INTO journal_entries VALUES ("372","187","148","0","86","1",NULL,"2026-11-16 15:11:38","127","0","0","2026-11-16 15:11:38","0","0");
INSERT INTO journal_entries VALUES ("375","189","148","33","0","0",NULL,"2026-11-16 15:11:45","0","129","0","2026-11-16 15:11:45","0","0");
INSERT INTO journal_entries VALUES ("376","189","275","0","33","1",NULL,"2026-11-16 15:11:45","0","129","0","2026-11-16 15:11:45","0","0");
INSERT INTO journal_entries VALUES ("377","190","21","33","0","0",NULL,"2026-11-16 15:11:45","129","0","0","2026-11-16 15:11:45","0","0");
INSERT INTO journal_entries VALUES ("378","190","148","0","33","1",NULL,"2026-11-16 15:11:45","129","0","0","2026-11-16 15:11:45","0","0");
INSERT INTO journal_entries VALUES ("381","192","148","33","0","0",NULL,"2026-11-16 15:11:50","0","131","0","2026-11-16 15:11:50","0","0");
INSERT INTO journal_entries VALUES ("382","192","275","0","33","1",NULL,"2026-11-16 15:11:50","0","131","0","2026-11-16 15:11:50","0","0");
INSERT INTO journal_entries VALUES ("383","193","21","33","0","0",NULL,"2026-11-16 15:11:50","131","0","0","2026-11-16 15:11:50","0","0");
INSERT INTO journal_entries VALUES ("384","193","148","0","33","1",NULL,"2026-11-16 15:11:50","131","0","0","2026-11-16 15:11:50","0","0");
INSERT INTO journal_entries VALUES ("387","195","148","33","0","0",NULL,"2026-11-16 15:21:52","0","133","0","2026-11-16 15:21:52","0","0");
INSERT INTO journal_entries VALUES ("388","195","275","0","33","1",NULL,"2026-11-16 15:21:52","0","133","0","2026-11-16 15:21:52","0","0");
INSERT INTO journal_entries VALUES ("389","196","21","33","0","0",NULL,"2026-11-16 15:21:52","133","0","0","2026-11-16 15:21:52","0","0");
INSERT INTO journal_entries VALUES ("390","196","148","0","33","1",NULL,"2026-11-16 15:21:52","133","0","0","2026-11-16 15:21:52","0","0");
INSERT INTO journal_entries VALUES ("393","198","148","33","0","0",NULL,"2026-11-16 15:32:46","0","135","0","2026-11-16 15:32:46","0","0");
INSERT INTO journal_entries VALUES ("394","198","275","0","33","1",NULL,"2026-11-16 15:32:46","0","135","0","2026-11-16 15:32:46","0","0");
INSERT INTO journal_entries VALUES ("395","199","21","33","0","0",NULL,"2026-11-16 15:32:46","135","0","0","2026-11-16 15:32:46","0","0");
INSERT INTO journal_entries VALUES ("396","199","148","0","33","1",NULL,"2026-11-16 15:32:46","135","0","0","2026-11-16 15:32:46","0","0");
INSERT INTO journal_entries VALUES ("399","201","148","50","0","0",NULL,"2026-11-16 15:33:57","0","137","0","2026-11-16 15:33:57","0","0");
INSERT INTO journal_entries VALUES ("400","201","275","0","50","1",NULL,"2026-11-16 15:33:57","0","137","0","2026-11-16 15:33:57","0","0");
INSERT INTO journal_entries VALUES ("401","202","21","50","0","0",NULL,"2026-11-16 15:33:57","137","0","0","2026-11-16 15:33:57","0","0");
INSERT INTO journal_entries VALUES ("402","202","148","0","50","1",NULL,"2026-11-16 15:33:57","137","0","0","2026-11-16 15:33:57","0","0");
INSERT INTO journal_entries VALUES ("403","203","148","133","0","0",NULL,"2026-11-16 15:35:10","0","139","0","2026-11-16 15:35:10","0","0");
INSERT INTO journal_entries VALUES ("404","203","275","0","133","1",NULL,"2026-11-16 15:35:10","0","139","0","2026-11-16 15:35:10","0","0");
INSERT INTO journal_entries VALUES ("405","204","21","133","0","0",NULL,"2026-11-16 15:35:10","139","0","0","2026-11-16 15:35:10","0","0");
INSERT INTO journal_entries VALUES ("406","204","148","0","133","1",NULL,"2026-11-16 15:35:10","139","0","0","2026-11-16 15:35:10","0","0");
INSERT INTO journal_entries VALUES ("409","206","148","11","0","0",NULL,"2026-11-16 15:35:25","0","141","0","2026-11-16 15:35:25","0","0");
INSERT INTO journal_entries VALUES ("410","206","275","0","11","1",NULL,"2026-11-16 15:35:25","0","141","0","2026-11-16 15:35:25","0","0");
INSERT INTO journal_entries VALUES ("411","207","21","11","0","0",NULL,"2026-11-16 15:35:25","141","0","0","2026-11-16 15:35:25","0","0");
INSERT INTO journal_entries VALUES ("412","207","148","0","11","1",NULL,"2026-11-16 15:35:25","141","0","0","2026-11-16 15:35:25","0","0");
INSERT INTO journal_entries VALUES ("415","209","148","61","0","0",NULL,"2026-11-16 15:36:04","0","143","0","2026-11-16 15:36:04","0","0");
INSERT INTO journal_entries VALUES ("416","209","275","0","61","1",NULL,"2026-11-16 15:36:04","0","143","0","2026-11-16 15:36:04","0","0");
INSERT INTO journal_entries VALUES ("417","210","21","61","0","0",NULL,"2026-11-16 15:36:04","143","0","0","2026-11-16 15:36:04","0","0");
INSERT INTO journal_entries VALUES ("418","210","148","0","61","1",NULL,"2026-11-16 15:36:04","143","0","0","2026-11-16 15:36:04","0","0");
INSERT INTO journal_entries VALUES ("421","212","148","33","0","0",NULL,"2026-11-16 15:45:41","0","145","0","2026-11-16 15:45:41","0","0");
INSERT INTO journal_entries VALUES ("422","212","275","0","33","1",NULL,"2026-11-16 15:45:41","0","145","0","2026-11-16 15:45:41","0","0");
INSERT INTO journal_entries VALUES ("423","213","21","33","0","0",NULL,"2026-11-16 15:45:41","145","0","0","2026-11-16 15:45:41","0","0");
INSERT INTO journal_entries VALUES ("424","213","148","0","33","1",NULL,"2026-11-16 15:45:41","145","0","0","2026-11-16 15:45:41","0","0");
INSERT INTO journal_entries VALUES ("427","215","148","33","0","0",NULL,"2026-11-16 21:04:41","0","147","0","2026-11-16 21:04:41","0","0");
INSERT INTO journal_entries VALUES ("428","215","275","0","33","1",NULL,"2026-11-16 21:04:41","0","147","0","2026-11-16 21:04:41","0","0");
INSERT INTO journal_entries VALUES ("429","216","21","33","0","0",NULL,"2026-11-16 21:04:41","147","0","0","2026-11-16 21:04:41","0","0");
INSERT INTO journal_entries VALUES ("430","216","148","0","33","1",NULL,"2026-11-16 21:04:41","147","0","0","2026-11-16 21:04:41","0","0");
INSERT INTO journal_entries VALUES ("433","218","148","33","0","0",NULL,"2026-11-16 21:05:44","0","149","0","2026-11-16 21:05:44","0","0");
INSERT INTO journal_entries VALUES ("434","218","275","0","33","1",NULL,"2026-11-16 21:05:44","0","149","0","2026-11-16 21:05:44","0","0");
INSERT INTO journal_entries VALUES ("435","219","21","33","0","0",NULL,"2026-11-16 21:05:44","149","0","0","2026-11-16 21:05:44","0","0");
INSERT INTO journal_entries VALUES ("436","219","148","0","33","1",NULL,"2026-11-16 21:05:44","149","0","0","2026-11-16 21:05:44","0","0");
INSERT INTO journal_entries VALUES ("439","221","148","103","0","0",NULL,"2026-11-16 21:07:00","0","151","0","2026-11-16 21:07:00","0","0");
INSERT INTO journal_entries VALUES ("440","221","275","0","103","1",NULL,"2026-11-16 21:07:00","0","151","0","2026-11-16 21:07:00","0","0");
INSERT INTO journal_entries VALUES ("441","222","21","103","0","0",NULL,"2026-11-16 21:07:00","151","0","0","2026-11-16 21:07:00","0","0");
INSERT INTO journal_entries VALUES ("442","222","148","0","103","1",NULL,"2026-11-16 21:07:00","151","0","0","2026-11-16 21:07:00","0","0");
INSERT INTO journal_entries VALUES ("445","224","148","81","0","0",NULL,"2026-11-16 21:15:13","0","153","0","2026-11-16 21:15:13","0","0");
INSERT INTO journal_entries VALUES ("446","224","275","0","81","1",NULL,"2026-11-16 21:15:13","0","153","0","2026-11-16 21:15:13","0","0");
INSERT INTO journal_entries VALUES ("447","225","39","81","0","0",NULL,"2026-11-16 21:15:13","153","0","0","2026-11-16 21:15:13","0","0");
INSERT INTO journal_entries VALUES ("448","225","148","0","81","1",NULL,"2026-11-16 21:15:13","153","0","0","2026-11-16 21:15:13","0","0");
INSERT INTO journal_entries VALUES ("451","227","148","31","0","0",NULL,"2026-11-16 21:15:17","0","155","0","2026-11-16 21:15:17","0","0");
INSERT INTO journal_entries VALUES ("452","227","275","0","31","1",NULL,"2026-11-16 21:15:17","0","155","0","2026-11-16 21:15:17","0","0");
INSERT INTO journal_entries VALUES ("453","228","21","31","0","0",NULL,"2026-11-16 21:15:17","155","0","0","2026-11-16 21:15:17","0","0");
INSERT INTO journal_entries VALUES ("454","228","148","0","31","1",NULL,"2026-11-16 21:15:17","155","0","0","2026-11-16 21:15:17","0","0");
INSERT INTO journal_entries VALUES ("457","230","148","33","0","0",NULL,"2026-11-18 15:39:22","0","157","0","2026-11-18 15:39:22","0","0");
INSERT INTO journal_entries VALUES ("458","230","275","0","33","1",NULL,"2026-11-18 15:39:22","0","157","0","2026-11-18 15:39:22","0","0");
INSERT INTO journal_entries VALUES ("459","231","21","33","0","0",NULL,"2026-11-18 15:39:22","157","0","0","2026-11-18 15:39:22","0","0");
INSERT INTO journal_entries VALUES ("460","231","148","0","33","1",NULL,"2026-11-18 15:39:22","157","0","0","2026-11-18 15:39:22","0","0");
INSERT INTO journal_entries VALUES ("463","233","148","70","0","0",NULL,"2026-11-18 15:40:03","0","159","0","2026-11-18 15:40:03","0","0");
INSERT INTO journal_entries VALUES ("464","233","275","0","70","1",NULL,"2026-11-18 15:40:03","0","159","0","2026-11-18 15:40:03","0","0");
INSERT INTO journal_entries VALUES ("465","234","21","70","0","0",NULL,"2026-11-18 15:40:03","159","0","0","2026-11-18 15:40:03","0","0");
INSERT INTO journal_entries VALUES ("466","234","148","0","70","1",NULL,"2026-11-18 15:40:03","159","0","0","2026-11-18 15:40:03","0","0");
INSERT INTO journal_entries VALUES ("467","235","148","70","0","0",NULL,"2026-11-18 15:40:13","0","161","0","2026-11-18 15:40:13","0","0");
INSERT INTO journal_entries VALUES ("468","235","275","0","70","1",NULL,"2026-11-18 15:40:13","0","161","0","2026-11-18 15:40:13","0","0");
INSERT INTO journal_entries VALUES ("469","236","21","70","0","0",NULL,"2026-11-18 15:40:13","161","0","0","2026-11-18 15:40:13","0","0");
INSERT INTO journal_entries VALUES ("470","236","148","0","70","1",NULL,"2026-11-18 15:40:13","161","0","0","2026-11-18 15:40:13","0","0");
INSERT INTO journal_entries VALUES ("471","237","148","70","0","0",NULL,"2026-11-18 15:40:23","0","163","0","2026-11-18 15:40:23","0","0");
INSERT INTO journal_entries VALUES ("472","237","275","0","70","1",NULL,"2026-11-18 15:40:23","0","163","0","2026-11-18 15:40:23","0","0");
INSERT INTO journal_entries VALUES ("473","238","21","70","0","0",NULL,"2026-11-18 15:40:23","163","0","0","2026-11-18 15:40:23","0","0");
INSERT INTO journal_entries VALUES ("474","238","148","0","70","1",NULL,"2026-11-18 15:40:23","163","0","0","2026-11-18 15:40:23","0","0");
INSERT INTO journal_entries VALUES ("475","239","148","70","0","0",NULL,"2026-11-18 15:40:46","0","165","0","2026-11-18 15:40:46","0","0");
INSERT INTO journal_entries VALUES ("476","239","275","0","70","1",NULL,"2026-11-18 15:40:46","0","165","0","2026-11-18 15:40:46","0","0");
INSERT INTO journal_entries VALUES ("477","240","21","70","0","0",NULL,"2026-11-18 15:40:46","165","0","0","2026-11-18 15:40:46","0","0");
INSERT INTO journal_entries VALUES ("478","240","148","0","70","1",NULL,"2026-11-18 15:40:46","165","0","0","2026-11-18 15:40:46","0","0");
INSERT INTO journal_entries VALUES ("479","241","148","33","0","0",NULL,"2026-11-18 16:04:31","0","167","0","2026-11-18 16:04:31","0","0");
INSERT INTO journal_entries VALUES ("480","241","275","0","33","1",NULL,"2026-11-18 16:04:31","0","167","0","2026-11-18 16:04:31","0","0");
INSERT INTO journal_entries VALUES ("481","242","21","33","0","0",NULL,"2026-11-18 16:04:31","167","0","0","2026-11-18 16:04:31","0","0");
INSERT INTO journal_entries VALUES ("482","242","148","0","33","1",NULL,"2026-11-18 16:04:31","167","0","0","2026-11-18 16:04:31","0","0");
INSERT INTO journal_entries VALUES ("485","244","148","42","0","0",NULL,"2026-11-18 16:05:32","0","169","0","2026-11-18 16:05:32","0","0");
INSERT INTO journal_entries VALUES ("486","244","275","0","42","1",NULL,"2026-11-18 16:05:32","0","169","0","2026-11-18 16:05:32","0","0");
INSERT INTO journal_entries VALUES ("487","245","21","42","0","0",NULL,"2026-11-18 16:05:32","169","0","0","2026-11-18 16:05:32","0","0");
INSERT INTO journal_entries VALUES ("488","245","148","0","42","1",NULL,"2026-11-18 16:05:32","169","0","0","2026-11-18 16:05:32","0","0");
INSERT INTO journal_entries VALUES ("491","247","148","33","0","0",NULL,"2026-11-18 16:06:12","0","171","0","2026-11-18 16:06:12","0","0");
INSERT INTO journal_entries VALUES ("492","247","275","0","33","1",NULL,"2026-11-18 16:06:12","0","171","0","2026-11-18 16:06:12","0","0");
INSERT INTO journal_entries VALUES ("493","248","21","33","0","0",NULL,"2026-11-18 16:06:12","171","0","0","2026-11-18 16:06:12","0","0");
INSERT INTO journal_entries VALUES ("494","248","148","0","33","1",NULL,"2026-11-18 16:06:12","171","0","0","2026-11-18 16:06:12","0","0");
INSERT INTO journal_entries VALUES ("497","250","21","1000","0","0","إدخال دين","2026-11-19 14:14:42","0","0","1","2026-11-19 14:15:37","0","0");
INSERT INTO journal_entries VALUES ("498","250","39","15000","0","0","إدخال دين","2026-11-19 14:14:42","0","0","1","2026-11-19 14:15:37","0","0");
INSERT INTO journal_entries VALUES ("499","250","97","0","1600","1","إدخال ائتمان","2026-11-19 14:14:42","0","0","1","2026-11-19 14:15:37","0","0");
INSERT INTO journal_entries VALUES ("500","250","29","0","1200","1","إدخال ائتمان","2026-11-19 14:14:42","0","0","1","2026-11-19 14:15:37","0","0");
INSERT INTO journal_entries VALUES ("501","250","148","0","13200","1","إدخال ائتمان","2026-11-19 14:14:42","0","0","1","2026-11-19 14:15:37","0","0");
INSERT INTO journal_entries VALUES ("502","251","148","33","0","0",NULL,"2026-11-21 12:50:35","0","173","0","2026-11-21 12:50:35","0","0");
INSERT INTO journal_entries VALUES ("503","251","275","0","33","1",NULL,"2026-11-21 12:50:35","0","173","0","2026-11-21 12:50:35","0","0");
INSERT INTO journal_entries VALUES ("504","252","21","33","0","0",NULL,"2026-11-21 12:50:35","173","0","0","2026-11-21 12:50:35","0","0");
INSERT INTO journal_entries VALUES ("505","252","148","0","33","1",NULL,"2026-11-21 12:50:35","173","0","0","2026-11-21 12:50:35","0","0");
INSERT INTO journal_entries VALUES ("508","254","148","53","0","0",NULL,"2026-11-21 12:50:38","0","175","0","2026-11-21 12:50:38","0","0");
INSERT INTO journal_entries VALUES ("509","254","275","0","53","1",NULL,"2026-11-21 12:50:38","0","175","0","2026-11-21 12:50:38","0","0");
INSERT INTO journal_entries VALUES ("510","255","21","53","0","0",NULL,"2026-11-21 12:50:38","175","0","0","2026-11-21 12:50:38","0","0");
INSERT INTO journal_entries VALUES ("511","255","148","0","53","1",NULL,"2026-11-21 12:50:38","175","0","0","2026-11-21 12:50:38","0","0");
INSERT INTO journal_entries VALUES ("514","257","148","103","0","0",NULL,"2026-11-21 12:50:43","0","177","0","2026-11-21 12:50:43","0","0");
INSERT INTO journal_entries VALUES ("515","257","275","0","103","1",NULL,"2026-11-21 12:50:43","0","177","0","2026-11-21 12:50:43","0","0");
INSERT INTO journal_entries VALUES ("516","258","21","103","0","0",NULL,"2026-11-21 12:50:43","177","0","0","2026-11-21 12:50:43","0","0");
INSERT INTO journal_entries VALUES ("517","258","148","0","103","1",NULL,"2026-11-21 12:50:43","177","0","0","2026-11-21 12:50:43","0","0");
INSERT INTO journal_entries VALUES ("520","260","148","55","0","0",NULL,"2026-11-21 12:50:48","0","179","0","2026-11-21 12:50:48","0","0");
INSERT INTO journal_entries VALUES ("521","260","275","0","55","1",NULL,"2026-11-21 12:50:48","0","179","0","2026-11-21 12:50:48","0","0");
INSERT INTO journal_entries VALUES ("522","261","21","55","0","0",NULL,"2026-11-21 12:50:48","179","0","0","2026-11-21 12:50:48","0","0");
INSERT INTO journal_entries VALUES ("523","261","148","0","55","1",NULL,"2026-11-21 12:50:48","179","0","0","2026-11-21 12:50:48","0","0");
INSERT INTO journal_entries VALUES ("526","263","148","90","0","0",NULL,"2026-11-21 12:52:58","0","181","0","2026-11-21 12:52:58","0","0");
INSERT INTO journal_entries VALUES ("527","263","275","0","90","1",NULL,"2026-11-21 12:52:58","0","181","0","2026-11-21 12:52:58","0","0");
INSERT INTO journal_entries VALUES ("528","264","21","90","0","0",NULL,"2026-11-21 12:52:58","181","0","0","2026-11-21 12:52:58","0","0");
INSERT INTO journal_entries VALUES ("529","264","148","0","90","1",NULL,"2026-11-21 12:52:58","181","0","0","2026-11-21 12:52:58","0","0");
INSERT INTO journal_entries VALUES ("530","265","275","90","0","0",NULL,"2026-11-21 12:53:05","0","183","0","2026-11-21 15:29:52","0","0");
INSERT INTO journal_entries VALUES ("531","265","148","0","90","1",NULL,"2026-11-21 12:53:05","0","183","0","2026-11-21 12:53:05","0","0");
INSERT INTO journal_entries VALUES ("532","266","148","90","0","0",NULL,"2026-11-21 12:53:05","183","0","0","2026-11-21 12:53:05","0","0");
INSERT INTO journal_entries VALUES ("533","266","21","0","90","1",NULL,"2026-11-21 12:53:05","183","0","0","2026-11-21 12:53:05","0","0");
INSERT INTO journal_entries VALUES ("542","271","148","70","0","0",NULL,"2026-11-21 15:50:38","0","185","0","2026-11-21 15:50:38","0","0");
INSERT INTO journal_entries VALUES ("543","271","275","0","70","1",NULL,"2026-11-21 15:50:38","0","185","0","2026-11-21 15:50:38","0","0");
INSERT INTO journal_entries VALUES ("544","272","21","70","0","0",NULL,"2026-11-21 15:50:38","185","0","0","2026-11-21 15:50:38","0","0");
INSERT INTO journal_entries VALUES ("545","272","148","0","70","1",NULL,"2026-11-21 15:50:38","185","0","0","2026-11-21 15:50:38","0","0");
INSERT INTO journal_entries VALUES ("546","273","148","353","0","0",NULL,"2026-11-21 17:13:55","0","187","0","2026-11-21 17:13:55","0","0");
INSERT INTO journal_entries VALUES ("547","273","275","0","353","1",NULL,"2026-11-21 17:13:55","0","187","0","2026-11-21 17:13:55","0","0");
INSERT INTO journal_entries VALUES ("548","274","21","353","0","0",NULL,"2026-11-21 17:13:55","187","0","0","2026-11-21 17:13:55","0","0");
INSERT INTO journal_entries VALUES ("549","274","148","0","353","1",NULL,"2026-11-21 17:13:55","187","0","0","2026-11-21 17:13:55","0","0");
INSERT INTO journal_entries VALUES ("552","276","148","90","0","0",NULL,"2026-11-21 17:16:41","0","189","0","2026-11-21 17:16:41","0","0");
INSERT INTO journal_entries VALUES ("553","276","275","0","90","1",NULL,"2026-11-21 17:16:41","0","189","0","2026-11-21 17:16:41","0","0");
INSERT INTO journal_entries VALUES ("554","277","21","90","0","0",NULL,"2026-11-21 17:16:41","189","0","0","2026-11-21 17:16:41","0","0");
INSERT INTO journal_entries VALUES ("555","277","148","0","90","1",NULL,"2026-11-21 17:16:41","189","0","0","2026-11-21 17:16:41","0","0");
INSERT INTO journal_entries VALUES ("556","278","275","70","0","0",NULL,"2026-11-21 17:35:13","0","191","0","2026-11-21 17:35:13","0","0");
INSERT INTO journal_entries VALUES ("557","278","148","0","70","1",NULL,"2026-11-21 17:35:13","0","191","0","2026-11-21 17:35:13","0","0");
INSERT INTO journal_entries VALUES ("558","279","148","70","0","0",NULL,"2026-11-21 17:35:13","191","0","0","2026-11-21 17:35:13","0","0");
INSERT INTO journal_entries VALUES ("559","279","21","0","70","1",NULL,"2026-11-21 17:35:13","191","0","0","2026-11-21 17:35:13","0","0");
INSERT INTO journal_entries VALUES ("560","280","148","50","0","0",NULL,"2026-11-21 17:35:57","0","193","0","2026-11-21 17:35:57","0","0");
INSERT INTO journal_entries VALUES ("561","280","275","0","50","1",NULL,"2026-11-21 17:35:57","0","193","0","2026-11-21 17:35:57","0","0");
INSERT INTO journal_entries VALUES ("562","281","21","50","0","0",NULL,"2026-11-21 17:35:57","193","0","0","2026-11-21 17:35:57","0","0");
INSERT INTO journal_entries VALUES ("563","281","148","0","50","1",NULL,"2026-11-21 17:35:57","193","0","0","2026-11-21 17:35:57","0","0");
INSERT INTO journal_entries VALUES ("564","282","275","123","0","0",NULL,"2026-11-21 17:36:10","0","195","0","2026-11-21 17:36:10","0","0");
INSERT INTO journal_entries VALUES ("565","282","148","0","123","1",NULL,"2026-11-21 17:36:10","0","195","0","2026-11-21 17:36:10","0","0");
INSERT INTO journal_entries VALUES ("566","283","148","123","0","0",NULL,"2026-11-21 17:36:10","195","0","0","2026-11-21 17:36:10","0","0");
INSERT INTO journal_entries VALUES ("567","283","21","0","123","1",NULL,"2026-11-21 17:36:10","195","0","0","2026-11-21 17:36:10","0","0");
INSERT INTO journal_entries VALUES ("570","285","148","125","0","0",NULL,"2026-11-22 19:12:43","0","197","0","2026-11-22 19:12:43","0","0");
INSERT INTO journal_entries VALUES ("571","285","275","0","125","1",NULL,"2026-11-22 19:12:43","0","197","0","2026-11-22 19:12:43","0","0");
INSERT INTO journal_entries VALUES ("572","286","21","125","0","0",NULL,"2026-11-22 19:12:43","197","0","0","2026-11-22 19:12:43","0","0");
INSERT INTO journal_entries VALUES ("573","286","148","0","125","1",NULL,"2026-11-22 19:12:43","197","0","0","2026-11-22 19:12:43","0","0");
INSERT INTO journal_entries VALUES ("576","288","148","103","0","0",NULL,"2026-11-22 19:13:04","0","199","0","2026-11-22 19:13:04","0","0");
INSERT INTO journal_entries VALUES ("577","288","275","0","103","1",NULL,"2026-11-22 19:13:04","0","199","0","2026-11-22 19:13:04","0","0");
INSERT INTO journal_entries VALUES ("578","289","21","103","0","0",NULL,"2026-11-22 19:13:04","199","0","0","2026-11-22 19:13:04","0","0");
INSERT INTO journal_entries VALUES ("579","289","148","0","103","1",NULL,"2026-11-22 19:13:04","199","0","0","2026-11-22 19:13:04","0","0");
INSERT INTO journal_entries VALUES ("586","293","148","33","0","0",NULL,"2026-11-22 19:24:59","0","203","0","2026-11-22 19:24:59","0","0");
INSERT INTO journal_entries VALUES ("587","293","275","0","33","1",NULL,"2026-11-22 19:24:59","0","203","0","2026-11-22 19:24:59","0","0");
INSERT INTO journal_entries VALUES ("588","294","21","33","0","0",NULL,"2026-11-22 19:24:59","203","0","0","2026-11-22 19:24:59","0","0");
INSERT INTO journal_entries VALUES ("589","294","148","0","33","1",NULL,"2026-11-22 19:24:59","203","0","0","2026-11-22 19:24:59","0","0");
INSERT INTO journal_entries VALUES ("592","296","148","33","0","0",NULL,"2026-11-22 19:31:48","0","205","0","2026-11-22 19:31:48","0","0");
INSERT INTO journal_entries VALUES ("593","296","275","0","33","1",NULL,"2026-11-22 19:31:48","0","205","0","2026-11-22 19:31:48","0","0");
INSERT INTO journal_entries VALUES ("594","297","21","33","0","0",NULL,"2026-11-22 19:31:48","205","0","0","2026-11-22 19:31:48","0","0");
INSERT INTO journal_entries VALUES ("595","297","148","0","33","1",NULL,"2026-11-22 19:31:48","205","0","0","2026-11-22 19:31:48","0","0");
INSERT INTO journal_entries VALUES ("598","299","148","33","0","0",NULL,"2026-11-22 19:34:35","0","207","0","2026-11-22 19:34:35","0","0");
INSERT INTO journal_entries VALUES ("599","299","275","0","33","1",NULL,"2026-11-22 19:34:35","0","207","0","2026-11-22 19:34:35","0","0");
INSERT INTO journal_entries VALUES ("600","300","21","33","0","0",NULL,"2026-11-22 19:34:35","207","0","0","2026-11-22 19:34:35","0","0");
INSERT INTO journal_entries VALUES ("601","300","148","0","33","1",NULL,"2026-11-22 19:34:35","207","0","0","2026-11-22 19:34:35","0","0");
INSERT INTO journal_entries VALUES ("604","302","148","33","0","0",NULL,"2026-11-22 19:35:11","0","209","0","2026-11-22 19:35:11","0","0");
INSERT INTO journal_entries VALUES ("605","302","275","0","33","1",NULL,"2026-11-22 19:35:11","0","209","0","2026-11-22 19:35:11","0","0");
INSERT INTO journal_entries VALUES ("606","303","21","33","0","0",NULL,"2026-11-22 19:35:11","209","0","0","2026-11-22 19:35:11","0","0");
INSERT INTO journal_entries VALUES ("607","303","148","0","33","1",NULL,"2026-11-22 19:35:11","209","0","0","2026-11-22 19:35:11","0","0");
INSERT INTO journal_entries VALUES ("610","305","148","2991","0","0",NULL,"2026-11-22 19:44:00","0","211","0","2026-11-22 19:44:00","0","0");
INSERT INTO journal_entries VALUES ("611","305","275","0","2991","1",NULL,"2026-11-22 19:44:00","0","211","0","2026-11-22 19:44:00","0","0");
INSERT INTO journal_entries VALUES ("612","306","21","2991","0","0",NULL,"2026-11-22 19:44:00","211","0","0","2026-11-22 19:44:00","0","0");
INSERT INTO journal_entries VALUES ("613","306","148","0","2991","1",NULL,"2026-11-22 19:44:00","211","0","0","2026-11-22 19:44:00","0","0");
INSERT INTO journal_entries VALUES ("616","308","148","33","0","0",NULL,"2026-11-22 19:44:29","0","213","0","2026-11-22 19:44:29","0","0");
INSERT INTO journal_entries VALUES ("617","308","275","0","33","1",NULL,"2026-11-22 19:44:30","0","213","0","2026-11-22 19:44:30","0","0");
INSERT INTO journal_entries VALUES ("618","309","21","33","0","0",NULL,"2026-11-22 19:44:30","213","0","0","2026-11-22 19:44:30","0","0");
INSERT INTO journal_entries VALUES ("619","309","148","0","33","1",NULL,"2026-11-22 19:44:30","213","0","0","2026-11-22 19:44:30","0","0");
INSERT INTO journal_entries VALUES ("622","311","148","33","0","0",NULL,"2026-11-22 20:26:45","0","215","0","2026-11-22 20:26:45","0","0");
INSERT INTO journal_entries VALUES ("623","311","275","0","33","1",NULL,"2026-11-22 20:26:45","0","215","0","2026-11-22 20:26:45","0","0");
INSERT INTO journal_entries VALUES ("624","312","21","33","0","0",NULL,"2026-11-22 20:26:45","215","0","0","2026-11-22 20:26:45","0","0");
INSERT INTO journal_entries VALUES ("625","312","148","0","33","1",NULL,"2026-11-22 20:26:45","215","0","0","2026-11-22 20:26:45","0","0");
INSERT INTO journal_entries VALUES ("642","321","148","154","0","0",NULL,"2026-11-22 22:31:09","0","217","0","2026-11-22 22:31:09","0","0");
INSERT INTO journal_entries VALUES ("643","321","275","0","154","1",NULL,"2026-11-22 22:31:09","0","217","0","2026-11-22 22:31:09","0","0");
INSERT INTO journal_entries VALUES ("644","322","21","154","0","0",NULL,"2026-11-22 22:31:09","217","0","0","2026-11-22 22:31:09","0","0");
INSERT INTO journal_entries VALUES ("645","322","148","0","154","1",NULL,"2026-11-22 22:31:09","217","0","0","2026-11-22 22:31:09","0","0");
INSERT INTO journal_entries VALUES ("648","324","148","55","0","0",NULL,"2026-11-22 22:31:21","0","218","0","2026-11-22 22:31:21","0","0");
INSERT INTO journal_entries VALUES ("649","324","275","0","55","1",NULL,"2026-11-22 22:31:21","0","218","0","2026-11-22 22:31:21","0","0");
INSERT INTO journal_entries VALUES ("650","325","21","55","0","0",NULL,"2026-11-22 22:31:21","218","0","0","2026-11-22 22:31:21","0","0");
INSERT INTO journal_entries VALUES ("651","325","148","0","55","1",NULL,"2026-11-22 22:31:21","218","0","0","2026-11-22 22:31:21","0","0");
INSERT INTO journal_entries VALUES ("654","327","274","33","0","0",NULL,"2026-11-23 13:27:06","0","187","0","2026-11-23 13:27:06","0","0");
INSERT INTO journal_entries VALUES ("655","327","27","0","33","1",NULL,"2026-11-23 13:27:06","0","187","0","2026-11-23 13:27:06","0","0");
INSERT INTO journal_entries VALUES ("656","328","27","33","0","0",NULL,"2026-11-23 13:27:06","0","195","0","2026-11-23 13:27:06","0","0");
INSERT INTO journal_entries VALUES ("657","328","274","0","33","1",NULL,"2026-11-23 13:27:06","0","195","0","2026-11-23 13:27:06","0","0");
INSERT INTO journal_entries VALUES ("658","329","274","55","0","0",NULL,"2026-11-23 13:27:06","0","197","0","2026-11-23 13:27:06","0","0");
INSERT INTO journal_entries VALUES ("659","329","27","0","55","1",NULL,"2026-11-23 13:27:06","0","197","0","2026-11-23 13:27:06","0","0");
INSERT INTO journal_entries VALUES ("660","330","274","33","0","0",NULL,"2026-11-23 13:27:06","0","199","0","2026-11-23 13:27:06","0","0");
INSERT INTO journal_entries VALUES ("661","330","27","0","33","1",NULL,"2026-11-23 13:27:06","0","199","0","2026-11-23 13:27:06","0","0");
INSERT INTO journal_entries VALUES ("662","331","274","33","0","0",NULL,"2026-11-23 13:27:06","0","203","0","2026-11-23 13:27:06","0","0");
INSERT INTO journal_entries VALUES ("663","331","27","0","33","1",NULL,"2026-11-23 13:27:06","0","203","0","2026-11-23 13:27:06","0","0");
INSERT INTO journal_entries VALUES ("664","332","274","33","0","0",NULL,"2026-11-23 13:27:06","0","205","0","2026-11-23 13:27:06","0","0");
INSERT INTO journal_entries VALUES ("665","332","27","0","33","1",NULL,"2026-11-23 13:27:06","0","205","0","2026-11-23 13:27:06","0","0");
INSERT INTO journal_entries VALUES ("666","333","274","33","0","0",NULL,"2026-11-23 13:27:06","0","207","0","2026-11-23 13:27:06","0","0");
INSERT INTO journal_entries VALUES ("667","333","27","0","33","1",NULL,"2026-11-23 13:27:06","0","207","0","2026-11-23 13:27:06","0","0");
INSERT INTO journal_entries VALUES ("668","334","274","33","0","0",NULL,"2026-11-23 13:27:06","0","209","0","2026-11-23 13:27:06","0","0");
INSERT INTO journal_entries VALUES ("669","334","27","0","33","1",NULL,"2026-11-23 13:27:06","0","209","0","2026-11-23 13:27:06","0","0");
INSERT INTO journal_entries VALUES ("670","335","274","99","0","0",NULL,"2026-11-23 13:27:06","0","211","0","2026-11-23 13:27:06","0","0");
INSERT INTO journal_entries VALUES ("671","335","27","0","99","1",NULL,"2026-11-23 13:27:06","0","211","0","2026-11-23 13:27:06","0","0");
INSERT INTO journal_entries VALUES ("672","336","274","33","0","0",NULL,"2026-11-23 13:27:06","0","213","0","2026-11-23 13:27:06","0","0");
INSERT INTO journal_entries VALUES ("673","336","27","0","33","1",NULL,"2026-11-23 13:27:06","0","213","0","2026-11-23 13:27:06","0","0");
INSERT INTO journal_entries VALUES ("674","337","274","33","0","0",NULL,"2026-11-23 13:27:06","0","215","0","2026-11-23 13:27:06","0","0");
INSERT INTO journal_entries VALUES ("675","337","27","0","33","1",NULL,"2026-11-23 13:27:06","0","215","0","2026-11-23 13:27:06","0","0");
INSERT INTO journal_entries VALUES ("676","338","274","154","0","0",NULL,"2026-11-23 13:27:06","0","217","0","2026-11-23 13:27:06","0","0");
INSERT INTO journal_entries VALUES ("677","338","27","0","154","1",NULL,"2026-11-23 13:27:06","0","217","0","2026-11-23 13:27:06","0","0");
INSERT INTO journal_entries VALUES ("678","339","274","44","0","0",NULL,"2026-11-23 13:27:06","0","218","0","2026-11-23 13:27:06","0","0");
INSERT INTO journal_entries VALUES ("679","339","27","0","44","1",NULL,"2026-11-23 13:27:06","0","218","0","2026-11-23 13:27:06","0","0");
INSERT INTO journal_entries VALUES ("680","340","148","33","0","0",NULL,"2026-11-23 15:43:10","0","222","0","2026-11-23 15:43:10","0","0");
INSERT INTO journal_entries VALUES ("681","340","275","0","33","1",NULL,"2026-11-23 15:43:10","0","222","0","2026-11-23 15:43:10","0","0");
INSERT INTO journal_entries VALUES ("682","341","21","33","0","0",NULL,"2026-11-23 15:43:10","222","0","0","2026-11-23 15:43:10","0","0");
INSERT INTO journal_entries VALUES ("683","341","148","0","33","1",NULL,"2026-11-23 15:43:10","222","0","0","2026-11-23 15:43:10","0","0");
INSERT INTO journal_entries VALUES ("684","342","274","33","0","0",NULL,"2026-11-23 15:43:10","0","222","0","2026-11-23 15:43:10","0","0");
INSERT INTO journal_entries VALUES ("685","342","27","0","33","1",NULL,"2026-11-23 15:43:10","0","222","0","2026-11-23 15:43:10","0","0");
INSERT INTO journal_entries VALUES ("686","343","148","33","0","0",NULL,"2026-11-23 15:43:49","0","224","0","2026-11-23 15:43:49","0","0");
INSERT INTO journal_entries VALUES ("687","343","275","0","33","1",NULL,"2026-11-23 15:43:49","0","224","0","2026-11-23 15:43:49","0","0");
INSERT INTO journal_entries VALUES ("688","344","21","33","0","0",NULL,"2026-11-23 15:43:49","224","0","0","2026-11-23 15:43:49","0","0");
INSERT INTO journal_entries VALUES ("689","344","148","0","33","1",NULL,"2026-11-23 15:43:49","224","0","0","2026-11-23 15:43:49","0","0");
INSERT INTO journal_entries VALUES ("690","345","274","33","0","0",NULL,"2026-11-23 15:43:49","0","224","0","2026-11-23 15:43:49","0","0");
INSERT INTO journal_entries VALUES ("691","345","27","0","33","1",NULL,"2026-11-23 15:43:49","0","224","0","2026-11-23 15:43:49","0","0");
INSERT INTO journal_entries VALUES ("692","346","148","33","0","0",NULL,"2026-11-23 15:44:14","0","226","0","2026-11-23 15:44:14","0","0");
INSERT INTO journal_entries VALUES ("693","346","275","0","33","1",NULL,"2026-11-23 15:44:14","0","226","0","2026-11-23 15:44:14","0","0");
INSERT INTO journal_entries VALUES ("694","347","21","33","0","0",NULL,"2026-11-23 15:44:14","226","0","0","2026-11-23 15:44:14","0","0");
INSERT INTO journal_entries VALUES ("695","347","148","0","33","1",NULL,"2026-11-23 15:44:14","226","0","0","2026-11-23 15:44:14","0","0");
INSERT INTO journal_entries VALUES ("696","348","274","33","0","0",NULL,"2026-11-23 15:44:14","0","226","0","2026-11-23 15:44:14","0","0");
INSERT INTO journal_entries VALUES ("697","348","27","0","33","1",NULL,"2026-11-23 15:44:14","0","226","0","2026-11-23 15:44:14","0","0");
INSERT INTO journal_entries VALUES ("698","349","148","33","0","0",NULL,"2026-11-23 15:45:26","0","228","0","2026-11-23 15:45:26","0","0");
INSERT INTO journal_entries VALUES ("699","349","275","0","33","1",NULL,"2026-11-23 15:45:26","0","228","0","2026-11-23 15:45:26","0","0");
INSERT INTO journal_entries VALUES ("700","350","21","33","0","0",NULL,"2026-11-23 15:45:26","228","0","0","2026-11-23 15:45:26","0","0");
INSERT INTO journal_entries VALUES ("701","350","148","0","33","1",NULL,"2026-11-23 15:45:26","228","0","0","2026-11-23 15:45:26","0","0");
INSERT INTO journal_entries VALUES ("702","351","274","33","0","0",NULL,"2026-11-23 15:45:26","0","228","0","2026-11-23 15:45:26","0","0");
INSERT INTO journal_entries VALUES ("703","351","27","0","33","1",NULL,"2026-11-23 15:45:26","0","228","0","2026-11-23 15:45:26","0","0");
INSERT INTO journal_entries VALUES ("704","352","148","221","0","0",NULL,"2026-11-23 15:59:43","0","230","0","2026-11-23 15:59:43","0","0");
INSERT INTO journal_entries VALUES ("705","352","275","0","221","1",NULL,"2026-11-23 15:59:43","0","230","0","2026-11-23 15:59:43","0","0");
INSERT INTO journal_entries VALUES ("706","353","21","221","0","0",NULL,"2026-11-23 15:59:43","230","0","0","2026-11-23 15:59:43","0","0");
INSERT INTO journal_entries VALUES ("707","353","148","0","221","1",NULL,"2026-11-23 15:59:43","230","0","0","2026-11-23 15:59:43","0","0");
INSERT INTO journal_entries VALUES ("708","354","274","11","0","0",NULL,"2026-11-23 15:59:43","0","230","0","2026-11-23 15:59:43","0","0");
INSERT INTO journal_entries VALUES ("709","354","27","0","11","1",NULL,"2026-11-23 15:59:43","0","230","0","2026-11-23 15:59:43","0","0");



DROP TABLE IF EXISTS journal_heads;

CREATE TABLE `journal_heads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `journal_id` int(11) NOT NULL,
  `total` double NOT NULL,
  `jdate` date NOT NULL DEFAULT current_timestamp(),
  `op_id` int(11) DEFAULT NULL,
  `pro_tybe` int(11) DEFAULT NULL,
  `details` varchar(250) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `op2` int(11) DEFAULT 0,
  `isdeleted` tinyint(1) DEFAULT 0,
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user` int(1) DEFAULT NULL,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `journal_heads_ibfk_1` (`pro_tybe`)
) ENGINE=InnoDB AUTO_INCREMENT=355 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO journal_heads VALUES ("1","1","33","2026-11-14","1",NULL,"فاتورة ريسيت _ 1","2026-11-14 21:22:30","0","0","2026-11-14 21:22:30","1","0","0");
INSERT INTO journal_heads VALUES ("2","2","33","2026-11-14","2",NULL,"سند قبض كاش _ 1","2026-11-14 21:22:30","1","0","2026-11-14 21:22:30","1","0","0");
INSERT INTO journal_heads VALUES ("4","4","33","2026-11-14","3",NULL,"فاتورة ريسيت _ 3","2026-11-14 21:22:53","0","0","2026-11-14 21:22:53","1","0","0");
INSERT INTO journal_heads VALUES ("5","5","33","2026-11-14","4",NULL,"سند قبض كاش _ 2","2026-11-14 21:22:53","3","0","2026-11-14 21:22:53","1","0","0");
INSERT INTO journal_heads VALUES ("7","7","132","2026-11-14","5",NULL,"فاتورة ريسيت _ 5","2026-11-14 21:23:43","0","0","2026-11-14 21:23:43","1","0","0");
INSERT INTO journal_heads VALUES ("8","8","132","2026-11-14","6",NULL,"سند قبض كاش _ 3","2026-11-14 21:23:43","5","0","2026-11-14 21:23:43","1","0","0");
INSERT INTO journal_heads VALUES ("10","10","33","2026-11-14","7",NULL,"فاتورة ريسيت _ 7","2026-11-14 22:48:12","0","0","2026-11-14 22:48:12","1","0","0");
INSERT INTO journal_heads VALUES ("11","11","33","2026-11-14","8",NULL,"سند قبض كاش _ 4","2026-11-14 22:48:12","7","0","2026-11-14 22:48:12","1","0","0");
INSERT INTO journal_heads VALUES ("13","13","11","2026-11-14","9",NULL,"فاتورة ريسيت _ 9","2026-11-14 22:54:20","0","0","2026-11-14 22:54:20","1","0","0");
INSERT INTO journal_heads VALUES ("14","14","11","2026-11-14","10",NULL,"سند قبض كاش _ 5","2026-11-14 22:54:20","9","0","2026-11-14 22:54:20","1","0","0");
INSERT INTO journal_heads VALUES ("16","16","33","2026-11-14","11",NULL,"فاتورة ريسيت _ 11","2026-11-14 23:33:14","0","0","2026-11-14 23:33:14","1","0","0");
INSERT INTO journal_heads VALUES ("17","17","22","2026-11-14","12",NULL,"سند قبض كاش _ 6","2026-11-14 23:33:14","11","0","2026-11-14 23:33:14","1","0","0");
INSERT INTO journal_heads VALUES ("19","19","66","2026-11-16","13",NULL,"فاتورة ريسيت _ 13","2026-11-16 11:54:26","0","0","2026-11-16 11:54:26","1","0","0");
INSERT INTO journal_heads VALUES ("20","20","66","2026-11-16","14",NULL,"سند قبض كاش _ 7","2026-11-16 11:54:26","13","0","2026-11-16 11:54:26","1","0","0");
INSERT INTO journal_heads VALUES ("23","22","33","2026-11-16","16",NULL,"فاتورة ريسيت _ 16","2026-11-16 12:20:30","0","0","2026-11-16 12:20:30","1","0","0");
INSERT INTO journal_heads VALUES ("24","23","33","2026-11-16","17",NULL,"سند قبض كاش _ 8","2026-11-16 12:20:30","16","0","2026-11-16 12:20:30","1","0","0");
INSERT INTO journal_heads VALUES ("26","25","33","2026-11-16","18",NULL,"فاتورة ريسيت _ 18","2026-11-16 12:20:58","0","0","2026-11-16 12:20:58","1","0","0");
INSERT INTO journal_heads VALUES ("27","26","33","2026-11-16","19",NULL,"سند قبض كاش _ 9","2026-11-16 12:20:58","18","0","2026-11-16 12:20:58","1","0","0");
INSERT INTO journal_heads VALUES ("29","28","33","2026-11-16","20",NULL,"فاتورة ريسيت _ 20","2026-11-16 12:25:30","0","0","2026-11-16 12:25:30","1","0","0");
INSERT INTO journal_heads VALUES ("30","29","33","2026-11-16","21",NULL,"سند قبض كاش _ 10","2026-11-16 12:25:30","20","0","2026-11-16 12:25:30","1","0","0");
INSERT INTO journal_heads VALUES ("32","31","33","2026-11-16","22",NULL,"فاتورة ريسيت _ 22","2026-11-16 12:27:19","0","0","2026-11-16 12:27:19","1","0","0");
INSERT INTO journal_heads VALUES ("33","32","33","2026-11-16","23",NULL,"سند قبض كاش _ 11","2026-11-16 12:27:19","22","0","2026-11-16 12:27:19","1","0","0");
INSERT INTO journal_heads VALUES ("35","34","33","2026-11-16","24",NULL,"فاتورة ريسيت _ 24","2026-11-16 12:29:22","0","0","2026-11-16 12:29:22","1","0","0");
INSERT INTO journal_heads VALUES ("36","35","33","2026-11-16","25",NULL,"سند قبض كاش _ 12","2026-11-16 12:29:22","24","0","2026-11-16 12:29:22","1","0","0");
INSERT INTO journal_heads VALUES ("38","37","33","2026-11-16","26",NULL,"فاتورة ريسيت _ 26","2026-11-16 12:29:35","0","0","2026-11-16 12:29:35","1","0","0");
INSERT INTO journal_heads VALUES ("39","38","33","2026-11-16","27",NULL,"سند قبض كاش _ 13","2026-11-16 12:29:35","26","0","2026-11-16 12:29:35","1","0","0");
INSERT INTO journal_heads VALUES ("41","40","33","2026-11-16","28",NULL,"فاتورة ريسيت _ 28","2026-11-16 12:30:46","0","0","2026-11-16 12:30:46","1","0","0");
INSERT INTO journal_heads VALUES ("42","41","33","2026-11-16","29",NULL,"سند قبض كاش _ 14","2026-11-16 12:30:46","28","0","2026-11-16 12:30:46","1","0","0");
INSERT INTO journal_heads VALUES ("44","43","103","2026-11-16","30",NULL,"فاتورة ريسيت _ 30","2026-11-16 12:31:21","0","0","2026-11-16 12:31:21","1","0","0");
INSERT INTO journal_heads VALUES ("45","44","103","2026-11-16","31",NULL,"سند قبض كاش _ 15","2026-11-16 12:31:21","30","0","2026-11-16 12:31:21","1","0","0");
INSERT INTO journal_heads VALUES ("47","46","33","2026-11-16","32",NULL,"فاتورة ريسيت _ 32","2026-11-16 12:35:34","0","0","2026-11-16 12:35:34","1","0","0");
INSERT INTO journal_heads VALUES ("48","47","33","2026-11-16","33",NULL,"سند قبض كاش _ 16","2026-11-16 12:35:34","32","0","2026-11-16 12:35:34","1","0","0");
INSERT INTO journal_heads VALUES ("50","49","50","2026-11-16","34",NULL,"فاتورة ريسيت _ 34","2026-11-16 12:53:40","0","0","2026-11-16 12:53:40","1","0","0");
INSERT INTO journal_heads VALUES ("51","50","20","2026-11-16","35",NULL,"سند قبض كاش _ 17","2026-11-16 12:53:40","34","0","2026-11-16 12:53:40","1","0","0");
INSERT INTO journal_heads VALUES ("52","51","30","2026-11-16","36",NULL,"سند قبض صرافة _ 17","2026-11-16 12:53:40","34","0","2026-11-16 12:53:40","1","0","0");
INSERT INTO journal_heads VALUES ("53","52","33","2026-11-16","37",NULL,"فاتورة ريسيت _ 37","2026-11-16 12:54:41","0","0","2026-11-16 12:54:41","1","0","0");
INSERT INTO journal_heads VALUES ("54","53","33","2026-11-16","38",NULL,"سند قبض كاش _ 18","2026-11-16 12:54:41","37","0","2026-11-16 12:54:41","1","0","0");
INSERT INTO journal_heads VALUES ("56","55","33","2026-11-16","39",NULL,"فاتورة ريسيت _ 39","2026-11-16 12:59:37","0","0","2026-11-16 12:59:37","1","0","0");
INSERT INTO journal_heads VALUES ("57","56","33","2026-11-16","40",NULL,"سند قبض كاش _ 19","2026-11-16 12:59:37","39","0","2026-11-16 12:59:37","1","0","0");
INSERT INTO journal_heads VALUES ("59","58","33","2026-11-16","41",NULL,"فاتورة ريسيت _ 41","2026-11-16 12:59:58","0","0","2026-11-16 12:59:58","1","0","0");
INSERT INTO journal_heads VALUES ("60","59","33","2026-11-16","42",NULL,"سند قبض كاش _ 20","2026-11-16 12:59:58","41","0","2026-11-16 12:59:58","1","0","0");
INSERT INTO journal_heads VALUES ("62","61","33","2026-11-16","43",NULL,"فاتورة ريسيت _ 43","2026-11-16 13:00:28","0","0","2026-11-16 13:00:28","1","0","0");
INSERT INTO journal_heads VALUES ("63","62","33","2026-11-16","44",NULL,"سند قبض كاش _ 21","2026-11-16 13:00:28","43","0","2026-11-16 13:00:28","1","0","0");
INSERT INTO journal_heads VALUES ("65","64","33","2026-11-16","45",NULL,"فاتورة ريسيت _ 45","2026-11-16 13:37:59","0","0","2026-11-16 13:37:59","1","0","0");
INSERT INTO journal_heads VALUES ("66","65","33","2026-11-16","46",NULL,"سند قبض كاش _ 22","2026-11-16 13:37:59","45","0","2026-11-16 13:37:59","1","0","0");
INSERT INTO journal_heads VALUES ("68","67","33","2026-11-16","47",NULL,"فاتورة ريسيت _ 47","2026-11-16 13:40:22","0","0","2026-11-16 13:40:22","1","0","0");
INSERT INTO journal_heads VALUES ("69","68","33","2026-11-16","48",NULL,"سند قبض كاش _ 23","2026-11-16 13:40:22","47","0","2026-11-16 13:40:22","1","0","0");
INSERT INTO journal_heads VALUES ("71","70","33","2026-11-16","49",NULL,"فاتورة ريسيت _ 49","2026-11-16 13:50:55","0","0","2026-11-16 13:50:55","1","0","0");
INSERT INTO journal_heads VALUES ("72","71","33","2026-11-16","50",NULL,"سند قبض كاش _ 24","2026-11-16 13:50:55","49","0","2026-11-16 13:50:55","1","0","0");
INSERT INTO journal_heads VALUES ("74","73","33","2026-11-16","51",NULL,"فاتورة ريسيت _ 51","2026-11-16 13:51:27","0","0","2026-11-16 13:51:27","1","0","0");
INSERT INTO journal_heads VALUES ("75","74","33","2026-11-16","52",NULL,"سند قبض كاش _ 25","2026-11-16 13:51:27","51","0","2026-11-16 13:51:27","1","0","0");
INSERT INTO journal_heads VALUES ("77","76","33","2026-11-16","53",NULL,"فاتورة ريسيت _ 53","2026-11-16 13:52:05","0","0","2026-11-16 13:52:05","1","0","0");
INSERT INTO journal_heads VALUES ("78","77","33","2026-11-16","54",NULL,"سند قبض كاش _ 26","2026-11-16 13:52:05","53","0","2026-11-16 13:52:05","1","0","0");
INSERT INTO journal_heads VALUES ("80","79","22","2026-11-16","55",NULL,"فاتورة ريسيت _ 55","2026-11-16 13:56:26","0","0","2026-11-16 13:56:26","1","0","0");
INSERT INTO journal_heads VALUES ("81","80","22","2026-11-16","56",NULL,"سند قبض كاش _ 27","2026-11-16 13:56:26","55","0","2026-11-16 13:56:26","1","0","0");
INSERT INTO journal_heads VALUES ("83","82","70","2026-11-16","57",NULL,"فاتورة ريسيت _ 57","2026-11-16 13:58:12","0","0","2026-11-16 13:58:12","1","0","0");
INSERT INTO journal_heads VALUES ("84","83","70","2026-11-16","58",NULL,"سند قبض كاش _ 28","2026-11-16 13:58:12","57","0","2026-11-16 13:58:12","1","0","0");
INSERT INTO journal_heads VALUES ("85","84","33","2026-11-16","59",NULL,"فاتورة ريسيت _ 59","2026-11-16 14:00:21","0","0","2026-11-16 14:00:21","1","0","0");
INSERT INTO journal_heads VALUES ("86","85","33","2026-11-16","60",NULL,"سند قبض كاش _ 29","2026-11-16 14:00:21","59","0","2026-11-16 14:00:21","1","0","0");
INSERT INTO journal_heads VALUES ("88","87","33","2026-11-16","61",NULL,"فاتورة ريسيت _ 61","2026-11-16 14:00:46","0","0","2026-11-16 14:00:46","1","0","0");
INSERT INTO journal_heads VALUES ("89","88","33","2026-11-16","62",NULL,"سند قبض كاش _ 30","2026-11-16 14:00:46","61","0","2026-11-16 14:00:46","1","0","0");
INSERT INTO journal_heads VALUES ("91","90","22","2026-11-16","63",NULL,"فاتورة ريسيت _ 63","2026-11-16 14:02:35","0","0","2026-11-16 14:02:35","1","0","0");
INSERT INTO journal_heads VALUES ("92","91","22","2026-11-16","64",NULL,"سند قبض كاش _ 31","2026-11-16 14:02:35","63","0","2026-11-16 14:02:35","1","0","0");
INSERT INTO journal_heads VALUES ("94","93","44","2026-11-16","65",NULL,"فاتورة ريسيت _ 65","2026-11-16 14:03:15","0","0","2026-11-16 14:03:15","1","0","0");
INSERT INTO journal_heads VALUES ("95","94","44","2026-11-16","66",NULL,"سند قبض كاش _ 32","2026-11-16 14:03:15","65","0","2026-11-16 14:03:15","1","0","0");
INSERT INTO journal_heads VALUES ("97","96","33","2026-11-16","67",NULL,"فاتورة ريسيت _ 67","2026-11-16 14:10:43","0","0","2026-11-16 14:10:43","1","0","0");
INSERT INTO journal_heads VALUES ("98","97","33","2026-11-16","68",NULL,"سند قبض كاش _ 33","2026-11-16 14:10:43","67","0","2026-11-16 14:10:43","1","0","0");
INSERT INTO journal_heads VALUES ("100","99","33","2026-11-16","69",NULL,"فاتورة ريسيت _ 69","2026-11-16 14:14:20","0","0","2026-11-16 14:14:20","1","0","0");
INSERT INTO journal_heads VALUES ("101","100","33","2026-11-16","70",NULL,"سند قبض كاش _ 34","2026-11-16 14:14:20","69","0","2026-11-16 14:14:20","1","0","0");
INSERT INTO journal_heads VALUES ("103","102","33","2026-11-16","71",NULL,"فاتورة ريسيت _ 71","2026-11-16 14:19:32","0","0","2026-11-16 14:19:32","1","0","0");
INSERT INTO journal_heads VALUES ("104","103","33","2026-11-16","72",NULL,"سند قبض كاش _ 35","2026-11-16 14:19:32","71","0","2026-11-16 14:19:32","1","0","0");
INSERT INTO journal_heads VALUES ("106","105","33","2026-11-16","73",NULL,"فاتورة ريسيت _ 73","2026-11-16 14:19:45","0","0","2026-11-16 14:19:45","1","0","0");
INSERT INTO journal_heads VALUES ("107","106","33","2026-11-16","74",NULL,"سند قبض كاش _ 36","2026-11-16 14:19:45","73","0","2026-11-16 14:19:45","1","0","0");
INSERT INTO journal_heads VALUES ("109","108","33","2026-11-16","75",NULL,"فاتورة ريسيت _ 75","2026-11-16 14:20:41","0","0","2026-11-16 14:20:41","1","0","0");
INSERT INTO journal_heads VALUES ("110","109","33","2026-11-16","76",NULL,"سند قبض كاش _ 37","2026-11-16 14:20:41","75","0","2026-11-16 14:20:41","1","0","0");
INSERT INTO journal_heads VALUES ("112","111","33","2026-11-16","77",NULL,"فاتورة ريسيت _ 77","2026-11-16 14:25:02","0","0","2026-11-16 14:25:02","1","0","0");
INSERT INTO journal_heads VALUES ("113","112","33","2026-11-16","78",NULL,"سند قبض كاش _ 38","2026-11-16 14:25:02","77","0","2026-11-16 14:25:02","1","0","0");
INSERT INTO journal_heads VALUES ("115","114","33","2026-11-16","79",NULL,"فاتورة ريسيت _ 79","2026-11-16 14:25:18","0","0","2026-11-16 14:25:18","1","0","0");
INSERT INTO journal_heads VALUES ("116","115","33","2026-11-16","80",NULL,"سند قبض كاش _ 39","2026-11-16 14:25:18","79","0","2026-11-16 14:25:18","1","0","0");
INSERT INTO journal_heads VALUES ("118","117","33","2026-11-16","81",NULL,"فاتورة ريسيت _ 81","2026-11-16 14:32:27","0","0","2026-11-16 14:32:27","1","0","0");
INSERT INTO journal_heads VALUES ("119","118","33","2026-11-16","82",NULL,"سند قبض كاش _ 40","2026-11-16 14:32:27","81","0","2026-11-16 14:32:27","1","0","0");
INSERT INTO journal_heads VALUES ("121","120","33","2026-11-16","83",NULL,"فاتورة ريسيت _ 83","2026-11-16 14:32:50","0","0","2026-11-16 14:32:50","1","0","0");
INSERT INTO journal_heads VALUES ("122","121","33","2026-11-16","84",NULL,"سند قبض كاش _ 41","2026-11-16 14:32:50","83","0","2026-11-16 14:32:50","1","0","0");
INSERT INTO journal_heads VALUES ("124","123","33","2026-11-16","85",NULL,"فاتورة ريسيت _ 85","2026-11-16 14:35:41","0","0","2026-11-16 14:35:41","1","0","0");
INSERT INTO journal_heads VALUES ("125","124","33","2026-11-16","86",NULL,"سند قبض كاش _ 42","2026-11-16 14:35:41","85","0","2026-11-16 14:35:41","1","0","0");
INSERT INTO journal_heads VALUES ("127","126","33","2026-11-16","87",NULL,"فاتورة ريسيت _ 87","2026-11-16 14:37:04","0","0","2026-11-16 14:37:04","1","0","0");
INSERT INTO journal_heads VALUES ("128","127","33","2026-11-16","88",NULL,"سند قبض كاش _ 43","2026-11-16 14:37:04","87","0","2026-11-16 14:37:04","1","0","0");
INSERT INTO journal_heads VALUES ("130","129","33","2026-11-16","89",NULL,"فاتورة ريسيت _ 89","2026-11-16 14:41:12","0","0","2026-11-16 14:41:12","1","0","0");
INSERT INTO journal_heads VALUES ("131","130","33","2026-11-16","90",NULL,"سند قبض كاش _ 44","2026-11-16 14:41:12","89","0","2026-11-16 14:41:12","1","0","0");
INSERT INTO journal_heads VALUES ("133","132","33","2026-11-16","91",NULL,"فاتورة ريسيت _ 91","2026-11-16 14:41:34","0","0","2026-11-16 14:41:34","1","0","0");
INSERT INTO journal_heads VALUES ("134","133","33","2026-11-16","92",NULL,"سند قبض كاش _ 45","2026-11-16 14:41:34","91","0","2026-11-16 14:41:34","1","0","0");
INSERT INTO journal_heads VALUES ("136","135","81","2026-11-16","93",NULL,"فاتورة ريسيت _ 93","2026-11-16 14:41:38","0","0","2026-11-16 14:41:38","1","0","0");
INSERT INTO journal_heads VALUES ("137","136","81","2026-11-16","94",NULL,"سند قبض كاش _ 46","2026-11-16 14:41:38","93","0","2026-11-16 14:41:38","1","0","0");
INSERT INTO journal_heads VALUES ("139","138","33","2026-11-16","95",NULL,"فاتورة ريسيت _ 95","2026-11-16 14:42:25","0","0","2026-11-16 14:42:25","1","0","0");
INSERT INTO journal_heads VALUES ("140","139","33","2026-11-16","96",NULL,"سند قبض كاش _ 47","2026-11-16 14:42:25","95","0","2026-11-16 14:42:25","1","0","0");
INSERT INTO journal_heads VALUES ("142","141","53","2026-11-16","97",NULL,"فاتورة ريسيت _ 97","2026-11-16 14:42:34","0","0","2026-11-16 14:42:34","1","0","0");
INSERT INTO journal_heads VALUES ("143","142","53","2026-11-16","98",NULL,"سند قبض كاش _ 48","2026-11-16 14:42:34","97","0","2026-11-16 14:42:34","1","0","0");
INSERT INTO journal_heads VALUES ("145","144","53","2026-11-16","99",NULL,"فاتورة ريسيت _ 99","2026-11-16 14:43:00","0","0","2026-11-16 14:43:00","1","0","0");
INSERT INTO journal_heads VALUES ("146","145","53","2026-11-16","100",NULL,"سند قبض كاش _ 49","2026-11-16 14:43:00","99","0","2026-11-16 14:43:00","1","0","0");
INSERT INTO journal_heads VALUES ("148","147","33","2026-11-16","101",NULL,"فاتورة ريسيت _ 101","2026-11-16 14:43:46","0","0","2026-11-16 14:43:46","1","0","0");
INSERT INTO journal_heads VALUES ("149","148","33","2026-11-16","102",NULL,"سند قبض كاش _ 50","2026-11-16 14:43:46","101","0","2026-11-16 14:43:46","1","0","0");
INSERT INTO journal_heads VALUES ("151","150","33","2026-11-16","103",NULL,"فاتورة ريسيت _ 103","2026-11-16 14:45:32","0","0","2026-11-16 14:45:32","1","0","0");
INSERT INTO journal_heads VALUES ("152","151","33","2026-11-16","104",NULL,"سند قبض كاش _ 51","2026-11-16 14:45:32","103","0","2026-11-16 14:45:32","1","0","0");
INSERT INTO journal_heads VALUES ("154","153","33","2026-11-16","105",NULL,"فاتورة ريسيت _ 105","2026-11-16 14:46:18","0","0","2026-11-16 14:46:18","1","0","0");
INSERT INTO journal_heads VALUES ("155","154","33","2026-11-16","106",NULL,"سند قبض كاش _ 52","2026-11-16 14:46:18","105","0","2026-11-16 14:46:18","1","0","0");
INSERT INTO journal_heads VALUES ("157","156","31","2026-11-16","107",NULL,"فاتورة ريسيت _ 107","2026-11-16 14:54:50","0","0","2026-11-16 14:54:50","1","0","0");
INSERT INTO journal_heads VALUES ("158","157","31","2026-11-16","108",NULL,"سند قبض كاش _ 53","2026-11-16 14:54:50","107","0","2026-11-16 14:54:50","1","0","0");
INSERT INTO journal_heads VALUES ("160","159","33","2026-11-16","109",NULL,"فاتورة ريسيت _ 109","2026-11-16 14:55:15","0","0","2026-11-16 14:55:15","1","0","0");
INSERT INTO journal_heads VALUES ("161","160","33","2026-11-16","110",NULL,"سند قبض كاش _ 54","2026-11-16 14:55:15","109","0","2026-11-16 14:55:15","1","0","0");
INSERT INTO journal_heads VALUES ("163","162","33","2026-11-16","111",NULL,"فاتورة ريسيت _ 111","2026-11-16 14:55:58","0","0","2026-11-16 14:55:58","1","0","0");
INSERT INTO journal_heads VALUES ("164","163","33","2026-11-16","112",NULL,"سند قبض كاش _ 55","2026-11-16 14:55:58","111","0","2026-11-16 14:55:58","1","0","0");
INSERT INTO journal_heads VALUES ("166","165","33","2026-11-16","113",NULL,"فاتورة ريسيت _ 113","2026-11-16 14:57:30","0","0","2026-11-16 14:57:30","1","0","0");
INSERT INTO journal_heads VALUES ("167","166","33","2026-11-16","114",NULL,"سند قبض كاش _ 56","2026-11-16 14:57:30","113","0","2026-11-16 14:57:30","1","0","0");
INSERT INTO journal_heads VALUES ("169","168","33","2026-11-16","115",NULL,"فاتورة ريسيت _ 115","2026-11-16 14:58:10","0","0","2026-11-16 14:58:10","1","0","0");
INSERT INTO journal_heads VALUES ("170","169","33","2026-11-16","116",NULL,"سند قبض كاش _ 57","2026-11-16 14:58:10","115","0","2026-11-16 14:58:10","1","0","0");
INSERT INTO journal_heads VALUES ("172","171","42","2026-11-16","117",NULL,"فاتورة ريسيت _ 117","2026-11-16 14:58:26","0","0","2026-11-16 14:58:26","1","0","0");
INSERT INTO journal_heads VALUES ("173","172","42","2026-11-16","118",NULL,"سند قبض كاش _ 58","2026-11-16 14:58:26","117","0","2026-11-16 14:58:26","1","0","0");
INSERT INTO journal_heads VALUES ("175","174","50","2026-11-16","119",NULL,"فاتورة ريسيت _ 119","2026-11-16 14:59:05","0","0","2026-11-16 14:59:05","1","0","0");
INSERT INTO journal_heads VALUES ("176","175","50","2026-11-16","120",NULL,"سند قبض كاش _ 59","2026-11-16 14:59:05","119","0","2026-11-16 14:59:05","1","0","0");
INSERT INTO journal_heads VALUES ("177","176","33","2026-11-16","121",NULL,"فاتورة ريسيت _ 121","2026-11-16 15:07:25","0","0","2026-11-16 15:07:25","1","0","0");
INSERT INTO journal_heads VALUES ("178","177","33","2026-11-16","122",NULL,"سند قبض كاش _ 60","2026-11-16 15:07:25","121","0","2026-11-16 15:07:25","1","0","0");
INSERT INTO journal_heads VALUES ("180","179","33","2026-11-16","123",NULL,"فاتورة ريسيت _ 123","2026-11-16 15:07:46","0","0","2026-11-16 15:07:46","1","0","0");
INSERT INTO journal_heads VALUES ("181","180","33","2026-11-16","124",NULL,"سند قبض كاش _ 61","2026-11-16 15:07:46","123","0","2026-11-16 15:07:46","1","0","0");
INSERT INTO journal_heads VALUES ("183","182","33","2026-11-16","125",NULL,"فاتورة ريسيت _ 125","2026-11-16 15:08:15","0","0","2026-11-16 15:08:15","1","0","0");
INSERT INTO journal_heads VALUES ("184","183","33","2026-11-16","126",NULL,"سند قبض كاش _ 62","2026-11-16 15:08:15","125","0","2026-11-16 15:08:15","1","0","0");
INSERT INTO journal_heads VALUES ("186","185","86","2026-11-16","127",NULL,"فاتورة ريسيت _ 127","2026-11-16 15:11:38","0","0","2026-11-16 15:11:38","1","0","0");
INSERT INTO journal_heads VALUES ("187","186","86","2026-11-16","128",NULL,"سند قبض كاش _ 63","2026-11-16 15:11:38","127","0","2026-11-16 15:11:38","1","0","0");
INSERT INTO journal_heads VALUES ("189","188","33","2026-11-16","129",NULL,"فاتورة ريسيت _ 129","2026-11-16 15:11:45","0","0","2026-11-16 15:11:45","1","0","0");
INSERT INTO journal_heads VALUES ("190","189","33","2026-11-16","130",NULL,"سند قبض كاش _ 64","2026-11-16 15:11:45","129","0","2026-11-16 15:11:45","1","0","0");
INSERT INTO journal_heads VALUES ("192","191","33","2026-11-16","131",NULL,"فاتورة ريسيت _ 131","2026-11-16 15:11:50","0","0","2026-11-16 15:11:50","1","0","0");
INSERT INTO journal_heads VALUES ("193","192","33","2026-11-16","132",NULL,"سند قبض كاش _ 65","2026-11-16 15:11:50","131","0","2026-11-16 15:11:50","1","0","0");
INSERT INTO journal_heads VALUES ("195","194","33","2026-11-16","133",NULL,"فاتورة ريسيت _ 133","2026-11-16 15:21:52","0","0","2026-11-16 15:21:52","1","0","0");
INSERT INTO journal_heads VALUES ("196","195","33","2026-11-16","134",NULL,"سند قبض كاش _ 66","2026-11-16 15:21:52","133","0","2026-11-16 15:21:52","1","0","0");
INSERT INTO journal_heads VALUES ("198","197","33","2026-11-16","135",NULL,"فاتورة ريسيت _ 135","2026-11-16 15:32:46","0","0","2026-11-16 15:32:46","1","0","0");
INSERT INTO journal_heads VALUES ("199","198","33","2026-11-16","136",NULL,"سند قبض كاش _ 67","2026-11-16 15:32:46","135","0","2026-11-16 15:32:46","1","0","0");
INSERT INTO journal_heads VALUES ("201","200","50","2026-11-16","137",NULL,"فاتورة ريسيت _ 137","2026-11-16 15:33:57","0","0","2026-11-16 15:33:57","1","0","0");
INSERT INTO journal_heads VALUES ("202","201","50","2026-11-16","138",NULL,"سند قبض كاش _ 68","2026-11-16 15:33:57","137","0","2026-11-16 15:33:57","1","0","0");
INSERT INTO journal_heads VALUES ("203","202","133","2026-11-16","139",NULL,"فاتورة ريسيت _ 139","2026-11-16 15:35:10","0","0","2026-11-16 15:35:10","1","0","0");
INSERT INTO journal_heads VALUES ("204","203","133","2026-11-16","140",NULL,"سند قبض كاش _ 69","2026-11-16 15:35:10","139","0","2026-11-16 15:35:10","1","0","0");
INSERT INTO journal_heads VALUES ("206","205","11","2026-11-16","141",NULL,"فاتورة ريسيت _ 141","2026-11-16 15:35:25","0","0","2026-11-16 15:35:25","1","0","0");
INSERT INTO journal_heads VALUES ("207","206","11","2026-11-16","142",NULL,"سند قبض كاش _ 70","2026-11-16 15:35:25","141","0","2026-11-16 15:35:25","1","0","0");
INSERT INTO journal_heads VALUES ("209","208","61","2026-11-16","143",NULL,"فاتورة ريسيت _ 143","2026-11-16 15:36:04","0","0","2026-11-16 15:36:04","1","0","0");
INSERT INTO journal_heads VALUES ("210","209","61","2026-11-16","144",NULL,"سند قبض كاش _ 71","2026-11-16 15:36:04","143","0","2026-11-16 15:36:04","1","0","0");
INSERT INTO journal_heads VALUES ("212","211","33","2026-11-16","145",NULL,"فاتورة ريسيت _ 145","2026-11-16 15:45:41","0","0","2026-11-16 15:45:41","1","0","0");
INSERT INTO journal_heads VALUES ("213","212","33","2026-11-16","146",NULL,"سند قبض كاش _ 72","2026-11-16 15:45:41","145","0","2026-11-16 15:45:41","1","0","0");
INSERT INTO journal_heads VALUES ("215","214","33","2026-11-16","147",NULL,"فاتورة ريسيت _ 147","2026-11-16 21:04:41","0","0","2026-11-16 21:04:41","1","0","0");
INSERT INTO journal_heads VALUES ("216","215","33","2026-11-16","148",NULL,"سند قبض كاش _ 73","2026-11-16 21:04:41","147","0","2026-11-16 21:04:41","1","0","0");
INSERT INTO journal_heads VALUES ("218","217","33","2026-11-16","149",NULL,"فاتورة ريسيت _ 149","2026-11-16 21:05:44","0","0","2026-11-16 21:05:44","1","0","0");
INSERT INTO journal_heads VALUES ("219","218","33","2026-11-16","150",NULL,"سند قبض كاش _ 74","2026-11-16 21:05:44","149","0","2026-11-16 21:05:44","1","0","0");
INSERT INTO journal_heads VALUES ("221","220","103","2026-11-16","151",NULL,"فاتورة ريسيت _ 151","2026-11-16 21:07:00","0","0","2026-11-16 21:07:00","1","0","0");
INSERT INTO journal_heads VALUES ("222","221","103","2026-11-16","152",NULL,"سند قبض كاش _ 75","2026-11-16 21:07:00","151","0","2026-11-16 21:07:00","1","0","0");
INSERT INTO journal_heads VALUES ("224","223","81","2026-11-16","153",NULL,"فاتورة ريسيت _ 153","2026-11-16 21:15:13","0","0","2026-11-16 21:15:13","1","0","0");
INSERT INTO journal_heads VALUES ("225","224","81","2026-11-16","154",NULL,"سند قبض صرافة _ 76","2026-11-16 21:15:13","153","0","2026-11-16 21:15:13","1","0","0");
INSERT INTO journal_heads VALUES ("227","226","31","2026-11-16","155",NULL,"فاتورة ريسيت _ 155","2026-11-16 21:15:17","0","0","2026-11-16 21:15:17","1","0","0");
INSERT INTO journal_heads VALUES ("228","227","31","2026-11-16","156",NULL,"سند قبض كاش _ 77","2026-11-16 21:15:17","155","0","2026-11-16 21:15:17","1","0","0");
INSERT INTO journal_heads VALUES ("230","229","33","2026-11-18","157",NULL,"فاتورة ريسيت _ 157","2026-11-18 15:39:22","0","0","2026-11-18 15:39:22","1","0","0");
INSERT INTO journal_heads VALUES ("231","230","33","2026-11-18","158",NULL,"سند قبض كاش _ 78","2026-11-18 15:39:22","157","0","2026-11-18 15:39:22","1","0","0");
INSERT INTO journal_heads VALUES ("233","232","70","2026-11-18","159",NULL,"فاتورة مبيعات POS رقم 79","2026-11-18 15:40:03","0","0","2026-11-18 15:40:03","1","0","0");
INSERT INTO journal_heads VALUES ("234","233","70","2026-11-18","160",NULL,"سند قبض من فاتورة POS رقم 79","2026-11-18 15:40:03","159","0","2026-11-18 15:40:03","1","0","0");
INSERT INTO journal_heads VALUES ("235","234","70","2026-11-18","161",NULL,"فاتورة مبيعات POS رقم 80","2026-11-18 15:40:13","0","0","2026-11-18 15:40:13","1","0","0");
INSERT INTO journal_heads VALUES ("236","235","70","2026-11-18","162",NULL,"سند قبض من فاتورة POS رقم 80","2026-11-18 15:40:13","161","0","2026-11-18 15:40:13","1","0","0");
INSERT INTO journal_heads VALUES ("237","236","70","2026-11-18","163",NULL,"فاتورة مبيعات POS رقم 81","2026-11-18 15:40:23","0","0","2026-11-18 15:40:23","1","0","0");
INSERT INTO journal_heads VALUES ("238","237","70","2026-11-18","164",NULL,"سند قبض من فاتورة POS رقم 81","2026-11-18 15:40:23","163","0","2026-11-18 15:40:23","1","0","0");
INSERT INTO journal_heads VALUES ("239","238","70","2026-11-18","165",NULL,"فاتورة مبيعات POS رقم 82","2026-11-18 15:40:46","0","0","2026-11-18 15:40:46","1","0","0");
INSERT INTO journal_heads VALUES ("240","239","70","2026-11-18","166",NULL,"سند قبض من فاتورة POS رقم 82","2026-11-18 15:40:46","165","0","2026-11-18 15:40:46","1","0","0");
INSERT INTO journal_heads VALUES ("241","240","33","2026-11-18","167",NULL,"فاتورة ريسيت _ 167","2026-11-18 16:04:31","0","0","2026-11-18 16:04:31","1","0","0");
INSERT INTO journal_heads VALUES ("242","241","33","2026-11-18","168",NULL,"سند قبض كاش _ 83","2026-11-18 16:04:31","167","0","2026-11-18 16:04:31","1","0","0");
INSERT INTO journal_heads VALUES ("244","243","42","2026-11-18","169",NULL,"فاتورة ريسيت _ 169","2026-11-18 16:05:32","0","0","2026-11-18 16:05:32","1","0","0");
INSERT INTO journal_heads VALUES ("245","244","42","2026-11-18","170",NULL,"سند قبض كاش _ 84","2026-11-18 16:05:32","169","0","2026-11-18 16:05:32","1","0","0");
INSERT INTO journal_heads VALUES ("247","246","33","2026-11-18","171",NULL,"فاتورة ريسيت _ 171","2026-11-18 16:06:12","0","0","2026-11-18 16:06:12","1","0","0");
INSERT INTO journal_heads VALUES ("248","247","33","2026-11-18","172",NULL,"سند قبض كاش _ 85","2026-11-18 16:06:12","171","0","2026-11-18 16:06:12","1","0","0");
INSERT INTO journal_heads VALUES ("250","249","16000","2026-11-19",NULL,NULL,"دفع تجريبي","2026-11-19 14:14:42","0","1","2026-11-19 14:15:37","1","0","0");
INSERT INTO journal_heads VALUES ("251","250","33","2026-11-21","173",NULL,"فاتورة ريسيت _ 173","2026-11-21 12:50:35","0","0","2026-11-21 12:50:35","1","0","0");
INSERT INTO journal_heads VALUES ("252","251","33","2026-11-21","174",NULL,"سند قبض كاش _ 86","2026-11-21 12:50:35","173","0","2026-11-21 12:50:35","1","0","0");
INSERT INTO journal_heads VALUES ("254","253","53","2026-11-21","175",NULL,"فاتورة ريسيت _ 175","2026-11-21 12:50:38","0","0","2026-11-21 12:50:38","1","0","0");
INSERT INTO journal_heads VALUES ("255","254","53","2026-11-21","176",NULL,"سند قبض كاش _ 87","2026-11-21 12:50:38","175","0","2026-11-21 12:50:38","1","0","0");
INSERT INTO journal_heads VALUES ("257","256","103","2026-11-21","177",NULL,"فاتورة ريسيت _ 177","2026-11-21 12:50:43","0","0","2026-11-21 12:50:43","1","0","0");
INSERT INTO journal_heads VALUES ("258","257","103","2026-11-21","178",NULL,"سند قبض كاش _ 88","2026-11-21 12:50:43","177","0","2026-11-21 12:50:43","1","0","0");
INSERT INTO journal_heads VALUES ("260","259","55","2026-11-21","179",NULL,"فاتورة ريسيت _ 179","2026-11-21 12:50:48","0","0","2026-11-21 12:50:48","1","0","0");
INSERT INTO journal_heads VALUES ("261","260","55","2026-11-21","180",NULL,"سند قبض كاش _ 89","2026-11-21 12:50:48","179","0","2026-11-21 12:50:48","1","0","0");
INSERT INTO journal_heads VALUES ("263","262","90","2026-11-21","181",NULL,"فاتورة مبيعات POS رقم 90","2026-11-21 12:52:58","0","0","2026-11-21 12:52:58","1","0","0");
INSERT INTO journal_heads VALUES ("264","263","90","2026-11-21","182",NULL,"سند قبض من فاتورة POS رقم 90","2026-11-21 12:52:58","181","0","2026-11-21 12:52:58","1","0","0");
INSERT INTO journal_heads VALUES ("265","264","90","2026-11-21","183",NULL,"فاتورة مردود مبيعات رقم 1","2026-11-21 12:53:05","0","0","2026-11-21 12:53:05","1","0","0");
INSERT INTO journal_heads VALUES ("266","265","90","2026-11-21","184",NULL,"سند صرف (رد) من فاتورة POS رقم 1","2026-11-21 12:53:05","183","0","2026-11-21 12:53:05","1","0","0");
INSERT INTO journal_heads VALUES ("271","270","70","2026-11-21","185",NULL,"فاتورة مبيعات POS رقم 91","2026-11-21 15:50:38","0","0","2026-11-21 15:50:38","1","0","0");
INSERT INTO journal_heads VALUES ("272","271","70","2026-11-21","186",NULL,"سند قبض كاش _ 91","2026-11-21 15:50:38","185","0","2026-11-21 15:50:38","1","0","0");
INSERT INTO journal_heads VALUES ("273","272","353","2026-11-21","187",NULL,"فاتورة مبيعات POS رقم 92","2026-11-21 17:13:55","0","0","2026-11-21 17:13:55","1","0","0");
INSERT INTO journal_heads VALUES ("274","273","353","2026-11-21","188",NULL,"سند قبض كاش _ 92","2026-11-21 17:13:55","187","0","2026-11-21 17:13:55","1","0","0");
INSERT INTO journal_heads VALUES ("276","275","90","2026-11-21","189",NULL,"فاتورة مبيعات POS رقم 93","2026-11-21 17:16:41","0","0","2026-11-21 17:16:41","1","0","0");
INSERT INTO journal_heads VALUES ("277","276","90","2026-11-21","190",NULL,"سند قبض كاش _ 93","2026-11-21 17:16:41","189","0","2026-11-21 17:16:41","1","0","0");
INSERT INTO journal_heads VALUES ("278","277","70","2026-11-21","191",NULL,"فاتورة مردود مبيعات رقم 2","2026-11-21 17:35:13","0","0","2026-11-21 17:35:13","1","0","0");
INSERT INTO journal_heads VALUES ("279","278","70","2026-11-21","192",NULL,"سند دفع كاش _ 2","2026-11-21 17:35:13","191","0","2026-11-21 17:35:13","1","0","0");
INSERT INTO journal_heads VALUES ("280","279","50","2026-11-21","193",NULL,"فاتورة مبيعات POS رقم 94","2026-11-21 17:35:57","0","0","2026-11-21 17:35:57","1","0","0");
INSERT INTO journal_heads VALUES ("281","280","50","2026-11-21","194",NULL,"سند قبض كاش _ 94","2026-11-21 17:35:57","193","0","2026-11-21 17:35:57","1","0","0");
INSERT INTO journal_heads VALUES ("282","281","123","2026-11-21","195",NULL,"فاتورة مردود مبيعات رقم 3","2026-11-21 17:36:10","0","0","2026-11-21 17:36:10","1","0","0");
INSERT INTO journal_heads VALUES ("283","282","123","2026-11-21","196",NULL,"سند دفع كاش _ 3","2026-11-21 17:36:10","195","0","2026-11-21 17:36:10","1","0","0");
INSERT INTO journal_heads VALUES ("285","284","125","2026-11-22","197",NULL,"فاتورة ريسيت _ 197","2026-11-22 19:12:43","0","0","2026-11-22 19:12:43","1","0","0");
INSERT INTO journal_heads VALUES ("286","285","125","2026-11-22","198",NULL,"سند قبض كاش _ 95","2026-11-22 19:12:43","197","0","2026-11-22 19:12:43","1","0","0");
INSERT INTO journal_heads VALUES ("288","287","103","2026-11-22","199",NULL,"فاتورة ريسيت _ 199","2026-11-22 19:13:04","0","0","2026-11-22 19:13:04","1","0","0");
INSERT INTO journal_heads VALUES ("289","288","103","2026-11-22","200",NULL,"سند قبض كاش _ 96","2026-11-22 19:13:04","199","0","2026-11-22 19:13:04","1","0","0");
INSERT INTO journal_heads VALUES ("293","290","33","2026-11-22","203",NULL,"فاتورة ريسيت _ 203","2026-11-22 19:24:59","0","0","2026-11-22 19:24:59","1","0","0");
INSERT INTO journal_heads VALUES ("294","291","33","2026-11-22","204",NULL,"سند قبض كاش _ 97","2026-11-22 19:24:59","203","0","2026-11-22 19:24:59","1","0","0");
INSERT INTO journal_heads VALUES ("296","293","33","2026-11-22","205",NULL,"فاتورة ريسيت _ 205","2026-11-22 19:31:48","0","0","2026-11-22 19:31:48","1","0","0");
INSERT INTO journal_heads VALUES ("297","294","33","2026-11-22","206",NULL,"سند قبض كاش _ 98","2026-11-22 19:31:48","205","0","2026-11-22 19:31:48","1","0","0");
INSERT INTO journal_heads VALUES ("299","296","33","2026-11-22","207",NULL,"فاتورة ريسيت _ 207","2026-11-22 19:34:35","0","0","2026-11-22 19:34:35","1","0","0");
INSERT INTO journal_heads VALUES ("300","297","33","2026-11-22","208",NULL,"سند قبض كاش _ 99","2026-11-22 19:34:35","207","0","2026-11-22 19:34:35","1","0","0");
INSERT INTO journal_heads VALUES ("302","299","33","2026-11-22","209",NULL,"فاتورة ريسيت _ 209","2026-11-22 19:35:11","0","0","2026-11-22 19:35:11","1","0","0");
INSERT INTO journal_heads VALUES ("303","300","33","2026-11-22","210",NULL,"سند قبض كاش _ 100","2026-11-22 19:35:11","209","0","2026-11-22 19:35:11","1","0","0");
INSERT INTO journal_heads VALUES ("305","302","2991","2026-11-22","211",NULL,"فاتورة ريسيت _ 211","2026-11-22 19:44:00","0","0","2026-11-22 19:44:00","1","0","0");
INSERT INTO journal_heads VALUES ("306","303","2991","2026-11-22","212",NULL,"سند قبض كاش _ 101","2026-11-22 19:44:00","211","0","2026-11-22 19:44:00","1","0","0");
INSERT INTO journal_heads VALUES ("308","305","33","2026-11-22","213",NULL,"فاتورة ريسيت _ 213","2026-11-22 19:44:29","0","0","2026-11-22 19:44:29","1","0","0");
INSERT INTO journal_heads VALUES ("309","306","33","2026-11-22","214",NULL,"سند قبض كاش _ 102","2026-11-22 19:44:30","213","0","2026-11-22 19:44:30","1","0","0");
INSERT INTO journal_heads VALUES ("311","308","33","2026-11-22","215",NULL,"فاتورة ريسيت _ 215","2026-11-22 20:26:45","0","0","2026-11-22 20:26:45","1","0","0");
INSERT INTO journal_heads VALUES ("312","309","33","2026-11-22","216",NULL,"سند قبض كاش _ 103","2026-11-22 20:26:45","215","0","2026-11-22 20:26:45","1","0","0");
INSERT INTO journal_heads VALUES ("321","316","154","2026-11-22","217",NULL,"فاتورة ريسيت _ 217","2026-11-22 22:31:09","0","0","2026-11-22 22:31:09","1","0","0");
INSERT INTO journal_heads VALUES ("322","317","154","2026-11-22","220",NULL,"سند قبض كاش _ 106","2026-11-22 22:31:09","217","0","2026-11-22 22:31:09","1","0","0");
INSERT INTO journal_heads VALUES ("324","319","55","2026-11-22","218",NULL,"فاتورة ريسيت _ 218","2026-11-22 22:31:21","0","0","2026-11-22 22:31:21","1","0","0");
INSERT INTO journal_heads VALUES ("325","320","55","2026-11-22","221",NULL,"سند قبض كاش _ 106","2026-11-22 22:31:21","218","0","2026-11-22 22:31:21","1","0","0");
INSERT INTO journal_heads VALUES ("327","322","33","2026-11-21","187","20","قيد تكلفة مبيعات _ 187","2026-11-23 13:27:06","0","0","2026-11-23 13:27:06","1","0","0");
INSERT INTO journal_heads VALUES ("328","323","33","2026-11-21","195","20","قيد تكلفة مردود مبيعات _ 195","2026-11-23 13:27:06","0","0","2026-11-23 13:27:06","1","0","0");
INSERT INTO journal_heads VALUES ("329","324","55","2026-11-22","197","20","قيد تكلفة مبيعات _ 197","2026-11-23 13:27:06","0","0","2026-11-23 13:27:06","1","0","0");
INSERT INTO journal_heads VALUES ("330","325","33","2026-11-22","199","20","قيد تكلفة مبيعات _ 199","2026-11-23 13:27:06","0","0","2026-11-23 13:27:06","1","0","0");
INSERT INTO journal_heads VALUES ("331","326","33","2026-11-22","203","20","قيد تكلفة مبيعات _ 203","2026-11-23 13:27:06","0","0","2026-11-23 13:27:06","1","0","0");
INSERT INTO journal_heads VALUES ("332","327","33","2026-11-22","205","20","قيد تكلفة مبيعات _ 205","2026-11-23 13:27:06","0","0","2026-11-23 13:27:06","1","0","0");
INSERT INTO journal_heads VALUES ("333","328","33","2026-11-22","207","20","قيد تكلفة مبيعات _ 207","2026-11-23 13:27:06","0","0","2026-11-23 13:27:06","1","0","0");
INSERT INTO journal_heads VALUES ("334","329","33","2026-11-22","209","20","قيد تكلفة مبيعات _ 209","2026-11-23 13:27:06","0","0","2026-11-23 13:27:06","1","0","0");
INSERT INTO journal_heads VALUES ("335","330","99","2026-11-22","211","20","قيد تكلفة مبيعات _ 211","2026-11-23 13:27:06","0","0","2026-11-23 13:27:06","1","0","0");
INSERT INTO journal_heads VALUES ("336","331","33","2026-11-22","213","20","قيد تكلفة مبيعات _ 213","2026-11-23 13:27:06","0","0","2026-11-23 13:27:06","1","0","0");
INSERT INTO journal_heads VALUES ("337","332","33","2026-11-22","215","20","قيد تكلفة مبيعات _ 215","2026-11-23 13:27:06","0","0","2026-11-23 13:27:06","1","0","0");
INSERT INTO journal_heads VALUES ("338","333","154","2026-11-22","217","20","قيد تكلفة مبيعات _ 217","2026-11-23 13:27:06","0","0","2026-11-23 13:27:06","1","0","0");
INSERT INTO journal_heads VALUES ("339","334","44","2026-11-22","218","20","قيد تكلفة مبيعات _ 218","2026-11-23 13:27:06","0","0","2026-11-23 13:27:06","1","0","0");
INSERT INTO journal_heads VALUES ("340","335","33","2026-11-23","222",NULL,"فاتورة ريسيت _ 222","2026-11-23 15:43:10","0","0","2026-11-23 15:43:10","1","0","0");
INSERT INTO journal_heads VALUES ("341","336","33","2026-11-23","223",NULL,"سند قبض كاش _ 106","2026-11-23 15:43:10","222","0","2026-11-23 15:43:10","1","0","0");
INSERT INTO journal_heads VALUES ("342","337","33","2026-11-23","222","20","قيد تكلفة مبيعات _ 222","2026-11-23 15:43:10","0","0","2026-11-23 15:43:10","1","0","0");
INSERT INTO journal_heads VALUES ("343","338","33","2026-11-23","224",NULL,"فاتورة ريسيت _ 224","2026-11-23 15:43:49","0","0","2026-11-23 15:43:49","1","0","0");
INSERT INTO journal_heads VALUES ("344","339","33","2026-11-23","225",NULL,"سند قبض كاش _ 107","2026-11-23 15:43:49","224","0","2026-11-23 15:43:49","1","0","0");
INSERT INTO journal_heads VALUES ("345","340","33","2026-11-23","224","20","قيد تكلفة مبيعات _ 224","2026-11-23 15:43:49","0","0","2026-11-23 15:43:49","1","0","0");
INSERT INTO journal_heads VALUES ("346","341","33","2026-11-23","226",NULL,"فاتورة ريسيت _ 226","2026-11-23 15:44:14","0","0","2026-11-23 15:44:14","1","0","0");
INSERT INTO journal_heads VALUES ("347","342","33","2026-11-23","227",NULL,"سند قبض كاش _ 108","2026-11-23 15:44:14","226","0","2026-11-23 15:44:14","1","0","0");
INSERT INTO journal_heads VALUES ("348","343","33","2026-11-23","226","20","قيد تكلفة مبيعات _ 226","2026-11-23 15:44:14","0","0","2026-11-23 15:44:14","1","0","0");
INSERT INTO journal_heads VALUES ("349","344","33","2026-11-23","228",NULL,"فاتورة ريسيت _ 228","2026-11-23 15:45:26","0","0","2026-11-23 15:45:26","1","0","0");
INSERT INTO journal_heads VALUES ("350","345","33","2026-11-23","229",NULL,"سند قبض كاش _ 109","2026-11-23 15:45:26","228","0","2026-11-23 15:45:26","1","0","0");
INSERT INTO journal_heads VALUES ("351","346","33","2026-11-23","228","20","قيد تكلفة مبيعات _ 228","2026-11-23 15:45:26","0","0","2026-11-23 15:45:26","1","0","0");
INSERT INTO journal_heads VALUES ("352","347","221","2026-11-23","230",NULL,"فاتورة ريسيت _ 230","2026-11-23 15:59:43","0","0","2026-11-23 15:59:43","1","0","0");
INSERT INTO journal_heads VALUES ("353","348","221","2026-11-23","231",NULL,"سند قبض كاش _ 110","2026-11-23 15:59:43","230","0","2026-11-23 15:59:43","1","0","0");
INSERT INTO journal_heads VALUES ("354","349","11","2026-11-23","230","20","قيد تكلفة مبيعات _ 230","2026-11-23 15:59:43","0","0","2026-11-23 15:59:43","1","0","0");



DROP TABLE IF EXISTS journal_tybes;

CREATE TABLE `journal_tybes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `journal_id` int(11) DEFAULT NULL,
  `jname` varchar(222) DEFAULT NULL,
  `jtext` varchar(222) DEFAULT NULL,
  `info` varchar(222) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO journal_tybes VALUES ("1","1","purchases","يومية المقبوضات",NULL,"2024-03-14 02:34:38","2024-03-14 02:34:38","0","0","0");
INSERT INTO journal_tybes VALUES ("2","2","sales","يومية المدفوعات",NULL,"2024-03-14 02:34:38","2024-03-14 02:34:38","0","0","0");
INSERT INTO journal_tybes VALUES ("3","3","Payments","المبيعات",NULL,"2024-03-14 02:34:38","2024-03-14 02:34:38","0","0","0");
INSERT INTO journal_tybes VALUES ("4","4","receipts","يومية المشتريات",NULL,"2024-03-14 02:34:38","2024-03-14 02:34:38","0","0","0");
INSERT INTO journal_tybes VALUES ("5","5","Accrueds","ايراد مستحق",NULL,"2024-03-14 02:34:38","2024-03-14 02:34:38","0","0","0");
INSERT INTO journal_tybes VALUES ("6","6","Accrueds","خصم مكتسب",NULL,"2024-03-14 02:34:38","2024-03-14 02:34:38","0","0","0");
INSERT INTO journal_tybes VALUES ("7","7","Accrueds","خصم مسموح به",NULL,"2024-03-14 02:34:38","2024-03-14 02:34:38","0","0","0");
INSERT INTO journal_tybes VALUES ("8","8","journal","القيود اليومية",NULL,"2024-03-14 02:34:38","2024-03-14 02:34:38","0","0","0");



DROP TABLE IF EXISTS karta;

CREATE TABLE `karta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kname` varchar(200) NOT NULL,
  `info` varchar(200) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NULL DEFAULT current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE IF EXISTS kbis;

CREATE TABLE `kbis` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kname` varchar(100) NOT NULL,
  `info` varchar(100) DEFAULT NULL,
  `user` int(11) DEFAULT 1,
  `isdeleted` tinyint(1) DEFAULT 0,
  `crtime` datetime DEFAULT current_timestamp(),
  `mdtime` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `ktybe` varchar(100) DEFAULT NULL,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO kbis VALUES ("1","معدل الانجاز","المهمات المكتملة/ المهمات الموكلة","1","0","2024-07-24 20:10:58","2024-07-24 20:25:02","الانتاجية","0","0");
INSERT INTO kbis VALUES ("2","معدل العمل الفعلي","وقت العمل الفعلي/وقت العمل","1","0","2024-07-24 20:24:47","2024-07-24 20:24:47","الانتاجية","0","0");
INSERT INTO kbis VALUES ("3","معدل الاخطاء","100% بدون اخطاء  ||    0% اخطاء اكثر من المسموح","1","0","2024-07-24 20:26:59","2024-07-24 20:26:59","","0","0");
INSERT INTO kbis VALUES ("4","معدل جودة المخرجات","يتم التقييم من ادارة الجودة","1","0","2024-07-24 20:27:55","2024-07-24 20:27:55","الجودة","0","0");
INSERT INTO kbis VALUES ("5","معدل الحضور","الحضور بالساعات / الساعات المقررة","1","0","2024-07-24 20:29:18","2024-07-24 20:29:18","الالتزام","0","0");
INSERT INTO kbis VALUES ("6","معدل التطور","احساب المهارات المضافة شهريا","1","0","2024-07-24 20:30:04","2024-07-24 20:30:04","التطوير","0","0");
INSERT INTO kbis VALUES ("7","تقييم الزملاء","يتم من خلال استطلاعات
","1","0","2024-07-24 20:32:32","2024-07-24 20:32:32","العمل الجماعي","0","0");
INSERT INTO kbis VALUES ("8","معدل المشاركة في الاجتماعات","المشاركة / الاجتماعات","1","0","2024-07-24 20:35:16","2024-07-24 20:35:16","العمل الجماعي","0","0");
INSERT INTO kbis VALUES ("9","تقييم القائد","تقييم الفريق للقائد","1","0","2024-07-24 20:37:33","2024-07-24 20:37:33","المديرين","0","0");
INSERT INTO kbis VALUES ("10","نسبه الحقيق للفريق","نسبة تحقيق الاهداف .. لمتخذي القرار","1","0","2024-07-24 20:38:11","2024-07-24 20:38:11","المديرين","0","0");
INSERT INTO kbis VALUES ("11","وقت الاستجابة","يتم عن طريق التغذية العكسية","1","0","2024-07-24 20:43:24","2024-07-24 20:43:24","خدمة العملاء","0","0");
INSERT INTO kbis VALUES ("12","معدل حل المشكلات","المشكلات المحلولة / عدد المشكلات","1","0","2024-07-24 20:44:03","2024-07-24 20:44:03","خدمة العملاء","0","0");
INSERT INTO kbis VALUES ("13","نسبة رضا العميل","العملاء الراضين / عدد عملاء الاستطلاع","1","0","2024-07-24 20:44:36","2024-07-24 20:44:36","خدمة العملاء","0","0");
INSERT INTO kbis VALUES ("14","معدل تقليل التكاليف","تقييم المشرف","1","0","2024-07-24 20:46:07","2024-07-24 20:46:07","الكفاءة التشغيلية","0","0");
INSERT INTO kbis VALUES ("15","استغلال الموارد","تقييم المديرين","1","0","2024-07-24 20:49:12","2024-07-24 20:49:12","الكفاءة التشغيلية","0","0");
INSERT INTO kbis VALUES ("16","عدد الافكار الجديدة المنفذه","عدد الافكار المنفذه / عدد الافكار الجديدة المقدمة","1","0","2024-07-24 21:03:59","2024-07-24 21:03:59","الابتكار","0","0");
INSERT INTO kbis VALUES ("17","اه","","1","0","2026-02-03 14:50:44","2026-02-03 14:50:44","بدوي","0","0");



DROP TABLE IF EXISTS my_news;

CREATE TABLE `my_news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `img` varchar(250) DEFAULT NULL,
  `tags` varchar(250) DEFAULT NULL,
  `content` text NOT NULL,
  `user` int(11) NOT NULL DEFAULT 1,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE IF EXISTS myinstallments;

CREATE TABLE `myinstallments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cl_id` int(11) NOT NULL,
  `rent_id` int(11) NOT NULL,
  `contract` int(11) DEFAULT 0,
  `ins_value` double NOT NULL DEFAULT 0,
  `ins_date` date NOT NULL,
  `ins_case` int(11) NOT NULL,
  `ins_paid` double NOT NULL,
  `voucher` int(11) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `info` varchar(250) DEFAULT NULL,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `cl_id` (`cl_id`),
  KEY `rent_id` (`rent_id`),
  KEY `contract` (`contract`),
  CONSTRAINT `myinstallments_ibfk_1` FOREIGN KEY (`cl_id`) REFERENCES `acc_head` (`id`),
  CONSTRAINT `myinstallments_ibfk_2` FOREIGN KEY (`rent_id`) REFERENCES `acc_head` (`id`),
  CONSTRAINT `myinstallments_ibfk_3` FOREIGN KEY (`contract`) REFERENCES `myrents` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE IF EXISTS myitems;

CREATE TABLE `myitems` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `iname` varchar(200) NOT NULL,
  `name2` varchar(200) DEFAULT NULL,
  `code` int(11) DEFAULT NULL,
  `salesqty` double DEFAULT 1,
  `barcode` varchar(25) DEFAULT NULL,
  `itmqty` double NOT NULL DEFAULT 0,
  `info` varchar(250) DEFAULT NULL,
  `market_price` float NOT NULL DEFAULT 0,
  `cost_price` float NOT NULL DEFAULT 0,
  `last_price` int(11) NOT NULL DEFAULT 0,
  `price1` float NOT NULL DEFAULT 0,
  `price2` float NOT NULL DEFAULT 0,
  `price3` float NOT NULL,
  `group1` int(11) NOT NULL DEFAULT 0,
  `group2` int(11) NOT NULL DEFAULT 0,
  `group3` int(11) NOT NULL DEFAULT 0,
  `isdeleted` tinyint(1) DEFAULT 0,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user` int(11) NOT NULL DEFAULT 1,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  `manual_price_edit` tinyint(1) DEFAULT 0,
  `is_assembly` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'صنف مجمع له وصفة تصنيع',
  PRIMARY KEY (`id`),
  UNIQUE KEY `iname` (`iname`),
  KEY `idx_myitems_iname` (`iname`),
  KEY `idx_myitems_name2` (`name2`),
  KEY `idx_myitems_barcode` (`barcode`),
  KEY `idx_myitems_isdeleted` (`isdeleted`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO myitems VALUES ("1","تجريبي","","1","1","1","14974","","0","11","0","11","0","0","1","10","0","0","2026-11-11 17:02:57","2026-11-23 16:18:00","1","0","0","0","0");
INSERT INTO myitems VALUES ("2","تجريبي 2","","2","1","2","100","","0","22","0","22","0","0","1","10","0","0","2026-11-11 17:03:09","2026-11-23 16:18:00","1","0","0","0","0");
INSERT INTO myitems VALUES ("3","ساندوتش فراخ","","3","1","3","18","","0","0","0","0","0","0","1","10","0","0","2026-11-14 21:40:03","2026-11-23 16:18:00","1","0","0","0","0");
INSERT INTO myitems VALUES ("4","قهوة تركي","","4","1","4","5","","0","0","0","20","0","0","1","10","0","0","2026-11-14 21:40:21","2026-11-23 16:18:00","1","0","0","0","0");
INSERT INTO myitems VALUES ("5","ساندوتش لحم","","5","1","5","-8","","0","0","0","50","0","0","1","10","0","0","2026-11-14 21:41:16","2026-11-23 16:18:00","1","0","0","0","0");
INSERT INTO myitems VALUES ("6","ساندوتش فراخ كرانشي","","6","1","6","2","","0","0","0","20","0","0","1","10","0","0","2026-11-14 21:41:33","2026-11-23 16:18:00","1","0","0","0","1");



DROP TABLE IF EXISTS myoper_det;

CREATE TABLE `myoper_det` (
  `oper_det_id` int(11) NOT NULL,
  `int_oper_det_date` int(11) DEFAULT NULL,
  `oper_head_id` int(11) DEFAULT NULL,
  `comp_id` int(11) DEFAULT NULL,
  `debit` decimal(26,4) DEFAULT NULL,
  `credit` decimal(26,4) DEFAULT NULL,
  `eng_debit` decimal(26,4) DEFAULT NULL,
  `eng_credit` decimal(26,4) DEFAULT NULL,
  `model_val` decimal(26,4) DEFAULT NULL,
  `def_val` decimal(26,4) DEFAULT NULL,
  `acc_id` int(11) DEFAULT NULL,
  `stor_id` int(11) DEFAULT NULL,
  `group_id` int(11) DEFAULT NULL,
  `man_id` int(11) DEFAULT NULL,
  `cost_center_id` int(11) DEFAULT NULL,
  `has_costed_link` tinyint(4) DEFAULT NULL,
  `is_not_active` tinyint(4) DEFAULT NULL,
  `notes` varchar(50) DEFAULT NULL,
  `mst_no` varchar(20) DEFAULT NULL,
  `mst_date` varchar(12) DEFAULT NULL,
  `balance_befor` decimal(26,4) DEFAULT NULL,
  `balance_after` decimal(26,4) DEFAULT NULL,
  `det_Currency_id` int(11) DEFAULT NULL,
  `det_Currency_unit_convert` decimal(12,6) DEFAULT NULL,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE IF EXISTS myoptions;

CREATE TABLE `myoptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `oname` varchar(30) NOT NULL,
  `info` varchar(250) DEFAULT NULL,
  `def_value` int(11) NOT NULL DEFAULT 0,
  `cur_value` int(11) NOT NULL DEFAULT 0,
  `op_tybe` int(11) NOT NULL DEFAULT 0,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO myoptions VALUES ("1","def_cl","العميل الافتراضي","24","24","1","2024-02-19 22:10:57","2024-02-19 22:21:14","0","0","0");
INSERT INTO myoptions VALUES ("2","def_prod","المورد الافتراضي","36","36","1","2024-02-19 22:10:57","2024-02-19 22:21:17","0","0","0");
INSERT INTO myoptions VALUES ("3","def_emp","الموظف الافتراضي","41","42","1","2024-02-19 22:10:57","2024-02-20 03:09:18","0","0","0");
INSERT INTO myoptions VALUES ("4","def_store","المخزن الافتراضي","27","27","1","2024-02-19 22:10:57","2024-02-19 22:21:23","0","0","0");
INSERT INTO myoptions VALUES ("5","def_fund","الصندوق الافتراضي","21","21","1","2024-02-19 22:10:57","2024-02-19 22:21:26","0","0","0");
INSERT INTO myoptions VALUES ("6","def_bank","البتك الافتراضي","39","39","1","2024-02-19 22:10:57","2024-02-19 22:21:31","0","0","0");
INSERT INTO myoptions VALUES ("7","def_store","المخزن الافتراضي","27","27","1","2024-02-19 22:10:57","2024-03-09 23:48:39","0","0","0");
INSERT INTO myoptions VALUES ("8","def_disc_acc1","حساب الخصم المكتسب الافتراضي","97","97","1","2024-02-19 22:10:57","2024-03-09 23:48:39","0","0","0");



DROP TABLE IF EXISTS mypatterns;

CREATE TABLE `mypatterns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pname` varchar(100) NOT NULL,
  `ptext` varchar(100) NOT NULL,
  `is_def` int(11) NOT NULL DEFAULT 0,
  `is_basic` int(11) NOT NULL DEFAULT 0,
  `ptybe` int(11) NOT NULL DEFAULT 4,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `info` varchar(100) DEFAULT NULL,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE IF EXISTS mypowers;

CREATE TABLE `mypowers` (
  `power_id` int(11) NOT NULL,
  `section_type_no` int(11) DEFAULT NULL,
  `power_name` varchar(100) DEFAULT NULL,
  `eng_power_name` varchar(100) DEFAULT NULL,
  `is_hide_in_casher` int(11) DEFAULT NULL,
  `level_no` tinyint(4) DEFAULT NULL,
  `is_for_view_only` tinyint(4) DEFAULT NULL,
  `power_code` varchar(100) DEFAULT NULL,
  `power_class` tinyint(4) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `col_index` int(11) DEFAULT NULL,
  `stoped` tinyint(4) DEFAULT NULL,
  `tmp_state_no` tinyint(4) DEFAULT NULL,
  `power_type` tinyint(4) DEFAULT NULL,
  `menu_type` tinyint(4) DEFAULT NULL,
  `def_state` varchar(20) DEFAULT NULL,
  `user_1` varchar(20) DEFAULT NULL,
  `kind` varchar(10) DEFAULT NULL,
  `is_on_my_thread` tinyint(4) DEFAULT NULL,
  `is_calling_from_main` tinyint(4) DEFAULT NULL,
  `calling_from` varchar(10) DEFAULT NULL,
  `edit_mode` tinyint(4) DEFAULT NULL,
  `frist_shown_id` varchar(10) DEFAULT NULL,
  `is_casher_from` tinyint(4) DEFAULT NULL,
  `is_op_paper` tinyint(4) DEFAULT NULL,
  `is_hiddin` tinyint(4) DEFAULT NULL,
  `prog_id` tinyint(4) DEFAULT NULL,
  `is_pure_kitchen` tinyint(4) DEFAULT NULL,
  `is_for_api` tinyint(4) DEFAULT NULL,
  `t_stamp` timestamp NULL DEFAULT NULL,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE IF EXISTS myrents;

CREATE TABLE `myrents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cl_id` int(11) NOT NULL,
  `rent_id` int(11) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `idintity` varchar(50) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `pay_tybe` int(11) NOT NULL DEFAULT 1,
  `r_value` double NOT NULL DEFAULT 0,
  `bnd1` varchar(250) DEFAULT NULL,
  `bnd2` varchar(250) DEFAULT NULL,
  `bnd3` varchar(250) DEFAULT NULL,
  `bnd4` varchar(250) DEFAULT NULL,
  `info` varchar(250) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `cl_id` (`cl_id`),
  KEY `rent_id` (`rent_id`),
  CONSTRAINT `myrents_ibfk_1` FOREIGN KEY (`cl_id`) REFERENCES `acc_head` (`id`),
  CONSTRAINT `myrents_ibfk_2` FOREIGN KEY (`rent_id`) REFERENCES `acc_head` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO myrents VALUES ("1","148","257","1223345665466","456565656576575","2025-05-03","2027-10-03","1","700","test item 1 ","","","",NULL,"2026-02-03 14:02:56","2026-02-03 14:06:51","0","0","0");



DROP TABLE IF EXISTS myunits;

CREATE TABLE `myunits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uname` varchar(60) NOT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO myunits VALUES ("2","ق","2026-11-11 16:59:55","2026-11-11 16:59:55","0","0","0");
INSERT INTO myunits VALUES ("3","قطعه","2026-11-11 16:59:58","2026-11-11 16:59:58","0","0","0");
INSERT INTO myunits VALUES ("4","ك","2026-11-14 21:34:14","2026-11-14 21:34:14","0","0","0");



DROP TABLE IF EXISTS myvouchers;

CREATE TABLE `myvouchers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vdate` date NOT NULL DEFAULT current_timestamp(),
  `tybe` int(1) NOT NULL,
  `val` double DEFAULT NULL,
  `account` int(1) NOT NULL,
  `fund_account` int(1) NOT NULL,
  `voucher_id` varchar(15) NOT NULL,
  `serial_number` varchar(20) DEFAULT NULL,
  `cost_center` int(1) DEFAULT NULL,
  `info` varchar(200) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user` int(11) NOT NULL DEFAULT 1,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE IF EXISTS notes;

CREATE TABLE `notes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `n1` varchar(100) DEFAULT '0',
  `n2` varchar(100) DEFAULT '0',
  `n3` varchar(100) DEFAULT '0',
  `n4` varchar(100) DEFAULT '0',
  `n5` varchar(100) DEFAULT '0',
  `n6` varchar(100) DEFAULT '0',
  `n7` varchar(100) DEFAULT '0',
  `n8` varchar(100) DEFAULT '0',
  `n9` varchar(100) DEFAULT '0',
  `n10` varchar(100) DEFAULT '0',
  `n11` varchar(100) DEFAULT '0',
  `n12` varchar(100) DEFAULT '0',
  `n13` varchar(100) DEFAULT '0',
  `n14` varchar(100) DEFAULT '0',
  `n15` varchar(100) DEFAULT '0',
  `n16` varchar(100) DEFAULT '0',
  `n17` varchar(100) DEFAULT '0',
  `n18` varchar(100) DEFAULT '0',
  `n19` varchar(100) DEFAULT '0',
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE IF EXISTS oppatterns;

CREATE TABLE `oppatterns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pame` varchar(100) DEFAULT NULL,
  `ptext` varchar(100) DEFAULT NULL,
  `def_width` int(11) NOT NULL DEFAULT 50,
  `cur_width` int(11) NOT NULL DEFAULT 50,
  `shown` int(11) NOT NULL DEFAULT 1,
  `is_edit` int(11) NOT NULL DEFAULT 1,
  `is_print` int(11) NOT NULL DEFAULT 1,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE IF EXISTS order_status;

CREATE TABLE `order_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `user` int(1) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE IF EXISTS order_types;

CREATE TABLE `order_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `info` varchar(100) DEFAULT NULL,
  `user` int(1) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE IF EXISTS orders;

CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tybe` int(11) DEFAULT NULL,
  `employee` int(11) DEFAULT NULL,
  `status` int(11) NOT NULL,
  `applyingdate` date NOT NULL,
  `curdate` date NOT NULL,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `employee_id` (`employee`),
  KEY `tybe` (`tybe`),
  KEY `status` (`status`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`employee`) REFERENCES `employees` (`id`),
  CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`tybe`) REFERENCES `order_types` (`id`),
  CONSTRAINT `orders_ibfk_3` FOREIGN KEY (`status`) REFERENCES `order_status` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE IF EXISTS ot_head;

CREATE TABLE `ot_head` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pro_id` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `table_id` int(11) DEFAULT NULL COMMENT 'رقم الطاولة',
  `order_type` enum('dine_in','takeaway','delivery','table') DEFAULT 'takeaway',
  `pro_tybe` int(1) DEFAULT NULL,
  `is_stock` int(11) DEFAULT NULL,
  `is_finance` int(11) DEFAULT NULL,
  `is_manager` int(11) DEFAULT NULL,
  `is_journal` int(11) DEFAULT NULL,
  `journal_tybe` int(11) DEFAULT NULL,
  `info` varchar(200) DEFAULT NULL,
  `start_time` time DEFAULT current_timestamp(),
  `end_time` time DEFAULT current_timestamp(),
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `pro_date` date DEFAULT NULL,
  `accural_date` date DEFAULT NULL,
  `pro_pattren` int(11) DEFAULT NULL,
  `pro_num` varchar(50) DEFAULT NULL,
  `receipt_number` varchar(50) DEFAULT NULL,
  `pro_serial` varchar(50) DEFAULT NULL,
  `tax_num` varchar(50) DEFAULT NULL,
  `price_list` int(11) DEFAULT NULL,
  `store_id` int(11) DEFAULT NULL,
  `emp_id` int(11) DEFAULT NULL,
  `emp2_id` int(11) DEFAULT NULL,
  `delivery_person_id` int(11) DEFAULT NULL COMMENT 'معرف الطيار/مندوب التوصيل',
  `acc1` int(11) DEFAULT NULL,
  `acc1_before` double DEFAULT NULL,
  `acc1_after` double DEFAULT NULL,
  `acc2` int(11) DEFAULT NULL,
  `acc2_before` double DEFAULT NULL,
  `acc2_after` double DEFAULT NULL,
  `pro_value` double DEFAULT NULL,
  `fat_cost` double DEFAULT NULL,
  `cost_center` int(11) DEFAULT NULL,
  `profit` double DEFAULT NULL,
  `fat_total` double DEFAULT NULL,
  `fat_net` double DEFAULT 0,
  `fat_disc` double DEFAULT NULL,
  `fat_disc_per` double DEFAULT NULL,
  `fat_plus` double DEFAULT NULL,
  `fat_plus_per` double DEFAULT NULL,
  `fat_tax` double DEFAULT NULL,
  `fat_tax_per` double DEFAULT NULL,
  `paid_amount` decimal(15,2) DEFAULT 0.00,
  `remaining_amount` decimal(15,2) DEFAULT 0.00,
  `payment_status` enum('unpaid','partial','paid','refunded') DEFAULT 'unpaid',
  `invoice_status` enum('draft','completed','cancelled') DEFAULT 'completed',
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `acc_fund` int(1) DEFAULT 0,
  `op2` int(11) DEFAULT 0,
  `isdeleted` tinyint(1) DEFAULT 0,
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user` int(11) NOT NULL DEFAULT 1,
  `waiter_id` int(11) DEFAULT NULL COMMENT 'معرف الويتر',
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  `closed` int(1) DEFAULT 0,
  `order_status` enum('draft','active','completed','cancelled') DEFAULT 'active' COMMENT 'حالة الطلب',
  `payment_method` varchar(20) DEFAULT 'cash' COMMENT 'طريقة الدفع',
  `payment_notes` text DEFAULT NULL COMMENT 'ملاحظات الدفع',
  `payment_date` datetime DEFAULT NULL COMMENT 'تاريخ الدفع',
  `jal_name` varchar(255) DEFAULT NULL,
  `jal_notes` text DEFAULT NULL,
  `jal_amount` decimal(10,2) DEFAULT 0.00,
  `shift_id` int(11) NOT NULL DEFAULT 0 COMMENT 'رقم الشيفت في closed_orders (0 = شيفت مفتوح، -1 = قبل نظام الشيفتات)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `receipt_number` (`receipt_number`),
  KEY `acc1` (`acc1`),
  KEY `acc2` (`acc2`),
  KEY `emp2_id` (`emp2_id`),
  KEY `emp_id` (`emp_id`),
  KEY `journal_tybe` (`journal_tybe`),
  KEY `user` (`user`),
  KEY `cost_center` (`cost_center`),
  KEY `store_id` (`store_id`),
  KEY `price_list` (`price_list`),
  KEY `idx_table` (`table_id`),
  KEY `idx_order_type` (`order_type`),
  KEY `idx_payment_status` (`payment_status`),
  KEY `idx_isdeleted` (`isdeleted`),
  KEY `waiter_id` (`waiter_id`),
  KEY `idx_ot_head_user_shift` (`user`,`shift_id`)
) ENGINE=InnoDB AUTO_INCREMENT=232 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO ot_head VALUES ("1","1",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","21:22:30","21:22:30",NULL,NULL,"2026-11-14","2026-11-14","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"33","0","1","33","33","33","0","0","0","0","0","0","33.00","0.00","paid","completed","2026-11-14 21:22:30","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":33,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","1");
INSERT INTO ot_head VALUES ("2","1",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","21:22:30","21:22:30",NULL,NULL,"2026-11-14",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"33",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-14 21:22:30","0","1","0","2026-11-14 21:22:30","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","1");
INSERT INTO ot_head VALUES ("3","2",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","21:22:53","21:22:53",NULL,NULL,"2026-11-14","2026-11-14","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"33","0","1","33","33","33","0","0","0","0","0","0","33.00","0.00","paid","completed","2026-11-14 21:22:53","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":33,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","1");
INSERT INTO ot_head VALUES ("4","2",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","21:22:53","21:22:53",NULL,NULL,"2026-11-14",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"33",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-14 21:22:53","0","3","0","2026-11-14 21:22:53","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","1");
INSERT INTO ot_head VALUES ("5","3",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","21:23:43","21:23:43",NULL,NULL,"2026-11-14","2026-11-14","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"132","0","1","132","132","132","0","0","0","0","0","0","132.00","0.00","paid","completed","2026-11-14 21:23:43","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":132,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("6","3",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","21:23:43","21:23:43",NULL,NULL,"2026-11-14",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"132",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-14 21:23:43","0","5","0","2026-11-14 21:23:43","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("7","4",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","22:48:12","22:48:12",NULL,NULL,"2026-11-14","2026-11-14","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"33","0","1","33","33","33","0","0","0","0","0","0","33.00","0.00","paid","completed","2026-11-14 22:48:12","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":33,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("8","4",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","22:48:12","22:48:12",NULL,NULL,"2026-11-14",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"33",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-14 22:48:12","0","7","0","2026-11-14 22:48:12","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("9","5",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","22:54:20","22:54:20",NULL,NULL,"2026-11-14","2026-11-14","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"11","0","1","11","11","11","0","0","0","0","0","0","11.00","0.00","paid","completed","2026-11-14 22:54:20","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":11,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("10","5",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","22:54:20","22:54:20",NULL,NULL,"2026-11-14",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"11",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-14 22:54:20","0","9","0","2026-11-14 22:54:20","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("11","6",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","23:33:14","23:33:14",NULL,NULL,"2026-11-14","2026-11-14","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"33","0","1","33","33","33","0","0","0","0","0","0","22.00","0.00","partial","completed","2026-11-14 23:33:14","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":22,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("12","6",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","23:33:14","23:33:14",NULL,NULL,"2026-11-14",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"22",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-14 23:33:14","0","11","0","2026-11-14 23:33:14","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("13","7",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","11:54:26","11:54:26",NULL,NULL,"2026-11-16","2026-11-16","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"66","0","1","66","66","66","0","0","0","0","0","0","66.00","0.00","paid","completed","2026-11-16 11:54:26","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":66,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("14","7",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","11:54:26","11:54:26",NULL,NULL,"2026-11-16",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"66",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-16 11:54:26","0","13","0","2026-11-16 11:54:26","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("16","8",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","12:20:30","12:20:30",NULL,NULL,"2026-11-16","2026-11-16","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"33","0","1","33","33","33","0","0","0","0","0","0","33.00","0.00","paid","completed","2026-11-16 12:20:30","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":33,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("17","8",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","12:20:30","12:20:30",NULL,NULL,"2026-11-16",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"33",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-16 12:20:30","0","16","0","2026-11-16 12:20:30","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("18","9",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","12:20:58","12:20:58",NULL,NULL,"2026-11-16","2026-11-16","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"33","0","1","33","33","33","0","0","0","0","0","0","33.00","0.00","paid","completed","2026-11-16 12:20:58","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":33,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("19","9",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","12:20:58","12:20:58",NULL,NULL,"2026-11-16",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"33",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-16 12:20:58","0","18","0","2026-11-16 12:20:58","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("20","10",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","12:25:30","12:25:30",NULL,NULL,"2026-11-16","2026-11-16","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"33","0","1","33","33","33","0","0","0","0","0","0","33.00","0.00","paid","completed","2026-11-16 12:25:30","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":33,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("21","10",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","12:25:30","12:25:30",NULL,NULL,"2026-11-16",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"33",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-16 12:25:30","0","20","0","2026-11-16 12:25:30","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("22","11",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","12:27:19","12:27:19",NULL,NULL,"2026-11-16","2026-11-16","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"33","0","1","33","33","33","0","0","0","0","0","0","33.00","0.00","paid","completed","2026-11-16 12:27:19","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":33,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("23","11",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","12:27:19","12:27:19",NULL,NULL,"2026-11-16",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"33",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-16 12:27:19","0","22","0","2026-11-16 12:27:19","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("24","12",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","12:29:22","12:29:22",NULL,NULL,"2026-11-16","2026-11-16","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"33","0","1","33","33","33","0","0","0","0","0","0","33.00","0.00","paid","completed","2026-11-16 12:29:22","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":33,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("25","12",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","12:29:22","12:29:22",NULL,NULL,"2026-11-16",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"33",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-16 12:29:22","0","24","0","2026-11-16 12:29:22","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("26","13",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","12:29:35","12:29:35",NULL,NULL,"2026-11-16","2026-11-16","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"33","0","1","33","33","33","0","0","0","0","0","0","33.00","0.00","paid","completed","2026-11-16 12:29:35","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":33,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("27","13",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","12:29:35","12:29:35",NULL,NULL,"2026-11-16",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"33",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-16 12:29:35","0","26","0","2026-11-16 12:29:35","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("28","14",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","12:30:46","12:30:46",NULL,NULL,"2026-11-16","2026-11-16","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"33","0","1","33","33","33","0","0","0","0","0","0","33.00","0.00","paid","completed","2026-11-16 12:30:46","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":33,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("29","14",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","12:30:46","12:30:46",NULL,NULL,"2026-11-16",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"33",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-16 12:30:46","0","28","0","2026-11-16 12:30:46","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("30","15",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","12:31:21","12:31:21",NULL,NULL,"2026-11-16","2026-11-16","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"103","0","1","103","103","103","0","0","0","0","0","0","103.00","0.00","paid","completed","2026-11-16 12:31:21","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":103,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("31","15",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","12:31:21","12:31:21",NULL,NULL,"2026-11-16",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"103",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-16 12:31:21","0","30","0","2026-11-16 12:31:21","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("32","16",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","12:35:34","12:35:34",NULL,NULL,"2026-11-16","2026-11-16","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"33","0","1","33","33","33","0","0","0","0","0","0","33.00","0.00","paid","completed","2026-11-16 12:35:34","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":33,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("33","16",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","12:35:34","12:35:34",NULL,NULL,"2026-11-16",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"33",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-16 12:35:34","0","32","0","2026-11-16 12:35:34","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("34","17",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","12:53:40","12:53:40",NULL,NULL,"2026-11-16","2026-11-16","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"50","0","1","50","50","50","0","0","0","0","0","0","50.00","0.00","paid","completed","2026-11-16 12:53:40","0","0","0","2026-11-16 12:53:40","1",NULL,"0","0","0","active","cash","{\"paid_cash\":20,\"paid_bank\":30,\"payment_fund_id\":21,\"payment_bank_id\":39,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("35","17",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","12:53:40","12:53:40",NULL,NULL,"2026-11-16",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"20",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-16 12:53:40","0","34","0","2026-11-16 12:53:40","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("36","18",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض صرافة","12:53:40","12:53:40",NULL,NULL,"2026-11-16",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"39",NULL,NULL,"148",NULL,NULL,"30",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-16 12:53:40","0","34","0","2026-11-16 12:53:40","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("37","18",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","12:54:41","12:54:41",NULL,NULL,"2026-11-16","2026-11-16","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"33","0","1","33","33","33","0","0","0","0","0","0","33.00","0.00","paid","completed","2026-11-16 12:54:41","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":33,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("38","19",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","12:54:41","12:54:41",NULL,NULL,"2026-11-16",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"33",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-16 12:54:41","0","37","0","2026-11-16 12:54:41","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("39","19",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","12:59:37","12:59:37",NULL,NULL,"2026-11-16","2026-11-16","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"33","0","1","33","33","33","0","0","0","0","0","0","33.00","0.00","paid","completed","2026-11-16 12:59:37","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":33,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("40","20",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","12:59:37","12:59:37",NULL,NULL,"2026-11-16",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"33",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-16 12:59:37","0","39","0","2026-11-16 12:59:37","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("41","20",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","12:59:58","12:59:58",NULL,NULL,"2026-11-16","2026-11-16","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"33","0","1","33","33","33","0","0","0","0","0","0","33.00","0.00","paid","completed","2026-11-16 12:59:58","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":33,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("42","21",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","12:59:58","12:59:58",NULL,NULL,"2026-11-16",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"33",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-16 12:59:58","0","41","0","2026-11-16 12:59:58","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("43","21",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","13:00:28","13:00:28",NULL,NULL,"2026-11-16","2026-11-16","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"33","0","1","33","33","33","0","0","0","0","0","0","33.00","0.00","paid","completed","2026-11-16 13:00:28","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":33,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("44","22",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","13:00:28","13:00:28",NULL,NULL,"2026-11-16",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"33",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-16 13:00:28","0","43","0","2026-11-16 13:00:28","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("45","22",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","13:37:59","13:37:59",NULL,NULL,"2026-11-16","2026-11-16","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"33","0","1","33","33","33","0","0","0","0","0","0","33.00","0.00","paid","completed","2026-11-16 13:37:59","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":33,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("46","23",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","13:37:59","13:37:59",NULL,NULL,"2026-11-16",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"33",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-16 13:37:59","0","45","0","2026-11-16 13:37:59","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("47","23",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","13:40:22","13:40:22",NULL,NULL,"2026-11-16","2026-11-16","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"33","0","1","33","33","33","0","0","0","0","0","0","33.00","0.00","paid","completed","2026-11-16 13:40:22","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":33,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("48","24",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","13:40:22","13:40:22",NULL,NULL,"2026-11-16",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"33",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-16 13:40:22","0","47","0","2026-11-16 13:40:22","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("49","24",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","13:50:55","13:50:55",NULL,NULL,"2026-11-16","2026-11-16","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"33","0","1","33","33","33","0","0","0","0","0","0","33.00","0.00","paid","completed","2026-11-16 13:50:55","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":33,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("50","25",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","13:50:55","13:50:55",NULL,NULL,"2026-11-16",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"33",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-16 13:50:55","0","49","0","2026-11-16 13:50:55","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("51","25",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","13:51:27","13:51:27",NULL,NULL,"2026-11-16","2026-11-16","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"33","0","1","33","33","33","0","0","0","0","0","0","33.00","0.00","paid","completed","2026-11-16 13:51:27","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":33,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("52","26",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","13:51:27","13:51:27",NULL,NULL,"2026-11-16",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"33",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-16 13:51:27","0","51","0","2026-11-16 13:51:27","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("53","26",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","13:52:05","13:52:05",NULL,NULL,"2026-11-16","2026-11-16","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"33","0","1","33","33","33","0","0","0","0","0","0","33.00","0.00","paid","completed","2026-11-16 13:52:05","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":33,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("54","27",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","13:52:05","13:52:05",NULL,NULL,"2026-11-16",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"33",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-16 13:52:05","0","53","0","2026-11-16 13:52:05","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("55","27",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","13:56:26","13:56:26",NULL,NULL,"2026-11-16","2026-11-16","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"22","0","1","22","22","22","0","0","0","0","0","0","22.00","0.00","paid","completed","2026-11-16 13:56:26","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":22,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("56","28",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","13:56:26","13:56:26",NULL,NULL,"2026-11-16",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"22",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-16 13:56:26","0","55","0","2026-11-16 13:56:26","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("57","28",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","13:58:12","13:58:12",NULL,NULL,"2026-11-16","2026-11-16","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"70","0","1","70","70","70","0","0","0","0","0","0","70.00","0.00","paid","completed","2026-11-16 13:58:12","0","0","0","2026-11-16 13:58:12","1",NULL,"0","0","0","active","cash","{\"paid_cash\":70,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("58","29",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","13:58:12","13:58:12",NULL,NULL,"2026-11-16",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"70",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-16 13:58:12","0","57","0","2026-11-16 13:58:12","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("59","29",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","14:00:21","14:00:21",NULL,NULL,"2026-11-16","2026-11-16","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"33","0","1","33","33","33","0","0","0","0","0","0","33.00","0.00","paid","completed","2026-11-16 14:00:21","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":33,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("60","30",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","14:00:21","14:00:21",NULL,NULL,"2026-11-16",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"33",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-16 14:00:21","0","59","0","2026-11-16 14:00:21","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("61","30",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","14:00:45","14:00:45",NULL,NULL,"2026-11-16","2026-11-16","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"33","0","1","33","33","33","0","0","0","0","0","0","33.00","0.00","paid","completed","2026-11-16 14:00:45","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":33,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("62","31",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","14:00:46","14:00:46",NULL,NULL,"2026-11-16",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"33",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-16 14:00:46","0","61","0","2026-11-16 14:00:46","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("63","31",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","14:02:35","14:02:35",NULL,NULL,"2026-11-16","2026-11-16","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"22","0","1","22","22","22","0","0","0","0","0","0","22.00","0.00","paid","completed","2026-11-16 14:02:35","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":22,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("64","32",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","14:02:35","14:02:35",NULL,NULL,"2026-11-16",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"22",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-16 14:02:35","0","63","0","2026-11-16 14:02:35","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("65","32",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","14:03:15","14:03:15",NULL,NULL,"2026-11-16","2026-11-16","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"44","0","1","44","44","44","0","0","0","0","0","0","44.00","0.00","paid","completed","2026-11-16 14:03:15","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":44,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("66","33",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","14:03:15","14:03:15",NULL,NULL,"2026-11-16",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"44",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-16 14:03:15","0","65","0","2026-11-16 14:03:15","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("67","33",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","14:10:43","14:10:43",NULL,NULL,"2026-11-16","2026-11-16","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"33","0","1","33","33","33","0","0","0","0","0","0","33.00","0.00","paid","completed","2026-11-16 14:10:43","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":33,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("68","34",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","14:10:43","14:10:43",NULL,NULL,"2026-11-16",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"33",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-16 14:10:43","0","67","0","2026-11-16 14:10:43","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("69","34",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","14:14:20","14:14:20",NULL,NULL,"2026-11-16","2026-11-16","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"33","0","1","33","33","33","0","0","0","0","0","0","33.00","0.00","paid","completed","2026-11-16 14:14:20","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":33,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("70","35",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","14:14:20","14:14:20",NULL,NULL,"2026-11-16",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"33",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-16 14:14:20","0","69","0","2026-11-16 14:14:20","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("71","35",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","14:19:32","14:19:32",NULL,NULL,"2026-11-16","2026-11-16","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"33","0","1","33","33","33","0","0","0","0","0","0","33.00","0.00","paid","completed","2026-11-16 14:19:32","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":33,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("72","36",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","14:19:32","14:19:32",NULL,NULL,"2026-11-16",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"33",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-16 14:19:32","0","71","0","2026-11-16 14:19:32","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("73","36",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","14:19:45","14:19:45",NULL,NULL,"2026-11-16","2026-11-16","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"33","0","1","33","33","33","0","0","0","0","0","0","33.00","0.00","paid","completed","2026-11-16 14:19:45","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":33,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("74","37",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","14:19:45","14:19:45",NULL,NULL,"2026-11-16",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"33",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-16 14:19:45","0","73","0","2026-11-16 14:19:45","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("75","37",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","14:20:41","14:20:41",NULL,NULL,"2026-11-16","2026-11-16","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"33","0","1","33","33","33","0","0","0","0","0","0","33.00","0.00","paid","completed","2026-11-16 14:20:41","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":33,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("76","38",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","14:20:41","14:20:41",NULL,NULL,"2026-11-16",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"33",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-16 14:20:41","0","75","0","2026-11-16 14:20:41","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("77","38",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","14:25:02","14:25:02",NULL,NULL,"2026-11-16","2026-11-16","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"33","0","1","33","33","33","0","0","0","0","0","0","33.00","0.00","paid","completed","2026-11-16 14:25:02","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":33,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("78","39",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","14:25:02","14:25:02",NULL,NULL,"2026-11-16",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"33",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-16 14:25:02","0","77","0","2026-11-16 14:25:02","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("79","39",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","14:25:18","14:25:18",NULL,NULL,"2026-11-16","2026-11-16","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"33","0","1","33","33","33","0","0","0","0","0","0","33.00","0.00","paid","completed","2026-11-16 14:25:18","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":33,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("80","40",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","14:25:18","14:25:18",NULL,NULL,"2026-11-16",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"33",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-16 14:25:18","0","79","0","2026-11-16 14:25:18","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("81","40",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","14:32:27","14:32:27",NULL,NULL,"2026-11-16","2026-11-16","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"33","0","1","33","33","33","0","0","0","0","0","0","33.00","0.00","paid","completed","2026-11-16 14:32:27","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":33,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("82","41",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","14:32:27","14:32:27",NULL,NULL,"2026-11-16",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"33",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-16 14:32:27","0","81","0","2026-11-16 14:32:27","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("83","41",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","14:32:50","14:32:50",NULL,NULL,"2026-11-16","2026-11-16","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"33","0","1","33","33","33","0","0","0","0","0","0","33.00","0.00","paid","completed","2026-11-16 14:32:50","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":33,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("84","42",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","14:32:50","14:32:50",NULL,NULL,"2026-11-16",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"33",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-16 14:32:50","0","83","0","2026-11-16 14:32:50","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("85","42",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","14:35:41","14:35:41",NULL,NULL,"2026-11-16","2026-11-16","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"33","0","1","33","33","33","0","0","0","0","0","0","33.00","0.00","paid","completed","2026-11-16 14:35:41","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":33,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("86","43",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","14:35:41","14:35:41",NULL,NULL,"2026-11-16",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"33",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-16 14:35:41","0","85","0","2026-11-16 14:35:41","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("87","43",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","14:37:04","14:37:04",NULL,NULL,"2026-11-16","2026-11-16","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"33","0","1","33","33","33","0","0","0","0","0","0","33.00","0.00","paid","completed","2026-11-16 14:37:04","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":33,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("88","44",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","14:37:04","14:37:04",NULL,NULL,"2026-11-16",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"33",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-16 14:37:04","0","87","0","2026-11-16 14:37:04","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("89","44",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","14:41:12","14:41:12",NULL,NULL,"2026-11-16","2026-11-16","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"33","0","1","33","33","33","0","0","0","0","0","0","33.00","0.00","paid","completed","2026-11-16 14:41:12","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":33,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("90","45",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","14:41:12","14:41:12",NULL,NULL,"2026-11-16",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"33",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-16 14:41:12","0","89","0","2026-11-16 14:41:12","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("91","45",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","14:41:34","14:41:34",NULL,NULL,"2026-11-16","2026-11-16","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"33","0","1","33","33","33","0","0","0","0","0","0","33.00","0.00","paid","completed","2026-11-16 14:41:34","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":33,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("92","46",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","14:41:34","14:41:34",NULL,NULL,"2026-11-16",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"33",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-16 14:41:34","0","91","0","2026-11-16 14:41:34","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("93","46",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","14:41:38","14:41:38",NULL,NULL,"2026-11-16","2026-11-16","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"81","0","1","81","81","81","0","0","0","0","0","0","81.00","0.00","paid","completed","2026-11-16 14:41:38","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":81,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("94","47",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","14:41:38","14:41:38",NULL,NULL,"2026-11-16",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"81",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-16 14:41:38","0","93","0","2026-11-16 14:41:38","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("95","47",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","14:42:25","14:42:25",NULL,NULL,"2026-11-16","2026-11-16","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"33","0","1","33","33","33","0","0","0","0","0","0","33.00","0.00","paid","completed","2026-11-16 14:42:25","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":33,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("96","48",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","14:42:25","14:42:25",NULL,NULL,"2026-11-16",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"33",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-16 14:42:25","0","95","0","2026-11-16 14:42:25","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("97","48",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","14:42:34","14:42:34",NULL,NULL,"2026-11-16","2026-11-16","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"53","0","1","53","53","53","0","0","0","0","0","0","53.00","0.00","paid","completed","2026-11-16 14:42:34","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":53,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("98","49",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","14:42:34","14:42:34",NULL,NULL,"2026-11-16",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"53",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-16 14:42:34","0","97","0","2026-11-16 14:42:34","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("99","49",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","14:43:00","14:43:00",NULL,NULL,"2026-11-16","2026-11-16","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"53","0","1","53","53","53","0","0","0","0","0","0","53.00","0.00","paid","completed","2026-11-16 14:43:00","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":53,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("100","50",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","14:43:00","14:43:00",NULL,NULL,"2026-11-16",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"53",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-16 14:43:00","0","99","0","2026-11-16 14:43:00","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("101","50",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","14:43:46","14:43:46",NULL,NULL,"2026-11-16","2026-11-16","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"33","0","1","33","33","33","0","0","0","0","0","0","33.00","0.00","paid","completed","2026-11-16 14:43:46","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":33,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("102","51",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","14:43:46","14:43:46",NULL,NULL,"2026-11-16",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"33",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-16 14:43:46","0","101","0","2026-11-16 14:43:46","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("103","51",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","14:45:32","14:45:32",NULL,NULL,"2026-11-16","2026-11-16","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"33","0","1","33","33","33","0","0","0","0","0","0","33.00","0.00","paid","completed","2026-11-16 14:45:32","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":33,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("104","52",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","14:45:32","14:45:32",NULL,NULL,"2026-11-16",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"33",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-16 14:45:32","0","103","0","2026-11-16 14:45:32","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("105","52",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","14:46:18","14:46:18",NULL,NULL,"2026-11-16","2026-11-16","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"33","0","1","33","33","33","0","0","0","0","0","0","33.00","0.00","paid","completed","2026-11-16 14:46:18","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":33,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("106","53",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","14:46:18","14:46:18",NULL,NULL,"2026-11-16",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"33",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-16 14:46:18","0","105","0","2026-11-16 14:46:18","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("107","53",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","14:54:50","14:54:50",NULL,NULL,"2026-11-16","2026-11-16","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"31","0","1","31","31","31","0","0","0","0","0","0","31.00","0.00","paid","completed","2026-11-16 14:54:50","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":31,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("108","54",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","14:54:50","14:54:50",NULL,NULL,"2026-11-16",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"31",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-16 14:54:50","0","107","0","2026-11-16 14:54:50","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("109","54",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","14:55:15","14:55:15",NULL,NULL,"2026-11-16","2026-11-16","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"33","0","1","33","33","33","0","0","0","0","0","0","33.00","0.00","paid","completed","2026-11-16 14:55:15","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":33,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("110","55",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","14:55:15","14:55:15",NULL,NULL,"2026-11-16",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"33",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-16 14:55:15","0","109","0","2026-11-16 14:55:15","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("111","55",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","14:55:58","14:55:58",NULL,NULL,"2026-11-16","2026-11-16","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"33","0","1","33","33","33","0","0","0","0","0","0","33.00","0.00","paid","completed","2026-11-16 14:55:58","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":33,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("112","56",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","14:55:58","14:55:58",NULL,NULL,"2026-11-16",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"33",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-16 14:55:58","0","111","0","2026-11-16 14:55:58","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("113","56",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","14:57:30","14:57:30",NULL,NULL,"2026-11-16","2026-11-16","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"33","0","1","33","33","33","0","0","0","0","0","0","33.00","0.00","paid","completed","2026-11-16 14:57:30","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":33,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("114","57",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","14:57:30","14:57:30",NULL,NULL,"2026-11-16",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"33",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-16 14:57:30","0","113","0","2026-11-16 14:57:30","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("115","57",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","14:58:10","14:58:10",NULL,NULL,"2026-11-16","2026-11-16","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"33","0","1","33","33","33","0","0","0","0","0","0","33.00","0.00","paid","completed","2026-11-16 14:58:10","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":33,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("116","58",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","14:58:10","14:58:10",NULL,NULL,"2026-11-16",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"33",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-16 14:58:10","0","115","0","2026-11-16 14:58:10","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("117","58",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","14:58:26","14:58:26",NULL,NULL,"2026-11-16","2026-11-16","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"42","0","1","42","42","42","0","0","0","0","0","0","42.00","0.00","paid","completed","2026-11-16 14:58:26","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":42,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("118","59",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","14:58:26","14:58:26",NULL,NULL,"2026-11-16",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"42",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-16 14:58:26","0","117","0","2026-11-16 14:58:26","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("119","59",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","14:59:05","14:59:05",NULL,NULL,"2026-11-16","2026-11-16","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"50","0","1","50","50","50","0","0","0","0","0","0","50.00","0.00","paid","completed","2026-11-16 14:59:05","0","0","0","2026-11-16 14:59:05","1",NULL,"0","0","0","active","cash","{\"paid_cash\":50,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("120","60",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","14:59:05","14:59:05",NULL,NULL,"2026-11-16",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"50",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-16 14:59:05","0","119","0","2026-11-16 14:59:05","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("121","60",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","15:07:25","15:07:25",NULL,NULL,"2026-11-16","2026-11-16","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"33","0","1","33","33","33","0","0","0","0","0","0","33.00","0.00","paid","completed","2026-11-16 15:07:25","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":33,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("122","61",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","15:07:25","15:07:25",NULL,NULL,"2026-11-16",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"33",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-16 15:07:25","0","121","0","2026-11-16 15:07:25","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("123","61",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","15:07:46","15:07:46",NULL,NULL,"2026-11-16","2026-11-16","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"33","0","1","33","33","33","0","0","0","0","0","0","33.00","0.00","paid","completed","2026-11-16 15:07:46","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":33,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("124","62",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","15:07:46","15:07:46",NULL,NULL,"2026-11-16",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"33",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-16 15:07:46","0","123","0","2026-11-16 15:07:46","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("125","62",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","15:08:15","15:08:15",NULL,NULL,"2026-11-16","2026-11-16","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"33","0","1","33","33","33","0","0","0","0","0","0","33.00","0.00","paid","completed","2026-11-16 15:08:15","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":33,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("126","63",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","15:08:15","15:08:15",NULL,NULL,"2026-11-16",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"33",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-16 15:08:15","0","125","0","2026-11-16 15:08:15","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("127","63",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","15:11:38","15:11:38",NULL,NULL,"2026-11-16","2026-11-16","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"86","0","1","86","86","86","0","0","0","0","0","0","86.00","0.00","paid","completed","2026-11-16 15:11:38","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":86,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("128","64",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","15:11:38","15:11:38",NULL,NULL,"2026-11-16",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"86",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-16 15:11:38","0","127","0","2026-11-16 15:11:38","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("129","64",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","15:11:45","15:11:45",NULL,NULL,"2026-11-16","2026-11-16","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"33","0","1","33","33","33","0","0","0","0","0","0","33.00","0.00","paid","completed","2026-11-16 15:11:45","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":33,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("130","65",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","15:11:45","15:11:45",NULL,NULL,"2026-11-16",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"33",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-16 15:11:45","0","129","0","2026-11-16 15:11:45","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("131","65",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","15:11:50","15:11:50",NULL,NULL,"2026-11-16","2026-11-16","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"33","0","1","33","33","33","0","0","0","0","0","0","33.00","0.00","paid","completed","2026-11-16 15:11:50","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":33,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("132","66",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","15:11:50","15:11:50",NULL,NULL,"2026-11-16",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"33",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-16 15:11:50","0","131","0","2026-11-16 15:11:50","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("133","66",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","15:21:52","15:21:52",NULL,NULL,"2026-11-16","2026-11-16","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"33","0","1","33","33","33","0","0","0","0","0","0","33.00","0.00","paid","completed","2026-11-16 15:21:52","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":33,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("134","67",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","15:21:52","15:21:52",NULL,NULL,"2026-11-16",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"33",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-16 15:21:52","0","133","0","2026-11-16 15:21:52","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("135","67",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","15:32:46","15:32:46",NULL,NULL,"2026-11-16","2026-11-16","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"33","0","1","33","33","33","0","0","0","0","0","0","33.00","0.00","paid","completed","2026-11-16 15:32:46","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":33,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("136","68",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","15:32:46","15:32:46",NULL,NULL,"2026-11-16",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"33",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-16 15:32:46","0","135","0","2026-11-16 15:32:46","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("137","68",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","15:33:57","15:33:57",NULL,NULL,"2026-11-16","2026-11-16","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"50","0","1","50","50","50","0","0","0","0","0","0","50.00","0.00","paid","completed","2026-11-16 15:33:57","0","0","0","2026-11-16 15:33:57","1",NULL,"0","0","0","active","cash","{\"paid_cash\":50,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("138","69",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","15:33:57","15:33:57",NULL,NULL,"2026-11-16",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"50",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-16 15:33:57","0","137","0","2026-11-16 15:33:57","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("139","69",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","15:35:10","15:35:10",NULL,NULL,"2026-11-16","2026-11-16","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"133","0","1","133","133","133","0","0","0","0","0","0","133.00","0.00","paid","completed","2026-11-16 15:35:10","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":133,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("140","70",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","15:35:10","15:35:10",NULL,NULL,"2026-11-16",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"133",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-16 15:35:10","0","139","0","2026-11-16 15:35:10","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("141","70",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","15:35:25","15:35:25",NULL,NULL,"2026-11-16","2026-11-16","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"11","0","1","11","11","11","0","0","0","0","0","0","11.00","0.00","paid","completed","2026-11-16 15:35:25","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":11,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("142","71",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","15:35:25","15:35:25",NULL,NULL,"2026-11-16",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"11",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-16 15:35:25","0","141","0","2026-11-16 15:35:25","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("143","71",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","15:36:04","15:36:04",NULL,NULL,"2026-11-16","2026-11-16","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"61","0","1","61","61","61","0","0","0","0","0","0","61.00","0.00","paid","completed","2026-11-16 15:36:04","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":61,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("144","72",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","15:36:04","15:36:04",NULL,NULL,"2026-11-16",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"61",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-16 15:36:04","0","143","0","2026-11-16 15:36:04","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("145","72",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","15:45:41","15:45:41",NULL,NULL,"2026-11-16","2026-11-16","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"33","0","1","33","33","33","0","0","0","0","0","0","33.00","0.00","paid","completed","2026-11-16 15:45:41","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":33,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("146","73",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","15:45:41","15:45:41",NULL,NULL,"2026-11-16",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"33",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-16 15:45:41","0","145","0","2026-11-16 15:45:41","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("147","73",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","21:04:41","21:04:41",NULL,NULL,"2026-11-16","2026-11-16","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"33","0","1","33","33","33","0","0","0","0","0","0","33.00","0.00","paid","completed","2026-11-16 21:04:41","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":33,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("148","74",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","21:04:41","21:04:41",NULL,NULL,"2026-11-16",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"33",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-16 21:04:41","0","147","0","2026-11-16 21:04:41","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("149","74",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","21:05:44","21:05:44",NULL,NULL,"2026-11-16","2026-11-16","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"33","0","1","33","33","33","0","0","0","0","0","0","33.00","0.00","paid","completed","2026-11-16 21:05:44","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":33,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("150","75",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","21:05:44","21:05:44",NULL,NULL,"2026-11-16",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"33",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-16 21:05:44","0","149","0","2026-11-16 21:05:44","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("151","75",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","21:07:00","21:07:00",NULL,NULL,"2026-11-16","2026-11-16","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"103","0","1","103","103","103","0","0","0","0","0","0","103.00","0.00","paid","completed","2026-11-16 21:07:00","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":103,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("152","76",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","21:07:00","21:07:00",NULL,NULL,"2026-11-16",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"103",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-16 21:07:00","0","151","0","2026-11-16 21:07:00","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("153","76",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","21:15:13","21:15:13",NULL,NULL,"2026-11-16","2026-11-16","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"81","0","1","81","81","81","0","0","0","0","0","0","81.00","0.00","paid","completed","2026-11-16 21:15:13","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":0,\"paid_bank\":81,\"payment_fund_id\":21,\"payment_bank_id\":39,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("154","77",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض صرافة","21:15:13","21:15:13",NULL,NULL,"2026-11-16",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"39",NULL,NULL,"148",NULL,NULL,"81",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-16 21:15:13","0","153","0","2026-11-16 21:15:13","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("155","77",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","21:15:17","21:15:17",NULL,NULL,"2026-11-16","2026-11-16","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"31","0","1","31","31","31","0","0","0","0","0","0","31.00","0.00","paid","completed","2026-11-16 21:15:17","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":31,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("156","78",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","21:15:17","21:15:17",NULL,NULL,"2026-11-16",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"31",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-16 21:15:17","0","155","0","2026-11-16 21:15:17","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("157","78",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","15:39:22","15:39:22",NULL,NULL,"2026-11-18","2026-11-18","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"33","0","1","33","33","33","0","0","0","0","0","0","33.00","0.00","paid","completed","2026-11-18 15:39:22","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":33,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("158","79",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","15:39:22","15:39:22",NULL,NULL,"2026-11-18",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"33",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-18 15:39:22","0","157","0","2026-11-18 15:39:22","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("159","79",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: بيع مباشر","15:40:03","15:40:03",NULL,NULL,"2026-11-18","2026-11-18",NULL,NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"70",NULL,NULL,"70","70","70","0",NULL,"0",NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-18 15:40:03","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("160","79",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","سند قبض من فاتورة POS رقم 79","15:40:03","15:40:03",NULL,NULL,"2026-11-18",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"70",NULL,NULL,NULL,NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-18 15:40:03","0","159","0","2026-11-18 15:40:03","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("161","80",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: بيع مباشر","15:40:13","15:40:13",NULL,NULL,"2026-11-18","2026-11-18",NULL,NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"70",NULL,NULL,"70","70","70","0",NULL,"0",NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-18 15:40:13","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("162","80",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","سند قبض من فاتورة POS رقم 80","15:40:13","15:40:13",NULL,NULL,"2026-11-18",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"70",NULL,NULL,NULL,NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-18 15:40:13","0","161","0","2026-11-18 15:40:13","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("163","81",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: بيع مباشر","15:40:23","15:40:23",NULL,NULL,"2026-11-18","2026-11-18",NULL,NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"70",NULL,NULL,"70","70","70","0",NULL,"0",NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-18 15:40:23","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("164","81",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","سند قبض من فاتورة POS رقم 81","15:40:23","15:40:23",NULL,NULL,"2026-11-18",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"70",NULL,NULL,NULL,NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-18 15:40:23","0","163","0","2026-11-18 15:40:23","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("165","82",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: بيع مباشر","15:40:46","15:40:46",NULL,NULL,"2026-11-18","2026-11-18",NULL,NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"70",NULL,NULL,"70","70","70","0",NULL,"0",NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-18 15:40:46","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("166","82",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","سند قبض من فاتورة POS رقم 82","15:40:46","15:40:46",NULL,NULL,"2026-11-18",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"70",NULL,NULL,NULL,NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-18 15:40:46","0","165","0","2026-11-18 15:40:46","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("167","83",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","16:04:31","16:04:31",NULL,NULL,"2026-11-18","2026-11-18","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"33","0","1","33","33","33","0","0","0","0","0","0","33.00","0.00","paid","completed","2026-11-18 16:04:31","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":33,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("168","83",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","16:04:31","16:04:31",NULL,NULL,"2026-11-18",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"33",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-18 16:04:31","0","167","0","2026-11-18 16:04:31","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("169","84",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","16:05:32","16:05:32",NULL,NULL,"2026-11-18","2026-11-18","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"42","0","1","42","42","42","0","0","0","0","0","0","42.00","0.00","paid","completed","2026-11-18 16:05:32","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":42,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("170","84",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","16:05:32","16:05:32",NULL,NULL,"2026-11-18",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"42",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-18 16:05:32","0","169","0","2026-11-18 16:05:32","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("171","85",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","16:06:12","16:06:12",NULL,NULL,"2026-11-18","2026-11-18","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"33","0","1","33","33","33","0","0","0","0","0","0","33.00","0.00","paid","completed","2026-11-18 16:06:12","0","0","0","2026-11-18 17:12:30","1",NULL,"0","0","0","active","cash","{\"paid_cash\":33,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("172","85",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","16:06:12","16:06:12",NULL,NULL,"2026-11-18",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"33",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-18 16:06:12","0","171","0","2026-11-18 16:06:12","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("173","86",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","12:50:35","12:50:35",NULL,NULL,"2026-11-21","2026-11-21","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"33","0","1","33","33","33","0","0","0","0","0","0","33.00","0.00","paid","completed","2026-11-21 12:50:35","0","0","0","2026-11-21 15:46:59","1",NULL,"0","0","0","active","cash","{\"paid_cash\":33,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("174","86",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","12:50:35","12:50:35",NULL,NULL,"2026-11-21",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"33",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-21 12:50:35","0","173","0","2026-11-21 12:50:35","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("175","87",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","12:50:38","12:50:38",NULL,NULL,"2026-11-21","2026-11-21","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"53","0","1","53","53","53","0","0","0","0","0","0","53.00","0.00","paid","completed","2026-11-21 12:50:38","0","0","0","2026-11-21 15:46:59","1",NULL,"0","0","0","active","cash","{\"paid_cash\":53,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("176","87",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","12:50:38","12:50:38",NULL,NULL,"2026-11-21",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"53",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-21 12:50:38","0","175","0","2026-11-21 12:50:38","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("177","88",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","12:50:43","12:50:43",NULL,NULL,"2026-11-21","2026-11-21","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"103","0","1","103","103","103","0","0","0","0","0","0","103.00","0.00","paid","completed","2026-11-21 12:50:43","0","0","0","2026-11-21 15:46:59","1",NULL,"0","0","0","active","cash","{\"paid_cash\":103,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("178","88",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","12:50:43","12:50:43",NULL,NULL,"2026-11-21",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"103",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-21 12:50:43","0","177","0","2026-11-21 12:50:43","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("179","89",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","12:50:48","12:50:48",NULL,NULL,"2026-11-21","2026-11-21","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"55","0","1","55","55","55","0","0","0","0","0","0","55.00","0.00","paid","completed","2026-11-21 12:50:48","0","0","0","2026-11-21 15:46:59","1",NULL,"0","0","0","active","cash","{\"paid_cash\":55,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("180","89",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","12:50:48","12:50:48",NULL,NULL,"2026-11-21",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"55",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-21 12:50:48","0","179","0","2026-11-21 12:50:48","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("181","90",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: بيع مباشر","12:52:58","12:52:58",NULL,NULL,"2026-11-21","2026-11-21",NULL,NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"90",NULL,NULL,"90","90","90","0",NULL,"0",NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-21 12:52:58","0","0","0","2026-11-21 15:46:59","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("182","90",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","سند قبض من فاتورة POS رقم 90","12:52:58","12:52:58",NULL,NULL,"2026-11-21",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"90",NULL,NULL,NULL,NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-21 12:52:58","0","181","0","2026-11-21 12:52:58","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("183","1",NULL,NULL,"takeaway","11","1",NULL,NULL,"1","11","نوع الطلب: مردود مبيعات","12:53:05","12:53:05",NULL,NULL,"2026-11-21","2026-11-21",NULL,NULL,NULL,"0",NULL,"1","27","131","131",NULL,"148",NULL,NULL,"148",NULL,NULL,"90",NULL,NULL,"-90","90","90","0",NULL,"0",NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-21 12:53:05","0","0","0","2026-11-21 15:46:59","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("184","1",NULL,NULL,"takeaway","2",NULL,NULL,NULL,"1","2","سند صرف (رد) من فاتورة POS رقم 1","12:53:05","12:53:05",NULL,NULL,"2026-11-21",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"148",NULL,NULL,"21",NULL,NULL,"90",NULL,NULL,NULL,NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-21 12:53:05","0","183","0","2026-11-21 12:53:05","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("185","91",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: بيع مباشر","15:50:38","15:50:38",NULL,NULL,"2026-11-21","2026-11-21",NULL,NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"70",NULL,NULL,"70","70","70","0",NULL,"0",NULL,NULL,NULL,"70.00","0.00","paid","completed","2026-11-21 15:50:38","0","0","0","2026-11-23 15:11:41","1",NULL,"0","0","0","active","cash","{\"paid_cash\":70,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("186","91",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: بيع مباشر - قبض كاش","15:50:38","15:50:38",NULL,NULL,"2026-11-21",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"70",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-21 15:50:38","0","185","0","2026-11-21 15:50:38","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("187","92",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: بيع مباشر","17:13:55","17:13:55",NULL,NULL,"2026-11-21","2026-11-21",NULL,NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"353",NULL,NULL,"353","353","353","0",NULL,"0",NULL,NULL,NULL,"353.00","0.00","paid","completed","2026-11-21 17:13:55","0","0","0","2026-11-23 15:11:41","1",NULL,"0","0","0","active","cash","{\"paid_cash\":353,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("188","92",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: بيع مباشر - قبض كاش","17:13:55","17:13:55",NULL,NULL,"2026-11-21",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"353",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-21 17:13:55","0","187","0","2026-11-21 17:13:55","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("189","93",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: بيع مباشر","17:16:41","17:16:41",NULL,NULL,"2026-11-21","2026-11-21",NULL,NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"90",NULL,NULL,"90","90","90","0",NULL,"0",NULL,NULL,NULL,"90.00","0.00","paid","completed","2026-11-21 17:16:41","0","0","0","2026-11-21 17:16:41","1",NULL,"0","0","0","active","cash","{\"paid_cash\":90,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("190","93",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: بيع مباشر - قبض كاش","17:16:41","17:16:41",NULL,NULL,"2026-11-21",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"90",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-21 17:16:41","0","189","0","2026-11-21 17:16:41","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("191","2",NULL,NULL,"takeaway","11","1",NULL,NULL,"1","11","نوع الطلب: مردود مبيعات","17:35:13","17:35:13",NULL,NULL,"2026-11-21","2026-11-21",NULL,NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"70",NULL,NULL,"-70","70","70","0",NULL,"0",NULL,NULL,NULL,"70.00","0.00","paid","completed","2026-11-21 17:35:13","0","0","0","2026-11-21 17:35:13","1",NULL,"0","0","0","active","cash","{\"paid_cash\":70,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("192","2",NULL,NULL,"takeaway","2",NULL,NULL,NULL,"1","2","نوع الطلب: مردود مبيعات - دفع كاش","17:35:13","17:35:13",NULL,NULL,"2026-11-21",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"148",NULL,NULL,"21",NULL,NULL,"70",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-21 17:35:13","0","191","0","2026-11-21 17:35:13","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("193","94",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: بيع مباشر","17:35:57","17:35:57",NULL,NULL,"2026-11-21","2026-11-21",NULL,NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"50",NULL,NULL,"50","50","50","0",NULL,"0",NULL,NULL,NULL,"50.00","0.00","paid","completed","2026-11-21 17:35:57","0","0","0","2026-11-21 17:35:57","1",NULL,"0","0","0","active","cash","{\"paid_cash\":50,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("194","94",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: بيع مباشر - قبض كاش","17:35:57","17:35:57",NULL,NULL,"2026-11-21",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"50",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-21 17:35:57","0","193","0","2026-11-21 17:35:57","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("195","3",NULL,NULL,"takeaway","11","1",NULL,NULL,"1","11","نوع الطلب: مردود مبيعات","17:36:10","17:36:10",NULL,NULL,"2026-11-21","2026-11-21",NULL,NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"123",NULL,NULL,"-123","123","123","0",NULL,"0",NULL,NULL,NULL,"123.00","0.00","paid","completed","2026-11-21 17:36:10","0","0","0","2026-11-23 15:11:41","1",NULL,"0","0","0","active","cash","{\"paid_cash\":123,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("196","3",NULL,NULL,"takeaway","2",NULL,NULL,NULL,"1","2","نوع الطلب: مردود مبيعات - دفع كاش","17:36:10","17:36:10",NULL,NULL,"2026-11-21",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"148",NULL,NULL,"21",NULL,NULL,"123",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-21 17:36:10","0","195","0","2026-11-21 17:36:10","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("197","95",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","15656545646546عهاهععهاعه اعهاع اع عاع اعاعهالعهاهع هع اع - نوع الطلب: تيك أواي","19:12:42","19:12:42",NULL,NULL,"2026-11-22","2026-11-22","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"125","0","1","125","125","125","0","0","0","0","0","0","125.00","0.00","paid","completed","2026-11-22 19:12:42","0","0","0","2026-11-23 15:11:41","1",NULL,"0","0","0","active","cash","{\"paid_cash\":125,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("198","95",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","15656545646546عهاهععهاعه اعهاع اع عاع اعاعهالعهاهع هع اع - نوع الطلب: تيك أواي - قبض كاش","19:12:43","19:12:43",NULL,NULL,"2026-11-22",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"125",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-22 19:12:43","0","197","0","2026-11-22 19:12:43","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("199","96",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","465ا4ت5ف5ا4 54564ف5645 64ف564ث 564 - نوع الطلب: تيك أواي","19:13:04","19:13:04",NULL,NULL,"2026-11-22","2026-11-22","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"103","0","1","103","103","103","0","0","0","0","0","0","103.00","0.00","paid","completed","2026-11-22 19:13:04","0","0","0","2026-11-23 15:11:41","1",NULL,"0","0","0","active","cash","{\"paid_cash\":103,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("200","96",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","465ا4ت5ف5ا4 54564ف5645 64ف564ث 564 - نوع الطلب: تيك أواي - قبض كاش","19:13:04","19:13:04",NULL,NULL,"2026-11-22",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"103",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-22 19:13:04","0","199","0","2026-11-22 19:13:04","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("203","97",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","19:24:59","19:24:59",NULL,NULL,"2026-11-22","2026-11-22","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"33","0","1","33","33","33","0","0","0","0","0","0","33.00","0.00","paid","completed","2026-11-22 19:24:59","0","0","0","2026-11-23 15:11:41","1",NULL,"0","0","0","active","cash","{\"paid_cash\":33,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("204","97",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","19:24:59","19:24:59",NULL,NULL,"2026-11-22",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"33",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-22 19:24:59","0","203","0","2026-11-22 19:24:59","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("205","98",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","ثفصفصثفصثفصثفصثفصث - نوع الطلب: تيك أواي","19:31:47","19:31:47",NULL,NULL,"2026-11-22","2026-11-22","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"33","0","1","33","33","33","0","0","0","0","0","0","33.00","0.00","paid","completed","2026-11-22 19:31:47","0","0","0","2026-11-23 15:11:41","1",NULL,"0","0","0","active","cash","{\"paid_cash\":33,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("206","98",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","ثفصفصثفصثفصثفصثفصث - نوع الطلب: تيك أواي - قبض كاش","19:31:48","19:31:48",NULL,NULL,"2026-11-22",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"33",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-22 19:31:48","0","205","0","2026-11-22 19:31:48","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("207","99",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","19:34:35","19:34:35",NULL,NULL,"2026-11-22","2026-11-22","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"33","0","1","33","33","33","0","0","0","0","0","0","33.00","0.00","paid","completed","2026-11-22 19:34:35","0","0","0","2026-11-23 15:11:41","1",NULL,"0","0","0","active","cash","{\"paid_cash\":33,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("208","99",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","19:34:35","19:34:35",NULL,NULL,"2026-11-22",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"33",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-22 19:34:35","0","207","0","2026-11-22 19:34:35","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("209","100",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","بدون سكر بدون شاي بدون مياه - نوع الطلب: تيك أواي","19:35:11","19:35:11",NULL,NULL,"2026-11-22","2026-11-22","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"33","0","1","33","33","33","0","0","0","0","0","0","33.00","0.00","paid","completed","2026-11-22 19:35:11","0","0","0","2026-11-23 15:11:41","1",NULL,"0","0","0","active","cash","{\"paid_cash\":33,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("210","100",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","بدون سكر بدون شاي بدون مياه - نوع الطلب: تيك أواي - قبض كاش","19:35:11","19:35:11",NULL,NULL,"2026-11-22",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"33",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-22 19:35:11","0","209","0","2026-11-22 19:35:11","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("211","101",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","بدون كاتشب - نوع الطلب: تيك أواي","19:44:00","19:44:00",NULL,NULL,"2026-11-22","2026-11-22","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"2991","0","1","2991","2991","2991","0","0","0","0","0","0","2991.00","0.00","paid","completed","2026-11-22 19:44:00","0","0","0","2026-11-23 15:11:41","1",NULL,"0","0","0","active","cash","{\"paid_cash\":2991,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("212","101",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","بدون كاتشب - نوع الطلب: تيك أواي - قبض كاش","19:44:00","19:44:00",NULL,NULL,"2026-11-22",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"2991",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-22 19:44:00","0","211","0","2026-11-22 19:44:00","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("213","102",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","كاتشب كتير - نوع الطلب: تيك أواي","19:44:29","19:44:29",NULL,NULL,"2026-11-22","2026-11-22","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"33","0","1","33","33","33","0","0","0","0","0","0","33.00","0.00","paid","completed","2026-11-22 19:44:29","0","0","0","2026-11-23 15:11:41","1",NULL,"0","0","0","active","cash","{\"paid_cash\":33,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","2");
INSERT INTO ot_head VALUES ("214","102",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","كاتشب كتير - نوع الطلب: تيك أواي - قبض كاش","19:44:30","19:44:30",NULL,NULL,"2026-11-22",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"33",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-22 19:44:30","0","213","0","2026-11-22 19:44:30","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("215","103",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","20:26:45","20:26:45",NULL,NULL,"2026-11-22","2026-11-22","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"33","0","1","33","33","33","0","0","0","0","0","0","33.00","0.00","paid","completed","2026-11-22 20:26:45","0","0","0","2026-11-23 15:11:41","1",NULL,"0","0","0","active","cash","{\"paid_cash\":33,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","0");
INSERT INTO ot_head VALUES ("216","103",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","20:26:45","20:26:45",NULL,NULL,"2026-11-22",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"33",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-22 20:26:45","0","215","0","2026-11-22 20:26:45","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","0");
INSERT INTO ot_head VALUES ("217","104",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي - نوع الطلب: تيك أواي - نوع الطلب: تيك أواي","22:26:42","22:26:42",NULL,NULL,"2026-11-22","2026-11-22","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"154","0","1","154","154","154","0","0","0","0","0","0","154.00","0.00","paid","completed","2026-11-22 22:26:42","0","0","0","2026-11-23 15:11:41","1",NULL,"0","0","0","active","cash","{\"paid_cash\":154,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","0");
INSERT INTO ot_head VALUES ("218","105",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي - نوع الطلب: تيك أواي - نوع الطلب: تيك أواي - نوع الطلب: تيك أواي - نوع الطلب: تيك أواي","22:26:59","22:26:59",NULL,NULL,"2026-11-22","2026-11-22","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"55","0","1","44","55","55","0","0","0","0","0","0","66.00","11.00","paid","completed","2026-11-22 22:26:59","0","0","0","2026-11-23 15:11:41","1",NULL,"0","0","0","active","cash","{\"paid_cash\":66,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":11}",NULL,"","","0.00","0");
INSERT INTO ot_head VALUES ("220","105",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - نوع الطلب: تيك أواي - نوع الطلب: تيك أواي - قبض كاش","22:31:09","22:31:09",NULL,NULL,"2026-11-22",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"154",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-22 22:31:09","0","217","0","2026-11-22 22:31:09","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","0");
INSERT INTO ot_head VALUES ("221","106",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - نوع الطلب: تيك أواي - نوع الطلب: تيك أواي - نوع الطلب: تيك أواي - نوع الطلب: تيك أواي - قبض كاش","22:31:21","22:31:21",NULL,NULL,"2026-11-22",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"55",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-22 22:31:21","0","218","0","2026-11-22 22:31:21","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","0");
INSERT INTO ot_head VALUES ("222","106",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","15:43:10","15:43:10",NULL,NULL,"2026-11-23","2026-11-23","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"33","0","1","0","33","33","0","0","0","0","0","0","33.00","0.00","paid","completed","2026-11-23 15:43:10","0","0","0","2026-11-23 15:43:10","1",NULL,"0","0","0","active","cash","{\"paid_cash\":33,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","0");
INSERT INTO ot_head VALUES ("223","107",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","15:43:10","15:43:10",NULL,NULL,"2026-11-23",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"33",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-23 15:43:10","0","222","0","2026-11-23 15:43:10","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","0");
INSERT INTO ot_head VALUES ("224","107",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","15:43:49","15:43:49",NULL,NULL,"2026-11-23","2026-11-23","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"33","0","1","0","33","33","0","0","0","0","0","0","33.00","0.00","paid","completed","2026-11-23 15:43:49","0","0","0","2026-11-23 15:43:49","1",NULL,"0","0","0","active","cash","{\"paid_cash\":33,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","0");
INSERT INTO ot_head VALUES ("225","108",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","15:43:49","15:43:49",NULL,NULL,"2026-11-23",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"33",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-23 15:43:49","0","224","0","2026-11-23 15:43:49","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","0");
INSERT INTO ot_head VALUES ("226","108",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","15:44:14","15:44:14",NULL,NULL,"2026-11-23","2026-11-23","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"33","0","1","0","33","33","0","0","0","0","0","0","33.00","0.00","paid","completed","2026-11-23 15:44:14","0","0","0","2026-11-23 15:44:14","1",NULL,"0","0","0","active","cash","{\"paid_cash\":33,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","0");
INSERT INTO ot_head VALUES ("227","109",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","15:44:14","15:44:14",NULL,NULL,"2026-11-23",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"33",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-23 15:44:14","0","226","0","2026-11-23 15:44:14","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","0");
INSERT INTO ot_head VALUES ("228","109",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","15:45:26","15:45:26",NULL,NULL,"2026-11-23","2026-11-23","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"33","0","1","0","33","33","0","0","0","0","0","0","33.00","0.00","paid","completed","2026-11-23 15:45:26","0","0","0","2026-11-23 15:45:26","1",NULL,"0","0","0","active","cash","{\"paid_cash\":33,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","0");
INSERT INTO ot_head VALUES ("229","110",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","15:45:26","15:45:26",NULL,NULL,"2026-11-23",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"33",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-23 15:45:26","0","228","0","2026-11-23 15:45:26","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","0");
INSERT INTO ot_head VALUES ("230","110",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: تيك أواي","15:59:43","15:59:43",NULL,NULL,"2026-11-23","2026-11-23","1",NULL,NULL,"0",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"221","0","1","210","221","221","0","0","0","0","0","0","221.00","0.00","paid","completed","2026-11-23 15:59:43","0","0","0","2026-11-23 15:59:43","1",NULL,"0","0","0","active","cash","{\"paid_cash\":221,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","0");
INSERT INTO ot_head VALUES ("231","111",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","نوع الطلب: تيك أواي - قبض كاش","15:59:43","15:59:43",NULL,NULL,"2026-11-23",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"221",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-23 15:59:43","0","230","0","2026-11-23 15:59:43","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","0");



DROP TABLE IF EXISTS paper_types;

CREATE TABLE `paper_types` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `pname` varchar(50) NOT NULL,
  `info` varchar(100) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE IF EXISTS patt_cols;

CREATE TABLE `patt_cols` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cname` varchar(100) NOT NULL,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE IF EXISTS payroll_calcs;

CREATE TABLE `payroll_calcs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `snd_id` int(11) NOT NULL,
  `calc_tybe` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1=مكافأة 2=تأمين 3=ضريبة 4=خصم 5=سلفة قصيرة',
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
  KEY `emp_date` (`emp_id`,`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE IF EXISTS period_meta;

CREATE TABLE `period_meta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `db_name` varchar(64) NOT NULL,
  `label` varchar(120) NOT NULL,
  `parent_db` varchar(64) DEFAULT NULL,
  `successor_db` varchar(64) DEFAULT NULL,
  `is_closed` tinyint(1) NOT NULL DEFAULT 0,
  `closed_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_db_name` (`db_name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO period_meta VALUES ("1","test001","test001",NULL,NULL,"0",NULL,"2026-11-11 14:56:59");



DROP TABLE IF EXISTS permissions;

CREATE TABLE `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `label` varchar(150) NOT NULL DEFAULT '' COMMENT 'الاسم في شاشة الدور',
  `group_key` varchar(30) NOT NULL DEFAULT '' COMMENT 'قسم القائمة الجانبية',
  `group_name` varchar(100) NOT NULL DEFAULT '',
  `entity_label` varchar(150) NOT NULL DEFAULT '' COMMENT 'اسم الكيان (صف في جدول CRUD)',
  `action` varchar(10) DEFAULT NULL COMMENT 'view/add/edit/delete للكيانات، NULL للأفعال',
  `sort` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=299 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO permissions VALUES ("1","dashboard.cards","web","2026-11-11 16:56:57","2026-11-11 16:56:57","كروت الملخص في الشاشة الرئيسية","dashboard","لوحة التحكم","كروت الملخص في الشاشة الرئيسية",NULL,"10");
INSERT INTO permissions VALUES ("2","dashboard.shortcuts","web","2026-11-11 16:56:57","2026-11-11 16:56:57","الاختصارات في الشاشة الرئيسية","dashboard","لوحة التحكم","الاختصارات في الشاشة الرئيسية",NULL,"20");
INSERT INTO permissions VALUES ("3","dashboard.tables","web","2026-11-11 16:56:57","2026-11-11 16:56:57","الجداول في الشاشة الرئيسية","dashboard","لوحة التحكم","الجداول في الشاشة الرئيسية",NULL,"30");
INSERT INTO permissions VALUES ("4","dashboard.hr","web","2026-11-11 16:56:57","2026-11-11 16:56:57","ملخص الموارد البشرية في الشاشة الرئيسية","dashboard","لوحة التحكم","ملخص الموارد البشرية في الشاشة الرئيسية",NULL,"40");
INSERT INTO permissions VALUES ("5","clients.view","web","2026-11-11 16:56:57","2026-11-11 16:56:57","عرض العملاء","basic","البيانات الأساسية","العملاء","view","50");
INSERT INTO permissions VALUES ("6","clients.add","web","2026-11-11 16:56:57","2026-11-11 16:56:57","إضافة العملاء","basic","البيانات الأساسية","العملاء","add","60");
INSERT INTO permissions VALUES ("7","clients.edit","web","2026-11-11 16:56:57","2026-11-11 16:56:57","تعديل العملاء","basic","البيانات الأساسية","العملاء","edit","70");
INSERT INTO permissions VALUES ("8","clients.delete","web","2026-11-11 16:56:57","2026-11-11 16:56:57","حذف العملاء","basic","البيانات الأساسية","العملاء","delete","80");
INSERT INTO permissions VALUES ("9","suppliers.view","web","2026-11-11 16:56:57","2026-11-11 16:56:57","عرض الموردين","basic","البيانات الأساسية","الموردين","view","90");
INSERT INTO permissions VALUES ("10","suppliers.add","web","2026-11-11 16:56:57","2026-11-11 16:56:57","إضافة الموردين","basic","البيانات الأساسية","الموردين","add","100");
INSERT INTO permissions VALUES ("11","suppliers.edit","web","2026-11-11 16:56:57","2026-11-11 16:56:57","تعديل الموردين","basic","البيانات الأساسية","الموردين","edit","110");
INSERT INTO permissions VALUES ("12","suppliers.delete","web","2026-11-11 16:56:57","2026-11-11 16:56:57","حذف الموردين","basic","البيانات الأساسية","الموردين","delete","120");
INSERT INTO permissions VALUES ("13","funds.view","web","2026-11-11 16:56:57","2026-11-11 16:56:57","عرض الخزائن","basic","البيانات الأساسية","الخزائن","view","130");
INSERT INTO permissions VALUES ("14","funds.add","web","2026-11-11 16:56:57","2026-11-11 16:56:57","إضافة الخزائن","basic","البيانات الأساسية","الخزائن","add","140");
INSERT INTO permissions VALUES ("15","funds.edit","web","2026-11-11 16:56:57","2026-11-11 16:56:57","تعديل الخزائن","basic","البيانات الأساسية","الخزائن","edit","150");
INSERT INTO permissions VALUES ("16","funds.delete","web","2026-11-11 16:56:57","2026-11-11 16:56:57","حذف الخزائن","basic","البيانات الأساسية","الخزائن","delete","160");
INSERT INTO permissions VALUES ("17","banks.view","web","2026-11-11 16:56:57","2026-11-11 16:56:57","عرض البنوك","basic","البيانات الأساسية","البنوك","view","170");
INSERT INTO permissions VALUES ("18","banks.add","web","2026-11-11 16:56:57","2026-11-11 16:56:57","إضافة البنوك","basic","البيانات الأساسية","البنوك","add","180");
INSERT INTO permissions VALUES ("19","banks.edit","web","2026-11-11 16:56:57","2026-11-11 16:56:57","تعديل البنوك","basic","البيانات الأساسية","البنوك","edit","190");
INSERT INTO permissions VALUES ("20","banks.delete","web","2026-11-11 16:56:57","2026-11-11 16:56:57","حذف البنوك","basic","البيانات الأساسية","البنوك","delete","200");
INSERT INTO permissions VALUES ("21","stores.view","web","2026-11-11 16:56:57","2026-11-11 16:56:57","عرض المخازن","basic","البيانات الأساسية","المخازن","view","210");
INSERT INTO permissions VALUES ("22","stores.add","web","2026-11-11 16:56:57","2026-11-11 16:56:57","إضافة المخازن","basic","البيانات الأساسية","المخازن","add","220");
INSERT INTO permissions VALUES ("23","stores.edit","web","2026-11-11 16:56:57","2026-11-11 16:56:57","تعديل المخازن","basic","البيانات الأساسية","المخازن","edit","230");
INSERT INTO permissions VALUES ("24","stores.delete","web","2026-11-11 16:56:57","2026-11-11 16:56:57","حذف المخازن","basic","البيانات الأساسية","المخازن","delete","240");
INSERT INTO permissions VALUES ("25","expenses.view","web","2026-11-11 16:56:57","2026-11-11 16:56:57","عرض المصروفات","basic","البيانات الأساسية","المصروفات","view","250");
INSERT INTO permissions VALUES ("26","expenses.add","web","2026-11-11 16:56:57","2026-11-11 16:56:57","إضافة المصروفات","basic","البيانات الأساسية","المصروفات","add","260");
INSERT INTO permissions VALUES ("27","expenses.edit","web","2026-11-11 16:56:57","2026-11-11 16:56:57","تعديل المصروفات","basic","البيانات الأساسية","المصروفات","edit","270");
INSERT INTO permissions VALUES ("28","expenses.delete","web","2026-11-11 16:56:57","2026-11-11 16:56:57","حذف المصروفات","basic","البيانات الأساسية","المصروفات","delete","280");
INSERT INTO permissions VALUES ("29","revenues.view","web","2026-11-11 16:56:57","2026-11-11 16:56:57","عرض الإيرادات","basic","البيانات الأساسية","الإيرادات","view","290");
INSERT INTO permissions VALUES ("30","revenues.add","web","2026-11-11 16:56:57","2026-11-11 16:56:57","إضافة الإيرادات","basic","البيانات الأساسية","الإيرادات","add","300");
INSERT INTO permissions VALUES ("31","revenues.edit","web","2026-11-11 16:56:57","2026-11-11 16:56:57","تعديل الإيرادات","basic","البيانات الأساسية","الإيرادات","edit","310");
INSERT INTO permissions VALUES ("32","revenues.delete","web","2026-11-11 16:56:57","2026-11-11 16:56:57","حذف الإيرادات","basic","البيانات الأساسية","الإيرادات","delete","320");
INSERT INTO permissions VALUES ("33","creditors.view","web","2026-11-11 16:56:57","2026-11-11 16:56:57","عرض دائنين آخرين","basic","البيانات الأساسية","دائنين آخرين","view","330");
INSERT INTO permissions VALUES ("34","creditors.add","web","2026-11-11 16:56:57","2026-11-11 16:56:57","إضافة دائنين آخرين","basic","البيانات الأساسية","دائنين آخرين","add","340");
INSERT INTO permissions VALUES ("35","creditors.edit","web","2026-11-11 16:56:57","2026-11-11 16:56:57","تعديل دائنين آخرين","basic","البيانات الأساسية","دائنين آخرين","edit","350");
INSERT INTO permissions VALUES ("36","creditors.delete","web","2026-11-11 16:56:57","2026-11-11 16:56:57","حذف دائنين آخرين","basic","البيانات الأساسية","دائنين آخرين","delete","360");
INSERT INTO permissions VALUES ("37","debtors.view","web","2026-11-11 16:56:57","2026-11-11 16:56:57","عرض مدينين آخرين","basic","البيانات الأساسية","مدينين آخرين","view","370");
INSERT INTO permissions VALUES ("38","debtors.add","web","2026-11-11 16:56:57","2026-11-11 16:56:57","إضافة مدينين آخرين","basic","البيانات الأساسية","مدينين آخرين","add","380");
INSERT INTO permissions VALUES ("39","debtors.edit","web","2026-11-11 16:56:57","2026-11-11 16:56:57","تعديل مدينين آخرين","basic","البيانات الأساسية","مدينين آخرين","edit","390");
INSERT INTO permissions VALUES ("40","debtors.delete","web","2026-11-11 16:56:57","2026-11-11 16:56:57","حذف مدينين آخرين","basic","البيانات الأساسية","مدينين آخرين","delete","400");
INSERT INTO permissions VALUES ("41","partners.view","web","2026-11-11 16:56:57","2026-11-11 16:56:57","عرض الشركاء","basic","البيانات الأساسية","الشركاء","view","410");
INSERT INTO permissions VALUES ("42","partners.add","web","2026-11-11 16:56:57","2026-11-11 16:56:57","إضافة الشركاء","basic","البيانات الأساسية","الشركاء","add","420");
INSERT INTO permissions VALUES ("43","partners.edit","web","2026-11-11 16:56:57","2026-11-11 16:56:57","تعديل الشركاء","basic","البيانات الأساسية","الشركاء","edit","430");
INSERT INTO permissions VALUES ("44","partners.delete","web","2026-11-11 16:56:57","2026-11-11 16:56:57","حذف الشركاء","basic","البيانات الأساسية","الشركاء","delete","440");
INSERT INTO permissions VALUES ("45","assets.view","web","2026-11-11 16:56:57","2026-11-11 16:56:57","عرض الأصول","basic","البيانات الأساسية","الأصول","view","450");
INSERT INTO permissions VALUES ("46","assets.add","web","2026-11-11 16:56:57","2026-11-11 16:56:57","إضافة الأصول","basic","البيانات الأساسية","الأصول","add","460");
INSERT INTO permissions VALUES ("47","assets.edit","web","2026-11-11 16:56:57","2026-11-11 16:56:57","تعديل الأصول","basic","البيانات الأساسية","الأصول","edit","470");
INSERT INTO permissions VALUES ("48","assets.delete","web","2026-11-11 16:56:57","2026-11-11 16:56:57","حذف الأصول","basic","البيانات الأساسية","الأصول","delete","480");
INSERT INTO permissions VALUES ("49","rentables.view","web","2026-11-11 16:56:57","2026-11-11 16:56:57","عرض الأصول القابلة للتأجير","basic","البيانات الأساسية","الأصول القابلة للتأجير","view","490");
INSERT INTO permissions VALUES ("50","rentables.add","web","2026-11-11 16:56:57","2026-11-11 16:56:57","إضافة الأصول القابلة للتأجير","basic","البيانات الأساسية","الأصول القابلة للتأجير","add","500");
INSERT INTO permissions VALUES ("51","rentables.edit","web","2026-11-11 16:56:57","2026-11-11 16:56:57","تعديل الأصول القابلة للتأجير","basic","البيانات الأساسية","الأصول القابلة للتأجير","edit","510");
INSERT INTO permissions VALUES ("52","rentables.delete","web","2026-11-11 16:56:57","2026-11-11 16:56:57","حذف الأصول القابلة للتأجير","basic","البيانات الأساسية","الأصول القابلة للتأجير","delete","520");
INSERT INTO permissions VALUES ("53","delivery.view","web","2026-11-11 16:56:57","2026-11-11 16:56:57","عرض الديليفري","basic","البيانات الأساسية","الديليفري","view","530");
INSERT INTO permissions VALUES ("54","delivery.add","web","2026-11-11 16:56:57","2026-11-11 16:56:57","إضافة الديليفري","basic","البيانات الأساسية","الديليفري","add","540");
INSERT INTO permissions VALUES ("55","delivery.edit","web","2026-11-11 16:56:57","2026-11-11 16:56:57","تعديل الديليفري","basic","البيانات الأساسية","الديليفري","edit","550");
INSERT INTO permissions VALUES ("56","delivery.delete","web","2026-11-11 16:56:57","2026-11-11 16:56:57","حذف الديليفري","basic","البيانات الأساسية","الديليفري","delete","560");
INSERT INTO permissions VALUES ("57","towns.view","web","2026-11-11 16:56:57","2026-11-11 16:56:57","عرض المدن والمناطق","basic","البيانات الأساسية","المدن والمناطق","view","570");
INSERT INTO permissions VALUES ("58","towns.add","web","2026-11-11 16:56:57","2026-11-11 16:56:57","إضافة المدن والمناطق","basic","البيانات الأساسية","المدن والمناطق","add","580");
INSERT INTO permissions VALUES ("59","towns.edit","web","2026-11-11 16:56:57","2026-11-11 16:56:57","تعديل المدن والمناطق","basic","البيانات الأساسية","المدن والمناطق","edit","590");
INSERT INTO permissions VALUES ("60","towns.delete","web","2026-11-11 16:56:57","2026-11-11 16:56:57","حذف المدن والمناطق","basic","البيانات الأساسية","المدن والمناطق","delete","600");
INSERT INTO permissions VALUES ("61","items.view","web","2026-11-11 16:56:57","2026-11-11 16:56:57","عرض الأصناف","inventory","المخزون","الأصناف","view","610");
INSERT INTO permissions VALUES ("62","items.add","web","2026-11-11 16:56:57","2026-11-11 16:56:57","إضافة الأصناف","inventory","المخزون","الأصناف","add","620");
INSERT INTO permissions VALUES ("63","items.edit","web","2026-11-11 16:56:57","2026-11-11 16:56:57","تعديل الأصناف","inventory","المخزون","الأصناف","edit","630");
INSERT INTO permissions VALUES ("64","items.delete","web","2026-11-11 16:56:57","2026-11-11 16:56:57","حذف الأصناف","inventory","المخزون","الأصناف","delete","640");
INSERT INTO permissions VALUES ("65","item_groups.view","web","2026-11-11 16:56:57","2026-11-11 16:56:57","عرض مجموعات وتصنيفات الأصناف","inventory","المخزون","مجموعات وتصنيفات الأصناف","view","650");
INSERT INTO permissions VALUES ("66","item_groups.add","web","2026-11-11 16:56:57","2026-11-11 16:56:57","إضافة مجموعات وتصنيفات الأصناف","inventory","المخزون","مجموعات وتصنيفات الأصناف","add","660");
INSERT INTO permissions VALUES ("67","item_groups.edit","web","2026-11-11 16:56:57","2026-11-11 16:56:57","تعديل مجموعات وتصنيفات الأصناف","inventory","المخزون","مجموعات وتصنيفات الأصناف","edit","670");
INSERT INTO permissions VALUES ("68","item_groups.delete","web","2026-11-11 16:56:57","2026-11-11 16:56:57","حذف مجموعات وتصنيفات الأصناف","inventory","المخزون","مجموعات وتصنيفات الأصناف","delete","680");
INSERT INTO permissions VALUES ("69","units.view","web","2026-11-11 16:56:57","2026-11-11 16:56:57","عرض الوحدات","inventory","المخزون","الوحدات","view","690");
INSERT INTO permissions VALUES ("70","units.add","web","2026-11-11 16:56:57","2026-11-11 16:56:57","إضافة الوحدات","inventory","المخزون","الوحدات","add","700");
INSERT INTO permissions VALUES ("71","units.edit","web","2026-11-11 16:56:57","2026-11-11 16:56:57","تعديل الوحدات","inventory","المخزون","الوحدات","edit","710");
INSERT INTO permissions VALUES ("72","units.delete","web","2026-11-11 16:56:57","2026-11-11 16:56:57","حذف الوحدات","inventory","المخزون","الوحدات","delete","720");
INSERT INTO permissions VALUES ("73","items.pricing","web","2026-11-11 16:56:57","2026-11-11 16:56:57","التسعير والباركود وإعادة احتساب التكلفة","inventory","المخزون","التسعير والباركود وإعادة احتساب التكلفة",NULL,"730");
INSERT INTO permissions VALUES ("74","items.opening_balance","web","2026-11-11 16:56:57","2026-11-11 16:56:57","أرصدة أول المدة للأصناف","inventory","المخزون","أرصدة أول المدة للأصناف",NULL,"740");
INSERT INTO permissions VALUES ("75","inventory.audit","web","2026-11-11 16:56:57","2026-11-11 16:56:57","جرد المخزن","inventory","المخزون","جرد المخزن",NULL,"750");
INSERT INTO permissions VALUES ("76","stock.sell_negative","web","2026-11-11 16:56:57","2026-11-11 16:56:57","البيع بأكثر من رصيد المخزن","inventory","المخزون","البيع بأكثر من رصيد المخزن",NULL,"760");
INSERT INTO permissions VALUES ("77","sales.view","web","2026-11-11 16:56:57","2026-11-11 16:56:57","عرض فواتير المبيعات","sales","المبيعات","فواتير المبيعات","view","770");
INSERT INTO permissions VALUES ("78","sales.add","web","2026-11-11 16:56:57","2026-11-11 16:56:57","إضافة فواتير المبيعات","sales","المبيعات","فواتير المبيعات","add","780");
INSERT INTO permissions VALUES ("79","sales.edit","web","2026-11-11 16:56:57","2026-11-11 16:56:57","تعديل فواتير المبيعات","sales","المبيعات","فواتير المبيعات","edit","790");
INSERT INTO permissions VALUES ("80","sales.delete","web","2026-11-11 16:56:57","2026-11-11 16:56:57","حذف فواتير المبيعات","sales","المبيعات","فواتير المبيعات","delete","800");
INSERT INTO permissions VALUES ("81","sales_returns.view","web","2026-11-11 16:56:57","2026-11-11 16:56:57","عرض مردود المبيعات","sales","المبيعات","مردود المبيعات","view","810");
INSERT INTO permissions VALUES ("82","sales_returns.add","web","2026-11-11 16:56:57","2026-11-11 16:56:57","إضافة مردود المبيعات","sales","المبيعات","مردود المبيعات","add","820");
INSERT INTO permissions VALUES ("83","sales_returns.edit","web","2026-11-11 16:56:57","2026-11-11 16:56:57","تعديل مردود المبيعات","sales","المبيعات","مردود المبيعات","edit","830");
INSERT INTO permissions VALUES ("84","sales_returns.delete","web","2026-11-11 16:56:57","2026-11-11 16:56:57","حذف مردود المبيعات","sales","المبيعات","مردود المبيعات","delete","840");
INSERT INTO permissions VALUES ("85","sales_orders.view","web","2026-11-11 16:56:57","2026-11-11 16:56:57","عرض أوامر البيع","sales","المبيعات","أوامر البيع","view","850");
INSERT INTO permissions VALUES ("86","sales_orders.add","web","2026-11-11 16:56:57","2026-11-11 16:56:57","إضافة أوامر البيع","sales","المبيعات","أوامر البيع","add","860");
INSERT INTO permissions VALUES ("87","sales_orders.edit","web","2026-11-11 16:56:57","2026-11-11 16:56:57","تعديل أوامر البيع","sales","المبيعات","أوامر البيع","edit","870");
INSERT INTO permissions VALUES ("88","sales_orders.delete","web","2026-11-11 16:56:57","2026-11-11 16:56:57","حذف أوامر البيع","sales","المبيعات","أوامر البيع","delete","880");
INSERT INTO permissions VALUES ("89","quotations.view","web","2026-11-11 16:56:57","2026-11-11 16:56:57","عرض عروض الأسعار","sales","المبيعات","عروض الأسعار","view","890");
INSERT INTO permissions VALUES ("90","quotations.add","web","2026-11-11 16:56:57","2026-11-11 16:56:57","إضافة عروض الأسعار","sales","المبيعات","عروض الأسعار","add","900");
INSERT INTO permissions VALUES ("91","quotations.edit","web","2026-11-11 16:56:57","2026-11-11 16:56:57","تعديل عروض الأسعار","sales","المبيعات","عروض الأسعار","edit","910");
INSERT INTO permissions VALUES ("92","quotations.delete","web","2026-11-11 16:56:57","2026-11-11 16:56:57","حذف عروض الأسعار","sales","المبيعات","عروض الأسعار","delete","920");
INSERT INTO permissions VALUES ("93","pos.use","web","2026-11-11 16:56:57","2026-11-11 16:56:57","استخدام نقطة البيع","pos","نقاط البيع","استخدام نقطة البيع",NULL,"930");
INSERT INTO permissions VALUES ("94","pos.cancel_order","web","2026-11-11 16:56:57","2026-11-11 16:56:57","إلغاء طلب أو حذف صنف من طلب","pos","نقاط البيع","إلغاء طلب أو حذف صنف من طلب",NULL,"940");
INSERT INTO permissions VALUES ("95","pos.delivery","web","2026-11-11 16:56:57","2026-11-11 16:56:57","طلبات الديليفري من الكاشير","pos","نقاط البيع","طلبات الديليفري من الكاشير",NULL,"950");
INSERT INTO permissions VALUES ("96","tables.view","web","2026-11-11 16:56:57","2026-11-11 16:56:57","عرض الطاولات","pos","نقاط البيع","الطاولات","view","960");
INSERT INTO permissions VALUES ("97","tables.add","web","2026-11-11 16:56:57","2026-11-11 16:56:57","إضافة الطاولات","pos","نقاط البيع","الطاولات","add","970");
INSERT INTO permissions VALUES ("98","tables.edit","web","2026-11-11 16:56:57","2026-11-11 16:56:57","تعديل الطاولات","pos","نقاط البيع","الطاولات","edit","980");
INSERT INTO permissions VALUES ("99","tables.delete","web","2026-11-11 16:56:57","2026-11-11 16:56:57","حذف الطاولات","pos","نقاط البيع","الطاولات","delete","990");
INSERT INTO permissions VALUES ("100","pos.closed_sessions","web","2026-11-11 16:56:57","2026-11-11 16:56:57","الجلسات المقفولة","pos","نقاط البيع","الجلسات المقفولة",NULL,"1000");
INSERT INTO permissions VALUES ("101","pos.close_shift","web","2026-11-11 16:56:57","2026-11-11 16:56:57","تقفيل الشيفت","pos","نقاط البيع","تقفيل الشيفت",NULL,"1010");
INSERT INTO permissions VALUES ("102","pos.view_shift_expected","web","2026-11-11 16:56:57","2026-11-11 16:56:57","عرض المتوقع في تقفيل الشيفت (حتى مع الجرد الأعمى)","pos","نقاط البيع","عرض المتوقع في تقفيل الشيفت (حتى مع الجرد الأعمى)",NULL,"1020");
INSERT INTO permissions VALUES ("103","visits.view","web","2026-11-11 16:56:57","2026-11-11 16:56:57","عرض الزيارات","visits","الزيارات","الزيارات","view","1030");
INSERT INTO permissions VALUES ("104","visits.add","web","2026-11-11 16:56:57","2026-11-11 16:56:57","إضافة الزيارات","visits","الزيارات","الزيارات","add","1040");
INSERT INTO permissions VALUES ("105","visits.edit","web","2026-11-11 16:56:57","2026-11-11 16:56:57","تعديل الزيارات","visits","الزيارات","الزيارات","edit","1050");
INSERT INTO permissions VALUES ("106","visits.delete","web","2026-11-11 16:56:57","2026-11-11 16:56:57","حذف الزيارات","visits","الزيارات","الزيارات","delete","1060");
INSERT INTO permissions VALUES ("107","visit_types.view","web","2026-11-11 16:56:57","2026-11-11 16:56:57","عرض أنواع الزيارات","visits","الزيارات","أنواع الزيارات","view","1070");
INSERT INTO permissions VALUES ("108","visit_types.add","web","2026-11-11 16:56:57","2026-11-11 16:56:57","إضافة أنواع الزيارات","visits","الزيارات","أنواع الزيارات","add","1080");
INSERT INTO permissions VALUES ("109","visit_types.edit","web","2026-11-11 16:56:57","2026-11-11 16:56:57","تعديل أنواع الزيارات","visits","الزيارات","أنواع الزيارات","edit","1090");
INSERT INTO permissions VALUES ("110","visit_types.delete","web","2026-11-11 16:56:57","2026-11-11 16:56:57","حذف أنواع الزيارات","visits","الزيارات","أنواع الزيارات","delete","1100");
INSERT INTO permissions VALUES ("111","cards.view","web","2026-11-11 16:56:57","2026-11-11 16:56:57","عرض الكروت","cards","إدارة الكروت","الكروت","view","1110");
INSERT INTO permissions VALUES ("112","cards.add","web","2026-11-11 16:56:57","2026-11-11 16:56:57","إضافة الكروت","cards","إدارة الكروت","الكروت","add","1120");
INSERT INTO permissions VALUES ("113","cards.edit","web","2026-11-11 16:56:57","2026-11-11 16:56:57","تعديل الكروت","cards","إدارة الكروت","الكروت","edit","1130");
INSERT INTO permissions VALUES ("114","cards.delete","web","2026-11-11 16:56:57","2026-11-11 16:56:57","حذف الكروت","cards","إدارة الكروت","الكروت","delete","1140");
INSERT INTO permissions VALUES ("115","cards.pass","web","2026-11-11 16:56:57","2026-11-11 16:56:57","تمرير كارت","cards","إدارة الكروت","تمرير كارت",NULL,"1150");
INSERT INTO permissions VALUES ("116","purchases.view","web","2026-11-11 16:56:57","2026-11-11 16:56:57","عرض فواتير المشتريات","purchases","المشتريات","فواتير المشتريات","view","1160");
INSERT INTO permissions VALUES ("117","purchases.add","web","2026-11-11 16:56:57","2026-11-11 16:56:57","إضافة فواتير المشتريات","purchases","المشتريات","فواتير المشتريات","add","1170");
INSERT INTO permissions VALUES ("118","purchases.edit","web","2026-11-11 16:56:57","2026-11-11 16:56:57","تعديل فواتير المشتريات","purchases","المشتريات","فواتير المشتريات","edit","1180");
INSERT INTO permissions VALUES ("119","purchases.delete","web","2026-11-11 16:56:57","2026-11-11 16:56:57","حذف فواتير المشتريات","purchases","المشتريات","فواتير المشتريات","delete","1190");
INSERT INTO permissions VALUES ("120","purchase_returns.view","web","2026-11-11 16:56:57","2026-11-11 16:56:57","عرض مردود المشتريات","purchases","المشتريات","مردود المشتريات","view","1200");
INSERT INTO permissions VALUES ("121","purchase_returns.add","web","2026-11-11 16:56:57","2026-11-11 16:56:57","إضافة مردود المشتريات","purchases","المشتريات","مردود المشتريات","add","1210");
INSERT INTO permissions VALUES ("122","purchase_returns.edit","web","2026-11-11 16:56:57","2026-11-11 16:56:57","تعديل مردود المشتريات","purchases","المشتريات","مردود المشتريات","edit","1220");
INSERT INTO permissions VALUES ("123","purchase_returns.delete","web","2026-11-11 16:56:57","2026-11-11 16:56:57","حذف مردود المشتريات","purchases","المشتريات","مردود المشتريات","delete","1230");
INSERT INTO permissions VALUES ("124","purchase_orders.view","web","2026-11-11 16:56:57","2026-11-11 16:56:57","عرض أوامر الشراء","purchases","المشتريات","أوامر الشراء","view","1240");
INSERT INTO permissions VALUES ("125","purchase_orders.add","web","2026-11-11 16:56:57","2026-11-11 16:56:57","إضافة أوامر الشراء","purchases","المشتريات","أوامر الشراء","add","1250");
INSERT INTO permissions VALUES ("126","purchase_orders.edit","web","2026-11-11 16:56:57","2026-11-11 16:56:57","تعديل أوامر الشراء","purchases","المشتريات","أوامر الشراء","edit","1260");
INSERT INTO permissions VALUES ("127","purchase_orders.delete","web","2026-11-11 16:56:57","2026-11-11 16:56:57","حذف أوامر الشراء","purchases","المشتريات","أوامر الشراء","delete","1270");
INSERT INTO permissions VALUES ("128","receipts.view","web","2026-11-11 16:56:57","2026-11-11 16:56:57","عرض سندات القبض","vouchers","السندات","سندات القبض","view","1280");
INSERT INTO permissions VALUES ("129","receipts.add","web","2026-11-11 16:56:57","2026-11-11 16:56:57","إضافة سندات القبض","vouchers","السندات","سندات القبض","add","1290");
INSERT INTO permissions VALUES ("130","receipts.edit","web","2026-11-11 16:56:57","2026-11-11 16:56:57","تعديل سندات القبض","vouchers","السندات","سندات القبض","edit","1300");
INSERT INTO permissions VALUES ("131","receipts.delete","web","2026-11-11 16:56:57","2026-11-11 16:56:57","حذف سندات القبض","vouchers","السندات","سندات القبض","delete","1310");
INSERT INTO permissions VALUES ("132","payments.view","web","2026-11-11 16:56:57","2026-11-11 16:56:57","عرض سندات الصرف","vouchers","السندات","سندات الصرف","view","1320");
INSERT INTO permissions VALUES ("133","payments.add","web","2026-11-11 16:56:57","2026-11-11 16:56:57","إضافة سندات الصرف","vouchers","السندات","سندات الصرف","add","1330");
INSERT INTO permissions VALUES ("134","payments.edit","web","2026-11-11 16:56:57","2026-11-11 16:56:57","تعديل سندات الصرف","vouchers","السندات","سندات الصرف","edit","1340");
INSERT INTO permissions VALUES ("135","payments.delete","web","2026-11-11 16:56:57","2026-11-11 16:56:57","حذف سندات الصرف","vouchers","السندات","سندات الصرف","delete","1350");
INSERT INTO permissions VALUES ("136","journals.view","web","2026-11-11 16:56:57","2026-11-11 16:56:57","عرض قيود اليومية","accounting","الحسابات العامة","قيود اليومية","view","1360");
INSERT INTO permissions VALUES ("137","journals.add","web","2026-11-11 16:56:57","2026-11-11 16:56:57","إضافة قيود اليومية","accounting","الحسابات العامة","قيود اليومية","add","1370");
INSERT INTO permissions VALUES ("138","journals.edit","web","2026-11-11 16:56:57","2026-11-11 16:56:57","تعديل قيود اليومية","accounting","الحسابات العامة","قيود اليومية","edit","1380");
INSERT INTO permissions VALUES ("139","journals.delete","web","2026-11-11 16:56:57","2026-11-11 16:56:57","حذف قيود اليومية","accounting","الحسابات العامة","قيود اليومية","delete","1390");
INSERT INTO permissions VALUES ("140","accounts.chart","web","2026-11-11 16:56:57","2026-11-11 16:56:57","دليل الحسابات وقائمة الحسابات","accounting","الحسابات العامة","دليل الحسابات وقائمة الحسابات",NULL,"1400");
INSERT INTO permissions VALUES ("141","accounts.opening_balance","web","2026-11-11 16:56:57","2026-11-11 16:56:57","أرصدة أول المدة للحسابات","accounting","الحسابات العامة","أرصدة أول المدة للحسابات",NULL,"1410");
INSERT INTO permissions VALUES ("142","assets.operations","web","2026-11-11 16:56:57","2026-11-11 16:56:57","شراء / بيع / إهلاك أصل","assets_ops","عمليات الأصول","شراء / بيع / إهلاك أصل",NULL,"1420");
INSERT INTO permissions VALUES ("143","rent_contracts.view","web","2026-11-11 16:56:57","2026-11-11 16:56:57","عرض عقود الإيجار","rents","التأجير","عقود الإيجار","view","1430");
INSERT INTO permissions VALUES ("144","rent_contracts.add","web","2026-11-11 16:56:57","2026-11-11 16:56:57","إضافة عقود الإيجار","rents","التأجير","عقود الإيجار","add","1440");
INSERT INTO permissions VALUES ("145","rent_contracts.edit","web","2026-11-11 16:56:57","2026-11-11 16:56:57","تعديل عقود الإيجار","rents","التأجير","عقود الإيجار","edit","1450");
INSERT INTO permissions VALUES ("146","rent_contracts.delete","web","2026-11-11 16:56:57","2026-11-11 16:56:57","حذف عقود الإيجار","rents","التأجير","عقود الإيجار","delete","1460");
INSERT INTO permissions VALUES ("147","rent_installments.collect","web","2026-11-11 16:56:57","2026-11-11 16:56:57","تحصيل الأقساط","rents","التأجير","تحصيل الأقساط",NULL,"1470");
INSERT INTO permissions VALUES ("148","reservations.view","web","2026-11-11 16:56:57","2026-11-11 16:56:57","عرض الحجوزات","clinics","العيادات","الحجوزات","view","1480");
INSERT INTO permissions VALUES ("149","reservations.add","web","2026-11-11 16:56:57","2026-11-11 16:56:57","إضافة الحجوزات","clinics","العيادات","الحجوزات","add","1490");
INSERT INTO permissions VALUES ("150","reservations.edit","web","2026-11-11 16:56:57","2026-11-11 16:56:57","تعديل الحجوزات","clinics","العيادات","الحجوزات","edit","1500");
INSERT INTO permissions VALUES ("151","reservations.delete","web","2026-11-11 16:56:57","2026-11-11 16:56:57","حذف الحجوزات","clinics","العيادات","الحجوزات","delete","1510");
INSERT INTO permissions VALUES ("152","reservations.view_ended","web","2026-11-11 16:56:57","2026-11-11 16:56:57","عرض الحجوزات المنتهية","clinics","العيادات","عرض الحجوزات المنتهية",NULL,"1520");
INSERT INTO permissions VALUES ("153","reservations.view_total","web","2026-11-11 16:56:57","2026-11-11 16:56:57","عرض إجمالي الحجوزات","clinics","العيادات","عرض إجمالي الحجوزات",NULL,"1530");
INSERT INTO permissions VALUES ("154","patients.view","web","2026-11-11 16:56:57","2026-11-11 16:56:57","عرض بيانات المرضى والروشتات","clinics","العيادات","بيانات المرضى والروشتات","view","1540");
INSERT INTO permissions VALUES ("155","patients.add","web","2026-11-11 16:56:57","2026-11-11 16:56:57","إضافة بيانات المرضى والروشتات","clinics","العيادات","بيانات المرضى والروشتات","add","1550");
INSERT INTO permissions VALUES ("156","patients.edit","web","2026-11-11 16:56:57","2026-11-11 16:56:57","تعديل بيانات المرضى والروشتات","clinics","العيادات","بيانات المرضى والروشتات","edit","1560");
INSERT INTO permissions VALUES ("157","patients.delete","web","2026-11-11 16:56:57","2026-11-11 16:56:57","حذف بيانات المرضى والروشتات","clinics","العيادات","بيانات المرضى والروشتات","delete","1570");
INSERT INTO permissions VALUES ("158","drugs.view","web","2026-11-11 16:56:57","2026-11-11 16:56:57","عرض الأدوية","clinics","العيادات","الأدوية","view","1580");
INSERT INTO permissions VALUES ("159","drugs.add","web","2026-11-11 16:56:57","2026-11-11 16:56:57","إضافة الأدوية","clinics","العيادات","الأدوية","add","1590");
INSERT INTO permissions VALUES ("160","drugs.edit","web","2026-11-11 16:56:57","2026-11-11 16:56:57","تعديل الأدوية","clinics","العيادات","الأدوية","edit","1600");
INSERT INTO permissions VALUES ("161","drugs.delete","web","2026-11-11 16:56:57","2026-11-11 16:56:57","حذف الأدوية","clinics","العيادات","الأدوية","delete","1610");
INSERT INTO permissions VALUES ("162","employees.view","web","2026-11-11 16:56:57","2026-11-11 16:56:57","عرض الموظفين","hr","الموارد البشرية","الموظفين","view","1620");
INSERT INTO permissions VALUES ("163","employees.add","web","2026-11-11 16:56:57","2026-11-11 16:56:57","إضافة الموظفين","hr","الموارد البشرية","الموظفين","add","1630");
INSERT INTO permissions VALUES ("164","employees.edit","web","2026-11-11 16:56:57","2026-11-11 16:56:57","تعديل الموظفين","hr","الموارد البشرية","الموظفين","edit","1640");
INSERT INTO permissions VALUES ("165","employees.delete","web","2026-11-11 16:56:57","2026-11-11 16:56:57","حذف الموظفين","hr","الموارد البشرية","الموظفين","delete","1650");
INSERT INTO permissions VALUES ("166","jobs.view","web","2026-11-11 16:56:57","2026-11-11 16:56:57","عرض الوظائف","hr","الموارد البشرية","الوظائف","view","1660");
INSERT INTO permissions VALUES ("167","jobs.add","web","2026-11-11 16:56:57","2026-11-11 16:56:57","إضافة الوظائف","hr","الموارد البشرية","الوظائف","add","1670");
INSERT INTO permissions VALUES ("168","jobs.edit","web","2026-11-11 16:56:57","2026-11-11 16:56:57","تعديل الوظائف","hr","الموارد البشرية","الوظائف","edit","1680");
INSERT INTO permissions VALUES ("169","jobs.delete","web","2026-11-11 16:56:57","2026-11-11 16:56:57","حذف الوظائف","hr","الموارد البشرية","الوظائف","delete","1690");
INSERT INTO permissions VALUES ("170","job_levels.view","web","2026-11-11 16:56:57","2026-11-11 16:56:57","عرض المستويات الوظيفية","hr","الموارد البشرية","المستويات الوظيفية","view","1700");
INSERT INTO permissions VALUES ("171","job_levels.add","web","2026-11-11 16:56:57","2026-11-11 16:56:57","إضافة المستويات الوظيفية","hr","الموارد البشرية","المستويات الوظيفية","add","1710");
INSERT INTO permissions VALUES ("172","job_levels.edit","web","2026-11-11 16:56:57","2026-11-11 16:56:57","تعديل المستويات الوظيفية","hr","الموارد البشرية","المستويات الوظيفية","edit","1720");
INSERT INTO permissions VALUES ("173","job_levels.delete","web","2026-11-11 16:56:57","2026-11-11 16:56:57","حذف المستويات الوظيفية","hr","الموارد البشرية","المستويات الوظيفية","delete","1730");
INSERT INTO permissions VALUES ("174","job_rules.view","web","2026-11-11 16:56:57","2026-11-11 16:56:57","عرض قواعد الوظائف","hr","الموارد البشرية","قواعد الوظائف","view","1740");
INSERT INTO permissions VALUES ("175","job_rules.add","web","2026-11-11 16:56:57","2026-11-11 16:56:57","إضافة قواعد الوظائف","hr","الموارد البشرية","قواعد الوظائف","add","1750");
INSERT INTO permissions VALUES ("176","job_rules.edit","web","2026-11-11 16:56:57","2026-11-11 16:56:57","تعديل قواعد الوظائف","hr","الموارد البشرية","قواعد الوظائف","edit","1760");
INSERT INTO permissions VALUES ("177","job_rules.delete","web","2026-11-11 16:56:57","2026-11-11 16:56:57","حذف قواعد الوظائف","hr","الموارد البشرية","قواعد الوظائف","delete","1770");
INSERT INTO permissions VALUES ("178","departments.view","web","2026-11-11 16:56:57","2026-11-11 16:56:57","عرض الأقسام","hr","الموارد البشرية","الأقسام","view","1780");
INSERT INTO permissions VALUES ("179","departments.add","web","2026-11-11 16:56:57","2026-11-11 16:56:57","إضافة الأقسام","hr","الموارد البشرية","الأقسام","add","1790");
INSERT INTO permissions VALUES ("180","departments.edit","web","2026-11-11 16:56:57","2026-11-11 16:56:57","تعديل الأقسام","hr","الموارد البشرية","الأقسام","edit","1800");
INSERT INTO permissions VALUES ("181","departments.delete","web","2026-11-11 16:56:57","2026-11-11 16:56:57","حذف الأقسام","hr","الموارد البشرية","الأقسام","delete","1810");
INSERT INTO permissions VALUES ("182","shifts.view","web","2026-11-11 16:56:57","2026-11-11 16:56:57","عرض الورديات","hr","الموارد البشرية","الورديات","view","1820");
INSERT INTO permissions VALUES ("183","shifts.add","web","2026-11-11 16:56:57","2026-11-11 16:56:57","إضافة الورديات","hr","الموارد البشرية","الورديات","add","1830");
INSERT INTO permissions VALUES ("184","shifts.edit","web","2026-11-11 16:56:57","2026-11-11 16:56:57","تعديل الورديات","hr","الموارد البشرية","الورديات","edit","1840");
INSERT INTO permissions VALUES ("185","shifts.delete","web","2026-11-11 16:56:57","2026-11-11 16:56:57","حذف الورديات","hr","الموارد البشرية","الورديات","delete","1850");
INSERT INTO permissions VALUES ("186","hr_operations.view","web","2026-11-11 16:56:57","2026-11-11 16:56:57","عرض تعريف العمليات وخطواتها","hr","الموارد البشرية","تعريف العمليات وخطواتها","view","1860");
INSERT INTO permissions VALUES ("187","hr_operations.add","web","2026-11-11 16:56:57","2026-11-11 16:56:57","إضافة تعريف العمليات وخطواتها","hr","الموارد البشرية","تعريف العمليات وخطواتها","add","1870");
INSERT INTO permissions VALUES ("188","hr_operations.edit","web","2026-11-11 16:56:57","2026-11-11 16:56:57","تعديل تعريف العمليات وخطواتها","hr","الموارد البشرية","تعريف العمليات وخطواتها","edit","1880");
INSERT INTO permissions VALUES ("189","hr_operations.delete","web","2026-11-11 16:56:57","2026-11-11 16:56:57","حذف تعريف العمليات وخطواتها","hr","الموارد البشرية","تعريف العمليات وخطواتها","delete","1890");
INSERT INTO permissions VALUES ("190","employee_operations.view","web","2026-11-11 16:56:57","2026-11-11 16:56:57","عرض عمليات الموظفين","hr","الموارد البشرية","عمليات الموظفين","view","1900");
INSERT INTO permissions VALUES ("191","employee_operations.add","web","2026-11-11 16:56:57","2026-11-11 16:56:57","إضافة عمليات الموظفين","hr","الموارد البشرية","عمليات الموظفين","add","1910");
INSERT INTO permissions VALUES ("192","employee_operations.edit","web","2026-11-11 16:56:57","2026-11-11 16:56:57","تعديل عمليات الموظفين","hr","الموارد البشرية","عمليات الموظفين","edit","1920");
INSERT INTO permissions VALUES ("193","employee_operations.delete","web","2026-11-11 16:56:57","2026-11-11 16:56:57","حذف عمليات الموظفين","hr","الموارد البشرية","عمليات الموظفين","delete","1930");
INSERT INTO permissions VALUES ("194","pulse.view","web","2026-11-11 16:56:57","2026-11-11 16:56:57","عرض تقييمات Pulse","hr","الموارد البشرية","تقييمات Pulse","view","1940");
INSERT INTO permissions VALUES ("195","pulse.add","web","2026-11-11 16:56:57","2026-11-11 16:56:57","إضافة تقييمات Pulse","hr","الموارد البشرية","تقييمات Pulse","add","1950");
INSERT INTO permissions VALUES ("196","pulse.edit","web","2026-11-11 16:56:57","2026-11-11 16:56:57","تعديل تقييمات Pulse","hr","الموارد البشرية","تقييمات Pulse","edit","1960");
INSERT INTO permissions VALUES ("197","pulse.delete","web","2026-11-11 16:56:57","2026-11-11 16:56:57","حذف تقييمات Pulse","hr","الموارد البشرية","تقييمات Pulse","delete","1970");
INSERT INTO permissions VALUES ("198","pulse_types.view","web","2026-11-11 16:56:57","2026-11-11 16:56:57","عرض أنواع التقييم","hr","الموارد البشرية","أنواع التقييم","view","1980");
INSERT INTO permissions VALUES ("199","pulse_types.add","web","2026-11-11 16:56:57","2026-11-11 16:56:57","إضافة أنواع التقييم","hr","الموارد البشرية","أنواع التقييم","add","1990");
INSERT INTO permissions VALUES ("200","pulse_types.edit","web","2026-11-11 16:56:57","2026-11-11 16:56:57","تعديل أنواع التقييم","hr","الموارد البشرية","أنواع التقييم","edit","2000");
INSERT INTO permissions VALUES ("201","pulse_types.delete","web","2026-11-11 16:56:57","2026-11-11 16:56:57","حذف أنواع التقييم","hr","الموارد البشرية","أنواع التقييم","delete","2010");
INSERT INTO permissions VALUES ("202","tasks.view","web","2026-11-11 16:56:57","2026-11-11 16:56:57","عرض المهام","tasks","المهام والأداء والتعاقدات","المهام","view","2020");
INSERT INTO permissions VALUES ("203","tasks.add","web","2026-11-11 16:56:57","2026-11-11 16:56:57","إضافة المهام","tasks","المهام والأداء والتعاقدات","المهام","add","2030");
INSERT INTO permissions VALUES ("204","tasks.edit","web","2026-11-11 16:56:57","2026-11-11 16:56:57","تعديل المهام","tasks","المهام والأداء والتعاقدات","المهام","edit","2040");
INSERT INTO permissions VALUES ("205","tasks.delete","web","2026-11-11 16:56:57","2026-11-11 16:56:57","حذف المهام","tasks","المهام والأداء والتعاقدات","المهام","delete","2050");
INSERT INTO permissions VALUES ("206","tasks.view_all","web","2026-11-11 16:56:57","2026-11-11 16:56:57","عرض مهام كل الموظفين","tasks","المهام والأداء والتعاقدات","عرض مهام كل الموظفين",NULL,"2060");
INSERT INTO permissions VALUES ("207","kpis.view","web","2026-11-11 16:56:57","2026-11-11 16:56:57","عرض معدلات الأداء","tasks","المهام والأداء والتعاقدات","معدلات الأداء","view","2070");
INSERT INTO permissions VALUES ("208","kpis.add","web","2026-11-11 16:56:57","2026-11-11 16:56:57","إضافة معدلات الأداء","tasks","المهام والأداء والتعاقدات","معدلات الأداء","add","2080");
INSERT INTO permissions VALUES ("209","kpis.edit","web","2026-11-11 16:56:57","2026-11-11 16:56:57","تعديل معدلات الأداء","tasks","المهام والأداء والتعاقدات","معدلات الأداء","edit","2090");
INSERT INTO permissions VALUES ("210","kpis.delete","web","2026-11-11 16:56:57","2026-11-11 16:56:57","حذف معدلات الأداء","tasks","المهام والأداء والتعاقدات","معدلات الأداء","delete","2100");
INSERT INTO permissions VALUES ("211","employee_kpis.view","web","2026-11-11 16:56:57","2026-11-11 16:56:57","عرض أوزان الأداء للموظفين","tasks","المهام والأداء والتعاقدات","أوزان الأداء للموظفين","view","2110");
INSERT INTO permissions VALUES ("212","employee_kpis.add","web","2026-11-11 16:56:57","2026-11-11 16:56:57","إضافة أوزان الأداء للموظفين","tasks","المهام والأداء والتعاقدات","أوزان الأداء للموظفين","add","2120");
INSERT INTO permissions VALUES ("213","employee_kpis.edit","web","2026-11-11 16:56:57","2026-11-11 16:56:57","تعديل أوزان الأداء للموظفين","tasks","المهام والأداء والتعاقدات","أوزان الأداء للموظفين","edit","2130");
INSERT INTO permissions VALUES ("214","employee_kpis.delete","web","2026-11-11 16:56:57","2026-11-11 16:56:57","حذف أوزان الأداء للموظفين","tasks","المهام والأداء والتعاقدات","أوزان الأداء للموظفين","delete","2140");
INSERT INTO permissions VALUES ("215","contracts.view","web","2026-11-11 16:56:57","2026-11-11 16:56:57","عرض عقود التدريب والتعيين والتعهيد","tasks","المهام والأداء والتعاقدات","عقود التدريب والتعيين والتعهيد","view","2150");
INSERT INTO permissions VALUES ("216","contracts.add","web","2026-11-11 16:56:57","2026-11-11 16:56:57","إضافة عقود التدريب والتعيين والتعهيد","tasks","المهام والأداء والتعاقدات","عقود التدريب والتعيين والتعهيد","add","2160");
INSERT INTO permissions VALUES ("217","contracts.edit","web","2026-11-11 16:56:57","2026-11-11 16:56:57","تعديل عقود التدريب والتعيين والتعهيد","tasks","المهام والأداء والتعاقدات","عقود التدريب والتعيين والتعهيد","edit","2170");
INSERT INTO permissions VALUES ("218","contracts.delete","web","2026-11-11 16:56:57","2026-11-11 16:56:57","حذف عقود التدريب والتعيين والتعهيد","tasks","المهام والأداء والتعاقدات","عقود التدريب والتعيين والتعهيد","delete","2180");
INSERT INTO permissions VALUES ("219","cvs.view","web","2026-11-11 16:56:57","2026-11-11 16:56:57","عرض السير الذاتية","tasks","المهام والأداء والتعاقدات","السير الذاتية","view","2190");
INSERT INTO permissions VALUES ("220","cvs.add","web","2026-11-11 16:56:57","2026-11-11 16:56:57","إضافة السير الذاتية","tasks","المهام والأداء والتعاقدات","السير الذاتية","add","2200");
INSERT INTO permissions VALUES ("221","cvs.edit","web","2026-11-11 16:56:57","2026-11-11 16:56:57","تعديل السير الذاتية","tasks","المهام والأداء والتعاقدات","السير الذاتية","edit","2210");
INSERT INTO permissions VALUES ("222","cvs.delete","web","2026-11-11 16:56:57","2026-11-11 16:56:57","حذف السير الذاتية","tasks","المهام والأداء والتعاقدات","السير الذاتية","delete","2220");
INSERT INTO permissions VALUES ("223","requests.view","web","2026-11-11 16:56:57","2026-11-11 16:56:57","عرض الطلبات (إجازات وغيرها)","tasks","المهام والأداء والتعاقدات","الطلبات (إجازات وغيرها)","view","2230");
INSERT INTO permissions VALUES ("224","requests.add","web","2026-11-11 16:56:57","2026-11-11 16:56:57","إضافة الطلبات (إجازات وغيرها)","tasks","المهام والأداء والتعاقدات","الطلبات (إجازات وغيرها)","add","2240");
INSERT INTO permissions VALUES ("225","requests.edit","web","2026-11-11 16:56:57","2026-11-11 16:56:57","تعديل الطلبات (إجازات وغيرها)","tasks","المهام والأداء والتعاقدات","الطلبات (إجازات وغيرها)","edit","2250");
INSERT INTO permissions VALUES ("226","requests.delete","web","2026-11-11 16:56:57","2026-11-11 16:56:57","حذف الطلبات (إجازات وغيرها)","tasks","المهام والأداء والتعاقدات","الطلبات (إجازات وغيرها)","delete","2260");
INSERT INTO permissions VALUES ("227","attendance.view","web","2026-11-11 16:56:57","2026-11-11 16:56:57","عرض سجل الحضور والبصمات والأذونات","payroll","الحضور والمرتبات","سجل الحضور والبصمات والأذونات","view","2270");
INSERT INTO permissions VALUES ("228","attendance.add","web","2026-11-11 16:56:57","2026-11-11 16:56:57","إضافة سجل الحضور والبصمات والأذونات","payroll","الحضور والمرتبات","سجل الحضور والبصمات والأذونات","add","2280");
INSERT INTO permissions VALUES ("229","attendance.edit","web","2026-11-11 16:56:57","2026-11-11 16:56:57","تعديل سجل الحضور والبصمات والأذونات","payroll","الحضور والمرتبات","سجل الحضور والبصمات والأذونات","edit","2290");
INSERT INTO permissions VALUES ("230","attendance.delete","web","2026-11-11 16:56:57","2026-11-11 16:56:57","حذف سجل الحضور والبصمات والأذونات","payroll","الحضور والمرتبات","سجل الحضور والبصمات والأذونات","delete","2300");
INSERT INTO permissions VALUES ("231","allowances.view","web","2026-11-11 16:56:57","2026-11-11 16:56:57","عرض البدلات والاستقطاعات","payroll","الحضور والمرتبات","البدلات والاستقطاعات","view","2310");
INSERT INTO permissions VALUES ("232","allowances.add","web","2026-11-11 16:56:57","2026-11-11 16:56:57","إضافة البدلات والاستقطاعات","payroll","الحضور والمرتبات","البدلات والاستقطاعات","add","2320");
INSERT INTO permissions VALUES ("233","allowances.edit","web","2026-11-11 16:56:57","2026-11-11 16:56:57","تعديل البدلات والاستقطاعات","payroll","الحضور والمرتبات","البدلات والاستقطاعات","edit","2330");
INSERT INTO permissions VALUES ("234","allowances.delete","web","2026-11-11 16:56:57","2026-11-11 16:56:57","حذف البدلات والاستقطاعات","payroll","الحضور والمرتبات","البدلات والاستقطاعات","delete","2340");
INSERT INTO permissions VALUES ("235","attendance.process","web","2026-11-11 16:56:57","2026-11-11 16:56:57","معالجة الحضور","payroll","الحضور والمرتبات","معالجة الحضور",NULL,"2350");
INSERT INTO permissions VALUES ("236","production.view","web","2026-11-11 16:56:57","2026-11-11 16:56:57","عرض الإنتاجية اليومية","payroll","الحضور والمرتبات","الإنتاجية اليومية","view","2360");
INSERT INTO permissions VALUES ("237","production.add","web","2026-11-11 16:56:57","2026-11-11 16:56:57","إضافة الإنتاجية اليومية","payroll","الحضور والمرتبات","الإنتاجية اليومية","add","2370");
INSERT INTO permissions VALUES ("238","production.edit","web","2026-11-11 16:56:57","2026-11-11 16:56:57","تعديل الإنتاجية اليومية","payroll","الحضور والمرتبات","الإنتاجية اليومية","edit","2380");
INSERT INTO permissions VALUES ("239","production.delete","web","2026-11-11 16:56:57","2026-11-11 16:56:57","حذف الإنتاجية اليومية","payroll","الحضور والمرتبات","الإنتاجية اليومية","delete","2390");
INSERT INTO permissions VALUES ("240","payroll.view","web","2026-11-11 16:56:57","2026-11-11 16:56:57","عرض احتسابات الرواتب","payroll","الحضور والمرتبات","احتسابات الرواتب","view","2400");
INSERT INTO permissions VALUES ("241","payroll.add","web","2026-11-11 16:56:57","2026-11-11 16:56:57","إضافة احتسابات الرواتب","payroll","الحضور والمرتبات","احتسابات الرواتب","add","2410");
INSERT INTO permissions VALUES ("242","payroll.edit","web","2026-11-11 16:56:57","2026-11-11 16:56:57","تعديل احتسابات الرواتب","payroll","الحضور والمرتبات","احتسابات الرواتب","edit","2420");
INSERT INTO permissions VALUES ("243","payroll.delete","web","2026-11-11 16:56:57","2026-11-11 16:56:57","حذف احتسابات الرواتب","payroll","الحضور والمرتبات","احتسابات الرواتب","delete","2430");
INSERT INTO permissions VALUES ("244","crm_clients.view","web","2026-11-11 16:56:57","2026-11-23 15:08:07","عرض العملاء (متقدم)","crm","CRM","العملاء (متقدم)","view","2440");
INSERT INTO permissions VALUES ("245","crm_clients.add","web","2026-11-11 16:56:57","2026-11-23 15:08:07","إضافة العملاء (متقدم)","crm","CRM","العملاء (متقدم)","add","2450");
INSERT INTO permissions VALUES ("246","crm_clients.edit","web","2026-11-11 16:56:57","2026-11-23 15:08:07","تعديل العملاء (متقدم)","crm","CRM","العملاء (متقدم)","edit","2460");
INSERT INTO permissions VALUES ("247","crm_clients.delete","web","2026-11-11 16:56:57","2026-11-23 15:08:07","حذف العملاء (متقدم)","crm","CRM","العملاء (متقدم)","delete","2470");
INSERT INTO permissions VALUES ("248","client_profile.view","web","2026-11-11 16:56:57","2026-11-23 15:08:07","عرض بروفايل العميل ومستنداته","crm","CRM","بروفايل العميل ومستنداته","view","2480");
INSERT INTO permissions VALUES ("249","client_profile.add","web","2026-11-11 16:56:57","2026-11-23 15:08:07","إضافة بروفايل العميل ومستنداته","crm","CRM","بروفايل العميل ومستنداته","add","2490");
INSERT INTO permissions VALUES ("250","client_profile.edit","web","2026-11-11 16:56:57","2026-11-23 15:08:07","تعديل بروفايل العميل ومستنداته","crm","CRM","بروفايل العميل ومستنداته","edit","2500");
INSERT INTO permissions VALUES ("251","client_profile.delete","web","2026-11-11 16:56:57","2026-11-23 15:08:07","حذف بروفايل العميل ومستنداته","crm","CRM","بروفايل العميل ومستنداته","delete","2510");
INSERT INTO permissions VALUES ("252","chances.view","web","2026-11-11 16:56:57","2026-11-23 15:08:07","عرض الفرص","crm","CRM","الفرص","view","2520");
INSERT INTO permissions VALUES ("253","chances.add","web","2026-11-11 16:56:57","2026-11-23 15:08:07","إضافة الفرص","crm","CRM","الفرص","add","2530");
INSERT INTO permissions VALUES ("254","chances.edit","web","2026-11-11 16:56:57","2026-11-23 15:08:07","تعديل الفرص","crm","CRM","الفرص","edit","2540");
INSERT INTO permissions VALUES ("255","chances.delete","web","2026-11-11 16:56:57","2026-11-23 15:08:07","حذف الفرص","crm","CRM","الفرص","delete","2550");
INSERT INTO permissions VALUES ("256","calls.view","web","2026-11-11 16:56:57","2026-11-23 15:08:07","عرض المكالمات","crm","CRM","المكالمات","view","2560");
INSERT INTO permissions VALUES ("257","calls.add","web","2026-11-11 16:56:57","2026-11-23 15:08:07","إضافة المكالمات","crm","CRM","المكالمات","add","2570");
INSERT INTO permissions VALUES ("258","calls.edit","web","2026-11-11 16:56:57","2026-11-23 15:08:07","تعديل المكالمات","crm","CRM","المكالمات","edit","2580");
INSERT INTO permissions VALUES ("259","calls.delete","web","2026-11-11 16:56:57","2026-11-23 15:08:07","حذف المكالمات","crm","CRM","المكالمات","delete","2590");
INSERT INTO permissions VALUES ("260","reports.sales","web","2026-11-11 16:56:57","2026-11-11 16:56:57","تقارير المبيعات","reports","التقارير","تقارير المبيعات",NULL,"2600");
INSERT INTO permissions VALUES ("261","reports.shift","web","2026-11-11 16:56:57","2026-11-11 16:56:57","تقارير الشيفت","reports","التقارير","تقارير الشيفت",NULL,"2610");
INSERT INTO permissions VALUES ("262","reports.purchases","web","2026-11-11 16:56:57","2026-11-11 16:56:57","تقارير المشتريات","reports","التقارير","تقارير المشتريات",NULL,"2620");
INSERT INTO permissions VALUES ("263","reports.accounting","web","2026-11-11 16:56:57","2026-11-11 16:56:57","كشوف الحسابات والقوائم المالية","reports","التقارير","كشوف الحسابات والقوائم المالية",NULL,"2630");
INSERT INTO permissions VALUES ("264","reports.rents","web","2026-11-11 16:56:57","2026-11-11 16:56:57","تقارير التأجير","reports","التقارير","تقارير التأجير",NULL,"2640");
INSERT INTO permissions VALUES ("265","reports.clinics","web","2026-11-11 16:56:57","2026-11-11 16:56:57","تقارير العيادات","reports","التقارير","تقارير العيادات",NULL,"2650");
INSERT INTO permissions VALUES ("266","reports.visits","web","2026-11-11 16:56:57","2026-11-11 16:56:57","إحصائيات الزيارات","reports","التقارير","إحصائيات الزيارات",NULL,"2660");
INSERT INTO permissions VALUES ("267","reports.payroll","web","2026-11-11 16:56:57","2026-11-11 16:56:57","تقارير المرتبات والحضور","reports","التقارير","تقارير المرتبات والحضور",NULL,"2670");
INSERT INTO permissions VALUES ("268","reports.hr","web","2026-11-11 16:56:57","2026-11-11 16:56:57","تقرير الموظفين","reports","التقارير","تقرير الموظفين",NULL,"2680");
INSERT INTO permissions VALUES ("269","users.view","web","2026-11-11 16:56:57","2026-11-11 16:56:57","عرض المستخدمين","users","المستخدمين والأدوار","المستخدمين","view","2690");
INSERT INTO permissions VALUES ("270","users.add","web","2026-11-11 16:56:57","2026-11-11 16:56:57","إضافة المستخدمين","users","المستخدمين والأدوار","المستخدمين","add","2700");
INSERT INTO permissions VALUES ("271","users.edit","web","2026-11-11 16:56:57","2026-11-11 16:56:57","تعديل المستخدمين","users","المستخدمين والأدوار","المستخدمين","edit","2710");
INSERT INTO permissions VALUES ("272","users.delete","web","2026-11-11 16:56:57","2026-11-11 16:56:57","حذف المستخدمين","users","المستخدمين والأدوار","المستخدمين","delete","2720");
INSERT INTO permissions VALUES ("273","users.change_password","web","2026-11-11 16:56:57","2026-11-11 16:56:57","تغيير كلمات مرور المستخدمين","users","المستخدمين والأدوار","تغيير كلمات مرور المستخدمين",NULL,"2730");
INSERT INTO permissions VALUES ("274","users.waiter_codes","web","2026-11-11 16:56:57","2026-11-11 16:56:57","باركود الويترز","users","المستخدمين والأدوار","باركود الويترز",NULL,"2740");
INSERT INTO permissions VALUES ("275","roles.view","web","2026-11-11 16:56:57","2026-11-11 16:56:57","عرض الأدوار والصلاحيات","users","المستخدمين والأدوار","الأدوار والصلاحيات","view","2750");
INSERT INTO permissions VALUES ("276","roles.add","web","2026-11-11 16:56:57","2026-11-11 16:56:57","إضافة الأدوار والصلاحيات","users","المستخدمين والأدوار","الأدوار والصلاحيات","add","2760");
INSERT INTO permissions VALUES ("277","roles.edit","web","2026-11-11 16:56:57","2026-11-11 16:56:57","تعديل الأدوار والصلاحيات","users","المستخدمين والأدوار","الأدوار والصلاحيات","edit","2770");
INSERT INTO permissions VALUES ("278","roles.delete","web","2026-11-11 16:56:57","2026-11-11 16:56:57","حذف الأدوار والصلاحيات","users","المستخدمين والأدوار","الأدوار والصلاحيات","delete","2780");
INSERT INTO permissions VALUES ("279","settings.manage","web","2026-11-11 16:56:57","2026-11-11 16:56:57","الإعدادات العامة","system","الإعدادات والنظام","الإعدادات العامة",NULL,"2790");
INSERT INTO permissions VALUES ("280","system.manage","web","2026-11-11 16:56:57","2026-11-11 16:56:57","النظام وقاعدة البيانات والترخيص","system","الإعدادات والنظام","النظام وقاعدة البيانات والترخيص",NULL,"2800");
INSERT INTO permissions VALUES ("281","news.view","web","2026-11-11 16:56:57","2026-11-11 16:56:57","عرض الأخبار والإعلانات","system","الإعدادات والنظام","الأخبار والإعلانات","view","2810");
INSERT INTO permissions VALUES ("282","news.add","web","2026-11-11 16:56:57","2026-11-11 16:56:57","إضافة الأخبار والإعلانات","system","الإعدادات والنظام","الأخبار والإعلانات","add","2820");
INSERT INTO permissions VALUES ("283","news.edit","web","2026-11-11 16:56:57","2026-11-11 16:56:57","تعديل الأخبار والإعلانات","system","الإعدادات والنظام","الأخبار والإعلانات","edit","2830");
INSERT INTO permissions VALUES ("284","news.delete","web","2026-11-11 16:56:57","2026-11-11 16:56:57","حذف الأخبار والإعلانات","system","الإعدادات والنظام","الأخبار والإعلانات","delete","2840");
INSERT INTO permissions VALUES ("285","defaults.change","web","2026-11-11 16:56:59","2026-11-11 16:56:59","تغيير الافتراضيات في الفواتير والكاشير والسندات","users","المستخدمين والأدوار","تغيير الافتراضيات في الفواتير والكاشير والسندات",NULL,"2750");
INSERT INTO permissions VALUES ("286","store_transfers.view","web","2026-11-11 16:56:59","2026-11-11 16:56:59","عرض التحويلات المخزنية","inventory","المخزون","تحويل مخزني","view","752");
INSERT INTO permissions VALUES ("287","store_transfers.add","web","2026-11-11 16:56:59","2026-11-11 16:56:59","إضافة التحويلات المخزنية","inventory","المخزون","تحويل مخزني","add","753");
INSERT INTO permissions VALUES ("288","store_transfers.edit","web","2026-11-11 16:56:59","2026-11-11 16:56:59","تعديل التحويلات المخزنية","inventory","المخزون","تحويل مخزني","edit","754");
INSERT INTO permissions VALUES ("289","store_transfers.delete","web","2026-11-11 16:56:59","2026-11-11 16:56:59","حذف التحويلات المخزنية","inventory","المخزون","تحويل مخزني","delete","755");
INSERT INTO permissions VALUES ("290","barcode_docs.view","web","2026-11-11 16:56:59","2026-11-11 16:56:59","عرض مستندات طباعة الباركود","purchases","المشتريات","مستند طباعة باركود","view","1280");
INSERT INTO permissions VALUES ("291","barcode_docs.add","web","2026-11-11 16:56:59","2026-11-11 16:56:59","إضافة مستندات طباعة الباركود","purchases","المشتريات","مستند طباعة باركود","add","1290");
INSERT INTO permissions VALUES ("292","barcode_docs.edit","web","2026-11-11 16:56:59","2026-11-11 16:56:59","تعديل مستندات طباعة الباركود","purchases","المشتريات","مستند طباعة باركود","edit","1300");
INSERT INTO permissions VALUES ("293","barcode_docs.delete","web","2026-11-11 16:56:59","2026-11-11 16:56:59","حذف مستندات طباعة الباركود","purchases","المشتريات","مستند طباعة باركود","delete","1310");
INSERT INTO permissions VALUES ("294","items.auto_invoice","web","2026-11-11 16:56:59","2026-11-11 16:56:59","صنف جديد تلقائي في الفاتورة","inventory","المخزون","الأصناف",NULL,"625");
INSERT INTO permissions VALUES ("295","manufacturing.view","web","2026-11-18 16:01:16","2026-11-18 16:01:16","عرض أوامر التصنيع","inventory","المخزون","التصنيع","view","760");
INSERT INTO permissions VALUES ("296","manufacturing.add","web","2026-11-18 16:01:16","2026-11-18 16:01:16","إضافة أوامر التصنيع","inventory","المخزون","التصنيع","add","761");
INSERT INTO permissions VALUES ("297","manufacturing.edit","web","2026-11-18 16:01:16","2026-11-18 16:01:16","تعديل أوامر التصنيع","inventory","المخزون","التصنيع","edit","762");
INSERT INTO permissions VALUES ("298","manufacturing.delete","web","2026-11-18 16:01:16","2026-11-18 16:01:16","حذف أوامر التصنيع","inventory","المخزون","التصنيع","delete","763");



DROP TABLE IF EXISTS permits;

CREATE TABLE `permits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `empid` int(1) NOT NULL,
  `info` varchar(100) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `curdate` date NOT NULL,
  `startdate` date NOT NULL,
  `enddate` date NOT NULL,
  `val` double DEFAULT NULL,
  `statue` int(1) NOT NULL,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE IF EXISTS prescdetails;

CREATE TABLE `prescdetails` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `prescid` int(11) DEFAULT NULL,
  `drug` int(11) DEFAULT NULL,
  `dose` varchar(200) NOT NULL,
  `info` varchar(200) NOT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE IF EXISTS prescs;

CREATE TABLE `prescs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client` int(11) DEFAULT NULL,
  `visit` int(11) DEFAULT NULL,
  `analayses` varchar(250) DEFAULT NULL,
  `info` varchar(200) NOT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE IF EXISTS price_lists;

CREATE TABLE `price_lists` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pname` varchar(100) NOT NULL,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO price_lists VALUES ("1","قطاعي","0","0","0");
INSERT INTO price_lists VALUES ("2","جملة","0","0","0");
INSERT INTO price_lists VALUES ("3","السوق","0","0","0");



DROP TABLE IF EXISTS print;

CREATE TABLE `print` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `pname` varchar(50) NOT NULL,
  `type` varchar(11) NOT NULL,
  `number` varchar(11) NOT NULL,
  `info` varchar(100) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE IF EXISTS pro_tybes;

CREATE TABLE `pro_tybes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pname` varchar(200) DEFAULT NULL,
  `ptext` varchar(200) DEFAULT NULL,
  `ptybe` int(11) DEFAULT NULL,
  `info` varchar(200) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO pro_tybes VALUES ("1","سند قبض",NULL,"1",NULL,"2024-03-14 04:01:35","2024-03-14 04:01:35","0","0","0");
INSERT INTO pro_tybes VALUES ("2","سند دفع",NULL,"2",NULL,"2024-03-14 04:01:35","2024-03-14 04:02:22","0","0","0");
INSERT INTO pro_tybes VALUES ("3","فاتورة مبيعات",NULL,"3",NULL,"2024-03-14 04:01:58","2024-03-14 04:02:26","0","0","0");
INSERT INTO pro_tybes VALUES ("4","فاتورة مشتريات",NULL,"4",NULL,"2024-03-14 04:01:58","2024-03-14 04:02:28","0","0","0");
INSERT INTO pro_tybes VALUES ("5","استحقاق قسط",NULL,"5",NULL,"2024-03-17 05:53:16","2024-03-17 05:53:27","0","0","0");
INSERT INTO pro_tybes VALUES ("6","خصم مكتسب",NULL,"6",NULL,"2024-03-17 05:53:16","2024-03-17 05:53:27","0","0","0");
INSERT INTO pro_tybes VALUES ("7","خصم مسموح به",NULL,"7",NULL,"2024-03-17 05:53:16","2024-03-17 05:53:27","0","0","0");
INSERT INTO pro_tybes VALUES ("8","قيد يومية",NULL,"8",NULL,"2024-05-14 14:06:41","2024-05-14 14:06:54","0","0","0");
INSERT INTO pro_tybes VALUES ("9","كاشير",NULL,"9",NULL,"2024-05-14 14:06:41","2026-11-11 16:56:59","0","0","0");
INSERT INTO pro_tybes VALUES ("10","فاتورة مردود مشتريات",NULL,"10",NULL,"2024-05-14 14:06:41","2026-11-11 16:56:59","0","0","0");
INSERT INTO pro_tybes VALUES ("11","فاتورة مردود مبيعات",NULL,"11",NULL,"2024-05-14 14:06:41","2026-11-11 16:56:59","0","0","0");
INSERT INTO pro_tybes VALUES ("12","أمر شراء",NULL,"12",NULL,"2024-05-14 14:06:41","2024-11-21 17:25:12","0","0","0");
INSERT INTO pro_tybes VALUES ("13","أمر بيع",NULL,"13",NULL,"2024-05-14 14:06:41","2024-11-21 17:25:16","0","0","0");
INSERT INTO pro_tybes VALUES ("14","عرض سعر",NULL,"14",NULL,"2024-05-14 14:06:41","2026-11-11 16:56:56","0","0","0");
INSERT INTO pro_tybes VALUES ("15","رصيد افتتاحي حسابات",NULL,"15",NULL,"2024-05-14 14:06:41","2024-11-23 13:40:52","0","0","0");
INSERT INTO pro_tybes VALUES ("18","تحويل مخزني","","18",NULL,"2026-11-11 16:56:59","2026-11-11 16:56:59","0","0","0");
INSERT INTO pro_tybes VALUES ("19","مستند طباعة باركود","","19",NULL,"2026-11-11 16:56:59","2026-11-11 16:56:59","0","0","0");
INSERT INTO pro_tybes VALUES ("20","قيد تكلفة مبيعات","","20",NULL,"2026-11-11 16:56:59","2026-11-11 16:56:59","0","0","0");
INSERT INTO pro_tybes VALUES ("21","سند قبض متعدد","","21",NULL,"2026-11-19 14:23:57","2026-11-23 13:24:51","0","0","0");
INSERT INTO pro_tybes VALUES ("22","سند دفع متعدد","","22",NULL,"2026-11-19 14:23:57","2026-11-23 13:24:51","0","0","0");
INSERT INTO pro_tybes VALUES ("23","تسوية جرد",NULL,"23",NULL,"2026-11-21 15:46:52","2026-11-21 15:46:52","0","0","0");



DROP TABLE IF EXISTS process;

CREATE TABLE `process` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(255) NOT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=133 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO process VALUES ("1","add unit: r","2026-11-11 16:59:54");
INSERT INTO process VALUES ("2","add unit: ق","2026-11-11 16:59:55");
INSERT INTO process VALUES ("3","add unit: قطعه","2026-11-11 16:59:58");
INSERT INTO process VALUES ("4","add item","2026-11-11 17:02:57");
INSERT INTO process VALUES ("5","add item","2026-11-11 17:03:09");
INSERT INTO process VALUES ("6","add cash","2026-11-14 21:22:30");
INSERT INTO process VALUES ("7","add cash","2026-11-14 21:22:53");
INSERT INTO process VALUES ("8","add cash","2026-11-14 21:23:44");
INSERT INTO process VALUES ("9","add unit: ك","2026-11-14 21:34:14");
INSERT INTO process VALUES ("10","add group","2026-11-14 21:34:37");
INSERT INTO process VALUES ("11","add group","2026-11-14 21:34:50");
INSERT INTO process VALUES ("12","add group2","2026-11-14 21:39:37");
INSERT INTO process VALUES ("13","add group2","2026-11-14 21:39:44");
INSERT INTO process VALUES ("14","add item","2026-11-14 21:40:03");
INSERT INTO process VALUES ("15","add item","2026-11-14 21:40:21");
INSERT INTO process VALUES ("16","add item","2026-11-14 21:41:16");
INSERT INTO process VALUES ("17","add item","2026-11-14 21:41:33");
INSERT INTO process VALUES ("18","add cash","2026-11-14 22:48:12");
INSERT INTO process VALUES ("19","add cash","2026-11-14 22:54:20");
INSERT INTO process VALUES ("20","add cash","2026-11-14 23:33:14");
INSERT INTO process VALUES ("21","add cash","2026-11-16 11:54:26");
INSERT INTO process VALUES ("22","add cash","2026-11-16 12:20:30");
INSERT INTO process VALUES ("23","add cash","2026-11-16 12:20:58");
INSERT INTO process VALUES ("24","add cash","2026-11-16 12:25:30");
INSERT INTO process VALUES ("25","add cash","2026-11-16 12:27:19");
INSERT INTO process VALUES ("26","add cash","2026-11-16 12:29:22");
INSERT INTO process VALUES ("27","add cash","2026-11-16 12:29:35");
INSERT INTO process VALUES ("28","add cash","2026-11-16 12:30:46");
INSERT INTO process VALUES ("29","add cash","2026-11-16 12:31:21");
INSERT INTO process VALUES ("30","add cash","2026-11-16 12:35:34");
INSERT INTO process VALUES ("31","add cash","2026-11-16 12:53:40");
INSERT INTO process VALUES ("32","add cash","2026-11-16 12:54:41");
INSERT INTO process VALUES ("33","add cash","2026-11-16 12:59:37");
INSERT INTO process VALUES ("34","add cash","2026-11-16 12:59:58");
INSERT INTO process VALUES ("35","add cash","2026-11-16 13:00:28");
INSERT INTO process VALUES ("36","add cash","2026-11-16 13:37:59");
INSERT INTO process VALUES ("37","add cash","2026-11-16 13:40:22");
INSERT INTO process VALUES ("38","add cash","2026-11-16 13:50:55");
INSERT INTO process VALUES ("39","add cash","2026-11-16 13:51:27");
INSERT INTO process VALUES ("40","add cash","2026-11-16 13:52:05");
INSERT INTO process VALUES ("41","add cash","2026-11-16 13:56:26");
INSERT INTO process VALUES ("42","add cash","2026-11-16 13:58:12");
INSERT INTO process VALUES ("43","add cash","2026-11-16 14:00:21");
INSERT INTO process VALUES ("44","add cash","2026-11-16 14:00:46");
INSERT INTO process VALUES ("45","add cash","2026-11-16 14:02:35");
INSERT INTO process VALUES ("46","add cash","2026-11-16 14:03:15");
INSERT INTO process VALUES ("47","add cash","2026-11-16 14:10:43");
INSERT INTO process VALUES ("48","add cash","2026-11-16 14:14:20");
INSERT INTO process VALUES ("49","add cash","2026-11-16 14:19:32");
INSERT INTO process VALUES ("50","add cash","2026-11-16 14:19:45");
INSERT INTO process VALUES ("51","add cash","2026-11-16 14:20:41");
INSERT INTO process VALUES ("52","add cash","2026-11-16 14:25:02");
INSERT INTO process VALUES ("53","add cash","2026-11-16 14:25:18");
INSERT INTO process VALUES ("54","add cash","2026-11-16 14:32:27");
INSERT INTO process VALUES ("55","add cash","2026-11-16 14:32:50");
INSERT INTO process VALUES ("56","add cash","2026-11-16 14:35:41");
INSERT INTO process VALUES ("57","add cash","2026-11-16 14:37:04");
INSERT INTO process VALUES ("58","add cash","2026-11-16 14:41:12");
INSERT INTO process VALUES ("59","add cash","2026-11-16 14:41:34");
INSERT INTO process VALUES ("60","add cash","2026-11-16 14:41:38");
INSERT INTO process VALUES ("61","add cash","2026-11-16 14:42:25");
INSERT INTO process VALUES ("62","add cash","2026-11-16 14:42:34");
INSERT INTO process VALUES ("63","add cash","2026-11-16 14:43:01");
INSERT INTO process VALUES ("64","add cash","2026-11-16 14:43:46");
INSERT INTO process VALUES ("65","add cash","2026-11-16 14:45:32");
INSERT INTO process VALUES ("66","add cash","2026-11-16 14:46:18");
INSERT INTO process VALUES ("67","add cash","2026-11-16 14:54:50");
INSERT INTO process VALUES ("68","add cash","2026-11-16 14:55:15");
INSERT INTO process VALUES ("69","add cash","2026-11-16 14:55:58");
INSERT INTO process VALUES ("70","add cash","2026-11-16 14:57:30");
INSERT INTO process VALUES ("71","add cash","2026-11-16 14:58:10");
INSERT INTO process VALUES ("72","add cash","2026-11-16 14:58:26");
INSERT INTO process VALUES ("73","add cash","2026-11-16 14:59:05");
INSERT INTO process VALUES ("74","add cash","2026-11-16 15:07:25");
INSERT INTO process VALUES ("75","add cash","2026-11-16 15:07:47");
INSERT INTO process VALUES ("76","add cash","2026-11-16 15:08:15");
INSERT INTO process VALUES ("77","add cash","2026-11-16 15:11:38");
INSERT INTO process VALUES ("78","add cash","2026-11-16 15:11:45");
INSERT INTO process VALUES ("79","add cash","2026-11-16 15:11:50");
INSERT INTO process VALUES ("80","add cash","2026-11-16 15:21:52");
INSERT INTO process VALUES ("81","add cash","2026-11-16 15:32:46");
INSERT INTO process VALUES ("82","add cash","2026-11-16 15:33:57");
INSERT INTO process VALUES ("83","add cash","2026-11-16 15:35:11");
INSERT INTO process VALUES ("84","add cash","2026-11-16 15:35:25");
INSERT INTO process VALUES ("85","add cash","2026-11-16 15:36:04");
INSERT INTO process VALUES ("86","add cash","2026-11-16 15:45:41");
INSERT INTO process VALUES ("87","add cash","2026-11-16 21:04:41");
INSERT INTO process VALUES ("88","add cash","2026-11-16 21:05:44");
INSERT INTO process VALUES ("89","add cash","2026-11-16 21:07:00");
INSERT INTO process VALUES ("90","add cash","2026-11-16 21:15:13");
INSERT INTO process VALUES ("91","add cash","2026-11-16 21:15:17");
INSERT INTO process VALUES ("92","add cash","2026-11-18 15:39:22");
INSERT INTO process VALUES ("93","logout >> admin","2026-11-18 15:59:28");
INSERT INTO process VALUES ("94","add cash","2026-11-18 16:04:31");
INSERT INTO process VALUES ("95","add cash","2026-11-18 16:05:32");
INSERT INTO process VALUES ("96","add cash","2026-11-18 16:06:12");
INSERT INTO process VALUES ("97","save role #1 (admin): 298 permissions by user 1","2026-11-18 17:13:33");
INSERT INTO process VALUES ("98","logout >> admin","2026-11-18 17:13:49");
INSERT INTO process VALUES ("99","إضافة قيد","2026-11-19 14:14:42");
INSERT INTO process VALUES ("100","add cash","2026-11-21 12:50:35");
INSERT INTO process VALUES ("101","add cash","2026-11-21 12:50:38");
INSERT INTO process VALUES ("102","add cash","2026-11-21 12:50:43");
INSERT INTO process VALUES ("103","add cash","2026-11-21 12:50:48");
INSERT INTO process VALUES ("104","logout >> admin","2026-11-22 15:33:02");
INSERT INTO process VALUES ("105","add cash","2026-11-22 19:12:43");
INSERT INTO process VALUES ("106","add cash","2026-11-22 19:13:04");
INSERT INTO process VALUES ("107","add cash","2026-11-22 19:24:59");
INSERT INTO process VALUES ("108","add cash","2026-11-22 19:31:48");
INSERT INTO process VALUES ("109","add cash","2026-11-22 19:34:35");
INSERT INTO process VALUES ("110","add cash","2026-11-22 19:35:12");
INSERT INTO process VALUES ("111","add cash","2026-11-22 19:44:00");
INSERT INTO process VALUES ("112","add cash","2026-11-22 19:44:30");
INSERT INTO process VALUES ("113","add cash","2026-11-22 20:26:45");
INSERT INTO process VALUES ("114","add cash","2026-11-22 22:26:42");
INSERT INTO process VALUES ("115","add cash","2026-11-22 22:26:59");
INSERT INTO process VALUES ("116","add cash","2026-11-22 22:30:44");
INSERT INTO process VALUES ("117","add cash","2026-11-22 22:31:09");
INSERT INTO process VALUES ("118","add cash","2026-11-22 22:31:21");
INSERT INTO process VALUES ("119","add client","2026-11-23 14:48:50");
INSERT INTO process VALUES ("120","save role #1 (admin): 274 permissions by user 1","2026-11-23 15:00:16");
INSERT INTO process VALUES ("121","logout >> admin","2026-11-23 15:00:21");
INSERT INTO process VALUES ("122","logout >> admin","2026-11-23 15:08:22");
INSERT INTO process VALUES ("123","logout >> admin","2026-11-23 15:35:19");
INSERT INTO process VALUES ("124","save role #1 (admin): 250 permissions by user 1","2026-11-23 15:36:12");
INSERT INTO process VALUES ("125","logout >> admin","2026-11-23 15:36:15");
INSERT INTO process VALUES ("126","save role #1 (admin): 168 permissions by user 1","2026-11-23 15:37:57");
INSERT INTO process VALUES ("127","logout >> admin","2026-11-23 15:38:02");
INSERT INTO process VALUES ("128","add cash","2026-11-23 15:43:10");
INSERT INTO process VALUES ("129","add cash","2026-11-23 15:43:49");
INSERT INTO process VALUES ("130","add cash","2026-11-23 15:44:14");
INSERT INTO process VALUES ("131","add cash","2026-11-23 15:45:26");
INSERT INTO process VALUES ("132","add cash","2026-11-23 15:59:43");



DROP TABLE IF EXISTS prods;

CREATE TABLE `prods` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `pname` varchar(50) NOT NULL,
  `info` varchar(100) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE IF EXISTS productions;

CREATE TABLE `productions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `snd_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `emp_id` int(11) DEFAULT NULL,
  `emp_name` varchar(100) NOT NULL,
  `qty` double NOT NULL,
  `price` double NOT NULL,
  `value` double NOT NULL,
  `info` varchar(150) DEFAULT NULL,
  `info2` varchar(150) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user` int(11) NOT NULL DEFAULT 1,
  `isdeleted` int(11) NOT NULL DEFAULT 0,
  `tenant` int(11) NOT NULL DEFAULT 0,
  `branch` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `emp_date` (`emp_id`,`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE IF EXISTS pst_activities;

CREATE TABLE `pst_activities` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `aname` varchar(111) NOT NULL,
  `info` varchar(111) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user` int(1) DEFAULT 0,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE IF EXISTS pst_criminals;

CREATE TABLE `pst_criminals` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `cname` varchar(150) NOT NULL,
  `otherdetails` varchar(200) DEFAULT NULL,
  `karta` int(1) DEFAULT NULL,
  `dngrs` int(1) DEFAULT NULL,
  `fesh` int(1) DEFAULT NULL,
  `prtnrs` varchar(150) DEFAULT NULL,
  `crmaddress` varchar(150) DEFAULT NULL,
  `idcardnum` varchar(150) DEFAULT NULL,
  `scar` varchar(150) DEFAULT NULL,
  `mname` varchar(150) DEFAULT NULL,
  `nickname` varchar(150) DEFAULT NULL,
  `tybe` varchar(150) DEFAULT NULL,
  `danger_factor` int(1) NOT NULL DEFAULT 1,
  `info` varchar(150) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user` int(1) NOT NULL,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE IF EXISTS pst_crmstyles;

CREATE TABLE `pst_crmstyles` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `cname` varchar(150) NOT NULL,
  `info` varchar(150) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user` int(1) NOT NULL,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE IF EXISTS pst_gangs;

CREATE TABLE `pst_gangs` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `gname` varchar(150) NOT NULL,
  `info` varchar(150) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user` int(1) NOT NULL,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE IF EXISTS pst_issues;

CREATE TABLE `pst_issues` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `iname` varchar(150) NOT NULL,
  `issue_tybe` int(1) NOT NULL DEFAULT 1,
  `info` varchar(150) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user` int(1) NOT NULL,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE IF EXISTS pulse_logs;

CREATE TABLE `pulse_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `category` enum('positive','negative') NOT NULL,
  `rating` int(11) DEFAULT 5,
  `notes` text DEFAULT NULL,
  `recorded_by` int(11) NOT NULL,
  `recorded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_employee` (`employee_id`),
  KEY `idx_type` (`type_id`),
  KEY `idx_recorded_at` (`recorded_at`),
  KEY `idx_category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE IF EXISTS pulse_types;

CREATE TABLE `pulse_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `category` enum('positive','negative') NOT NULL DEFAULT 'positive',
  `icon` varchar(50) DEFAULT 'fas fa-star',
  `points` int(11) DEFAULT 1,
  `isdeleted` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO pulse_types VALUES ("1","الالتزام بالمواعيد","positive","fas fa-clock","3","0","2026-11-11 16:56:59");
INSERT INTO pulse_types VALUES ("2","جودة العمل","positive","fas fa-award","5","0","2026-11-11 16:56:59");
INSERT INTO pulse_types VALUES ("3","روح الفريق","positive","fas fa-users","4","0","2026-11-11 16:56:59");
INSERT INTO pulse_types VALUES ("4","المبادرة","positive","fas fa-lightbulb","5","0","2026-11-11 16:56:59");
INSERT INTO pulse_types VALUES ("5","خدمة العملاء","positive","fas fa-handshake","4","0","2026-11-11 16:56:59");
INSERT INTO pulse_types VALUES ("6","النظافة والترتيب","positive","fas fa-broom","2","0","2026-11-11 16:56:59");
INSERT INTO pulse_types VALUES ("7","التأخر","negative","fas fa-clock","-3","0","2026-11-11 16:56:59");
INSERT INTO pulse_types VALUES ("8","الإهمال","negative","fas fa-exclamation-triangle","-5","0","2026-11-11 16:56:59");
INSERT INTO pulse_types VALUES ("9","عدم التعاون","negative","fas fa-user-slash","-4","0","2026-11-11 16:56:59");
INSERT INTO pulse_types VALUES ("10","سوء التعامل","negative","fas fa-frown","-5","0","2026-11-11 16:56:59");



DROP TABLE IF EXISTS rays;

CREATE TABLE `rays` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client` int(11) NOT NULL,
  `lap` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `comment` varchar(250) DEFAULT NULL,
  `img` varchar(250) DEFAULT NULL,
  `crtime` timestamp NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `info` varchar(250) DEFAULT NULL,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE IF EXISTS reservations;

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client` int(11) NOT NULL,
  `diseses` varchar(250) DEFAULT '0',
  `phone` varchar(15) DEFAULT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `duration` double DEFAULT NULL,
  `visittybe` int(11) NOT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `paid` int(11) DEFAULT 0,
  `deserved` int(11) DEFAULT 0,
  `rest` int(11) DEFAULT 0,
  `done` int(11) DEFAULT 0,
  `info` varchar(250) DEFAULT NULL,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO reservations VALUES ("2","9","برد
","","2026-02-03","10:00:00","14:13:00","14:13:00","0","3","2026-02-03 14:07:33","2026-02-03 14:13:50","500","0","0","0","","0","0","0");



DROP TABLE IF EXISTS role_has_permissions;

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO role_has_permissions VALUES ("1","1");
INSERT INTO role_has_permissions VALUES ("1","28");
INSERT INTO role_has_permissions VALUES ("2","1");
INSERT INTO role_has_permissions VALUES ("2","28");
INSERT INTO role_has_permissions VALUES ("3","1");
INSERT INTO role_has_permissions VALUES ("3","28");
INSERT INTO role_has_permissions VALUES ("4","1");
INSERT INTO role_has_permissions VALUES ("4","28");
INSERT INTO role_has_permissions VALUES ("5","1");
INSERT INTO role_has_permissions VALUES ("6","1");
INSERT INTO role_has_permissions VALUES ("7","1");
INSERT INTO role_has_permissions VALUES ("8","1");
INSERT INTO role_has_permissions VALUES ("9","1");
INSERT INTO role_has_permissions VALUES ("10","1");
INSERT INTO role_has_permissions VALUES ("11","1");
INSERT INTO role_has_permissions VALUES ("12","1");
INSERT INTO role_has_permissions VALUES ("13","1");
INSERT INTO role_has_permissions VALUES ("14","1");
INSERT INTO role_has_permissions VALUES ("15","1");
INSERT INTO role_has_permissions VALUES ("16","1");
INSERT INTO role_has_permissions VALUES ("17","1");
INSERT INTO role_has_permissions VALUES ("18","1");
INSERT INTO role_has_permissions VALUES ("19","1");
INSERT INTO role_has_permissions VALUES ("20","1");
INSERT INTO role_has_permissions VALUES ("21","1");
INSERT INTO role_has_permissions VALUES ("22","1");
INSERT INTO role_has_permissions VALUES ("23","1");
INSERT INTO role_has_permissions VALUES ("24","1");
INSERT INTO role_has_permissions VALUES ("25","1");
INSERT INTO role_has_permissions VALUES ("26","1");
INSERT INTO role_has_permissions VALUES ("27","1");
INSERT INTO role_has_permissions VALUES ("28","1");
INSERT INTO role_has_permissions VALUES ("29","1");
INSERT INTO role_has_permissions VALUES ("30","1");
INSERT INTO role_has_permissions VALUES ("31","1");
INSERT INTO role_has_permissions VALUES ("32","1");
INSERT INTO role_has_permissions VALUES ("33","1");
INSERT INTO role_has_permissions VALUES ("34","1");
INSERT INTO role_has_permissions VALUES ("35","1");
INSERT INTO role_has_permissions VALUES ("36","1");
INSERT INTO role_has_permissions VALUES ("37","1");
INSERT INTO role_has_permissions VALUES ("38","1");
INSERT INTO role_has_permissions VALUES ("39","1");
INSERT INTO role_has_permissions VALUES ("40","1");
INSERT INTO role_has_permissions VALUES ("41","1");
INSERT INTO role_has_permissions VALUES ("42","1");
INSERT INTO role_has_permissions VALUES ("43","1");
INSERT INTO role_has_permissions VALUES ("44","1");
INSERT INTO role_has_permissions VALUES ("45","1");
INSERT INTO role_has_permissions VALUES ("46","1");
INSERT INTO role_has_permissions VALUES ("47","1");
INSERT INTO role_has_permissions VALUES ("48","1");
INSERT INTO role_has_permissions VALUES ("49","1");
INSERT INTO role_has_permissions VALUES ("50","1");
INSERT INTO role_has_permissions VALUES ("51","1");
INSERT INTO role_has_permissions VALUES ("52","1");
INSERT INTO role_has_permissions VALUES ("53","1");
INSERT INTO role_has_permissions VALUES ("53","28");
INSERT INTO role_has_permissions VALUES ("54","1");
INSERT INTO role_has_permissions VALUES ("54","28");
INSERT INTO role_has_permissions VALUES ("55","1");
INSERT INTO role_has_permissions VALUES ("55","28");
INSERT INTO role_has_permissions VALUES ("56","1");
INSERT INTO role_has_permissions VALUES ("56","28");
INSERT INTO role_has_permissions VALUES ("57","1");
INSERT INTO role_has_permissions VALUES ("58","1");
INSERT INTO role_has_permissions VALUES ("59","1");
INSERT INTO role_has_permissions VALUES ("60","1");
INSERT INTO role_has_permissions VALUES ("61","1");
INSERT INTO role_has_permissions VALUES ("62","1");
INSERT INTO role_has_permissions VALUES ("63","1");
INSERT INTO role_has_permissions VALUES ("64","1");
INSERT INTO role_has_permissions VALUES ("65","1");
INSERT INTO role_has_permissions VALUES ("66","1");
INSERT INTO role_has_permissions VALUES ("67","1");
INSERT INTO role_has_permissions VALUES ("68","1");
INSERT INTO role_has_permissions VALUES ("69","1");
INSERT INTO role_has_permissions VALUES ("70","1");
INSERT INTO role_has_permissions VALUES ("71","1");
INSERT INTO role_has_permissions VALUES ("72","1");
INSERT INTO role_has_permissions VALUES ("73","1");
INSERT INTO role_has_permissions VALUES ("74","1");
INSERT INTO role_has_permissions VALUES ("75","1");
INSERT INTO role_has_permissions VALUES ("76","1");
INSERT INTO role_has_permissions VALUES ("76","28");
INSERT INTO role_has_permissions VALUES ("77","1");
INSERT INTO role_has_permissions VALUES ("77","28");
INSERT INTO role_has_permissions VALUES ("78","1");
INSERT INTO role_has_permissions VALUES ("78","28");
INSERT INTO role_has_permissions VALUES ("79","1");
INSERT INTO role_has_permissions VALUES ("79","28");
INSERT INTO role_has_permissions VALUES ("80","1");
INSERT INTO role_has_permissions VALUES ("80","28");
INSERT INTO role_has_permissions VALUES ("81","1");
INSERT INTO role_has_permissions VALUES ("82","1");
INSERT INTO role_has_permissions VALUES ("83","1");
INSERT INTO role_has_permissions VALUES ("84","1");
INSERT INTO role_has_permissions VALUES ("85","1");
INSERT INTO role_has_permissions VALUES ("85","28");
INSERT INTO role_has_permissions VALUES ("86","1");
INSERT INTO role_has_permissions VALUES ("86","28");
INSERT INTO role_has_permissions VALUES ("87","1");
INSERT INTO role_has_permissions VALUES ("87","28");
INSERT INTO role_has_permissions VALUES ("88","1");
INSERT INTO role_has_permissions VALUES ("88","28");
INSERT INTO role_has_permissions VALUES ("89","1");
INSERT INTO role_has_permissions VALUES ("89","28");
INSERT INTO role_has_permissions VALUES ("90","1");
INSERT INTO role_has_permissions VALUES ("90","28");
INSERT INTO role_has_permissions VALUES ("91","1");
INSERT INTO role_has_permissions VALUES ("91","28");
INSERT INTO role_has_permissions VALUES ("92","1");
INSERT INTO role_has_permissions VALUES ("92","28");
INSERT INTO role_has_permissions VALUES ("93","1");
INSERT INTO role_has_permissions VALUES ("93","28");
INSERT INTO role_has_permissions VALUES ("94","1");
INSERT INTO role_has_permissions VALUES ("94","28");
INSERT INTO role_has_permissions VALUES ("95","1");
INSERT INTO role_has_permissions VALUES ("95","28");
INSERT INTO role_has_permissions VALUES ("96","1");
INSERT INTO role_has_permissions VALUES ("97","1");
INSERT INTO role_has_permissions VALUES ("98","1");
INSERT INTO role_has_permissions VALUES ("99","1");
INSERT INTO role_has_permissions VALUES ("100","1");
INSERT INTO role_has_permissions VALUES ("100","28");
INSERT INTO role_has_permissions VALUES ("101","1");
INSERT INTO role_has_permissions VALUES ("101","28");
INSERT INTO role_has_permissions VALUES ("102","1");
INSERT INTO role_has_permissions VALUES ("103","28");
INSERT INTO role_has_permissions VALUES ("104","28");
INSERT INTO role_has_permissions VALUES ("105","28");
INSERT INTO role_has_permissions VALUES ("106","28");
INSERT INTO role_has_permissions VALUES ("107","28");
INSERT INTO role_has_permissions VALUES ("108","28");
INSERT INTO role_has_permissions VALUES ("109","28");
INSERT INTO role_has_permissions VALUES ("110","28");
INSERT INTO role_has_permissions VALUES ("111","28");
INSERT INTO role_has_permissions VALUES ("112","28");
INSERT INTO role_has_permissions VALUES ("113","28");
INSERT INTO role_has_permissions VALUES ("114","28");
INSERT INTO role_has_permissions VALUES ("115","28");
INSERT INTO role_has_permissions VALUES ("116","1");
INSERT INTO role_has_permissions VALUES ("117","1");
INSERT INTO role_has_permissions VALUES ("118","1");
INSERT INTO role_has_permissions VALUES ("119","1");
INSERT INTO role_has_permissions VALUES ("120","1");
INSERT INTO role_has_permissions VALUES ("121","1");
INSERT INTO role_has_permissions VALUES ("122","1");
INSERT INTO role_has_permissions VALUES ("123","1");
INSERT INTO role_has_permissions VALUES ("124","1");
INSERT INTO role_has_permissions VALUES ("125","1");
INSERT INTO role_has_permissions VALUES ("126","1");
INSERT INTO role_has_permissions VALUES ("127","1");
INSERT INTO role_has_permissions VALUES ("128","1");
INSERT INTO role_has_permissions VALUES ("129","1");
INSERT INTO role_has_permissions VALUES ("130","1");
INSERT INTO role_has_permissions VALUES ("131","1");
INSERT INTO role_has_permissions VALUES ("132","1");
INSERT INTO role_has_permissions VALUES ("133","1");
INSERT INTO role_has_permissions VALUES ("134","1");
INSERT INTO role_has_permissions VALUES ("135","1");
INSERT INTO role_has_permissions VALUES ("136","1");
INSERT INTO role_has_permissions VALUES ("137","1");
INSERT INTO role_has_permissions VALUES ("138","1");
INSERT INTO role_has_permissions VALUES ("139","1");
INSERT INTO role_has_permissions VALUES ("140","1");
INSERT INTO role_has_permissions VALUES ("141","1");
INSERT INTO role_has_permissions VALUES ("142","1");
INSERT INTO role_has_permissions VALUES ("223","28");
INSERT INTO role_has_permissions VALUES ("224","28");
INSERT INTO role_has_permissions VALUES ("225","28");
INSERT INTO role_has_permissions VALUES ("226","28");
INSERT INTO role_has_permissions VALUES ("260","1");
INSERT INTO role_has_permissions VALUES ("261","1");
INSERT INTO role_has_permissions VALUES ("262","1");
INSERT INTO role_has_permissions VALUES ("263","1");
INSERT INTO role_has_permissions VALUES ("264","1");
INSERT INTO role_has_permissions VALUES ("265","1");
INSERT INTO role_has_permissions VALUES ("266","1");
INSERT INTO role_has_permissions VALUES ("266","28");
INSERT INTO role_has_permissions VALUES ("267","1");
INSERT INTO role_has_permissions VALUES ("268","1");
INSERT INTO role_has_permissions VALUES ("269","1");
INSERT INTO role_has_permissions VALUES ("270","1");
INSERT INTO role_has_permissions VALUES ("271","1");
INSERT INTO role_has_permissions VALUES ("272","1");
INSERT INTO role_has_permissions VALUES ("273","1");
INSERT INTO role_has_permissions VALUES ("274","1");
INSERT INTO role_has_permissions VALUES ("275","1");
INSERT INTO role_has_permissions VALUES ("276","1");
INSERT INTO role_has_permissions VALUES ("277","1");
INSERT INTO role_has_permissions VALUES ("278","1");
INSERT INTO role_has_permissions VALUES ("279","1");
INSERT INTO role_has_permissions VALUES ("280","1");
INSERT INTO role_has_permissions VALUES ("281","1");
INSERT INTO role_has_permissions VALUES ("282","1");
INSERT INTO role_has_permissions VALUES ("283","1");
INSERT INTO role_has_permissions VALUES ("284","1");
INSERT INTO role_has_permissions VALUES ("285","1");
INSERT INTO role_has_permissions VALUES ("286","1");
INSERT INTO role_has_permissions VALUES ("287","1");
INSERT INTO role_has_permissions VALUES ("288","1");
INSERT INTO role_has_permissions VALUES ("289","1");
INSERT INTO role_has_permissions VALUES ("290","1");
INSERT INTO role_has_permissions VALUES ("291","1");
INSERT INTO role_has_permissions VALUES ("292","1");
INSERT INTO role_has_permissions VALUES ("293","1");
INSERT INTO role_has_permissions VALUES ("294","1");
INSERT INTO role_has_permissions VALUES ("295","1");
INSERT INTO role_has_permissions VALUES ("296","1");
INSERT INTO role_has_permissions VALUES ("297","1");
INSERT INTO role_has_permissions VALUES ("298","1");



DROP TABLE IF EXISTS roles;

CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL COMMENT 'وصف الدور',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'الدور المعطّل مالوش صلاحيات',
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO roles VALUES ("1","admin","web","2026-11-11 16:56:57","2026-11-23 15:37:57","wwww","1");
INSERT INTO roles VALUES ("28","بائع كاشير","web","2026-11-11 16:56:57","2026-11-11 16:56:57","مبيعات كاشير فقط","1");



DROP TABLE IF EXISTS salaries;

CREATE TABLE `salaries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `empid` int(1) NOT NULL,
  `info` varchar(100) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `starttime` time NOT NULL,
  `endtime` time NOT NULL,
  `salary` double DEFAULT 0,
  `extra` double DEFAULT 0,
  `disc` double DEFAULT 0,
  `allow` double DEFAULT 0,
  `dedu` double DEFAULT 0,
  `total` double NOT NULL DEFAULT 0,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE IF EXISTS schema_migrations;

CREATE TABLE `schema_migrations` (
  `version` varchar(64) NOT NULL,
  `applied_at` datetime NOT NULL DEFAULT current_timestamp(),
  `checksum` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO schema_migrations VALUES ("006_add_jal_columns","2026-11-11 14:56:55","81c1502330bc36b10b6c356fd3d9dd60");
INSERT INTO schema_migrations VALUES ("007_add_jal_amount","2026-11-11 14:56:55","26d87d030b30b2e413412b8af76a739a");
INSERT INTO schema_migrations VALUES ("008_comprehensive_update","2026-11-11 14:56:55","565c1c7ee1dae869597c2b7040ae739c");
INSERT INTO schema_migrations VALUES ("009_add_calc_type_to_employees","2026-11-11 14:56:55","35162e8977c1a0bad3b78ff8619c8441");
INSERT INTO schema_migrations VALUES ("010_add_payroll_calcs","2026-11-11 14:56:55","3b85703594c9475e0492ece83b04fa5b");
INSERT INTO schema_migrations VALUES ("011_add_single_fp_rule_to_shifts","2026-11-11 14:56:55","170d9e52a77c45e0850c1508d0617822");
INSERT INTO schema_migrations VALUES ("012_add_commission_to_settings","2026-11-11 14:56:55","209fabf9c3a493622b2388e2e82bad67");
INSERT INTO schema_migrations VALUES ("013_disc_pct","2026-11-11 14:56:55","f9e7b6cb399894b01919c564ba816d27");
INSERT INTO schema_migrations VALUES ("014_shifts_ignore_minutes","2026-11-11 14:56:56","a090ed9342980dd96bcd57c6e1ec550c");
INSERT INTO schema_migrations VALUES ("015_tables_merge","2026-11-11 14:56:56","d32aaea6875bbee8a3d8ba1baacad434");
INSERT INTO schema_migrations VALUES ("016_visits_columns","2026-11-11 14:56:56","47f2696dc655be6ec95f2ca3912088b8");
INSERT INTO schema_migrations VALUES ("017_period_meta","2026-11-11 14:56:56","42661abdf8ce6976dc0675f8aea6cea6");
INSERT INTO schema_migrations VALUES ("018_delivery_person_ot_head","2026-11-11 14:56:56","16d04842d9079fcfbd9ef6186642bfea");
INSERT INTO schema_migrations VALUES ("019_backup_path","2026-11-11 14:56:56","d4b2a7e6b5c79ec18ded9a985819a314");
INSERT INTO schema_migrations VALUES ("020_fat_details_doc_qty","2026-11-11 14:56:56","0ce780f11148adbd301a54a87290c734");
INSERT INTO schema_migrations VALUES ("021_fix_pro_tybe_14_price_offer","2026-11-11 14:56:56","48f72b42e6758c752d4f5320a3415b7c");
INSERT INTO schema_migrations VALUES ("022_add_shift_grace_rules","2026-11-11 14:56:56","79f0a448270e76568f521bc88b14b585");
INSERT INTO schema_migrations VALUES ("023_seed_entitles","2026-11-11 14:56:56","b46634e6aab15320a7e81c02debacdc6");
INSERT INTO schema_migrations VALUES ("024_acc_head_cash_only","2026-11-11 14:56:56","7f8da38cde045884d82854ecab1fb5bc");
INSERT INTO schema_migrations VALUES ("025_payroll_by_id_advance_holiday_pay","2026-11-11 14:56:56","b284d6994877bc15842d2a02fe557e89");
INSERT INTO schema_migrations VALUES ("026_shifts_flexible_hours","2026-11-11 14:56:56","d20b5780ee130abe9f6b156a21b3e9eb");
INSERT INTO schema_migrations VALUES ("027_pos_shift_closing","2026-11-11 14:56:57","2ac0f20658fad4883748adc49ad3fa1e");
INSERT INTO schema_migrations VALUES ("028_usr_pwrs_legacy_columns","2026-11-11 14:56:57","0e8cbb00ef8964b3afe808e1339d31c0");
INSERT INTO schema_migrations VALUES ("029_rbac_tables","2026-11-11 14:56:57","fd9e2d2d4e562d903b0093f72d686519");
INSERT INTO schema_migrations VALUES ("030_rbac_copy_from_usr_pwrs","2026-11-11 14:56:59","321ffa1d4df73ec734d25c44bcb97126");
INSERT INTO schema_migrations VALUES ("031_admin_role_all_permissions","2026-11-11 14:56:59","1006e19570a78789726634e3803a2bd3");
INSERT INTO schema_migrations VALUES ("032_user_defaults","2026-11-11 14:56:59","8e334d2e25cee55e6a9342d0c57a80ec");
INSERT INTO schema_migrations VALUES ("033_settings_allow_all_accounts","2026-11-11 14:56:59","f9ad7afee1c6f165e7703cd8b65ba8a4");
INSERT INTO schema_migrations VALUES ("034_pulse_types","2026-11-11 14:56:59","3b94a27a4f53c032ddbc96cd59b8fe02");
INSERT INTO schema_migrations VALUES ("035_fix_pro_tybes_return_names","2026-11-11 14:56:59","960af6a9d0bb8e793043db271602c313");
INSERT INTO schema_migrations VALUES ("036_fix_pro_tybes_cashier_name","2026-11-11 14:56:59","e8f6b117db525a2f2dd0df441b36201d");
INSERT INTO schema_migrations VALUES ("037_store_transfers","2026-11-11 14:56:59","ee79ceb347c163b1b7803a9da1dcb51f");
INSERT INTO schema_migrations VALUES ("038_shift_groups_dynamic","2026-11-11 14:56:59","0894e75eb986346a15e0df8c16940764");
INSERT INTO schema_migrations VALUES ("039_barcode_print_doc","2026-11-11 14:56:59","189e45c84a7c4f18442c6360d0bbe57a");
INSERT INTO schema_migrations VALUES ("040_items_auto_invoice","2026-11-11 14:56:59","e633d9607639fde95d42722af121e198");
INSERT INTO schema_migrations VALUES ("041_cost_of_sales_journal","2026-11-11 14:56:59","d3dd87cff705e7d071846da5d7792c1e");
INSERT INTO schema_migrations VALUES ("042_session_time_id_autoincrement","2026-11-11 14:56:59","3c3180164c17fe676ef1a05fc52389fa");
INSERT INTO schema_migrations VALUES ("043_sales_revenue_account","2026-11-16 12:20:22","27250609942b8e0dafd0ab4157c9c6a3");
INSERT INTO schema_migrations VALUES ("044_fix_clothes_pos_party_accounts","2026-11-18 16:01:16","d98d018b5e23897599f7767d5a322aab");
INSERT INTO schema_migrations VALUES ("044_manufacturing","2026-11-18 16:01:16","9478363bac26e86b9b19b6cf2e8ccfee");
INSERT INTO schema_migrations VALUES ("045_manufacturing_recost","2026-11-18 16:01:16","fa1e102c6d153e989e4163542e8dd40d");
INSERT INTO schema_migrations VALUES ("046_pos_icon_size","2026-11-18 16:39:06","fc46cf5f4a38c665b718d7b6d5a5f80d");
INSERT INTO schema_migrations VALUES ("047_multi_voucher_types","2026-11-21 15:29:41","cc8688f27fb2d9c06758905bbcc92f4e");
INSERT INTO schema_migrations VALUES ("048_fix_clothes_pos_sales_return_type","2026-11-21 15:29:41","7c8b09318e83142230e925c907790688");
INSERT INTO schema_migrations VALUES ("049_backfill_clothes_pos_profit","2026-11-21 17:16:19","5ed588bcb0089afa784fdfe761ceda5c");
INSERT INTO schema_migrations VALUES ("050_pos_cat_size_tables","2026-11-22 16:11:30","f1fa43b80c0e3bd101d4cb133cee5cce");
INSERT INTO schema_migrations VALUES ("051_fat_details_item_notes","2026-11-22 19:24:50","97d5e6af00894deb0f8d0f405e11ef3d");
INSERT INTO schema_migrations VALUES ("052_rename_multi_journal","2026-11-23 13:24:51","61e38e4a5e5ff8f5deff10def4d5f7e0");
INSERT INTO schema_migrations VALUES ("053_multi_voucher_and_journal_types","2026-11-23 13:24:51","e911b0f823e54751b309454ed377fa6d");
INSERT INTO schema_migrations VALUES ("054_crm_rename_and_admin_module_perms","2026-11-23 15:08:07","427680dc2eb37621b6d096390914fe93");



DROP TABLE IF EXISTS services;

CREATE TABLE `services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sname` varchar(50) NOT NULL,
  `info` varchar(100) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE IF EXISTS session_time;

CREATE TABLE `session_time` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(1) NOT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO session_time VALUES ("1","1","2026-11-14 21:21:05","0","0","0");
INSERT INTO session_time VALUES ("2","1","2026-11-14 22:46:01","0","0","0");
INSERT INTO session_time VALUES ("3","1","2026-11-16 14:55:47","0","0","0");
INSERT INTO session_time VALUES ("4","1","2026-11-18 15:43:21","0","0","0");
INSERT INTO session_time VALUES ("5","1","2026-11-18 15:59:31","0","0","0");
INSERT INTO session_time VALUES ("6","1","2026-11-18 17:13:52","0","0","0");
INSERT INTO session_time VALUES ("7","1","2026-11-19 13:52:50","0","0","0");
INSERT INTO session_time VALUES ("8","1","2026-11-22 15:33:05","0","0","0");
INSERT INTO session_time VALUES ("9","1","2026-11-22 22:22:44","0","0","0");
INSERT INTO session_time VALUES ("10","1","2026-11-23 15:00:24","0","0","0");
INSERT INTO session_time VALUES ("11","1","2026-11-23 15:04:26","0","0","0");
INSERT INTO session_time VALUES ("12","1","2026-11-23 15:08:25","0","0","0");
INSERT INTO session_time VALUES ("13","1","2026-11-23 15:35:21","0","0","0");
INSERT INTO session_time VALUES ("14","1","2026-11-23 15:36:17","0","0","0");
INSERT INTO session_time VALUES ("15","1","2026-11-23 15:38:05","0","0","0");
INSERT INTO session_time VALUES ("16","1","2026-11-23 15:45:19","0","0","0");



DROP TABLE IF EXISTS settings;

CREATE TABLE `settings` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `company_name` varchar(200) DEFAULT NULL,
  `company_add` varchar(200) DEFAULT NULL,
  `company_email` varchar(50) DEFAULT NULL,
  `company_tel` varchar(200) DEFAULT NULL,
  `edit_pass` varchar(50) DEFAULT NULL,
  `lic` varchar(250) DEFAULT NULL,
  `updateline` text DEFAULT NULL,
  `acc_rent` int(11) DEFAULT 0,
  `startdate` date DEFAULT NULL,
  `enddate` date DEFAULT NULL,
  `lang` varchar(20) DEFAULT 'ar',
  `bodycolor` varchar(50) DEFAULT NULL,
  `showhr` int(1) NOT NULL DEFAULT 1,
  `showclinc` int(1) NOT NULL DEFAULT 1,
  `showatt` int(11) NOT NULL DEFAULT 1,
  `showpayroll` int(11) NOT NULL DEFAULT 1,
  `showrent` int(11) NOT NULL DEFAULT 1,
  `showpay` int(11) DEFAULT 1,
  `showtsk` int(11) NOT NULL DEFAULT 1,
  `def_pos_client` int(1) DEFAULT NULL,
  `def_pos_store` int(1) DEFAULT NULL,
  `def_pos_employee` int(1) DEFAULT NULL,
  `def_pos_fund` int(1) DEFAULT NULL,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  `logo` varchar(255) DEFAULT NULL,
  `show_all_tasks` int(1) DEFAULT NULL,
  `pos_type` varchar(20) DEFAULT 'barcode' COMMENT 'نوع نظام POS: barcode أو clothes',
  `pos_has_password` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1 = POS محمي بباركود، 0 = POS مفتوح',
  `emp_commission` double NOT NULL DEFAULT 0,
  `user_commission` double NOT NULL DEFAULT 0,
  `backup_path` varchar(500) NOT NULL DEFAULT '',
  `pos_blind_close` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'جرد أعمى: الكاشير ميشوفش المتوقع قبل التقفيل',
  `allow_all_accounts` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'السماح بالبيع والشراء لجميع الحسابات',
  `showpulse` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'تفعيل نظام Pulse للتقييم اللحظي',
  `receipt_show_logo` tinyint(1) NOT NULL DEFAULT 1,
  `receipt_font_size` int(11) NOT NULL DEFAULT 14,
  `receipt_paper_width` varchar(20) NOT NULL DEFAULT '78mm',
  `receipt_footer_text` varchar(255) NOT NULL DEFAULT '❤ perfect place to grow',
  `receipt_show_client` tinyint(1) NOT NULL DEFAULT 1,
  `company_logo` varchar(255) NOT NULL DEFAULT '',
  `barcode_design` mediumtext DEFAULT NULL,
  `receipt_design` mediumtext DEFAULT NULL,
  `receipt_header_text` text NOT NULL,
  `receipt_notes_text` text NOT NULL,
  `pos_icon_size_barcode` varchar(10) NOT NULL DEFAULT 'medium' COMMENT 'حجم أيقونات POS باركود المطعم',
  `pos_icon_size_clothes` varchar(10) NOT NULL DEFAULT 'medium' COMMENT 'حجم أيقونات POS الملابس',
  `pos_cat_size_tables` varchar(10) NOT NULL DEFAULT 'medium' COMMENT 'حجم أزرار التصنيفات في نقطة بيع الطاولات',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO settings VALUES ("1","FOCUS HOUSE","سمنود - برج زايد - الدور الخامس","abdelhadeeladawy@gmail.com","010053662038","125","d35c99e7485691ea14f829029dc03e69A67b8d2f92148f52cad46e331936922e8","","99","2024-01-01","2024-12-31","ar","solarized_white","1","1","1","1","1","1","1","155","27","131","21","0","0","0",NULL,NULL,"barcode","0","0","0","","0","0","1","0","16","78mm","❤ perfect place to grow","0","","{\"enabled\":true,\"company_name\":\"focus house\",\"align\":\"center\",\"invert\":true,\"paper_width\":25,\"paper_height\":38,\"margin_top\":2,\"margin_bottom\":2,\"margin_left\":2,\"margin_right\":2,\"elements\":{\"company\":{\"show\":true,\"h\":4,\"w\":21,\"top\":0,\"left\":0,\"font\":10},\"item_name\":{\"show\":true,\"h\":4,\"w\":21,\"top\":4,\"left\":0,\"font\":8},\"barcode_linear\":{\"show\":true,\"h\":15,\"w\":21,\"top\":8,\"left\":0,\"font\":0},\"barcode_text\":{\"show\":true,\"h\":3.5,\"w\":21,\"top\":20,\"left\":0,\"font\":8},\"code\":{\"show\":true,\"h\":3.5,\"w\":21,\"top\":23.5,\"left\":0,\"font\":8},\"price_before\":{\"show\":true,\"h\":3,\"w\":21,\"top\":27,\"left\":0,\"font\":9},\"price_after\":{\"show\":true,\"h\":3.5,\"w\":21,\"top\":30,\"left\":0,\"font\":9},\"price_retail\":{\"show\":false,\"h\":3.5,\"w\":21,\"top\":33.5,\"left\":0,\"font\":9},\"price_wholesale\":{\"show\":false,\"h\":3.5,\"w\":21,\"top\":37,\"left\":0,\"font\":9},\"line_1\":{\"show\":false,\"h\":0.5,\"w\":21,\"top\":7.5,\"left\":0,\"font\":0},\"line_2\":{\"show\":false,\"h\":0.5,\"w\":21,\"top\":26,\"left\":0,\"font\":0},\"text_1\":{\"show\":false,\"h\":3,\"w\":21,\"top\":28.5,\"left\":0,\"font\":8},\"text_2\":{\"show\":false,\"h\":3,\"w\":21,\"top\":31.5,\"left\":0,\"font\":8}},\"text_1_content\":\"\",\"text_2_content\":\"\",\"code_prefix\":\"\",\"code_suffix\":\"\",\"unit_barcode_add\":\"\",\"include_barcode\":true,\"include_item_code\":true,\"embed_prices\":\"none\",\"price_before_source\":\"price1\",\"price_after_source\":\"printed\",\"strike_before\":true}","{\"enabled\":true,\"paper_width\":\"78mm\",\"font_family\":\"Courier New\",\"font_size\":16,\"line_spacing\":1.4,\"margin_top\":1,\"margin_bottom\":2,\"margin_left\":1,\"margin_right\":1,\"auto_print\":true,\"auto_close\":true,\"direct_print\":true,\"print_jobs\":\"receipt\",\"show_borders\":false,\"header\":{\"show_logo\":false,\"logo_width\":60,\"logo_align\":\"center\",\"show_company_name\":true,\"company_name_size\":16,\"show_company_tel\":false,\"show_company_add\":false,\"show_tax_no\":true,\"tax_no\":\"300000000000003\",\"show_cr_no\":true,\"cr_no\":\"\",\"header_text\":\"\",\"align\":\"center\"},\"meta\":{\"show_invoice_num\":true,\"invoice_num_style\":\"badge\",\"show_invoice_barcode\":false,\"barcode_height\":30,\"show_datetime\":true,\"show_cashier\":true,\"show_order_type\":false,\"show_driver\":false},\"customer\":{\"show\":false,\"show_name\":true,\"show_phone\":true,\"show_address\":true,\"show_balance_before\":false,\"show_balance_after\":false},\"items\":{\"show_index\":false,\"show_code\":false,\"show_name\":true,\"show_qty\":true,\"show_unit\":false,\"show_price\":true,\"show_disc\":false,\"show_total\":true,\"show_item_disc_subline\":true,\"border_style\":\"minimal\",\"font_size\":16},\"totals\":{\"show_items_total\":false,\"show_items_disc\":false,\"show_subtotal\":true,\"show_inv_disc\":false,\"show_extra\":false,\"show_net\":true,\"show_paid\":false,\"show_change\":false,\"highlight_net\":true,\"show_vat\":false,\"show_vat_qr\":true,\"vat_rate\":5,\"vat_inclusive\":true},\"footer\":{\"show_footer_text\":false,\"footer_text\":\"❤ perfect place to grow\",\"show_notes\":false,\"notes_text\":\"\",\"align\":\"center\"}}","","","0","small","xlarge");



DROP TABLE IF EXISTS shift_group_items;

CREATE TABLE `shift_group_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL COMMENT 'رقم مجموعة الورديات',
  `shift_id` int(11) NOT NULL COMMENT 'رقم الوردية من shifts',
  `sort` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_group_shift_unique` (`group_id`,`shift_id`),
  KEY `idx_shift_group_items_group` (`group_id`),
  KEY `idx_shift_group_items_shift` (`shift_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE IF EXISTS shift_groups;

CREATE TABLE `shift_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT 'اسم مجموعة الورديات',
  `info` text DEFAULT NULL COMMENT 'ملاحظات وتفاصيل المجموعة',
  `max_detect_window` int(11) NOT NULL DEFAULT 720 COMMENT 'أقصى نافذة للدقائق للتعرف التلقائي',
  `isdeleted` tinyint(1) NOT NULL DEFAULT 0,
  `tenant` int(11) NOT NULL DEFAULT 0,
  `branch` int(11) NOT NULL DEFAULT 0,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_shift_groups_tenant` (`tenant`,`isdeleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE IF EXISTS shift_payments;

CREATE TABLE `shift_payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shift_id` int(11) NOT NULL COMMENT 'closed_orders.id',
  `account_id` int(11) NOT NULL COMMENT 'الخزنة/البنك من acc_head',
  `start_amount` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'رصيد بداية الشيفت',
  `movement` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'صافي الحركة خلال الشيفت',
  `expected` decimal(15,2) NOT NULL DEFAULT 0.00,
  `counted` decimal(15,2) NOT NULL DEFAULT 0.00,
  `diff` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'المعدود - المتوقع',
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_shift_payments_shift` (`shift_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO shift_payments VALUES ("1","1","21","0.00","66.00","66.00","66.00","0.00","2026-11-14 21:23:20");
INSERT INTO shift_payments VALUES ("2","2","21","0.00","7399.00","7399.00","0.00","-7399.00","2026-11-22 19:52:54");
INSERT INTO shift_payments VALUES ("3","2","39","0.00","111.00","111.00","0.00","-111.00","2026-11-22 19:52:54");



DROP TABLE IF EXISTS shifts;

CREATE TABLE `shifts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `info` text DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `shiftstart` time DEFAULT NULL,
  `shiftend` time DEFAULT NULL,
  `hours` double(11,2) DEFAULT NULL,
  `instart` time DEFAULT NULL,
  `inend` time DEFAULT NULL,
  `outstart` time DEFAULT NULL,
  `outend` time DEFAULT NULL,
  `latelimit` int(1) DEFAULT NULL,
  `earlylimit` int(11) DEFAULT NULL,
  `grace1_action` varchar(20) NOT NULL DEFAULT 'actual',
  `latelimit_2` int(11) DEFAULT 0,
  `earlylimit_2` int(11) DEFAULT 0,
  `grace2_action` varchar(20) NOT NULL DEFAULT 'half',
  `ignore_early_in` tinyint(1) DEFAULT 0,
  `ignore_late_out` tinyint(1) DEFAULT 0,
  `workingdays` varchar(20) DEFAULT NULL,
  `single_fp_rule` varchar(20) NOT NULL DEFAULT 'half',
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  `is_flexible` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'ساعات مرنة',
  `required_hours` decimal(5,2) NOT NULL DEFAULT 8.00 COMMENT 'الساعات المطلوبة يوميًا (مرن)',
  `day_cutoff` time NOT NULL DEFAULT '06:00:00' COMMENT 'بداية يوم العمل: البصمة قبلها تتبع اليوم السابق (مرن)',
  `dup_fp_minutes` int(11) NOT NULL DEFAULT 2 COMMENT 'البصمات المتتالية خلال هذه الدقائق تُعد بصمة واحدة (مرن)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE IF EXISTS shw_optns;

CREATE TABLE `shw_optns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sname` varchar(100) NOT NULL,
  `is_show` int(11) NOT NULL DEFAULT 0,
  `def_width` int(11) NOT NULL DEFAULT 50,
  `cur_width` int(11) NOT NULL DEFAULT 50,
  `info` varchar(150) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO shw_optns VALUES ("1","op_id","0","50","50","معرف العملية","2024-03-13 19:54:16","2024-03-13 20:32:33","0","0","0");
INSERT INTO shw_optns VALUES ("2","op_date","0","50","50","التاريخ","2024-03-13 20:32:08","2024-03-13 20:32:46","0","0","0");
INSERT INTO shw_optns VALUES ("3","op_tybe","0","50","50","نوع العمليه","2024-03-13 20:32:08","2024-03-13 20:32:54","0","0","0");
INSERT INTO shw_optns VALUES ("4","op_store","0","50","50","المستودع","2024-03-13 20:32:08","2024-03-13 20:32:08","0","0","0");
INSERT INTO shw_optns VALUES ("5","op_num","0","50","50","رقم السند","2024-03-13 20:32:08","2024-03-13 20:32:08","0","0","0");
INSERT INTO shw_optns VALUES ("6","acc_num","0","50","50","رقم الحساب","2024-03-13 20:32:08","2024-03-13 20:32:08","0","0","0");
INSERT INTO shw_optns VALUES ("7","acc_id","0","50","50","اسم الحساب","2024-03-13 20:32:08","2024-03-13 20:32:08","0","0","0");
INSERT INTO shw_optns VALUES ("8","op_val","0","50","50","قيمه العمليه","2024-03-13 20:32:08","2024-03-13 20:32:08","0","0","0");
INSERT INTO shw_optns VALUES ("9","op_profit","0","50","50","الربح","2024-03-13 20:32:08","2024-03-13 20:33:07","0","0","0");
INSERT INTO shw_optns VALUES ("10","emb_id","0","50","50","البائع","2024-03-13 20:32:08","2024-03-13 20:33:15","0","0","0");
INSERT INTO shw_optns VALUES ("11","usid","0","50","50","المستخدم","2024-03-13 20:32:08","2024-03-13 20:33:22","0","0","0");
INSERT INTO shw_optns VALUES ("12","fatcost","0","50","50","تكلفه المبيعات","2024-03-13 20:32:08","2024-03-13 20:33:28","0","0","0");
INSERT INTO shw_optns VALUES ("13","crtime","0","50","50","الوقت","2024-03-13 20:32:08","2024-03-13 20:32:08","0","0","0");
INSERT INTO shw_optns VALUES ("14","cl_code","0","50","50","رقم العميل","2024-03-13 20:47:10","2024-03-13 20:47:10","0","0","0");
INSERT INTO shw_optns VALUES ("15","cl_id","0","50","50","اسم العميل","2024-03-13 20:47:10","2024-03-13 20:47:10","0","0","0");
INSERT INTO shw_optns VALUES ("16","acc_blance","0","50","50","الرصيد الحالى-بالعمله المحليه","2024-03-13 20:47:10","2024-03-13 20:47:10","0","0","0");
INSERT INTO shw_optns VALUES ("17","acc_cur","0","50","50","عمله الحساب","2024-03-13 20:47:10","2024-03-13 20:47:10","0","0","0");



DROP TABLE IF EXISTS sitting_items;

CREATE TABLE `sitting_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `iname` varchar(250) NOT NULL,
  `item_value` int(1) NOT NULL DEFAULT 0,
  `item_description` varchar(250) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO sitting_items VALUES ("1","الموظف يحاسب علي اساس ساعات العمل","0",NULL,"2023-12-27 00:32:16","2023-12-27 00:32:16","0","0","0");
INSERT INTO sitting_items VALUES ("2","الموظف يحاسب علي اساس ساعات العمل التقديريه 26 يوم","0",NULL,"2023-12-27 00:33:03","2023-12-27 00:33:03","0","0","0");
INSERT INTO sitting_items VALUES ("3","الشهر عباره عن 30 يوم","0",NULL,"2023-12-27 00:35:34","2023-12-27 00:35:34","0","0","0");
INSERT INTO sitting_items VALUES ("4","البصمه المفقوده يتم تجاهلها","0",NULL,"2023-12-27 00:35:34","2023-12-27 00:35:34","0","0","0");



DROP TABLE IF EXISTS skills;

CREATE TABLE `skills` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sname` varchar(200) NOT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `info` varchar(200) DEFAULT NULL,
  `scolor` varchar(100) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user` int(11) NOT NULL DEFAULT 1,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE IF EXISTS system_logs;

CREATE TABLE `system_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `timestamp` datetime NOT NULL,
  `level` varchar(20) NOT NULL,
  `message` text NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `request_uri` varchar(500) DEFAULT NULL,
  `context` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;




DROP TABLE IF EXISTS tables;

CREATE TABLE `tables` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tname` varchar(255) NOT NULL,
  `table_case` int(11) NOT NULL DEFAULT 0,
  `crtime` datetime DEFAULT current_timestamp(),
  `mdtime` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `isdeleted` tinyint(1) NOT NULL DEFAULT 0,
  `branch` varchar(255) DEFAULT NULL,
  `tatnet` varchar(255) DEFAULT NULL,
  `parent_table_id` int(11) DEFAULT NULL COMMENT 'الطاولة الرئيسية في حالة الدمج',
  `is_merged` tinyint(1) DEFAULT 0 COMMENT 'هل الطاولة مدمجة',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO tables VALUES ("1","طاولة 1","0","2026-11-14 21:22:22","2026-11-14 21:22:22","0",NULL,NULL,NULL,"0");
INSERT INTO tables VALUES ("2","طاولة 2","0","2026-11-14 21:22:22","2026-11-14 21:22:22","0",NULL,NULL,NULL,"0");
INSERT INTO tables VALUES ("3","طاولة 3","0","2026-11-14 21:22:22","2026-11-14 21:22:22","0",NULL,NULL,NULL,"0");
INSERT INTO tables VALUES ("4","طاولة 4","0","2026-11-14 21:22:22","2026-11-14 21:22:22","0",NULL,NULL,NULL,"0");
INSERT INTO tables VALUES ("5","طاولة 5","0","2026-11-14 21:22:22","2026-11-14 21:22:22","0",NULL,NULL,NULL,"0");
INSERT INTO tables VALUES ("6","طاولة 6","0","2026-11-14 21:22:22","2026-11-14 21:22:22","0",NULL,NULL,NULL,"0");
INSERT INTO tables VALUES ("7","طاولة 7","0","2026-11-14 21:22:22","2026-11-14 21:22:22","0",NULL,NULL,NULL,"0");
INSERT INTO tables VALUES ("8","طاولة 8","0","2026-11-14 21:22:22","2026-11-14 21:22:22","0",NULL,NULL,NULL,"0");
INSERT INTO tables VALUES ("9","طاولة 9","0","2026-11-14 21:22:22","2026-11-14 21:22:22","0",NULL,NULL,NULL,"0");
INSERT INTO tables VALUES ("10","طاولة 10","0","2026-11-14 21:22:22","2026-11-14 21:22:22","0",NULL,NULL,NULL,"0");
INSERT INTO tables VALUES ("11","طاولة 11","0","2026-11-14 21:22:22","2026-11-14 21:22:22","0",NULL,NULL,NULL,"0");
INSERT INTO tables VALUES ("12","طاولة 12","0","2026-11-14 21:22:22","2026-11-14 21:22:22","0",NULL,NULL,NULL,"0");



DROP TABLE IF EXISTS tasks;

CREATE TABLE `tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `ch_tybe` int(11) NOT NULL,
  `info` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `user` int(1) NOT NULL,
  `tasktybe` int(1) NOT NULL,
  `important` int(1) NOT NULL,
  `urgent` int(1) NOT NULL,
  `emp_comment` varchar(200) DEFAULT NULL,
  `cl_comment` varchar(200) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE IF EXISTS tasktybes;

CREATE TABLE `tasktybes` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `name` varchar(25) NOT NULL,
  `info` text DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO tasktybes VALUES ("1","زياره اعطال",NULL,"2023-07-27 15:13:07","0","0","0");
INSERT INTO tasktybes VALUES ("2","زياره تسويق",NULL,"2023-07-27 15:13:07","0","0","0");
INSERT INTO tasktybes VALUES ("3","زياره علاقات",NULL,"2023-07-27 15:13:07","0","0","0");
INSERT INTO tasktybes VALUES ("4","تركيب",NULL,"2023-12-23 03:44:24","0","0","0");
INSERT INTO tasktybes VALUES ("5","كلينت",NULL,"2023-12-23 03:44:24","0","0","0");



DROP TABLE IF EXISTS test;

CREATE TABLE `test` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `name` varchar(50) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `test` varchar(1) DEFAULT NULL,
  `time` time DEFAULT NULL,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;




DROP TABLE IF EXISTS towns;

CREATE TABLE `towns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `info` text DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE IF EXISTS transactions;

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tdate` date NOT NULL,
  `details` varchar(200) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE IF EXISTS users;

CREATE TABLE `users` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `uname` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `usertype` int(11) NOT NULL,
  `userrole` int(11) NOT NULL DEFAULT 1,
  `is_waiter` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1 = ويتر، 0 = مستخدم عادي',
  `img` varchar(255) NOT NULL,
  `def_client` int(11) DEFAULT NULL,
  `def_fund` int(11) DEFAULT NULL,
  `def_store` int(11) DEFAULT NULL,
  `def_prod` int(11) DEFAULT NULL,
  `def_emp` int(11) DEFAULT NULL,
  `tasksindex` int(11) DEFAULT NULL,
  `tasksadd` int(11) DEFAULT NULL,
  `tasksedit` int(11) DEFAULT NULL,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  `def_supplier` int(11) DEFAULT NULL COMMENT 'المورد الافتراضي (acc_head.id)',
  `def_price_list` int(11) DEFAULT NULL COMMENT 'قائمة الأسعار الافتراضية (price_lists.id)',
  PRIMARY KEY (`id`),
  KEY `is_waiter` (`is_waiter`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO users VALUES ("1","admin","$2y$10$Iwj2rn5VtA/ohPp3Xw6FzugFbB33yA7G/..cBJxGCP9LNOpQFgWou","2022-12-05 17:01:33","0","2","1","0","22947314.png",NULL,NULL,NULL,NULL,NULL,NULL,"1",NULL,"0","0",NULL,NULL);



DROP TABLE IF EXISTS usr_pwrs;

CREATE TABLE `usr_pwrs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rollname` varchar(50) DEFAULT NULL,
  `is_active` int(11) DEFAULT 1,
  `is_fav_users` int(11) DEFAULT 0,
  `show_users` int(11) DEFAULT 1,
  `add_users` int(11) DEFAULT 1,
  `edit_users` int(11) DEFAULT 1,
  `delete_users` int(11) DEFAULT 1,
  `is_fav_general_entrys` int(11) DEFAULT 0,
  `show_general_entrys` int(11) DEFAULT 1,
  `add_general_entrys` int(11) DEFAULT 1,
  `edit_general_entrys` int(11) DEFAULT 1,
  `delete_general_entrys` int(11) DEFAULT 1,
  `is_fav_clients` int(11) DEFAULT 0,
  `show_clients` int(11) DEFAULT 1,
  `add_clients` int(11) DEFAULT 1,
  `edit_clients` int(11) DEFAULT 1,
  `is_fav_suppliers` int(11) DEFAULT 0,
  `delete_clients` int(11) DEFAULT 1,
  `show_suppliers` int(11) DEFAULT 1,
  `add_suppliers` int(11) DEFAULT 1,
  `edit_suppliers` int(11) DEFAULT 1,
  `delete_suppliers` int(11) DEFAULT 1,
  `is_fav_funds` int(11) DEFAULT 0,
  `show_funds` int(11) DEFAULT 1,
  `add_funds` int(11) DEFAULT 1,
  `edit_funds` int(11) DEFAULT 1,
  `delete_funds` int(11) DEFAULT 1,
  `is_fav_banks` int(11) DEFAULT 0,
  `show_banks` int(11) DEFAULT 1,
  `add_banks` int(11) DEFAULT 1,
  `edit_banks` int(11) DEFAULT 1,
  `delete_banks` int(11) DEFAULT 1,
  `is_fav_stock` int(11) DEFAULT 0,
  `show_stock` int(11) DEFAULT 1,
  `add_stock` int(11) DEFAULT 1,
  `edit_stock` int(11) DEFAULT 1,
  `delete_stock` int(11) DEFAULT 1,
  `is_fav_expenses` int(11) DEFAULT 0,
  `show_expenses` int(11) DEFAULT 1,
  `add_expenses` int(11) DEFAULT 1,
  `edit_expenses` int(11) DEFAULT 1,
  `delete_expenses` int(11) DEFAULT 1,
  `is_fav_revenuses` int(11) DEFAULT 0,
  `show_revenuses` int(11) DEFAULT 1,
  `add_revenuses` int(11) DEFAULT 1,
  `edit_revenuses` int(11) DEFAULT 1,
  `delete_revenuses` int(11) DEFAULT 1,
  `is_fav_credits` int(11) DEFAULT 0,
  `show_credits` int(11) DEFAULT 1,
  `add_credits` int(11) DEFAULT 1,
  `edit_credits` int(11) DEFAULT 1,
  `delete_credits` int(11) DEFAULT 1,
  `is_fav_depits` int(11) DEFAULT 0,
  `show_depits` int(11) DEFAULT 1,
  `add_depits` int(11) DEFAULT 1,
  `edit_depits` int(11) DEFAULT 1,
  `delete_depits` int(11) DEFAULT 1,
  `is_fav_partners` int(11) DEFAULT 0,
  `show_partners` int(11) DEFAULT 1,
  `add_partners` int(11) DEFAULT 1,
  `edit_partners` int(11) DEFAULT 1,
  `delete_partners` int(11) DEFAULT 1,
  `is_fav_assets` int(11) DEFAULT 0,
  `show_assets` int(11) DEFAULT 1,
  `add_assets` int(11) DEFAULT 1,
  `edit_assets` int(11) DEFAULT 1,
  `delete_assets` int(11) DEFAULT 1,
  `is_fav_rentables` int(11) DEFAULT 0,
  `show_rentables` int(11) DEFAULT 1,
  `add_rentables` int(11) DEFAULT 1,
  `edit_rentables` int(11) DEFAULT 1,
  `delete_rentables` int(11) DEFAULT 1,
  `is_fav_employees` int(11) DEFAULT 0,
  `show_employees` int(11) DEFAULT 1,
  `add_employees` int(11) DEFAULT 1,
  `edit_employees` int(11) DEFAULT 1,
  `delete_employees` int(11) DEFAULT 1,
  `is_fav_items` int(11) DEFAULT 0,
  `show_items` int(11) DEFAULT 1,
  `add_items` int(11) DEFAULT 1,
  `edit_items` int(11) DEFAULT 1,
  `delete_items` int(11) DEFAULT 1,
  `is_fav_item_groups` int(11) DEFAULT 0,
  `show_item_groups` int(11) DEFAULT 1,
  `add_item_groups` int(11) DEFAULT 1,
  `edit_item_groups` int(11) DEFAULT 1,
  `delete_item_groups` int(11) DEFAULT 1,
  `is_fav_sales` int(11) DEFAULT 0,
  `show_sales` int(11) DEFAULT 1,
  `add_sales` int(11) DEFAULT 1,
  `edit_sales` int(11) DEFAULT 1,
  `delete_sales` int(11) DEFAULT 1,
  `is_fav_resale` int(11) DEFAULT 0,
  `show_resale` int(11) DEFAULT 1,
  `add_resale` int(11) DEFAULT 1,
  `edit_resale` int(11) DEFAULT 1,
  `delete_resale` int(11) DEFAULT 1,
  `is_fav_purcases` int(11) DEFAULT 0,
  `show_purchases` int(11) DEFAULT 1,
  `add_purchases` int(11) DEFAULT 1,
  `edit_purchases` int(11) DEFAULT 1,
  `delete_purchases` int(11) DEFAULT 1,
  `is_fav_repurchases` int(11) DEFAULT 0,
  `show_repurchases` int(11) DEFAULT 1,
  `add_repurchases` int(11) DEFAULT 1,
  `edit_repurchases` int(11) DEFAULT 1,
  `delete_repurchases` int(11) DEFAULT 1,
  `is_fav_recive` int(11) DEFAULT 0,
  `show_recive` int(11) DEFAULT 1,
  `add_recive` int(11) DEFAULT 1,
  `edit_recive` int(11) DEFAULT 1,
  `delete_recive` int(11) DEFAULT 1,
  `show_payment` int(11) DEFAULT 1,
  `is_fav_payment` int(11) DEFAULT 0,
  `add_payment` int(11) DEFAULT 1,
  `edit_payment` int(11) DEFAULT 1,
  `delete_payment` int(11) DEFAULT 1,
  `is_fav_clinic_clients` int(11) DEFAULT 0,
  `show_clinic_clients` int(11) DEFAULT 1,
  `add_clinic_clients` int(11) DEFAULT 1,
  `edit_clinic_clients` int(11) DEFAULT 1,
  `delete_clinic_clients` int(11) DEFAULT 1,
  `is_fav_reservations` int(11) DEFAULT 0,
  `show_reservations` int(11) DEFAULT 1,
  `add_reservations` int(11) DEFAULT 1,
  `edit_reservations` int(11) DEFAULT 1,
  `delete_reservations` int(11) DEFAULT 1,
  `is_fav_drugs` int(11) DEFAULT 0,
  `show_drugs` int(11) DEFAULT 1,
  `add_drugs` int(11) DEFAULT 1,
  `edit_drugs` int(11) DEFAULT 1,
  `is_fav_attandance` int(11) DEFAULT 1,
  `delete_attandance` int(11) DEFAULT 1,
  `edit_attandance` int(11) DEFAULT 1,
  `show_attandance` int(11) DEFAULT 1,
  `add_attandance` int(11) DEFAULT 1,
  `delete_drugs` int(11) DEFAULT 1,
  `is_fav_client_profile` int(11) DEFAULT 0,
  `show_client_profile` int(11) DEFAULT 1,
  `add_client_profile` int(11) DEFAULT 1,
  `edit_client_profile` int(11) DEFAULT 1,
  `delete_client_profile` int(11) DEFAULT 1,
  `is_fav_advanced_clients` int(11) DEFAULT 0,
  `show_advanced_clients` int(11) DEFAULT 1,
  `add_advanced_clients` int(11) DEFAULT 1,
  `edit_advanced_clients` int(11) DEFAULT 1,
  `delete_advanced_clients` int(11) DEFAULT 1,
  `is_fav_chances` int(11) DEFAULT 0,
  `show_chances` int(11) DEFAULT 1,
  `add_chances` int(11) DEFAULT 1,
  `edit_chances` int(11) DEFAULT 1,
  `delete_chances` int(11) DEFAULT 1,
  `is_fav_calls` int(11) DEFAULT 0,
  `show_calls` int(11) DEFAULT 1,
  `add_calls` int(11) DEFAULT 1,
  `edit_calls` int(11) DEFAULT 1,
  `delete_calls` int(11) DEFAULT 1,
  `is_fav_journals` int(11) DEFAULT 0,
  `show_journals` int(11) DEFAULT 1,
  `add_journals` int(11) DEFAULT 1,
  `edit_journals` int(11) DEFAULT 1,
  `delete_journals` int(11) DEFAULT 1,
  `show_gl_reports` int(11) DEFAULT 1,
  `show_clinic_reports` int(11) DEFAULT 1,
  `show_rent_reports` int(11) DEFAULT 1,
  `show_payroll_report` int(11) DEFAULT 1,
  `show_hr_report` int(11) DEFAULT 1,
  `sid_entry` int(11) DEFAULT 1,
  `sid_stock` int(11) DEFAULT 1,
  `sid_sales` int(11) DEFAULT 1,
  `sid_purchases` int(11) DEFAULT 1,
  `sid_vouchers` int(11) DEFAULT 1,
  `sid_clinics` int(11) DEFAULT 1,
  `sid_crm` int(11) DEFAULT 1,
  `sid_accounts` int(11) DEFAULT 1,
  `sid_assets` int(11) DEFAULT 1,
  `sid_reports` int(11) DEFAULT 1,
  `sid_hr` int(11) DEFAULT 1,
  `sid_payroll` int(11) DEFAULT 1,
  `sid_rents` int(11) NOT NULL DEFAULT 1,
  `show_total_reservation` int(11) DEFAULT 1,
  `show_ended_reservation` int(11) DEFAULT 1,
  `info` varchar(250) DEFAULT NULL,
  `isdeleted` tinyint(1) DEFAULT 0,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  `show_all_tasks` int(1) DEFAULT NULL,
  `show_main_cards` tinyint(1) NOT NULL DEFAULT 1,
  `show_main_elements` tinyint(1) NOT NULL DEFAULT 1,
  `show_main_tables` tinyint(1) NOT NULL DEFAULT 1,
  `show_main_hr` tinyint(1) NOT NULL DEFAULT 1,
  `view_shift_expected` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'يشوف المتوقع في تقفيل الشيفت حتى مع الجرد الأعمى',
  `add_delivery` int(11) NOT NULL DEFAULT 1,
  `allow_pos_delivery` int(11) NOT NULL DEFAULT 1,
  `delete_delivery` int(11) NOT NULL DEFAULT 1,
  `edit_delivery` int(11) NOT NULL DEFAULT 1,
  `edit_user_passwords` int(11) NOT NULL DEFAULT 0,
  `prevent_negative_stock` int(11) NOT NULL DEFAULT 0,
  `show_delivery` int(11) NOT NULL DEFAULT 1,
  `sid_cards` int(11) NOT NULL DEFAULT 1,
  `sid_visits` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `rollname` (`rollname`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO usr_pwrs VALUES ("1","admin","1","1","1","1","1","1","0","0","0","0","0","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","0","0","0","0","0","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","wwww","1","2024-05-12 18:05:26","2026-11-11 16:56:57","0","0","1","1","1","1","1","1","1","1","1","1","0","0","1","1","1");
INSERT INTO usr_pwrs VALUES ("2","cashier","1","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","1","0","0","0","0","0","0","0","0","0","0","0","0","cshier","1","2024-05-12 18:11:21","2025-03-05 21:32:34","0","0","0","0","1","0","1","0","1","1","1","1","0","0","1","1","1");
INSERT INTO usr_pwrs VALUES ("26","دكتور","1","1","1","1","1","1","0","0","0","0","0","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","0","0","0","0","0","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","0","0","0","0","0","1","0","1","0","0","0","0","0","1","1","دكتور","1","2024-05-19 22:04:27","2024-08-21 16:00:57","0","0","0","1","1","1","1","0","1","1","1","1","0","0","1","1","1");
INSERT INTO usr_pwrs VALUES ("27","مساعد دكتور","1","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","1","0","0","0","0","0","0","0","0","0","مساعد دكتور","1","2024-05-30 23:48:11","2024-05-31 01:02:13","0","0",NULL,"1","1","1","1","0","1","1","1","1","0","0","1","1","1");
INSERT INTO usr_pwrs VALUES ("28","بائع كاشير","1","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","1","1","1","1","1","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","مبيعات كاشير فقط","0","2024-07-28 20:02:09","2024-07-28 20:02:09","0","0",NULL,"1","1","1","1","0","1","1","1","1","0","0","1","1","1");



DROP TABLE IF EXISTS vacancies;

CREATE TABLE `vacancies` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `info` text DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE IF EXISTS visits;

CREATE TABLE `visits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client` int(11) NOT NULL,
  `gender` enum('male','female') NOT NULL DEFAULT 'male',
  `age_group` enum('under18','18_25','25_40','over40') NOT NULL DEFAULT 'under18',
  `mode` enum('solo','group') NOT NULL DEFAULT 'solo',
  `start_time` time NOT NULL DEFAULT '00:00:00',
  `end_time` time NOT NULL DEFAULT '00:00:00',
  `order_value` enum('under60','over60') NOT NULL DEFAULT 'under60',
  `type` enum('new','returning','regular') NOT NULL DEFAULT 'new',
  `created_by` int(10) unsigned NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `complaint` varchar(250) DEFAULT NULL,
  `diagnosis` varchar(250) DEFAULT NULL,
  `recommendation` varchar(250) DEFAULT NULL,
  `prescription` varchar(250) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `info` varchar(250) NOT NULL,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE IF EXISTS visittybes;

CREATE TABLE `visittybes` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `name` varchar(25) NOT NULL,
  `value` double DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO visittybes VALUES ("1","كشف 1","400","2023-09-04 02:57:36","0","0","0");
INSERT INTO visittybes VALUES ("2","اعادة","250","2023-09-04 02:57:36","0","0","0");
INSERT INTO visittybes VALUES ("3","مستعجل","500","2024-05-03 23:57:27","0","0","0");
INSERT INTO visittybes VALUES ("4","زيارة 2","500","2024-05-04 20:57:54","0","0","0");
INSERT INTO visittybes VALUES ("5","private","800","2024-05-04 20:58:28","0","0","0");



DROP TABLE IF EXISTS zankat;

CREATE TABLE `zankat` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `zname` varchar(100) NOT NULL,
  `colors` int(1) NOT NULL DEFAULT 1,
  `ctp` int(1) DEFAULT NULL,
  `zncase` int(1) DEFAULT NULL,
  `print` int(1) DEFAULT NULL,
  `ptype` int(1) DEFAULT NULL,
  `service` int(1) DEFAULT NULL,
  `prod` int(1) DEFAULT NULL,
  `measure` int(1) NOT NULL,
  `draw` int(1) NOT NULL,
  `farkh` int(1) NOT NULL,
  `info` varchar(255) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `date` varchar(20) NOT NULL,
  `user` varchar(20) NOT NULL,
  `fatid` int(11) DEFAULT 0,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




