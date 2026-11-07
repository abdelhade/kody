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
) ENGINE=InnoDB AUTO_INCREMENT=281 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
INSERT INTO acc_head VALUES ("21","1211","0","1","الصندوق الافتراضي",NULL,NULL,NULL,"0","0","1",NULL,"18",NULL,"1","0","0","0","0","-13466.000","0","2023-12-09 10:46:52","2026-11-03 15:15:48",NULL,"0","0","0","0","1");
INSERT INTO acc_head VALUES ("24","1221","0","0","العميل النقدي",NULL,NULL,NULL,"0","0","0","0","19",NULL,"1","0","0","0","0","0.000","0","2023-12-28 01:25:46","2026-10-31 20:04:15",NULL,"0","0","0","1","1");
INSERT INTO acc_head VALUES ("27","123001","0","0","المخزن الرئيسي",NULL,NULL,NULL,"0","1","0","0","20",NULL,"1","0","0","0","0","1122326.000","0","2023-12-28 03:35:35","2026-11-03 15:16:57",NULL,"0","0","0","0","1");
INSERT INTO acc_head VALUES ("29","2211","0","0","الشريك الرئيسي",NULL,NULL,NULL,"0","0","0",NULL,"9",NULL,"1","0","0","0","0","0.000","0","2023-12-30 02:12:22","2025-10-28 11:51:21",NULL,"0","0","0","0","1");
INSERT INTO acc_head VALUES ("33","211","0","0","الموردين",NULL,NULL,NULL,"0","0","0",NULL,"8",NULL,"1","1","0","0","0","0.000","0","2024-01-22 05:41:26","2024-06-11 05:17:55",NULL,"0","0","0","0","1");
INSERT INTO acc_head VALUES ("34","212","0","1","الدائنين الاخرين",NULL,NULL,NULL,"0","0","0",NULL,"8",NULL,"1","1","0","0","0","0.000","0","2024-01-22 05:42:08","2026-10-31 20:04:15",NULL,"0","0","0","0","1");
INSERT INTO acc_head VALUES ("35","213","0","0","الموظفين",NULL,NULL,NULL,"0","0","0",NULL,"8",NULL,"1","1","0","0","0","0.000","0","2024-01-22 05:42:29","2024-06-11 05:18:01",NULL,"0","0","0","0","1");
INSERT INTO acc_head VALUES ("36","2111","0","0","المورد الافتراضي",NULL,NULL,NULL,"0","0","0","0","33",NULL,"1","0","0","0","0","-420322.000","0","2024-01-23 06:17:26","2026-11-02 21:10:52",NULL,"0","0","0","1","1");
INSERT INTO acc_head VALUES ("37","124","0","0","البنوك",NULL,NULL,NULL,"0","0","0",NULL,"7",NULL,"1","1","0","0","0","0.000","0","2024-01-23 06:22:23","2024-06-11 05:18:07",NULL,"0","0","0","0","1");
INSERT INTO acc_head VALUES ("38","125","0","0","مدينين آخرين",NULL,NULL,NULL,"0","0","0",NULL,"7",NULL,"1","1","0","0","0","0.000","0","2024-01-23 06:30:11","2024-06-11 05:18:13",NULL,"0","0","0","0","1");
INSERT INTO acc_head VALUES ("39","1241","0","1","البنك الافتراضي",NULL,NULL,NULL,"0","0","0",NULL,"37",NULL,"1","0","0","0","0","1534.000","0","2024-01-23 06:32:21","2026-11-02 21:33:04",NULL,"0","0","0","0","1");
INSERT INTO acc_head VALUES ("40","44","0","0","مصروفات عامه ",NULL,NULL,NULL,"0","0","0",NULL,"5",NULL,"2","1","0","0","0","0.000","0","2024-01-23 06:34:08","2024-06-11 05:18:24",NULL,"0","0","0","0","1");
INSERT INTO acc_head VALUES ("55","112","0","0","اصول قابله للتأجير",NULL,NULL,NULL,"0","0","0","0","6",NULL,"1","1","0","0","0","0.000","0","2024-02-20 05:16:57","2024-06-11 05:18:30",NULL,"0","0","0","0","1");
INSERT INTO acc_head VALUES ("86","411","0","0","صافي المشتريات",NULL,NULL,NULL,"0","0","0","0","15",NULL,"2","1","0","0","0","0.000","0","2024-03-08 00:56:24","2024-06-11 05:18:46",NULL,"0","0","0","0","1");
INSERT INTO acc_head VALUES ("89","4111","0","0","المشتربات",NULL,NULL,NULL,"0","0","0","0","86",NULL,"2","1","0","0","0","0.000","0","2024-03-08 21:37:09","2024-06-11 05:18:53",NULL,"0","0","0","0","1");
INSERT INTO acc_head VALUES ("90","4112","0","0","مردود المشتريات",NULL,NULL,NULL,"0","0","0","0","86",NULL,"2","1","0","0","0","0.000","0","2024-03-08 21:38:25","2024-06-11 05:18:56",NULL,"0","0","0","0","1");
INSERT INTO acc_head VALUES ("91","41103","0","1","خصم مسموح به",NULL,NULL,NULL,"0","0","0","0","86",NULL,"2","0","0","0","0","0.000","0","2024-03-08 21:43:40","2026-10-31 20:04:15",NULL,"0","0","0","0","1");
INSERT INTO acc_head VALUES ("92","311","0","0","صافي المبيعات",NULL,NULL,NULL,"0","0","0","0","13",NULL,"2","1","0","0","0","0.000","0","2024-03-08 21:48:15","2024-06-11 05:19:02",NULL,"0","0","0","0","1");
INSERT INTO acc_head VALUES ("93","3111","0","0","المبيعات",NULL,NULL,NULL,"0","0","0","0","92",NULL,"2","1","0","0","0","0.000","0","2024-03-08 21:49:07","2024-06-11 05:19:05",NULL,"0","0","0","0","1");
INSERT INTO acc_head VALUES ("94","3112","0","0","مردود المبيعات",NULL,NULL,NULL,"0","0","0","0","92",NULL,"2","1","0","0","0","0.000","0","2024-03-08 21:50:03","2024-06-11 05:19:07",NULL,"0","0","0","0","1");
INSERT INTO acc_head VALUES ("95","3113","0","0","خصومات تشغيل",NULL,NULL,NULL,"0","0","0","0","92",NULL,"2","1","0","0","0","0.000","0","2024-03-08 21:54:56","2024-06-11 05:19:10",NULL,"0","0","0","0","1");
INSERT INTO acc_head VALUES ("97","31131","0","1","خصم مكتسب",NULL,NULL,NULL,"0","0","0","0","95",NULL,"2","0","0","0","0","0.000","0","2024-03-10 00:38:24","2024-11-16 22:48:00",NULL,"0","0","0","0","1");
INSERT INTO acc_head VALUES ("98","321","0","0","ايرادات من تأجير أصول",NULL,NULL,NULL,"0","0","0","0","14",NULL,"2","1","0","0","0","0.000","0","2024-03-14 14:58:05","2024-06-11 05:19:27",NULL,"0","0","0","0","1");
INSERT INTO acc_head VALUES ("99","32101","0","0","ايرادات من التأجير",NULL,NULL,NULL,"0","0","0","0","98",NULL,"2","0","0","0","0","-45000.000","0","2024-03-14 15:01:19","2026-10-26 18:26:47",NULL,"0","0","0","0","1");
INSERT INTO acc_head VALUES ("131","213001","0","1","الموظف 1",NULL,NULL,NULL,"0","0","0","0","35",NULL,"1","0","0","0","0","0.000","0","2024-06-18 09:41:56","2026-10-31 20:04:15",NULL,"0","0","0","0","1");
INSERT INTO acc_head VALUES ("148","122001","0","0","العميل الافتراضي","","",NULL,"0","0","0","0","19",NULL,"1","0","0","0","0","45000.000","0","2024-06-23 01:26:46","2026-11-02 21:33:09",NULL,"0","0","0","0","1");
INSERT INTO acc_head VALUES ("274","121002","1","1","insta","","",NULL,"0","0","1","0","18",NULL,"1","0","0","0","0","0.000","0","2026-10-31 20:20:29","2026-10-31 20:20:29",NULL,"0","0","0","0","1");
INSERT INTO acc_head VALUES ("275","124002","1","1","بنك 2","","",NULL,"0","0","0","0","37",NULL,"1","0","0","0","0","0.000","0","2026-10-31 20:20:48","2026-10-31 20:20:48",NULL,"0","0","0","0","1");
INSERT INTO acc_head VALUES ("280","2112","1","1","مورد 2","","",NULL,"0","0","0",NULL,"211",NULL,NULL,"0","0","0","0","-690072.000","0","2026-11-03 15:15:36","2026-11-03 15:16:57","","0","0","0","0","1");



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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




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

INSERT INTO closed_orders VALUES ("1","1","admin","2026-11-02","2026-10-26 18:26:46","21:31:53","46135.4","0","0","0","0","0","0",NULL,"0","","2026-11-02 21:31:53","2026-11-02 21:31:53",NULL,"1","1","851.55","283.85","0.00","0.00","0.00","0.00","0.00","-1135.40","1",NULL,"1","2026-10-26 18:26:46","2026-11-02 21:31:53","6","0.00","1135.40","0.00");
INSERT INTO closed_orders VALUES ("2","2","admin","2026-11-02","2026-11-02 21:32:49","21:33:23","1930.8","0","0","0","0","0","0",NULL,"0","","2026-11-02 21:33:23","2026-11-02 21:33:23",NULL,"1","1","681.55","1249.25","0.00","0.00","0.00","0.00","0.00","-1930.80","1",NULL,"1","2026-11-02 21:32:49","2026-11-02 21:33:23","4","0.00","1930.80","0.00");



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
  `fatid` int(11) DEFAULT NULL,
  `fat_tybe` int(11) NOT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`),
  CONSTRAINT `fat_details_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `myitems` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO fat_details VALUES ("1","3","27","1","1","1.000","0","150","150","100","0.00",NULL,"0","0.00",NULL,"15000","15000","1","3","2026-10-26 18:26:47","0","0","0");
