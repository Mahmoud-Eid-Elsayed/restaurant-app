
--
-- Database: `restaurant_db`
--
CREATE DATABASE IF NOT EXISTS `restaurant_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `restaurant_db`;

-- --------------------------------------------------------

--
-- Table structure for table `customerstats`
--

DROP TABLE IF EXISTS `customerstats`;
CREATE TABLE IF NOT EXISTS `customerstats` (
  `CustomerID` int(11) NOT NULL,
  `TotalSpent` decimal(10,2) DEFAULT 0.00,
  `LastOrderDate` datetime DEFAULT NULL,
  `OrderCount` int(11) DEFAULT 0,
  PRIMARY KEY (`CustomerID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `customerstats`
--

TRUNCATE TABLE `customerstats`;
-- --------------------------------------------------------

--
-- Table structure for table `inventoryitem`
--

DROP TABLE IF EXISTS `inventoryitem`;
CREATE TABLE IF NOT EXISTS `inventoryitem` (
  `InventoryItemID` int(11) NOT NULL AUTO_INCREMENT,
  `ItemName` varchar(100) NOT NULL,
  `QuantityInStock` decimal(10,2) NOT NULL DEFAULT 0.00,
  `UnitOfMeasurement` varchar(50) NOT NULL,
  `ReorderLevel` decimal(10,2) NOT NULL DEFAULT 0.00,
  `SupplierID` int(11) NOT NULL,
  `LastPurchasedDate` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`InventoryItemID`),
  KEY `SupplierID` (`SupplierID`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `inventoryitem`
--

TRUNCATE TABLE `inventoryitem`;
--
-- Dumping data for table `inventoryitem`
--

INSERT DELAYED INTO `inventoryitem` (`InventoryItemID`, `ItemName`, `QuantityInStock`, `UnitOfMeasurement`, `ReorderLevel`, `SupplierID`, `LastPurchasedDate`) VALUES
(9, 'chererer', 60.00, 'kg', 4.00, 10, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `menucategory`
--

DROP TABLE IF EXISTS `menucategory`;
CREATE TABLE IF NOT EXISTS `menucategory` (
  `CategoryID` int(11) NOT NULL AUTO_INCREMENT,
  `CategoryName` varchar(100) NOT NULL,
  `Description` text DEFAULT NULL,
  `ImageURL` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`CategoryID`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `menucategory`
--

TRUNCATE TABLE `menucategory`;
--
-- Dumping data for table `menucategory`
--

INSERT DELAYED INTO `menucategory` (`CategoryID`, `CategoryName`, `Description`, `ImageURL`) VALUES
(1, 'Main Dish', NULL, NULL),
(2, 'Breakfast', NULL, NULL),
(3, 'Drink', NULL, NULL),
(4, 'special offers', 'black Friday offers', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `menuitem`
--

DROP TABLE IF EXISTS `menuitem`;
CREATE TABLE IF NOT EXISTS `menuitem` (
  `ItemID` int(11) NOT NULL AUTO_INCREMENT,
  `ItemName` varchar(100) NOT NULL,
  `Description` text DEFAULT NULL,
  `Price` decimal(10,2) NOT NULL,
  `ImageURL` varchar(255) DEFAULT NULL,
  `CategoryID` int(11) NOT NULL,
  `Availability` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`ItemID`),
  KEY `CategoryID` (`CategoryID`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `menuitem`
--

TRUNCATE TABLE `menuitem`;
--
-- Dumping data for table `menuitem`
--

INSERT DELAYED INTO `menuitem` (`ItemID`, `ItemName`, `Description`, `Price`, `ImageURL`, `CategoryID`, `Availability`) VALUES
(1, 'Grilled Chicken', 'Juicy grilled chicken', 22.00, 'grilled-chicken.jpg', 1, 1),
(2, 'Beef Burger', 'Delicious beef burger', 18.00, 'beef-burger.jpg', 1, 1),
(3, 'Spaghetti Bolognese', 'Pasta with meat sauce', 15.00, 'spaghetti-bolognese.jpg', 1, 1),
(4, 'Chicken Alfredo', 'Creamy chicken pasta', 17.00, 'chicken-alfredo.jpg', 1, 1),
(5, 'Grilled Salmon', 'Fresh grilled salmon', 25.00, 'grilled-salmon.jpg', 1, 1),


-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

DROP TABLE IF EXISTS `notification`;
CREATE TABLE IF NOT EXISTS `notification` (
  `NotificationID` int(11) NOT NULL AUTO_INCREMENT,
  `UserID` int(11) DEFAULT NULL,
  `OrderID` int(11) DEFAULT NULL,
  `ReservationID` int(11) DEFAULT NULL,
  `NotificationType` varchar(100) NOT NULL,
  `Message` text NOT NULL,
  `Timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `IsRead` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`NotificationID`),
  KEY `UserID` (`UserID`),
  KEY `OrderID` (`OrderID`),
  KEY `ReservationID` (`ReservationID`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `notification`
--

TRUNCATE TABLE `notification`;
--
-- Dumping data for table `notification`
--

INSERT DELAYED INTO `notification` (`NotificationID`, `UserID`, `OrderID`, `ReservationID`, `NotificationType`, `Message`, `Timestamp`, `IsRead`) VALUES
(1, 2, NULL, NULL, '', 'Your order #1 has been placed successfully.', '2025-02-23 13:33:48', 0),
(2, NULL, NULL, NULL, '', 'New order #1 has been placed by customer ID 2.', '2025-02-23 13:33:48', 0),
(3, 2, NULL, NULL, '', 'Your order #2 has been placed successfully.', '2025-02-23 13:38:53', 0),


-- --------------------------------------------------------

--
-- Table structure for table `order`
--

DROP TABLE IF EXISTS `order`;
CREATE TABLE IF NOT EXISTS `order` (
  `OrderID` int(11) NOT NULL AUTO_INCREMENT,
  `CustomerID` int(11) DEFAULT NULL,
  `OrderDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `OrderStatus` enum('Pending','Processing','Completed','Cancelled','Refunded') NOT NULL DEFAULT 'Pending',
  `TotalAmount` decimal(10,2) NOT NULL,
  `DeliveryAddress` text DEFAULT NULL,
  `Notes` text DEFAULT NULL,
  `LastModified` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `RefundReason` text DEFAULT NULL,
  `RefundDate` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`OrderID`),
  KEY `CustomerID` (`CustomerID`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `order`
--

TRUNCATE TABLE `order`;
--
-- Dumping data for table `order`
--

INSERT DELAYED INTO `order` (`OrderID`, `CustomerID`, `OrderDate`, `OrderStatus`, `TotalAmount`, `DeliveryAddress`, `Notes`, `LastModified`, `RefundReason`, `RefundDate`) VALUES
(1, 2, '2025-02-23 13:33:48', 'Pending', 14.00, NULL, NULL, NULL, NULL, NULL),
(2, 2, '2025-02-23 13:38:53', '', 0.00, NULL, NULL, '2025-02-23 13:40:41', NULL, NULL),
(3, 2, '2025-02-23 14:15:22', 'Pending', 216.00, NULL, NULL, NULL, NULL, NULL),
(4, 2, '2025-02-23 14:33:06', 'Pending', 258.00, NULL, NULL, NULL, NULL, NULL),
(5, 2, '2025-02-23 15:29:32', 'Pending', 50.00, NULL, NULL, NULL, NULL, NULL),


--
-- Triggers `order`
--
DROP TRIGGER IF EXISTS `after_order_update`;
DELIMITER $$
CREATE TRIGGER `after_order_update` AFTER UPDATE ON `order` FOR EACH ROW BEGIN
    IF NEW.OrderStatus = 'Completed' AND OLD.OrderStatus != 'Completed' THEN
        INSERT INTO CustomerStats (CustomerID, TotalSpent, LastOrderDate, OrderCount)
        VALUES (NEW.CustomerID, NEW.TotalAmount, NEW.OrderDate, 1)
        ON DUPLICATE KEY UPDATE
            TotalSpent = TotalSpent + NEW.TotalAmount,
            LastOrderDate = NEW.OrderDate,
            OrderCount = OrderCount + 1;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `orderitem`
--

DROP TABLE IF EXISTS `orderitem`;
CREATE TABLE IF NOT EXISTS `orderitem` (
  `OrderItemID` int(11) NOT NULL AUTO_INCREMENT,
  `OrderID` int(11) NOT NULL,
  `ItemID` int(11) NOT NULL,
  `Quantity` int(11) NOT NULL DEFAULT 1,
  `PriceAtTimeOfOrder` decimal(10,2) NOT NULL,
  `Customizations` text DEFAULT NULL,
  PRIMARY KEY (`OrderItemID`),
  KEY `OrderID` (`OrderID`),
  KEY `ItemID` (`ItemID`)
) ENGINE=InnoDB AUTO_INCREMENT=75 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `orderitem`
--

TRUNCATE TABLE `orderitem`;
--
-- Dumping data for table `orderitem`
--

INSERT DELAYED INTO `orderitem` (`OrderItemID`, `OrderID`, `ItemID`, `Quantity`, `PriceAtTimeOfOrder`, `Customizations`) VALUES
(1, 1, 9, 1, 0.00, NULL),
(20, 2, 9, 2, 0.00, NULL),
(21, 2, 20, 3, 0.00, NULL),
(22, 2, 1, 2, 0.00, NULL),
(23, 2, 7, 5, 0.00, NULL),
(24, 2, 6, 1, 0.00, NULL),
(25, 2, 8, 1, 0.00, NULL),

-- --------------------------------------------------------

--
-- Table structure for table `orderstatushistory`
--

DROP TABLE IF EXISTS `orderstatushistory`;
CREATE TABLE IF NOT EXISTS `orderstatushistory` (
  `HistoryID` int(11) NOT NULL AUTO_INCREMENT,
  `OrderID` int(11) NOT NULL,
  `StatusFrom` enum('Pending','Processing','Completed','Cancelled','Refunded') NOT NULL,
  `StatusTo` enum('Pending','Processing','Completed','Cancelled','Refunded') NOT NULL,
  `ChangedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `Notes` text DEFAULT NULL,
  `ChangedBy` varchar(50) DEFAULT 'admin',
  PRIMARY KEY (`HistoryID`),
  KEY `OrderID` (`OrderID`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `orderstatushistory`
--

TRUNCATE TABLE `orderstatushistory`;
--
-- Dumping data for table `orderstatushistory`
--

INSERT DELAYED INTO `orderstatushistory` (`HistoryID`, `OrderID`, `StatusFrom`, `StatusTo`, `ChangedAt`, `Notes`, `ChangedBy`) VALUES
(1, 2, 'Pending', '', '2025-02-23 13:40:41', 'Status updated via admin dashboard', 'admin'),
(2, 2, '', '', '2025-02-23 13:41:06', 'Status updated via admin dashboard', 'admin'),
(3, 2, '', '', '2025-02-23 13:41:20', 'Status updated via admin dashboard', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

DROP TABLE IF EXISTS `payment`;
CREATE TABLE IF NOT EXISTS `payment` (
  `PaymentID` int(11) NOT NULL AUTO_INCREMENT,
  `OrderID` int(11) NOT NULL,
  `Amount` decimal(10,2) NOT NULL,
  `PaymentMethod` enum('Cash','Credit Card','Debit Card','Online') NOT NULL,
  `PaymentStatus` enum('Pending','Completed','Failed','Refunded') NOT NULL DEFAULT 'Pending',
  `TransactionID` varchar(100) DEFAULT NULL,
  `PaymentDate` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`PaymentID`),
  KEY `OrderID` (`OrderID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `payment`
--

TRUNCATE TABLE `payment`;
-- --------------------------------------------------------

--
-- Table structure for table `refund`
--

DROP TABLE IF EXISTS `refund`;
CREATE TABLE IF NOT EXISTS `refund` (
  `RefundID` int(11) NOT NULL AUTO_INCREMENT,
  `OrderID` int(11) NOT NULL,
  `RefundAmount` decimal(10,2) NOT NULL,
  `RefundDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `RefundReason` text NOT NULL,
  `Status` enum('Pending','Processed','Failed') NOT NULL DEFAULT 'Pending',
  `ProcessedAt` timestamp NULL DEFAULT NULL,
  `Notes` text DEFAULT NULL,
  PRIMARY KEY (`RefundID`),
  KEY `OrderID` (`OrderID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `refund`
--

TRUNCATE TABLE `refund`;
-- --------------------------------------------------------

--
-- Table structure for table `reservation`
--

DROP TABLE IF EXISTS `reservation`;
CREATE TABLE IF NOT EXISTS `reservation` (
  `ReservationID` int(11) NOT NULL AUTO_INCREMENT,
  `CustomerID` int(11) DEFAULT NULL,
  `TableID` int(11) NOT NULL,
  `CustomerName` varchar(100) NOT NULL,
  `CustomerEmail` varchar(100) NOT NULL,
  `CustomerPhone` varchar(20) NOT NULL,
  `ReservationDate` date NOT NULL,
  `ReservationTime` time NOT NULL,
  `NumberOfGuests` int(11) NOT NULL,
  `ReservationStatus` enum('Pending','Confirmed','Cancelled','Completed') DEFAULT 'Pending',
  `Notes` text DEFAULT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `LastModified` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`ReservationID`),
  KEY `CustomerID` (`CustomerID`),
  KEY `TableID` (`TableID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `reservation`
--

TRUNCATE TABLE `reservation`;
--
-- Triggers `reservation`
--
DROP TRIGGER IF EXISTS `after_reservation_insert`;
DELIMITER $$
CREATE TRIGGER `after_reservation_insert` AFTER INSERT ON `reservation` FOR EACH ROW BEGIN
    INSERT INTO ReservationHistory (ReservationID, Status, Notes)
    VALUES (NEW.ReservationID, NEW.ReservationStatus, 'Reservation created');
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `after_reservation_update`;
DELIMITER $$
CREATE TRIGGER `after_reservation_update` AFTER UPDATE ON `reservation` FOR EACH ROW BEGIN
    IF OLD.ReservationStatus != NEW.ReservationStatus THEN
        INSERT INTO ReservationHistory (ReservationID, Status, Notes)
        VALUES (NEW.ReservationID, NEW.ReservationStatus, 
                CONCAT('Status changed from ', OLD.ReservationStatus, ' to ', NEW.ReservationStatus));
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `reservationhistory`
--

DROP TABLE IF EXISTS `reservationhistory`;
CREATE TABLE IF NOT EXISTS `reservationhistory` (
  `HistoryID` int(11) NOT NULL AUTO_INCREMENT,
  `ReservationID` int(11) NOT NULL,
  `Status` varchar(50) NOT NULL,
  `Notes` text DEFAULT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`HistoryID`),
  KEY `ReservationID` (`ReservationID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `reservationhistory`
--

TRUNCATE TABLE `reservationhistory`;
-- --------------------------------------------------------

--
-- Table structure for table `revenue`
--

DROP TABLE IF EXISTS `revenue`;
CREATE TABLE IF NOT EXISTS `revenue` (
  `RevenueID` int(11) NOT NULL AUTO_INCREMENT,
  `Date` date NOT NULL,
  `TotalRevenue` decimal(10,2) NOT NULL DEFAULT 0.00,
  `OrderCount` int(11) DEFAULT 0,
  `AverageOrderValue` decimal(10,2) DEFAULT 0.00,
  `Notes` text DEFAULT NULL,
  PRIMARY KEY (`RevenueID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `revenue`
--

TRUNCATE TABLE `revenue`;
-- --------------------------------------------------------

--
-- Table structure for table `review`
--

DROP TABLE IF EXISTS `review`;
CREATE TABLE IF NOT EXISTS `review` (
  `ReviewID` int(11) NOT NULL AUTO_INCREMENT,
  `CustomerID` int(11) DEFAULT NULL,
  `OrderID` int(11) DEFAULT NULL,
  `Rating` int(11) NOT NULL CHECK (`Rating` >= 1 and `Rating` <= 5),
  `Comment` text DEFAULT NULL,
  `ReviewDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `IsPublished` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`ReviewID`),
  KEY `CustomerID` (`CustomerID`),
  KEY `OrderID` (`OrderID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `review`
--

TRUNCATE TABLE `review`;
-- --------------------------------------------------------

--
-- Table structure for table `specialoffer`
--

DROP TABLE IF EXISTS `specialoffer`;
CREATE TABLE IF NOT EXISTS `specialoffer` (
  `OfferID` int(11) NOT NULL AUTO_INCREMENT,
  `OfferName` varchar(100) NOT NULL,
  `Description` text DEFAULT NULL,
  `DiscountPercentage` decimal(5,2) DEFAULT NULL,
  `StartDate` date NOT NULL,
  `EndDate` date NOT NULL,
  `IsActive` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`OfferID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `specialoffer`
--

TRUNCATE TABLE `specialoffer`;
-- --------------------------------------------------------

--
-- Table structure for table `supplier`
--

DROP TABLE IF EXISTS `supplier`;
CREATE TABLE IF NOT EXISTS `supplier` (
  `SupplierID` int(11) NOT NULL AUTO_INCREMENT,
  `SupplierName` varchar(100) NOT NULL,
  `ContactPerson` varchar(100) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `Phone` varchar(20) DEFAULT NULL,
  `Address` text DEFAULT NULL,
  PRIMARY KEY (`SupplierID`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `supplier`
--

TRUNCATE TABLE `supplier`;
--
-- Dumping data for table `supplier`
--

INSERT DELAYED INTO `supplier` (`SupplierID`, `SupplierName`, `ContactPerson`, `Email`, `Phone`, `Address`) VALUES
(1, 'supplier1', '456456', 'supplier1@gm.com', '0123546456', '10 d ST'),
(2, 'supplier2', 'dfgdfg', 'supplier2@gm.com', '01235689654', '256 DG ST');

-- --------------------------------------------------------

--
-- Table structure for table `supplierorders`
--

DROP TABLE IF EXISTS `supplierorders`;
CREATE TABLE IF NOT EXISTS `supplierorders` (
  `OrderID` int(11) NOT NULL AUTO_INCREMENT,
  `SupplierID` int(11) NOT NULL,
  `OrderDate` date NOT NULL,
  `TotalAmount` decimal(10,2) NOT NULL,
  `Status` enum('Pending','Shipped','Delivered') DEFAULT 'Pending',
  PRIMARY KEY (`OrderID`),
  KEY `SupplierID` (`SupplierID`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `supplierorders`
--

TRUNCATE TABLE `supplierorders`;
--
-- Dumping data for table `supplierorders`
--

INSERT DELAYED INTO `supplierorders` (`OrderID`, `SupplierID`, `OrderDate`, `TotalAmount`, `Status`) VALUES
(4, 10, '2025-02-21', 5.00, 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `table`
--

DROP TABLE IF EXISTS `table`;
CREATE TABLE IF NOT EXISTS `table` (
  `TableID` int(11) NOT NULL AUTO_INCREMENT,
  `TableNumber` varchar(10) NOT NULL,
  `Capacity` int(11) NOT NULL,
  `Location` varchar(50) NOT NULL,
  `Status` enum('Available','Occupied','Reserved','Maintenance') DEFAULT 'Available',
  PRIMARY KEY (`TableID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `table`
--

TRUNCATE TABLE `table`;
-- --------------------------------------------------------

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `UserID` int(11) NOT NULL AUTO_INCREMENT,
  `Username` varchar(50) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Role` enum('Admin','Staff','Customer') NOT NULL DEFAULT 'Customer',
  `FirstName` varchar(50) DEFAULT NULL,
  `LastName` varchar(50) DEFAULT NULL,
  `Email` varchar(100) NOT NULL,
  `PhoneNumber` varchar(20) DEFAULT NULL,
  `Address` text DEFAULT NULL,
  `ProfilePictureURL` varchar(255) DEFAULT NULL,
  `RegistrationDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `LastLoginDate` timestamp NULL DEFAULT NULL,
  `LastModified` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `Status` enum('active','inactive') DEFAULT 'active',
  `LastLogin` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`UserID`),
  UNIQUE KEY `Username` (`Username`),
  UNIQUE KEY `Email` (`Email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `user`
--

TRUNCATE TABLE `user`;
--
-- Dumping data for table `user`
--

INSERT DELAYED INTO `user` (`UserID`, `Username`, `Password`, `Role`, `FirstName`, `LastName`, `Email`, `PhoneNumber`, `Address`, `ProfilePictureURL`, `RegistrationDate`, `LastLoginDate`, `LastModified`, `Status`, `LastLogin`) VALUES
(1, 'admin', '$2y$10$Z8zqorF5V1wP8B4zaW4yqOeTk3RKDmAtOc.MbBgDdQZj.WYDaFgrS', 'Staff', 'Mado', 'Kira', 'admin@example.com', '01236598756', '123 Ramadan St', 'uploads/profile_pictures/67bae207e4def_Discord_SrankSungJinwoo.png', '2025-02-19 18:08:57', NULL, '2025-02-23 17:08:25', 'active', '2025-02-23 17:08:25'),
(2, 'zaid', '$2y$10$NLQ7BM9p6ku0KozeWtecouZ/RHpvmdOSZy58YAi70QBdk9agOxkAi', 'Customer', 'zaid', 'ammer', 'zaid55@gmail.com', '01234567895', '10 ramadan ST', '../../../assets/images/users/uploads/user_2.jfif', '2025-02-23 07:49:30', '2025-02-23 07:49:30', '2025-02-23 16:11:53', 'active', '2025-02-23 16:11:53');

--
-- Triggers `user`
--
DROP TRIGGER IF EXISTS `before_user_update`;
DELIMITER $$
CREATE TRIGGER `before_user_update` BEFORE UPDATE ON `user` FOR EACH ROW BEGIN
    SET NEW.LastModified = CURRENT_TIMESTAMP;
END
$$
DELIMITER ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `customerstats`
--
ALTER TABLE `customerstats`
  ADD CONSTRAINT `customerstats_ibfk_1` FOREIGN KEY (`CustomerID`) REFERENCES `user` (`UserID`) ON DELETE CASCADE;

--
-- Constraints for table `inventoryitem`
--
ALTER TABLE `inventoryitem`
  ADD CONSTRAINT `inventoryitem_ibfk_1` FOREIGN KEY (`SupplierID`) REFERENCES `supplier` (`SupplierID`) ON UPDATE CASCADE;

--
-- Constraints for table `menuitem`
--
ALTER TABLE `menuitem`
  ADD CONSTRAINT `menuitem_ibfk_1` FOREIGN KEY (`CategoryID`) REFERENCES `menucategory` (`CategoryID`) ON UPDATE CASCADE;

--
-- Constraints for table `notification`
--
ALTER TABLE `notification`
  ADD CONSTRAINT `notification_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `notification_ibfk_2` FOREIGN KEY (`OrderID`) REFERENCES `order` (`OrderID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `notification_ibfk_3` FOREIGN KEY (`ReservationID`) REFERENCES `reservation` (`ReservationID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `order`
--
ALTER TABLE `order`
  ADD CONSTRAINT `order_ibfk_1` FOREIGN KEY (`CustomerID`) REFERENCES `user` (`UserID`) ON UPDATE CASCADE;

--
-- Constraints for table `orderitem`
--
ALTER TABLE `orderitem`
  ADD CONSTRAINT `orderitem_ibfk_1` FOREIGN KEY (`OrderID`) REFERENCES `order` (`OrderID`) ON DELETE CASCADE,
  ADD CONSTRAINT `orderitem_ibfk_2` FOREIGN KEY (`ItemID`) REFERENCES `menuitem` (`ItemID`) ON UPDATE CASCADE;

--
-- Constraints for table `orderstatushistory`
--
ALTER TABLE `orderstatushistory`
  ADD CONSTRAINT `orderstatushistory_ibfk_1` FOREIGN KEY (`OrderID`) REFERENCES `order` (`OrderID`) ON DELETE CASCADE;

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`OrderID`) REFERENCES `order` (`OrderID`) ON UPDATE CASCADE;

--
-- Constraints for table `refund`
--
ALTER TABLE `refund`
  ADD CONSTRAINT `refund_ibfk_1` FOREIGN KEY (`OrderID`) REFERENCES `order` (`OrderID`) ON DELETE CASCADE;

--
-- Constraints for table `reservation`
--
ALTER TABLE `reservation`
  ADD CONSTRAINT `reservation_ibfk_1` FOREIGN KEY (`CustomerID`) REFERENCES `user` (`UserID`) ON DELETE SET NULL,
  ADD CONSTRAINT `reservation_ibfk_2` FOREIGN KEY (`TableID`) REFERENCES `table` (`TableID`) ON UPDATE CASCADE;

--
-- Constraints for table `reservationhistory`
--
ALTER TABLE `reservationhistory`
  ADD CONSTRAINT `reservationhistory_ibfk_1` FOREIGN KEY (`ReservationID`) REFERENCES `reservation` (`ReservationID`) ON DELETE CASCADE;

--
-- Constraints for table `review`
--
ALTER TABLE `review`
  ADD CONSTRAINT `review_ibfk_1` FOREIGN KEY (`CustomerID`) REFERENCES `user` (`UserID`) ON DELETE SET NULL,
  ADD CONSTRAINT `review_ibfk_2` FOREIGN KEY (`OrderID`) REFERENCES `order` (`OrderID`) ON DELETE SET NULL;

--
-- Constraints for table `supplierorders`
--
ALTER TABLE `supplierorders`
  ADD CONSTRAINT `supplierorders_ibfk_1` FOREIGN KEY (`SupplierID`) REFERENCES `supplier` (`SupplierID`) ON DELETE CASCADE;
COMMIT;
