
CREATE TABLE `acc_groups` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `aname` varchar(40) NOT NULL,
  `acc_type` int(1) NOT NULL,
  `parent_id` int(1) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `code` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE `acc_head` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL,
  `deletable` int(11) DEFAULT 1,
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
  `isdeleted` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=157 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO acc_head VALUES ("1","1","0","الاصول","","","","0","0","0","","0","1","1","1","0","0","0","0.000","0","2023-11-25 02:55:49","2024-06-11 05:16:15","","0");
INSERT INTO acc_head VALUES ("2","2","0","الخصوم","","","","0","0","0","","0","1","1","1","0","0","0","0.000","0","2023-11-26 04:10:59","2024-06-11 05:16:21","","0");
INSERT INTO acc_head VALUES ("3","22","0","حقوق الملكية","","","","0","0","0","","2","1","1","1","0","0","0","0.000","0","2023-11-26 04:14:04","2024-06-11 05:16:25","","0");
INSERT INTO acc_head VALUES ("4","3","0","الايرادات","","","","0","0","0","","0","1","2","1","0","0","0","0.000","0","2023-11-26 04:14:29","2024-06-11 05:16:29","","0");
INSERT INTO acc_head VALUES ("5","4","0","المصروفات","","","","0","0","0","","0","1","2","1","0","0","0","0.000","0","2023-11-26 04:14:59","2024-06-11 05:16:35","","0");
INSERT INTO acc_head VALUES ("6","11","0","الاصول الثابته","","","","0","0","0","","1","1","1","1","0","0","0","0.000","0","2023-11-26 04:38:23","2024-06-11 05:16:38","","0");
INSERT INTO acc_head VALUES ("7","12","0","الاصول المتداوله","","","","0","0","0","","1","","1","1","0","0","0","0.000","0","2023-11-26 04:45:08","2024-06-11 05:16:42","","0");
INSERT INTO acc_head VALUES ("8","21","0","الخصوم المتداولة","","","","0","0","0","","2","","1","1","0","0","0","0.000","0","2023-11-26 04:45:47","2024-06-11 05:16:46","","0");
INSERT INTO acc_head VALUES ("9","221","0","الشركاء","","","","0","0","0","","3","","1","1","0","0","0","0.000","0","2023-11-26 04:46:20","2024-06-11 05:16:52","","0");
INSERT INTO acc_head VALUES ("10","222","0","ارباح غير موزعة","","","","0","0","0","","3","","1","1","0","0","0","0.000","0","2023-11-26 04:47:03","2024-06-11 05:16:55","","0");
INSERT INTO acc_head VALUES ("11","223","0","ارباح غير موزعة لفترات سابقة","","","","0","0","0","","3","","1","1","0","0","0","0.000","0","2023-11-26 04:47:50","2024-06-11 05:16:58","","0");
INSERT INTO acc_head VALUES ("13","31","0","ايرادات المبيعات","","","","0","0","0","","4","","2","1","0","0","0","0.000","0","2023-11-26 21:37:49","2024-06-11 05:17:02","","0");
INSERT INTO acc_head VALUES ("14","32","0","ايرادات غير تشغيليه","","","","0","0","0","","4","","2","1","0","0","0","0.000","0","2023-11-26 21:38:15","2024-06-11 05:17:06","","0");
INSERT INTO acc_head VALUES ("15","41","0","تكاليف المبيعات","","","","0","0","0","0","5","","2","1","0","0","0","0.000","0","2023-11-26 21:39:10","2024-06-11 05:17:09","","0");
INSERT INTO acc_head VALUES ("16","42","0","تكلفه البضاعه المباعه","","","","0","0","0","","5","","2","1","0","0","0","0.000","0","2023-11-26 21:39:49","2024-06-11 05:17:12","","0");
INSERT INTO acc_head VALUES ("17","43","0","رواتب و اجور","","","","0","0","0","","5","","2","1","0","0","0","0.000","0","2023-11-26 21:40:07","2024-06-11 05:17:16","","0");
INSERT INTO acc_head VALUES ("18","121","0","الصناديق","","","","0","0","0","","7","","1","1","0","0","0","0.000","0","2023-12-08 12:50:49","2024-06-11 05:17:18","","0");
INSERT INTO acc_head VALUES ("19","122","0","العملاء","","","","0","0","0","","7","","1","1","0","0","0","0.000","0","2023-12-08 12:52:13","2024-06-11 05:17:22","","0");
INSERT INTO acc_head VALUES ("20","123","0","المخزون","","","","0","0","0","","7","","1","1","0","0","0","0.000","0","2023-12-08 12:52:51","2024-06-11 05:17:27","","0");
INSERT INTO acc_head VALUES ("21","1211","0","الصندوق الافتراضي","","","","0","0","1","","18","","1","0","0","0","0","0.000","0","2023-12-09 10:46:52","2024-07-02 19:34:56","","0");
INSERT INTO acc_head VALUES ("24","1221","0","العميل النقدي","","","","0","0","0","0","19","","1","1","0","0","0","0.000","0","2023-12-28 01:25:46","2024-06-22 19:37:57","","0");
INSERT INTO acc_head VALUES ("27","123001","0","المخزن الرئيسي","","","","0","1","0","0","20","","1","0","0","0","0","4050.000","0","2023-12-28 03:35:35","2024-07-07 00:10:23","","0");
INSERT INTO acc_head VALUES ("29","2211","0","الشريك الرئيسي","","","","0","0","0","","9","","1","0","0","0","0","0.000","0","2023-12-30 02:12:22","2024-07-02 19:34:56","","0");
INSERT INTO acc_head VALUES ("33","211","0","الموردين","","","","0","0","0","","8","","1","1","0","0","0","0.000","0","2024-01-22 05:41:26","2024-06-11 05:17:55","","0");
INSERT INTO acc_head VALUES ("34","212","0","الدائنين الاخرين","","","","0","0","0","","8","","1","0","0","0","0","0.000","0","2024-01-22 05:42:08","2024-06-11 05:17:58","","0");
INSERT INTO acc_head VALUES ("35","213","0","الموظفين","","","","0","0","0","","8","","1","1","0","0","0","0.000","0","2024-01-22 05:42:29","2024-06-11 05:18:01","","0");
INSERT INTO acc_head VALUES ("36","2111","0","المورد الافتراضي","","","","0","0","0","","33","","1","0","0","0","0","-4050.000","0","2024-01-23 06:17:26","2024-07-07 00:10:23","","0");
INSERT INTO acc_head VALUES ("37","124","0","البنوك","","","","0","0","0","","7","","1","1","0","0","0","0.000","0","2024-01-23 06:22:23","2024-06-11 05:18:07","","0");
INSERT INTO acc_head VALUES ("38","125","0","مدينين آخرين","","","","0","0","0","","7","","1","1","0","0","0","0.000","0","2024-01-23 06:30:11","2024-06-11 05:18:13","","0");
INSERT INTO acc_head VALUES ("39","1241","0","البنك الافتراضي","","","","0","0","0","","37","","1","0","0","0","0","0.000","0","2024-01-23 06:32:21","2024-06-11 05:18:16","","0");
INSERT INTO acc_head VALUES ("40","44","0","مصروفات عامه ","","","","0","0","0","","5","","2","1","0","0","0","0.000","0","2024-01-23 06:34:08","2024-06-11 05:18:24","","0");
INSERT INTO acc_head VALUES ("55","112","0","اصول قابله للتأجير","","","","0","0","0","0","6","","1","1","0","0","0","0.000","0","2024-02-20 05:16:57","2024-06-11 05:18:30","","0");
INSERT INTO acc_head VALUES ("86","411","0","صافي المشتريات","","","","0","0","0","0","15","","2","1","0","0","0","0.000","0","2024-03-08 00:56:24","2024-06-11 05:18:46","","0");
INSERT INTO acc_head VALUES ("89","4111","0","المشتربات","","","","0","0","0","0","86","","2","1","0","0","0","0.000","0","2024-03-08 21:37:09","2024-06-11 05:18:53","","0");
INSERT INTO acc_head VALUES ("90","4112","0","مردود المشتريات","","","","0","0","0","0","86","","2","1","0","0","0","0.000","0","2024-03-08 21:38:25","2024-06-11 05:18:56","","0");
INSERT INTO acc_head VALUES ("91","41103","0","خصم مسموح به","","","","0","0","0","0","86","","2","0","0","0","0","0.000","0","2024-03-08 21:43:40","2024-06-23 01:29:18","","0");
INSERT INTO acc_head VALUES ("92","311","0","صافي المبيعات","","","","0","0","0","0","13","","2","1","0","0","0","0.000","0","2024-03-08 21:48:15","2024-06-11 05:19:02","","0");
INSERT INTO acc_head VALUES ("93","3111","0","المبيعات","","","","0","0","0","0","92","","2","1","0","0","0","0.000","0","2024-03-08 21:49:07","2024-06-11 05:19:05","","0");
INSERT INTO acc_head VALUES ("94","3112","0","مردود المبيعات","","","","0","0","0","0","92","","2","1","0","0","0","0.000","0","2024-03-08 21:50:03","2024-06-11 05:19:07","","0");
INSERT INTO acc_head VALUES ("95","3113","0","خصومات تشغيل","","","","0","0","0","0","92","","2","1","0","0","0","0.000","0","2024-03-08 21:54:56","2024-06-11 05:19:10","","0");
INSERT INTO acc_head VALUES ("97","31131","0","خصم مكتسب","","","","0","0","0","0","95","","2","0","0","0","0","0.000","0","2024-03-10 00:38:24","2024-07-01 20:19:14","","0");
INSERT INTO acc_head VALUES ("98","321","0","ايرادات من تأجير أصول","","","","0","0","0","0","14","","2","1","0","0","0","0.000","0","2024-03-14 14:58:05","2024-06-11 05:19:27","","0");
INSERT INTO acc_head VALUES ("99","32101","0","ايرادات من التأجير","","","","0","0","0","0","98","","2","0","0","0","0","0.000","0","2024-03-14 15:01:19","2024-06-19 04:55:26","","0");
INSERT INTO acc_head VALUES ("131","213001","0","الموظف 1","","","","0","0","0","0","35","","1","0","0","0","0","0.000","0","2024-06-18 09:41:56","2024-06-18 09:42:10","","0");
INSERT INTO acc_head VALUES ("148","122001","0","العميل الافتراضي","","","","0","0","0","0","19","","1","0","0","0","0","0.000","0","2024-06-23 01:26:46","2024-07-02 19:34:56","","0");
INSERT INTO acc_head VALUES ("155","122002","1","abdel hady","gyfr","ygyfg","","0","0","0","0","19","","1","0","0","0","0","0.000","0","2024-07-02 23:53:17","2024-07-02 23:53:17","","0");
INSERT INTO acc_head VALUES ("156","122003","1","مصطفى الاشرم","01212132379","سمنود","","0","0","0","0","19","","1","0","0","0","0","0.000","0","2024-07-04 19:29:50","2024-07-04 19:29:50","","0");




