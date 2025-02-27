-- MySQL dump 10.13  Distrib 8.0.41, for Linux (x86_64)
--
-- Host: localhost    Database: Restaurant_DB
-- ------------------------------------------------------
-- Server version	8.0.41-0ubuntu0.22.04.1

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
-- Current Database: `Restaurant_DB`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `Restaurant_DB` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `Restaurant_DB`;

--
-- Table structure for table `CustomerStats`
--

DROP TABLE IF EXISTS `CustomerStats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `CustomerStats` (
  `CustomerID` int NOT NULL,
  `TotalSpent` decimal(10,2) DEFAULT '0.00',
  `LastOrderDate` datetime DEFAULT NULL,
  `OrderCount` int DEFAULT '0',
  PRIMARY KEY (`CustomerID`),
  CONSTRAINT `CustomerStats_ibfk_1` FOREIGN KEY (`CustomerID`) REFERENCES `User` (`UserID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `CustomerStats`
--

LOCK TABLES `CustomerStats` WRITE;
/*!40000 ALTER TABLE `CustomerStats` DISABLE KEYS */;
INSERT INTO `CustomerStats` VALUES (1,450.00,'2024-02-20 16:30:00',2),(2,460.00,'2024-02-25 17:45:00',2),(3,510.00,'2024-03-01 18:20:00',2),(4,150.00,'2024-02-15 15:10:00',1),(5,160.00,'2024-03-05 19:15:00',1),(6,5500.00,'2024-03-18 11:25:00',18),(8,3200.00,'2024-03-16 19:15:00',11),(9,1800.00,'2024-03-15 18:30:00',6),(10,750.00,'2024-03-14 20:20:00',2);
/*!40000 ALTER TABLE `CustomerStats` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `InventoryItem`
--

DROP TABLE IF EXISTS `InventoryItem`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `InventoryItem` (
  `InventoryItemID` int NOT NULL AUTO_INCREMENT,
  `ItemName` varchar(100) NOT NULL,
  `QuantityInStock` decimal(10,2) NOT NULL DEFAULT '0.00',
  `UnitOfMeasurement` varchar(50) NOT NULL,
  `ReorderLevel` decimal(10,2) NOT NULL DEFAULT '0.00',
  `SupplierID` int NOT NULL,
  `LastPurchasedDate` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`InventoryItemID`),
  KEY `fk_InventoryItem_Supplier1_idx` (`SupplierID`),
  CONSTRAINT `fk_InventoryItem_Supplier1` FOREIGN KEY (`SupplierID`) REFERENCES `Supplier` (`SupplierID`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `InventoryItem`
--

LOCK TABLES `InventoryItem` WRITE;
/*!40000 ALTER TABLE `InventoryItem` DISABLE KEYS */;
INSERT INTO `InventoryItem` VALUES (3,'Flour',200.00,'kg',50.00,3,'2024-02-22 22:00:00'),(5,'Olive Oil',30.00,'liters',10.00,1,'2024-02-22 22:00:00'),(6,'Tomatoes',50.00,'kg',20.00,1,'2024-02-23 22:00:00'),(7,'Chicken',100.00,'kg',30.00,2,'2024-02-23 22:00:00'),(8,'Flour',200.00,'kg',50.00,3,'2024-02-22 22:00:00'),(9,'Cheese',40.00,'kg',15.00,2,'2024-02-23 22:00:00'),(10,'Olive Oil',30.00,'liters',10.00,1,'2024-02-22 22:00:00'),(11,'Tomatoes',50.00,'kg',20.00,1,'2024-02-23 22:00:00'),(12,'Chicken',100.00,'kg',30.00,2,'2024-02-23 22:00:00'),(13,'Flour',200.00,'kg',50.00,3,'2024-02-22 22:00:00'),(14,'Cheese',40.00,'kg',15.00,2,'2024-02-23 22:00:00'),(15,'Olive Oil',30.00,'liters',10.00,1,'2024-02-22 22:00:00'),(16,'llll',5.00,'4',1.00,8,NULL);
/*!40000 ALTER TABLE `InventoryItem` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `MenuCategory`
--

DROP TABLE IF EXISTS `MenuCategory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `MenuCategory` (
  `CategoryID` int NOT NULL AUTO_INCREMENT,
  `CategoryName` varchar(100) NOT NULL,
  `Description` text,
  `ImageURL` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`CategoryID`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `MenuCategory`
--

LOCK TABLES `MenuCategory` WRITE;
/*!40000 ALTER TABLE `MenuCategory` DISABLE KEYS */;
INSERT INTO `MenuCategory` VALUES (1,'Main Dishes','Delicious main course meals','assets/images/categories/main.jpg'),(2,'Appetizers','Start your meal right','assets/images/categories/appetizers.jpg'),(3,'Desserts','Sweet endings','assets/images/categories/desserts.jpg'),(4,'ddd','yhii','assets/images/categories/beverages.jpg'),(7,'Desserts','Sweet endings','assets/images/categories/desserts.jpg'),(8,'Beverages','Refreshing drinks','assets/images/categories/beverages.jpg'),(9,'Main Dishes','Delicious main course meals','assets/images/categories/main.jpg'),(10,'Appetizers','Start your meal right','assets/images/categories/appetizers.jpg'),(11,'Desserts','Sweet endings','assets/images/categories/desserts.jpg'),(14,'Appetizers','Starters and small plates',NULL),(15,'Desserts','Sweet treats',NULL);
/*!40000 ALTER TABLE `MenuCategory` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `MenuItem`
--

DROP TABLE IF EXISTS `MenuItem`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `MenuItem` (
  `ItemID` int NOT NULL AUTO_INCREMENT,
  `ItemName` varchar(100) NOT NULL,
  `Description` text,
  `Price` decimal(10,2) NOT NULL,
  `ImageURL` varchar(255) DEFAULT NULL,
  `CategoryID` int NOT NULL,
  `Availability` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`ItemID`),
  KEY `fk_MenuItem_MenuCategory_idx` (`CategoryID`),
  CONSTRAINT `fk_MenuItem_MenuCategory` FOREIGN KEY (`CategoryID`) REFERENCES `MenuCategory` (`CategoryID`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `MenuItem`
--

LOCK TABLES `MenuItem` WRITE;
/*!40000 ALTER TABLE `MenuItem` DISABLE KEYS */;
INSERT INTO `MenuItem` VALUES (1,'Grilled Chicken','Tender grilled chicken with herbs',89.99,'assets/images/menu/grilled-chicken.jpg',1,1),(2,'Pizza Margherita','Classic Italian pizza',79.99,'assets/images/menu/pizza.jpg',1,1),(3,'Caesar Salad','Fresh salad with caesar dressing',49.99,'assets/images/menu/caesar-salad.jpg',2,1),(4,'Chocolate Cake','Rich chocolate layer cake',39.99,'assets/images/menu/chocolate-cake.jpg',3,1),(5,'etg','ad',968.00,'assets/images/menu/orange-juice.jpg',1,0),(6,'Pasta Carbonara','Creamy pasta with bacon',69.99,'assets/images/menu/pasta.jpg',1,1),(7,'Grilled Chicken','Tender grilled chicken with herbs',89.99,'assets/images/menu/grilled-chicken.jpg',1,1),(8,'Pizza Margherita','Classic Italian pizza',79.99,'assets/images/menu/pizza.jpg',1,1),(9,'Caesar Salad','Fresh salad with caesar dressing',49.99,'assets/images/menu/caesar-salad.jpg',2,1),(10,'Chocolate Cake','Rich chocolate layer cake',39.99,'assets/images/menu/chocolate-cake.jpg',3,1),(11,'Fresh Orange Juice','Freshly squeezed oranges',19.99,'assets/images/menu/orange-juice.jpg',4,1),(12,'Pasta Carbonara','Creamy pasta with bacon',69.99,'assets/images/menu/pasta.jpg',1,1),(13,'Grilled Chicken','Tender grilled chicken with herbs',89.99,'assets/images/menu/grilled-chicken.jpg',1,1),(14,'Pizza Margherita','Classic Italian pizza',79.99,'assets/images/menu/pizza.jpg',1,1),(16,'Chocolate Cake','Rich chocolate layer cake',39.99,'assets/images/menu/chocolate-cake.jpg',3,1),(17,'llll','dad',55.00,'assets/images/menu/orange-juice.jpg',15,1),(18,'Pasta Carbonara','Creamy pasta with bacon',69.99,'assets/images/menu/pasta.jpg',1,1),(19,'Grilled Chicken','Marinated grilled chicken with herbs',120.00,NULL,1,1),(21,'Chocolate Cake','Rich chocolate cake with ganache',45.00,NULL,3,1),(23,'tfug','eret',58.00,'../../uploads/Menu-item/table-512.jpg',14,1),(24,'smsm','tgd',8645.00,'../../uploads/Menu-item/_cae85f2b-342f-4ff9-adb7-f99c44571fb8.jpeg',1,1),(25,'ss','ad',23.10,'../../uploads/Menu-item/table-512.jpg',1,1),(26,'sss','dsvfs',22.00,'../../uploads/Menu-item/sofa-512.jpg',1,1),(27,'ssss','gmj',75.00,'../../uploads/Menu-item/_cae85f2b-342f-4ff9-adb7-f99c44571fb8.jpeg',1,1),(28,'smmsm','rgddgr',5.00,'../../uploads/Menu-item/_13f7e066-28ed-416a-b0ef-9b27c3075bc7.jpeg',1,1);
/*!40000 ALTER TABLE `MenuItem` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Notification`
--

DROP TABLE IF EXISTS `Notification`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Notification` (
  `NotificationID` int NOT NULL AUTO_INCREMENT,
  `UserID` int DEFAULT NULL,
  `OrderID` int DEFAULT NULL,
  `ReservationID` int DEFAULT NULL,
  `NotificationType` varchar(100) NOT NULL,
  `Message` text NOT NULL,
  `Timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `IsRead` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`NotificationID`),
  KEY `fk_Notification_User1_idx` (`UserID`),
  KEY `fk_Notification_Order1_idx` (`OrderID`),
  KEY `fk_Notification_Reservation1_idx` (`ReservationID`),
  CONSTRAINT `fk_Notification_Order1` FOREIGN KEY (`OrderID`) REFERENCES `Order` (`OrderID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_Notification_Reservation1` FOREIGN KEY (`ReservationID`) REFERENCES `Reservation` (`ReservationID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_Notification_User1` FOREIGN KEY (`UserID`) REFERENCES `User` (`UserID`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Notification`
--

LOCK TABLES `Notification` WRITE;
/*!40000 ALTER TABLE `Notification` DISABLE KEYS */;
INSERT INTO `Notification` VALUES (6,1,1,NULL,'Order','New order received #1','2025-02-16 13:34:02',0),(11,1,1,NULL,'Order','New order received #1','2025-02-16 13:37:22',0),(19,1,NULL,NULL,'RESERVATION_DELETED','Reservation #8 has been deleted','2025-02-17 10:15:50',0),(20,2,NULL,NULL,'RESERVATION_DELETED','Reservation #8 has been deleted','2025-02-17 10:15:50',0),(21,25,NULL,NULL,'RESERVATION_DELETED','Reservation #8 has been deleted','2025-02-17 10:15:50',0),(22,1,NULL,NULL,'RESERVATION_DELETED','Reservation #4 has been deleted','2025-02-17 10:16:00',0),(23,2,NULL,NULL,'RESERVATION_DELETED','Reservation #4 has been deleted','2025-02-17 10:16:00',0),(24,25,NULL,NULL,'RESERVATION_DELETED','Reservation #4 has been deleted','2025-02-17 10:16:00',0),(25,1,NULL,12,'RESERVATION_UPDATED','Reservation #12 has been updated','2025-02-17 10:16:20',0),(26,2,NULL,12,'RESERVATION_UPDATED','Reservation #12 has been updated','2025-02-17 10:16:20',0),(27,25,NULL,12,'RESERVATION_UPDATED','Reservation #12 has been updated','2025-02-17 10:16:20',0),(28,1,NULL,13,'RESERVATION_CREATED','New reservation #13 has been created','2025-02-17 10:16:41',0),(29,2,NULL,13,'RESERVATION_CREATED','New reservation #13 has been created','2025-02-17 10:16:41',0),(30,25,NULL,13,'RESERVATION_CREATED','New reservation #13 has been created','2025-02-17 10:16:41',0),(31,1,NULL,14,'RESERVATION_CREATED','New reservation #14 has been created','2025-02-17 10:17:03',0),(32,2,NULL,14,'RESERVATION_CREATED','New reservation #14 has been created','2025-02-17 10:17:03',0),(33,25,NULL,14,'RESERVATION_CREATED','New reservation #14 has been created','2025-02-17 10:17:03',0);
/*!40000 ALTER TABLE `Notification` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Order`
--

DROP TABLE IF EXISTS `Order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Order` (
  `OrderID` int NOT NULL AUTO_INCREMENT,
  `CustomerID` int NOT NULL,
  `OrderDate` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `OrderStatus` enum('Pending','Preparing','Ready','Delivered','Cancelled') NOT NULL,
  `TotalAmount` decimal(10,2) NOT NULL,
  `DeliveryAddress` varchar(255) DEFAULT NULL,
  `Notes` text,
  `LastModified` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `RefundReason` text,
  `RefundDate` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`OrderID`),
  KEY `fk_Order_User1_idx` (`CustomerID`),
  CONSTRAINT `fk_Order_User1` FOREIGN KEY (`CustomerID`) REFERENCES `User` (`UserID`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Order`
--

LOCK TABLES `Order` WRITE;
/*!40000 ALTER TABLE `Order` DISABLE KEYS */;
INSERT INTO `Order` VALUES (1,1,'2024-02-25 18:00:00','Delivered',99.99,'Cairo, Egypt','Please deliver ASAP',NULL,NULL,NULL),(2,2,'2024-02-25 18:15:00','Preparing',149.99,'Alexandria, Egypt','Extra spicy',NULL,NULL,NULL),(3,3,'2024-02-10 12:20:00','Delivered',320.00,'Giza, Dokki','No onions',NULL,NULL,NULL),(4,4,'2024-02-15 13:10:00','Pending',94.99,'Mansoura, Downtown','Ring the bell','2025-02-18 12:56:51',NULL,NULL),(5,1,'2024-02-25 18:00:00','Cancelled',39.99,'Cairo, Egypt','Please deliver ASAP','2025-02-17 13:55:35',NULL,NULL),(6,2,'2024-02-25 18:15:00','Preparing',149.99,'Alexandria, Egypt','Extra spicy',NULL,NULL,NULL),(7,3,'2024-03-01 16:20:00','Preparing',49.99,'Giza, 6th October',NULL,'2025-02-17 14:04:43',NULL,NULL),(8,5,'2024-03-05 17:15:00','Cancelled',160.00,'Aswan, Downtown','Customer cancelled',NULL,NULL,NULL);
/*!40000 ALTER TABLE `Order` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `OrderItem`
--

DROP TABLE IF EXISTS `OrderItem`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `OrderItem` (
  `OrderItemID` int NOT NULL AUTO_INCREMENT,
  `OrderID` int NOT NULL,
  `ItemID` int NOT NULL,
  `Quantity` int NOT NULL DEFAULT '1',
  `PriceAtTimeOfOrder` decimal(10,2) NOT NULL,
  `Customizations` text,
  PRIMARY KEY (`OrderItemID`),
  KEY `fk_OrderItem_Order1_idx` (`OrderID`),
  KEY `fk_OrderItem_MenuItem1_idx` (`ItemID`),
  CONSTRAINT `fk_OrderItem_MenuItem1` FOREIGN KEY (`ItemID`) REFERENCES `MenuItem` (`ItemID`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_OrderItem_Order1` FOREIGN KEY (`OrderID`) REFERENCES `Order` (`OrderID`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `OrderItem`
--

LOCK TABLES `OrderItem` WRITE;
/*!40000 ALTER TABLE `OrderItem` DISABLE KEYS */;
INSERT INTO `OrderItem` VALUES (1,1,1,2,89.99,'Extra sauce'),(2,1,3,1,49.99,NULL),(3,2,2,3,79.99,'No cheese'),(6,1,1,2,89.99,'Extra sauce'),(7,1,3,1,49.99,NULL),(8,2,2,3,79.99,'No cheese'),(12,5,4,1,39.99,NULL),(14,7,3,1,49.99,NULL),(15,4,9,1,49.99,NULL),(16,4,21,1,45.00,NULL);
/*!40000 ALTER TABLE `OrderItem` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `OrderStatusHistory`
--

DROP TABLE IF EXISTS `OrderStatusHistory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `OrderStatusHistory` (
  `HistoryID` int NOT NULL AUTO_INCREMENT,
  `OrderID` int NOT NULL,
  `StatusFrom` enum('Pending','Processing','Completed','Cancelled','Refunded') COLLATE utf8mb4_unicode_ci NOT NULL,
  `StatusTo` enum('Pending','Processing','Completed','Cancelled','Refunded') COLLATE utf8mb4_unicode_ci NOT NULL,
  `ChangedAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Notes` text COLLATE utf8mb4_unicode_ci,
  `ChangedBy` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'admin',
  PRIMARY KEY (`HistoryID`),
  KEY `idx_order_history` (`OrderID`,`ChangedAt`),
  CONSTRAINT `OrderStatusHistory_ibfk_1` FOREIGN KEY (`OrderID`) REFERENCES `Order` (`OrderID`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `OrderStatusHistory`
--

LOCK TABLES `OrderStatusHistory` WRITE;
/*!40000 ALTER TABLE `OrderStatusHistory` DISABLE KEYS */;
INSERT INTO `OrderStatusHistory` VALUES (1,5,'Pending','Cancelled','2025-02-17 13:55:35','Order cancelled by admin','admin');
/*!40000 ALTER TABLE `OrderStatusHistory` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Payment`
--

DROP TABLE IF EXISTS `Payment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Payment` (
  `PaymentID` int NOT NULL AUTO_INCREMENT,
  `OrderID` int DEFAULT NULL,
  `PaymentDate` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `PaymentMethod` enum('Credit Card','Cash','Online') NOT NULL,
  `Amount` decimal(10,2) NOT NULL,
  `TransactionID` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`PaymentID`),
  KEY `fk_Payment_Order1_idx` (`OrderID`),
  CONSTRAINT `fk_Payment_Order1` FOREIGN KEY (`OrderID`) REFERENCES `Order` (`OrderID`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Payment`
--

LOCK TABLES `Payment` WRITE;
/*!40000 ALTER TABLE `Payment` DISABLE KEYS */;
/*!40000 ALTER TABLE `Payment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Refund`
--

DROP TABLE IF EXISTS `Refund`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Refund` (
  `RefundID` int NOT NULL AUTO_INCREMENT,
  `OrderID` int NOT NULL,
  `RefundAmount` decimal(10,2) NOT NULL,
  `RefundDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `RefundReason` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `Status` enum('Pending','Processed','Failed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `ProcessedAt` timestamp NULL DEFAULT NULL,
  `Notes` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`RefundID`),
  KEY `idx_order_refund` (`OrderID`),
  CONSTRAINT `Refund_ibfk_1` FOREIGN KEY (`OrderID`) REFERENCES `Order` (`OrderID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Refund`
--

LOCK TABLES `Refund` WRITE;
/*!40000 ALTER TABLE `Refund` DISABLE KEYS */;
/*!40000 ALTER TABLE `Refund` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Reservation`
--

DROP TABLE IF EXISTS `Reservation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Reservation` (
  `ReservationID` int NOT NULL AUTO_INCREMENT,
  `CustomerName` varchar(100) NOT NULL,
  `CustomerEmail` varchar(100) NOT NULL,
  `CustomerPhone` varchar(20) NOT NULL,
  `TableID` int NOT NULL,
  `ReservationDate` date NOT NULL,
  `ReservationTime` time NOT NULL,
  `NumberOfGuests` int NOT NULL,
  `ReservationStatus` enum('Pending','Confirmed','Cancelled','Completed') NOT NULL DEFAULT 'Pending',
  `Notes` text,
  PRIMARY KEY (`ReservationID`),
  KEY `fk_Reservation_Table1_idx` (`TableID`),
  CONSTRAINT `fk_Reservation_Table1` FOREIGN KEY (`TableID`) REFERENCES `Table` (`TableID`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Reservation`
--

LOCK TABLES `Reservation` WRITE;
/*!40000 ALTER TABLE `Reservation` DISABLE KEYS */;
INSERT INTO `Reservation` VALUES (3,'David Lee','david@email.com','1234567899',3,'2024-02-27','18:30:00',6,'Confirmed','Business dinner'),(5,'James Wilson','james@email.com','1234567897',1,'2024-02-26','19:00:00',2,'Confirmed','Anniversary dinner'),(6,'Mary Johnson','mary@email.com','1234567898',2,'2024-02-26','20:00:00',4,'Pending','Birthday celebration'),(7,'David Lee','david@email.com','1234567899',3,'2024-02-27','18:30:00',6,'Confirmed','Business dinner'),(9,'James Wilson','james@email.com','1234567897',1,'2024-02-26','19:00:00',2,'Confirmed','Anniversary dinner'),(10,'Mary Johnson','mary@email.com','1234567898',2,'2024-02-26','20:00:00',4,'Pending','Birthday celebration'),(11,'David Lee','david@email.com','1234567899',3,'2024-02-27','18:30:00',6,'Confirmed','Business dinner'),(12,'Ahmad Hassan','ahmad@example.com','1234567894',2,'2024-02-28','19:30:00',125,'Cancelled',''),(13,'Mohamed Hassan','mohamed@test.com','0123456787',11,'0005-04-05','08:46:00',846,'Pending',''),(14,'Ahmad Hassan','ahmad@example.com','1234567894',12,'0415-03-05','05:04:00',64,'Cancelled','');
/*!40000 ALTER TABLE `Reservation` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `after_reservation_insert` AFTER INSERT ON `Reservation` FOR EACH ROW BEGIN
    INSERT INTO ReservationHistory (ReservationID, Status, Notes)
    VALUES (NEW.ReservationID, NEW.ReservationStatus, 'Reservation created');
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `after_reservation_update` AFTER UPDATE ON `Reservation` FOR EACH ROW BEGIN
    IF OLD.ReservationStatus != NEW.ReservationStatus THEN
        INSERT INTO ReservationHistory (ReservationID, Status, Notes)
        VALUES (NEW.ReservationID, NEW.ReservationStatus, 
                CONCAT('Status changed from ', OLD.ReservationStatus, ' to ', NEW.ReservationStatus));
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `ReservationHistory`
--

DROP TABLE IF EXISTS `ReservationHistory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ReservationHistory` (
  `HistoryID` int NOT NULL AUTO_INCREMENT,
  `ReservationID` int NOT NULL,
  `Status` varchar(50) NOT NULL,
  `Notes` text,
  `CreatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`HistoryID`),
  KEY `ReservationID` (`ReservationID`),
  CONSTRAINT `ReservationHistory_ibfk_1` FOREIGN KEY (`ReservationID`) REFERENCES `Reservation` (`ReservationID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ReservationHistory`
--

LOCK TABLES `ReservationHistory` WRITE;
/*!40000 ALTER TABLE `ReservationHistory` DISABLE KEYS */;
/*!40000 ALTER TABLE `ReservationHistory` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Revenue`
--

DROP TABLE IF EXISTS `Revenue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Revenue` (
  `RevenueID` int NOT NULL AUTO_INCREMENT,
  `Amount` decimal(10,2) NOT NULL,
  `Timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `OrderID` int DEFAULT NULL,
  PRIMARY KEY (`RevenueID`),
  KEY `OrderID` (`OrderID`),
  CONSTRAINT `Revenue_ibfk_1` FOREIGN KEY (`OrderID`) REFERENCES `Order` (`OrderID`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Revenue`
--

LOCK TABLES `Revenue` WRITE;
/*!40000 ALTER TABLE `Revenue` DISABLE KEYS */;
INSERT INTO `Revenue` VALUES (1,159.98,'2024-02-25 08:05:00',1),(2,119.98,'2024-02-25 09:35:00',2),(3,69.99,'2024-02-25 14:25:00',1),(6,159.98,'2024-02-25 08:05:00',1),(7,119.98,'2024-02-25 09:35:00',2),(8,69.99,'2024-02-25 14:25:00',1);
/*!40000 ALTER TABLE `Revenue` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Review`
--

DROP TABLE IF EXISTS `Review`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Review` (
  `ReviewID` int NOT NULL AUTO_INCREMENT,
  `CustomerID` int DEFAULT NULL,
  `Rating` decimal(2,1) NOT NULL,
  `Comment` text NOT NULL,
  `ReviewDate` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ReviewID`),
  KEY `CustomerID` (`CustomerID`),
  CONSTRAINT `Review_ibfk_1` FOREIGN KEY (`CustomerID`) REFERENCES `User` (`UserID`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Review`
--

LOCK TABLES `Review` WRITE;
/*!40000 ALTER TABLE `Review` DISABLE KEYS */;
INSERT INTO `Review` VALUES (1,1,4.5,'الطعام رائع جداً! الخدمة ممتازة والجو مريح. سأعود مرة أخرى بالتأكيد.','2024-03-10 14:30:00'),(3,3,3.5,'الطعام جيد ولكن الخدمة بطيئة بعض الشيء. أتمنى تحسين وقت الانتظار.','2024-03-08 20:45:00'),(4,4,4.0,'المكان جميل والأطباق لذيذة. الأسعار معقولة مقارنة بالجودة.','2024-03-07 13:20:00'),(5,5,5.0,'من أفضل المطاعم التي زرتها! كل شيء كان مثالياً من الخدمة إلى النظافة والطعام.','2024-03-06 21:10:00'),(6,1,4.0,'زيارتي الثانية كانت جيدة مثل الأولى. أحب تنوع القائمة وجودة المكونات.','2024-03-05 18:30:00'),(7,2,4.5,'الأطباق الجديدة رائعة! أحب كيف يقدم المطعم دائماً أفكاراً مبتكرة.','2024-03-04 17:45:00'),(8,3,3.0,'الطعام جيد ولكن الأسعار مرتفعة قليلاً. أتمنى إضافة عروض وتخفيضات.','2024-03-03 15:20:00'),(9,4,4.5,'جو المطعم هادئ ومريح. مناسب للعائلات والمناسبات الخاصة.','2024-03-02 20:00:00'),(10,5,5.0,'دائماً ما أجد تجربة مميزة هنا. الطاقم ودود والطعام شهي.','2024-03-01 19:30:00');
/*!40000 ALTER TABLE `Review` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `SpecialOffer`
--

DROP TABLE IF EXISTS `SpecialOffer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `SpecialOffer` (
  `OfferID` int NOT NULL AUTO_INCREMENT,
  `Description` varchar(255) DEFAULT NULL,
  `DiscountPercentage` decimal(5,2) DEFAULT NULL,
  `DiscountAmount` decimal(10,2) DEFAULT NULL,
  `StartDate` datetime NOT NULL,
  `ExpiryDate` datetime NOT NULL,
  `ItemID` int NOT NULL,
  `OfferCode` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`OfferID`),
  KEY `fk_SpecialOffer_MenuItem1_idx` (`ItemID`),
  CONSTRAINT `fk_SpecialOffer_MenuItem1` FOREIGN KEY (`ItemID`) REFERENCES `MenuItem` (`ItemID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `SpecialOffer`
--

LOCK TABLES `SpecialOffer` WRITE;
/*!40000 ALTER TABLE `SpecialOffer` DISABLE KEYS */;
INSERT INTO `SpecialOffer` VALUES (4,'Weekend Special',15.00,NULL,'2024-02-24 00:00:00','2024-02-26 23:59:59',1,'WEEKEND15'),(5,'New Customer',NULL,20.00,'2024-02-01 00:00:00','2024-03-01 23:59:59',2,'NEWCUST20'),(6,'Happy Hour',25.00,NULL,'2024-02-25 16:00:00','2024-02-25 18:00:00',4,'HAPPY25'),(7,'Weekend Special',15.00,NULL,'2024-02-24 00:00:00','2024-02-26 23:59:59',1,'WEEKEND15'),(8,'New Customer',NULL,20.00,'2024-02-01 00:00:00','2024-03-01 23:59:59',2,'NEWCUST20'),(9,'Happy Hour',25.00,NULL,'2024-02-25 16:00:00','2024-02-25 18:00:00',4,'HAPPY25'),(10,'Weekend Special',15.00,NULL,'2024-02-24 00:00:00','2024-02-26 23:59:59',1,'WEEKEND15'),(11,'New Customer',NULL,20.00,'2024-02-01 00:00:00','2024-03-01 23:59:59',2,'NEWCUST20'),(12,'Happy Hour',25.00,NULL,'2024-02-25 16:00:00','2024-02-25 18:00:00',4,'HAPPY25');
/*!40000 ALTER TABLE `SpecialOffer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Supplier`
--

DROP TABLE IF EXISTS `Supplier`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Supplier` (
  `SupplierID` int NOT NULL AUTO_INCREMENT,
  `SupplierName` varchar(100) NOT NULL,
  `ContactPerson` varchar(100) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `PhoneNumber` varchar(20) DEFAULT NULL,
  `Address` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`SupplierID`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Supplier`
--

LOCK TABLES `Supplier` WRITE;
/*!40000 ALTER TABLE `Supplier` DISABLE KEYS */;
INSERT INTO `Supplier` VALUES (1,'Fresh Vegetables Co.','Mohamed Ali','mohamed@freshveg.com','1111111111','Cairo, Egypt'),(2,'Meat Masters','Ahmed Hassan','ahmed@meatmasters.com','2222222222','Alexandria, Egypt'),(3,'Bakery Supplies','Fatima Said','fatima@bakery.com','3333333333','Giza, Egypt'),(4,'Fresh Vegetables Co.','Mohamed Ali','mohamed@freshveg.com','1111111111','Cairo, Egypt'),(5,'Meat Masters','Ahmed Hassan','ahmed@meatmasters.com','2222222222','Alexandria, Egypt'),(6,'Bakery Supplies','Fatima Said','fatima@bakery.com','3333333333','Giza, Egypt'),(7,'Fresh Vegetables Co.','Mohamed Ali','mohamed@freshveg.com','1111111111','Cairo, Egypt'),(8,'Meat Masters','Ahmed Hassan','ahmed@meatmasters.com','2222222222','Alexandria, Egypt'),(9,'Bakery Supplies','Fatima Said','fatima@bakery.com','3333333333','Giza, Egypt');
/*!40000 ALTER TABLE `Supplier` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `SupplierOrders`
--

DROP TABLE IF EXISTS `SupplierOrders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `SupplierOrders` (
  `OrderID` int NOT NULL AUTO_INCREMENT,
  `SupplierID` int NOT NULL,
  `OrderDate` date NOT NULL,
  `TotalAmount` decimal(10,2) NOT NULL,
  `Status` enum('Pending','Shipped','Delivered') COLLATE utf8mb4_unicode_ci DEFAULT 'Pending',
  `CreatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `LastModified` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`OrderID`),
  KEY `idx_supplier_orders_supplier` (`SupplierID`),
  KEY `idx_supplier_orders_date` (`OrderDate`),
  CONSTRAINT `fk_SupplierOrders_Supplier` FOREIGN KEY (`SupplierID`) REFERENCES `Supplier` (`SupplierID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `SupplierOrders`
--

LOCK TABLES `SupplierOrders` WRITE;
/*!40000 ALTER TABLE `SupplierOrders` DISABLE KEYS */;
/*!40000 ALTER TABLE `SupplierOrders` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `before_supplier_order_update` BEFORE UPDATE ON `SupplierOrders` FOR EACH ROW BEGIN
    SET NEW.LastModified = CURRENT_TIMESTAMP;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `Table`
--

DROP TABLE IF EXISTS `Table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Table` (
  `TableID` int NOT NULL AUTO_INCREMENT,
  `TableNumber` varchar(50) NOT NULL,
  `Capacity` int NOT NULL,
  `Location` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`TableID`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Table`
--

LOCK TABLES `Table` WRITE;
/*!40000 ALTER TABLE `Table` DISABLE KEYS */;
INSERT INTO `Table` VALUES (1,'T1',2,'Window Side'),(2,'T2',4,'Window Side'),(3,'T3',6,'Center'),(4,'T4',8,'Private Room'),(5,'T5',4,'Garden'),(6,'T1',2,'Window Side'),(7,'T2',4,'Window Side'),(8,'T3',6,'Center'),(9,'T4',8,'Private Room'),(10,'T5',4,'Garden'),(11,'T1',2,'Window Side'),(12,'T2',4,'Window Side'),(13,'T3',6,'Center'),(14,'T4',8,'Private Room'),(15,'T5',4,'Garden');
/*!40000 ALTER TABLE `Table` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `TrendingKeywords`
--

DROP TABLE IF EXISTS `TrendingKeywords`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `TrendingKeywords` (
  `KeywordID` int NOT NULL AUTO_INCREMENT,
  `Keyword` varchar(50) NOT NULL,
  `Category` varchar(20) NOT NULL,
  `Count` int DEFAULT '0',
  `LastUpdated` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`KeywordID`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `TrendingKeywords`
--

LOCK TABLES `TrendingKeywords` WRITE;
/*!40000 ALTER TABLE `TrendingKeywords` DISABLE KEYS */;
INSERT INTO `TrendingKeywords` VALUES (1,'القاهرة','location',45,'2025-02-16 17:15:01'),(2,'الإسكندرية','location',38,'2025-02-16 17:15:01'),(3,'الجيزة','location',32,'2025-02-16 17:15:01'),(4,'المنصورة','location',28,'2025-02-16 17:15:01'),(5,'أسوان','location',25,'2025-02-16 17:15:01'),(6,'زبون متميز','customer_type',22,'2025-02-16 17:15:01'),(7,'زبون منتظم','customer_type',20,'2025-02-16 17:15:01'),(8,'زبون جديد','customer_type',18,'2025-02-16 17:15:01'),(9,'طلبات متكررة','order_pattern',15,'2025-02-16 17:15:01'),(10,'طلبات خاصة','order_pattern',12,'2025-02-16 17:15:01'),(11,'حار جداً','preference',10,'2025-02-16 17:15:01'),(12,'بدون بصل','preference',8,'2025-02-16 17:15:01'),(13,'صوص إضافي','preference',7,'2025-02-16 17:15:01'),(14,'تسليم سريع','delivery',14,'2025-02-16 17:15:01'),(15,'الدفع عند الاستلام','payment',16,'2025-02-16 17:15:01');
/*!40000 ALTER TABLE `TrendingKeywords` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `User`
--

DROP TABLE IF EXISTS `User`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `User` (
  `UserID` int NOT NULL AUTO_INCREMENT,
  `Username` varchar(50) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Role` enum('Staff','Customer') NOT NULL,
  `FirstName` varchar(50) DEFAULT NULL,
  `LastName` varchar(50) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `PhoneNumber` varchar(20) DEFAULT NULL,
  `Address` varchar(255) DEFAULT NULL,
  `ProfilePictureURL` varchar(255) DEFAULT NULL,
  `RegistrationDate` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `LastLoginDate` timestamp NULL DEFAULT NULL,
  `Status` enum('active','inactive') DEFAULT 'active',
  `LastLogin` timestamp NULL DEFAULT NULL,
  `LastModified` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`UserID`),
  UNIQUE KEY `Username` (`Username`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `User`
--

LOCK TABLES `User` WRITE;
/*!40000 ALTER TABLE `User` DISABLE KEYS */;
INSERT INTO `User` VALUES (1,'admin_user','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Staff','Admin','User','admin@restaurant.com','1234567895','Cairo, Egypt','assets/images/users/admin.jpg','2025-02-16 13:29:11',NULL,'active','2025-02-17 15:39:05','2025-02-17 15:39:05'),(2,'chef_gordon','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Staff','Gordon','Chef','chef@restaurant.com','1234567896','Alexandria, Egypt','assets/images/users/chef.jpg','2025-02-16 13:29:11',NULL,'active','2025-02-17 15:39:05','2025-02-17 15:39:05'),(3,'somaya','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Customer','Mohamed','Hassan','mohamed@example.com','01234567892','Giza, Egypt',NULL,'2023-03-10 07:15:00',NULL,'active','2025-02-17 15:39:05','2025-02-17 15:39:05'),(4,'nour.ahmed','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Customer','Nour','Ahmed','nour@example.com','01234567893','Mansoura, Egypt',NULL,'2023-04-05 14:20:00',NULL,'active','2025-02-17 15:39:05','2025-02-17 15:39:05'),(5,'ali.ibrahim','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Customer','Ali','Ibrahim','ali@example.com','01234567894','Aswan, Egypt',NULL,'2023-05-12 08:50:00',NULL,'inactive','2025-02-17 15:39:05','2025-02-17 15:39:05'),(6,'mariam.hassan','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Customer','مريم','حسن','mariam@example.com','01234567895','القاهرة، المعادي',NULL,'2024-03-05 11:25:00',NULL,'active','2025-02-17 15:39:05','2025-02-17 15:39:05'),(8,'sara_smith','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Customer','Sara','Smith','sara@example.com','1234567891','Alexandria, Egypt','assets/images/users/user2.jpg','2025-02-16 13:34:02',NULL,'active','2025-02-17 15:39:05','2025-02-17 15:39:05'),(9,'mike_brown','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Customer','Mike','Brown','mike@example.com','1234567892','Giza, Egypt','assets/images/users/user3.jpg','2025-02-16 13:34:02',NULL,'active','2025-02-17 15:39:05','2025-02-17 15:39:05'),(10,'lisa_white','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Customer','Lisa','White','lisa@example.com','1234567893','Luxor, Egypt','assets/images/users/user4.jpg','2025-02-16 13:34:02',NULL,'active','2025-02-17 15:39:05','2025-02-17 15:39:05'),(11,'ssss','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Customer','Ahmad','Hassan','ahmad@example.com','1234567894','Aswan, Egypt','assets/images/users/user5.jpg','2025-02-16 13:34:02',NULL,'inactive','2025-02-17 15:39:05','2025-02-17 15:39:05'),(21,'customer1','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Customer','somaya','hassan','ahmed1@test.com','0123456789','Cairo, Egypt',NULL,'2025-02-16 14:19:33',NULL,'active','2025-02-17 15:39:05','2025-02-17 15:39:05'),(23,'ss','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Customer','Mohamed','Hassan','mohamed@test.com','0123456787','Giza, Egypt',NULL,'2025-02-16 14:19:33',NULL,'active','2025-02-17 15:39:05','2025-02-17 15:39:05'),(25,'admin','$2y$10$6fIoI/AEgkSdQoZ77clOQuFfANg8.xAexfjd5XIxCf4ycTjceAPIG','Staff','Chef','Admin','admin@restaurant.com','1234567890','','uploads/profile_pictures/67b48a3e7befa_chef.jpg','2025-02-16 16:54:39',NULL,'active','2025-02-27 12:03:00','2025-02-27 12:03:00');
/*!40000 ALTER TABLE `User` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customers`
--

LOCK TABLES `customers` WRITE;
/*!40000 ALTER TABLE `customers` DISABLE KEYS */;
INSERT INTO `customers` VALUES (1,'John Doe','john@example.com','+1234567890','2025-02-16 16:14:01','2025-02-16 16:14:01'),(2,'Jane Smith','jane@example.com','+1234567891','2025-02-16 16:14:01','2025-02-16 16:14:01'),(3,'Mike Johnson','mike@example.com','+1234567892','2025-02-16 16:14:01','2025-02-16 16:14:01'),(4,'Sarah Williams','sarah@example.com','+1234567893','2025-02-16 16:14:01','2025-02-16 16:14:01'),(5,'David Brown','david@example.com','+1234567894','2025-02-16 16:14:01','2025-02-16 16:14:01');
/*!40000 ALTER TABLE `customers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reservations`
--

DROP TABLE IF EXISTS `reservations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reservations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `customer_id` int NOT NULL,
  `table_number` int NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `party_size` int NOT NULL,
  `status` enum('Pending','Confirmed','Completed','Cancelled') DEFAULT 'Pending',
  `special_requests` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `customer_id` (`customer_id`),
  KEY `table_number` (`table_number`),
  CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reservations_ibfk_2` FOREIGN KEY (`table_number`) REFERENCES `tables` (`number`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reservations`
--

LOCK TABLES `reservations` WRITE;
/*!40000 ALTER TABLE `reservations` DISABLE KEYS */;
INSERT INTO `reservations` VALUES (1,1,3,'2025-02-16','18:00:00',4,'Confirmed','Window seat preferred','2025-02-16 16:14:02','2025-02-16 16:14:02'),(2,2,5,'2025-02-16','19:00:00',6,'Pending','Birthday celebration','2025-02-16 16:14:02','2025-02-16 16:14:02'),(3,3,7,'2025-02-17','20:00:00',8,'Confirmed','Allergic to nuts','2025-02-16 16:14:02','2025-02-16 16:14:02'),(4,4,1,'2025-02-18','17:30:00',2,'Pending',NULL,'2025-02-16 16:14:02','2025-02-16 16:14:02'),(5,5,9,'2025-02-19','19:30:00',10,'Confirmed','Anniversary celebration','2025-02-16 16:14:02','2025-02-16 16:14:02');
/*!40000 ALTER TABLE `reservations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tables`
--

DROP TABLE IF EXISTS `tables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tables` (
  `number` int NOT NULL,
  `capacity` int NOT NULL,
  `status` enum('Available','Reserved','Occupied','Out of Service') DEFAULT 'Available',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tables`
--

LOCK TABLES `tables` WRITE;
/*!40000 ALTER TABLE `tables` DISABLE KEYS */;
INSERT INTO `tables` VALUES (1,2,'Available','2025-02-16 16:14:02','2025-02-16 16:14:02'),(2,2,'Available','2025-02-16 16:14:02','2025-02-16 16:14:02'),(3,4,'Available','2025-02-16 16:14:02','2025-02-16 16:14:02'),(4,4,'Available','2025-02-16 16:14:02','2025-02-16 16:14:02'),(5,6,'Available','2025-02-16 16:14:02','2025-02-16 16:14:02'),(6,6,'Available','2025-02-16 16:14:02','2025-02-16 16:14:02'),(7,8,'Available','2025-02-16 16:14:02','2025-02-16 16:14:02'),(8,8,'Available','2025-02-16 16:14:02','2025-02-16 16:14:02'),(9,10,'Available','2025-02-16 16:14:02','2025-02-16 16:14:02'),(10,12,'Available','2025-02-16 16:14:02','2025-02-16 16:14:02');
/*!40000 ALTER TABLE `tables` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-02-27 15:13:51
