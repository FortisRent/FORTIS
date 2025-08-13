-- MySQL dump 10.13  Distrib 8.0.39, for Linux (x86_64)
--
-- Host: 195.179.239.102    Database: u234488260_octopus
-- ------------------------------------------------------
-- Server version	5.5.5-10.11.8-MariaDB-cll-lve

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `activity`
--

DROP TABLE IF EXISTS `activity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activity` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL,
  `name` varchar(255) NOT NULL,
  `image_url` varchar(128) DEFAULT '',
  `created_at` datetime DEFAULT current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity`
--

LOCK TABLES `activity` WRITE;
/*!40000 ALTER TABLE `activity` DISABLE KEYS */;
INSERT INTO `activity` VALUES (1,'','Run','','2024-03-10 16:13:02',1),(2,'','Walk','','2024-03-10 16:13:02',1),(3,'','Cycle','','2024-03-10 16:13:02',1),(4,'','Swim','','2024-03-10 16:13:02',1),(5,'','Jiu Jitsu','','2024-03-10 16:13:02',1),(6,'','Boxing','','2024-03-10 16:13:02',1),(7,'','Crossfit','','2024-03-10 16:13:02',1),(8,'','Muay Thai','','2024-03-10 16:13:02',1),(9,'','Yoga','','2024-03-10 16:13:02',1),(10,'7xZS0TbtmcXtM8DDTs6brHURRkERKtFL','Leg Press 45','','2024-03-14 20:00:51',1),(11,'izXdP8UmCvNolVnQMKfzPFgDUg6vFlzd','Teste vue','','2024-03-14 20:01:39',1),(12,'XlmORgdOTvRUa8vtVHWTbNBrqnEcm4nW','Leg Press 90','','2024-04-25 21:30:44',1),(13,'8TmCWEqcrJbwE3GwzmQ75p9f584e4Xnz','Supino','','2024-04-25 21:34:48',1);
/*!40000 ALTER TABLE `activity` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `anamnese`
--

DROP TABLE IF EXISTS `anamnese`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `anamnese` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1,
  `user_info` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'anamnese info' CHECK (json_valid(`user_info`)),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `anamnese_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `anamnese`
--

