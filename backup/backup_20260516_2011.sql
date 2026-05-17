DROP TABLE acc_groups;

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




DROP TABLE acc_head;

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
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  UNIQUE KEY `aname` (`aname`)
) ENGINE=InnoDB AUTO_INCREMENT=276 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO acc_head VALUES ("1","1","0","0","الاصول","","","","0","0","0","","0","1","1","1","0","0","0","0.000","0","2023-11-25 02:55:49","2024-11-16 22:16:48","","0","0","0");
INSERT INTO acc_head VALUES ("2","2","0","0","الخصوم","","","","0","0","0","","0","1","1","1","0","0","0","0.000","0","2023-11-26 04:10:59","2024-11-16 22:16:50","","0","0","0");
INSERT INTO acc_head VALUES ("3","22","0","0","حقوق الملكية","","","","0","0","0","","2","1","1","1","0","0","0","0.000","0","2023-11-26 04:14:04","2024-11-16 22:16:53","","0","0","0");
INSERT INTO acc_head VALUES ("4","3","0","0","الايرادات","","","","0","0","0","","0","1","2","1","0","0","0","0.000","0","2023-11-26 04:14:29","2024-06-11 05:16:29","","0","0","0");
INSERT INTO acc_head VALUES ("5","4","0","0","المصروفات","","","","0","0","0","","0","1","2","1","0","0","0","0.000","0","2023-11-26 04:14:59","2024-06-11 05:16:35","","0","0","0");
INSERT INTO acc_head VALUES ("6","11","0","0","الاصول الثابته","","","","0","0","0","","1","1","1","1","0","0","0","0.000","0","2023-11-26 04:38:23","2024-06-11 05:16:38","","0","0","0");
INSERT INTO acc_head VALUES ("7","12","0","0","الاصول المتداوله","","","","0","0","0","","1","","1","1","0","0","0","0.000","0","2023-11-26 04:45:08","2024-06-11 05:16:42","","0","0","0");
INSERT INTO acc_head VALUES ("8","21","0","0","الخصوم المتداولة","","","","0","0","0","","2","","1","1","0","0","0","0.000","0","2023-11-26 04:45:47","2024-06-11 05:16:46","","0","0","0");
INSERT INTO acc_head VALUES ("9","221","0","0","الشركاء","","","","0","0","0","","3","","1","1","0","0","0","0.000","0","2023-11-26 04:46:20","2024-06-11 05:16:52","","0","0","0");
INSERT INTO acc_head VALUES ("10","222","0","0","ارباح غير موزعة","","","","0","0","0","","3","","1","1","0","0","0","0.000","0","2023-11-26 04:47:03","2024-11-16 22:23:26","","0","0","0");
INSERT INTO acc_head VALUES ("11","223","0","1","ارباح غير موزعة لفترات سابقة","","","","0","0","0","","3","","1","0","0","0","0","0.000","0","2023-11-26 04:47:50","2025-10-28 11:51:19","","0","0","0");
INSERT INTO acc_head VALUES ("13","31","0","0","ايرادات المبيعات","","","","0","0","0","","4","","2","1","0","0","0","0.000","0","2023-11-26 21:37:49","2024-11-16 22:23:31","","0","0","0");
INSERT INTO acc_head VALUES ("14","32","0","0","ايرادات غير تشغيليه","","","","0","0","0","","4","","2","1","0","0","0","0.000","0","2023-11-26 21:38:15","2024-11-16 22:23:33","","0","0","0");
INSERT INTO acc_head VALUES ("15","41","0","0","تكاليف المبيعات","","","","0","0","0","0","5","","2","1","0","0","0","0.000","0","2023-11-26 21:39:10","2024-11-16 22:23:36","","0","0","0");
INSERT INTO acc_head VALUES ("16","42","0","0","تكلفه البضاعه المباعه","","","","0","0","0","","5","","2","1","0","0","0","0.000","0","2023-11-26 21:39:49","2024-06-11 05:17:12","","0","0","0");
INSERT INTO acc_head VALUES ("17","43","0","0","رواتب و اجور","","","","0","0","0","","5","","2","1","0","0","0","0.000","0","2023-11-26 21:40:07","2024-06-11 05:17:16","","0","0","0");
INSERT INTO acc_head VALUES ("18","121","0","0","الصناديق","","","","0","0","0","","7","","1","1","0","0","0","0.000","0","2023-12-08 12:50:49","2024-06-11 05:17:18","","0","0","0");
INSERT INTO acc_head VALUES ("19","122","0","0","العملاء","","","","0","0","0","","7","","1","1","0","0","0","0.000","0","2023-12-08 12:52:13","2024-06-11 05:17:22","","0","0","0");
INSERT INTO acc_head VALUES ("20","123","0","0","المخزون","","","","0","0","0","","7","","1","1","0","0","0","0.000","0","2023-12-08 12:52:51","2024-06-11 05:17:27","","0","0","0");
INSERT INTO acc_head VALUES ("21","1211","0","1","الصندوق الافتراضي","","","","0","0","1","","18","","1","0","0","0","0","0.000","0","2023-12-09 10:46:52","2026-05-09 20:23:20","","0","0","0");
INSERT INTO acc_head VALUES ("24","1221","0","0","العميل النقدي","","","","0","0","0","0","19","","1","0","0","0","0","0.000","0","2023-12-28 01:25:46","2026-05-09 20:20:48","","1","0","0");
INSERT INTO acc_head VALUES ("27","123001","0","0","المخزن الرئيسي","","","","0","1","0","0","20","","1","0","0","0","0","53800.000","0","2023-12-28 03:35:35","2026-05-13 16:26:17","","0","0","0");
INSERT INTO acc_head VALUES ("29","2211","0","0","الشريك الرئيسي","","","","0","0","0","","9","","1","0","0","0","0","0.000","0","2023-12-30 02:12:22","2025-10-28 11:51:21","","0","0","0");
INSERT INTO acc_head VALUES ("33","211","0","0","الموردين","","","","0","0","0","","8","","1","1","0","0","0","0.000","0","2024-01-22 05:41:26","2024-06-11 05:17:55","","0","0","0");
INSERT INTO acc_head VALUES ("34","212","0","1","الدائنين الاخرين","","","","0","0","0","","8","","1","1","0","0","0","0.000","0","2024-01-22 05:42:08","2026-05-09 20:20:40","","0","0","0");
INSERT INTO acc_head VALUES ("35","213","0","0","الموظفين","","","","0","0","0","","8","","1","1","0","0","0","0.000","0","2024-01-22 05:42:29","2024-06-11 05:18:01","","0","0","0");
INSERT INTO acc_head VALUES ("36","2111","0","0","المورد الافتراضي","","","","0","0","0","","33","","1","0","0","0","0","-53800.000","0","2024-01-23 06:17:26","2026-05-13 16:26:17","","0","0","0");
INSERT INTO acc_head VALUES ("37","124","0","0","البنوك","","","","0","0","0","","7","","1","1","0","0","0","0.000","0","2024-01-23 06:22:23","2024-06-11 05:18:07","","0","0","0");
INSERT INTO acc_head VALUES ("38","125","0","0","مدينين آخرين","","","","0","0","0","","7","","1","1","0","0","0","0.000","0","2024-01-23 06:30:11","2024-06-11 05:18:13","","0","0","0");
INSERT INTO acc_head VALUES ("39","1241","0","1","البنك الافتراضي","","","","0","0","0","","37","","1","0","0","0","0","0.000","0","2024-01-23 06:32:21","2026-05-09 20:20:40","","0","0","0");
INSERT INTO acc_head VALUES ("40","44","0","0","مصروفات عامه ","","","","0","0","0","","5","","2","1","0","0","0","0.000","0","2024-01-23 06:34:08","2024-06-11 05:18:24","","0","0","0");
INSERT INTO acc_head VALUES ("55","112","0","0","اصول قابله للتأجير","","","","0","0","0","0","6","","1","1","0","0","0","0.000","0","2024-02-20 05:16:57","2024-06-11 05:18:30","","0","0","0");
INSERT INTO acc_head VALUES ("86","411","0","0","صافي المشتريات","","","","0","0","0","0","15","","2","1","0","0","0","0.000","0","2024-03-08 00:56:24","2024-06-11 05:18:46","","0","0","0");
INSERT INTO acc_head VALUES ("89","4111","0","0","المشتربات","","","","0","0","0","0","86","","2","1","0","0","0","0.000","0","2024-03-08 21:37:09","2024-06-11 05:18:53","","0","0","0");
INSERT INTO acc_head VALUES ("90","4112","0","0","مردود المشتريات","","","","0","0","0","0","86","","2","1","0","0","0","0.000","0","2024-03-08 21:38:25","2024-06-11 05:18:56","","0","0","0");
INSERT INTO acc_head VALUES ("91","41103","0","1","خصم مسموح به","","","","0","0","0","0","86","","2","0","0","0","0","0.000","0","2024-03-08 21:43:40","2026-05-09 20:23:20","","0","0","0");
INSERT INTO acc_head VALUES ("92","311","0","0","صافي المبيعات","","","","0","0","0","0","13","","2","1","0","0","0","0.000","0","2024-03-08 21:48:15","2024-06-11 05:19:02","","0","0","0");
INSERT INTO acc_head VALUES ("93","3111","0","0","المبيعات","","","","0","0","0","0","92","","2","1","0","0","0","0.000","0","2024-03-08 21:49:07","2024-06-11 05:19:05","","0","0","0");
INSERT INTO acc_head VALUES ("94","3112","0","0","مردود المبيعات","","","","0","0","0","0","92","","2","1","0","0","0","0.000","0","2024-03-08 21:50:03","2024-06-11 05:19:07","","0","0","0");
INSERT INTO acc_head VALUES ("95","3113","0","0","خصومات تشغيل","","","","0","0","0","0","92","","2","1","0","0","0","0.000","0","2024-03-08 21:54:56","2024-06-11 05:19:10","","0","0","0");
INSERT INTO acc_head VALUES ("97","31131","0","1","خصم مكتسب","","","","0","0","0","0","95","","2","0","0","0","0","0.000","0","2024-03-10 00:38:24","2024-11-16 22:48:00","","0","0","0");
INSERT INTO acc_head VALUES ("98","321","0","0","ايرادات من تأجير أصول","","","","0","0","0","0","14","","2","1","0","0","0","0.000","0","2024-03-14 14:58:05","2024-06-11 05:19:27","","0","0","0");
INSERT INTO acc_head VALUES ("99","32101","0","0","ايرادات من التأجير","","","","0","0","0","0","98","","2","0","0","0","0","0.000","0","2024-03-14 15:01:19","2024-11-16 22:49:22","","0","0","0");
INSERT INTO acc_head VALUES ("131","213001","0","1","الموظف 1","","","","0","0","0","0","35","","1","0","0","0","0","0.000","0","2024-06-18 09:41:56","2026-05-09 20:21:45","","1","0","0");
INSERT INTO acc_head VALUES ("148","122001","0","0","العميل الافتراضي","","","","0","0","0","0","19","","1","0","0","0","0","0.000","0","2024-06-23 01:26:46","2026-05-09 20:23:20","","1","0","0");



