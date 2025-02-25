-- Create SupplierOrders table if it doesn't exist
CREATE TABLE IF NOT EXISTS `SupplierOrders` (
    `OrderID` INT NOT NULL AUTO_INCREMENT,
    `SupplierID` INT NOT NULL,
    `OrderDate` DATE NOT NULL,
    `TotalAmount` DECIMAL(10, 2) NOT NULL,
    `Status` ENUM('Pending', 'Shipped', 'Delivered') DEFAULT 'Pending',
    `CreatedAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `LastModified` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`OrderID`),
    CONSTRAINT `fk_SupplierOrders_Supplier`
        FOREIGN KEY (`SupplierID`)
        REFERENCES `Supplier` (`SupplierID`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create index for better performance
CREATE INDEX `idx_supplier_orders_supplier` ON `SupplierOrders` (`SupplierID`);
CREATE INDEX `idx_supplier_orders_date` ON `SupplierOrders` (`OrderDate`);

-- Create trigger for LastModified
DELIMITER //
CREATE TRIGGER `before_supplier_order_update`
BEFORE UPDATE ON `SupplierOrders`
FOR EACH ROW
BEGIN
    SET NEW.LastModified = CURRENT_TIMESTAMP;
END //
DELIMITER ; 