LOCK TABLES `anamnese` WRITE;
/*!40000 ALTER TABLE `anamnese` DISABLE KEYS */;
INSERT INTO `anamnese` VALUES (21,'uAgN90T5AF5M4hbaOZjs5rPSgZibXxbh',10,'2024-04-25 11:10:48',0,'{\"birthdate\":\"1990\\/12\\/24\",\"main_goal\":\"Get active again\",\"training_days\":\"2-3 dias\",\"workout_lifting_weights\":\"2-3 dias\",\"lifting_weights_experience\":\"intermediate\",\"choose_activity\":\"gym\",\"weight\":\"8500\",\"height\":\"186\"}'),(22,'F40nmioQhhxWRxwMze0ODsgOWWNsxhtO',14,'2024-04-25 22:05:16',1,'{\"birthdate\":\"1980\\/04\\/16\",\"main_goal\":\"Gain muscle\",\"training_days\":\"4-5 dias\",\"workout_lifting_weights\":\"2-3 dias\",\"lifting_weights_experience\":\"intermediate\",\"choose_activity\":\"gym\",\"weight\":\"7500\",\"height\":\"170\"}'),(23,'odIJ9GV6a7I7Y6nIItmCEjKpoPwfWRqH',10,'2024-04-26 12:52:51',1,'{\"birthdate\":\"1990\\/05\\/31\",\"main_goal\":\"Loose weight\",\"training_days\":\"2-3 dias\",\"workout_lifting_weights\":\"4-5 dias\",\"lifting_weights_experience\":\"intermediate\",\"choose_activity\":\"bjj\",\"weight\":\"7500\",\"height\":\"186\"}'),(24,'M2WMYVx0gWLJbAJsjLHzTo00EuAHy8RL',12,'2024-04-26 19:39:01',1,'{\"birthdate\":\"1995\\/10\\/16\",\"main_goal\":\"Gain muscle\",\"training_days\":\"4-5 dias\",\"workout_lifting_weights\":\"4-5 dias\",\"lifting_weights_experience\":\"intermediate\",\"choose_activity\":\"gym\",\"weight\":\"7200\",\"height\":\"170\"}'),(25,'7nXhmpEBxGf1UF1rkqxP9BOdICi7CNmh',16,'2024-04-28 12:15:53',1,'{\"birthdate\":\"2000\\/07\\/19\",\"main_goal\":\"Gain muscle\",\"training_days\":\"4-5 dias\",\"workout_lifting_weights\":\"4-5 dias\",\"lifting_weights_experience\":\"intermediate\",\"choose_activity\":\"gym\",\"weight\":\"6800\",\"height\":\"176\"}'),(26,'bKECxZgWCI1XV8MgnZOoeuXZSmsdIhS7',15,'2024-04-29 20:29:34',0,'{\"birthdate\":\"1997\\/11\\/24\",\"main_goal\":\"Gain muscle\",\"training_days\":\"4-5 dias\",\"workout_lifting_weights\":\"4-5 dias\",\"lifting_weights_experience\":\"intermediate\",\"choose_activity\":\"gym\",\"weight\":\"50\",\"height\":\"153\"}'),(27,'ILH4Yjqqh21n7iozUyXt0JUYp4T74g9p',15,'2024-04-29 20:35:15',1,'{\"birthdate\":\"1997\\/11\\/24\",\"main_goal\":\"Gain muscle\",\"training_days\":\"6-7 dias\",\"workout_lifting_weights\":\"6-7 dias\",\"lifting_weights_experience\":\"intermediate\",\"choose_activity\":\"gym\",\"weight\":\"5000\",\"height\":\"153\"}'),(28,'aYiOjSsUnSn23MFwMHeQ9WbveaeOckJa',12,'2024-06-22 18:07:47',1,'{\"birthdate\":\"\",\"main_goal\":\"Loose weight\",\"training_days\":\"2-3 dias\",\"workout_lifting_weights\":\"2-3 dias\",\"lifting_weights_experience\":\"beginner\",\"choose_activity\":\"gym\",\"weight\":\"7500\",\"height\":\"170\"}');
/*!40000 ALTER TABLE `anamnese` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `city`
--

DROP TABLE IF EXISTS `city`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `city` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `geo_state_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `geo_state_id` (`geo_state_id`),
  CONSTRAINT `city_ibfk_1` FOREIGN KEY (`geo_state_id`) REFERENCES `geo_state` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `city`
--

LOCK TABLES `city` WRITE;
/*!40000 ALTER TABLE `city` DISABLE KEYS */;
INSERT INTO `city` VALUES (1,'Florianópolis',24),(2,'São José',24),(3,'Palhoça',24),(4,'Garopaba',24),(5,'Biguaçu',24),(6,'Tijucas',24),(7,'Imbituba',24),(8,'Santo Amaro',24),(9,'São Paulo',25),(10,'Guarulhos',25),(11,'Campinas',25),(12,'Curitiba',16),(13,'Maringá',16),(14,'Porto Alegre',21),(15,'Gramado',21),(16,'Canela',21),(17,'Novo Hamburgo',21);
/*!40000 ALTER TABLE `city` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `country`
--

DROP TABLE IF EXISTS `country`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `country` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `iso` char(2) NOT NULL,
  `name` varchar(80) NOT NULL,
  `nicename` varchar(80) NOT NULL,
  `iso3` char(3) DEFAULT NULL,
  `numcode` smallint(6) DEFAULT NULL,
  `phonecode` int(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=240 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `country`
--

LOCK TABLES `country` WRITE;
/*!40000 ALTER TABLE `country` DISABLE KEYS */;
INSERT INTO `country` VALUES (1,'AF','AFGHANISTAN','Afghanistan','AFG',4,93),(2,'AL','ALBANIA','Albania','ALB',8,355),(3,'DZ','ALGERIA','Algeria','DZA',12,213),(4,'AS','AMERICAN SAMOA','American Samoa','ASM',16,1684),(5,'AD','ANDORRA','Andorra','AND',20,376),(6,'AO','ANGOLA','Angola','AGO',24,244),(7,'AI','ANGUILLA','Anguilla','AIA',660,1264),(8,'AQ','ANTARCTICA','Antarctica',NULL,NULL,0),(9,'AG','ANTIGUA AND BARBUDA','Antigua and Barbuda','ATG',28,1268),(10,'AR','ARGENTINA','Argentina','ARG',32,54),(11,'AM','ARMENIA','Armenia','ARM',51,374),(12,'AW','ARUBA','Aruba','ABW',533,297),(13,'AU','AUSTRALIA','Australia','AUS',36,61),(14,'AT','AUSTRIA','Austria','AUT',40,43),(15,'AZ','AZERBAIJAN','Azerbaijan','AZE',31,994),(16,'BS','BAHAMAS','Bahamas','BHS',44,1242),(17,'BH','BAHRAIN','Bahrain','BHR',48,973),(18,'BD','BANGLADESH','Bangladesh','BGD',50,880),(19,'BB','BARBADOS','Barbados','BRB',52,1246),(20,'BY','BELARUS','Belarus','BLR',112,375),(21,'BE','BELGIUM','Belgium','BEL',56,32),(22,'BZ','BELIZE','Belize','BLZ',84,501),(23,'BJ','BENIN','Benin','BEN',204,229),(24,'BM','BERMUDA','Bermuda','BMU',60,1441),(25,'BT','BHUTAN','Bhutan','BTN',64,975),(26,'BO','BOLIVIA','Bolivia','BOL',68,591),(27,'BA','BOSNIA AND HERZEGOVINA','Bosnia and Herzegovina','BIH',70,387),(28,'BW','BOTSWANA','Botswana','BWA',72,267),(29,'BV','BOUVET ISLAND','Bouvet Island',NULL,NULL,0),(30,'BR','BRAZIL','Brazil','BRA',76,55),(31,'IO','BRITISH INDIAN OCEAN TERRITORY','British Indian Ocean Territory',NULL,NULL,246),(32,'BN','BRUNEI DARUSSALAM','Brunei Darussalam','BRN',96,673),(33,'BG','BULGARIA','Bulgaria','BGR',100,359),(34,'BF','BURKINA FASO','Burkina Faso','BFA',854,226),(35,'BI','BURUNDI','Burundi','BDI',108,257),(36,'KH','CAMBODIA','Cambodia','KHM',116,855),(37,'CM','CAMEROON','Cameroon','CMR',120,237),(38,'CA','CANADA','Canada','CAN',124,1),(39,'CV','CAPE VERDE','Cape Verde','CPV',132,238),(40,'KY','CAYMAN ISLANDS','Cayman Islands','CYM',136,1345),(41,'CF','CENTRAL AFRICAN REPUBLIC','Central African Republic','CAF',140,236),(42,'TD','CHAD','Chad','TCD',148,235),(43,'CL','CHILE','Chile','CHL',152,56),(44,'CN','CHINA','China','CHN',156,86),(45,'CX','CHRISTMAS ISLAND','Christmas Island',NULL,NULL,61),(46,'CC','COCOS (KEELING) ISLANDS','Cocos (Keeling) Islands',NULL,NULL,672),(47,'CO','COLOMBIA','Colombia','COL',170,57),(48,'KM','COMOROS','Comoros','COM',174,269),(49,'CG','CONGO','Congo','COG',178,242),(50,'CD','CONGO, THE DEMOCRATIC REPUBLIC OF THE','Congo, the Democratic Republic of the','COD',180,242),(51,'CK','COOK ISLANDS','Cook Islands','COK',184,682),(52,'CR','COSTA RICA','Costa Rica','CRI',188,506),(53,'CI','COTE D\'IVOIRE','Cote D\'Ivoire','CIV',384,225),(54,'HR','CROATIA','Croatia','HRV',191,385),(55,'CU','CUBA','Cuba','CUB',192,53),(56,'CY','CYPRUS','Cyprus','CYP',196,357),(57,'CZ','CZECH REPUBLIC','Czech Republic','CZE',203,420),(58,'DK','DENMARK','Denmark','DNK',208,45),(59,'DJ','DJIBOUTI','Djibouti','DJI',262,253),(60,'DM','DOMINICA','Dominica','DMA',212,1767),(61,'DO','DOMINICAN REPUBLIC','Dominican Republic','DOM',214,1809),(62,'EC','ECUADOR','Ecuador','ECU',218,593),(63,'EG','EGYPT','Egypt','EGY',818,20),(64,'SV','EL SALVADOR','El Salvador','SLV',222,503),(65,'GQ','EQUATORIAL GUINEA','Equatorial Guinea','GNQ',226,240),(66,'ER','ERITREA','Eritrea','ERI',232,291),(67,'EE','ESTONIA','Estonia','EST',233,372),(68,'ET','ETHIOPIA','Ethiopia','ETH',231,251),(69,'FK','FALKLAND ISLANDS (MALVINAS)','Falkland Islands (Malvinas)','FLK',238,500),(70,'FO','FAROE ISLANDS','Faroe Islands','FRO',234,298),(71,'FJ','FIJI','Fiji','FJI',242,679),(72,'FI','FINLAND','Finland','FIN',246,358),(73,'FR','FRANCE','France','FRA',250,33),(74,'GF','FRENCH GUIANA','French Guiana','GUF',254,594),(75,'PF','FRENCH POLYNESIA','French Polynesia','PYF',258,689),(76,'TF','FRENCH SOUTHERN TERRITORIES','French Southern Territories',NULL,NULL,0),(77,'GA','GABON','Gabon','GAB',266,241),(78,'GM','GAMBIA','Gambia','GMB',270,220),(79,'GE','GEORGIA','Georgia','GEO',268,995),(80,'DE','GERMANY','Germany','DEU',276,49),(81,'GH','GHANA','Ghana','GHA',288,233),(82,'GI','GIBRALTAR','Gibraltar','GIB',292,350),(83,'GR','GREECE','Greece','GRC',300,30),(84,'GL','GREENLAND','Greenland','GRL',304,299),(85,'GD','GRENADA','Grenada','GRD',308,1473),(86,'GP','GUADELOUPE','Guadeloupe','GLP',312,590),(87,'GU','GUAM','Guam','GUM',316,1671),(88,'GT','GUATEMALA','Guatemala','GTM',320,502),(89,'GN','GUINEA','Guinea','GIN',324,224),(90,'GW','GUINEA-BISSAU','Guinea-Bissau','GNB',624,245),(91,'GY','GUYANA','Guyana','GUY',328,592),(92,'HT','HAITI','Haiti','HTI',332,509),(93,'HM','HEARD ISLAND AND MCDONALD ISLANDS','Heard Island and Mcdonald Islands',NULL,NULL,0),(94,'VA','HOLY SEE (VATICAN CITY STATE)','Holy See (Vatican City State)','VAT',336,39),(95,'HN','HONDURAS','Honduras','HND',340,504),(96,'HK','HONG KONG','Hong Kong','HKG',344,852),(97,'HU','HUNGARY','Hungary','HUN',348,36),(98,'IS','ICELAND','Iceland','ISL',352,354),(99,'IN','INDIA','India','IND',356,91),(100,'ID','INDONESIA','Indonesia','IDN',360,62),(101,'IR','IRAN, ISLAMIC REPUBLIC OF','Iran, Islamic Republic of','IRN',364,98),(102,'IQ','IRAQ','Iraq','IRQ',368,964),(103,'IE','IRELAND','Ireland','IRL',372,353),(104,'IL','ISRAEL','Israel','ISR',376,972),(105,'IT','ITALY','Italy','ITA',380,39),(106,'JM','JAMAICA','Jamaica','JAM',388,1876),(107,'JP','JAPAN','Japan','JPN',392,81),(108,'JO','JORDAN','Jordan','JOR',400,962),(109,'KZ','KAZAKHSTAN','Kazakhstan','KAZ',398,7),(110,'KE','KENYA','Kenya','KEN',404,254),(111,'KI','KIRIBATI','Kiribati','KIR',296,686),(112,'KP','KOREA, DEMOCRATIC PEOPLE\'S REPUBLIC OF','Korea, Democratic People\'s Republic of','PRK',408,850),(113,'KR','KOREA, REPUBLIC OF','Korea, Republic of','KOR',410,82),(114,'KW','KUWAIT','Kuwait','KWT',414,965),(115,'KG','KYRGYZSTAN','Kyrgyzstan','KGZ',417,996),(116,'LA','LAO PEOPLE\'S DEMOCRATIC REPUBLIC','Lao People\'s Democratic Republic','LAO',418,856),(117,'LV','LATVIA','Latvia','LVA',428,371),(118,'LB','LEBANON','Lebanon','LBN',422,961),(119,'LS','LESOTHO','Lesotho','LSO',426,266),(120,'LR','LIBERIA','Liberia','LBR',430,231),(121,'LY','LIBYAN ARAB JAMAHIRIYA','Libyan Arab Jamahiriya','LBY',434,218),(122,'LI','LIECHTENSTEIN','Liechtenstein','LIE',438,423),(123,'LT','LITHUANIA','Lithuania','LTU',440,370),(124,'LU','LUXEMBOURG','Luxembourg','LUX',442,352),(125,'MO','MACAO','Macao','MAC',446,853),(126,'MK','MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF','Macedonia, the Former Yugoslav Republic of','MKD',807,389),(127,'MG','MADAGASCAR','Madagascar','MDG',450,261),(128,'MW','MALAWI','Malawi','MWI',454,265),(129,'MY','MALAYSIA','Malaysia','MYS',458,60),(130,'MV','MALDIVES','Maldives','MDV',462,960),(131,'ML','MALI','Mali','MLI',466,223),(132,'MT','MALTA','Malta','MLT',470,356),(133,'MH','MARSHALL ISLANDS','Marshall Islands','MHL',584,692),(134,'MQ','MARTINIQUE','Martinique','MTQ',474,596),(135,'MR','MAURITANIA','Mauritania','MRT',478,222),(136,'MU','MAURITIUS','Mauritius','MUS',480,230),(137,'YT','MAYOTTE','Mayotte',NULL,NULL,269),(138,'MX','MEXICO','Mexico','MEX',484,52),(139,'FM','MICRONESIA, FEDERATED STATES OF','Micronesia, Federated States of','FSM',583,691),(140,'MD','MOLDOVA, REPUBLIC OF','Moldova, Republic of','MDA',498,373),(141,'MC','MONACO','Monaco','MCO',492,377),(142,'MN','MONGOLIA','Mongolia','MNG',496,976),(143,'MS','MONTSERRAT','Montserrat','MSR',500,1664),(144,'MA','MOROCCO','Morocco','MAR',504,212),(145,'MZ','MOZAMBIQUE','Mozambique','MOZ',508,258),(146,'MM','MYANMAR','Myanmar','MMR',104,95),(147,'NA','NAMIBIA','Namibia','NAM',516,264),(148,'NR','NAURU','Nauru','NRU',520,674),(149,'NP','NEPAL','Nepal','NPL',524,977),(150,'NL','NETHERLANDS','Netherlands','NLD',528,31),(151,'AN','NETHERLANDS ANTILLES','Netherlands Antilles','ANT',530,599),(152,'NC','NEW CALEDONIA','New Caledonia','NCL',540,687),(153,'NZ','NEW ZEALAND','New Zealand','NZL',554,64),(154,'NI','NICARAGUA','Nicaragua','NIC',558,505),(155,'NE','NIGER','Niger','NER',562,227),(156,'NG','NIGERIA','Nigeria','NGA',566,234),(157,'NU','NIUE','Niue','NIU',570,683),(158,'NF','NORFOLK ISLAND','Norfolk Island','NFK',574,672),(159,'MP','NORTHERN MARIANA ISLANDS','Northern Mariana Islands','MNP',580,1670),(160,'NO','NORWAY','Norway','NOR',578,47),(161,'OM','OMAN','Oman','OMN',512,968),(162,'PK','PAKISTAN','Pakistan','PAK',586,92),(163,'PW','PALAU','Palau','PLW',585,680),(164,'PS','PALESTINIAN TERRITORY, OCCUPIED','Palestinian Territory, Occupied',NULL,NULL,970),(165,'PA','PANAMA','Panama','PAN',591,507),(166,'PG','PAPUA NEW GUINEA','Papua New Guinea','PNG',598,675),(167,'PY','PARAGUAY','Paraguay','PRY',600,595),(168,'PE','PERU','Peru','PER',604,51),(169,'PH','PHILIPPINES','Philippines','PHL',608,63),(170,'PN','PITCAIRN','Pitcairn','PCN',612,0),(171,'PL','POLAND','Poland','POL',616,48),(172,'PT','PORTUGAL','Portugal','PRT',620,351),(173,'PR','PUERTO RICO','Puerto Rico','PRI',630,1787),(174,'QA','QATAR','Qatar','QAT',634,974),(175,'RE','REUNION','Reunion','REU',638,262),(176,'RO','ROMANIA','Romania','ROM',642,40),(177,'RU','RUSSIAN FEDERATION','Russian Federation','RUS',643,70),(178,'RW','RWANDA','Rwanda','RWA',646,250),(179,'SH','SAINT HELENA','Saint Helena','SHN',654,290),(180,'KN','SAINT KITTS AND NEVIS','Saint Kitts and Nevis','KNA',659,1869),(181,'LC','SAINT LUCIA','Saint Lucia','LCA',662,1758),(182,'PM','SAINT PIERRE AND MIQUELON','Saint Pierre and Miquelon','SPM',666,508),(183,'VC','SAINT VINCENT AND THE GRENADINES','Saint Vincent and the Grenadines','VCT',670,1784),(184,'WS','SAMOA','Samoa','WSM',882,684),(185,'SM','SAN MARINO','San Marino','SMR',674,378),(186,'ST','SAO TOME AND PRINCIPE','Sao Tome and Principe','STP',678,239),(187,'SA','SAUDI ARABIA','Saudi Arabia','SAU',682,966),(188,'SN','SENEGAL','Senegal','SEN',686,221),(189,'CS','SERBIA AND MONTENEGRO','Serbia and Montenegro',NULL,NULL,381),(190,'SC','SEYCHELLES','Seychelles','SYC',690,248),(191,'SL','SIERRA LEONE','Sierra Leone','SLE',694,232),(192,'SG','SINGAPORE','Singapore','SGP',702,65),(193,'SK','SLOVAKIA','Slovakia','SVK',703,421),(194,'SI','SLOVENIA','Slovenia','SVN',705,386),(195,'SB','SOLOMON ISLANDS','Solomon Islands','SLB',90,677),(196,'SO','SOMALIA','Somalia','SOM',706,252),(197,'ZA','SOUTH AFRICA','South Africa','ZAF',710,27),(198,'GS','SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS','South Georgia and the South Sandwich Islands',NULL,NULL,0),(199,'ES','SPAIN','Spain','ESP',724,34),(200,'LK','SRI LANKA','Sri Lanka','LKA',144,94),(201,'SD','SUDAN','Sudan','SDN',736,249),(202,'SR','SURINAME','Suriname','SUR',740,597),(203,'SJ','SVALBARD AND JAN MAYEN','Svalbard and Jan Mayen','SJM',744,47),(204,'SZ','SWAZILAND','Swaziland','SWZ',748,268),(205,'SE','SWEDEN','Sweden','SWE',752,46),(206,'CH','SWITZERLAND','Switzerland','CHE',756,41),(207,'SY','SYRIAN ARAB REPUBLIC','Syrian Arab Republic','SYR',760,963),(208,'TW','TAIWAN, PROVINCE OF CHINA','Taiwan, Province of China','TWN',158,886),(209,'TJ','TAJIKISTAN','Tajikistan','TJK',762,992),(210,'TZ','TANZANIA, UNITED REPUBLIC OF','Tanzania, United Republic of','TZA',834,255),(211,'TH','THAILAND','Thailand','THA',764,66),(212,'TL','TIMOR-LESTE','Timor-Leste',NULL,NULL,670),(213,'TG','TOGO','Togo','TGO',768,228),(214,'TK','TOKELAU','Tokelau','TKL',772,690),(215,'TO','TONGA','Tonga','TON',776,676),(216,'TT','TRINIDAD AND TOBAGO','Trinidad and Tobago','TTO',780,1868),(217,'TN','TUNISIA','Tunisia','TUN',788,216),(218,'TR','TURKEY','Turkey','TUR',792,90),(219,'TM','TURKMENISTAN','Turkmenistan','TKM',795,7370),(220,'TC','TURKS AND CAICOS ISLANDS','Turks and Caicos Islands','TCA',796,1649),(221,'TV','TUVALU','Tuvalu','TUV',798,688),(222,'UG','UGANDA','Uganda','UGA',800,256),(223,'UA','UKRAINE','Ukraine','UKR',804,380),(224,'AE','UNITED ARAB EMIRATES','United Arab Emirates','ARE',784,971),(225,'GB','UNITED KINGDOM','United Kingdom','GBR',826,44),(226,'US','UNITED STATES','United States','USA',840,1),(227,'UM','UNITED STATES MINOR OUTLYING ISLANDS','United States Minor Outlying Islands',NULL,NULL,1),(228,'UY','URUGUAY','Uruguay','URY',858,598),(229,'UZ','UZBEKISTAN','Uzbekistan','UZB',860,998),(230,'VU','VANUATU','Vanuatu','VUT',548,678),(231,'VE','VENEZUELA','Venezuela','VEN',862,58),(232,'VN','VIET NAM','Viet Nam','VNM',704,84),(233,'VG','VIRGIN ISLANDS, BRITISH','Virgin Islands, British','VGB',92,1284),(234,'VI','VIRGIN ISLANDS, U.S.','Virgin Islands, U.s.','VIR',850,1340),(235,'WF','WALLIS AND FUTUNA','Wallis and Futuna','WLF',876,681),(236,'EH','WESTERN SAHARA','Western Sahara','ESH',732,212),(237,'YE','YEMEN','Yemen','YEM',887,967),(238,'ZM','ZAMBIA','Zambia','ZMB',894,260),(239,'ZW','ZIMBABWE','Zimbabwe','ZWE',716,263);
/*!40000 ALTER TABLE `country` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `food`
--

DROP TABLE IF EXISTS `food`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `food` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `calories` int(11) DEFAULT NULL,
  `description` varchar(128) DEFAULT 'NULL',
  `image_url` varchar(128) DEFAULT '',
  `created_at` datetime DEFAULT current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `food_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `food`
--

LOCK TABLES `food` WRITE;
/*!40000 ALTER TABLE `food` DISABLE KEYS */;
INSERT INTO `food` VALUES (1,'mp8iRdF5NYw0h8Iw3OjoZ827NoKSHjqg',6,'Rango teste',150,'0','','2024-03-10 17:55:38',1),(2,'Q8v9eeO6J5jCbXxnnhWKcmogptoadun8',6,'Rango teste',150,'0','','2024-03-10 17:56:13',1),(3,'NmlffPiuGZ3Cli3ESErky6iAqlujnG7I',6,'Rango teste',150,'0','','2024-03-10 17:56:45',1),(4,'5hE8MI9vmAJ3Gk4X73oJggUkGxQPWDbN',6,'Rango teste',150,'0','','2024-03-10 17:59:06',1),(5,'QFzdUXs1DyuWtfbaDQy6AFhTW9Y0A9To',6,'Rango teste',150,'Rango descrição','','2024-03-10 18:01:39',1),(6,'tDeFVVs5uHhjxe5ViOQH3mKfv1YYYlfw',6,'Rango teste123',160,'Rango descrição1','food_img_tDeFVVs5uHhjxe5ViOQH3mKfv1YYYlfw.jpg','2024-03-10 18:06:07',1),(7,'gsTOke23FbrqzWVzCrPl5EKvzAB62gkX',7,'1231',231,'23132','','2024-03-19 15:53:15',1),(8,'KEyc43B69rn85OqNeQmUEG0PseTy0wvV',10,'Frango',12345,'Frango com pure de batatas','','2024-03-24 17:42:40',1),(10,'aGvmr1j0zXwOAb5bjN2i3TA7SopkC3Ll',10,'Teste',123,'<div align=\"left\"><b><u>asdasdaaaaa asdddd<b>hauisdhi</b></u></b></div><div align=\"left\"><b><u><br></u></b></div>','','2024-03-24 22:43:13',1),(11,'2vIoxQ25QeMaRlzpkHtq94NICnVKwruD',11,'Marmita fit',350,'Marmita do monstro<br>','','2024-03-24 23:07:06',1),(12,'j69K6EuE32zGPeAiEiXhJNV5vakjiiyA',16,'Frango ao molho',1800,'Frango<div>Molho</div>','','2024-04-28 12:16:38',1),(13,'PQXH2azkbCcrn9du9qkWx69HO6nArI9o',16,'Frango assado',2000,'Frango<div>Assado</div>','','2024-04-28 12:19:21',1);
/*!40000 ALTER TABLE `food` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `geo_state`
--

DROP TABLE IF EXISTS `geo_state`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `geo_state` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `country_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `country_id` (`country_id`),
  CONSTRAINT `geo_state_ibfk_1` FOREIGN KEY (`country_id`) REFERENCES `country` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `geo_state`
--

LOCK TABLES `geo_state` WRITE;
/*!40000 ALTER TABLE `geo_state` DISABLE KEYS */;
INSERT INTO `geo_state` VALUES (1,'Acre',30),(2,'Alagoas',30),(3,'Amapá',30),(4,'Amazonas',30),(5,'Bahia',30),(6,'Ceará',30),(7,'Distrito Federal',30),(8,'Espírito Santo',30),(9,'Goiás',30),(10,'Maranhão',30),(11,'Mato Grosso',30),(12,'Mato Grosso do Sul',30),(13,'Minas Gerais',30),(14,'Pará',30),(15,'Paraíba',30),(16,'Paraná',30),(17,'Pernambuco',30),(18,'Piauí',30),(19,'Rio de Janeiro',30),(20,'Rio Grande do Norte',30),(21,'Rio Grande do Sul',30),(22,'Rondônia',30),(23,'Roraima',30),(24,'Santa Catarina',30),(25,'São Paulo',30),(26,'Sergipe',30),(27,'Tocantins',30);
/*!40000 ALTER TABLE `geo_state` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inspirational`
--

DROP TABLE IF EXISTS `inspirational`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inspirational` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL,
  `message` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inspirational`
--

LOCK TABLES `inspirational` WRITE;
/*!40000 ALTER TABLE `inspirational` DISABLE KEYS */;
INSERT INTO `inspirational` VALUES (1,'K7rKldS9dRDwvxS9YHPHt9nijg13VQ0s','Mensagem inspiracional','2024-03-10 17:28:33',1),(2,'TjVA8uKKK3E5mmF452N9oQVgiT6mNF9I','Teste mensagem 1','2024-03-19 16:56:21',1),(3,'T9VPbzdM2YSA3KQNuRTJdEiqkpz2Le4J','Teste mensagem 2','2024-03-19 16:56:32',1),(4,'nhx8ohzqBeAt2VtdX0ERDYjW4hLqnakT','Mensagem bonita aqui','2024-04-25 12:07:14',1);
/*!40000 ALTER TABLE `inspirational` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `log_stage`
--

DROP TABLE IF EXISTS `log_stage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `log_stage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `error` int(11) DEFAULT NULL,
  `message` varchar(512) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `log_stage_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `log_stage`
--

LOCK TABLES `log_stage` WRITE;
/*!40000 ALTER TABLE `log_stage` DISABLE KEYS */;
/*!40000 ALTER TABLE `log_stage` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `meal_category`
--

DROP TABLE IF EXISTS `meal_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `meal_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL,
  `name` varchar(255) NOT NULL,
  `image_url` varchar(128) DEFAULT '',
  `created_at` datetime DEFAULT current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `meal_category`
--

LOCK TABLES `meal_category` WRITE;
/*!40000 ALTER TABLE `meal_category` DISABLE KEYS */;
INSERT INTO `meal_category` VALUES (1,'','Dessert','','2024-03-10 16:18:13',1),(2,'','Breakfast','','2024-03-10 16:18:13',1),(3,'','Lunch','','2024-03-10 16:18:13',1),(4,'','Snack','','2024-03-10 16:18:13',1),(5,'','Protein Bar','','2024-03-10 16:18:13',1),(6,'','Dinner','','2024-03-10 16:18:13',1),(7,'','Fruit','','2024-03-10 16:18:13',1),(8,'fBJzXyheVucaKKhhiwdxnmuJI3IVuSKT','Brunch','meal_category_img_fBJzXyheVucaKKhhiwdxnmuJI3IVuSKT.jpg','2024-03-10 18:27:21',1);
/*!40000 ALTER TABLE `meal_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `meal_goal`
--

DROP TABLE IF EXISTS `meal_goal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `meal_goal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `goal` int(11) NOT NULL,
  `calories` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `meal_goal_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `meal_goal`
--

LOCK TABLES `meal_goal` WRITE;
/*!40000 ALTER TABLE `meal_goal` DISABLE KEYS */;
INSERT INTO `meal_goal` VALUES (1,'tnV0IK4RawAnxcD4bmmlf6idZKeeF6K2',NULL,1500,NULL,'2024-03-10 18:45:09',1),(2,'vUpY0hddoVeKzLtRqKeKYilWl0BaxEfH',NULL,1500,NULL,'2024-03-10 18:45:25',1),(3,'oV8Me8LFIQzjNrAn5189kq6DTvEPPPsn',6,1500,NULL,'2024-03-10 18:46:30',0),(4,'tb12aBzK3vQOcgoM19SABjicpU3bENsi',7,123,NULL,'2024-03-19 15:57:10',1),(5,'sbik5B9kDALTn9sUMkcikfPbkDk75RAj',7,1313131,NULL,'2024-03-19 15:57:18',1),(6,'jChJggFYg4Ke2JmAKdIzuvGig2QdK4s0',10,123,NULL,'2024-03-24 17:46:31',1),(7,'hjzX2xoNzaottHobh6qT8RP6ACMobDRb',10,3,NULL,'2024-03-24 22:48:11',1),(8,'G1mRvxwNWgFGA4dttI1WKoFGiN5Z52Tn',11,1500,NULL,'2024-03-24 23:07:54',1),(9,'eJcdjRqctPNqKzmzdQCSnsd4kX3jEUT9',16,3000,NULL,'2024-04-28 12:12:16',1);
/*!40000 ALTER TABLE `meal_goal` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `meal_journal`
--

DROP TABLE IF EXISTS `meal_journal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `meal_journal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL,
  `meal_category_id` int(11) NOT NULL,
  `food_id` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  `description` varchar(128) DEFAULT NULL,
  `time` datetime NOT NULL,
  `image_url` varchar(128) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `food_id` (`food_id`),
  KEY `meal_category_id` (`meal_category_id`),
  CONSTRAINT `meal_journal_ibfk_1` FOREIGN KEY (`food_id`) REFERENCES `food` (`id`),
  CONSTRAINT `meal_journal_ibfk_3` FOREIGN KEY (`meal_category_id`) REFERENCES `meal_category` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `meal_journal`
--

LOCK TABLES `meal_journal` WRITE;
/*!40000 ALTER TABLE `meal_journal` DISABLE KEYS */;
INSERT INTO `meal_journal` VALUES (1,'3HKceJsdE3HN6sNSjNC7rvvDW5ZCX8fQ',2,2,123,'Purê de batatas com frango','2024-03-10 18:46:30','meal_journal_img_3HKceJsdE3HN6sNSjNC7rvvDW5ZCX8fQ.jpg','2024-03-10 19:09:06',1),(2,'BlCSzs4yAoNml1kVogBYwlyRoZM7zEp0',1,1,123,'Purê de batatas com frango','2024-03-10 18:46:30',NULL,'2024-03-10 19:16:00',1),(3,'18w8FfBgdagT2x7l8sDes0lFceD4Mry9',1,6,1231,'233333','2024-03-20 16:12:00',NULL,'2024-03-19 16:12:34',1),(4,'YsoM4TdvxAw7eDqKyxK2Y4rFC5evW1SZ',3,2,123,'1231111','2024-03-20 06:28:00',NULL,'2024-03-19 16:27:58',1),(5,'o9yK7CteCaqlRyeIiXyHag9jiITXq5ow',4,5,111111,'123','2024-03-13 16:28:00',NULL,'2024-03-19 16:28:22',1),(6,'h2hTLO08UGEPAScVgWYzAOuRFHTun3kW',4,8,123,'11111','2025-04-25 06:46:00',NULL,'2024-03-24 17:45:15',1),(7,'iS5JMiIWdEUc98KUFG3u8jtHNCamLrmO',4,8,1000,'1000','2025-04-25 06:47:00',NULL,'2024-03-24 17:46:10',1),(8,'4U7m7oBuoJb80cVmpj9x1mNoVsYgE3d9',2,10,12,'123123333','2025-04-25 23:53:00',NULL,'2024-03-24 22:52:55',1),(9,'ElRwN0KF9ClTZUSZfOvTryABLHaV2r4M',8,8,12333,'12333','2025-04-25 11:54:00',NULL,'2024-03-24 22:53:31',1),(10,'ELBfRhdBA7Qfewb8Ne0WzU0KJEXBgBSz',5,11,500,'monstro','2026-04-25 12:08:00',NULL,'2024-03-24 23:07:38',1),(11,'FSsO8nN8TDB9zNRXgng2j9GYfziMgPJ6',3,12,1900,'Teste<div>Teste</div>','2024-04-29 13:19:00',NULL,'2024-04-28 12:18:43',1);
/*!40000 ALTER TABLE `meal_journal` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pass_recover_token`
--

DROP TABLE IF EXISTS `pass_recover_token`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pass_recover_token` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `code` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `pass_recover_token_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pass_recover_token`
--

LOCK TABLES `pass_recover_token` WRITE;
/*!40000 ALTER TABLE `pass_recover_token` DISABLE KEYS */;
/*!40000 ALTER TABLE `pass_recover_token` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment`
--

DROP TABLE IF EXISTS `payment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL,
  `stripe_id` varchar(255) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `plan_id` int(11) DEFAULT NULL,
  `is_completed` tinyint(1) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `plan_id` (`plan_id`),
  CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `payment_ibfk_2` FOREIGN KEY (`plan_id`) REFERENCES `plan` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment`
--

LOCK TABLES `payment` WRITE;
/*!40000 ALTER TABLE `payment` DISABLE KEYS */;
/*!40000 ALTER TABLE `payment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_method`
--

DROP TABLE IF EXISTS `payment_method`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_method` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_method`
--

LOCK TABLES `payment_method` WRITE;
/*!40000 ALTER TABLE `payment_method` DISABLE KEYS */;
INSERT INTO `payment_method` VALUES (1,'Dinheiro'),(2,'Crédito'),(3,'Débito'),(4,'PIX'),(5,'Cupon');
/*!40000 ALTER TABLE `payment_method` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pill`
--

DROP TABLE IF EXISTS `pill`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pill` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `quantity` int(11) DEFAULT NULL,
  `interval` int(11) DEFAULT NULL,
  `days` int(11) DEFAULT NULL,
  `image_url` varchar(128) DEFAULT '',
  `prescription_url` varchar(128) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `pill_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pill`
--

LOCK TABLES `pill` WRITE;
/*!40000 ALTER TABLE `pill` DISABLE KEYS */;
INSERT INTO `pill` VALUES (1,'ETAZwITAdj28Ay2oMMNmV7jS5KYxpnTf',6,'Ritalina',15,12,15,'pill_img_ETAZwITAdj28Ay2oMMNmV7jS5KYxpnTf.jpg','prescription_img_ETAZwITAdj28Ay2oMMNmV7jS5KYxpnTf.jpg','2024-03-10 15:47:08',1),(2,'yn3fVLI7Ve8YDZXhrGhcuEiPdgIGtfCc',7,'Piriquitona',1,2,3,'',NULL,'2024-03-14 20:49:09',1),(3,'fu5u0M4nCmj60xq3TieQm6R8gsR7nNgY',6,'123',123,1231,23,'',NULL,'2024-03-24 15:28:17',0),(4,'VhXO8AVtUJNgeANWjXKMBziyoWYCrqkz',6,'123',123,1231,23,'',NULL,'2024-03-24 15:28:18',0),(5,'jukRqCLdGGSrWRzrjfV9jcL4xXU8rMg8',6,'Ritalina',1,12,30,'',NULL,'2024-03-24 15:49:22',1),(6,'aAoWrV5BnYHJ3JjyBD8TE0bwfMk30jlA',10,'Creatina',30,24,30,'',NULL,'2024-03-24 17:28:33',0),(7,'hjnq1XEz0V5CdlEuAfXSN2JsDlE0zI2F',10,'Ritalina',15,12,90,'',NULL,'2024-03-24 19:36:57',1),(8,'lU7iiKZ1N0834qV3tKwNWTlH4qaiuN2U',10,'Ritalina',15,12,90,'',NULL,'2024-03-24 19:49:55',1),(9,'EFXbORuPi8X42Ty9vpHMyYhSrLXTiAjf',10,'Ritalina',15,12,90,'',NULL,'2024-03-24 19:49:56',1),(10,'Wx1QalVfJ6Y7X89VtaiJecyy9vpJUwRr',10,'Teste',123,123,123,'',NULL,'2024-03-24 19:50:12',1),(11,'TS1qWrdjM3HdD5k1ZAHVAID1dCbJmPW5',10,'Teste',123,123,123,'',NULL,'2024-03-24 19:50:13',0),(12,'LOyIb3YOJy54riHzrbukpjkrhf0RWN8k',10,'Cocaina',1,12,12,'',NULL,'2024-03-24 20:26:02',1),(13,'xmpmkHRknB9qd5i6pGWcgFwPWaWspvEB',10,'Cocaina',1,12,12,'',NULL,'2024-03-24 20:26:03',1),(14,'v12xzPVXwkZeuuJjXVtbcJTWuW6yvRWD',11,'Ritalina',15,8,90,'',NULL,'2024-03-24 23:04:51',1),(15,'sIBvHcbXadx6hV2qG25Z23I6iUdM6ZrJ',12,'Blend Testosterona ',2000,24,365,'',NULL,'2024-04-28 12:29:41',1),(16,'bScwzkZG0GWjCS9mGQWM0V11StRo9FwB',18,'Teste',48,48,48,'',NULL,'2024-08-29 14:05:19',0),(17,'kGXUomcQhwLS1WbBGHUBGFMQtWIMi9CJ',18,'lalala',456,456,456,'',NULL,'2024-08-29 14:06:50',1),(18,'MdtStTly3Rf2FQ7XbrK6P78wPfp5hhV4',18,'luis',11,45,45,'',NULL,'2024-08-29 14:09:01',1);
/*!40000 ALTER TABLE `pill` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pill_journal`
--

DROP TABLE IF EXISTS `pill_journal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pill_journal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL,
  `pill_id` int(11) DEFAULT NULL,
  `time` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `pill_id` (`pill_id`),
  CONSTRAINT `pill_journal_ibfk_1` FOREIGN KEY (`pill_id`) REFERENCES `pill` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pill_journal`
--

LOCK TABLES `pill_journal` WRITE;
/*!40000 ALTER TABLE `pill_journal` DISABLE KEYS */;
INSERT INTO `pill_journal` VALUES (1,'snSNc20LxjQJkPlZv3VlLCfDUypHMzK3',1,'2024-03-10 14:10:00','2024-03-10 16:22:11',1),(2,'s0g18b8XqlihOjltXBvvGD0wqwTmYEeJ',1,'2024-01-01 01:00:00','2024-03-18 20:26:32',1),(3,'v9alqCIQBIKhT3MCGsH79dmLtsNIZEQo',2,'2024-03-18 20:28:00','2024-03-18 20:27:04',1),(4,'WZONKQMvfZxWzSgoKtyvWNkThGwzEAyc',1,'2024-03-14 12:12:00','2024-03-24 15:55:34',1),(5,'y89fVAHPcZaWfKjfIKZHYnBTVCugRsKS',1,'2024-03-14 12:12:00','2024-03-24 15:55:35',1),(6,'utku7JbtkZlHIVlYLUngBC99L4ZWNCAG',6,'2026-04-25 06:29:00','2024-03-24 17:29:03',1),(7,'thY1S7LUmtF8VSPjL6NIr5dYdtc9O4gH',6,'2025-04-25 03:20:00','2024-03-24 17:30:30',1),(8,'d6RJwethaCPWie6LQfeWOTtRL3ZTFgDI',10,'2024-03-20 09:19:00','2024-03-24 20:18:21',1),(9,'tkOZFxq3RfxB3F2QSqZJAUN5am7GY6mz',10,'2024-03-20 09:19:00','2024-03-24 20:18:22',0),(10,'EzKIKoANacqnNzaV2dn3hpjbeAPeAuZ5',12,'1111-11-11 11:11:00','2024-03-24 20:26:28',1),(11,'xx5KNJzuJ1WS1EVishQJDurDph4y47uT',12,'1111-11-11 11:11:00','2024-03-24 20:26:29',1),(12,'f3iuNjuipOWqYWLmUGFWwoc422UduUYj',7,'1212-12-12 12:12:00','2024-03-24 20:27:30',1),(13,'68PueMLTvyCWZZlG5HwaHyGAMaPxFbyE',14,'2025-04-25 12:06:00','2024-03-24 23:05:04',1),(14,'3D8BCgE8gAcg9nop4Qdd7lQmZASQ4ZZU',14,'2025-04-26 02:01:00','2024-03-25 01:01:01',1);
/*!40000 ALTER TABLE `pill_journal` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `plan`
--

DROP TABLE IF EXISTS `plan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `plan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `amount` int(11) NOT NULL,
  `image_url` varchar(128) DEFAULT '',
  `created_at` datetime DEFAULT current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `plan`
--

LOCK TABLES `plan` WRITE;
/*!40000 ALTER TABLE `plan` DISABLE KEYS */;
/*!40000 ALTER TABLE `plan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `serie`
--

DROP TABLE IF EXISTS `serie`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `serie` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL,
  `training_id` int(11) DEFAULT NULL,
  `activity_id` int(11) DEFAULT NULL,
  `weight` int(11) DEFAULT NULL,
  `rounds` int(11) DEFAULT NULL,
  `repetition` int(11) DEFAULT NULL,
  `interval` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `training_id` (`training_id`),
  KEY `activity_id` (`activity_id`),
  CONSTRAINT `serie_ibfk_1` FOREIGN KEY (`training_id`) REFERENCES `training` (`id`),
  CONSTRAINT `serie_ibfk_2` FOREIGN KEY (`activity_id`) REFERENCES `activity` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `serie`
--

LOCK TABLES `serie` WRITE;
/*!40000 ALTER TABLE `serie` DISABLE KEYS */;
INSERT INTO `serie` VALUES (1,'aZ6VYI17xzr6ZaFWZMXhdUsoaPN0tgn4',2,10,20,4,15,90,'2024-04-25 18:06:38',1),(2,'a1OTaEoQIchmjOsJbpge5YuUo1rUmSIp',2,10,20,4,15,120,'2024-04-25 18:12:41',1),(3,'tQ7Vrhd7CCXACKIvuYqpSS9uHoMSZrRC',7,13,12,3,15,90,'2024-04-25 21:47:58',1),(4,'ONbWiPU1yMpqoYpco9SElB1vdTVSaa7H',7,12,30,4,15,90,'2024-04-25 21:49:16',1),(5,'O1VK1SXWPYCo6enaAHWo4D5XcOpfEiwB',7,12,30,4,15,90,'2024-04-25 21:50:54',1),(6,'POSx83OPsv5OjXCjNAZA1oCc2QPzbGzy',9,13,15,3,12,90,'2024-04-25 22:19:26',1),(7,'U9WjRjbrVgR6Yy6Fhw8Iaewdo7nzlAFT',9,10,50,4,15,120,'2024-04-25 22:20:05',1);
/*!40000 ALTER TABLE `serie` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `serie_journal`
--

DROP TABLE IF EXISTS `serie_journal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `serie_journal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL,
  `serie_id` int(11) DEFAULT NULL,
  `finished_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `serie_id` (`serie_id`),
  CONSTRAINT `serie_journal_ibfk_1` FOREIGN KEY (`serie_id`) REFERENCES `serie` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `serie_journal`
--

LOCK TABLES `serie_journal` WRITE;
/*!40000 ALTER TABLE `serie_journal` DISABLE KEYS */;
INSERT INTO `serie_journal` VALUES (1,'cLQu1DSyOp74Qg0sroPMU82B5DxRzgHS',1,'2024-12-23 12:55:00','2024-04-25 18:46:13',1),(2,'1j7n9flFN6V8qcT34Q5qCBntrOCT4rvi',1,'2024-12-23 12:55:00','2024-04-25 18:47:19',1);
/*!40000 ALTER TABLE `serie_journal` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `training`
--

DROP TABLE IF EXISTS `training`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `training` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `weekday_id` int(11) DEFAULT NULL,
  `name` varchar(64) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `weekday_id` (`weekday_id`),
  CONSTRAINT `training_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `training_ibfk_2` FOREIGN KEY (`weekday_id`) REFERENCES `weekday` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `training`
--

LOCK TABLES `training` WRITE;
/*!40000 ALTER TABLE `training` DISABLE KEYS */;
INSERT INTO `training` VALUES (1,'dDwA5MyN5rNnaaRIubet0vhfihnwUBZh',5,2,'Treino Teste','2024-04-25 17:10:33',1),(2,'42iaWO11wA3dTCqXHQXE5ZKBeh5DkTx1',5,2,'Treino Teste','2024-04-25 17:14:32',1),(3,'f3JXeWHlMrJPDMKtitzABtTxtkqEX3Xm',7,2,'Treino Teste','2024-04-25 17:31:54',0),(4,'DvRqry7QP1u8UAYqMSvnq3363p9xlswN',7,4,'123','2024-04-25 20:01:01',1),(5,'xzTEKvmRhOaHZkeshCqLSNjrzseuiCrn',10,2,'Treino A','2024-04-25 20:02:13',1),(6,'BDW5zfXoDD76K71QnEhqYrKNEmq3u32v',10,4,'Treino B','2024-04-25 20:16:44',1),(7,'TTPHu0Ot9E8WuIiLKUbsw8FpZJ0WFt2X',6,2,'Treino A','2024-04-25 21:38:55',1),(8,'IXF1AvZYievPFSKoa93oeyZeUoNhJNXD',10,1,'Treinamento pra matar o fdp','2024-04-25 21:58:53',1),(9,'aQ1LEYW88EMA0kmO7Jl6qnGr7WCOnPpF',14,2,'Treino A','2024-04-25 22:17:43',1);
/*!40000 ALTER TABLE `training` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `unit`
--

DROP TABLE IF EXISTS `unit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `unit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `unit`
--

LOCK TABLES `unit` WRITE;
/*!40000 ALTER TABLE `unit` DISABLE KEYS */;
INSERT INTO `unit` VALUES (1,'Kg'),(2,'Gramas'),(3,'Porção'),(4,'Garrafa'),(5,'Lata'),(6,'Litro'),(7,'Pacote'),(8,'Unidade'),(9,'Maço');
/*!40000 ALTER TABLE `unit` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(128) NOT NULL,
  `image_url` varchar(128) DEFAULT '',
  `created_at` datetime DEFAULT current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1,
  `is_admin` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (5,'','luis','luis@teste.com',NULL,'12345','','2024-03-10 16:55:49',1,1),(6,'EAX4QcC8hOp7KE8rr8H4C5HHK0VdWHKs','Luis Zimermann','luis2@teste.com',NULL,'8cb2237d0679ca88db6464eac60da96345513964','','2024-03-10 13:59:19',1,0),(7,'2RTeMWlT7r1VXXnGvQu1D8xvHRhj9QWE','Luis','luis@form.com',NULL,'b8abef497d665c9673e064b42d0c45ec5b8f0b9b','','2024-03-12 18:34:35',1,0),(8,'iKeBNZwVr69kbMQhz1osaZ4ODV0ShZsN','Luis Vue','vue@gmail.com',NULL,'ba3da472cb1a59f523b87f74c4e42c860c2aa5d0','','2024-03-12 21:20:48',1,0),(9,'n5YVJ9gVJjLi9fDWYfVoGGmJb2iSSPAk','Admin Teste','adm@omni.com',NULL,'8cb2237d0679ca88db6464eac60da96345513964','','2024-03-24 16:26:31',1,1),(10,'c4tk3oRY58fSvxIl9HzF75rCRQ2MJKFU','Cliente Teste','client@omni.com',NULL,'8cb2237d0679ca88db6464eac60da96345513964','','2024-03-24 16:28:55',1,0),(11,'733efHYZ7dmGp2EdhmJyu3lhKSJiKjKG','Luis Quasar','luis@quasar.com',NULL,'8cb2237d0679ca88db6464eac60da96345513964','','2024-03-24 23:01:58',1,0),(12,'HkPAeq4gpvbypFgud52YaMUXZZxMxYDZ','Euller Moura','euller@omnifitness.com.br',NULL,'6572732dc85afdaf6479a70a9e4d666d162921a3','','2024-03-25 20:01:23',1,0),(13,'zqTerG3OmURuDIDcjiegms5E7jrw55lk','Amanda Portela ','amanda.salaz.96@gmail.com',NULL,'84761bcca8136f0dff8a10d24e03a41bebbc42ba','','2024-03-25 21:40:40',1,0),(14,'YWe8ZMk0tBkWAocwf6U64EaEQwUe2EMu','Euller','euller@teste.com',NULL,'8cb2237d0679ca88db6464eac60da96345513964','','2024-04-25 22:02:30',1,0),(15,'8wbKSdyAdL7YOqi1iqgBzfa8mKZx213Q','Damares marques de moraes ','damaresmarques95@gmail.com',NULL,'303ab014e83fe2adadefa06654091719596b05dd','','2024-04-26 19:41:13',1,0),(16,'6HdnKs9OBCAFPCgguwTgiDg80hTHmg4H','Wagdo','wagdobragajunior@gmail.com',NULL,'6bcc1a723ab131873ccaf0fb165ca4b821ee80cb','','2024-04-27 17:09:39',1,0),(17,'nmYq89y3HzY8VKYwcRjs45pfsthwZvv8','Wagdo','wagdobragajunior@gmail.com',NULL,'6bcc1a723ab131873ccaf0fb165ca4b821ee80cb','','2024-04-27 17:09:39',1,0),(18,'WjjsM7RgZX6v4VFS8EPv4w3iOPN355OK','Luis','testeluis@teste.com',NULL,'8cb2237d0679ca88db6464eac60da96345513964','','2024-08-29 14:03:12',1,0);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `water_goal`
--

DROP TABLE IF EXISTS `water_goal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `water_goal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `goal` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `water_goal_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `water_goal`
--

LOCK TABLES `water_goal` WRITE;
/*!40000 ALTER TABLE `water_goal` DISABLE KEYS */;
INSERT INTO `water_goal` VALUES (1,'Pf9ZkqYr9ECkiRwSKOmfX7hCyaNRu360',6,2800,'2024-03-10 15:09:22',0),(2,'Fs6JHzT7SHBkZT3LaGpU2OVU73IGhATA',7,55,'2024-03-14 20:13:58',0),(3,'SOsRAGvebJsWT7zdE6vgiExlUppBXxS0',10,123,'2024-03-24 17:27:30',1),(4,'yNNSlP2g6ALRGq9HzBS109zLLrXLyPWH',10,1800,'2024-03-24 18:53:15',1),(5,'JBBLY2yxFHIvx4ZH04pyHAlDaijULB5s',10,1800,'2024-03-24 18:53:32',1),(6,'WwPXxh4ws3aVfr467YQgbLdQ8clC5DNT',10,1800,'2024-03-24 18:53:58',1),(7,'Ozuf1mw7oZISUpMqz5ryxjq12ThO9Inj',10,1200,'2024-03-24 19:00:13',0),(8,'zEq2QoZ0RUDLvBXwzz7xtF8QcETKkzfN',10,2800,'2024-03-24 19:00:22',0),(9,'8o0shNnke7Aq6EdvrCUUqpfOPszmervX',NULL,2800,'2024-03-24 23:02:28',1),(10,'McSQRE2lldxqZCvGum7HIpUtgt1mapE0',11,2500,'2024-03-24 23:04:04',1),(11,'3wyaTsxKgoHbjZALidF45V8keZ2mjwsI',13,3000,'2024-03-25 21:51:47',1),(12,'FNfnPEhLF4HcJLTNHC5WG9zFTrJMRA30',12,4000,'2024-04-26 13:02:59',1),(13,'sKsNxvzeYPY4VXgwN6cbvsK0Qss2xQlK',16,2000,'2024-04-28 12:20:28',0),(14,'OTrmxF0uRTsLjBDzxMBiapeAb83nYm5g',15,4,'2024-04-29 20:26:37',1);
/*!40000 ALTER TABLE `water_goal` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `water_journal`
--

DROP TABLE IF EXISTS `water_journal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `water_journal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `time` datetime DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `water_journal_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `water_journal`
--

LOCK TABLES `water_journal` WRITE;
/*!40000 ALTER TABLE `water_journal` DISABLE KEYS */;
INSERT INTO `water_journal` VALUES (1,'hqfyGZjl1Fd09gMi2c4cfCJRfH9Cv4M1',6,'2024-03-10 14:15:00',250,'2024-03-10 15:23:39',1),(2,'6aHWjc98r7edaGeMJDZpCByL9f2KfWYT',6,'2024-03-10 14:10:00',200,'2024-03-10 15:27:11',1),(3,'DcdyEdTNWnYMkwu9KL5vZXoNMWE5isfm',6,'2024-03-10 14:10:00',200,'2024-03-10 15:28:43',1),(4,'k6PruiHE2nBbNRAn2YuukK58yocdbVCK',7,'2024-03-16 20:37:00',250,'2024-03-14 20:38:05',1),(5,'YOSuVbKPR4m5lnkneC2EJgKq5nDKSA9y',7,'2024-01-01 02:00:00',55,'2024-03-14 20:38:21',1),(6,'gLc9o0A8Sv32TYuYvkNHbyKWR6jYHYvj',10,'2025-04-25 18:28:00',1000,'2024-03-24 17:27:53',0),(7,'tvY3BuxQ4QXhcQ8dmV22pxWQxzXIgXii',10,'0000-00-00 00:00:00',123,'2024-03-24 19:28:10',1),(8,'4kVLm4XrvLTSFmLyk3Iyu8OFErEWJAQz',10,'0000-00-00 00:00:00',500,'2024-03-24 19:28:30',1),(9,'vvauoSmjRowtWeiU37LmS79cdAySGGK7',10,'1111-11-11 11:11:00',11111,'2024-03-24 19:29:06',1),(10,'AbSogBRh4gE1HpZUPy3hMzxcDdwllxcG',11,'2025-04-25 12:05:00',500,'2024-03-24 23:04:25',1),(11,'oCxT8fgyCYKj1Dk02iBZ28R9gMBufCgg',13,'2024-03-25 21:52:00',200,'2024-03-25 21:52:11',1),(12,'OWY8I0a0UYS2YwFJ5R7mcXbIR6elekyB',16,'2024-04-29 12:19:00',2000,'2024-04-28 12:19:58',1),(13,'45tl1RkPEC86XSy6C7DBfeLRZ7E2V6u6',15,'2024-04-29 20:37:00',3,'2024-04-29 20:27:44',1);
/*!40000 ALTER TABLE `water_journal` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `weekday`
--

DROP TABLE IF EXISTS `weekday`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `weekday` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `weekday`
--

LOCK TABLES `weekday` WRITE;
/*!40000 ALTER TABLE `weekday` DISABLE KEYS */;
INSERT INTO `weekday` VALUES (1,'Sunday'),(2,'Monday'),(3,'Tuesday'),(4,'Wednesday'),(5,'Thursday'),(6,'Friday'),(7,'Saturday');
/*!40000 ALTER TABLE `weekday` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `workout_goal`
--

DROP TABLE IF EXISTS `workout_goal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `workout_goal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `activity_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `activity_id` (`activity_id`),
  CONSTRAINT `workout_goal_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `workout_goal_ibfk_2` FOREIGN KEY (`activity_id`) REFERENCES `activity` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `workout_goal`
--

LOCK TABLES `workout_goal` WRITE;
/*!40000 ALTER TABLE `workout_goal` DISABLE KEYS */;
INSERT INTO `workout_goal` VALUES (3,'r2POLf3cYdHWgJwh8j29A08ZFHon8cde',6,4,5,'2024-03-10 14:01:49',1),(4,'4EQS7ZVAH0QhYu2haHhZaRnmAxWp6aM8',7,4,4,'2024-03-19 14:48:34',1),(5,'mabz3Nwt8vpbq8xUXS7veh7l6OSGs7bw',7,9,5,'2024-03-19 14:49:11',1),(6,'azkdam83I8yYH542wnyFLCCCDi7DecMj',10,1,10,'2024-03-24 17:39:15',1),(7,'nQsLPPdxEYapMfvKBvsuEWqGHV7FBxOY',NULL,1,1,'2024-03-24 21:40:43',1),(8,'DywHjInGwg3tviAgSbLIZ7qphIdC7ZBY',NULL,5,3,'2024-03-24 21:41:24',1),(9,'rhpxCFpsMcb0FwjpVzh2QecO86WDPcM6',NULL,5,3,'2024-03-24 21:44:12',1),(10,'nIp0ChqmJJFuO5ba3zprVtV4qNNRqEMh',10,5,3,'2024-03-24 21:45:58',1),(11,'IKChRTV2wuRwR4kDs6OzpQRoScDq0Iz9',10,6,4,'2024-03-24 21:52:42',1),(12,'aAaliP2GC152VTSLh22wjNJxTl25b1jw',10,8,5,'2024-03-24 21:53:04',1),(13,'juFyZJ1zwI5xTxmYzAye1DCec48l7E4O',11,5,3,'2024-03-24 23:05:30',1),(14,'R7nCgB13txTX6usyFxb3jMD747HtQp45',11,6,3,'2024-03-25 00:57:59',1),(15,'7Nb5rBmqazENL98A5LtIvKCmMWRiZAt6',10,8,5,'2024-03-25 20:50:38',1),(16,'LqO4IbIPnjtmhiCIBB8AmwPVhx5kq3Ud',10,5,1,'2024-04-15 17:46:57',1);
/*!40000 ALTER TABLE `workout_goal` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `workout_journal`
--

DROP TABLE IF EXISTS `workout_journal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `workout_journal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `activity_id` int(11) DEFAULT NULL,
  `started_at` datetime DEFAULT NULL,
  `finished_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `activity_id` (`activity_id`),
  CONSTRAINT `workout_journal_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `workout_journal_ibfk_2` FOREIGN KEY (`activity_id`) REFERENCES `activity` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `workout_journal`
--

LOCK TABLES `workout_journal` WRITE;
/*!40000 ALTER TABLE `workout_journal` DISABLE KEYS */;
INSERT INTO `workout_journal` VALUES (1,'FwHfkzttA0DDvGsMqxVzIgTfwCNPVEVj',6,2,'2024-03-10 14:10:00','2024-03-11 14:55:00','2024-03-10 14:27:55',1),(2,'nZm0fpWZeNlbKjyIHsxSPeAIz40wh6Vg',7,11,'2024-03-26 14:41:00','2024-03-20 16:40:00','2024-03-19 14:39:27',1),(3,'6P2vWaJqxb3F42KsVZuNzAFniYlSD6Rc',7,5,'2024-03-19 14:40:00','2024-03-19 16:30:00','2024-03-19 14:40:47',1),(4,'OJzYxN8xH55F3jyfTamPOmATTSa0noQE',7,4,'2024-03-21 14:45:00','2024-03-13 14:45:00','2024-03-19 14:45:31',1),(5,'Vm614C1SEF0ucRVrGRB3bWnWhKNNpY0Q',10,1,'1111-01-01 06:41:00','1111-01-01 07:48:00','2024-03-24 17:40:33',1),(6,'fjvtmfNKyYCnrL37S9mBiZPcfAwle3zt',10,5,'2025-04-25 11:05:00','2025-04-25 11:25:00','2024-03-24 22:06:21',1),(7,'3l4g59YPCeLzga8koRsXkcRUrdOn1xmS',11,5,'2025-04-25 12:00:00','2025-04-25 13:00:00','2024-03-24 23:06:08',1),(8,'k2hQKK1Bz6y3P2gkBb4d6J9W4IkyYwY3',11,6,'2025-04-26 01:59:00','2025-04-26 02:59:00','2024-03-25 00:58:34',1),(9,'kYFFynQRCJ5nG5w91QYef7s3TmjZDs6S',10,8,'2024-01-01 01:00:00','2024-01-01 02:15:00','2024-03-25 20:51:14',1),(10,'HPUUpsACIINkzWpZ3IhOVxfWY5x2bnom',10,5,'2024-04-01 17:47:00','2024-04-30 17:47:00','2024-04-15 17:47:50',1),(11,'DULmNhNoFyvz1eqDMSjxxUkrtE33zEWW',16,2,'2024-04-29 12:13:00','2024-04-29 14:15:00','2024-04-28 12:13:24',1);
/*!40000 ALTER TABLE `workout_journal` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `workout_plan`
--

DROP TABLE IF EXISTS `workout_plan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `workout_plan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `image_url` varchar(128) DEFAULT '',
  `created_at` datetime DEFAULT current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `workout_plan`
--

LOCK TABLES `workout_plan` WRITE;
/*!40000 ALTER TABLE `workout_plan` DISABLE KEYS */;
INSERT INTO `workout_plan` VALUES (1,NULL,'Lose weight','image_url','2024-03-10 16:10:30',1),(2,NULL,'Build Muscle','image_url','2024-03-10 16:10:30',1),(3,NULL,'Keep Fit','image_url','2024-03-10 16:10:30',1);
/*!40000 ALTER TABLE `workout_plan` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-08-30 12:01:38