DROP TABLE allowances;

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




DROP TABLE analisys;

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




DROP TABLE attandance;

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
  PRIMARY KEY (`id`),
  KEY `employee` (`employee`),
  CONSTRAINT `attandance_ibfk_1` FOREIGN KEY (`employee`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=758 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE attdocs;

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
  `info` varchar(250) DEFAULT NULL,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=139 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE attlog;

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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE barcodes;

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




DROP TABLE book_tybes;

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




DROP TABLE booking_cards;

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




DROP TABLE calls;

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




DROP TABLE cases;

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




DROP TABLE chances;

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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO chances VALUES ("1","عبدالهادي العدوي ","عبدالهادي العدوي ","01005366038","2026-05-13","1","0","1","2026-05-13 15:24:50","2026-05-13 15:24:50","0","0","0");



DROP TABLE chances_tybes;

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

INSERT INTO chances_tybes VALUES ("1","جديد","","2023-11-28 03:20:13","0","0","0");
INSERT INTO chances_tybes VALUES ("2","تم الاتفاق","","2023-11-28 03:27:21","0","0","0");
INSERT INTO chances_tybes VALUES ("3","دفع عربون","","2023-11-28 03:27:21","0","0","0");
INSERT INTO chances_tybes VALUES ("4","صفقه تامه","","2023-11-28 03:27:42","0","0","0");



DROP TABLE cities;

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




DROP TABLE clients;

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
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO clients VALUES ("10","عبدالهادي العدوي","01005366038","","","","","0","0","0","1988-12-08","","","","2026-05-09 20:41:04","2026-05-09 20:43:54","","","0","","","","","","","","","0","0","0");
INSERT INTO clients VALUES ("11","محمد علي","","","","","","","","","","","","","2026-05-09 20:46:52","2026-05-09 20:46:52","","","","","","","","","","","","0","0","0");
INSERT INTO clients VALUES ("12","علي محمود","","","","","","","","","","","","","2026-05-09 20:47:04","2026-05-09 20:47:04","","","","","","","","","","","","0","0","0");



DROP TABLE closed_orders;

CREATE TABLE `closed_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shift` varchar(10) NOT NULL,
  `user` varchar(10) DEFAULT NULL,
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
  `info` varchar(50) DEFAULT NULL,
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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE cost_centers;

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

INSERT INTO cost_centers VALUES ("1","المركز الافتراضي","","2024-01-19 03:17:02","2024-01-19 03:17:02","0","0","0");



DROP TABLE criminals;

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




DROP TABLE crm_style;

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




DROP TABLE ctp;

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




DROP TABLE cvs;

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




DROP TABLE delivery_clients;

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




DROP TABLE departments;

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




DROP TABLE drugs;

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




DROP TABLE emp_allowences;

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




DROP TABLE emp_kbis;

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




DROP TABLE emplog;

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




DROP TABLE employee_operations;

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




DROP TABLE employees;

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
  `password` varchar(250) DEFAULT NULL,
  `education` varchar(100) DEFAULT NULL,
  `skills` varchar(200) DEFAULT NULL,
  `hour_extra` decimal(10,2) NOT NULL DEFAULT 0.00,
  `day_extra` decimal(10,2) NOT NULL DEFAULT 0.00,
  `ent_tybe` int(11) NOT NULL DEFAULT 1,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=139 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE entitles;

CREATE TABLE `entitles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tybe` varchar(50) NOT NULL,
  `info` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE extras;

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




DROP TABLE fat_details;

CREATE TABLE `fat_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pro_tybe` int(11) DEFAULT NULL,
  `det_store` int(11) DEFAULT 1,
  `pro_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT 0,
  `u_val` decimal(10,3) NOT NULL DEFAULT 1.000,
  `qty_in` double DEFAULT 0,
  `qty_out` double DEFAULT 0,
  `price` double DEFAULT 0,
  `cost_price` double(12,2) DEFAULT NULL,
  `stock_value` double(12,2) DEFAULT NULL,
  `discount` double DEFAULT NULL,
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

INSERT INTO fat_details VALUES ("43","4","27","34","7","1.000","100","0","100","100.00","","0","","10000","0","34","4","2026-05-13 15:21:15","0","0","0");
INSERT INTO fat_details VALUES ("44","4","27","34","6","1.000","1","0","150","150.00","","0","","150","0","34","4","2026-05-13 15:21:15","0","0","0");
INSERT INTO fat_details VALUES ("45","4","27","35","7","1.000","100","0","100","100.00","","0","","10000","0","35","4","2026-05-13 15:53:22","0","0","0");
INSERT INTO fat_details VALUES ("46","4","27","35","6","1.000","150","0","150","150.00","","0","","22500","0","35","4","2026-05-13 15:53:22","0","0","0");
INSERT INTO fat_details VALUES ("47","4","27","36","6","1.000","1","0","150","150.00","","0","","150","0","36","4","2026-05-13 16:01:41","0","0","0");
INSERT INTO fat_details VALUES ("48","4","27","36","7","1.000","100","0","100","100.00","","0","","10000","0","36","4","2026-05-13 16:01:41","0","0","0");
INSERT INTO fat_details VALUES ("49","4","27","37","7","1.000","10","0","100","100.00","","0","","1000","0","37","4","2026-05-13 16:26:17","0","0","0");



DROP TABLE fat_tybes;

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

INSERT INTO fat_tybes VALUES ("1","فاتورة مبيعات","","2024-01-29 18:39:27","0","0","0");
INSERT INTO fat_tybes VALUES ("2","فاتورة مشنريات","","2024-01-29 18:41:22","0","0","0");
INSERT INTO fat_tybes VALUES ("3","فاتورة مردود مبيعات","","2024-03-06 17:25:41","0","0","0");
INSERT INTO fat_tybes VALUES ("4","فاتورة مردود مشتريات","","2024-03-06 17:26:30","0","0","0");
INSERT INTO fat_tybes VALUES ("5","اذن تسليم بضاعه","","2024-03-06 17:26:30","0","0","0");
INSERT INTO fat_tybes VALUES ("6","اذن استلام بضاعه","","2024-03-06 17:26:57","0","0","0");
INSERT INTO fat_tybes VALUES ("7","اذن تسليم بضاعه","","2024-03-06 17:26:57","0","0","0");
INSERT INTO fat_tybes VALUES ("8","اذن حجز","","2024-03-06 17:29:32","0","0","0");
INSERT INTO fat_tybes VALUES ("9","امر بيع","","2024-03-06 17:29:32","0","0","0");
INSERT INTO fat_tybes VALUES ("10","امر شراء","","2024-03-06 17:29:32","0","0","0");
INSERT INTO fat_tybes VALUES ("11","فاتورة تصنيع حر","","2024-03-06 17:29:32","0","0","0");
INSERT INTO fat_tybes VALUES ("12","تصنيع نموذجي","","2024-03-06 17:29:32","0","0","0");



DROP TABLE fats;

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




DROP TABLE fptybes;

CREATE TABLE `fptybes` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO fptybes VALUES ("1","حضور","2023-08-01 01:57:14","","0","0");
INSERT INTO fptybes VALUES ("2","انصراف","2023-08-01 01:57:14","","0","0");
INSERT INTO fptybes VALUES ("3","حضور اضافي","2023-08-01 01:57:42","","0","0");
INSERT INTO fptybes VALUES ("4","انصراف اضافي","2023-08-01 01:58:34","","0","0");
INSERT INTO fptybes VALUES ("5","invalid","2023-08-10 07:45:50","","0","0");



DROP TABLE hiringcontracts;

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




DROP TABLE holidays;

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




DROP TABLE hr_operation_steps;

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




DROP TABLE hr_operations;

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




DROP TABLE imgs;

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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO imgs VALUES ("1","DSC08632 copy133185-10-.jpg","","0","5481394","10","2026-05-09","2026-05-09 20:44:11","0","0","0");
INSERT INTO imgs VALUES ("2","Gemini_Generated_Image_n6zcopn6zcopn6zc125364-10-.png","","0","7941203","10","2026-05-09","2026-05-09 20:45:22","0","0","0");



DROP TABLE imporfplog;

CREATE TABLE `imporfplog` (
  `id` int(1) DEFAULT NULL,
  `basma_id` int(11) NOT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE item_group;

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




DROP TABLE item_group2;

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




DROP TABLE item_group3;

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




DROP TABLE item_units;

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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO item_units VALUES ("4","6","1","1.000","0","0","0","0","0.000","0.000","0.000","0.000","1","0","0","0");
INSERT INTO item_units VALUES ("5","7","1","1.000","0","0","0","0","0.000","0.000","0.000","0.000","2","0","0","0");



DROP TABLE joplevels;

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




DROP TABLE joprules;

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




DROP TABLE jops;

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




DROP TABLE joptybes;

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




DROP TABLE journal_entries;

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
) ENGINE=InnoDB AUTO_INCREMENT=75 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO journal_entries VALUES ("67","34","27","10150","0","0","","2026-05-13 15:21:15","0","34","0","2026-05-13 15:21:15","0","0");
INSERT INTO journal_entries VALUES ("68","34","36","0","10150","1","","2026-05-13 15:21:15","0","34","0","2026-05-13 15:21:15","0","0");
INSERT INTO journal_entries VALUES ("69","35","27","32500","0","0","","2026-05-13 15:53:22","0","35","0","2026-05-13 15:53:22","0","0");
INSERT INTO journal_entries VALUES ("70","35","36","0","32500","1","","2026-05-13 15:53:22","0","35","0","2026-05-13 15:53:22","0","0");
INSERT INTO journal_entries VALUES ("71","36","27","10150","0","0","","2026-05-13 16:01:41","0","36","0","2026-05-13 16:01:41","0","0");
INSERT INTO journal_entries VALUES ("72","36","36","0","10150","1","","2026-05-13 16:01:41","0","36","0","2026-05-13 16:01:41","0","0");
INSERT INTO journal_entries VALUES ("73","37","27","1000","0","0","","2026-05-13 16:26:17","0","37","0","2026-05-13 16:26:17","0","0");
INSERT INTO journal_entries VALUES ("74","37","36","0","1000","1","","2026-05-13 16:26:17","0","37","0","2026-05-13 16:26:17","0","0");



DROP TABLE journal_heads;

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
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO journal_heads VALUES ("34","1","10150","2026-05-13","34","","فاتورة مشتريات _ 34","2026-05-13 15:21:15","0","0","2026-05-13 15:21:15","1","0","0");
INSERT INTO journal_heads VALUES ("35","2","32500","2026-05-13","35","","فاتورة مشتريات _ 35","2026-05-13 15:53:22","0","0","2026-05-13 15:53:22","1","0","0");
INSERT INTO journal_heads VALUES ("36","3","10150","2026-05-13","36","","فاتورة مشتريات _ 36","2026-05-13 16:01:41","0","0","2026-05-13 16:01:41","1","0","0");
INSERT INTO journal_heads VALUES ("37","4","1000","2026-05-13","37","","فاتورة مشتريات _ 37","2026-05-13 16:26:17","0","0","2026-05-13 16:26:17","1","0","0");



DROP TABLE journal_tybes;

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

INSERT INTO journal_tybes VALUES ("1","1","purchases","يومية المقبوضات","","2024-03-14 02:34:38","2024-03-14 02:34:38","0","0","0");
INSERT INTO journal_tybes VALUES ("2","2","sales","يومية المدفوعات","","2024-03-14 02:34:38","2024-03-14 02:34:38","0","0","0");
INSERT INTO journal_tybes VALUES ("3","3","Payments","المبيعات","","2024-03-14 02:34:38","2024-03-14 02:34:38","0","0","0");
INSERT INTO journal_tybes VALUES ("4","4","receipts","يومية المشتريات","","2024-03-14 02:34:38","2024-03-14 02:34:38","0","0","0");
INSERT INTO journal_tybes VALUES ("5","5","Accrueds","ايراد مستحق","","2024-03-14 02:34:38","2024-03-14 02:34:38","0","0","0");
INSERT INTO journal_tybes VALUES ("6","6","Accrueds","خصم مكتسب","","2024-03-14 02:34:38","2024-03-14 02:34:38","0","0","0");
INSERT INTO journal_tybes VALUES ("7","7","Accrueds","خصم مسموح به","","2024-03-14 02:34:38","2024-03-14 02:34:38","0","0","0");
INSERT INTO journal_tybes VALUES ("8","8","journal","القيود اليومية","","2024-03-14 02:34:38","2024-03-14 02:34:38","0","0","0");



DROP TABLE karta;

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




DROP TABLE kbis;

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



DROP TABLE my_news;

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




DROP TABLE myinstallments;

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




DROP TABLE myitems;

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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO myitems VALUES ("6","تجريبي 1","","1","1","1","152","","0","150","150","0","0","0","0","0","0","0","2026-05-13 14:26:42","2026-05-13 16:01:41","1","0","0","0");
INSERT INTO myitems VALUES ("7","تجريبي 2","","2","1","2","310","","0","100","100","0","0","0","0","0","0","0","2026-05-13 14:39:44","2026-05-13 16:26:17","1","0","0","0");



DROP TABLE myoper_det;

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




DROP TABLE myoptions;

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



DROP TABLE mypatterns;

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




DROP TABLE mypowers;

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




DROP TABLE myrents;

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




DROP TABLE myunits;

CREATE TABLE `myunits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uname` varchar(60) NOT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO myunits VALUES ("1","ق","2026-05-07 19:00:03","2026-05-16 18:21:08","0","0","0");
INSERT INTO myunits VALUES ("2","كيلو","2026-05-07 19:00:08","2026-05-07 19:00:08","0","0","0");



DROP TABLE myvouchers;

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




DROP TABLE notes;

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




DROP TABLE oppatterns;

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




DROP TABLE order_status;

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




DROP TABLE order_types;

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




DROP TABLE orders;

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




DROP TABLE ot_head;

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
  KEY `waiter_id` (`waiter_id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO ot_head VALUES ("34","1","","","takeaway","4","1","","","1","4","نوع الطلب: تيك أواي","15:21:15","15:21:15","","","2026-05-13","0000-00-00","1","","","","","1","27","131","131","27","","","36","","","10150","0","1","0","10150","10150","0","0","0","0","0","0","0.00","0.00","unpaid","completed","2026-05-13 15:21:15","0","0","0","2026-05-13 15:21:15","1","","0","0","0","active","cash","","","","","0.00");
INSERT INTO ot_head VALUES ("35","2","","","takeaway","4","1","","","1","4","نوع الطلب: تيك أواي","15:53:22","15:53:22","","","2026-05-13","0000-00-00","1","","","","","1","27","131","131","27","","","36","","","32500","0","1","0","32500","32500","0","0","0","0","0","0","0.00","0.00","unpaid","completed","2026-05-13 15:53:22","0","0","0","2026-05-13 15:53:22","1","","0","0","0","active","cash","","","","","0.00");
INSERT INTO ot_head VALUES ("36","3","","","takeaway","4","1","","","1","4","نوع الطلب: تيك أواي","16:01:41","16:01:41","","","2026-05-13","0000-00-00","1","","","","","1","27","131","131","27","","","36","","","10150","0","1","0","10150","10150","0","0","0","0","0","0","0.00","0.00","unpaid","completed","2026-05-13 16:01:41","0","0","0","2026-05-13 16:01:41","1","","0","0","0","active","cash","","","","","0.00");
INSERT INTO ot_head VALUES ("37","4","","","takeaway","4","1","","","1","4","نوع الطلب: تيك أواي","16:26:17","16:26:17","","","2026-05-13","0000-00-00","1","","","","","1","27","131","131","27","","","36","","","1000","0","1","0","1000","1000","0","0","0","0","0","0","0.00","0.00","unpaid","completed","2026-05-13 16:26:17","0","0","0","2026-05-13 16:26:17","1","","0","0","0","active","cash","","","","","0.00");



DROP TABLE paper_types;

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




DROP TABLE patt_cols;

CREATE TABLE `patt_cols` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cname` varchar(100) NOT NULL,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE permits;

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




DROP TABLE prescdetails;

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




DROP TABLE prescs;

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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO prescs VALUES ("3","10","4","cbr","","2026-05-09 20:44:33","2026-05-09 20:44:33","0","0","0");



DROP TABLE price_lists;

CREATE TABLE `price_lists` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pname` varchar(100) NOT NULL,
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO price_lists VALUES ("1","سعر 1","0","0","0");
INSERT INTO price_lists VALUES ("2","سعر 2","0","0","0");



DROP TABLE print;

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




DROP TABLE pro_tybes;

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
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO pro_tybes VALUES ("1","سند قبض","","1","","2024-03-14 04:01:35","2024-03-14 04:01:35","0","0","0");
INSERT INTO pro_tybes VALUES ("2","سند دفع","","2","","2024-03-14 04:01:35","2024-03-14 04:02:22","0","0","0");
INSERT INTO pro_tybes VALUES ("3","فاتورة مبيعات","","3","","2024-03-14 04:01:58","2024-03-14 04:02:26","0","0","0");
INSERT INTO pro_tybes VALUES ("4","فاتورة مشتريات","","4","","2024-03-14 04:01:58","2024-03-14 04:02:28","0","0","0");
INSERT INTO pro_tybes VALUES ("5","استحقاق قسط","","5","","2024-03-17 05:53:16","2024-03-17 05:53:27","0","0","0");
INSERT INTO pro_tybes VALUES ("6","خصم مكتسب","","6","","2024-03-17 05:53:16","2024-03-17 05:53:27","0","0","0");
INSERT INTO pro_tybes VALUES ("7","خصم مسموح به","","7","","2024-03-17 05:53:16","2024-03-17 05:53:27","0","0","0");
INSERT INTO pro_tybes VALUES ("8","قيد يومية","","8","","2024-05-14 14:06:41","2024-05-14 14:06:54","0","0","0");
INSERT INTO pro_tybes VALUES ("9","فاتورة كاشير","","9","","2024-05-14 14:06:41","2024-07-19 20:25:29","0","0","0");
INSERT INTO pro_tybes VALUES ("10","فاتورة مردود مبيعات","","10","","2024-05-14 14:06:41","2024-11-21 17:25:06","0","0","0");
INSERT INTO pro_tybes VALUES ("11","فاتورة مردود مشتريات","","11","","2024-05-14 14:06:41","2024-11-21 17:25:10","0","0","0");
INSERT INTO pro_tybes VALUES ("12","أمر شراء","","12","","2024-05-14 14:06:41","2024-11-21 17:25:12","0","0","0");
INSERT INTO pro_tybes VALUES ("13","أمر بيع","","13","","2024-05-14 14:06:41","2024-11-21 17:25:16","0","0","0");
INSERT INTO pro_tybes VALUES ("14","رصيد افتتاحي مخازن","","14","","2024-05-14 14:06:41","2024-11-23 13:40:49","0","0","0");
INSERT INTO pro_tybes VALUES ("15","رصيد افتتاحي حسابات","","15","","2024-05-14 14:06:41","2024-11-23 13:40:52","0","0","0");



DROP TABLE process;

CREATE TABLE `process` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(255) NOT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO process VALUES ("1","add item","2026-05-07 18:59:12");
INSERT INTO process VALUES ("2","add item","2026-05-07 18:59:19");
INSERT INTO process VALUES ("3","add buy","2026-05-07 18:59:42");
INSERT INTO process VALUES ("4","add unit","2026-05-07 19:00:03");
INSERT INTO process VALUES ("5","add unit","2026-05-07 19:00:08");
INSERT INTO process VALUES ("6","add item","2026-05-07 19:01:03");
INSERT INTO process VALUES ("7","add item","2026-05-07 19:01:08");
INSERT INTO process VALUES ("8","add item","2026-05-07 19:04:02");
INSERT INTO process VALUES ("9","add sales","2026-05-07 19:06:56");
INSERT INTO process VALUES ("10","edit buy","2026-05-07 19:26:42");
INSERT INTO process VALUES ("11","add buy","2026-05-07 19:34:57");
INSERT INTO process VALUES ("12","edit buy","2026-05-07 19:36:35");
INSERT INTO process VALUES ("13","edit buy","2026-05-07 19:36:47");
INSERT INTO process VALUES ("14","add account >> علي","2026-05-09 20:21:07");
INSERT INTO process VALUES ("15","add account >> موظف 2","2026-05-09 20:22:16");
INSERT INTO process VALUES ("16","add user","2026-05-09 20:39:36");
INSERT INTO process VALUES ("17","add user","2026-05-09 20:39:48");
INSERT INTO process VALUES ("18","logout >> admin","2026-05-09 20:39:50");
INSERT INTO process VALUES ("19","add reservation","2026-05-09 20:41:04");
INSERT INTO process VALUES ("20","add reservation","2026-05-09 20:42:11");
INSERT INTO process VALUES ("21","add docs","2026-05-09 20:44:11");
INSERT INTO process VALUES ("22","add presc","2026-05-09 20:44:33");
INSERT INTO process VALUES ("23","add docs","2026-05-09 20:45:22");
INSERT INTO process VALUES ("24","logout >> دكتور","2026-05-09 20:46:06");
INSERT INTO process VALUES ("25","add reservation","2026-05-09 20:46:52");
INSERT INTO process VALUES ("26","add reservation","2026-05-09 20:47:04");
INSERT INTO process VALUES ("27","logout >> مساعد","2026-05-09 20:48:39");
INSERT INTO process VALUES ("28","logout >> admin","2026-05-12 15:46:25");
INSERT INTO process VALUES ("29","add item","2026-05-13 14:26:42");
INSERT INTO process VALUES ("30","add item","2026-05-13 14:39:44");
INSERT INTO process VALUES ("31","add buy","2026-05-13 15:21:15");
INSERT INTO process VALUES ("32","add chance","2026-05-13 15:24:50");
INSERT INTO process VALUES ("33","add buy","2026-05-13 15:53:22");
INSERT INTO process VALUES ("34","add buy","2026-05-13 16:01:41");
INSERT INTO process VALUES ("35","add buy","2026-05-13 16:26:17");
INSERT INTO process VALUES ("36","add visit","2026-05-13 19:13:57");
INSERT INTO process VALUES ("37","add unit","2026-05-16 18:21:31");



DROP TABLE prods;

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




DROP TABLE productions;

CREATE TABLE `productions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `snd_id` int(11) NOT NULL,
  `date` date NOT NULL,
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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE pst_activities;

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




DROP TABLE pst_criminals;

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




DROP TABLE pst_crmstyles;

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




DROP TABLE pst_gangs;

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




DROP TABLE pst_issues;

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




DROP TABLE rays;

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




DROP TABLE reservations;

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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO reservations VALUES ("3","10","0","","2026-05-10","10:30:00","","","","3","2026-05-09 20:41:04","2026-05-09 20:41:04","500","0","0","0","","0","0","0");
INSERT INTO reservations VALUES ("4","10","0","","2026-05-09","10:00:00","20:42:00","20:45:00","3","1","2026-05-09 20:42:11","2026-05-09 20:45:50","400","0","0","0","","0","0","0");
INSERT INTO reservations VALUES ("5","11","0","","2026-05-09","10:00:00","","","","1","2026-05-09 20:46:52","2026-05-09 20:46:52","400","0","0","0","","0","0","0");
INSERT INTO reservations VALUES ("6","12","0","","2026-05-09","10:15:00","","","","1","2026-05-09 20:47:04","2026-05-09 20:47:04","400","0","0","0","","0","0","0");



DROP TABLE salaries;

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




DROP TABLE services;

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




DROP TABLE session_time;

CREATE TABLE `session_time` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `user` int(1) NOT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `isdeleted` tinyint(1) DEFAULT 0,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO session_time VALUES ("3","29","2026-05-09 20:39:54","0","0","0");
INSERT INTO session_time VALUES ("4","30","2026-05-09 20:46:09","0","0","0");
INSERT INTO session_time VALUES ("5","1","2026-05-10 14:52:47","0","0","0");
INSERT INTO session_time VALUES ("6","1","2026-05-12 15:26:02","0","0","0");
INSERT INTO session_time VALUES ("7","1","2026-05-13 14:26:20","0","0","0");
INSERT INTO session_time VALUES ("8","1","2026-05-13 15:11:41","0","0","0");
INSERT INTO session_time VALUES ("9","1","2026-05-16 18:20:48","0","0","0");



DROP TABLE settings;

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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO settings VALUES ("1","FOCUS HOUSE","سمنود - برج زايد - الدور الخامس","abdelhadeeladawy@gmail.com","010053662038","125","d35c99e7485691ea14f829029dc03e69A67b8d2f92148f52cad46e331936922e8","","99","2024-01-01","2024-12-31","ar","#f0f0f0","0","1","0","0","0","1","1","155","27","131","21","0","0","0","","","barcode","0");



DROP TABLE shifts;

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
  `workingdays` varchar(20) DEFAULT NULL,
  `tenant` int(11) DEFAULT 0,
  `branch` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE shw_optns;

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



DROP TABLE sitting_items;

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

INSERT INTO sitting_items VALUES ("1","الموظف يحاسب علي اساس ساعات العمل","0","","2023-12-27 00:32:16","2023-12-27 00:32:16","0","0","0");
INSERT INTO sitting_items VALUES ("2","الموظف يحاسب علي اساس ساعات العمل التقديريه 26 يوم","0","","2023-12-27 00:33:03","2023-12-27 00:33:03","0","0","0");
INSERT INTO sitting_items VALUES ("3","الشهر عباره عن 30 يوم","0","","2023-12-27 00:35:34","2023-12-27 00:35:34","0","0","0");
INSERT INTO sitting_items VALUES ("4","البصمه المفقوده يتم تجاهلها","0","","2023-12-27 00:35:34","2023-12-27 00:35:34","0","0","0");



DROP TABLE skills;

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




DROP TABLE system_logs;

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




DROP TABLE tables;

CREATE TABLE `tables` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tname` varchar(255) NOT NULL,
  `table_case` int(11) NOT NULL DEFAULT 0,
  `crtime` datetime DEFAULT current_timestamp(),
  `mdtime` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `isdeleted` tinyint(1) NOT NULL DEFAULT 0,
  `branch` varchar(255) DEFAULT NULL,
  `tatnet` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO tables VALUES ("1","طاولة 1","0","2026-05-16 20:11:04","2026-05-16 20:11:04","0","","");
INSERT INTO tables VALUES ("2","طاولة 2","0","2026-05-16 20:11:04","2026-05-16 20:11:04","0","","");
INSERT INTO tables VALUES ("3","طاولة 3","0","2026-05-16 20:11:04","2026-05-16 20:11:04","0","","");
INSERT INTO tables VALUES ("4","طاولة 4","0","2026-05-16 20:11:04","2026-05-16 20:11:04","0","","");
INSERT INTO tables VALUES ("5","طاولة 5","0","2026-05-16 20:11:04","2026-05-16 20:11:04","0","","");
INSERT INTO tables VALUES ("6","طاولة 6","0","2026-05-16 20:11:04","2026-05-16 20:11:04","0","","");
INSERT INTO tables VALUES ("7","طاولة 7","0","2026-05-16 20:11:04","2026-05-16 20:11:04","0","","");
INSERT INTO tables VALUES ("8","طاولة 8","0","2026-05-16 20:11:04","2026-05-16 20:11:04","0","","");
INSERT INTO tables VALUES ("9","طاولة 9","0","2026-05-16 20:11:04","2026-05-16 20:11:04","0","","");
INSERT INTO tables VALUES ("10","طاولة 10","0","2026-05-16 20:11:04","2026-05-16 20:11:04","0","","");
INSERT INTO tables VALUES ("11","طاولة 11","0","2026-05-16 20:11:04","2026-05-16 20:11:04","0","","");
INSERT INTO tables VALUES ("12","طاولة 12","0","2026-05-16 20:11:04","2026-05-16 20:11:04","0","","");



DROP TABLE tasks;

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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO tasks VALUES ("1","عبدالهادي العدوي ","1","","01005366038","29","0","1","0","54564","","2026-05-13 15:24:50","2026-05-13 15:25:09","1","0","0");



DROP TABLE tasktybes;

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

INSERT INTO tasktybes VALUES ("1","زياره اعطال","","2023-07-27 15:13:07","0","0","0");
INSERT INTO tasktybes VALUES ("2","زياره تسويق","","2023-07-27 15:13:07","0","0","0");
INSERT INTO tasktybes VALUES ("3","زياره علاقات","","2023-07-27 15:13:07","0","0","0");
INSERT INTO tasktybes VALUES ("4","تركيب","","2023-12-23 03:44:24","0","0","0");
INSERT INTO tasktybes VALUES ("5","كلينت","","2023-12-23 03:44:24","0","0","0");



DROP TABLE test;

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




DROP TABLE towns;

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




DROP TABLE transactions;

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




DROP TABLE users;

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
  PRIMARY KEY (`id`),
  KEY `is_waiter` (`is_waiter`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO users VALUES ("1","admin","$2y$10$P4w1yXa1z1yQqUKh2q4lX.vU0vDb.fpRztEGUrFWx5Y7MQRR8iyCm","2022-12-05 17:01:33","0","2","1","0","22947314.png","","","","","","","1","","0","0");
INSERT INTO users VALUES ("29","دكتور","$2y$10$MchwiqcCdcQi8zaySFoxEuUkL6BSx4uMxbc/x/4FqE0wPgsopGON6","2026-05-09 20:39:36","0","0","26","0","","","","","","","","","","0","0");
INSERT INTO users VALUES ("30","مساعد","$2y$10$w74mg.gh7w0xJnQzfjMFnujllNm50kfdm527VSSjQ8RgN4Sv8ou9a","2026-05-09 20:39:48","0","0","27","0","","","","","","","","","","0","0");



DROP TABLE usr_pwrs;

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
  PRIMARY KEY (`id`),
  UNIQUE KEY `rollname` (`rollname`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO usr_pwrs VALUES ("1","admin","1","1","1","1","1","1","0","0","0","0","0","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","0","0","0","0","0","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","wwww","1","2024-05-12 18:05:26","2025-12-18 12:37:11","0","0","1","1","1","1");
INSERT INTO usr_pwrs VALUES ("2","cashier","1","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","1","0","0","0","0","0","0","0","0","0","0","0","0","cshier","1","2024-05-12 18:11:21","2025-03-05 21:32:34","0","0","0","0","1","0");
INSERT INTO usr_pwrs VALUES ("26","دكتور","1","1","1","1","1","1","0","0","0","0","0","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","0","0","0","0","0","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","0","0","0","0","0","1","0","1","0","0","0","0","0","1","1","دكتور","1","2024-05-19 22:04:27","2024-08-21 16:00:57","0","0","0","1","1","1");
INSERT INTO usr_pwrs VALUES ("27","مساعد دكتور","1","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","1","0","0","0","0","0","0","0","0","0","مساعد دكتور","1","2024-05-30 23:48:11","2024-05-31 01:02:13","0","0","","1","1","1");
INSERT INTO usr_pwrs VALUES ("28","بائع كاشير","1","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","1","1","1","1","1","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","مبيعات كاشير فقط","0","2024-07-28 20:02:09","2024-07-28 20:02:09","0","0","","1","1","1");



DROP TABLE vacancies;

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




DROP TABLE visits;

CREATE TABLE `visits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gender` enum('male','female') NOT NULL,
  `age_group` enum('under18','18_25','25_40','over40') NOT NULL,
  `mode` enum('solo','group') NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `order_value` enum('under60','over60') NOT NULL,
  `type` enum('new','returning','regular') NOT NULL,
  `created_by` int(10) unsigned NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `client` int(11) NOT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO visits VALUES ("1","male","18_25","solo","19:13:00","19:14:00","under60","new","1","2026-05-13 19:13:57","0","","","","","2026-05-13 19:13:57","2026-05-13 19:13:57","","0","0","0");
INSERT INTO visits VALUES ("2","male","over40","solo","21:47:47","00:00:00","over60","new","1","2026-05-13 21:47:47","0","","","","","2026-05-13 21:47:47","2026-05-13 21:47:47","","0","0","0");
INSERT INTO visits VALUES ("3","male","under18","solo","21:48:05","00:00:00","over60","new","1","2026-05-13 21:48:05","0","","","","","2026-05-13 21:48:05","2026-05-13 21:48:05","","0","0","0");
INSERT INTO visits VALUES ("4","male","18_25","group","21:48:43","00:00:00","under60","returning","1","2026-05-13 21:48:43","0","","","","","2026-05-13 21:48:43","2026-05-13 21:48:43","","0","0","0");
INSERT INTO visits VALUES ("5","male","under18","solo","21:48:54","00:00:00","under60","new","1","2026-05-13 21:48:54","0","","","","","2026-05-13 21:48:54","2026-05-13 21:48:54","","0","0","0");
INSERT INTO visits VALUES ("6","male","under18","solo","21:48:57","00:00:00","under60","new","1","2026-05-13 21:48:57","0","","","","","2026-05-13 21:48:57","2026-05-13 21:48:57","","0","0","0");
INSERT INTO visits VALUES ("7","male","under18","solo","21:48:59","00:00:00","under60","new","1","2026-05-13 21:48:59","0","","","","","2026-05-13 21:48:59","2026-05-13 21:48:59","","0","0","0");
INSERT INTO visits VALUES ("8","male","under18","solo","21:49:02","00:00:00","under60","regular","1","2026-05-13 21:49:02","0","","","","","2026-05-13 21:49:02","2026-05-13 21:49:02","","0","0","0");



DROP TABLE visittybes;

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



DROP TABLE zankat;

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




