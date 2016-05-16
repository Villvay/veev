-- MySQL dump 10.15  Distrib 10.0.23-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: veev
-- ------------------------------------------------------
-- Server version	10.0.23-MariaDB

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
-- Table structure for table `app`
--

DROP TABLE IF EXISTS `app`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `app` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner` bigint(20) DEFAULT NULL,
  `title` varchar(32) DEFAULT NULL,
  `urls` text,
  `clientid` varchar(32) DEFAULT NULL,
  `secret` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `app`
--

LOCK TABLES `app` WRITE;
/*!40000 ALTER TABLE `app` DISABLE KEYS */;
/*!40000 ALTER TABLE `app` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `content`
--

DROP TABLE IF EXISTS `content`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `content` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `pid` bigint(20) DEFAULT '-1',
  `author` bigint(20) DEFAULT '-1',
  `category` int(11) DEFAULT '-1',
  `published` datetime DEFAULT NULL,
  `lang` varchar(8) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `title` varchar(250) NOT NULL,
  `content` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `content`
--

LOCK TABLES `content` WRITE;
/*!40000 ALTER TABLE `content` DISABLE KEYS */;
INSERT INTO `content` VALUES (1,-1,-1,-1,'2016-05-13 22:21:35','en','home','Minimalistic Framework for Rapid Web Weaving','<p>xxxxxxxxx</p>'),(2,-1,-1,-1,'2016-05-14 11:47:13','en','about','About Us','<p>dsfghdf sdfh sdfgsdf gsdfgasdfg</p>'),(3,-1,-1,-1,'2016-05-14 11:58:08','en','contact','Contact Us','<p>vishva@villvay.com</p>\r\n<p>094 77 944&nbsp;79 15</p>');
/*!40000 ALTER TABLE `content` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `login`
--

DROP TABLE IF EXISTS `login`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `login` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `cookie` varchar(32) DEFAULT NULL,
  `remember` int(1) NOT NULL,
  `ip` varchar(16) DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `session` varchar(32) DEFAULT NULL,
  `useragent` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `login`
--

LOCK TABLES `login` WRITE;
/*!40000 ALTER TABLE `login` DISABLE KEYS */;
INSERT INTO `login` VALUES (23,1,'GWGeLaDkQlu4eWVmjNoXo8VFpY',1,'127.0.0.1','2016-05-13 16:25:07','di9db9vmvcl9buric93h4e2r31','{\"platform\":\"Linux\",\"browser\":\"Chrome\",\"version\":\"49.0.2623.87\"}');
/*!40000 ALTER TABLE `login` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `organization`
--

DROP TABLE IF EXISTS `organization`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `organization` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  `domain` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `organization`
--

LOCK TABLES `organization` WRITE;
/*!40000 ALTER TABLE `organization` DISABLE KEYS */;
INSERT INTO `organization` VALUES (1,'TinyF(x)','tinyfx','http://32k.co/'),(2,'Veev','veev','https://github.com/villvay/Veev/');
/*!40000 ALTER TABLE `organization` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `id` bigint(20) NOT NULL,
  `organization` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `lang` varchar(8) NOT NULL DEFAULT 'en',
  `timezone` varchar(50) NOT NULL DEFAULT 'UTC/GMT',
  `auth` text,
  `reset_code` varchar(50) NOT NULL,
  `groups` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,1,'admin','c5d06a24b81f64ecd21a66e3cd8940a1','admin@tinyfx.com','en','Asia/Kolkata','{\"dashboard\":[\"full\",\"view\",\"add\",\"edit\",\"delete\"],\"dashboard/developer\":[\"full\",\"view\",\"add\",\"edit\",\"delete\"],\"admin\":[\"full\",\"view\",\"add\",\"edit\",\"delete\"],\"index\":[\"view\"],\"user\":[\"view\"]}','','4'),(2,1,'user','cdf6db7a570d6a469c4f2f1763ea4dc1','0','en','Asia/Kolkata','{\"admin/developer\":[\"view\",\"add\",\"edit\",\"delete\"],\"index\":[\"view\"],\"user\":[\"view\",\"edit\"],\"news\":[\"view\",\"add\",\"edit\",\"delete\"],\"news/dashboard\":[\"view\",\"add\",\"edit\",\"delete\"]}','',NULL),(4,1,'Managers','[GROUP]','','en','UTC/GMT','{\"admin\":[\"view\"],\"admin/tunnel\":[\"view\"],\"admin/developer\":[\"view\"],\"index\":[\"view\"],\"user\":[\"view\"],\"news\":[\"view\"],\"news/dashboard\":[\"view\"]}','',NULL);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'veev'
--

--
-- Dumping routines for database 'veev'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-05-16  8:38:24
