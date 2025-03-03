-- Test data for Restaurant_DB
USE Restaurant_DB;

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
(1, CURRENT_DATE(), 'Completed', 150.00, '123 Main St'),
(2, CURRENT_DATE(), 'Preparing', 85.50, '456 Oak St'),
(1, CURRENT_DATE(), 'Ready', 200.00, '123 Main St'),

-- Yesterday's orders
(2, DATE_SUB(CURRENT_DATE(), INTERVAL 1 DAY), 'Completed', 120.00, '456 Oak St'),
(1, DATE_SUB(CURRENT_DATE(), INTERVAL 1 DAY), 'Completed', 75.00, '123 Main St'),

-- Orders from last week
(1, DATE_SUB(CURRENT_DATE(), INTERVAL 3 DAY), 'Completed', 95.00, '123 Main St'),
(2, DATE_SUB(CURRENT_DATE(), INTERVAL 4 DAY), 'Completed', 180.00, '456 Oak St'),
(1, DATE_SUB(CURRENT_DATE(), INTERVAL 5 DAY), 'Completed', 145.00, '123 Main St'),
(2, DATE_SUB(CURRENT_DATE(), INTERVAL 6 DAY), 'Completed', 220.00, '456 Oak St'),

-- Orders from earlier this month
(1, DATE_SUB(CURRENT_DATE(), INTERVAL 10 DAY), 'Completed', 165.00, '123 Main St'),
(2, DATE_SUB(CURRENT_DATE(), INTERVAL 15 DAY), 'Completed', 195.00, '456 Oak St'),
(1, DATE_SUB(CURRENT_DATE(), INTERVAL 20 DAY), 'Completed', 135.00, '123 Main St'),
(2, DATE_SUB(CURRENT_DATE(), INTERVAL 25 DAY), 'Completed', 175.00, '456 Oak St');

-- Insert test revenue data
INSERT INTO `Revenue` (`Amount`, `Timestamp`, `OrderID`)
SELECT 
    TotalAmount as Amount,
    OrderDate as Timestamp,
    OrderID
FROM `Order`
WHERE OrderStatus = 'Completed';

-- Update CustomerStats
INSERT INTO CustomerStats (CustomerID, TotalSpent, LastOrderDate, OrderCount)
SELECT 
    CustomerID,
    SUM(TotalAmount) as TotalSpent,
    MAX(OrderDate) as LastOrderDate,
    COUNT(*) as OrderCount
FROM `Order`
WHERE OrderStatus = 'Completed'
GROUP BY CustomerID
ON DUPLICATE KEY UPDATE
    TotalSpent = VALUES(TotalSpent),
    LastOrderDate = VALUES(LastOrderDate),
    OrderCount = VALUES(OrderCount); 