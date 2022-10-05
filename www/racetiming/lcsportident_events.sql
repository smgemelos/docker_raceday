-- MySQL dump 10.13  Distrib 5.5.49, for Win32 (AMD64)
--
-- Host: localhost    Database: lcsportident_events
-- ------------------------------------------------------
-- Server version	5.5.49-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `lccard_link_locations`
--

DROP TABLE IF EXISTS `lccard_link_locations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lccard_link_locations` (
  `id_lccard_link_locations` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_card` int(10) unsigned NOT NULL,
  `id_locations` int(10) unsigned NOT NULL,
  `last_modification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_lccard_link_locations`),
  KEY `id_card_link_id_location` (`id_card`,`id_locations`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lccard_link_locations`
--

LOCK TABLES `lccard_link_locations` WRITE;
/*!40000 ALTER TABLE `lccard_link_locations` DISABLE KEYS */;
/*!40000 ALTER TABLE `lccard_link_locations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lccard_link_stamps`
--

DROP TABLE IF EXISTS `lccard_link_stamps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lccard_link_stamps` (
  `id_card_link_stamps` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_card` int(11) unsigned NOT NULL,
  `id_stamp` int(11) unsigned NOT NULL,
  `last_modification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_card_link_stamps`),
  KEY `id_card_link_id_stamp` (`id_card`,`id_stamp`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lccard_link_stamps`
--

LOCK TABLES `lccard_link_stamps` WRITE;
/*!40000 ALTER TABLE `lccard_link_stamps` DISABLE KEYS */;
INSERT INTO `lccard_link_stamps` VALUES (1,1,6,'2016-10-26 11:42:13'),(2,1,7,'2016-10-26 11:42:13'),(3,1,8,'2016-10-26 11:42:13'),(4,2,9,'2016-10-26 11:44:55'),(5,2,10,'2016-10-26 11:44:55'),(6,2,11,'2016-10-26 11:44:55'),(7,2,12,'2016-10-26 11:44:56'),(8,2,13,'2016-10-26 11:44:56'),(9,2,14,'2016-10-26 11:44:56'),(10,3,15,'2016-10-26 11:44:58'),(11,3,16,'2016-10-26 11:44:58'),(12,3,17,'2016-10-26 11:44:58'),(13,4,18,'2016-10-26 11:47:42'),(14,4,19,'2016-10-26 11:47:42'),(15,4,20,'2016-10-26 11:47:42'),(16,4,21,'2016-10-26 11:47:42'),(17,5,22,'2016-10-26 11:48:29'),(18,5,23,'2016-10-26 11:48:29'),(19,5,24,'2016-10-26 11:48:29'),(20,5,25,'2016-10-26 11:48:29'),(21,5,26,'2016-10-26 11:48:29'),(22,5,27,'2016-10-26 11:48:29');
/*!40000 ALTER TABLE `lccard_link_stamps` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lccards`
--

DROP TABLE IF EXISTS `lccards`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lccards` (
  `id_card` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_event` int(11) unsigned NOT NULL,
  `card_id` int(10) unsigned NOT NULL,
  `card_readout_datetime` datetime NOT NULL,
  `card_start_no` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `card_first_name` varchar(60) COLLATE utf8_unicode_ci DEFAULT NULL,
  `card_last_name` varchar(60) COLLATE utf8_unicode_ci DEFAULT NULL,
  `card_club` varchar(60) COLLATE utf8_unicode_ci DEFAULT NULL,
  `card_country` varchar(60) COLLATE utf8_unicode_ci DEFAULT NULL,
  `card_sex` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `card_date_of_birth` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `card_email` varchar(60) COLLATE utf8_unicode_ci DEFAULT NULL,
  `card_phone` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `card_mobile` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `card_street` varchar(60) COLLATE utf8_unicode_ci DEFAULT NULL,
  `card_zip` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `card_city` varchar(60) COLLATE utf8_unicode_ci DEFAULT NULL,
  `card_hardware_version` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `card_software_version` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `card_battery_date` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `card_battery_voltage` int(10) NOT NULL DEFAULT '-1',
  `card_clear_count` int(5) NOT NULL DEFAULT '0',
  `card_character_set` int(3) NOT NULL DEFAULT '0',
  `card_feedback` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `params` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `last_modification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_card`),
  KEY `id_event_link_id_card` (`id_card`,`id_event`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lccards`
--

LOCK TABLES `lccards` WRITE;
/*!40000 ALTER TABLE `lccards` DISABLE KEYS */;
INSERT INTO `lccards` VALUES (1,1,8639381,'2016-10-26 04:42:13','','','','','','','','','','','','','','1.0','1.0','0001-01-01',0,0,0,'','card_complete=1|','2016-10-26 11:42:13'),(2,1,8639382,'2016-10-26 04:44:55','','','','','','','','','','','','','','1.0','1.0','0001-01-01',0,0,0,'','card_complete=1|','2016-10-26 11:44:55'),(3,1,8639382,'2016-10-26 04:44:58','','','','','','','','','','','','','','1.0','1.0','0001-01-01',0,0,0,'','card_complete=1|','2016-10-26 11:44:58'),(4,1,8639383,'2016-10-26 04:47:42','','','','','','','','','','','','','','1.0','1.0','0001-01-01',0,0,0,'','card_complete=1|','2016-10-26 11:47:42'),(5,1,8639383,'2016-10-26 04:48:29','','','','','','','','','','','','','','1.0','1.0','0001-01-01',0,0,0,'','card_complete=1|','2016-10-26 11:48:29');
/*!40000 ALTER TABLE `lccards` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lcevents`
--

DROP TABLE IF EXISTS `lcevents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lcevents` (
  `id_event` int(11) unsigned NOT NULL COMMENT 'Primary key',
  `id_country` int(4) unsigned DEFAULT NULL COMMENT 'Foreign key for the country, the event takes place in',
  `event_foreign_id` varchar(15) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL COMMENT 'Foreign event identifer',
  `event_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Event name',
  `event_begin` datetime NOT NULL DEFAULT '2000-01-01 00:00:00' COMMENT 'Start datetime of the end',
  `event_end` datetime NOT NULL DEFAULT '2020-12-31 00:00:00' COMMENT 'End datetime of the end',
  `event_discipline` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Event discipline',
  `event_form` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Event form',
  `event_organiser` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `event_picture` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL COMMENT 'Name of the event logo file',
  `event_url` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Event url',
  `event_location` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Event location (venue)',
  `last_modification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_event`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lcevents`
--

LOCK TABLES `lcevents` WRITE;
/*!40000 ALTER TABLE `lcevents` DISABLE KEYS */;
INSERT INTO `lcevents` VALUES (20164,264,'0','China Peak','2016-10-25 00:00:00','2016-10-31 00:00:00','Enduro','SINGLE','CES','0','www.ces.com','California','2016-10-26 11:27:01'),(2008122301,83,'0','SPORTident#1','2008-12-23 00:00:00','2014-12-31 23:59:59','Multisport','SINGLE','SPORTident Germany','2008122301','www.sportident.com','Arnstadt, Thuringia','2009-12-15 23:33:43'),(2008122302,83,'0','SPORTident#2','2008-12-23 00:00:00','2014-12-31 23:59:59','Multisport','SINGLE','SPORTident Germany','2008122301','www.sportident.com','Arnstadt, Thuringia','2010-06-23 04:54:46'),(2008122303,83,'0','SPORTident#3','2008-12-23 00:00:00','2014-12-31 23:59:59','Multisport','SINGLE','SPORTident Germany','2008122301','www.sportident.com','Arnstadt, Thuringia','2009-12-15 23:33:43'),(2008122304,83,'0','SPORTident#4','2008-12-23 00:00:00','2014-12-31 23:59:59','Multisport','SINGLE','SPORTident Germany','2008122301','www.sportident.com','Arnstadt, Thuringia','2009-12-15 23:33:43');
/*!40000 ALTER TABLE `lcevents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `location_link_stamps`
--

DROP TABLE IF EXISTS `location_link_stamps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `location_link_stamps` (
  `id_location_link_stamp` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_location` int(10) unsigned NOT NULL,
  `id_stamp` int(10) unsigned NOT NULL,
  `last_modification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_location_link_stamp`),
  KEY `id_location_link_id_stamp` (`id_location`,`id_stamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `location_link_stamps`
--

LOCK TABLES `location_link_stamps` WRITE;
/*!40000 ALTER TABLE `location_link_stamps` DISABLE KEYS */;
/*!40000 ALTER TABLE `location_link_stamps` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `locations`
--

DROP TABLE IF EXISTS `locations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `locations` (
  `id_location` int(11) NOT NULL AUTO_INCREMENT,
  `latitude` float NOT NULL DEFAULT '0' COMMENT 'Latitude format in floating degrees',
  `longitude` float NOT NULL DEFAULT '0' COMMENT 'Longitude format in floating degrees',
  `height` int(11) NOT NULL DEFAULT '0' COMMENT 'Height above sea level in meters',
  `source` enum('GPS','KNOWN') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'GPS',
  `last_modification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_location`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `locations`
--

LOCK TABLES `locations` WRITE;
/*!40000 ALTER TABLE `locations` DISABLE KEYS */;
/*!40000 ALTER TABLE `locations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stamps`
--

DROP TABLE IF EXISTS `stamps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stamps` (
  `id_stamp` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key',
  `id_event` int(11) unsigned NOT NULL COMMENT 'Foreign key, refering the event',
  `stamp_card_id` int(10) unsigned NOT NULL COMMENT 'SPORTident card number',
  `stamp_control_code` int(4) unsigned NOT NULL DEFAULT '0' COMMENT 'Code number of the control station',
  `stamp_control_mode` int(2) unsigned NOT NULL DEFAULT '0' COMMENT 'Mode of the control station',
  `stamp_station_serial` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Serial number of the source device (readout station or SRR dongle)',
  `stamp_pointer` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Memory pointer of the current punch (where applicable)',
  `stamp_type` int(2) unsigned NOT NULL DEFAULT '0' COMMENT 'Source type of the record',
  `stamp_readout_datetime` datetime NOT NULL COMMENT 'Read timestamp of the record',
  `stamp_punch_timesi` int(12) unsigned NOT NULL COMMENT 'Punching time in SPORTidentTime format',
  `stamp_punch_datetime` datetime NOT NULL COMMENT 'Punching time as fully datetime',
  `stamp_punch_ms` int(4) unsigned NOT NULL COMMENT 'Punching time seconds fraction',
  `stamp_punch_ms_valid` int(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Punching time seconds fraction available (1) or not (0)',
  `stamp_punch_wday` int(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Punching time weekday',
  `stamp_punch_index` int(4) unsigned NOT NULL DEFAULT '0' COMMENT 'Item index in radio package',
  `stamp_punch_count` int(4) unsigned NOT NULL DEFAULT '0' COMMENT 'Overall items count in radio package',
  `stamp_punch_radio_mode` int(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Punch radio mode',
  `stamp_punch_battery_ok` int(1) unsigned NOT NULL DEFAULT '2' COMMENT 'Indicates if the battery is ok (1), not ok (0) or not applicable/available (2)',
  `stamp_punch_card_full` int(1) unsigned NOT NULL DEFAULT '2' COMMENT 'Indicates if the card is full (1), not full (0) or not applicable/available (2)',
  `stamp_punch_beacon_mode` int(1) unsigned NOT NULL DEFAULT '2' COMMENT 'Indicates the beacon mode: Timing mode (0), punching mode (1) or not applicable/available (2)',
  `stamp_punch_gate_mode` int(1) unsigned NOT NULL DEFAULT '2' COMMENT 'Indicates the gate mode: gate mode enabled (1), gate mode disabled (0) or not applicable/available (2)',
  `last_modification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `id_pk` int(11) unsigned DEFAULT '0',
  PRIMARY KEY (`id_stamp`),
  KEY `id_event_link_id_stamp` (`id_stamp`,`id_event`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stamps`
--

LOCK TABLES `stamps` WRITE;
/*!40000 ALTER TABLE `stamps` DISABLE KEYS */;
INSERT INTO `stamps` VALUES (1,2008122303,4015040,2,7,0,0,1,'2010-12-16 14:58:10',55593984,'2000-01-04 12:19:24',0,0,0,0,0,0,2,2,2,2,'2010-12-16 23:04:46',0),(2,2008122303,4015040,2,10,0,0,1,'2010-12-16 15:05:50',55593984,'2000-01-04 12:19:24',0,0,0,0,0,0,2,2,2,2,'2010-12-16 23:05:50',0),(3,2008122303,4015040,31,2,0,0,1,'2010-12-16 15:05:50',55593984,'2000-01-04 12:19:24',0,0,0,0,0,0,2,2,2,2,'2010-12-16 23:05:50',0),(4,2008122303,4015040,16,5,0,104,1,'2010-12-16 15:12:06',102482165,'2012-01-05 15:12:00',957,0,0,0,0,0,2,2,2,2,'2010-12-16 23:12:06',0),(5,2008122303,4015040,16,5,0,112,1,'2010-12-16 15:12:20',102487112,'2012-01-05 15:12:20',281,0,0,0,0,0,2,2,2,2,'2010-12-16 23:12:20',0),(6,1,8639381,1,7,0,0,3,'2016-10-26 04:42:13',78835968,'2000-01-01 13:32:33',0,0,3,0,3,0,1,1,2,0,'2016-10-26 11:42:13',0),(7,1,8639381,1,19,0,0,3,'2016-10-26 04:42:13',70628170,'2000-01-01 04:38:11',289,1,3,1,3,0,0,0,1,0,'2016-10-26 11:42:13',0),(8,1,8639381,11,20,0,0,3,'2016-10-26 04:42:13',70684476,'2000-01-01 04:41:51',234,1,3,2,3,0,0,0,1,0,'2016-10-26 11:42:13',0),(9,1,8639382,1,7,0,0,3,'2016-10-26 04:44:55',78837248,'2000-01-01 13:32:38',0,0,3,0,3,0,1,1,2,0,'2016-10-26 11:44:55',0),(10,1,8639382,1,19,0,0,3,'2016-10-26 04:44:55',70636692,'2000-01-01 04:38:44',578,1,3,1,3,0,0,0,1,0,'2016-10-26 11:44:55',0),(11,1,8639382,11,20,0,0,3,'2016-10-26 04:44:55',70727532,'2000-01-01 04:44:39',421,1,3,2,3,0,0,0,1,0,'2016-10-26 11:44:55',0),(12,1,8639382,1,7,0,0,3,'2016-10-26 04:44:56',78837248,'2000-01-01 13:32:38',0,0,3,0,3,0,1,1,2,0,'2016-10-26 11:44:56',0),(13,1,8639382,1,19,0,0,3,'2016-10-26 04:44:56',70636692,'2000-01-01 04:38:44',578,1,3,1,3,0,0,0,1,0,'2016-10-26 11:44:56',0),(14,1,8639382,11,20,0,0,3,'2016-10-26 04:44:56',70727532,'2000-01-01 04:44:39',421,1,3,2,3,0,0,0,1,0,'2016-10-26 11:44:56',0),(15,1,8639382,1,7,0,0,3,'2016-10-26 04:44:58',78837248,'2000-01-01 13:32:38',0,0,3,0,3,0,1,1,2,0,'2016-10-26 11:44:58',0),(16,1,8639382,1,19,0,0,3,'2016-10-26 04:44:58',70636692,'2000-01-01 04:38:44',578,1,3,1,3,0,0,0,1,0,'2016-10-26 11:44:58',0),(17,1,8639382,11,20,0,0,3,'2016-10-26 04:44:58',70727532,'2000-01-01 04:44:39',421,1,3,2,3,0,0,0,1,0,'2016-10-26 11:44:58',0),(18,1,8639383,1,7,0,0,3,'2016-10-26 04:47:42',78838528,'2000-01-01 13:32:43',0,0,3,0,4,0,1,1,2,0,'2016-10-26 11:47:42',0),(19,1,8639383,1,19,0,0,3,'2016-10-26 04:47:42',70643214,'2000-01-01 04:39:10',54,1,3,1,4,0,0,0,1,0,'2016-10-26 11:47:42',0),(20,1,8639383,1,19,0,0,3,'2016-10-26 04:47:42',70769450,'2000-01-01 04:47:23',164,1,3,2,4,0,0,0,1,0,'2016-10-26 11:47:42',0),(21,1,8639383,11,20,0,0,3,'2016-10-26 04:47:42',70770910,'2000-01-01 04:47:28',867,1,3,3,4,0,0,0,1,0,'2016-10-26 11:47:42',0),(22,1,8639383,1,7,0,0,3,'2016-10-26 04:48:29',78838528,'2000-01-01 13:32:43',0,0,3,0,6,0,1,1,2,0,'2016-10-26 11:48:29',0),(23,1,8639383,1,19,0,0,3,'2016-10-26 04:48:29',70643214,'2000-01-01 04:39:10',54,1,3,1,6,0,0,0,1,0,'2016-10-26 11:48:29',0),(24,1,8639383,1,19,0,0,3,'2016-10-26 04:48:29',70769450,'2000-01-01 04:47:23',164,1,3,2,6,0,0,0,1,0,'2016-10-26 11:48:29',0),(25,1,8639383,11,20,0,0,3,'2016-10-26 04:48:29',70770910,'2000-01-01 04:47:28',867,1,3,3,6,0,0,0,1,0,'2016-10-26 11:48:29',0),(26,1,8639383,1,19,0,0,3,'2016-10-26 04:48:29',70782624,'2000-01-01 04:48:14',625,1,3,4,6,0,0,0,1,0,'2016-10-26 11:48:29',0),(27,1,8639383,1,19,0,0,3,'2016-10-26 04:48:29',70784546,'2000-01-01 04:48:22',132,1,3,5,6,0,0,0,1,0,'2016-10-26 11:48:29',0);
/*!40000 ALTER TABLE `stamps` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-10-26  7:51:03
