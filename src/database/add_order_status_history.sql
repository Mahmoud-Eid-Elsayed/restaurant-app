-- Create OrderStatusHistory table
CREATE TABLE IF NOT EXISTS `OrderStatusHistory` (
    `HistoryID` INT PRIMARY KEY AUTO_INCREMENT,
    `OrderID` INT NOT NULL,
    `StatusFrom` ENUM('Pending', 'Processing', 'Completed', 'Cancelled', 'Refunded') NOT NULL,
    `StatusTo` ENUM('Pending', 'Processing', 'Completed', 'Cancelled', 'Refunded') NOT NULL,
    `ChangedAt` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `Notes` TEXT,
    `ChangedBy` VARCHAR(50) DEFAULT 'admin',
    FOREIGN KEY (`OrderID`) REFERENCES `Order`(`OrderID`) ON DELETE CASCADE,
    INDEX `idx_order_history` (`OrderID`, `ChangedAt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add columns to Order table
ALTER TABLE `Order`
ADD COLUMN `LastModified` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP;

ALTER TABLE `Order`
ADD COLUMN `RefundReason` TEXT;

ALTER TABLE `Order`
ADD COLUMN `RefundDate` TIMESTAMP NULL DEFAULT NULL;

-- Create Refund table
CREATE TABLE IF NOT EXISTS `Refund` (
    `RefundID` INT PRIMARY KEY AUTO_INCREMENT,
    `OrderID` INT NOT NULL,
    `RefundAmount` DECIMAL(10,2) NOT NULL,
    `RefundDate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `RefundReason` TEXT NOT NULL,
    `Status` ENUM('Pending', 'Processed', 'Failed') NOT NULL DEFAULT 'Pending',
    `ProcessedAt` TIMESTAMP NULL DEFAULT NULL,
    `Notes` TEXT,
    FOREIGN KEY (`OrderID`) REFERENCES `Order`(`OrderID`) ON DELETE CASCADE,
    INDEX `idx_order_refund` (`OrderID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci; 