INSERT INTO fat_details VALUES ("2","3","27","1","2","1.000","0","150","150","200","0.00",NULL,"0","0.00",NULL,"30000","30000","1","3","2026-10-26 18:26:47","0","0","0");
INSERT INTO fat_details VALUES ("3","4","27","2","1","1.000","1","0","1","99","99.00",NULL,"0","0.00",NULL,"99","0","2","4","2026-11-02 21:06:34","0","0","0");
INSERT INTO fat_details VALUES ("4","4","27","2","2","1.000","1","0","1","150","0.00",NULL,"0","0.00",NULL,"150","0","2","4","2026-11-02 21:06:34","0","0","0");
INSERT INTO fat_details VALUES ("5","4","27","3","3","1.000","2","0","2","44","44.00",NULL,"0","0.00",NULL,"88","0","3","4","2026-11-02 21:09:13","0","0","0");
INSERT INTO fat_details VALUES ("6","4","27","3","4","1.000","5","0","5","5","5.00",NULL,"0","0.00",NULL,"25","0","3","4","2026-11-02 21:09:13","0","0","0");
INSERT INTO fat_details VALUES ("7","4","27","3","5","1.000","5","0","5","48","48.00",NULL,"0","0.00",NULL,"240","0","3","4","2026-11-02 21:09:13","0","0","0");
INSERT INTO fat_details VALUES ("8","4","27","3","6","1.000","98","0","98","84","84.00",NULL,"0","0.00",NULL,"8232","0","3","4","2026-11-02 21:09:13","0","0","0");
INSERT INTO fat_details VALUES ("9","4","27","3","7","1.000","4","0","4","4","4.00",NULL,"0","0.00",NULL,"16","0","3","4","2026-11-02 21:09:13","0","0","0");
INSERT INTO fat_details VALUES ("10","4","27","3","8","1.000","1","0","1","848","848.00",NULL,"0","0.00",NULL,"848","0","3","4","2026-11-02 21:09:13","0","0","0");
INSERT INTO fat_details VALUES ("11","4","27","3","2","1.000","84","0","84","4848","0.00",NULL,"0","0.00",NULL,"407232","0","3","4","2026-11-02 21:09:13","0","0","0");
INSERT INTO fat_details VALUES ("12","4","27","3","1","1.000","4","0","4","848","99.00",NULL,"0","0.00",NULL,"3392","0","3","4","2026-11-02 21:09:13","0","0","0");
INSERT INTO fat_details VALUES ("13","9","27","4","8","1.000","0","1",NULL,"0",NULL,NULL,"0","0.00",NULL,"0","0","4","9","2026-11-02 21:30:37","0","0","0");
INSERT INTO fat_details VALUES ("14","9","27","4","6","1.000","0","1",NULL,"0",NULL,NULL,"0","0.00",NULL,"0","0","4","9","2026-11-02 21:30:37","0","0","0");
INSERT INTO fat_details VALUES ("15","9","27","4","5","1.000","0","1",NULL,"0",NULL,NULL,"0","0.00",NULL,"0","0","4","9","2026-11-02 21:30:37","0","0","0");
INSERT INTO fat_details VALUES ("16","9","27","4","3","1.000","0","1",NULL,"0",NULL,NULL,"0","0.00",NULL,"0","0","4","9","2026-11-02 21:30:37","0","0","0");
INSERT INTO fat_details VALUES ("17","9","27","5","8","1.000","0","1",NULL,"0",NULL,NULL,"0","0.00",NULL,"0","0","5","9","2026-11-02 21:30:46","0","0","0");
INSERT INTO fat_details VALUES ("18","9","27","5","1","1.000","0","1",NULL,"113.85",NULL,NULL,"0","0.00",NULL,"113.85","0","5","9","2026-11-02 21:30:46","0","0","0");
INSERT INTO fat_details VALUES ("19","9","27","5","2","1.000","0","1",NULL,"170",NULL,NULL,"0","0.00",NULL,"170","0","5","9","2026-11-02 21:30:46","0","0","0");
INSERT INTO fat_details VALUES ("20","9","27","5","7","1.000","0","1",NULL,"0",NULL,NULL,"0","0.00",NULL,"0","0","5","9","2026-11-02 21:30:46","0","0","0");
INSERT INTO fat_details VALUES ("21","9","27","5","6","1.000","0","1",NULL,"0",NULL,NULL,"0","0.00",NULL,"0","0","5","9","2026-11-02 21:30:46","0","0","0");
INSERT INTO fat_details VALUES ("22","9","27","5","5","1.000","0","1",NULL,"0",NULL,NULL,"0","0.00",NULL,"0","0","5","9","2026-11-02 21:30:46","0","0","0");
INSERT INTO fat_details VALUES ("23","9","27","7","8","1.000","0","1",NULL,"0",NULL,NULL,"0","0.00",NULL,"0","0","7","9","2026-11-02 21:31:04","0","0","0");
INSERT INTO fat_details VALUES ("24","9","27","7","7","1.000","0","1",NULL,"0",NULL,NULL,"0","0.00",NULL,"0","0","7","9","2026-11-02 21:31:04","0","0","0");
INSERT INTO fat_details VALUES ("25","9","27","7","6","1.000","0","1",NULL,"0",NULL,NULL,"0","0.00",NULL,"0","0","7","9","2026-11-02 21:31:04","0","0","0");
INSERT INTO fat_details VALUES ("26","9","27","7","5","1.000","0","1",NULL,"0",NULL,NULL,"0","0.00",NULL,"0","0","7","9","2026-11-02 21:31:04","0","0","0");
INSERT INTO fat_details VALUES ("27","9","27","8","8","1.000","0","1",NULL,"0",NULL,NULL,"0","0.00",NULL,"0","0","8","9","2026-11-02 21:31:17","0","0","0");
INSERT INTO fat_details VALUES ("28","9","27","8","7","1.000","0","1",NULL,"0",NULL,NULL,"0","0.00",NULL,"0","0","8","9","2026-11-02 21:31:17","0","0","0");
INSERT INTO fat_details VALUES ("29","9","27","8","6","1.000","0","1",NULL,"0",NULL,NULL,"0","0.00",NULL,"0","0","8","9","2026-11-02 21:31:17","0","0","0");
INSERT INTO fat_details VALUES ("30","9","27","8","5","1.000","0","1",NULL,"0",NULL,NULL,"0","0.00",NULL,"0","0","8","9","2026-11-02 21:31:17","0","0","0");
INSERT INTO fat_details VALUES ("31","9","27","8","2","1.000","0","1",NULL,"170",NULL,NULL,"0","0.00",NULL,"170","0","8","9","2026-11-02 21:31:17","0","0","0");
INSERT INTO fat_details VALUES ("32","9","27","8","1","1.000","0","1",NULL,"113.85",NULL,NULL,"0","0.00",NULL,"113.85","0","8","9","2026-11-02 21:31:17","0","0","0");
INSERT INTO fat_details VALUES ("33","9","27","10","2","1.000","0","2",NULL,"170",NULL,NULL,"0","0.00",NULL,"340","0","10","9","2026-11-02 21:31:22","0","0","0");
INSERT INTO fat_details VALUES ("34","9","27","10","1","1.000","0","2",NULL,"113.85",NULL,NULL,"0","0.00",NULL,"227.7","0","10","9","2026-11-02 21:31:22","0","0","0");
INSERT INTO fat_details VALUES ("35","9","27","12","2","1.000","0","1",NULL,"170",NULL,NULL,"0","0.00",NULL,"170","0","12","9","2026-11-02 21:32:49","0","0","0");
INSERT INTO fat_details VALUES ("36","9","27","12","1","1.000","0","1",NULL,"113.85",NULL,NULL,"0","0.00",NULL,"113.85","0","12","9","2026-11-02 21:32:49","0","0","0");
INSERT INTO fat_details VALUES ("37","9","27","14","1","1.000","0","3",NULL,"113.85",NULL,NULL,"0","0.00",NULL,"341.54999999999995","0","14","9","2026-11-02 21:32:54","0","0","0");
INSERT INTO fat_details VALUES ("38","9","27","14","2","1.000","0","2",NULL,"170",NULL,NULL,"0","0.00",NULL,"340","0","14","9","2026-11-02 21:32:54","0","0","0");
INSERT INTO fat_details VALUES ("39","9","27","16","1","1.000","0","2",NULL,"113.85",NULL,NULL,"0","0.00",NULL,"227.7","0","16","9","2026-11-02 21:33:04","0","0","0");
INSERT INTO fat_details VALUES ("40","9","27","16","2","1.000","0","2",NULL,"170",NULL,NULL,"0","0.00",NULL,"340","0","16","9","2026-11-02 21:33:04","0","0","0");
INSERT INTO fat_details VALUES ("41","9","27","18","1","1.000","0","2",NULL,"113.85",NULL,NULL,"0","0.00",NULL,"227.7","0","18","9","2026-11-02 21:33:08","0","0","0");
INSERT INTO fat_details VALUES ("42","9","27","18","2","1.000","0","1",NULL,"170",NULL,NULL,"0","0.00",NULL,"170","0","18","9","2026-11-02 21:33:08","0","0","0");
INSERT INTO fat_details VALUES ("43","19","27","20","3","1.000","0","0","4","0","44.00",NULL,"0","0.00",NULL,"0","0","20","19","2026-11-02 21:51:24","0","0","0");
INSERT INTO fat_details VALUES ("44","19","27","20","5","1.000","0","0","3","0","48.00",NULL,"0","0.00",NULL,"0","0","20","19","2026-11-02 21:51:24","0","0","0");
INSERT INTO fat_details VALUES ("45","19","27","20","7","1.000","0","0","3","0","4.00",NULL,"0","0.00",NULL,"0","0","20","19","2026-11-02 21:51:24","0","0","0");
INSERT INTO fat_details VALUES ("46","4","27","21","9","1.000","154","0","154","124","124.00",NULL,"0","0.00",NULL,"19096","0","21","4","2026-11-03 15:15:48","0","0","0");
INSERT INTO fat_details VALUES ("47","4","27","21","10","1.000","5454","0","5454","120","120.00",NULL,"0","0.00",NULL,"654480","0","21","4","2026-11-03 15:15:48","0","0","0");
INSERT INTO fat_details VALUES ("48","4","27","23","9","1.000","134","0","134","124","124.00",NULL,"0","0.00",NULL,"16616","0","23","4","2026-11-03 15:16:57","0","0","0");
INSERT INTO fat_details VALUES ("49","4","27","23","10","1.000","124","0","124","120","120.00",NULL,"0","0.00",NULL,"14880","0","23","4","2026-11-03 15:16:57","0","0","0");



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
  PRIMARY KEY (`id`),
  UNIQUE KEY `gname` (`gname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO item_units VALUES ("1","1","1","1.000","0","0","0","99","113.850","0.000","0.000","0.000","1","0","0","0");
INSERT INTO item_units VALUES ("2","2","1","1.000","0","0","0","0","170.000","150.000","0.000","0.000","1565456465456","0","0","0");
INSERT INTO item_units VALUES ("3","3","1","1.000","0","0","0","0","0.000","0.000","0.000","0.000","15454546","0","0","0");
INSERT INTO item_units VALUES ("4","4","1","1.000","0","0","0","0","0.000","0.000","0.000","0.000","15454547","0","0","0");
INSERT INTO item_units VALUES ("5","5","1","1.000","0","0","0","0","0.000","0.000","0.000","0.000","15454548","0","0","0");
INSERT INTO item_units VALUES ("6","6","1","1.000","0","0","0","0","0.000","0.000","0.000","0.000","15454549","0","0","0");
INSERT INTO item_units VALUES ("7","7","1","1.000","0","0","0","0","0.000","0.000","0.000","0.000","15454550","0","0","0");
INSERT INTO item_units VALUES ("8","8","1","1.000","0","0","0","0","0.000","0.000","0.000","0.000","15454551","0","0","0");
INSERT INTO item_units VALUES ("9","9","1","1.000","0","0","0","0","124.000","0.000","0.000","0.000","تجريبي 3","0","0","0");
INSERT INTO item_units VALUES ("10","10","1","1.000","0","0","0","0","120.000","0.000","0.000","0.000","تجريبي 4","0","0","0");



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
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO journal_entries VALUES ("1","1","148","45000","0","0",NULL,"2026-10-26 18:26:46","0","1","0","2026-10-26 18:26:46","0","0");
INSERT INTO journal_entries VALUES ("2","1","99","0","45000","1",NULL,"2026-10-26 18:26:47","0","1","0","2026-10-26 18:26:47","0","0");
INSERT INTO journal_entries VALUES ("3","2","27","249","0","0",NULL,"2026-11-02 21:06:34","0","2","0","2026-11-02 21:06:34","0","0");
INSERT INTO journal_entries VALUES ("4","2","36","0","249","1",NULL,"2026-11-02 21:06:34","0","2","0","2026-11-02 21:06:34","0","0");
INSERT INTO journal_entries VALUES ("5","3","27","420073","0","0",NULL,"2026-11-02 21:09:13","0","3","0","2026-11-02 21:09:13","0","0");
INSERT INTO journal_entries VALUES ("6","3","36","0","420073","1",NULL,"2026-11-02 21:09:13","0","3","0","2026-11-02 21:09:13","0","0");
INSERT INTO journal_entries VALUES ("7","4","148","0","0","0",NULL,"2026-11-02 21:30:37","0","4","0","2026-11-02 21:30:37","0","0");
INSERT INTO journal_entries VALUES ("8","4","27","0","0","1",NULL,"2026-11-02 21:30:37","0","4","0","2026-11-02 21:30:37","0","0");
INSERT INTO journal_entries VALUES ("9","5","148","284","0","0",NULL,"2026-11-02 21:30:46","0","5","0","2026-11-02 21:30:46","0","0");
INSERT INTO journal_entries VALUES ("10","5","27","0","284","1",NULL,"2026-11-02 21:30:46","0","5","0","2026-11-02 21:30:46","0","0");
INSERT INTO journal_entries VALUES ("11","6","39","284","0","0",NULL,"2026-11-02 21:30:46","5","0","0","2026-11-02 21:30:46","0","0");
INSERT INTO journal_entries VALUES ("12","6","148","0","284","1",NULL,"2026-11-02 21:30:46","5","0","0","2026-11-02 21:30:46","0","0");
INSERT INTO journal_entries VALUES ("13","7","148","0","0","0",NULL,"2026-11-02 21:31:04","0","7","0","2026-11-02 21:31:04","0","0");
INSERT INTO journal_entries VALUES ("14","7","27","0","0","1",NULL,"2026-11-02 21:31:04","0","7","0","2026-11-02 21:31:04","0","0");
INSERT INTO journal_entries VALUES ("15","8","148","284","0","0",NULL,"2026-11-02 21:31:17","0","8","0","2026-11-02 21:31:17","0","0");
INSERT INTO journal_entries VALUES ("16","8","27","0","284","1",NULL,"2026-11-02 21:31:17","0","8","0","2026-11-02 21:31:17","0","0");
INSERT INTO journal_entries VALUES ("17","9","21","284","0","0",NULL,"2026-11-02 21:31:17","8","0","0","2026-11-02 21:31:17","0","0");
INSERT INTO journal_entries VALUES ("18","9","148","0","284","1",NULL,"2026-11-02 21:31:17","8","0","0","2026-11-02 21:31:17","0","0");
INSERT INTO journal_entries VALUES ("19","10","148","568","0","0",NULL,"2026-11-02 21:31:22","0","10","0","2026-11-02 21:31:22","0","0");
INSERT INTO journal_entries VALUES ("20","10","27","0","568","1",NULL,"2026-11-02 21:31:22","0","10","0","2026-11-02 21:31:22","0","0");
INSERT INTO journal_entries VALUES ("21","11","21","568","0","0",NULL,"2026-11-02 21:31:22","10","0","0","2026-11-02 21:31:22","0","0");
INSERT INTO journal_entries VALUES ("22","11","148","0","568","1",NULL,"2026-11-02 21:31:22","10","0","0","2026-11-02 21:31:22","0","0");
INSERT INTO journal_entries VALUES ("23","12","148","284","0","0",NULL,"2026-11-02 21:32:49","0","12","0","2026-11-02 21:32:49","0","0");
INSERT INTO journal_entries VALUES ("24","12","27","0","284","1",NULL,"2026-11-02 21:32:49","0","12","0","2026-11-02 21:32:49","0","0");
INSERT INTO journal_entries VALUES ("25","13","21","284","0","0",NULL,"2026-11-02 21:32:49","12","0","0","2026-11-02 21:32:49","0","0");
INSERT INTO journal_entries VALUES ("26","13","148","0","284","1",NULL,"2026-11-02 21:32:49","12","0","0","2026-11-02 21:32:49","0","0");
INSERT INTO journal_entries VALUES ("27","14","148","682","0","0",NULL,"2026-11-02 21:32:54","0","14","0","2026-11-02 21:32:54","0","0");
INSERT INTO journal_entries VALUES ("28","14","27","0","682","1",NULL,"2026-11-02 21:32:54","0","14","0","2026-11-02 21:32:54","0","0");
INSERT INTO journal_entries VALUES ("29","15","39","682","0","0",NULL,"2026-11-02 21:32:54","14","0","0","2026-11-02 21:32:54","0","0");
INSERT INTO journal_entries VALUES ("30","15","148","0","682","1",NULL,"2026-11-02 21:32:54","14","0","0","2026-11-02 21:32:54","0","0");
INSERT INTO journal_entries VALUES ("31","16","148","568","0","0",NULL,"2026-11-02 21:33:04","0","16","0","2026-11-02 21:33:04","0","0");
INSERT INTO journal_entries VALUES ("32","16","27","0","568","1",NULL,"2026-11-02 21:33:04","0","16","0","2026-11-02 21:33:04","0","0");
INSERT INTO journal_entries VALUES ("33","17","39","568","0","0",NULL,"2026-11-02 21:33:04","16","0","0","2026-11-02 21:33:04","0","0");
INSERT INTO journal_entries VALUES ("34","17","148","0","568","1",NULL,"2026-11-02 21:33:04","16","0","0","2026-11-02 21:33:04","0","0");
INSERT INTO journal_entries VALUES ("35","18","148","398","0","0",NULL,"2026-11-02 21:33:08","0","18","0","2026-11-02 21:33:08","0","0");
INSERT INTO journal_entries VALUES ("36","18","27","0","398","1",NULL,"2026-11-02 21:33:08","0","18","0","2026-11-02 21:33:08","0","0");
INSERT INTO journal_entries VALUES ("37","19","21","398","0","0",NULL,"2026-11-02 21:33:09","18","0","0","2026-11-02 21:33:09","0","0");
INSERT INTO journal_entries VALUES ("38","19","148","0","398","1",NULL,"2026-11-02 21:33:09","18","0","0","2026-11-02 21:33:09","0","0");
INSERT INTO journal_entries VALUES ("39","20","27","673576","0","0",NULL,"2026-11-03 15:15:48","0","21","0","2026-11-03 15:15:48","0","0");
INSERT INTO journal_entries VALUES ("40","20","280","0","673576","1",NULL,"2026-11-03 15:15:48","0","21","0","2026-11-03 15:15:48","0","0");
INSERT INTO journal_entries VALUES ("41","21","280","15000","0","0",NULL,"2026-11-03 15:15:48","21","0","0","2026-11-03 15:15:48","0","0");
INSERT INTO journal_entries VALUES ("42","21","21","0","15000","1",NULL,"2026-11-03 15:15:48","21","0","0","2026-11-03 15:15:48","0","0");
INSERT INTO journal_entries VALUES ("43","22","27","31496","0","0",NULL,"2026-11-03 15:16:57","0","23","0","2026-11-03 15:16:57","0","0");
INSERT INTO journal_entries VALUES ("44","22","280","0","31496","1",NULL,"2026-11-03 15:16:57","0","23","0","2026-11-03 15:16:57","0","0");



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
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO journal_heads VALUES ("1","1","45000","2026-10-26","1",NULL,"فاتورة مبيعات _ 1","2026-10-26 18:26:46","0","0","2026-10-26 18:26:46","1","0","0");
INSERT INTO journal_heads VALUES ("2","2","249","2026-11-02","2",NULL,"فاتورة مشتريات _ 2","2026-11-02 21:06:34","0","0","2026-11-02 21:06:34","1","0","0");
INSERT INTO journal_heads VALUES ("3","3","420073","2026-11-02","3",NULL,"فاتورة مشتريات _ 3","2026-11-02 21:09:13","0","0","2026-11-02 21:09:13","1","0","0");
INSERT INTO journal_heads VALUES ("4","4","0","2026-11-02","4",NULL,"فاتورة مبيعات POS رقم 1","2026-11-02 21:30:37","0","0","2026-11-02 21:30:37","1","0","0");
INSERT INTO journal_heads VALUES ("5","5","283.85","2026-11-02","5",NULL,"فاتورة مبيعات POS رقم 2","2026-11-02 21:30:46","0","0","2026-11-02 21:30:46","1","0","0");
INSERT INTO journal_heads VALUES ("6","6","283.85","2026-11-02","6",NULL,"سند قبض من فاتورة POS رقم 2","2026-11-02 21:30:46","5","0","2026-11-02 21:30:46","1","0","0");
INSERT INTO journal_heads VALUES ("7","7","0","2026-11-02","7",NULL,"فاتورة مبيعات POS رقم 3","2026-11-02 21:31:04","0","0","2026-11-02 21:31:04","1","0","0");
INSERT INTO journal_heads VALUES ("8","8","283.85","2026-11-02","8",NULL,"فاتورة مبيعات POS رقم 4","2026-11-02 21:31:17","0","0","2026-11-02 21:31:17","1","0","0");
INSERT INTO journal_heads VALUES ("9","9","283.85","2026-11-02","9",NULL,"سند قبض من فاتورة POS رقم 4","2026-11-02 21:31:17","8","0","2026-11-02 21:31:17","1","0","0");
INSERT INTO journal_heads VALUES ("10","10","567.7","2026-11-02","10",NULL,"فاتورة مبيعات POS رقم 5","2026-11-02 21:31:22","0","0","2026-11-02 21:31:22","1","0","0");
INSERT INTO journal_heads VALUES ("11","11","567.7","2026-11-02","11",NULL,"سند قبض من فاتورة POS رقم 5","2026-11-02 21:31:22","10","0","2026-11-02 21:31:22","1","0","0");
INSERT INTO journal_heads VALUES ("12","12","283.85","2026-11-02","12",NULL,"فاتورة مبيعات POS رقم 6","2026-11-02 21:32:49","0","0","2026-11-02 21:32:49","1","0","0");
INSERT INTO journal_heads VALUES ("13","13","283.85","2026-11-02","13",NULL,"سند قبض من فاتورة POS رقم 6","2026-11-02 21:32:49","12","0","2026-11-02 21:32:49","1","0","0");
INSERT INTO journal_heads VALUES ("14","14","681.55","2026-11-02","14",NULL,"فاتورة مبيعات POS رقم 7","2026-11-02 21:32:54","0","0","2026-11-02 21:32:54","1","0","0");
INSERT INTO journal_heads VALUES ("15","15","681.55","2026-11-02","15",NULL,"سند قبض من فاتورة POS رقم 7","2026-11-02 21:32:54","14","0","2026-11-02 21:32:54","1","0","0");
INSERT INTO journal_heads VALUES ("16","16","567.7","2026-11-02","16",NULL,"فاتورة مبيعات POS رقم 8","2026-11-02 21:33:04","0","0","2026-11-02 21:33:04","1","0","0");
INSERT INTO journal_heads VALUES ("17","17","567.7","2026-11-02","17",NULL,"سند قبض من فاتورة POS رقم 8","2026-11-02 21:33:04","16","0","2026-11-02 21:33:04","1","0","0");
INSERT INTO journal_heads VALUES ("18","18","397.7","2026-11-02","18",NULL,"فاتورة مبيعات POS رقم 9","2026-11-02 21:33:08","0","0","2026-11-02 21:33:08","1","0","0");
INSERT INTO journal_heads VALUES ("19","19","397.7","2026-11-02","19",NULL,"سند قبض من فاتورة POS رقم 9","2026-11-02 21:33:09","18","0","2026-11-02 21:33:09","1","0","0");
INSERT INTO journal_heads VALUES ("20","20","673576","2026-11-03","21",NULL,"فاتورة مشتريات _ 21","2026-11-03 15:15:48","0","0","2026-11-03 15:15:48","1","0","0");
INSERT INTO journal_heads VALUES ("21","21","15000","2026-11-03","22",NULL,"سند دفع كاش _ 3","2026-11-03 15:15:48","21","0","2026-11-03 15:15:48","1","0","0");
INSERT INTO journal_heads VALUES ("22","22","31496","2026-11-03","23",NULL,"فاتورة مشتريات _ 23","2026-11-03 15:16:57","0","0","2026-11-03 15:16:57","1","0","0");



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
  PRIMARY KEY (`id`),
  UNIQUE KEY `iname` (`iname`),
  KEY `idx_myitems_iname` (`iname`),
  KEY `idx_myitems_name2` (`name2`),
  KEY `idx_myitems_barcode` (`barcode`),
  KEY `idx_myitems_isdeleted` (`isdeleted`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO myitems VALUES ("1","تجريبي 1","","1","1","1","-157","","0","99","848","113.85","0","0","0","0","0","0","2026-10-26 18:26:30","2026-11-02 21:33:08","1","0","0","0");
INSERT INTO myitems VALUES ("2","تجريبي 2","","2","1","15454545","-75","","0","0","4848","170","150","0","0","0","0","0","2026-10-26 18:26:35","2026-11-02 21:33:08","1","0","0","1");
INSERT INTO myitems VALUES ("3","Brianna Jenkins","Ocean Atkinson","3","1","15454546","1","Rerum be","0","44","44","0","0","0","0","0","0","0","2026-11-02 21:07:41","2026-11-02 21:30:37","1","0","0","0");
INSERT INTO myitems VALUES ("4","Leandra Odonnell","Blair Berg","4","1","15454547","5","Voluptas","0","5","5","0","0","0","0","0","0","0","2026-11-02 21:07:44","2026-11-02 21:09:13","1","0","0","0");
INSERT INTO myitems VALUES ("5","Medge Rodgers","Debra Roach","5","1","15454548","1","Delectus","0","48","48","0","0","0","0","0","0","0","2026-11-02 21:07:46","2026-11-02 21:31:17","1","0","0","0");
INSERT INTO myitems VALUES ("6","Noelani Nunez","Violet Bright","6","1","15454549","94","Cupidita","0","84","84","0","0","0","0","0","0","0","2026-11-02 21:07:49","2026-11-02 21:31:17","1","0","0","0");
INSERT INTO myitems VALUES ("7","Olivia Cherry","Otto Ortega","7","1","15454550","1","Quo prae","0","4","4","0","0","0","0","0","0","0","2026-11-02 21:07:51","2026-11-02 21:31:17","1","0","0","0");
INSERT INTO myitems VALUES ("8","Rafael Baker","Troy Goodman","8","1","15454551","-3","Minus ne","0","848","848","0","0","0","0","0","0","0","2026-11-02 21:07:53","2026-11-02 21:31:17","1","0","0","0");
INSERT INTO myitems VALUES ("9","تجريبي 3","","9","1","تجريبي 3","288","","0","124","124","124","0","0","0","0","0","0","2026-11-03 15:14:28","2026-11-03 15:16:57","1","0","0","0");
INSERT INTO myitems VALUES ("10","تجريبي 4","","10","1","تجريبي 4","5578","","0","120","120","120","0","0","0","0","0","0","2026-11-03 15:14:37","2026-11-03 15:16:57","1","0","0","0");



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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO myunits VALUES ("1","ق","2026-10-26 18:26:00","2026-10-26 18:26:00","0","0","0");
INSERT INTO myunits VALUES ("2","كرتونة","2026-10-26 18:26:06","2026-10-26 18:26:06","0","0","0");



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
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO ot_head VALUES ("1","1",NULL,NULL,"takeaway","3","1",NULL,NULL,"1","3","نوع الطلب: تيك أواي","18:26:46","18:26:46",NULL,NULL,"2026-10-26","0000-00-00","1",NULL,NULL,"",NULL,"1","27","131","131",NULL,"21",NULL,NULL,"148",NULL,NULL,"45000","0","1","45000","45000","45000","0","0","0","0","0","0","0.00","0.00","unpaid","completed","2026-10-26 18:26:46","0","0","0","2026-10-26 18:26:47","1",NULL,"0","0","0","active","cash","{\"paid_cash\":0,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","1");
INSERT INTO ot_head VALUES ("2","1",NULL,NULL,"takeaway","4","1",NULL,NULL,"1","4","نوع الطلب: تيك أواي","21:06:34","21:06:34",NULL,NULL,"2026-11-02","0000-00-00","1",NULL,NULL,"",NULL,"1","27","131","131",NULL,"27",NULL,NULL,"36",NULL,NULL,"249","0","1","0","249","249","0","0","0","0","0","0","0.00","0.00","unpaid","completed","2026-11-02 21:06:34","0","0","0","2026-11-02 21:06:34","1",NULL,"0","0","0","active","cash","{\"paid_cash\":0,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","1");
INSERT INTO ot_head VALUES ("3","2",NULL,NULL,"takeaway","4","1",NULL,NULL,"1","4","نوع الطلب: تيك أواي","21:09:13","21:09:13",NULL,NULL,"2026-11-02","0000-00-00","1",NULL,NULL,"",NULL,"1","27","131","131",NULL,"27",NULL,NULL,"36",NULL,NULL,"420073","0","1","0","420073","420073","0","0","0","0","0","0","0.00","0.00","unpaid","completed","2026-11-02 21:09:13","0","0","0","2026-11-02 21:09:13","1",NULL,"0","0","0","active","cash","{\"paid_cash\":0,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","1");
INSERT INTO ot_head VALUES ("4","1",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: بيع مباشر","21:30:37","21:30:37",NULL,NULL,"2026-11-02","2026-11-02",NULL,NULL,NULL,"0",NULL,"1","27","131","131",NULL,"148",NULL,NULL,"27",NULL,NULL,"0",NULL,NULL,NULL,"0","0","0",NULL,"0",NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-02 21:30:37","0","0","0","2026-11-02 21:30:37","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","1");
INSERT INTO ot_head VALUES ("5","2",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: بيع مباشر","21:30:46","21:30:46",NULL,NULL,"2026-11-02","2026-11-02",NULL,NULL,NULL,"0",NULL,"1","27","131","131",NULL,"148",NULL,NULL,"27",NULL,NULL,"283",NULL,NULL,NULL,"283.85","283.85","0",NULL,"0",NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-02 21:30:46","0","0","0","2026-11-02 21:30:46","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","1");
INSERT INTO ot_head VALUES ("6","2",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","سند قبض من فاتورة POS رقم 2","21:30:46","21:30:46",NULL,NULL,"2026-11-02",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"39",NULL,NULL,"148",NULL,NULL,"283.85",NULL,NULL,NULL,NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-02 21:30:46","0","5","0","2026-11-02 21:30:46","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","1");
INSERT INTO ot_head VALUES ("7","3",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: بيع مباشر","21:31:04","21:31:04",NULL,NULL,"2026-11-02","2026-11-02",NULL,NULL,NULL,"0",NULL,"1","27","131","131",NULL,"148",NULL,NULL,"27",NULL,NULL,"0",NULL,NULL,NULL,"0","0","0",NULL,"0",NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-02 21:31:04","0","0","0","2026-11-02 21:31:04","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","1");
INSERT INTO ot_head VALUES ("8","4",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: بيع مباشر","21:31:17","21:31:17",NULL,NULL,"2026-11-02","2026-11-02",NULL,NULL,NULL,"0",NULL,"1","27","131","131",NULL,"148",NULL,NULL,"27",NULL,NULL,"283",NULL,NULL,NULL,"283.85","283.85","0",NULL,"0",NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-02 21:31:17","0","0","0","2026-11-02 21:31:17","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","1");
INSERT INTO ot_head VALUES ("9","4",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","سند قبض من فاتورة POS رقم 4","21:31:17","21:31:17",NULL,NULL,"2026-11-02",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"283.85",NULL,NULL,NULL,NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-02 21:31:17","0","8","0","2026-11-02 21:31:17","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","1");
INSERT INTO ot_head VALUES ("10","5",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: بيع مباشر","21:31:22","21:31:22",NULL,NULL,"2026-11-02","2026-11-02",NULL,NULL,NULL,"0",NULL,"1","27","131","131",NULL,"148",NULL,NULL,"27",NULL,NULL,"567",NULL,NULL,NULL,"567.7","567.7","0",NULL,"0",NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-02 21:31:22","0","0","0","2026-11-02 21:31:22","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","1");
INSERT INTO ot_head VALUES ("11","5",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","سند قبض من فاتورة POS رقم 5","21:31:22","21:31:22",NULL,NULL,"2026-11-02",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"567.7",NULL,NULL,NULL,NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-02 21:31:22","0","10","0","2026-11-02 21:31:22","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","1");
INSERT INTO ot_head VALUES ("12","6",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: بيع مباشر","21:32:49","21:32:49",NULL,NULL,"2026-11-02","2026-11-02",NULL,NULL,NULL,"0",NULL,"1","27","131","131",NULL,"148",NULL,NULL,"27",NULL,NULL,"283",NULL,NULL,NULL,"283.85","283.85","0",NULL,"0",NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-02 21:32:49","0","0","0","2026-11-02 21:32:49","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("13","6",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","سند قبض من فاتورة POS رقم 6","21:32:49","21:32:49",NULL,NULL,"2026-11-02",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"283.85",NULL,NULL,NULL,NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-02 21:32:49","0","12","0","2026-11-02 21:32:49","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("14","7",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: بيع مباشر","21:32:54","21:32:54",NULL,NULL,"2026-11-02","2026-11-02",NULL,NULL,NULL,"0",NULL,"1","27","131","131",NULL,"148",NULL,NULL,"27",NULL,NULL,"681",NULL,NULL,NULL,"681.55","681.55","0",NULL,"0",NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-02 21:32:54","0","0","0","2026-11-02 21:32:54","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("15","7",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","سند قبض من فاتورة POS رقم 7","21:32:54","21:32:54",NULL,NULL,"2026-11-02",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"39",NULL,NULL,"148",NULL,NULL,"681.55",NULL,NULL,NULL,NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-02 21:32:54","0","14","0","2026-11-02 21:32:54","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("16","8",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: بيع مباشر","21:33:04","21:33:04",NULL,NULL,"2026-11-02","2026-11-02",NULL,NULL,NULL,"0",NULL,"1","27","131","131",NULL,"148",NULL,NULL,"27",NULL,NULL,"567",NULL,NULL,NULL,"567.7","567.7","0",NULL,"0",NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-02 21:33:04","0","0","0","2026-11-02 21:33:04","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("17","8",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","سند قبض من فاتورة POS رقم 8","21:33:04","21:33:04",NULL,NULL,"2026-11-02",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"39",NULL,NULL,"148",NULL,NULL,"567.7",NULL,NULL,NULL,NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-02 21:33:04","0","16","0","2026-11-02 21:33:04","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("18","9",NULL,NULL,"takeaway","9","1",NULL,NULL,"1","9","نوع الطلب: بيع مباشر","21:33:08","21:33:08",NULL,NULL,"2026-11-02","2026-11-02",NULL,NULL,NULL,"0",NULL,"1","27","131","131",NULL,"148",NULL,NULL,"27",NULL,NULL,"397",NULL,NULL,NULL,"397.7","397.7","0",NULL,"0",NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-02 21:33:08","0","0","0","2026-11-02 21:33:08","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("19","9",NULL,NULL,"takeaway","1",NULL,NULL,NULL,"1","1","سند قبض من فاتورة POS رقم 9","21:33:08","21:33:08",NULL,NULL,"2026-11-02",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"21",NULL,NULL,"148",NULL,NULL,"397.7",NULL,NULL,NULL,NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-02 21:33:08","0","18","0","2026-11-02 21:33:08","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","2");
INSERT INTO ot_head VALUES ("20","1",NULL,NULL,"takeaway","19","0",NULL,NULL,"0","19","مستند طباعة باركود - نوع الطلب: تيك أواي","21:51:24","21:51:24",NULL,NULL,"2026-11-02","0000-00-00","1",NULL,NULL,"",NULL,"1","27","131","131",NULL,"27",NULL,NULL,"27",NULL,NULL,"0","0","1","0","0","0","0","0","0","0","0","0","0.00","0.00","paid","completed","2026-11-02 21:51:24","0","0","0","2026-11-02 21:51:24","1",NULL,"0","0","0","active","cash","{\"paid_cash\":0,\"paid_bank\":0,\"payment_fund_id\":0,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","0");
INSERT INTO ot_head VALUES ("21","3",NULL,NULL,"takeaway","4","1",NULL,NULL,"1","4","نوع الطلب: تيك أواي","15:15:48","15:15:48",NULL,NULL,"2026-11-03","0000-00-00","1",NULL,NULL,"",NULL,"1","27","131","131",NULL,"27",NULL,NULL,"280",NULL,NULL,"673576","0","1","0","673576","673576","0","0","0","0","0","0","15000.00","0.00","partial","completed","2026-11-03 15:15:48","0","0","0","2026-11-03 15:15:48","1",NULL,"0","0","0","active","cash","{\"paid_cash\":15000,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","0");
INSERT INTO ot_head VALUES ("22","1",NULL,NULL,"takeaway","2",NULL,NULL,NULL,"1","2","نوع الطلب: تيك أواي - دفع كاش","15:15:48","15:15:48",NULL,NULL,"2026-11-03",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"131",NULL,NULL,"280",NULL,NULL,"21",NULL,NULL,"15000",NULL,"1","0",NULL,"0",NULL,NULL,NULL,NULL,NULL,NULL,"0.00","0.00","unpaid","completed","2026-11-03 15:15:48","0","21","0","2026-11-03 15:15:48","1",NULL,"0","0","0","active","cash",NULL,NULL,NULL,NULL,"0.00","0");
INSERT INTO ot_head VALUES ("23","4",NULL,NULL,"takeaway","4","1",NULL,NULL,"1","4","نوع الطلب: تيك أواي","15:16:57","15:16:57",NULL,NULL,"2026-11-03","0000-00-00","1",NULL,NULL,"",NULL,"1","27","131","131",NULL,"27",NULL,NULL,"280",NULL,NULL,"31496","0","1","0","31496","31496","0","0","0","0","0","0","0.00","0.00","unpaid","completed","2026-11-03 15:16:57","0","0","0","2026-11-03 15:16:57","1",NULL,"0","0","0","active","cash","{\"paid_cash\":0,\"paid_bank\":0,\"payment_fund_id\":21,\"payment_bank_id\":0,\"change_amount\":0}",NULL,"","","0.00","0");



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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




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
) ENGINE=InnoDB AUTO_INCREMENT=295 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO permissions VALUES ("1","dashboard.cards","web","2026-10-25 18:12:05","2026-10-25 18:12:05","كروت الملخص في الشاشة الرئيسية","dashboard","لوحة التحكم","كروت الملخص في الشاشة الرئيسية",NULL,"10");
INSERT INTO permissions VALUES ("2","dashboard.shortcuts","web","2026-10-25 18:12:05","2026-10-25 18:12:05","الاختصارات في الشاشة الرئيسية","dashboard","لوحة التحكم","الاختصارات في الشاشة الرئيسية",NULL,"20");
INSERT INTO permissions VALUES ("3","dashboard.tables","web","2026-10-25 18:12:05","2026-10-25 18:12:05","الجداول في الشاشة الرئيسية","dashboard","لوحة التحكم","الجداول في الشاشة الرئيسية",NULL,"30");
INSERT INTO permissions VALUES ("4","dashboard.hr","web","2026-10-25 18:12:05","2026-10-25 18:12:05","ملخص الموارد البشرية في الشاشة الرئيسية","dashboard","لوحة التحكم","ملخص الموارد البشرية في الشاشة الرئيسية",NULL,"40");
INSERT INTO permissions VALUES ("5","clients.view","web","2026-10-25 18:12:05","2026-10-25 18:12:05","عرض العملاء","basic","البيانات الأساسية","العملاء","view","50");
INSERT INTO permissions VALUES ("6","clients.add","web","2026-10-25 18:12:05","2026-10-25 18:12:05","إضافة العملاء","basic","البيانات الأساسية","العملاء","add","60");
INSERT INTO permissions VALUES ("7","clients.edit","web","2026-10-25 18:12:05","2026-10-25 18:12:05","تعديل العملاء","basic","البيانات الأساسية","العملاء","edit","70");
INSERT INTO permissions VALUES ("8","clients.delete","web","2026-10-25 18:12:05","2026-10-25 18:12:05","حذف العملاء","basic","البيانات الأساسية","العملاء","delete","80");
INSERT INTO permissions VALUES ("9","suppliers.view","web","2026-10-25 18:12:05","2026-10-25 18:12:05","عرض الموردين","basic","البيانات الأساسية","الموردين","view","90");
INSERT INTO permissions VALUES ("10","suppliers.add","web","2026-10-25 18:12:05","2026-10-25 18:12:05","إضافة الموردين","basic","البيانات الأساسية","الموردين","add","100");
INSERT INTO permissions VALUES ("11","suppliers.edit","web","2026-10-25 18:12:05","2026-10-25 18:12:05","تعديل الموردين","basic","البيانات الأساسية","الموردين","edit","110");
INSERT INTO permissions VALUES ("12","suppliers.delete","web","2026-10-25 18:12:05","2026-10-25 18:12:05","حذف الموردين","basic","البيانات الأساسية","الموردين","delete","120");
INSERT INTO permissions VALUES ("13","funds.view","web","2026-10-25 18:12:05","2026-10-25 18:12:05","عرض الخزائن","basic","البيانات الأساسية","الخزائن","view","130");
INSERT INTO permissions VALUES ("14","funds.add","web","2026-10-25 18:12:05","2026-10-25 18:12:05","إضافة الخزائن","basic","البيانات الأساسية","الخزائن","add","140");
INSERT INTO permissions VALUES ("15","funds.edit","web","2026-10-25 18:12:05","2026-10-25 18:12:05","تعديل الخزائن","basic","البيانات الأساسية","الخزائن","edit","150");
INSERT INTO permissions VALUES ("16","funds.delete","web","2026-10-25 18:12:05","2026-10-25 18:12:05","حذف الخزائن","basic","البيانات الأساسية","الخزائن","delete","160");
INSERT INTO permissions VALUES ("17","banks.view","web","2026-10-25 18:12:05","2026-10-25 18:12:05","عرض البنوك","basic","البيانات الأساسية","البنوك","view","170");
INSERT INTO permissions VALUES ("18","banks.add","web","2026-10-25 18:12:05","2026-10-25 18:12:05","إضافة البنوك","basic","البيانات الأساسية","البنوك","add","180");
INSERT INTO permissions VALUES ("19","banks.edit","web","2026-10-25 18:12:05","2026-10-25 18:12:05","تعديل البنوك","basic","البيانات الأساسية","البنوك","edit","190");
INSERT INTO permissions VALUES ("20","banks.delete","web","2026-10-25 18:12:05","2026-10-25 18:12:05","حذف البنوك","basic","البيانات الأساسية","البنوك","delete","200");
INSERT INTO permissions VALUES ("21","stores.view","web","2026-10-25 18:12:05","2026-10-25 18:12:05","عرض المخازن","basic","البيانات الأساسية","المخازن","view","210");
INSERT INTO permissions VALUES ("22","stores.add","web","2026-10-25 18:12:05","2026-10-25 18:12:05","إضافة المخازن","basic","البيانات الأساسية","المخازن","add","220");
INSERT INTO permissions VALUES ("23","stores.edit","web","2026-10-25 18:12:05","2026-10-25 18:12:05","تعديل المخازن","basic","البيانات الأساسية","المخازن","edit","230");
INSERT INTO permissions VALUES ("24","stores.delete","web","2026-10-25 18:12:05","2026-10-25 18:12:05","حذف المخازن","basic","البيانات الأساسية","المخازن","delete","240");
INSERT INTO permissions VALUES ("25","expenses.view","web","2026-10-25 18:12:05","2026-10-25 18:12:05","عرض المصروفات","basic","البيانات الأساسية","المصروفات","view","250");
INSERT INTO permissions VALUES ("26","expenses.add","web","2026-10-25 18:12:05","2026-10-25 18:12:05","إضافة المصروفات","basic","البيانات الأساسية","المصروفات","add","260");
INSERT INTO permissions VALUES ("27","expenses.edit","web","2026-10-25 18:12:05","2026-10-25 18:12:05","تعديل المصروفات","basic","البيانات الأساسية","المصروفات","edit","270");
INSERT INTO permissions VALUES ("28","expenses.delete","web","2026-10-25 18:12:05","2026-10-25 18:12:05","حذف المصروفات","basic","البيانات الأساسية","المصروفات","delete","280");
INSERT INTO permissions VALUES ("29","revenues.view","web","2026-10-25 18:12:05","2026-10-25 18:12:05","عرض الإيرادات","basic","البيانات الأساسية","الإيرادات","view","290");
INSERT INTO permissions VALUES ("30","revenues.add","web","2026-10-25 18:12:05","2026-10-25 18:12:05","إضافة الإيرادات","basic","البيانات الأساسية","الإيرادات","add","300");
INSERT INTO permissions VALUES ("31","revenues.edit","web","2026-10-25 18:12:05","2026-10-25 18:12:05","تعديل الإيرادات","basic","البيانات الأساسية","الإيرادات","edit","310");
INSERT INTO permissions VALUES ("32","revenues.delete","web","2026-10-25 18:12:05","2026-10-25 18:12:05","حذف الإيرادات","basic","البيانات الأساسية","الإيرادات","delete","320");
INSERT INTO permissions VALUES ("33","creditors.view","web","2026-10-25 18:12:05","2026-10-25 18:12:05","عرض دائنين آخرين","basic","البيانات الأساسية","دائنين آخرين","view","330");
INSERT INTO permissions VALUES ("34","creditors.add","web","2026-10-25 18:12:05","2026-10-25 18:12:05","إضافة دائنين آخرين","basic","البيانات الأساسية","دائنين آخرين","add","340");
INSERT INTO permissions VALUES ("35","creditors.edit","web","2026-10-25 18:12:05","2026-10-25 18:12:05","تعديل دائنين آخرين","basic","البيانات الأساسية","دائنين آخرين","edit","350");
INSERT INTO permissions VALUES ("36","creditors.delete","web","2026-10-25 18:12:05","2026-10-25 18:12:05","حذف دائنين آخرين","basic","البيانات الأساسية","دائنين آخرين","delete","360");
INSERT INTO permissions VALUES ("37","debtors.view","web","2026-10-25 18:12:05","2026-10-25 18:12:05","عرض مدينين آخرين","basic","البيانات الأساسية","مدينين آخرين","view","370");
INSERT INTO permissions VALUES ("38","debtors.add","web","2026-10-25 18:12:05","2026-10-25 18:12:05","إضافة مدينين آخرين","basic","البيانات الأساسية","مدينين آخرين","add","380");
INSERT INTO permissions VALUES ("39","debtors.edit","web","2026-10-25 18:12:05","2026-10-25 18:12:05","تعديل مدينين آخرين","basic","البيانات الأساسية","مدينين آخرين","edit","390");
INSERT INTO permissions VALUES ("40","debtors.delete","web","2026-10-25 18:12:05","2026-10-25 18:12:05","حذف مدينين آخرين","basic","البيانات الأساسية","مدينين آخرين","delete","400");
INSERT INTO permissions VALUES ("41","partners.view","web","2026-10-25 18:12:05","2026-10-25 18:12:05","عرض الشركاء","basic","البيانات الأساسية","الشركاء","view","410");
INSERT INTO permissions VALUES ("42","partners.add","web","2026-10-25 18:12:05","2026-10-25 18:12:05","إضافة الشركاء","basic","البيانات الأساسية","الشركاء","add","420");
INSERT INTO permissions VALUES ("43","partners.edit","web","2026-10-25 18:12:05","2026-10-25 18:12:05","تعديل الشركاء","basic","البيانات الأساسية","الشركاء","edit","430");
INSERT INTO permissions VALUES ("44","partners.delete","web","2026-10-25 18:12:05","2026-10-25 18:12:05","حذف الشركاء","basic","البيانات الأساسية","الشركاء","delete","440");
INSERT INTO permissions VALUES ("45","assets.view","web","2026-10-25 18:12:05","2026-10-25 18:12:05","عرض الأصول","basic","البيانات الأساسية","الأصول","view","450");
INSERT INTO permissions VALUES ("46","assets.add","web","2026-10-25 18:12:05","2026-10-25 18:12:05","إضافة الأصول","basic","البيانات الأساسية","الأصول","add","460");
INSERT INTO permissions VALUES ("47","assets.edit","web","2026-10-25 18:12:05","2026-10-25 18:12:05","تعديل الأصول","basic","البيانات الأساسية","الأصول","edit","470");
INSERT INTO permissions VALUES ("48","assets.delete","web","2026-10-25 18:12:05","2026-10-25 18:12:05","حذف الأصول","basic","البيانات الأساسية","الأصول","delete","480");
INSERT INTO permissions VALUES ("49","rentables.view","web","2026-10-25 18:12:05","2026-10-25 18:12:05","عرض الأصول القابلة للتأجير","basic","البيانات الأساسية","الأصول القابلة للتأجير","view","490");
INSERT INTO permissions VALUES ("50","rentables.add","web","2026-10-25 18:12:05","2026-10-25 18:12:05","إضافة الأصول القابلة للتأجير","basic","البيانات الأساسية","الأصول القابلة للتأجير","add","500");
INSERT INTO permissions VALUES ("51","rentables.edit","web","2026-10-25 18:12:05","2026-10-25 18:12:05","تعديل الأصول القابلة للتأجير","basic","البيانات الأساسية","الأصول القابلة للتأجير","edit","510");
INSERT INTO permissions VALUES ("52","rentables.delete","web","2026-10-25 18:12:05","2026-10-25 18:12:05","حذف الأصول القابلة للتأجير","basic","البيانات الأساسية","الأصول القابلة للتأجير","delete","520");
INSERT INTO permissions VALUES ("53","delivery.view","web","2026-10-25 18:12:05","2026-10-25 18:12:05","عرض الديليفري","basic","البيانات الأساسية","الديليفري","view","530");
INSERT INTO permissions VALUES ("54","delivery.add","web","2026-10-25 18:12:05","2026-10-25 18:12:05","إضافة الديليفري","basic","البيانات الأساسية","الديليفري","add","540");
INSERT INTO permissions VALUES ("55","delivery.edit","web","2026-10-25 18:12:05","2026-10-25 18:12:05","تعديل الديليفري","basic","البيانات الأساسية","الديليفري","edit","550");
INSERT INTO permissions VALUES ("56","delivery.delete","web","2026-10-25 18:12:05","2026-10-25 18:12:05","حذف الديليفري","basic","البيانات الأساسية","الديليفري","delete","560");
INSERT INTO permissions VALUES ("57","towns.view","web","2026-10-25 18:12:05","2026-10-25 18:12:05","عرض المدن والمناطق","basic","البيانات الأساسية","المدن والمناطق","view","570");
INSERT INTO permissions VALUES ("58","towns.add","web","2026-10-25 18:12:05","2026-10-25 18:12:05","إضافة المدن والمناطق","basic","البيانات الأساسية","المدن والمناطق","add","580");
INSERT INTO permissions VALUES ("59","towns.edit","web","2026-10-25 18:12:05","2026-10-25 18:12:05","تعديل المدن والمناطق","basic","البيانات الأساسية","المدن والمناطق","edit","590");
INSERT INTO permissions VALUES ("60","towns.delete","web","2026-10-25 18:12:05","2026-10-25 18:12:05","حذف المدن والمناطق","basic","البيانات الأساسية","المدن والمناطق","delete","600");
INSERT INTO permissions VALUES ("61","items.view","web","2026-10-25 18:12:05","2026-10-25 18:12:05","عرض الأصناف","inventory","المخزون","الأصناف","view","610");
INSERT INTO permissions VALUES ("62","items.add","web","2026-10-25 18:12:05","2026-10-25 18:12:05","إضافة الأصناف","inventory","المخزون","الأصناف","add","620");
INSERT INTO permissions VALUES ("63","items.edit","web","2026-10-25 18:12:05","2026-10-25 18:12:05","تعديل الأصناف","inventory","المخزون","الأصناف","edit","630");
INSERT INTO permissions VALUES ("64","items.delete","web","2026-10-25 18:12:05","2026-10-25 18:12:05","حذف الأصناف","inventory","المخزون","الأصناف","delete","640");
INSERT INTO permissions VALUES ("65","item_groups.view","web","2026-10-25 18:12:05","2026-10-25 18:12:05","عرض مجموعات وتصنيفات الأصناف","inventory","المخزون","مجموعات وتصنيفات الأصناف","view","650");
INSERT INTO permissions VALUES ("66","item_groups.add","web","2026-10-25 18:12:05","2026-10-25 18:12:05","إضافة مجموعات وتصنيفات الأصناف","inventory","المخزون","مجموعات وتصنيفات الأصناف","add","660");
INSERT INTO permissions VALUES ("67","item_groups.edit","web","2026-10-25 18:12:05","2026-10-25 18:12:05","تعديل مجموعات وتصنيفات الأصناف","inventory","المخزون","مجموعات وتصنيفات الأصناف","edit","670");
INSERT INTO permissions VALUES ("68","item_groups.delete","web","2026-10-25 18:12:05","2026-10-25 18:12:05","حذف مجموعات وتصنيفات الأصناف","inventory","المخزون","مجموعات وتصنيفات الأصناف","delete","680");
INSERT INTO permissions VALUES ("69","units.view","web","2026-10-25 18:12:05","2026-10-25 18:12:05","عرض الوحدات","inventory","المخزون","الوحدات","view","690");
INSERT INTO permissions VALUES ("70","units.add","web","2026-10-25 18:12:05","2026-10-25 18:12:05","إضافة الوحدات","inventory","المخزون","الوحدات","add","700");
INSERT INTO permissions VALUES ("71","units.edit","web","2026-10-25 18:12:05","2026-10-25 18:12:05","تعديل الوحدات","inventory","المخزون","الوحدات","edit","710");
INSERT INTO permissions VALUES ("72","units.delete","web","2026-10-25 18:12:05","2026-10-25 18:12:05","حذف الوحدات","inventory","المخزون","الوحدات","delete","720");
INSERT INTO permissions VALUES ("73","items.pricing","web","2026-10-25 18:12:05","2026-10-25 18:12:05","التسعير والباركود وإعادة احتساب التكلفة","inventory","المخزون","التسعير والباركود وإعادة احتساب التكلفة",NULL,"730");
INSERT INTO permissions VALUES ("74","items.opening_balance","web","2026-10-25 18:12:05","2026-10-25 18:12:05","أرصدة أول المدة للأصناف","inventory","المخزون","أرصدة أول المدة للأصناف",NULL,"740");
INSERT INTO permissions VALUES ("75","inventory.audit","web","2026-10-25 18:12:05","2026-10-25 18:12:05","جرد المخزن","inventory","المخزون","جرد المخزن",NULL,"750");
INSERT INTO permissions VALUES ("76","stock.sell_negative","web","2026-10-25 18:12:05","2026-10-25 18:12:05","البيع بأكثر من رصيد المخزن","inventory","المخزون","البيع بأكثر من رصيد المخزن",NULL,"760");
INSERT INTO permissions VALUES ("77","sales.view","web","2026-10-25 18:12:05","2026-10-25 18:12:05","عرض فواتير المبيعات","sales","المبيعات","فواتير المبيعات","view","770");
INSERT INTO permissions VALUES ("78","sales.add","web","2026-10-25 18:12:05","2026-10-25 18:12:05","إضافة فواتير المبيعات","sales","المبيعات","فواتير المبيعات","add","780");
INSERT INTO permissions VALUES ("79","sales.edit","web","2026-10-25 18:12:05","2026-10-25 18:12:05","تعديل فواتير المبيعات","sales","المبيعات","فواتير المبيعات","edit","790");
INSERT INTO permissions VALUES ("80","sales.delete","web","2026-10-25 18:12:05","2026-10-25 18:12:05","حذف فواتير المبيعات","sales","المبيعات","فواتير المبيعات","delete","800");
INSERT INTO permissions VALUES ("81","sales_returns.view","web","2026-10-25 18:12:05","2026-10-25 18:12:05","عرض مردود المبيعات","sales","المبيعات","مردود المبيعات","view","810");
INSERT INTO permissions VALUES ("82","sales_returns.add","web","2026-10-25 18:12:05","2026-10-25 18:12:05","إضافة مردود المبيعات","sales","المبيعات","مردود المبيعات","add","820");
INSERT INTO permissions VALUES ("83","sales_returns.edit","web","2026-10-25 18:12:05","2026-10-25 18:12:05","تعديل مردود المبيعات","sales","المبيعات","مردود المبيعات","edit","830");
INSERT INTO permissions VALUES ("84","sales_returns.delete","web","2026-10-25 18:12:05","2026-10-25 18:12:05","حذف مردود المبيعات","sales","المبيعات","مردود المبيعات","delete","840");
INSERT INTO permissions VALUES ("85","sales_orders.view","web","2026-10-25 18:12:05","2026-10-25 18:12:05","عرض أوامر البيع","sales","المبيعات","أوامر البيع","view","850");
INSERT INTO permissions VALUES ("86","sales_orders.add","web","2026-10-25 18:12:05","2026-10-25 18:12:05","إضافة أوامر البيع","sales","المبيعات","أوامر البيع","add","860");
INSERT INTO permissions VALUES ("87","sales_orders.edit","web","2026-10-25 18:12:05","2026-10-25 18:12:05","تعديل أوامر البيع","sales","المبيعات","أوامر البيع","edit","870");
INSERT INTO permissions VALUES ("88","sales_orders.delete","web","2026-10-25 18:12:05","2026-10-25 18:12:05","حذف أوامر البيع","sales","المبيعات","أوامر البيع","delete","880");
INSERT INTO permissions VALUES ("89","quotations.view","web","2026-10-25 18:12:05","2026-10-25 18:12:05","عرض عروض الأسعار","sales","المبيعات","عروض الأسعار","view","890");
INSERT INTO permissions VALUES ("90","quotations.add","web","2026-10-25 18:12:05","2026-10-25 18:12:05","إضافة عروض الأسعار","sales","المبيعات","عروض الأسعار","add","900");
INSERT INTO permissions VALUES ("91","quotations.edit","web","2026-10-25 18:12:05","2026-10-25 18:12:05","تعديل عروض الأسعار","sales","المبيعات","عروض الأسعار","edit","910");
INSERT INTO permissions VALUES ("92","quotations.delete","web","2026-10-25 18:12:05","2026-10-25 18:12:05","حذف عروض الأسعار","sales","المبيعات","عروض الأسعار","delete","920");
INSERT INTO permissions VALUES ("93","pos.use","web","2026-10-25 18:12:05","2026-10-25 18:12:05","استخدام نقطة البيع","pos","نقاط البيع","استخدام نقطة البيع",NULL,"930");
INSERT INTO permissions VALUES ("94","pos.cancel_order","web","2026-10-25 18:12:05","2026-10-25 18:12:05","إلغاء طلب أو حذف صنف من طلب","pos","نقاط البيع","إلغاء طلب أو حذف صنف من طلب",NULL,"940");
INSERT INTO permissions VALUES ("95","pos.delivery","web","2026-10-25 18:12:05","2026-10-25 18:12:05","طلبات الديليفري من الكاشير","pos","نقاط البيع","طلبات الديليفري من الكاشير",NULL,"950");
INSERT INTO permissions VALUES ("96","tables.view","web","2026-10-25 18:12:05","2026-10-25 18:12:05","عرض الطاولات","pos","نقاط البيع","الطاولات","view","960");
INSERT INTO permissions VALUES ("97","tables.add","web","2026-10-25 18:12:05","2026-10-25 18:12:05","إضافة الطاولات","pos","نقاط البيع","الطاولات","add","970");
INSERT INTO permissions VALUES ("98","tables.edit","web","2026-10-25 18:12:05","2026-10-25 18:12:05","تعديل الطاولات","pos","نقاط البيع","الطاولات","edit","980");
INSERT INTO permissions VALUES ("99","tables.delete","web","2026-10-25 18:12:05","2026-10-25 18:12:05","حذف الطاولات","pos","نقاط البيع","الطاولات","delete","990");
INSERT INTO permissions VALUES ("100","pos.closed_sessions","web","2026-10-25 18:12:05","2026-10-25 18:12:05","الجلسات المقفولة","pos","نقاط البيع","الجلسات المقفولة",NULL,"1000");
INSERT INTO permissions VALUES ("101","pos.close_shift","web","2026-10-25 18:12:05","2026-10-25 18:12:05","تقفيل الشيفت","pos","نقاط البيع","تقفيل الشيفت",NULL,"1010");
INSERT INTO permissions VALUES ("102","pos.view_shift_expected","web","2026-10-25 18:12:05","2026-10-25 18:12:05","عرض المتوقع في تقفيل الشيفت (حتى مع الجرد الأعمى)","pos","نقاط البيع","عرض المتوقع في تقفيل الشيفت (حتى مع الجرد الأعمى)",NULL,"1020");
INSERT INTO permissions VALUES ("103","visits.view","web","2026-10-25 18:12:05","2026-10-25 18:12:05","عرض الزيارات","visits","الزيارات","الزيارات","view","1030");
INSERT INTO permissions VALUES ("104","visits.add","web","2026-10-25 18:12:05","2026-10-25 18:12:05","إضافة الزيارات","visits","الزيارات","الزيارات","add","1040");
INSERT INTO permissions VALUES ("105","visits.edit","web","2026-10-25 18:12:05","2026-10-25 18:12:05","تعديل الزيارات","visits","الزيارات","الزيارات","edit","1050");
INSERT INTO permissions VALUES ("106","visits.delete","web","2026-10-25 18:12:05","2026-10-25 18:12:05","حذف الزيارات","visits","الزيارات","الزيارات","delete","1060");
INSERT INTO permissions VALUES ("107","visit_types.view","web","2026-10-25 18:12:05","2026-10-25 18:12:05","عرض أنواع الزيارات","visits","الزيارات","أنواع الزيارات","view","1070");
INSERT INTO permissions VALUES ("108","visit_types.add","web","2026-10-25 18:12:05","2026-10-25 18:12:05","إضافة أنواع الزيارات","visits","الزيارات","أنواع الزيارات","add","1080");
INSERT INTO permissions VALUES ("109","visit_types.edit","web","2026-10-25 18:12:05","2026-10-25 18:12:05","تعديل أنواع الزيارات","visits","الزيارات","أنواع الزيارات","edit","1090");
INSERT INTO permissions VALUES ("110","visit_types.delete","web","2026-10-25 18:12:05","2026-10-25 18:12:05","حذف أنواع الزيارات","visits","الزيارات","أنواع الزيارات","delete","1100");
INSERT INTO permissions VALUES ("111","cards.view","web","2026-10-25 18:12:05","2026-10-25 18:12:05","عرض الكروت","cards","إدارة الكروت","الكروت","view","1110");
INSERT INTO permissions VALUES ("112","cards.add","web","2026-10-25 18:12:05","2026-10-25 18:12:05","إضافة الكروت","cards","إدارة الكروت","الكروت","add","1120");
INSERT INTO permissions VALUES ("113","cards.edit","web","2026-10-25 18:12:05","2026-10-25 18:12:05","تعديل الكروت","cards","إدارة الكروت","الكروت","edit","1130");
INSERT INTO permissions VALUES ("114","cards.delete","web","2026-10-25 18:12:05","2026-10-25 18:12:05","حذف الكروت","cards","إدارة الكروت","الكروت","delete","1140");
INSERT INTO permissions VALUES ("115","cards.pass","web","2026-10-25 18:12:05","2026-10-25 18:12:05","تمرير كارت","cards","إدارة الكروت","تمرير كارت",NULL,"1150");
INSERT INTO permissions VALUES ("116","purchases.view","web","2026-10-25 18:12:05","2026-10-25 18:12:05","عرض فواتير المشتريات","purchases","المشتريات","فواتير المشتريات","view","1160");
INSERT INTO permissions VALUES ("117","purchases.add","web","2026-10-25 18:12:05","2026-10-25 18:12:05","إضافة فواتير المشتريات","purchases","المشتريات","فواتير المشتريات","add","1170");
INSERT INTO permissions VALUES ("118","purchases.edit","web","2026-10-25 18:12:05","2026-10-25 18:12:05","تعديل فواتير المشتريات","purchases","المشتريات","فواتير المشتريات","edit","1180");
INSERT INTO permissions VALUES ("119","purchases.delete","web","2026-10-25 18:12:05","2026-10-25 18:12:05","حذف فواتير المشتريات","purchases","المشتريات","فواتير المشتريات","delete","1190");
INSERT INTO permissions VALUES ("120","purchase_returns.view","web","2026-10-25 18:12:05","2026-10-25 18:12:05","عرض مردود المشتريات","purchases","المشتريات","مردود المشتريات","view","1200");
INSERT INTO permissions VALUES ("121","purchase_returns.add","web","2026-10-25 18:12:05","2026-10-25 18:12:05","إضافة مردود المشتريات","purchases","المشتريات","مردود المشتريات","add","1210");
INSERT INTO permissions VALUES ("122","purchase_returns.edit","web","2026-10-25 18:12:05","2026-10-25 18:12:05","تعديل مردود المشتريات","purchases","المشتريات","مردود المشتريات","edit","1220");
INSERT INTO permissions VALUES ("123","purchase_returns.delete","web","2026-10-25 18:12:05","2026-10-25 18:12:05","حذف مردود المشتريات","purchases","المشتريات","مردود المشتريات","delete","1230");
INSERT INTO permissions VALUES ("124","purchase_orders.view","web","2026-10-25 18:12:05","2026-10-25 18:12:05","عرض أوامر الشراء","purchases","المشتريات","أوامر الشراء","view","1240");
INSERT INTO permissions VALUES ("125","purchase_orders.add","web","2026-10-25 18:12:05","2026-10-25 18:12:05","إضافة أوامر الشراء","purchases","المشتريات","أوامر الشراء","add","1250");
INSERT INTO permissions VALUES ("126","purchase_orders.edit","web","2026-10-25 18:12:05","2026-10-25 18:12:05","تعديل أوامر الشراء","purchases","المشتريات","أوامر الشراء","edit","1260");
INSERT INTO permissions VALUES ("127","purchase_orders.delete","web","2026-10-25 18:12:05","2026-10-25 18:12:05","حذف أوامر الشراء","purchases","المشتريات","أوامر الشراء","delete","1270");
INSERT INTO permissions VALUES ("128","receipts.view","web","2026-10-25 18:12:05","2026-10-25 18:12:05","عرض سندات القبض","vouchers","السندات","سندات القبض","view","1280");
INSERT INTO permissions VALUES ("129","receipts.add","web","2026-10-25 18:12:05","2026-10-25 18:12:05","إضافة سندات القبض","vouchers","السندات","سندات القبض","add","1290");
INSERT INTO permissions VALUES ("130","receipts.edit","web","2026-10-25 18:12:05","2026-10-25 18:12:05","تعديل سندات القبض","vouchers","السندات","سندات القبض","edit","1300");
INSERT INTO permissions VALUES ("131","receipts.delete","web","2026-10-25 18:12:05","2026-10-25 18:12:05","حذف سندات القبض","vouchers","السندات","سندات القبض","delete","1310");
INSERT INTO permissions VALUES ("132","payments.view","web","2026-10-25 18:12:05","2026-10-25 18:12:05","عرض سندات الصرف","vouchers","السندات","سندات الصرف","view","1320");
INSERT INTO permissions VALUES ("133","payments.add","web","2026-10-25 18:12:05","2026-10-25 18:12:05","إضافة سندات الصرف","vouchers","السندات","سندات الصرف","add","1330");
INSERT INTO permissions VALUES ("134","payments.edit","web","2026-10-25 18:12:05","2026-10-25 18:12:05","تعديل سندات الصرف","vouchers","السندات","سندات الصرف","edit","1340");
INSERT INTO permissions VALUES ("135","payments.delete","web","2026-10-25 18:12:05","2026-10-25 18:12:05","حذف سندات الصرف","vouchers","السندات","سندات الصرف","delete","1350");
INSERT INTO permissions VALUES ("136","journals.view","web","2026-10-25 18:12:05","2026-10-25 18:12:05","عرض قيود اليومية","accounting","الحسابات العامة","قيود اليومية","view","1360");
INSERT INTO permissions VALUES ("137","journals.add","web","2026-10-25 18:12:05","2026-10-25 18:12:05","إضافة قيود اليومية","accounting","الحسابات العامة","قيود اليومية","add","1370");
INSERT INTO permissions VALUES ("138","journals.edit","web","2026-10-25 18:12:05","2026-10-25 18:12:05","تعديل قيود اليومية","accounting","الحسابات العامة","قيود اليومية","edit","1380");
INSERT INTO permissions VALUES ("139","journals.delete","web","2026-10-25 18:12:05","2026-10-25 18:12:05","حذف قيود اليومية","accounting","الحسابات العامة","قيود اليومية","delete","1390");
INSERT INTO permissions VALUES ("140","accounts.chart","web","2026-10-25 18:12:05","2026-10-25 18:12:05","دليل الحسابات وقائمة الحسابات","accounting","الحسابات العامة","دليل الحسابات وقائمة الحسابات",NULL,"1400");
INSERT INTO permissions VALUES ("141","accounts.opening_balance","web","2026-10-25 18:12:05","2026-10-25 18:12:05","أرصدة أول المدة للحسابات","accounting","الحسابات العامة","أرصدة أول المدة للحسابات",NULL,"1410");
INSERT INTO permissions VALUES ("142","assets.operations","web","2026-10-25 18:12:05","2026-10-25 18:12:05","شراء / بيع / إهلاك أصل","assets_ops","عمليات الأصول","شراء / بيع / إهلاك أصل",NULL,"1420");
INSERT INTO permissions VALUES ("143","rent_contracts.view","web","2026-10-25 18:12:05","2026-10-25 18:12:05","عرض عقود الإيجار","rents","التأجير","عقود الإيجار","view","1430");
INSERT INTO permissions VALUES ("144","rent_contracts.add","web","2026-10-25 18:12:05","2026-10-25 18:12:05","إضافة عقود الإيجار","rents","التأجير","عقود الإيجار","add","1440");
INSERT INTO permissions VALUES ("145","rent_contracts.edit","web","2026-10-25 18:12:05","2026-10-25 18:12:05","تعديل عقود الإيجار","rents","التأجير","عقود الإيجار","edit","1450");
INSERT INTO permissions VALUES ("146","rent_contracts.delete","web","2026-10-25 18:12:05","2026-10-25 18:12:05","حذف عقود الإيجار","rents","التأجير","عقود الإيجار","delete","1460");
INSERT INTO permissions VALUES ("147","rent_installments.collect","web","2026-10-25 18:12:05","2026-10-25 18:12:05","تحصيل الأقساط","rents","التأجير","تحصيل الأقساط",NULL,"1470");
INSERT INTO permissions VALUES ("148","reservations.view","web","2026-10-25 18:12:05","2026-10-25 18:12:05","عرض الحجوزات","clinics","العيادات","الحجوزات","view","1480");
INSERT INTO permissions VALUES ("149","reservations.add","web","2026-10-25 18:12:05","2026-10-25 18:12:05","إضافة الحجوزات","clinics","العيادات","الحجوزات","add","1490");
INSERT INTO permissions VALUES ("150","reservations.edit","web","2026-10-25 18:12:05","2026-10-25 18:12:05","تعديل الحجوزات","clinics","العيادات","الحجوزات","edit","1500");
INSERT INTO permissions VALUES ("151","reservations.delete","web","2026-10-25 18:12:05","2026-10-25 18:12:05","حذف الحجوزات","clinics","العيادات","الحجوزات","delete","1510");
INSERT INTO permissions VALUES ("152","reservations.view_ended","web","2026-10-25 18:12:05","2026-10-25 18:12:05","عرض الحجوزات المنتهية","clinics","العيادات","عرض الحجوزات المنتهية",NULL,"1520");
INSERT INTO permissions VALUES ("153","reservations.view_total","web","2026-10-25 18:12:05","2026-10-25 18:12:05","عرض إجمالي الحجوزات","clinics","العيادات","عرض إجمالي الحجوزات",NULL,"1530");
INSERT INTO permissions VALUES ("154","patients.view","web","2026-10-25 18:12:05","2026-10-25 18:12:05","عرض بيانات المرضى والروشتات","clinics","العيادات","بيانات المرضى والروشتات","view","1540");
INSERT INTO permissions VALUES ("155","patients.add","web","2026-10-25 18:12:05","2026-10-25 18:12:05","إضافة بيانات المرضى والروشتات","clinics","العيادات","بيانات المرضى والروشتات","add","1550");
INSERT INTO permissions VALUES ("156","patients.edit","web","2026-10-25 18:12:05","2026-10-25 18:12:05","تعديل بيانات المرضى والروشتات","clinics","العيادات","بيانات المرضى والروشتات","edit","1560");
INSERT INTO permissions VALUES ("157","patients.delete","web","2026-10-25 18:12:05","2026-10-25 18:12:05","حذف بيانات المرضى والروشتات","clinics","العيادات","بيانات المرضى والروشتات","delete","1570");
INSERT INTO permissions VALUES ("158","drugs.view","web","2026-10-25 18:12:05","2026-10-25 18:12:05","عرض الأدوية","clinics","العيادات","الأدوية","view","1580");
INSERT INTO permissions VALUES ("159","drugs.add","web","2026-10-25 18:12:05","2026-10-25 18:12:05","إضافة الأدوية","clinics","العيادات","الأدوية","add","1590");
INSERT INTO permissions VALUES ("160","drugs.edit","web","2026-10-25 18:12:05","2026-10-25 18:12:05","تعديل الأدوية","clinics","العيادات","الأدوية","edit","1600");
INSERT INTO permissions VALUES ("161","drugs.delete","web","2026-10-25 18:12:05","2026-10-25 18:12:05","حذف الأدوية","clinics","العيادات","الأدوية","delete","1610");
INSERT INTO permissions VALUES ("162","employees.view","web","2026-10-25 18:12:05","2026-10-25 18:12:05","عرض الموظفين","hr","الموارد البشرية","الموظفين","view","1620");
INSERT INTO permissions VALUES ("163","employees.add","web","2026-10-25 18:12:05","2026-10-25 18:12:05","إضافة الموظفين","hr","الموارد البشرية","الموظفين","add","1630");
INSERT INTO permissions VALUES ("164","employees.edit","web","2026-10-25 18:12:05","2026-10-25 18:12:05","تعديل الموظفين","hr","الموارد البشرية","الموظفين","edit","1640");
INSERT INTO permissions VALUES ("165","employees.delete","web","2026-10-25 18:12:05","2026-10-25 18:12:05","حذف الموظفين","hr","الموارد البشرية","الموظفين","delete","1650");
INSERT INTO permissions VALUES ("166","jobs.view","web","2026-10-25 18:12:05","2026-10-25 18:12:05","عرض الوظائف","hr","الموارد البشرية","الوظائف","view","1660");
INSERT INTO permissions VALUES ("167","jobs.add","web","2026-10-25 18:12:05","2026-10-25 18:12:05","إضافة الوظائف","hr","الموارد البشرية","الوظائف","add","1670");
INSERT INTO permissions VALUES ("168","jobs.edit","web","2026-10-25 18:12:05","2026-10-25 18:12:05","تعديل الوظائف","hr","الموارد البشرية","الوظائف","edit","1680");
INSERT INTO permissions VALUES ("169","jobs.delete","web","2026-10-25 18:12:05","2026-10-25 18:12:05","حذف الوظائف","hr","الموارد البشرية","الوظائف","delete","1690");
INSERT INTO permissions VALUES ("170","job_levels.view","web","2026-10-25 18:12:05","2026-10-25 18:12:05","عرض المستويات الوظيفية","hr","الموارد البشرية","المستويات الوظيفية","view","1700");
INSERT INTO permissions VALUES ("171","job_levels.add","web","2026-10-25 18:12:05","2026-10-25 18:12:05","إضافة المستويات الوظيفية","hr","الموارد البشرية","المستويات الوظيفية","add","1710");
INSERT INTO permissions VALUES ("172","job_levels.edit","web","2026-10-25 18:12:05","2026-10-25 18:12:05","تعديل المستويات الوظيفية","hr","الموارد البشرية","المستويات الوظيفية","edit","1720");
INSERT INTO permissions VALUES ("173","job_levels.delete","web","2026-10-25 18:12:05","2026-10-25 18:12:05","حذف المستويات الوظيفية","hr","الموارد البشرية","المستويات الوظيفية","delete","1730");
INSERT INTO permissions VALUES ("174","job_rules.view","web","2026-10-25 18:12:05","2026-10-25 18:12:05","عرض قواعد الوظائف","hr","الموارد البشرية","قواعد الوظائف","view","1740");
INSERT INTO permissions VALUES ("175","job_rules.add","web","2026-10-25 18:12:05","2026-10-25 18:12:05","إضافة قواعد الوظائف","hr","الموارد البشرية","قواعد الوظائف","add","1750");
INSERT INTO permissions VALUES ("176","job_rules.edit","web","2026-10-25 18:12:05","2026-10-25 18:12:05","تعديل قواعد الوظائف","hr","الموارد البشرية","قواعد الوظائف","edit","1760");
INSERT INTO permissions VALUES ("177","job_rules.delete","web","2026-10-25 18:12:05","2026-10-25 18:12:05","حذف قواعد الوظائف","hr","الموارد البشرية","قواعد الوظائف","delete","1770");
INSERT INTO permissions VALUES ("178","departments.view","web","2026-10-25 18:12:05","2026-10-25 18:12:05","عرض الأقسام","hr","الموارد البشرية","الأقسام","view","1780");
INSERT INTO permissions VALUES ("179","departments.add","web","2026-10-25 18:12:05","2026-10-25 18:12:05","إضافة الأقسام","hr","الموارد البشرية","الأقسام","add","1790");
INSERT INTO permissions VALUES ("180","departments.edit","web","2026-10-25 18:12:05","2026-10-25 18:12:05","تعديل الأقسام","hr","الموارد البشرية","الأقسام","edit","1800");
INSERT INTO permissions VALUES ("181","departments.delete","web","2026-10-25 18:12:05","2026-10-25 18:12:05","حذف الأقسام","hr","الموارد البشرية","الأقسام","delete","1810");
INSERT INTO permissions VALUES ("182","shifts.view","web","2026-10-25 18:12:05","2026-10-25 18:12:05","عرض الورديات","hr","الموارد البشرية","الورديات","view","1820");
INSERT INTO permissions VALUES ("183","shifts.add","web","2026-10-25 18:12:05","2026-10-25 18:12:05","إضافة الورديات","hr","الموارد البشرية","الورديات","add","1830");
INSERT INTO permissions VALUES ("184","shifts.edit","web","2026-10-25 18:12:05","2026-10-25 18:12:05","تعديل الورديات","hr","الموارد البشرية","الورديات","edit","1840");
INSERT INTO permissions VALUES ("185","shifts.delete","web","2026-10-25 18:12:05","2026-10-25 18:12:05","حذف الورديات","hr","الموارد البشرية","الورديات","delete","1850");
INSERT INTO permissions VALUES ("186","hr_operations.view","web","2026-10-25 18:12:05","2026-10-25 18:12:05","عرض تعريف العمليات وخطواتها","hr","الموارد البشرية","تعريف العمليات وخطواتها","view","1860");
INSERT INTO permissions VALUES ("187","hr_operations.add","web","2026-10-25 18:12:05","2026-10-25 18:12:05","إضافة تعريف العمليات وخطواتها","hr","الموارد البشرية","تعريف العمليات وخطواتها","add","1870");
INSERT INTO permissions VALUES ("188","hr_operations.edit","web","2026-10-25 18:12:05","2026-10-25 18:12:05","تعديل تعريف العمليات وخطواتها","hr","الموارد البشرية","تعريف العمليات وخطواتها","edit","1880");
INSERT INTO permissions VALUES ("189","hr_operations.delete","web","2026-10-25 18:12:05","2026-10-25 18:12:05","حذف تعريف العمليات وخطواتها","hr","الموارد البشرية","تعريف العمليات وخطواتها","delete","1890");
INSERT INTO permissions VALUES ("190","employee_operations.view","web","2026-10-25 18:12:05","2026-10-25 18:12:05","عرض عمليات الموظفين","hr","الموارد البشرية","عمليات الموظفين","view","1900");
INSERT INTO permissions VALUES ("191","employee_operations.add","web","2026-10-25 18:12:05","2026-10-25 18:12:05","إضافة عمليات الموظفين","hr","الموارد البشرية","عمليات الموظفين","add","1910");
INSERT INTO permissions VALUES ("192","employee_operations.edit","web","2026-10-25 18:12:05","2026-10-25 18:12:05","تعديل عمليات الموظفين","hr","الموارد البشرية","عمليات الموظفين","edit","1920");
INSERT INTO permissions VALUES ("193","employee_operations.delete","web","2026-10-25 18:12:05","2026-10-25 18:12:05","حذف عمليات الموظفين","hr","الموارد البشرية","عمليات الموظفين","delete","1930");
INSERT INTO permissions VALUES ("194","pulse.view","web","2026-10-25 18:12:05","2026-10-25 18:12:05","عرض تقييمات Pulse","hr","الموارد البشرية","تقييمات Pulse","view","1940");
INSERT INTO permissions VALUES ("195","pulse.add","web","2026-10-25 18:12:05","2026-10-25 18:12:05","إضافة تقييمات Pulse","hr","الموارد البشرية","تقييمات Pulse","add","1950");
INSERT INTO permissions VALUES ("196","pulse.edit","web","2026-10-25 18:12:05","2026-10-25 18:12:05","تعديل تقييمات Pulse","hr","الموارد البشرية","تقييمات Pulse","edit","1960");
INSERT INTO permissions VALUES ("197","pulse.delete","web","2026-10-25 18:12:05","2026-10-25 18:12:05","حذف تقييمات Pulse","hr","الموارد البشرية","تقييمات Pulse","delete","1970");
INSERT INTO permissions VALUES ("198","pulse_types.view","web","2026-10-25 18:12:05","2026-10-25 18:12:05","عرض أنواع التقييم","hr","الموارد البشرية","أنواع التقييم","view","1980");
INSERT INTO permissions VALUES ("199","pulse_types.add","web","2026-10-25 18:12:05","2026-10-25 18:12:05","إضافة أنواع التقييم","hr","الموارد البشرية","أنواع التقييم","add","1990");
INSERT INTO permissions VALUES ("200","pulse_types.edit","web","2026-10-25 18:12:05","2026-10-25 18:12:05","تعديل أنواع التقييم","hr","الموارد البشرية","أنواع التقييم","edit","2000");
INSERT INTO permissions VALUES ("201","pulse_types.delete","web","2026-10-25 18:12:05","2026-10-25 18:12:05","حذف أنواع التقييم","hr","الموارد البشرية","أنواع التقييم","delete","2010");
INSERT INTO permissions VALUES ("202","tasks.view","web","2026-10-25 18:12:05","2026-10-25 18:12:05","عرض المهام","tasks","المهام والأداء والتعاقدات","المهام","view","2020");
INSERT INTO permissions VALUES ("203","tasks.add","web","2026-10-25 18:12:05","2026-10-25 18:12:05","إضافة المهام","tasks","المهام والأداء والتعاقدات","المهام","add","2030");
INSERT INTO permissions VALUES ("204","tasks.edit","web","2026-10-25 18:12:05","2026-10-25 18:12:05","تعديل المهام","tasks","المهام والأداء والتعاقدات","المهام","edit","2040");
INSERT INTO permissions VALUES ("205","tasks.delete","web","2026-10-25 18:12:05","2026-10-25 18:12:05","حذف المهام","tasks","المهام والأداء والتعاقدات","المهام","delete","2050");
INSERT INTO permissions VALUES ("206","tasks.view_all","web","2026-10-25 18:12:05","2026-10-25 18:12:05","عرض مهام كل الموظفين","tasks","المهام والأداء والتعاقدات","عرض مهام كل الموظفين",NULL,"2060");
INSERT INTO permissions VALUES ("207","kpis.view","web","2026-10-25 18:12:05","2026-10-25 18:12:05","عرض معدلات الأداء","tasks","المهام والأداء والتعاقدات","معدلات الأداء","view","2070");
INSERT INTO permissions VALUES ("208","kpis.add","web","2026-10-25 18:12:05","2026-10-25 18:12:05","إضافة معدلات الأداء","tasks","المهام والأداء والتعاقدات","معدلات الأداء","add","2080");
INSERT INTO permissions VALUES ("209","kpis.edit","web","2026-10-25 18:12:05","2026-10-25 18:12:05","تعديل معدلات الأداء","tasks","المهام والأداء والتعاقدات","معدلات الأداء","edit","2090");
INSERT INTO permissions VALUES ("210","kpis.delete","web","2026-10-25 18:12:05","2026-10-25 18:12:05","حذف معدلات الأداء","tasks","المهام والأداء والتعاقدات","معدلات الأداء","delete","2100");
INSERT INTO permissions VALUES ("211","employee_kpis.view","web","2026-10-25 18:12:05","2026-10-25 18:12:05","عرض أوزان الأداء للموظفين","tasks","المهام والأداء والتعاقدات","أوزان الأداء للموظفين","view","2110");
INSERT INTO permissions VALUES ("212","employee_kpis.add","web","2026-10-25 18:12:05","2026-10-25 18:12:05","إضافة أوزان الأداء للموظفين","tasks","المهام والأداء والتعاقدات","أوزان الأداء للموظفين","add","2120");
INSERT INTO permissions VALUES ("213","employee_kpis.edit","web","2026-10-25 18:12:05","2026-10-25 18:12:05","تعديل أوزان الأداء للموظفين","tasks","المهام والأداء والتعاقدات","أوزان الأداء للموظفين","edit","2130");
INSERT INTO permissions VALUES ("214","employee_kpis.delete","web","2026-10-25 18:12:05","2026-10-25 18:12:05","حذف أوزان الأداء للموظفين","tasks","المهام والأداء والتعاقدات","أوزان الأداء للموظفين","delete","2140");
INSERT INTO permissions VALUES ("215","contracts.view","web","2026-10-25 18:12:05","2026-10-25 18:12:05","عرض عقود التدريب والتعيين والتعهيد","tasks","المهام والأداء والتعاقدات","عقود التدريب والتعيين والتعهيد","view","2150");
INSERT INTO permissions VALUES ("216","contracts.add","web","2026-10-25 18:12:05","2026-10-25 18:12:05","إضافة عقود التدريب والتعيين والتعهيد","tasks","المهام والأداء والتعاقدات","عقود التدريب والتعيين والتعهيد","add","2160");
INSERT INTO permissions VALUES ("217","contracts.edit","web","2026-10-25 18:12:05","2026-10-25 18:12:05","تعديل عقود التدريب والتعيين والتعهيد","tasks","المهام والأداء والتعاقدات","عقود التدريب والتعيين والتعهيد","edit","2170");
INSERT INTO permissions VALUES ("218","contracts.delete","web","2026-10-25 18:12:05","2026-10-25 18:12:05","حذف عقود التدريب والتعيين والتعهيد","tasks","المهام والأداء والتعاقدات","عقود التدريب والتعيين والتعهيد","delete","2180");
INSERT INTO permissions VALUES ("219","cvs.view","web","2026-10-25 18:12:05","2026-10-25 18:12:05","عرض السير الذاتية","tasks","المهام والأداء والتعاقدات","السير الذاتية","view","2190");
INSERT INTO permissions VALUES ("220","cvs.add","web","2026-10-25 18:12:05","2026-10-25 18:12:05","إضافة السير الذاتية","tasks","المهام والأداء والتعاقدات","السير الذاتية","add","2200");
INSERT INTO permissions VALUES ("221","cvs.edit","web","2026-10-25 18:12:05","2026-10-25 18:12:05","تعديل السير الذاتية","tasks","المهام والأداء والتعاقدات","السير الذاتية","edit","2210");
INSERT INTO permissions VALUES ("222","cvs.delete","web","2026-10-25 18:12:05","2026-10-25 18:12:05","حذف السير الذاتية","tasks","المهام والأداء والتعاقدات","السير الذاتية","delete","2220");
INSERT INTO permissions VALUES ("223","requests.view","web","2026-10-25 18:12:05","2026-10-25 18:12:05","عرض الطلبات (إجازات وغيرها)","tasks","المهام والأداء والتعاقدات","الطلبات (إجازات وغيرها)","view","2230");
INSERT INTO permissions VALUES ("224","requests.add","web","2026-10-25 18:12:05","2026-10-25 18:12:05","إضافة الطلبات (إجازات وغيرها)","tasks","المهام والأداء والتعاقدات","الطلبات (إجازات وغيرها)","add","2240");
INSERT INTO permissions VALUES ("225","requests.edit","web","2026-10-25 18:12:05","2026-10-25 18:12:05","تعديل الطلبات (إجازات وغيرها)","tasks","المهام والأداء والتعاقدات","الطلبات (إجازات وغيرها)","edit","2250");
INSERT INTO permissions VALUES ("226","requests.delete","web","2026-10-25 18:12:05","2026-10-25 18:12:05","حذف الطلبات (إجازات وغيرها)","tasks","المهام والأداء والتعاقدات","الطلبات (إجازات وغيرها)","delete","2260");
INSERT INTO permissions VALUES ("227","attendance.view","web","2026-10-25 18:12:05","2026-10-25 18:12:05","عرض سجل الحضور والبصمات والأذونات","payroll","الحضور والمرتبات","سجل الحضور والبصمات والأذونات","view","2270");
INSERT INTO permissions VALUES ("228","attendance.add","web","2026-10-25 18:12:05","2026-10-25 18:12:05","إضافة سجل الحضور والبصمات والأذونات","payroll","الحضور والمرتبات","سجل الحضور والبصمات والأذونات","add","2280");
INSERT INTO permissions VALUES ("229","attendance.edit","web","2026-10-25 18:12:05","2026-10-25 18:12:05","تعديل سجل الحضور والبصمات والأذونات","payroll","الحضور والمرتبات","سجل الحضور والبصمات والأذونات","edit","2290");
INSERT INTO permissions VALUES ("230","attendance.delete","web","2026-10-25 18:12:05","2026-10-25 18:12:05","حذف سجل الحضور والبصمات والأذونات","payroll","الحضور والمرتبات","سجل الحضور والبصمات والأذونات","delete","2300");
INSERT INTO permissions VALUES ("231","allowances.view","web","2026-10-25 18:12:05","2026-10-25 18:12:05","عرض البدلات والاستقطاعات","payroll","الحضور والمرتبات","البدلات والاستقطاعات","view","2310");
INSERT INTO permissions VALUES ("232","allowances.add","web","2026-10-25 18:12:05","2026-10-25 18:12:05","إضافة البدلات والاستقطاعات","payroll","الحضور والمرتبات","البدلات والاستقطاعات","add","2320");
INSERT INTO permissions VALUES ("233","allowances.edit","web","2026-10-25 18:12:05","2026-10-25 18:12:05","تعديل البدلات والاستقطاعات","payroll","الحضور والمرتبات","البدلات والاستقطاعات","edit","2330");
INSERT INTO permissions VALUES ("234","allowances.delete","web","2026-10-25 18:12:05","2026-10-25 18:12:05","حذف البدلات والاستقطاعات","payroll","الحضور والمرتبات","البدلات والاستقطاعات","delete","2340");
INSERT INTO permissions VALUES ("235","attendance.process","web","2026-10-25 18:12:05","2026-10-25 18:12:05","معالجة الحضور","payroll","الحضور والمرتبات","معالجة الحضور",NULL,"2350");
INSERT INTO permissions VALUES ("236","production.view","web","2026-10-25 18:12:05","2026-10-25 18:12:05","عرض الإنتاجية اليومية","payroll","الحضور والمرتبات","الإنتاجية اليومية","view","2360");
INSERT INTO permissions VALUES ("237","production.add","web","2026-10-25 18:12:05","2026-10-25 18:12:05","إضافة الإنتاجية اليومية","payroll","الحضور والمرتبات","الإنتاجية اليومية","add","2370");
INSERT INTO permissions VALUES ("238","production.edit","web","2026-10-25 18:12:05","2026-10-25 18:12:05","تعديل الإنتاجية اليومية","payroll","الحضور والمرتبات","الإنتاجية اليومية","edit","2380");
INSERT INTO permissions VALUES ("239","production.delete","web","2026-10-25 18:12:05","2026-10-25 18:12:05","حذف الإنتاجية اليومية","payroll","الحضور والمرتبات","الإنتاجية اليومية","delete","2390");
INSERT INTO permissions VALUES ("240","payroll.view","web","2026-10-25 18:12:05","2026-10-25 18:12:05","عرض احتسابات الرواتب","payroll","الحضور والمرتبات","احتسابات الرواتب","view","2400");
INSERT INTO permissions VALUES ("241","payroll.add","web","2026-10-25 18:12:05","2026-10-25 18:12:05","إضافة احتسابات الرواتب","payroll","الحضور والمرتبات","احتسابات الرواتب","add","2410");
INSERT INTO permissions VALUES ("242","payroll.edit","web","2026-10-25 18:12:05","2026-10-25 18:12:05","تعديل احتسابات الرواتب","payroll","الحضور والمرتبات","احتسابات الرواتب","edit","2420");
INSERT INTO permissions VALUES ("243","payroll.delete","web","2026-10-25 18:12:05","2026-10-25 18:12:05","حذف احتسابات الرواتب","payroll","الحضور والمرتبات","احتسابات الرواتب","delete","2430");
INSERT INTO permissions VALUES ("244","crm_clients.view","web","2026-10-25 18:12:05","2026-10-25 18:12:05","عرض العملاء (متقدم)","crm","إدارة علاقات العملاء","العملاء (متقدم)","view","2440");
INSERT INTO permissions VALUES ("245","crm_clients.add","web","2026-10-25 18:12:05","2026-10-25 18:12:05","إضافة العملاء (متقدم)","crm","إدارة علاقات العملاء","العملاء (متقدم)","add","2450");
INSERT INTO permissions VALUES ("246","crm_clients.edit","web","2026-10-25 18:12:05","2026-10-25 18:12:05","تعديل العملاء (متقدم)","crm","إدارة علاقات العملاء","العملاء (متقدم)","edit","2460");
INSERT INTO permissions VALUES ("247","crm_clients.delete","web","2026-10-25 18:12:05","2026-10-25 18:12:05","حذف العملاء (متقدم)","crm","إدارة علاقات العملاء","العملاء (متقدم)","delete","2470");
INSERT INTO permissions VALUES ("248","client_profile.view","web","2026-10-25 18:12:05","2026-10-25 18:12:05","عرض بروفايل العميل ومستنداته","crm","إدارة علاقات العملاء","بروفايل العميل ومستنداته","view","2480");
INSERT INTO permissions VALUES ("249","client_profile.add","web","2026-10-25 18:12:05","2026-10-25 18:12:05","إضافة بروفايل العميل ومستنداته","crm","إدارة علاقات العملاء","بروفايل العميل ومستنداته","add","2490");
INSERT INTO permissions VALUES ("250","client_profile.edit","web","2026-10-25 18:12:05","2026-10-25 18:12:05","تعديل بروفايل العميل ومستنداته","crm","إدارة علاقات العملاء","بروفايل العميل ومستنداته","edit","2500");
INSERT INTO permissions VALUES ("251","client_profile.delete","web","2026-10-25 18:12:05","2026-10-25 18:12:05","حذف بروفايل العميل ومستنداته","crm","إدارة علاقات العملاء","بروفايل العميل ومستنداته","delete","2510");
INSERT INTO permissions VALUES ("252","chances.view","web","2026-10-25 18:12:05","2026-10-25 18:12:05","عرض الفرص","crm","إدارة علاقات العملاء","الفرص","view","2520");
INSERT INTO permissions VALUES ("253","chances.add","web","2026-10-25 18:12:05","2026-10-25 18:12:05","إضافة الفرص","crm","إدارة علاقات العملاء","الفرص","add","2530");
INSERT INTO permissions VALUES ("254","chances.edit","web","2026-10-25 18:12:05","2026-10-25 18:12:05","تعديل الفرص","crm","إدارة علاقات العملاء","الفرص","edit","2540");
INSERT INTO permissions VALUES ("255","chances.delete","web","2026-10-25 18:12:05","2026-10-25 18:12:05","حذف الفرص","crm","إدارة علاقات العملاء","الفرص","delete","2550");
INSERT INTO permissions VALUES ("256","calls.view","web","2026-10-25 18:12:05","2026-10-25 18:12:05","عرض المكالمات","crm","إدارة علاقات العملاء","المكالمات","view","2560");
INSERT INTO permissions VALUES ("257","calls.add","web","2026-10-25 18:12:05","2026-10-25 18:12:05","إضافة المكالمات","crm","إدارة علاقات العملاء","المكالمات","add","2570");
INSERT INTO permissions VALUES ("258","calls.edit","web","2026-10-25 18:12:05","2026-10-25 18:12:05","تعديل المكالمات","crm","إدارة علاقات العملاء","المكالمات","edit","2580");
INSERT INTO permissions VALUES ("259","calls.delete","web","2026-10-25 18:12:05","2026-10-25 18:12:05","حذف المكالمات","crm","إدارة علاقات العملاء","المكالمات","delete","2590");
INSERT INTO permissions VALUES ("260","reports.sales","web","2026-10-25 18:12:05","2026-10-25 18:12:05","تقارير المبيعات","reports","التقارير","تقارير المبيعات",NULL,"2600");
INSERT INTO permissions VALUES ("261","reports.shift","web","2026-10-25 18:12:05","2026-10-25 18:12:05","تقارير الشيفت","reports","التقارير","تقارير الشيفت",NULL,"2610");
INSERT INTO permissions VALUES ("262","reports.purchases","web","2026-10-25 18:12:05","2026-10-25 18:12:05","تقارير المشتريات","reports","التقارير","تقارير المشتريات",NULL,"2620");
INSERT INTO permissions VALUES ("263","reports.accounting","web","2026-10-25 18:12:05","2026-10-25 18:12:05","كشوف الحسابات والقوائم المالية","reports","التقارير","كشوف الحسابات والقوائم المالية",NULL,"2630");
INSERT INTO permissions VALUES ("264","reports.rents","web","2026-10-25 18:12:05","2026-10-25 18:12:05","تقارير التأجير","reports","التقارير","تقارير التأجير",NULL,"2640");
INSERT INTO permissions VALUES ("265","reports.clinics","web","2026-10-25 18:12:05","2026-10-25 18:12:05","تقارير العيادات","reports","التقارير","تقارير العيادات",NULL,"2650");
INSERT INTO permissions VALUES ("266","reports.visits","web","2026-10-25 18:12:05","2026-10-25 18:12:05","إحصائيات الزيارات","reports","التقارير","إحصائيات الزيارات",NULL,"2660");
INSERT INTO permissions VALUES ("267","reports.payroll","web","2026-10-25 18:12:05","2026-10-25 18:12:05","تقارير المرتبات والحضور","reports","التقارير","تقارير المرتبات والحضور",NULL,"2670");
INSERT INTO permissions VALUES ("268","reports.hr","web","2026-10-25 18:12:05","2026-10-25 18:12:05","تقرير الموظفين","reports","التقارير","تقرير الموظفين",NULL,"2680");
INSERT INTO permissions VALUES ("269","users.view","web","2026-10-25 18:12:05","2026-10-25 18:12:05","عرض المستخدمين","users","المستخدمين والأدوار","المستخدمين","view","2690");
INSERT INTO permissions VALUES ("270","users.add","web","2026-10-25 18:12:05","2026-10-25 18:12:05","إضافة المستخدمين","users","المستخدمين والأدوار","المستخدمين","add","2700");
INSERT INTO permissions VALUES ("271","users.edit","web","2026-10-25 18:12:05","2026-10-25 18:12:05","تعديل المستخدمين","users","المستخدمين والأدوار","المستخدمين","edit","2710");
INSERT INTO permissions VALUES ("272","users.delete","web","2026-10-25 18:12:05","2026-10-25 18:12:05","حذف المستخدمين","users","المستخدمين والأدوار","المستخدمين","delete","2720");
INSERT INTO permissions VALUES ("273","users.change_password","web","2026-10-25 18:12:05","2026-10-25 18:12:05","تغيير كلمات مرور المستخدمين","users","المستخدمين والأدوار","تغيير كلمات مرور المستخدمين",NULL,"2730");
INSERT INTO permissions VALUES ("274","users.waiter_codes","web","2026-10-25 18:12:05","2026-10-25 18:12:05","باركود الويترز","users","المستخدمين والأدوار","باركود الويترز",NULL,"2740");
INSERT INTO permissions VALUES ("275","roles.view","web","2026-10-25 18:12:05","2026-10-25 18:12:05","عرض الأدوار والصلاحيات","users","المستخدمين والأدوار","الأدوار والصلاحيات","view","2750");
INSERT INTO permissions VALUES ("276","roles.add","web","2026-10-25 18:12:05","2026-10-25 18:12:05","إضافة الأدوار والصلاحيات","users","المستخدمين والأدوار","الأدوار والصلاحيات","add","2760");
INSERT INTO permissions VALUES ("277","roles.edit","web","2026-10-25 18:12:05","2026-10-25 18:12:05","تعديل الأدوار والصلاحيات","users","المستخدمين والأدوار","الأدوار والصلاحيات","edit","2770");
INSERT INTO permissions VALUES ("278","roles.delete","web","2026-10-25 18:12:05","2026-10-25 18:12:05","حذف الأدوار والصلاحيات","users","المستخدمين والأدوار","الأدوار والصلاحيات","delete","2780");
INSERT INTO permissions VALUES ("279","settings.manage","web","2026-10-25 18:12:05","2026-10-25 18:12:05","الإعدادات العامة","system","الإعدادات والنظام","الإعدادات العامة",NULL,"2790");
INSERT INTO permissions VALUES ("280","system.manage","web","2026-10-25 18:12:05","2026-10-25 18:12:05","النظام وقاعدة البيانات والترخيص","system","الإعدادات والنظام","النظام وقاعدة البيانات والترخيص",NULL,"2800");
INSERT INTO permissions VALUES ("281","news.view","web","2026-10-25 18:12:05","2026-10-25 18:12:05","عرض الأخبار والإعلانات","system","الإعدادات والنظام","الأخبار والإعلانات","view","2810");
INSERT INTO permissions VALUES ("282","news.add","web","2026-10-25 18:12:05","2026-10-25 18:12:05","إضافة الأخبار والإعلانات","system","الإعدادات والنظام","الأخبار والإعلانات","add","2820");
INSERT INTO permissions VALUES ("283","news.edit","web","2026-10-25 18:12:05","2026-10-25 18:12:05","تعديل الأخبار والإعلانات","system","الإعدادات والنظام","الأخبار والإعلانات","edit","2830");
INSERT INTO permissions VALUES ("284","news.delete","web","2026-10-25 18:12:05","2026-10-25 18:12:05","حذف الأخبار والإعلانات","system","الإعدادات والنظام","الأخبار والإعلانات","delete","2840");
INSERT INTO permissions VALUES ("285","defaults.change","web","2026-10-25 18:12:06","2026-10-25 18:12:06","تغيير الافتراضيات في الفواتير والكاشير والسندات","users","المستخدمين والأدوار","تغيير الافتراضيات في الفواتير والكاشير والسندات",NULL,"2750");
INSERT INTO permissions VALUES ("286","store_transfers.view","web","2026-10-25 18:12:06","2026-10-25 18:12:06","عرض التحويلات المخزنية","inventory","المخزون","تحويل مخزني","view","752");
INSERT INTO permissions VALUES ("287","store_transfers.add","web","2026-10-25 18:12:06","2026-10-25 18:12:06","إضافة التحويلات المخزنية","inventory","المخزون","تحويل مخزني","add","753");
INSERT INTO permissions VALUES ("288","store_transfers.edit","web","2026-10-25 18:12:06","2026-10-25 18:12:06","تعديل التحويلات المخزنية","inventory","المخزون","تحويل مخزني","edit","754");
INSERT INTO permissions VALUES ("289","store_transfers.delete","web","2026-10-25 18:12:06","2026-10-25 18:12:06","حذف التحويلات المخزنية","inventory","المخزون","تحويل مخزني","delete","755");
INSERT INTO permissions VALUES ("290","barcode_docs.view","web","2026-11-02 21:46:51","2026-11-02 21:46:51","عرض مستندات طباعة الباركود","purchases","المشتريات","مستند طباعة باركود","view","1280");
INSERT INTO permissions VALUES ("291","barcode_docs.add","web","2026-11-02 21:46:51","2026-11-02 21:46:51","إضافة مستندات طباعة الباركود","purchases","المشتريات","مستند طباعة باركود","add","1290");
INSERT INTO permissions VALUES ("292","barcode_docs.edit","web","2026-11-02 21:46:51","2026-11-02 21:46:51","تعديل مستندات طباعة الباركود","purchases","المشتريات","مستند طباعة باركود","edit","1300");
INSERT INTO permissions VALUES ("293","barcode_docs.delete","web","2026-11-02 21:46:51","2026-11-02 21:46:51","حذف مستندات طباعة الباركود","purchases","المشتريات","مستند طباعة باركود","delete","1310");
INSERT INTO permissions VALUES ("294","items.auto_invoice","web","2026-11-02 21:46:51","2026-11-02 21:46:51","صنف جديد تلقائي في الفاتورة","inventory","المخزون","الأصناف",NULL,"625");



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
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO pro_tybes VALUES ("1","سند قبض",NULL,"1",NULL,"2024-03-14 04:01:35","2024-03-14 04:01:35","0","0","0");
INSERT INTO pro_tybes VALUES ("2","سند دفع",NULL,"2",NULL,"2024-03-14 04:01:35","2024-03-14 04:02:22","0","0","0");
INSERT INTO pro_tybes VALUES ("3","فاتورة مبيعات",NULL,"3",NULL,"2024-03-14 04:01:58","2024-03-14 04:02:26","0","0","0");
INSERT INTO pro_tybes VALUES ("4","فاتورة مشتريات",NULL,"4",NULL,"2024-03-14 04:01:58","2024-03-14 04:02:28","0","0","0");
INSERT INTO pro_tybes VALUES ("5","استحقاق قسط",NULL,"5",NULL,"2024-03-17 05:53:16","2024-03-17 05:53:27","0","0","0");
INSERT INTO pro_tybes VALUES ("6","خصم مكتسب",NULL,"6",NULL,"2024-03-17 05:53:16","2024-03-17 05:53:27","0","0","0");
INSERT INTO pro_tybes VALUES ("7","خصم مسموح به",NULL,"7",NULL,"2024-03-17 05:53:16","2024-03-17 05:53:27","0","0","0");
INSERT INTO pro_tybes VALUES ("8","قيد يومية",NULL,"8",NULL,"2024-05-14 14:06:41","2024-05-14 14:06:54","0","0","0");
INSERT INTO pro_tybes VALUES ("9","كاشير",NULL,"9",NULL,"2024-05-14 14:06:41","2026-10-25 18:12:06","0","0","0");
INSERT INTO pro_tybes VALUES ("10","فاتورة مردود مشتريات",NULL,"10",NULL,"2024-05-14 14:06:41","2026-10-25 18:12:06","0","0","0");
INSERT INTO pro_tybes VALUES ("11","فاتورة مردود مبيعات",NULL,"11",NULL,"2024-05-14 14:06:41","2026-10-25 18:12:06","0","0","0");
INSERT INTO pro_tybes VALUES ("12","أمر شراء",NULL,"12",NULL,"2024-05-14 14:06:41","2024-11-21 17:25:12","0","0","0");
INSERT INTO pro_tybes VALUES ("13","أمر بيع",NULL,"13",NULL,"2024-05-14 14:06:41","2024-11-21 17:25:16","0","0","0");
INSERT INTO pro_tybes VALUES ("14","عرض سعر",NULL,"14",NULL,"2024-05-14 14:06:41","2026-10-25 18:12:04","0","0","0");
INSERT INTO pro_tybes VALUES ("15","رصيد افتتاحي حسابات",NULL,"15",NULL,"2024-05-14 14:06:41","2024-11-23 13:40:52","0","0","0");
INSERT INTO pro_tybes VALUES ("18","تحويل مخزني","","18",NULL,"2026-10-25 18:12:06","2026-10-25 18:12:06","0","0","0");
INSERT INTO pro_tybes VALUES ("19","مستند طباعة باركود","","19",NULL,"2026-11-02 21:46:51","2026-11-02 21:46:51","0","0","0");



DROP TABLE IF EXISTS process;

CREATE TABLE `process` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(255) NOT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO process VALUES ("1","add unit: ق","2026-10-26 18:26:00");
INSERT INTO process VALUES ("2","add unit: كرتونة","2026-10-26 18:26:06");
INSERT INTO process VALUES ("3","add item","2026-10-26 18:26:30");
INSERT INTO process VALUES ("4","add item","2026-10-26 18:26:35");
INSERT INTO process VALUES ("5","add sales","2026-10-26 18:26:47");
INSERT INTO process VALUES ("6","add account >> insta","2026-10-31 20:20:29");
INSERT INTO process VALUES ("7","add account >> بنك 2","2026-10-31 20:20:48");
INSERT INTO process VALUES ("8","logout >> admin","2026-11-01 17:43:08");
INSERT INTO process VALUES ("9","add buy","2026-11-02 21:06:34");
INSERT INTO process VALUES ("10","add item","2026-11-02 21:07:42");
INSERT INTO process VALUES ("11","add item","2026-11-02 21:07:44");
INSERT INTO process VALUES ("12","add item","2026-11-02 21:07:46");
INSERT INTO process VALUES ("13","add item","2026-11-02 21:07:49");
INSERT INTO process VALUES ("14","add item","2026-11-02 21:07:51");
INSERT INTO process VALUES ("15","add item","2026-11-02 21:07:53");
INSERT INTO process VALUES ("16","add buy","2026-11-02 21:09:13");
INSERT INTO process VALUES ("17","add account >> Maxwell Stone","2026-11-02 21:11:02");
INSERT INTO process VALUES ("18","add account >> Imelda Estrada","2026-11-02 21:11:03");
INSERT INTO process VALUES ("19","add account >> Yvonne Waters","2026-11-02 21:11:06");
INSERT INTO process VALUES ("20","add account >> Cecilia Santos","2026-11-02 21:11:08");
INSERT INTO process VALUES ("21","logout >> admin","2026-11-02 21:49:30");
INSERT INTO process VALUES ("22","add invoice","2026-11-02 21:51:24");
INSERT INTO process VALUES ("23","auto add item","2026-11-03 15:14:28");
INSERT INTO process VALUES ("24","auto add item","2026-11-03 15:14:37");
INSERT INTO process VALUES ("25","add buy","2026-11-03 15:15:48");
INSERT INTO process VALUES ("26","add buy","2026-11-03 15:16:57");



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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO pulse_logs VALUES ("1","138","10","negative","5","","1","2026-11-02 11:38:11");
INSERT INTO pulse_logs VALUES ("2","138","2","positive","5","","1","2026-11-02 11:38:15");
INSERT INTO pulse_logs VALUES ("3","138","1","positive","5","","1","2026-11-02 11:38:18");



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

INSERT INTO pulse_types VALUES ("1","الالتزام بالمواعيد","positive","fas fa-clock","3","0","2026-10-25 18:12:06");
INSERT INTO pulse_types VALUES ("2","جودة العمل","positive","fas fa-award","5","0","2026-10-25 18:12:06");
INSERT INTO pulse_types VALUES ("3","روح الفريق","positive","fas fa-users","4","0","2026-10-25 18:12:06");
INSERT INTO pulse_types VALUES ("4","المبادرة","positive","fas fa-lightbulb","5","0","2026-10-25 18:12:06");
INSERT INTO pulse_types VALUES ("5","خدمة العملاء","positive","fas fa-handshake","4","0","2026-10-25 18:12:06");
INSERT INTO pulse_types VALUES ("6","النظافة والترتيب","positive","fas fa-broom","2","0","2026-10-25 18:12:06");
INSERT INTO pulse_types VALUES ("7","التأخر","negative","fas fa-clock","-3","0","2026-10-25 18:12:06");
INSERT INTO pulse_types VALUES ("8","الإهمال","negative","fas fa-exclamation-triangle","-5","0","2026-10-25 18:12:06");
INSERT INTO pulse_types VALUES ("9","عدم التعاون","negative","fas fa-user-slash","-4","0","2026-10-25 18:12:06");
INSERT INTO pulse_types VALUES ("10","سوء التعامل","negative","fas fa-frown","-5","0","2026-10-25 18:12:06");



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
INSERT INTO role_has_permissions VALUES ("103","1");
INSERT INTO role_has_permissions VALUES ("103","28");
INSERT INTO role_has_permissions VALUES ("104","1");
INSERT INTO role_has_permissions VALUES ("104","28");
INSERT INTO role_has_permissions VALUES ("105","1");
INSERT INTO role_has_permissions VALUES ("105","28");
INSERT INTO role_has_permissions VALUES ("106","1");
INSERT INTO role_has_permissions VALUES ("106","28");
INSERT INTO role_has_permissions VALUES ("107","1");
INSERT INTO role_has_permissions VALUES ("107","28");
INSERT INTO role_has_permissions VALUES ("108","1");
INSERT INTO role_has_permissions VALUES ("108","28");
INSERT INTO role_has_permissions VALUES ("109","1");
INSERT INTO role_has_permissions VALUES ("109","28");
INSERT INTO role_has_permissions VALUES ("110","1");
INSERT INTO role_has_permissions VALUES ("110","28");
INSERT INTO role_has_permissions VALUES ("111","1");
INSERT INTO role_has_permissions VALUES ("111","28");
INSERT INTO role_has_permissions VALUES ("112","1");
INSERT INTO role_has_permissions VALUES ("112","28");
INSERT INTO role_has_permissions VALUES ("113","1");
INSERT INTO role_has_permissions VALUES ("113","28");
INSERT INTO role_has_permissions VALUES ("114","1");
INSERT INTO role_has_permissions VALUES ("114","28");
INSERT INTO role_has_permissions VALUES ("115","1");
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
INSERT INTO role_has_permissions VALUES ("143","1");
INSERT INTO role_has_permissions VALUES ("144","1");
INSERT INTO role_has_permissions VALUES ("145","1");
INSERT INTO role_has_permissions VALUES ("146","1");
INSERT INTO role_has_permissions VALUES ("147","1");
INSERT INTO role_has_permissions VALUES ("148","1");
INSERT INTO role_has_permissions VALUES ("149","1");
INSERT INTO role_has_permissions VALUES ("150","1");
INSERT INTO role_has_permissions VALUES ("151","1");
INSERT INTO role_has_permissions VALUES ("152","1");
INSERT INTO role_has_permissions VALUES ("153","1");
INSERT INTO role_has_permissions VALUES ("154","1");
INSERT INTO role_has_permissions VALUES ("155","1");
INSERT INTO role_has_permissions VALUES ("156","1");
INSERT INTO role_has_permissions VALUES ("157","1");
INSERT INTO role_has_permissions VALUES ("158","1");
INSERT INTO role_has_permissions VALUES ("159","1");
INSERT INTO role_has_permissions VALUES ("160","1");
INSERT INTO role_has_permissions VALUES ("161","1");
INSERT INTO role_has_permissions VALUES ("162","1");
INSERT INTO role_has_permissions VALUES ("163","1");
INSERT INTO role_has_permissions VALUES ("164","1");
INSERT INTO role_has_permissions VALUES ("165","1");
INSERT INTO role_has_permissions VALUES ("166","1");
INSERT INTO role_has_permissions VALUES ("167","1");
INSERT INTO role_has_permissions VALUES ("168","1");
INSERT INTO role_has_permissions VALUES ("169","1");
INSERT INTO role_has_permissions VALUES ("170","1");
INSERT INTO role_has_permissions VALUES ("171","1");
INSERT INTO role_has_permissions VALUES ("172","1");
INSERT INTO role_has_permissions VALUES ("173","1");
INSERT INTO role_has_permissions VALUES ("174","1");
INSERT INTO role_has_permissions VALUES ("175","1");
INSERT INTO role_has_permissions VALUES ("176","1");
INSERT INTO role_has_permissions VALUES ("177","1");
INSERT INTO role_has_permissions VALUES ("178","1");
INSERT INTO role_has_permissions VALUES ("179","1");
INSERT INTO role_has_permissions VALUES ("180","1");
INSERT INTO role_has_permissions VALUES ("181","1");
INSERT INTO role_has_permissions VALUES ("182","1");
INSERT INTO role_has_permissions VALUES ("183","1");
INSERT INTO role_has_permissions VALUES ("184","1");
INSERT INTO role_has_permissions VALUES ("185","1");
INSERT INTO role_has_permissions VALUES ("186","1");
INSERT INTO role_has_permissions VALUES ("187","1");
INSERT INTO role_has_permissions VALUES ("188","1");
INSERT INTO role_has_permissions VALUES ("189","1");
INSERT INTO role_has_permissions VALUES ("190","1");
INSERT INTO role_has_permissions VALUES ("191","1");
INSERT INTO role_has_permissions VALUES ("192","1");
INSERT INTO role_has_permissions VALUES ("193","1");
INSERT INTO role_has_permissions VALUES ("194","1");
INSERT INTO role_has_permissions VALUES ("195","1");
INSERT INTO role_has_permissions VALUES ("196","1");
INSERT INTO role_has_permissions VALUES ("197","1");
INSERT INTO role_has_permissions VALUES ("198","1");
INSERT INTO role_has_permissions VALUES ("199","1");
INSERT INTO role_has_permissions VALUES ("200","1");
INSERT INTO role_has_permissions VALUES ("201","1");
INSERT INTO role_has_permissions VALUES ("202","1");
INSERT INTO role_has_permissions VALUES ("203","1");
INSERT INTO role_has_permissions VALUES ("204","1");
INSERT INTO role_has_permissions VALUES ("205","1");
INSERT INTO role_has_permissions VALUES ("206","1");
INSERT INTO role_has_permissions VALUES ("207","1");
INSERT INTO role_has_permissions VALUES ("208","1");
INSERT INTO role_has_permissions VALUES ("209","1");
INSERT INTO role_has_permissions VALUES ("210","1");
INSERT INTO role_has_permissions VALUES ("211","1");
INSERT INTO role_has_permissions VALUES ("212","1");
INSERT INTO role_has_permissions VALUES ("213","1");
INSERT INTO role_has_permissions VALUES ("214","1");
INSERT INTO role_has_permissions VALUES ("215","1");
INSERT INTO role_has_permissions VALUES ("216","1");
INSERT INTO role_has_permissions VALUES ("217","1");
INSERT INTO role_has_permissions VALUES ("218","1");
INSERT INTO role_has_permissions VALUES ("219","1");
INSERT INTO role_has_permissions VALUES ("220","1");
INSERT INTO role_has_permissions VALUES ("221","1");
INSERT INTO role_has_permissions VALUES ("222","1");
INSERT INTO role_has_permissions VALUES ("223","1");
INSERT INTO role_has_permissions VALUES ("223","28");
INSERT INTO role_has_permissions VALUES ("224","1");
INSERT INTO role_has_permissions VALUES ("224","28");
INSERT INTO role_has_permissions VALUES ("225","1");
INSERT INTO role_has_permissions VALUES ("225","28");
INSERT INTO role_has_permissions VALUES ("226","1");
INSERT INTO role_has_permissions VALUES ("226","28");
INSERT INTO role_has_permissions VALUES ("227","1");
INSERT INTO role_has_permissions VALUES ("228","1");
INSERT INTO role_has_permissions VALUES ("229","1");
INSERT INTO role_has_permissions VALUES ("230","1");
INSERT INTO role_has_permissions VALUES ("231","1");
INSERT INTO role_has_permissions VALUES ("232","1");
INSERT INTO role_has_permissions VALUES ("233","1");
INSERT INTO role_has_permissions VALUES ("234","1");
INSERT INTO role_has_permissions VALUES ("235","1");
INSERT INTO role_has_permissions VALUES ("236","1");
INSERT INTO role_has_permissions VALUES ("237","1");
INSERT INTO role_has_permissions VALUES ("238","1");
INSERT INTO role_has_permissions VALUES ("239","1");
INSERT INTO role_has_permissions VALUES ("240","1");
INSERT INTO role_has_permissions VALUES ("241","1");
INSERT INTO role_has_permissions VALUES ("242","1");
INSERT INTO role_has_permissions VALUES ("243","1");
INSERT INTO role_has_permissions VALUES ("244","1");
INSERT INTO role_has_permissions VALUES ("245","1");
INSERT INTO role_has_permissions VALUES ("246","1");
INSERT INTO role_has_permissions VALUES ("247","1");
INSERT INTO role_has_permissions VALUES ("248","1");
INSERT INTO role_has_permissions VALUES ("249","1");
INSERT INTO role_has_permissions VALUES ("250","1");
INSERT INTO role_has_permissions VALUES ("251","1");
INSERT INTO role_has_permissions VALUES ("252","1");
INSERT INTO role_has_permissions VALUES ("253","1");
INSERT INTO role_has_permissions VALUES ("254","1");
INSERT INTO role_has_permissions VALUES ("255","1");
INSERT INTO role_has_permissions VALUES ("256","1");
INSERT INTO role_has_permissions VALUES ("257","1");
INSERT INTO role_has_permissions VALUES ("258","1");
INSERT INTO role_has_permissions VALUES ("259","1");
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

INSERT INTO roles VALUES ("1","admin","web","2026-10-25 18:12:05","2026-10-25 18:12:05","wwww","1");
INSERT INTO roles VALUES ("28","بائع كاشير","web","2026-10-25 18:12:05","2026-10-25 18:12:05","مبيعات كاشير فقط","1");



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

INSERT INTO schema_migrations VALUES ("006_add_jal_columns","2026-10-25 15:12:04","81c1502330bc36b10b6c356fd3d9dd60");
INSERT INTO schema_migrations VALUES ("007_add_jal_amount","2026-10-25 15:12:04","26d87d030b30b2e413412b8af76a739a");
INSERT INTO schema_migrations VALUES ("008_comprehensive_update","2026-10-25 15:12:04","565c1c7ee1dae869597c2b7040ae739c");
INSERT INTO schema_migrations VALUES ("009_add_calc_type_to_employees","2026-10-25 15:12:04","35162e8977c1a0bad3b78ff8619c8441");
INSERT INTO schema_migrations VALUES ("010_add_payroll_calcs","2026-10-25 15:12:04","3b85703594c9475e0492ece83b04fa5b");
INSERT INTO schema_migrations VALUES ("011_add_single_fp_rule_to_shifts","2026-10-25 15:12:04","170d9e52a77c45e0850c1508d0617822");
INSERT INTO schema_migrations VALUES ("012_add_commission_to_settings","2026-10-25 15:12:04","209fabf9c3a493622b2388e2e82bad67");
INSERT INTO schema_migrations VALUES ("013_disc_pct","2026-10-25 15:12:04","f9e7b6cb399894b01919c564ba816d27");
INSERT INTO schema_migrations VALUES ("014_shifts_ignore_minutes","2026-10-25 15:12:04","a090ed9342980dd96bcd57c6e1ec550c");
INSERT INTO schema_migrations VALUES ("015_tables_merge","2026-10-25 15:12:04","d32aaea6875bbee8a3d8ba1baacad434");
INSERT INTO schema_migrations VALUES ("016_visits_columns","2026-10-25 15:12:04","47f2696dc655be6ec95f2ca3912088b8");
INSERT INTO schema_migrations VALUES ("017_period_meta","2026-10-25 15:12:04","42661abdf8ce6976dc0675f8aea6cea6");
INSERT INTO schema_migrations VALUES ("018_delivery_person_ot_head","2026-10-25 15:12:04","16d04842d9079fcfbd9ef6186642bfea");
INSERT INTO schema_migrations VALUES ("019_backup_path","2026-10-25 15:12:04","d4b2a7e6b5c79ec18ded9a985819a314");
INSERT INTO schema_migrations VALUES ("020_fat_details_doc_qty","2026-10-25 15:12:04","0ce780f11148adbd301a54a87290c734");
INSERT INTO schema_migrations VALUES ("021_fix_pro_tybe_14_price_offer","2026-10-25 15:12:04","48f72b42e6758c752d4f5320a3415b7c");
INSERT INTO schema_migrations VALUES ("022_add_shift_grace_rules","2026-10-25 15:12:04","79f0a448270e76568f521bc88b14b585");
INSERT INTO schema_migrations VALUES ("023_seed_entitles","2026-10-25 15:12:04","b46634e6aab15320a7e81c02debacdc6");
INSERT INTO schema_migrations VALUES ("024_acc_head_cash_only","2026-10-25 15:12:04","7f8da38cde045884d82854ecab1fb5bc");
INSERT INTO schema_migrations VALUES ("025_payroll_by_id_advance_holiday_pay","2026-10-25 15:12:04","b284d6994877bc15842d2a02fe557e89");
INSERT INTO schema_migrations VALUES ("026_shifts_flexible_hours","2026-10-25 15:12:04","d20b5780ee130abe9f6b156a21b3e9eb");
INSERT INTO schema_migrations VALUES ("027_pos_shift_closing","2026-10-25 15:12:05","2ac0f20658fad4883748adc49ad3fa1e");
INSERT INTO schema_migrations VALUES ("028_usr_pwrs_legacy_columns","2026-10-25 15:12:05","0e8cbb00ef8964b3afe808e1339d31c0");
INSERT INTO schema_migrations VALUES ("029_rbac_tables","2026-10-25 15:12:05","fd9e2d2d4e562d903b0093f72d686519");
INSERT INTO schema_migrations VALUES ("030_rbac_copy_from_usr_pwrs","2026-10-25 15:12:06","321ffa1d4df73ec734d25c44bcb97126");
INSERT INTO schema_migrations VALUES ("031_admin_role_all_permissions","2026-10-25 15:12:06","1006e19570a78789726634e3803a2bd3");
INSERT INTO schema_migrations VALUES ("032_user_defaults","2026-10-25 15:12:06","8e334d2e25cee55e6a9342d0c57a80ec");
INSERT INTO schema_migrations VALUES ("033_settings_allow_all_accounts","2026-10-25 15:12:06","f9ad7afee1c6f165e7703cd8b65ba8a4");
INSERT INTO schema_migrations VALUES ("034_pulse_types","2026-10-25 15:12:06","3b94a27a4f53c032ddbc96cd59b8fe02");
INSERT INTO schema_migrations VALUES ("035_fix_pro_tybes_return_names","2026-10-25 15:12:06","960af6a9d0bb8e793043db271602c313");
INSERT INTO schema_migrations VALUES ("036_fix_pro_tybes_cashier_name","2026-10-25 15:12:06","e8f6b117db525a2f2dd0df441b36201d");
INSERT INTO schema_migrations VALUES ("037_store_transfers","2026-10-25 15:12:06","ee79ceb347c163b1b7803a9da1dcb51f");
INSERT INTO schema_migrations VALUES ("038_shift_groups_dynamic","2026-10-26 16:09:20","0894e75eb986346a15e0df8c16940764");
INSERT INTO schema_migrations VALUES ("039_barcode_print_doc","2026-11-02 21:46:51","189e45c84a7c4f18442c6360d0bbe57a");
INSERT INTO schema_migrations VALUES ("040_items_auto_invoice","2026-11-02 21:46:51","e633d9607639fde95d42722af121e198");



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
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `user` int(1) NOT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO session_time VALUES ("1","1","2026-11-01 17:43:11","0","0","0");
INSERT INTO session_time VALUES ("2","1","2026-11-02 21:49:33","0","0","0");



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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO settings VALUES ("1","FOCUS HOUSE","سمنود - برج زايد - الدور الخامس","abdelhadeeladawy@gmail.com","010053662038","125","d35c99e7485691ea14f829029dc03e69A67b8d2f92148f52cad46e331936922e8","","99","2024-01-01","2024-12-31","ar","solarized_white","1","1","1","1","1","1","1","155","27","131","21","0","0","0",NULL,NULL,"clothes","0","0","0","","0","0","1","1","13","78mm","❤ perfect place to grow","1","","{\"enabled\":true,\"company_name\":\"focus house\",\"align\":\"center\",\"invert\":false,\"paper_width\":38,\"paper_height\":25,\"margin_top\":0,\"margin_bottom\":0,\"margin_left\":0,\"margin_right\":3,\"elements\":{\"company\":{\"show\":true,\"h\":4,\"w\":15.1,\"top\":0,\"left\":19.4,\"font\":7},\"item_name\":{\"show\":true,\"h\":4,\"w\":26,\"top\":5,\"left\":7.1,\"font\":7},\"barcode_linear\":{\"show\":true,\"h\":9.3,\"w\":30,\"top\":12,\"left\":3.2,\"font\":0},\"barcode_text\":{\"show\":true,\"h\":3.5,\"w\":36,\"top\":21.6,\"left\":1,\"font\":6},\"code\":{\"show\":false,\"h\":3.5,\"w\":23.4,\"top\":23.5,\"left\":2.7,\"font\":8},\"price_before\":{\"show\":false,\"h\":3,\"w\":38,\"top\":19.7,\"left\":0,\"font\":13},\"price_after\":{\"show\":true,\"h\":3.5,\"w\":7.5,\"top\":0.5,\"left\":6.5,\"font\":5},\"price_retail\":{\"show\":false,\"h\":3.5,\"w\":38,\"top\":33.4,\"left\":0,\"font\":9},\"price_wholesale\":{\"show\":false,\"h\":3.5,\"w\":38,\"top\":37,\"left\":0,\"font\":9},\"line_1\":{\"show\":true,\"h\":0.1,\"w\":32,\"top\":9.7,\"left\":1,\"font\":0},\"line_2\":{\"show\":false,\"h\":0.5,\"w\":21,\"top\":26,\"left\":0,\"font\":0},\"text_1\":{\"show\":true,\"h\":4,\"w\":6.9,\"top\":0.1,\"left\":1.6,\"font\":7},\"text_2\":{\"show\":false,\"h\":3,\"w\":21,\"top\":31.5,\"left\":0,\"font\":8}},\"text_1_content\":\"price:\",\"text_2_content\":\"\",\"code_prefix\":\"\",\"code_suffix\":\"\",\"unit_barcode_add\":\"\",\"include_barcode\":true,\"include_item_code\":true,\"embed_prices\":\"none\",\"price_before_source\":\"price1\",\"price_after_source\":\"printed\",\"strike_before\":true}","{\"enabled\":true,\"paper_width\":\"78mm\",\"font_family\":\"Cairo\",\"font_size\":13,\"line_spacing\":1.25,\"margin_top\":2,\"margin_bottom\":4,\"margin_left\":2,\"margin_right\":2,\"auto_print\":true,\"auto_close\":true,\"show_borders\":true,\"header\":{\"show_logo\":true,\"logo_width\":90,\"logo_align\":\"center\",\"show_company_name\":true,\"company_name_size\":16,\"show_company_tel\":true,\"show_company_add\":true,\"show_tax_no\":false,\"tax_no\":\"\",\"show_cr_no\":false,\"cr_no\":\"\",\"header_text\":\"\",\"align\":\"center\"},\"meta\":{\"show_invoice_num\":true,\"invoice_num_style\":\"badge\",\"show_invoice_barcode\":true,\"barcode_height\":30,\"show_datetime\":true,\"show_cashier\":true,\"show_order_type\":true,\"show_driver\":true},\"customer\":{\"show\":true,\"show_name\":true,\"show_phone\":true,\"show_address\":true,\"show_balance\":true},\"items\":{\"show_index\":false,\"show_code\":false,\"show_name\":true,\"show_qty\":true,\"show_unit\":false,\"show_price\":true,\"show_disc\":false,\"show_total\":true,\"show_item_disc_subline\":true,\"border_style\":\"solid\",\"font_size\":13},\"totals\":{\"show_items_total\":true,\"show_items_disc\":true,\"show_subtotal\":true,\"show_inv_disc\":true,\"show_extra\":true,\"show_net\":true,\"show_paid\":true,\"show_change\":true,\"highlight_net\":true},\"footer\":{\"show_footer_text\":true,\"footer_text\":\"❤ perfect place to grow\",\"show_notes\":true,\"notes_text\":\"\",\"align\":\"center\"}}","","");



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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO shift_payments VALUES ("1","1","21","0.00","851.55","851.55","0.00","-851.55","2026-11-02 21:31:53");
INSERT INTO shift_payments VALUES ("2","1","274","0.00","0.00","0.00","0.00","0.00","2026-11-02 21:31:53");
INSERT INTO shift_payments VALUES ("3","1","39","0.00","283.85","283.85","0.00","-283.85","2026-11-02 21:31:53");
INSERT INTO shift_payments VALUES ("4","2","21","0.00","681.55","681.55","0.00","-681.55","2026-11-02 21:33:23");
INSERT INTO shift_payments VALUES ("5","2","274","0.00","0.00","0.00","0.00","0.00","2026-11-02 21:33:23");
INSERT INTO shift_payments VALUES ("6","2","39","0.00","1249.25","1249.25","0.00","-1249.25","2026-11-02 21:33:23");



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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




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

INSERT INTO users VALUES ("1","admin","$2y$10$CUC.H9Ev/X5CTYSlzYsl1ewxZdALXUgqLItuzKPdGWz5rV7ygcxda","2022-12-05 17:01:33","0","2","1","0","22947314.png","148","21","27",NULL,"131",NULL,"1",NULL,"0","0","36","1");



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

INSERT INTO usr_pwrs VALUES ("1","admin","1","1","1","1","1","1","0","0","0","0","0","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","0","0","0","0","0","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","wwww","1","2024-05-12 18:05:26","2026-10-25 18:12:05","0","0","1","1","1","1","1","1","1","1","1","1","0","0","1","1","1");
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