CREATE TABLE `allowances` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `info` varchar(250) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `tybe` int(1) NOT NULL,
  `isdeleted` int(11) DEFAULT NULL,
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
  `isdeleted` int(1) DEFAULT NULL,
  `fromwhere` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employee` (`employee`),
  CONSTRAINT `attandance_ibfk_1` FOREIGN KEY (`employee`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=335 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




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
  `info` varchar(250) DEFAULT NULL,
  `isdeleted` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE barcodes;

CREATE TABLE `barcodes` (
  `id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `barcode` varchar(25) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `barcode` (`barcode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE cases;

CREATE TABLE `cases` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `cname` varchar(50) NOT NULL,
  `info` varchar(100) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
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
  `isdeleted` int(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE chances_tybes;

CREATE TABLE `chances_tybes` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `cname` varchar(50) NOT NULL,
  `info` varchar(50) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO chances_tybes VALUES ("1","جديد","","2023-11-28 03:20:13");
INSERT INTO chances_tybes VALUES ("2","تم الاتفاق","","2023-11-28 03:27:21");
INSERT INTO chances_tybes VALUES ("3","دفع عربون","","2023-11-28 03:27:21");
INSERT INTO chances_tybes VALUES ("4","صفقه تامه","","2023-11-28 03:27:42");



DROP TABLE cities;

CREATE TABLE `cities` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `cname` varchar(150) NOT NULL,
  `info` varchar(150) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user` int(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO clients VALUES ("2","Olympia Morin","+1 (487) 492-3221","","Et autem ea aut non ","Dolore adipisicing q","Reprehenderit offici","","","","","","","","2024-07-09 19:06:54","2024-07-09 19:06:54","","","","","","","","","","","");
INSERT INTO clients VALUES ("3","عبدالهادي العدوي محمد المنير","01005366038","","سمنود ش الترعه","برج زايد الدور الخامس","","","","","","","","","2024-07-09 19:12:05","2024-07-09 19:12:05","","","","","","","","","","","");
INSERT INTO clients VALUES ("4","Octavius Harrington","+1 (759) 582-1457","+1 (131) 964-1274","Tempore voluptatum ","Porro eum ipsa et q","Labore qui velit dol","","","","","","","","2024-07-09 19:14:23","2024-07-09 19:14:23","","","","","","","","","","","");
INSERT INTO clients VALUES ("5","Alden Stanton","+1 (544) 464-5399","+1 (913) 657-4753","Obcaecati elit ea i","Officia qui suscipit","Mollitia velit aut ","","","","","","","","2024-07-09 19:16:10","2024-07-09 19:16:10","","","","","","","","","","","");
INSERT INTO clients VALUES ("6","Cody Torres","01005366038","+1 (531) 553-4921","Distinctio Necessit","Iusto consectetur e","Doloremque est esse","","","","","","","","2024-07-09 19:16:35","2024-07-09 19:16:35","","","","","","","","","","","");
INSERT INTO clients VALUES ("7","Inga English","+1 (188) 347-8624","+1 (146) 742-1367","Occaecat error esse ","Voluptatem exercitat","Magni magnam eiusmod","","","","","","","","2024-07-09 19:17:47","2024-07-09 19:17:47","","","","","","","","","","","");



DROP TABLE cost_centers;

CREATE TABLE `cost_centers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cname` varchar(100) NOT NULL,
  `info` varchar(200) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO cost_centers VALUES ("1","المركز الافتراضي","","2024-01-19 03:17:02","2024-01-19 03:17:02");



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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE crm_style;

CREATE TABLE `crm_style` (
  `id` int(11) NOT NULL,
  `cname` varchar(200) NOT NULL,
  `info` varchar(200) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE ctp;

CREATE TABLE `ctp` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `cname` varchar(50) NOT NULL,
  `type` varchar(100) DEFAULT NULL,
  `number` varchar(100) DEFAULT NULL,
  `info` varchar(100) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
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
  `lastsalary` double NOT NULL,
  `expsalary` double NOT NULL,
  `referances` text DEFAULT NULL,
  PRIMARY KEY (`id`,`email`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE departments;

CREATE TABLE `departments` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `info` varchar(250) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `isdeleted` int(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




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
  `isdeleted` int(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=130 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE extras;

CREATE TABLE `extras` (
  `id` int(11) NOT NULL,
  `empid` int(1) NOT NULL,
  `info` varchar(100) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `val` double NOT NULL,
  `tybe` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE fat_details;

CREATE TABLE `fat_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pro_tybe` int(11) DEFAULT NULL,
  `det_store` int(11) DEFAULT 1,
  `pro_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT 0,
  `qty_in` double DEFAULT 0,
  `qty_out` double DEFAULT 0,
  `price` double DEFAULT 0,
  `cost_price` double(12,2) DEFAULT NULL,
  `stock_value` double(12,2) DEFAULT NULL,
  `discount` double DEFAULT NULL,
  `plus` double DEFAULT NULL,
  `det_value` double DEFAULT 0,
  `fatid` int(11) DEFAULT NULL,
  `fat_tybe` int(11) NOT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `isdeleted` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`),
  CONSTRAINT `fat_details_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `myitems` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=171 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO fat_details VALUES ("169","4","27","271","630","120","0","15","","","0","","1800","271","4","2024-07-07 00:09:57","0");
INSERT INTO fat_details VALUES ("170","4","27","272","630","150","0","15","","","0","","2250","272","4","2024-07-07 00:10:23","0");



DROP TABLE fat_tybes;

CREATE TABLE `fat_tybes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fname` varchar(200) NOT NULL,
  `info` varchar(200) DEFAULT NULL,
  `crttime` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO fat_tybes VALUES ("1","فاتورة مبيعات","","2024-01-29 18:39:27");
INSERT INTO fat_tybes VALUES ("2","فاتورة مشنريات","","2024-01-29 18:41:22");
INSERT INTO fat_tybes VALUES ("3","فاتورة مردود مبيعات","","2024-03-06 17:25:41");
INSERT INTO fat_tybes VALUES ("4","فاتورة مردود مشتريات","","2024-03-06 17:26:30");
INSERT INTO fat_tybes VALUES ("5","اذن تسليم بضاعه","","2024-03-06 17:26:30");
INSERT INTO fat_tybes VALUES ("6","اذن استلام بضاعه","","2024-03-06 17:26:57");
INSERT INTO fat_tybes VALUES ("7","اذن تسليم بضاعه","","2024-03-06 17:26:57");
INSERT INTO fat_tybes VALUES ("8","اذن حجز","","2024-03-06 17:29:32");
INSERT INTO fat_tybes VALUES ("9","امر بيع","","2024-03-06 17:29:32");
INSERT INTO fat_tybes VALUES ("10","امر شراء","","2024-03-06 17:29:32");
INSERT INTO fat_tybes VALUES ("11","فاتورة تصنيع حر","","2024-03-06 17:29:32");
INSERT INTO fat_tybes VALUES ("12","تصنيع نموذجي","","2024-03-06 17:29:32");



DROP TABLE fats;

CREATE TABLE `fats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fat_id` int(11) NOT NULL,
  `zanka_id` int(11) NOT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1012 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE fptybes;

CREATE TABLE `fptybes` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `isdeleted` int(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO fptybes VALUES ("1","حضور","2023-08-01 01:57:14","");
INSERT INTO fptybes VALUES ("2","انصراف","2023-08-01 01:57:14","");
INSERT INTO fptybes VALUES ("3","حضور اضافي","2023-08-01 01:57:42","");
INSERT INTO fptybes VALUES ("4","انصراف اضافي","2023-08-01 01:58:34","");
INSERT INTO fptybes VALUES ("5","invalid","2023-08-10 07:45:50","");



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
  `isdeleted` int(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE holidays;

CREATE TABLE `holidays` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(10) NOT NULL,
  `info` text DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `isdeleted` int(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE imporfplog;

CREATE TABLE `imporfplog` (
  `id` int(1) DEFAULT NULL,
  `basma_id` int(11) NOT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp()
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
  `isdeleted` int(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `gname` (`gname`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO item_group VALUES ("1","عام","","0","2024-07-03 15:43:32","2024-07-06 01:46:38","0","0");
INSERT INTO item_group VALUES ("2","مجموعه 2","","0","2024-07-06 01:52:51","2024-07-06 01:52:51","0","0");
INSERT INTO item_group VALUES ("3","مشروبات ساخنه","","0","2024-07-06 02:05:14","2024-07-06 02:05:14","0","0");
INSERT INTO item_group VALUES ("4","مشروبات بارده","","0","2024-07-06 02:05:26","2024-07-06 02:05:26","0","0");
INSERT INTO item_group VALUES ("5","وجبات سريعه","","0","2024-07-06 02:05:39","2024-07-06 02:05:39","0","0");



DROP TABLE item_group2;

CREATE TABLE `item_group2` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gname` varchar(100) NOT NULL,
  `info` varchar(100) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user` int(11) NOT NULL DEFAULT 0,
  `isdeleted` int(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `gname` (`gname`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO item_group2 VALUES ("1","تصنيف عام","","2024-07-03 15:43:54","2024-07-03 15:43:54","0","0");
INSERT INTO item_group2 VALUES ("2","مشروبات ساخنه","","2024-07-06 02:51:14","2024-07-06 02:52:17","0","1");
INSERT INTO item_group2 VALUES ("3","مشروبات بارده","","2024-07-06 03:39:49","2024-07-06 03:39:49","0","0");
INSERT INTO item_group2 VALUES ("5","مشروبات ساخنة","","2024-07-06 03:43:52","2024-07-06 03:43:52","0","0");



DROP TABLE item_group3;

CREATE TABLE `item_group3` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gname` varchar(100) NOT NULL,
  `info` varchar(200) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user` int(1) NOT NULL DEFAULT 0,
  `isdeleted` int(1) DEFAULT 0,
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
  `unit_barcode` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO item_units VALUES ("29","620","1","1.000","0","0","0","1");
INSERT INTO item_units VALUES ("30","626","1","1.000","0","0","0","2");
INSERT INTO item_units VALUES ("31","627","1","1.000","0","0","0","3");
INSERT INTO item_units VALUES ("32","628","1","1.000","0","0","0","4");
INSERT INTO item_units VALUES ("33","629","1","1.000","0","0","0","5");
INSERT INTO item_units VALUES ("34","630","1","1.000","0","0","0","6");
INSERT INTO item_units VALUES ("35","631","1","1.000","0","0","0","7");
INSERT INTO item_units VALUES ("36","632","1","1.000","0","0","0","8");
INSERT INTO item_units VALUES ("37","633","1","1.000","0","0","0","9");



DROP TABLE joplevels;

CREATE TABLE `joplevels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `info` varchar(250) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `isdeleted` int(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE joprules;

CREATE TABLE `joprules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `info` varchar(250) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `isdeleted` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE jops;

CREATE TABLE `jops` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `info` varchar(250) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `isdeleted` int(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE joptybes;

CREATE TABLE `joptybes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `info` text DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `isdeleted` int(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




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
  `isdeleted` int(11) DEFAULT 0,
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `account_id` (`account_id`),
  KEY `journal_id` (`journal_id`),
  CONSTRAINT `journal_entries_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `acc_head` (`id`),
  CONSTRAINT `journal_entries_ibfk_2` FOREIGN KEY (`journal_id`) REFERENCES `journal_heads` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=533 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO journal_entries VALUES ("529","539","27","1800","0","0","","2024-07-07 00:09:57","0","271","0","2024-07-07 00:09:57");
INSERT INTO journal_entries VALUES ("530","539","36","0","1800","1","","2024-07-07 00:09:57","0","271","0","2024-07-07 00:09:57");
INSERT INTO journal_entries VALUES ("531","540","27","2250","0","0","","2024-07-07 00:10:23","0","272","0","2024-07-07 00:10:23");
INSERT INTO journal_entries VALUES ("532","540","36","0","2250","1","","2024-07-07 00:10:23","0","272","0","2024-07-07 00:10:23");



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
  `isdeleted` int(11) DEFAULT 0,
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user` int(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `journal_heads_ibfk_1` (`pro_tybe`)
) ENGINE=InnoDB AUTO_INCREMENT=541 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO journal_heads VALUES ("539","1","1800","2024-07-06","271","","فاتورة مشتريات _ 271","2024-07-07 00:09:57","0","0","2024-07-07 00:09:57","1");
INSERT INTO journal_heads VALUES ("540","2","2250","2024-07-06","272","","فاتورة مشتريات _ 272","2024-07-07 00:10:23","0","0","2024-07-07 00:10:23","1");



DROP TABLE journal_tybes;

CREATE TABLE `journal_tybes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `journal_id` int(11) DEFAULT NULL,
  `jname` varchar(222) DEFAULT NULL,
  `jtext` varchar(222) DEFAULT NULL,
  `info` varchar(222) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO journal_tybes VALUES ("1","1","purchases","يومية المقبوضات","","2024-03-14 02:34:38","2024-03-14 02:34:38");
INSERT INTO journal_tybes VALUES ("2","2","sales","يومية المدفوعات","","2024-03-14 02:34:38","2024-03-14 02:34:38");
INSERT INTO journal_tybes VALUES ("3","3","Payments","المبيعات","","2024-03-14 02:34:38","2024-03-14 02:34:38");
INSERT INTO journal_tybes VALUES ("4","4","receipts","يومية المشتريات","","2024-03-14 02:34:38","2024-03-14 02:34:38");
INSERT INTO journal_tybes VALUES ("5","5","Accrueds","ايراد مستحق","","2024-03-14 02:34:38","2024-03-14 02:34:38");
INSERT INTO journal_tybes VALUES ("6","6","Accrueds","خصم مكتسب","","2024-03-14 02:34:38","2024-03-14 02:34:38");
INSERT INTO journal_tybes VALUES ("7","7","Accrueds","خصم مسموح به","","2024-03-14 02:34:38","2024-03-14 02:34:38");
INSERT INTO journal_tybes VALUES ("8","8","journal","القيود اليومية","","2024-03-14 02:34:38","2024-03-14 02:34:38");



DROP TABLE karta;

CREATE TABLE `karta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kname` varchar(200) NOT NULL,
  `info` varchar(200) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




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
  `isdeleted` int(11) NOT NULL DEFAULT 0,
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
  PRIMARY KEY (`id`),
  KEY `cl_id` (`cl_id`),
  KEY `rent_id` (`rent_id`),
  KEY `contract` (`contract`),
  CONSTRAINT `myinstallments_ibfk_1` FOREIGN KEY (`cl_id`) REFERENCES `acc_head` (`id`),
  CONSTRAINT `myinstallments_ibfk_2` FOREIGN KEY (`rent_id`) REFERENCES `acc_head` (`id`),
  CONSTRAINT `myinstallments_ibfk_3` FOREIGN KEY (`contract`) REFERENCES `myrents` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




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
  `isdeleted` int(11) DEFAULT 0,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `iname` (`iname`)
) ENGINE=InnoDB AUTO_INCREMENT=634 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO myitems VALUES ("620","jiru","","1","1","1","0","","0","1.5","0","0","0","0","0","0","0","0","2024-07-02 21:39:14","2024-07-02 21:39:14","1");
INSERT INTO myitems VALUES ("626","شاي عادي","","2","1","2","0","","0","0","0","25","0","0","3","1","0","0","2024-07-06 06:41:43","2024-07-06 06:41:43","1");
INSERT INTO myitems VALUES ("627","قهوة","","3","1","3","0","","0","0","0","30","0","0","3","1","0","0","2024-07-06 06:42:05","2024-07-06 06:42:05","1");
INSERT INTO myitems VALUES ("628","اعشاب","","4","1","4","0","","0","0","0","25","0","0","1","1","0","0","2024-07-06 06:42:25","2024-07-06 06:42:25","1");
INSERT INTO myitems VALUES ("629","بيريل","","5","1","5","0","","0","0","0","30","0","0","1","1","0","0","2024-07-06 06:42:56","2024-07-06 06:42:56","1");
INSERT INTO myitems VALUES ("630","سبيرو سباتس","","6","1","6","270","","0","0","15","0","0","0","1","1","0","0","2024-07-06 06:43:11","2024-07-07 00:10:23","1");
INSERT INTO myitems VALUES ("631","ساندوتش رومي","","7","1","7","0","","0","0","0","20","0","0","5","1","0","0","2024-07-06 06:45:24","2024-07-06 06:45:24","1");
INSERT INTO myitems VALUES ("632","سندوتش حلاوة","","8","1","8","0","","0","0","0","15","0","0","1","1","0","0","2024-07-06 06:46:05","2024-07-06 06:46:05","1");
INSERT INTO myitems VALUES ("633","سندوتش حلاوة بالقشطة","","9","1","9","0","","0","0","0","20","0","0","5","1","0","0","2024-07-06 06:46:41","2024-07-06 06:46:41","1");



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
  `det_Currency_unit_convert` decimal(12,6) DEFAULT NULL
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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO myoptions VALUES ("1","def_cl","العميل الافتراضي","24","24","1","2024-02-19 22:10:57","2024-02-19 22:21:14");
INSERT INTO myoptions VALUES ("2","def_prod","المورد الافتراضي","36","36","1","2024-02-19 22:10:57","2024-02-19 22:21:17");
INSERT INTO myoptions VALUES ("3","def_emp","الموظف الافتراضي","41","42","1","2024-02-19 22:10:57","2024-02-20 03:09:18");
INSERT INTO myoptions VALUES ("4","def_store","المخزن الافتراضي","27","27","1","2024-02-19 22:10:57","2024-02-19 22:21:23");
INSERT INTO myoptions VALUES ("5","def_fund","الصندوق الافتراضي","21","21","1","2024-02-19 22:10:57","2024-02-19 22:21:26");
INSERT INTO myoptions VALUES ("6","def_bank","البتك الافتراضي","39","39","1","2024-02-19 22:10:57","2024-02-19 22:21:31");
INSERT INTO myoptions VALUES ("7","def_store","المخزن الافتراضي","27","27","1","2024-02-19 22:10:57","2024-03-09 23:48:39");
INSERT INTO myoptions VALUES ("8","def_disc_acc1","حساب الخصم المكتسب الافتراضي","97","97","1","2024-02-19 22:10:57","2024-03-09 23:48:39");



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
  `t_stamp` timestamp NULL DEFAULT NULL
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
  `isdeleted` int(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `cl_id` (`cl_id`),
  KEY `rent_id` (`rent_id`),
  CONSTRAINT `myrents_ibfk_1` FOREIGN KEY (`cl_id`) REFERENCES `acc_head` (`id`),
  CONSTRAINT `myrents_ibfk_2` FOREIGN KEY (`rent_id`) REFERENCES `acc_head` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE myunits;

CREATE TABLE `myunits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uname` varchar(60) NOT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `isdeleted` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO myunits VALUES ("1","قطعه","2024-03-25 07:56:49","2024-03-25 07:56:49","");
INSERT INTO myunits VALUES ("2","كرتونة","2024-03-25 07:59:05","2024-03-25 07:59:05","");
INSERT INTO myunits VALUES ("3","دسته","2024-03-25 07:59:12","2024-03-25 08:13:25","");



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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE order_status;

CREATE TABLE `order_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `user` int(1) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `isdeleted` int(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE order_types;

CREATE TABLE `order_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `info` varchar(100) DEFAULT NULL,
  `user` int(1) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `isdeleted` int(1) DEFAULT NULL,
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
  `isdeleted` int(1) DEFAULT NULL,
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
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `acc_fund` int(1) DEFAULT 0,
  `op2` int(11) DEFAULT 0,
  `isdeleted` int(11) DEFAULT 0,
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `acc1` (`acc1`),
  KEY `acc2` (`acc2`),
  KEY `emp2_id` (`emp2_id`),
  KEY `emp_id` (`emp_id`),
  KEY `journal_tybe` (`journal_tybe`),
  KEY `user` (`user`),
  KEY `cost_center` (`cost_center`),
  KEY `store_id` (`store_id`),
  KEY `price_list` (`price_list`)
) ENGINE=InnoDB AUTO_INCREMENT=273 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO ot_head VALUES ("271","1","","4","1","","","1","4","","00:09:57","00:09:57","","","2024-07-06","0000-00-00","1","","","","1","27","131","131","27","","","36","","","1800","0","1","0","1800","1800","0","0","0","0","0","0","2024-07-07 00:09:57","0","0","0","2024-07-07 00:09:57","1");
INSERT INTO ot_head VALUES ("272","2","","4","1","","","1","4","","00:10:23","00:10:23","","","2024-07-06","0000-00-00","1","","","","1","27","131","131","27","","","36","","","2250","0","1","0","2250","2250","0","0","0","0","0","0","2024-07-07 00:10:23","0","0","0","2024-07-07 00:10:23","1");



DROP TABLE paper_types;

CREATE TABLE `paper_types` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `pname` varchar(50) NOT NULL,
  `info` varchar(100) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE patt_cols;

CREATE TABLE `patt_cols` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cname` varchar(100) NOT NULL,
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
  `isdeleted` int(11) DEFAULT NULL,
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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE price_lists;

CREATE TABLE `price_lists` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pname` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO price_lists VALUES ("1","سعر 1");
INSERT INTO price_lists VALUES ("2","سعر 2");



DROP TABLE print;

CREATE TABLE `print` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `pname` varchar(50) NOT NULL,
  `type` varchar(11) NOT NULL,
  `number` varchar(11) NOT NULL,
  `info` varchar(100) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO pro_tybes VALUES ("1","سند قبض","","1","","2024-03-14 04:01:35","2024-03-14 04:01:35");
INSERT INTO pro_tybes VALUES ("2","سند دفع","","2","","2024-03-14 04:01:35","2024-03-14 04:02:22");
INSERT INTO pro_tybes VALUES ("3","فاتورة مبيعات","","3","","2024-03-14 04:01:58","2024-03-14 04:02:26");
INSERT INTO pro_tybes VALUES ("4","فاتورة مشتريات","","4","","2024-03-14 04:01:58","2024-03-14 04:02:28");
INSERT INTO pro_tybes VALUES ("5","استحقاق قسط","","5","","2024-03-17 05:53:16","2024-03-17 05:53:27");
INSERT INTO pro_tybes VALUES ("6","خصم مكتسب","","6","","2024-03-17 05:53:16","2024-03-17 05:53:27");
INSERT INTO pro_tybes VALUES ("7","خصم مسموح به","","7","","2024-03-17 05:53:16","2024-03-17 05:53:27");
INSERT INTO pro_tybes VALUES ("8","قيد يومية","","8","","2024-05-14 14:06:41","2024-05-14 14:06:54");



DROP TABLE prods;

CREATE TABLE `prods` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `pname` varchar(50) NOT NULL,
  `info` varchar(100) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE pst_activities;

CREATE TABLE `pst_activities` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `aname` varchar(111) NOT NULL,
  `info` varchar(111) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user` int(1) DEFAULT 0,
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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE services;

CREATE TABLE `services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sname` varchar(50) NOT NULL,
  `info` varchar(100) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE session_time;

CREATE TABLE `session_time` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `user` int(1) NOT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=95 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO session_time VALUES ("55","1","2024-07-02 23:38:02");
INSERT INTO session_time VALUES ("56","1","2024-07-03 14:40:13");
INSERT INTO session_time VALUES ("57","1","2024-07-03 14:40:34");
INSERT INTO session_time VALUES ("58","1","2024-07-03 15:28:33");
INSERT INTO session_time VALUES ("59","1","2024-07-03 15:40:20");
INSERT INTO session_time VALUES ("60","1","2024-07-04 15:18:17");
INSERT INTO session_time VALUES ("61","1","2024-07-04 18:08:28");
INSERT INTO session_time VALUES ("62","1","2024-07-04 19:28:00");
INSERT INTO session_time VALUES ("63","1","2024-07-04 21:57:25");
INSERT INTO session_time VALUES ("64","1","2024-07-04 22:13:21");
INSERT INTO session_time VALUES ("65","1","2024-07-05 01:32:40");
INSERT INTO session_time VALUES ("66","1","2024-07-06 00:59:16");
INSERT INTO session_time VALUES ("67","1","2024-07-06 02:52:40");
INSERT INTO session_time VALUES ("68","1","2024-07-06 15:01:12");
INSERT INTO session_time VALUES ("69","1","2024-07-06 17:05:39");
INSERT INTO session_time VALUES ("70","1","2024-07-06 18:03:22");
INSERT INTO session_time VALUES ("71","1","2024-07-06 18:23:23");
INSERT INTO session_time VALUES ("72","1","2024-07-06 20:07:30");
INSERT INTO session_time VALUES ("73","1","2024-07-06 23:34:04");
INSERT INTO session_time VALUES ("74","1","2024-07-06 23:55:38");
INSERT INTO session_time VALUES ("75","1","2024-07-07 00:44:43");
INSERT INTO session_time VALUES ("76","1","2024-07-07 01:44:37");
INSERT INTO session_time VALUES ("77","1","2024-07-07 15:20:42");
INSERT INTO session_time VALUES ("78","1","2024-07-07 15:38:28");
INSERT INTO session_time VALUES ("79","1","2024-07-07 15:59:21");
INSERT INTO session_time VALUES ("80","1","2024-07-07 16:05:31");
INSERT INTO session_time VALUES ("81","1","2024-07-07 16:09:40");
INSERT INTO session_time VALUES ("82","1","2024-07-07 16:35:24");
INSERT INTO session_time VALUES ("83","1","2024-07-08 03:54:11");
INSERT INTO session_time VALUES ("84","1","2024-07-08 15:13:06");
INSERT INTO session_time VALUES ("85","1","2024-07-08 15:15:17");
INSERT INTO session_time VALUES ("86","1","2024-07-08 18:19:58");
INSERT INTO session_time VALUES ("87","1","2024-07-08 23:09:29");
INSERT INTO session_time VALUES ("88","1","2024-07-08 23:25:14");
INSERT INTO session_time VALUES ("89","1","2024-07-08 23:25:59");
INSERT INTO session_time VALUES ("90","1","2024-07-09 03:16:16");
INSERT INTO session_time VALUES ("91","1","2024-07-09 06:54:55");
INSERT INTO session_time VALUES ("92","1","2024-07-09 07:05:20");
INSERT INTO session_time VALUES ("93","1","2024-07-09 14:45:59");
INSERT INTO session_time VALUES ("94","1","2024-07-09 17:54:30");



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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO settings VALUES ("1","SKY TEAM","سمنود - برج زايد - الدور الخامس","abdelhadeeladawy@gmail.com","010053662038","1","d35c99e7485691ea14f829029dc03e69A67b8d2f92148f52cad46e331936922e8","","99","2024-01-01","2024-12-31","ar","#534191","1","1","1","1","1","1","1");



DROP TABLE shifts;

CREATE TABLE `shifts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `info` text DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `isdeleted` int(1) DEFAULT NULL,
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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO shifts VALUES ("5","ورديه الاداره","","2023-08-05 04:37:08","","12:00:00","19:00:00","","11:00:00","14:00:00","17:00:00","23:59:00","15","15","6,7,1,2,3,4");



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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO shw_optns VALUES ("1","op_id","0","50","50","معرف العملية","2024-03-13 19:54:16","2024-03-13 20:32:33");
INSERT INTO shw_optns VALUES ("2","op_date","0","50","50","التاريخ","2024-03-13 20:32:08","2024-03-13 20:32:46");
INSERT INTO shw_optns VALUES ("3","op_tybe","0","50","50","نوع العمليه","2024-03-13 20:32:08","2024-03-13 20:32:54");
INSERT INTO shw_optns VALUES ("4","op_store","0","50","50","المستودع","2024-03-13 20:32:08","2024-03-13 20:32:08");
INSERT INTO shw_optns VALUES ("5","op_num","0","50","50","رقم السند","2024-03-13 20:32:08","2024-03-13 20:32:08");
INSERT INTO shw_optns VALUES ("6","acc_num","0","50","50","رقم الحساب","2024-03-13 20:32:08","2024-03-13 20:32:08");
INSERT INTO shw_optns VALUES ("7","acc_id","0","50","50","اسم الحساب","2024-03-13 20:32:08","2024-03-13 20:32:08");
INSERT INTO shw_optns VALUES ("8","op_val","0","50","50","قيمه العمليه","2024-03-13 20:32:08","2024-03-13 20:32:08");
INSERT INTO shw_optns VALUES ("9","op_profit","0","50","50","الربح","2024-03-13 20:32:08","2024-03-13 20:33:07");
INSERT INTO shw_optns VALUES ("10","emb_id","0","50","50","البائع","2024-03-13 20:32:08","2024-03-13 20:33:15");
INSERT INTO shw_optns VALUES ("11","usid","0","50","50","المستخدم","2024-03-13 20:32:08","2024-03-13 20:33:22");
INSERT INTO shw_optns VALUES ("12","fatcost","0","50","50","تكلفه المبيعات","2024-03-13 20:32:08","2024-03-13 20:33:28");
INSERT INTO shw_optns VALUES ("13","crtime","0","50","50","الوقت","2024-03-13 20:32:08","2024-03-13 20:32:08");
INSERT INTO shw_optns VALUES ("14","cl_code","0","50","50","رقم العميل","2024-03-13 20:47:10","2024-03-13 20:47:10");
INSERT INTO shw_optns VALUES ("15","cl_id","0","50","50","اسم العميل","2024-03-13 20:47:10","2024-03-13 20:47:10");
INSERT INTO shw_optns VALUES ("16","acc_blance","0","50","50","الرصيد الحالى-بالعمله المحليه","2024-03-13 20:47:10","2024-03-13 20:47:10");
INSERT INTO shw_optns VALUES ("17","acc_cur","0","50","50","عمله الحساب","2024-03-13 20:47:10","2024-03-13 20:47:10");



DROP TABLE sitting_items;

CREATE TABLE `sitting_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `iname` varchar(250) NOT NULL,
  `item_value` int(1) NOT NULL DEFAULT 0,
  `item_description` varchar(250) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO sitting_items VALUES ("1","الموظف يحاسب علي اساس ساعات العمل","0","","2023-12-27 00:32:16","2023-12-27 00:32:16");
INSERT INTO sitting_items VALUES ("2","الموظف يحاسب علي اساس ساعات العمل التقديريه 26 يوم","0","","2023-12-27 00:33:03","2023-12-27 00:33:03");
INSERT INTO sitting_items VALUES ("3","الشهر عباره عن 30 يوم","0","","2023-12-27 00:35:34","2023-12-27 00:35:34");
INSERT INTO sitting_items VALUES ("4","البصمه المفقوده يتم تجاهلها","0","","2023-12-27 00:35:34","2023-12-27 00:35:34");



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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




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
  `isdeleted` int(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE tasktybes;

CREATE TABLE `tasktybes` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `name` varchar(25) NOT NULL,
  `info` text DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `isdeleted` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO tasktybes VALUES ("1","زياره اعطال","","2023-07-27 15:13:07","0");
INSERT INTO tasktybes VALUES ("2","زياره تسويق","","2023-07-27 15:13:07","0");
INSERT INTO tasktybes VALUES ("3","زياره علاقات","","2023-07-27 15:13:07","0");
INSERT INTO tasktybes VALUES ("4","تركيب","","2023-12-23 03:44:24","0");
INSERT INTO tasktybes VALUES ("5","كلينت","","2023-12-23 03:44:24","0");



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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;




DROP TABLE towns;

CREATE TABLE `towns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `info` text DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `isdeleted` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO towns VALUES ("1","سمنود ","","0000-00-00 00:00:00","0");
INSERT INTO towns VALUES ("2","المحله","","0000-00-00 00:00:00","0");
INSERT INTO towns VALUES ("3","المنيا","","0000-00-00 00:00:00","0");
INSERT INTO towns VALUES ("4","اجا","","2023-07-25 22:20:26","0");



DROP TABLE transactions;

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tdate` date NOT NULL,
  `details` varchar(200) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE users;

CREATE TABLE `users` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `uname` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `isdeleted` int(1) DEFAULT 0,
  `usertype` int(11) NOT NULL,
  `userrole` int(11) NOT NULL DEFAULT 1,
  `img` varchar(255) NOT NULL,
  `def_client` int(11) DEFAULT NULL,
  `def_fund` int(11) DEFAULT NULL,
  `def_store` int(11) DEFAULT NULL,
  `def_prod` int(11) DEFAULT NULL,
  `def_emp` int(11) DEFAULT NULL,
  `tasksindex` int(11) DEFAULT NULL,
  `tasksadd` int(11) DEFAULT NULL,
  `tasksedit` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO users VALUES ("1","admin","202cb962ac59075b964b07152d234b70","2022-12-05 17:01:33","0","2","1","22947314.png","","","","","","","1","");



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
  `isdeleted` int(11) DEFAULT 1,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `rollname` (`rollname`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO usr_pwrs VALUES ("1","admin","1","1","1","1","1","1","0","0","0","0","0","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","0","0","0","0","0","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","0","0","1","0","1","0","0","0","1","1","wwww","1","2024-05-12 18:05:26","2024-06-29 19:37:32");
INSERT INTO usr_pwrs VALUES ("2","user","1","0","1","1","1","1","0","1","1","1","1","0","1","1","1","0","1","1","1","1","1","0","1","1","1","1","0","1","1","1","1","0","1","1","1","1","0","1","1","1","1","0","1","1","1","1","0","1","1","1","1","0","1","1","1","1","0","1","1","1","1","0","1","1","1","1","0","1","1","1","1","0","1","1","1","1","0","1","1","1","1","0","1","1","1","1","0","1","1","1","1","0","1","1","1","1","1","1","1","1","1","0","1","1","1","1","0","1","1","1","1","1","0","1","1","1","0","1","1","1","1","0","1","0","1","1","0","1","1","1","1","1","1","1","1","1","0","1","1","1","1","0","1","1","1","1","0","1","1","1","1","0","1","1","1","1","0","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","","1","2024-05-12 18:11:21","2024-05-16 23:34:20");
INSERT INTO usr_pwrs VALUES ("26","دكتور","1","1","1","1","1","1","0","0","0","0","0","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","0","0","0","0","0","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","1","0","0","0","0","1","0","1","0","0","0","0","0","1","1","دكتور","1","2024-05-19 22:04:27","2024-05-24 09:52:09");
INSERT INTO usr_pwrs VALUES ("27","مساعد دكتور","1","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","1","0","0","0","0","0","0","0","0","0","مساعد دكتور","1","2024-05-30 23:48:11","2024-05-31 01:02:13");



DROP TABLE vacancies;

CREATE TABLE `vacancies` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `info` text DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `isdeleted` int(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE visits;

CREATE TABLE `visits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client` int(11) NOT NULL,
  `complaint` varchar(250) DEFAULT NULL,
  `diagnosis` varchar(250) DEFAULT NULL,
  `recommendation` varchar(250) DEFAULT NULL,
  `prescription` varchar(250) DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `mdtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `info` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




DROP TABLE visittybes;

CREATE TABLE `visittybes` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `name` varchar(25) NOT NULL,
  `value` double DEFAULT NULL,
  `crtime` timestamp NOT NULL DEFAULT current_timestamp(),
  `isdeleted` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO visittybes VALUES ("1","كشف 1","400","2023-09-04 02:57:36","0");
INSERT INTO visittybes VALUES ("2","اعادة","250","2023-09-04 02:57:36","0");
INSERT INTO visittybes VALUES ("3","مستعجل","500","2024-05-03 23:57:27","0");
INSERT INTO visittybes VALUES ("4","زيارة 2","500","2024-05-04 20:57:54","0");
INSERT INTO visittybes VALUES ("5","private","800","2024-05-04 20:58:28","1");



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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




