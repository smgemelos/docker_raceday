# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 127.0.0.1 (MySQL 5.6.28)
# Database: lcsportident_events
# Generation Time: 2021-10-19 17:06:24 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table raceresults
# ------------------------------------------------------------

CREATE TABLE `raceresults` (
  `id` int(32) unsigned NOT NULL AUTO_INCREMENT,
  `plate` int(10) unsigned NOT NULL,
  `sicard_id` int(10) DEFAULT NULL,
  `name` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `riderid` char(8) COLLATE utf8_unicode_ci DEFAULT NULL,
  `raceid` int(32) DEFAULT NULL,
  `category` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `place` int(11) DEFAULT NULL,
  `total` varchar(14) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ttotal` float(14,3) DEFAULT NULL,
  `penalty` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tpenalty` int(11) DEFAULT NULL,
  `stages` int(11) DEFAULT NULL,
  `ranktotal` float(14,3) DEFAULT NULL,
  `s1` varchar(14) COLLATE utf8_unicode_ci DEFAULT NULL,
  `s2` varchar(14) COLLATE utf8_unicode_ci DEFAULT NULL,
  `s3` varchar(14) COLLATE utf8_unicode_ci DEFAULT NULL,
  `s4` varchar(14) COLLATE utf8_unicode_ci DEFAULT NULL,
  `s5` varchar(14) COLLATE utf8_unicode_ci DEFAULT NULL,
  `s6` varchar(14) COLLATE utf8_unicode_ci DEFAULT NULL,
  `s7` varchar(14) COLLATE utf8_unicode_ci DEFAULT NULL,
  `s8` varchar(14) COLLATE utf8_unicode_ci DEFAULT NULL,
  `s9` varchar(14) COLLATE utf8_unicode_ci DEFAULT NULL,
  `s10` varchar(14) COLLATE utf8_unicode_ci DEFAULT NULL,
  `s11` varchar(14) COLLATE utf8_unicode_ci DEFAULT NULL,
  `s12` varchar(14) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dnf` char(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dq` char(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  `last_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `t1` float(14,3) DEFAULT NULL,
  `t101` float(14,3) DEFAULT NULL,
  `t2` float(14,3) DEFAULT NULL,
  `t102` float(14,3) DEFAULT NULL,
  `t3` float(14,3) DEFAULT NULL,
  `t103` float(14,3) DEFAULT NULL,
  `t4` float(14,3) DEFAULT NULL,
  `t104` float(14,3) DEFAULT NULL,
  `t5` float(14,3) DEFAULT NULL,
  `t105` float(14,3) DEFAULT NULL,
  `t6` float(14,3) DEFAULT NULL,
  `t106` float(14,3) DEFAULT NULL,
  `t7` float(14,3) DEFAULT NULL,
  `t107` float(14,3) DEFAULT NULL,
  `t8` float(14,3) DEFAULT NULL,
  `t108` float(14,3) DEFAULT NULL,
  `t9` float(14,3) DEFAULT NULL,
  `t109` float(14,3) DEFAULT NULL,
  `t10` float(14,3) DEFAULT NULL,
  `t110` float(14,3) DEFAULT NULL,
  `t11` float(14,3) DEFAULT NULL,
  `t111` float(14,3) DEFAULT NULL,
  `t12` float(14,3) DEFAULT NULL,
  `t112` float(14,3) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
