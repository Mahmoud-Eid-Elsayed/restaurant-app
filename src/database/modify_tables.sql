USE Restaurant_DB;

-- Create User table if not exists
CREATE TABLE IF NOT EXISTS `User` (
    `UserID` INT PRIMARY KEY AUTO_INCREMENT,
    `Username` VARCHAR(50) UNIQUE NOT NULL,
    `Password` VARCHAR(255) NOT NULL,
    `Role` VARCHAR(20) NOT NULL,
    `FirstName` VARCHAR(50),
    `LastName` VARCHAR(50),
    `Email` VARCHAR(100),
    `PhoneNumber` VARCHAR(20),
    `Address` TEXT,
    `Status` VARCHAR(20) DEFAULT 'active'
);

-- Create Order table if not exists
CREATE TABLE IF NOT EXISTS `Order` (
    `OrderID` INT PRIMARY KEY AUTO_INCREMENT,
    `CustomerID` INT NOT NULL,
    `OrderDate` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `OrderStatus` VARCHAR(50),
    `TotalAmount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `DeliveryAddress` TEXT,
    FOREIGN KEY (`CustomerID`) REFERENCES `User`(`UserID`)
);

-- Create OrderItem table if not exists
CREATE TABLE IF NOT EXISTS `OrderItem` (
    `OrderItemID` INT PRIMARY KEY AUTO_INCREMENT,
    `OrderID` INT NOT NULL,
    `ItemID` INT NOT NULL,
    `Quantity` INT NOT NULL DEFAULT 1,
    `Price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    FOREIGN KEY (`OrderID`) REFERENCES `Order`(`OrderID`)
);

-- Create Revenue table if not exists
CREATE TABLE IF NOT EXISTS `Revenue` (
    `RevenueID` INT PRIMARY KEY AUTO_INCREMENT,
    `Amount` DECIMAL(10,2) NOT NULL,
    `Timestamp` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `OrderID` INT,
    FOREIGN KEY (`OrderID`) REFERENCES `Order`(`OrderID`)
);

-- Create CustomerStats table if not exists
CREATE TABLE IF NOT EXISTS `CustomerStats` (
    `CustomerID` INT PRIMARY KEY,
    `TotalSpent` DECIMAL(10,2) DEFAULT 0.00,
    `LastOrderDate` DATETIME,
    `OrderCount` INT DEFAULT 0,
    FOREIGN KEY (`CustomerID`) REFERENCES `User`(`UserID`)
);

-- Modify OrderStatus column
ALTER TABLE `Order` MODIFY COLUMN OrderStatus VARCHAR(50);

-- Insert test users if not exists
INSERT INTO `User` (`Username`, `Password`, `Role`, `FirstName`, `LastName`, `Email`, `PhoneNumber`, `Address`, `Status`)
VALUES 
('admin', '$2y$10$Z8zqorF5V1wP8B4zaW4yqOeTk3RKDmAtOc.MbBgDdQZj.WYDaFgrS', 'Staff', 'Admin', 'User', 'admin@elchef.com', '1234567890', 'Restaurant Address', 'active'),
('customer1', '$2y$10$Z8zqorF5V1wP8B4zaW4yqOeTk3RKDmAtOc.MbBgDdQZj.WYDaFgrS', 'Customer', 'John', 'Doe', 'john@example.com', '1234567890', '123 Main St', 'active'),
('customer2', '$2y$10$Z8zqorF5V1wP8B4zaW4yqOeTk3RKDmAtOc.MbBgDdQZj.WYDaFgrS', 'Customer', 'Jane', 'Smith', 'jane@example.com', '0987654321', '456 Oak St', 'active')
ON DUPLICATE KEY UPDATE `Username`=`Username`;

-- Insert test orders for the past month
INSERT INTO `Order` (`CustomerID`, `OrderDate`, `OrderStatus`, `TotalAmount`, `DeliveryAddress`)
VALUES
-- Today's orders
(1, CURRENT_DATE(), 'Delivered', 150.00, '123 Main St'),
(2, CURRENT_DATE(), 'Preparing', 85.50, '456 Oak St'),
(1, CURRENT_DATE(), 'Ready', 200.00, '123 Main St'),

-- Yesterday's orders
(2, DATE_SUB(CURRENT_DATE(), INTERVAL 1 DAY), 'Delivered', 120.00, '456 Oak St'),
(1, DATE_SUB(CURRENT_DATE(), INTERVAL 1 DAY), 'Delivered', 75.00, '123 Main St'),

-- Orders from last week
(1, DATE_SUB(CURRENT_DATE(), INTERVAL 3 DAY), 'Delivered', 95.00, '123 Main St'),
(2, DATE_SUB(CURRENT_DATE(), INTERVAL 4 DAY), 'Delivered', 180.00, '456 Oak St'),
(1, DATE_SUB(CURRENT_DATE(), INTERVAL 5 DAY), 'Delivered', 145.00, '123 Main St'),
(2, DATE_SUB(CURRENT_DATE(), INTERVAL 6 DAY), 'Delivered', 220.00, '456 Oak St'),

-- Orders from earlier this month
(1, DATE_SUB(CURRENT_DATE(), INTERVAL 10 DAY), 'Delivered', 165.00, '123 Main St'),
(2, DATE_SUB(CURRENT_DATE(), INTERVAL 15 DAY), 'Delivered', 195.00, '456 Oak St'),
(1, DATE_SUB(CURRENT_DATE(), INTERVAL 20 DAY), 'Delivered', 135.00, '123 Main St'),
(2, DATE_SUB(CURRENT_DATE(), INTERVAL 25 DAY), 'Delivered', 175.00, '456 Oak St');

-- Insert test revenue data
INSERT INTO `Revenue` (`Amount`, `Timestamp`, `OrderID`)
SELECT 
    TotalAmount as Amount,
    OrderDate as Timestamp,
    OrderID
FROM `Order`
WHERE OrderStatus = 'Delivered';

-- Update CustomerStats
INSERT INTO CustomerStats (CustomerID, TotalSpent, LastOrderDate, OrderCount)
SELECT 
    CustomerID,
    SUM(TotalAmount) as TotalSpent,
    MAX(OrderDate) as LastOrderDate,
    COUNT(*) as OrderCount
FROM `Order`
WHERE OrderStatus = 'Delivered'
GROUP BY CustomerID
ON DUPLICATE KEY UPDATE
    TotalSpent = VALUES(TotalSpent),
    LastOrderDate = VALUES(LastOrderDate),
    OrderCount = VALUES(OrderCount); 