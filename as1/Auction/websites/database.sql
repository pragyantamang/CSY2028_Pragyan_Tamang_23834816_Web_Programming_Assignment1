-- MariaDB dump 10.19  Distrib 10.5.19-MariaDB, for Linux (x86_64)
--
-- Host: mysql    Database: ijdb
-- ------------------------------------------------------
-- Server version	11.7.2-MariaDB-ubu2404

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `ijdb`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `ijdb` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_ai_ci */;

USE `ijdb`;

--
-- Table structure for table `auctions`
--

DROP TABLE IF EXISTS `auctions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auctions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `categoryId` int(11) NOT NULL,
  `endDate` date NOT NULL,
  `userId` int(11) NOT NULL,
  `imagePath` varchar(255) DEFAULT NULL,
  `price` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `categoryId` (`categoryId`),
  KEY `userId` (`userId`),
  CONSTRAINT `auctions_ibfk_1` FOREIGN KEY (`categoryId`) REFERENCES `categories` (`id`),
  CONSTRAINT `auctions_ibfk_2` FOREIGN KEY (`userId`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auctions`
--

LOCK TABLES `auctions` WRITE;
/*!40000 ALTER TABLE `auctions` DISABLE KEYS */;
INSERT INTO `auctions` VALUES (2,'ccccccccccccccccccc','llllll',1,'2025-05-10',1,NULL,''),(3,'llllllllll',';llllllllllll',3,'2025-03-29',1,'images/auctions/3.jpg',''),(4,'gdhdhwdhhwd','ddjbwkjdbjkqwbd',2,'2025-04-01',1,'images/auctions/3.jpg',''),(5,'vxhbhhbhbh','xjnxjanjxnaj',2,'2025-03-29',1,'images/auctions/3.jpg',''),(6,'gyugygygyg','jhhhhhghgh',1,'2025-05-10',1,'images/auctions/3.jpg','12345678.00'),(7,'nefjnjbdhjsB','fjknjfnjhbfjhB',1,'2025-05-09',1,'images/auctions/3.jpg','12345678'),(8,'nefjnjbdhjsB','fjknjfnjhbfjhB',1,'2025-05-09',1,'images/auctions/3.jpg','12345122.00'),(9,'njsndjsjcc','scn ncjnjnc',4,'2025-05-10',1,'images/auctions/3.jpg','12345678.00'),(10,'gvggvg','fffg',6,'2025-05-09',1,'images/auctions/3.jpg','12345678.00');
/*!40000 ALTER TABLE `auctions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bids`
--

DROP TABLE IF EXISTS `bids`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bids` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `auctionId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `bid_amount` decimal(10,2) NOT NULL,
  `bid_time` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `auctionId` (`auctionId`),
  KEY `userId` (`userId`),
  CONSTRAINT `bids_ibfk_1` FOREIGN KEY (`auctionId`) REFERENCES `auctions` (`id`),
  CONSTRAINT `bids_ibfk_2` FOREIGN KEY (`userId`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bids`
--

LOCK TABLES `bids` WRITE;
/*!40000 ALTER TABLE `bids` DISABLE KEYS */;
INSERT INTO `bids` VALUES (2,3,1,12345122.00,'0000-00-00 00:00:00'),(7,2,1,123.45,'2025-03-26 16:16:53'),(10,2,1,12345122.00,'2025-03-26 16:30:53'),(11,4,1,123456.98,'2025-03-26 17:16:58'),(12,5,1,123456.00,'2025-03-26 17:17:20'),(13,5,1,12345678.00,'2025-03-26 17:18:24'),(14,4,1,12345678.00,'2025-03-26 17:18:54'),(15,6,1,12345678.00,'2025-04-03 14:55:10'),(16,7,1,12345678.00,'2025-04-04 11:21:51'),(17,8,1,12345122.00,'2025-04-06 04:07:53'),(18,9,1,12345678.00,'2025-04-06 04:08:04'),(19,10,1,12345678.00,'2025-04-06 04:08:49');
/*!40000 ALTER TABLE `bids` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Estate'),(2,'Electric'),(3,'Coupe'),(4,'Saloon'),(5,'4x4'),(6,'Sports'),(7,'Hybrid'),(8,'More');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `category`
--

DROP TABLE IF EXISTS `category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `category`
--

LOCK TABLES `category` WRITE;
/*!40000 ALTER TABLE `category` DISABLE KEYS */;
INSERT INTO `category` VALUES (2,'user'),(3,'jjh'),(4,'bids');
/*!40000 ALTER TABLE `category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `auctionId` int(11) NOT NULL,
  `reviewText` varchar(255) NOT NULL,
  `createdate` timestamp NOT NULL,
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`),
  KEY `auctionId` (`auctionId`),
  CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`id`),
  CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`auctionId`) REFERENCES `auctions` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reviews`
--

LOCK TABLES `reviews` WRITE;
/*!40000 ALTER TABLE `reviews` DISABLE KEYS */;
INSERT INTO `reviews` VALUES (1,8,2,'ok','2025-04-06 04:17:16'),(2,8,3,'nice','2025-04-06 04:17:25'),(3,8,4,'lydh','2025-04-06 04:17:33'),(4,8,5,'nice','2025-04-06 04:18:15');
/*!40000 ALTER TABLE `reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `password` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Pragyan','pragyantamang002@gmail.com','$2y$12$KwtfmwfBY8y6396o.AdE0OuLicSS.P.T7SLp86Zf7QnJAVhLzPI1C'),(8,'bhadra','bhadratamang123@gmail.com','$2y$12$0BGfa1oV62oQACL.zDl1gOzaeon/sMqfqMSPyUhOu70/whqzjvyOy');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'ijdb'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-04-06  4:34:43
