-- MySQL dump 10.13  Distrib 5.6.22, for osx10.8 (x86_64)
--
-- Host: localhost    Database: bitsybay
-- ------------------------------------------------------
-- Server version	5.5.43-0+deb8u1

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
-- Table structure for table `category`
--

DROP TABLE IF EXISTS `category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `category` (
  `category_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_category_id` int(10) unsigned DEFAULT NULL,
  `sort_order` int(10) unsigned NOT NULL,
  `alias` varchar(255) NOT NULL,
  PRIMARY KEY (`category_id`),
  KEY `fk_category_category_id` (`parent_category_id`),
  CONSTRAINT `fk_category_category_id` FOREIGN KEY (`parent_category_id`) REFERENCES `category` (`category_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `category_description`
--

DROP TABLE IF EXISTS `category_description`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `category_description` (
  `category_description_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(10) unsigned NOT NULL,
  `language_id` int(10) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  PRIMARY KEY (`category_description_id`),
  UNIQUE KEY `UNIQUE` (`category_id`,`language_id`),
  KEY `fk_category_description_category_id` (`category_id`),
  KEY `fk_category_description_language_id` (`language_id`),
  CONSTRAINT `fk_category_description_category_id` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_category_description_language_id` FOREIGN KEY (`language_id`) REFERENCES `language` (`language_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `currency`
--

DROP TABLE IF EXISTS `currency`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `currency` (
  `currency_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rate` decimal(16,8) NOT NULL,
  `name` varchar(45) NOT NULL,
  `code` varchar(45) NOT NULL,
  `symbol` varchar(45) NOT NULL,
  PRIMARY KEY (`currency_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `language`
--

DROP TABLE IF EXISTS `language`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `language` (
  `language_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(45) NOT NULL,
  `locale` varchar(45) NOT NULL,
  `name` varchar(45) NOT NULL,
  PRIMARY KEY (`language_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `log_404`
--

DROP TABLE IF EXISTS `log_404`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `log_404` (
  `log_404_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `request` varchar(45) NOT NULL,
  `referrer` varchar(45) NOT NULL,
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`log_404_id`),
  KEY `fk_log_404_user_id` (`user_id`),
  CONSTRAINT `fk_log_404_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `log_search`
--

DROP TABLE IF EXISTS `log_search`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `log_search` (
  `log_search_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `results` int(11) NOT NULL,
  `term` varchar(255) DEFAULT NULL,
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`log_search_id`),
  KEY `fk_log_search_user_id` (`user_id`),
  CONSTRAINT `fk_log_search_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `login_attempt`
--

DROP TABLE IF EXISTS `login_attempt`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `login_attempt` (
  `login_attempt_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `login` varchar(255) NOT NULL,
  `ip` varchar(255) NOT NULL,
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`login_attempt_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `login_attempt`
--

LOCK TABLES `login_attempt` WRITE;
/*!40000 ALTER TABLE `login_attempt` DISABLE KEYS */;
/*!40000 ALTER TABLE `login_attempt` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order`
--

DROP TABLE IF EXISTS `order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order` (
  `order_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(10) unsigned DEFAULT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `order_status_id` int(10) unsigned NOT NULL,
  `fee` int(10) NOT NULL,
  `currency_id` int(10) unsigned NOT NULL,
  `amount` decimal(16,8) NOT NULL,
  `license` enum('regular','exclusive') NOT NULL,
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`order_id`),
  UNIQUE KEY `UNIQUE` (`product_id`,`user_id`,`license`,`amount`,`fee`),
  KEY `fk_order_user_id` (`user_id`),
  KEY `fk_order_order_status_id` (`order_status_id`),
  KEY `fk_order_currency_id` (`currency_id`),
  KEY `fk_order_product_id` (`product_id`),
  CONSTRAINT `fk_order_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_order_order_status_id` FOREIGN KEY (`order_status_id`) REFERENCES `order_status` (`order_status_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_order_currency_id` FOREIGN KEY (`currency_id`) REFERENCES `currency` (`currency_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_order_product_id` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `order_status`
--

DROP TABLE IF EXISTS `order_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_status` (
  `order_status_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`order_status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;



--
-- Table structure for table `order_status_description`
--

DROP TABLE IF EXISTS `order_status_description`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_status_description` (
  `order_status_description_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_status_id` int(10) unsigned NOT NULL,
  `language_id` int(10) unsigned NOT NULL,
  `name` varchar(45) NOT NULL,
  PRIMARY KEY (`order_status_description_id`),
  KEY `fk_order_status_description_order_status_id` (`order_status_id`),
  KEY `fk_order_status_description_language_id` (`language_id`),
  CONSTRAINT `fk_order_status_description_order_status_id` FOREIGN KEY (`order_status_id`) REFERENCES `order_status` (`order_status_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_order_status_description_language_id` FOREIGN KEY (`language_id`) REFERENCES `language` (`language_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `product`
--

DROP TABLE IF EXISTS `product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product` (
  `product_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(10) unsigned NOT NULL,
  `currency_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `viewed` int(10) unsigned NOT NULL,
  `regular_price` decimal(16,8) NOT NULL,
  `exclusive_price` decimal(16,8) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `date_added` datetime NOT NULL,
  `date_modified` datetime NOT NULL,
  `withdraw_address` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  PRIMARY KEY (`product_id`),
  UNIQUE KEY `alias_UNIQUE` (`alias`),
  KEY `fk_product_user_id` (`user_id`),
  KEY `fk_product_category_id` (`category_id`),
  KEY `fk_product_currency_id` (`currency_id`),
  CONSTRAINT `fk_product_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_product_category_id` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_product_currency_id` FOREIGN KEY (`currency_id`) REFERENCES `currency` (`currency_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `product_demo`
--

DROP TABLE IF EXISTS `product_demo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_demo` (
  `product_demo_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(10) unsigned NOT NULL,
  `sort_order` int(10) unsigned NOT NULL,
  `url` varchar(255) NOT NULL,
  `main` enum('1','0') DEFAULT NULL,
  PRIMARY KEY (`product_demo_id`),
  KEY `fk_product_demo_product_id` (`product_id`),
  CONSTRAINT `fk_product_demo_product_id` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_demo`
--

LOCK TABLES `product_demo` WRITE;
/*!40000 ALTER TABLE `product_demo` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_demo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_demo_description`
--

DROP TABLE IF EXISTS `product_demo_description`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_demo_description` (
  `product_demo_description_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_demo_id` int(10) unsigned NOT NULL,
  `language_id` int(10) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  PRIMARY KEY (`product_demo_description_id`),
  UNIQUE KEY `UNIQUE` (`product_demo_id`,`language_id`),
  KEY `fk_product_demo_description_product_id` (`product_demo_id`),
  KEY `fk_product_demo_description_language_id` (`language_id`),
  CONSTRAINT `fk_product_demo_description_product_id` FOREIGN KEY (`product_demo_id`) REFERENCES `product_demo` (`product_demo_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_product_demo_description_language_id` FOREIGN KEY (`language_id`) REFERENCES `language` (`language_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_demo_description`
--

LOCK TABLES `product_demo_description` WRITE;
/*!40000 ALTER TABLE `product_demo_description` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_demo_description` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_description`
--

DROP TABLE IF EXISTS `product_description`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_description` (
  `product_description_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(10) unsigned NOT NULL,
  `language_id` int(10) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`product_description_id`),
  UNIQUE KEY `UNIQUE` (`product_id`,`language_id`),
  KEY `fk_product_description_product_id` (`product_id`),
  KEY `fk_product_description_language_id` (`language_id`),
  CONSTRAINT `fk_product_description_product_id` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_product_description_language_id` FOREIGN KEY (`language_id`) REFERENCES `language` (`language_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `product_favorite`
--

DROP TABLE IF EXISTS `product_favorite`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_favorite` (
  `product_favorite_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`product_favorite_id`),
  UNIQUE KEY `UNIQUE` (`product_id`,`user_id`),
  KEY `fk_product_favorite_product_id` (`product_id`),
  KEY `fk_product_favorite_user_id` (`user_id`),
  CONSTRAINT `fk_product_favorite_product_id` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_product_favorite_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `product_file`
--

DROP TABLE IF EXISTS `product_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_file` (
  `product_file_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(10) unsigned NOT NULL,
  `date_added` datetime NOT NULL,
  `hash_md5` varchar(255) NOT NULL,
  `hash_sha1` varchar(255) NOT NULL,
  PRIMARY KEY (`product_file_id`),
  KEY `fk_product_file_product_id` (`product_id`),
  CONSTRAINT `fk_product_file_product_id` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `product_file_download`
--

DROP TABLE IF EXISTS `product_file_download`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_file_download` (
  `product_file_download_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_file_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`product_file_download_id`),
  KEY `fk_product_file_download_product_file_id` (`product_file_id`),
  KEY `fk_product_file_download_user_id` (`user_id`),
  CONSTRAINT `fk_product_file_download_product_file_id` FOREIGN KEY (`product_file_id`) REFERENCES `product_file` (`product_file_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_product_file_download_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `product_image`
--

DROP TABLE IF EXISTS `product_image`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_image` (
  `product_image_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(10) unsigned NOT NULL,
  `sort_order` int(10) unsigned NOT NULL,
  `main` enum('1','0') NOT NULL,
  `watermark` enum('1','0') NOT NULL,
  `identicon` enum('1','0') NOT NULL,
  PRIMARY KEY (`product_image_id`),
  KEY `fk_product_image_product_id` (`product_id`),
  CONSTRAINT `fk_product_image_product_id` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_image_color`
--

DROP TABLE IF EXISTS `product_image_color`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_image_color` (
  `product_image_color_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_image_id` int(10) unsigned NOT NULL,
  `frequency` int(10) NOT NULL,
  `hex` char(6) NOT NULL,
  `hue` double NOT NULL,
  `saturation` double NOT NULL,
  `value` double NOT NULL,
  `red` smallint(1) NOT NULL,
  `green` smallint(1) NOT NULL,
  `blue` smallint(1) NOT NULL,
  PRIMARY KEY (`product_image_color_id`),
  KEY `fk_product_image_color_product_image_id` (`product_image_id`),
  CONSTRAINT `fk_product_image_color_product_image_id` FOREIGN KEY (`product_image_id`) REFERENCES `product_image` (`product_image_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `product_image_description`
--

DROP TABLE IF EXISTS `product_image_description`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_image_description` (
  `image_description_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_image_id` int(10) unsigned NOT NULL,
  `language_id` int(10) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  PRIMARY KEY (`image_description_id`),
  UNIQUE KEY `UNIQUE` (`product_image_id`,`language_id`),
  KEY `fk_product_image_description_product_image_id` (`product_image_id`),
  KEY `fk_product_image_description_language_id` (`language_id`),
  CONSTRAINT `fk_product_image_description_product_image_id` FOREIGN KEY (`product_image_id`) REFERENCES `product_image` (`product_image_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_product_image_description_language_id` FOREIGN KEY (`language_id`) REFERENCES `language` (`language_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;



--
-- Table structure for table `product_report`
--

DROP TABLE IF EXISTS `product_report`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_report` (
  `product_report_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(10) unsigned DEFAULT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `date_added` datetime NOT NULL,
  `message` text,
  PRIMARY KEY (`product_report_id`),
  KEY `fk_product_report_product_id` (`product_id`),
  KEY `fk_product_report_user_id` (`user_id`),
  CONSTRAINT `fk_product_report_product_id` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_product_report_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `product_review`
--

DROP TABLE IF EXISTS `product_review`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_review` (
  `product_review_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `language_id` int(10) unsigned NOT NULL,
  `status` enum('1','0') NOT NULL,
  `review` text NOT NULL,
  `date_added` datetime NOT NULL,
  `date_modified` datetime NOT NULL,
  PRIMARY KEY (`product_review_id`),
  KEY `fk_product_review_product_id` (`product_id`),
  KEY `fk_product_review_user_id` (`user_id`),
  KEY `fk_product_review_language_id` (`language_id`),
  CONSTRAINT `fk_product_review_product_id` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_product_review_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_product_review_language_id` FOREIGN KEY (`language_id`) REFERENCES `language` (`language_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_review`
--

LOCK TABLES `product_review` WRITE;
/*!40000 ALTER TABLE `product_review` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_review` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_special`
--

DROP TABLE IF EXISTS `product_special`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_special` (
  `product_special_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(10) unsigned NOT NULL,
  `sort_order` int(10) unsigned NOT NULL,
  `date_start` date NOT NULL,
  `date_end` date NOT NULL,
  `regular_price` decimal(16,8) NOT NULL,
  `exclusive_price` decimal(16,8) NOT NULL,
  PRIMARY KEY (`product_special_id`),
  KEY `fk_product_special_product_id` (`product_id`),
  CONSTRAINT `fk_product_special_product_id` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `product_to_tag`
--

DROP TABLE IF EXISTS `product_to_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_to_tag` (
  `product_to_tag_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(10) unsigned NOT NULL,
  `tag_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`product_to_tag_id`),
  UNIQUE KEY `UNIQUE` (`product_id`,`tag_id`),
  KEY `fk_product_to_tag_product_id` (`product_id`),
  KEY `fk_product_to_tag_tag_id` (`tag_id`),
  CONSTRAINT `fk_product_to_tag_product_id` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_product_to_tag_tag_id` FOREIGN KEY (`tag_id`) REFERENCES `tag` (`tag_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `product_video`
--

DROP TABLE IF EXISTS `product_video`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_video` (
  `product_video_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(10) unsigned NOT NULL,
  `sort_order` int(10) unsigned NOT NULL,
  `reduce` enum('1','0') NOT NULL,
  PRIMARY KEY (`product_video_id`),
  KEY `fk_product_video_product_id` (`product_id`),
  CONSTRAINT `fk_product_video_product_id` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `product_video_description`
--

DROP TABLE IF EXISTS `product_video_description`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_video_description` (
  `product_video_description_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_video_id` int(10) unsigned NOT NULL,
  `language_id` int(10) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  PRIMARY KEY (`product_video_description_id`),
  UNIQUE KEY `UNIQUE` (`language_id`,`product_video_id`),
  KEY `fk_product_video_description_language_id` (`language_id`),
  KEY `fk_product_video_description_product_video_id` (`product_video_id`),
  CONSTRAINT `fk_product_video_description_language_id` FOREIGN KEY (`language_id`) REFERENCES `language` (`language_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_product_video_description_product_video_id` FOREIGN KEY (`product_video_id`) REFERENCES `product_video` (`product_video_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_audio`
--

DROP TABLE IF EXISTS `product_audio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_audio` (
  `product_audio_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(10) unsigned NOT NULL,
  `sort_order` int(10) unsigned NOT NULL,
  `cut` enum('1','0') NOT NULL,
  PRIMARY KEY (`product_audio_id`),
  KEY `fk_product_audio_product_id` (`product_id`),
  CONSTRAINT `fk_product_audio_product_id` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `product_audio_description`
--

DROP TABLE IF EXISTS `product_audio_description`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_audio_description` (
  `product_audio_description_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_audio_id` int(10) unsigned NOT NULL,
  `language_id` int(10) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  PRIMARY KEY (`product_audio_description_id`),
  UNIQUE KEY `UNIQUE` (`language_id`,`product_audio_id`),
  KEY `fk_product_audio_description_language_id` (`language_id`),
  KEY `fk_product_audio_description_product_audio_id` (`product_audio_id`),
  CONSTRAINT `fk_product_audio_description_language_id` FOREIGN KEY (`language_id`) REFERENCES `language` (`language_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_product_audio_description_product_audio_id` FOREIGN KEY (`product_audio_id`) REFERENCES `product_audio` (`product_audio_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `redirect`
--

DROP TABLE IF EXISTS `redirect`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `redirect` (
  `redirect_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` int(11) NOT NULL,
  `requested` int(11) NOT NULL,
  `uri_from` varchar(500) NOT NULL,
  `uri_to` varchar(500) NOT NULL,
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`redirect_id`),
  KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `tag`
--

DROP TABLE IF EXISTS `tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tag` (
  `tag_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `tag_description`
--

DROP TABLE IF EXISTS `tag_description`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tag_description` (
  `tag_description_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tag_id` int(10) unsigned NOT NULL,
  `language_id` int(10) unsigned NOT NULL,
  `name` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`tag_description_id`),
  UNIQUE KEY `UNIQUE` (`tag_id`,`language_id`),
  KEY `fk_tag_language_id` (`language_id`),
  KEY `fk_tag_tag_id` (`tag_id`),
  CONSTRAINT `fk_tag_language_id` FOREIGN KEY (`language_id`) REFERENCES `language` (`language_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_tag_tag_id` FOREIGN KEY (`tag_id`) REFERENCES `tag` (`tag_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `referrer_user_id` int(10) unsigned NULL,
  `file_quota` int(10) unsigned NOT NULL,
  `status` enum('1','0') NOT NULL,
  `buyer` enum('1','0') NOT NULL,
  `seller` enum('1','0') NOT NULL,
  `contributor` enum('1','0') NOT NULL,
  `approved` enum('1','0') NOT NULL,
  `verified` enum('1','0') NOT NULL,
  `affiliate_currency_id` int(10) unsigned NULL,
  `affiliate_address` varchar(255) NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `approval_code` varchar(255) NOT NULL,
  `salt` varchar(9) NOT NULL,
  `date_added` datetime NOT NULL,
  `date_modified` datetime NOT NULL,
  `date_visit` datetime NOT NULL,
  PRIMARY KEY (`user_id`),
  INDEX `fk_user_referrer_user_id` (`referrer_user_id` ASC),
  INDEX `fk_user_affiliate_currency_id` (`affiliate_currency_id` ASC),
  CONSTRAINT `fk_user_referrer_user_id`
    FOREIGN KEY (`referrer_user_id`)
    REFERENCES `user` (`user_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_affiliate_currency_id`
    FOREIGN KEY (`affiliate_currency_id`)
    REFERENCES `currency` (`currency_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;


-- -----------------------------------------------------
-- Table `user_notification`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `user_notification` ;

CREATE TABLE IF NOT EXISTS `user_notification` (
  `user_notification_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `read` TINYINT(1) NOT NULL,
  `label` ENUM('activity', 'secutity', 'news', 'common') NOT NULL,
  `date_added` DATETIME NOT NULL,
  `date_read` DATETIME NOT NULL,
  PRIMARY KEY (`user_notification_id`),
  INDEX `fk_user_notification_user_id` (`user_id` ASC),
  INDEX `fk_user_notification_language_id` (`language_id` ASC),
  CONSTRAINT `fk_user_notification_user_id`
    FOREIGN KEY (`user_id`)
    REFERENCES `user` (`user_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_notification_language_id`
    FOREIGN KEY (`language_id`)
    REFERENCES `language` (`language_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `user_notification_description`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `user_notification_description`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_notification_description` (
  `user_notification_description_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_notification_id` INT UNSIGNED NOT NULL,
  `language_id` INT UNSIGNED NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `description` MEDIUMTEXT NOT NULL,
  PRIMARY KEY (`user_notification_description_id`) ,
  UNIQUE INDEX `UNIQUE` (`user_notification_id` ASC, `language_id` ASC) ,
  KEY `fk_user_notification_description_notification_id` (`user_notification_id` ASC) ,
  KEY `fk_user_notification_description_language_id` (`language_id` ASC) ,
  CONSTRAINT `fk_user_notification_description_notification_id`
  FOREIGN KEY (`user_notification_id`)
  REFERENCES `user_notification` (`user_notification_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_notification_description_language_id`
  FOREIGN KEY (`language_id`)
  REFERENCES `language` (`language_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;


-- -----------------------------------------------------
-- Table `user_password_reset`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `user_password_reset` ;

CREATE TABLE IF NOT EXISTS `user_password_reset` (
  `user_password_reset_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `code` VARCHAR(255) NOT NULL,
  `ip` VARCHAR(255) NOT NULL,
  `date_added` DATETIME NOT NULL,
  PRIMARY KEY (`user_password_reset_id`),
  INDEX `fk_user_password_reset_user_id` (`user_id` ASC),
  CONSTRAINT `fk_user_password_reset_user_id`
    FOREIGN KEY (`user_id`)
    REFERENCES `user` (`user_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

--
-- Table structure for table `user_ip`
--

DROP TABLE IF EXISTS `user_ip`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_ip` (
  `user_ip_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `date_added` datetime NOT NULL,
  `ip` varchar(255) NOT NULL,
  PRIMARY KEY (`user_ip_id`),
  KEY `fk_user_ip_user_id` (`user_id`),
  CONSTRAINT `fk_user_ip_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `user_verification_request`
--

CREATE TABLE IF NOT EXISTS `user_verification_request` (
  `user_verification_request_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `currency_id` INT UNSIGNED NOT NULL,
  `amount` decimal(16,8) NOT NULL,
  `status` ENUM('pending', 'processed', 'approved', 'declined') NOT NULL,
  `address` VARCHAR(255) NOT NULL,
  `code` VARCHAR(255) NOT NULL,
  `proof` LONGTEXT NOT NULL,
  `comment` LONGTEXT NULL,
  `date_added` DATETIME NOT NULL,
  `date_conclusion` DATETIME NULL,
  PRIMARY KEY (`user_verification_request_id`),
  INDEX `fk_user_verification_request_user_id` (`user_id` ASC),
  INDEX `fk_user_verification_request_currency_id` (`currency_id` ASC),
  CONSTRAINT `fk_user_verification_request_user_id`
    FOREIGN KEY (`user_id`)
    REFERENCES `user` (`user_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_verification_request_currency_id`
    FOREIGN KEY (`currency_id`)
    REFERENCES `currency` (`currency_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `subscription`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `subscription` ;

CREATE TABLE IF NOT EXISTS `subscription` (
  `subscription_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`subscription_id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `subscription_description`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `subscription_description` ;

CREATE TABLE IF NOT EXISTS `subscription_description` (
  `subscription_description_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `subscription_id` INT UNSIGNED NOT NULL,
  `language_id` INT UNSIGNED NOT NULL,
  `group` VARCHAR(255) NOT NULL,
  `label` VARCHAR(255) NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`subscription_description_id`),
  INDEX `fk_subscription_description_subscription_id` (`subscription_id` ASC),
  INDEX `fk_subscription_description_language_id` (`language_id` ASC),
  CONSTRAINT `fk_subscription_description_subscription_id`
    FOREIGN KEY (`subscription_id`)
    REFERENCES `subscription` (`subscription_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_subscription_description_language_id`
    FOREIGN KEY (`language_id`)
    REFERENCES `language` (`language_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `user_subscription`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `user_subscription` ;

CREATE TABLE IF NOT EXISTS `user_subscription` (
  `user_subscription_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `subscription_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`user_subscription_id`),
  INDEX `fk_user_subscription_user_id` (`user_id` ASC),
  INDEX `fk_user_subscription_subscription_id` (`subscription_id` ASC),
  CONSTRAINT `fk_user_subscription_user_id`
    FOREIGN KEY (`user_id`)
    REFERENCES `user` (`user_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_subscription_subscription_id`
    FOREIGN KEY (`subscription_id`)
    REFERENCES `subscription` (`subscription_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `license`
-- -----------------------------------------------------

CREATE TABLE `license` (
  `license_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sort_order` int(11) NOT NULL,
  `code` varchar(45) NOT NULL,
  PRIMARY KEY (`license_id`),
  UNIQUE KEY `UNIQUE` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- -----------------------------------------------------
-- Table `license_description`
-- -----------------------------------------------------

CREATE TABLE `license_description` (
  `license_description_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `license_id` int(10) unsigned NOT NULL,
  `language_id` int(10) unsigned NOT NULL,
  `name` varchar(45) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`license_description_id`),
  UNIQUE KEY `UNIQUE` (`license_id`,`language_id`),
  KEY `fk_license_description_license_id` (`license_id`),
  KEY `fk_license_description_language_id` (`language_id`),
  CONSTRAINT `fk_license_description_license_id` FOREIGN KEY (`license_id`) REFERENCES `license` (`license_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_license_description_language_id` FOREIGN KEY (`language_id`) REFERENCES `language` (`language_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- -----------------------------------------------------
-- Table `license_condition`
-- -----------------------------------------------------

CREATE TABLE `license_condition` (
  `license_condition_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `license_id` int(10) unsigned NOT NULL,
  `sort_order` int(11) NOT NULL,
  `optional` enum('1','0') NOT NULL,
  PRIMARY KEY (`license_condition_id`),
  KEY `fk_ license_condition_license_id` (`license_id`),
  CONSTRAINT `fk_ license_condition_license_id` FOREIGN KEY (`license_id`) REFERENCES `license` (`license_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- -----------------------------------------------------
-- Table `license_condition_description`
-- -----------------------------------------------------

CREATE TABLE `license_condition_description` (
  `license_condition_description_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `license_condition_id` int(10) unsigned NOT NULL,
  `language_id` int(10) unsigned NOT NULL,
  `condition` varchar(255) NOT NULL,
  PRIMARY KEY (`license_condition_description_id`),
  UNIQUE KEY `UNIQUE` (`license_condition_id`,`language_id`),
  KEY `fk_ license_condition_description_language_id` (`language_id`),
  KEY `fk_ license_condition_description_license_condition_id` (`license_condition_id`),
  CONSTRAINT `fk_ license_condition_description_language_id` FOREIGN KEY (`language_id`) REFERENCES `language` (`language_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_ license_condition_description_license_condition_id` FOREIGN KEY (`license_condition_id`) REFERENCES `license_condition` (`license_condition_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

  -- -----------------------------------------------------
  -- Table `product_to_license_condition`
  -- -----------------------------------------------------

CREATE TABLE `product_to_license_condition` (
  `product_to_license_condition_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(10) unsigned NOT NULL,
  `license_condition_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`product_to_license_condition_id`),
  UNIQUE KEY `UNIQUE` (`product_id`,`license_condition_id`),
  KEY `fk_product_to_license_condition_product_id` (`product_id`),
  KEY `fk_product_to_license_condition_license_condition` (`license_condition_id`),
  CONSTRAINT `fk_product_to_license_condition_product_id` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_product_to_license_condition_license_condition` FOREIGN KEY (`license_condition_id`) REFERENCES `license_condition` (`license_condition_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Structure for view `catalog` (SPHINX SEARCH INDEX SOURCE)
--

CREATE VIEW `catalog` AS SELECT `pd`.`product_id`  AS `product_id`,
                                `pd`.`language_id` AS `language_id`,
                                `pd`.`title`       AS `title`,

                                (SELECT GROUP_CONCAT(`cd`.`title`) FROM `category_description` AS `cd`
                                                                   JOIN `category` AS `c` ON (`c`.`category_id` = `cd`.`category_id`)
                                                                   JOIN `product`  AS `p` ON (`p`.`category_id` = `c`.`category_id`)
                                                                   WHERE `p`.`product_id` = `pd`.`product_id` AND `cd`.`language_id` = `pd`.`language_id`) AS `category`,

                                (SELECT GROUP_CONCAT(`td`.`name`)  FROM `tag_description` AS `td`
                                                                   JOIN `product_to_tag`  AS `p2t` ON (`p2t`.`tag_id` = `td`.`tag_id`)
                                                                   WHERE `p2t`.`product_id` = `pd`.`product_id` AND `td`.`language_id` = `pd`.`language_id`) AS `tags`

                                FROM `product_description` AS `pd`;


/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-06-30 20:07:54
