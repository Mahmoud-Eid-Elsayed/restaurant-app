-- -----------------------------------------------------
-- Schema Restaurant_DB
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `Restaurant_DB` ;
USE `Restaurant_DB` ;

-- -----------------------------------------------------
-- Table `Restaurant_DB`.`User`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Restaurant_DB`.`User` (
  `UserID` INT NOT NULL AUTO_INCREMENT,
  `Username` VARCHAR(50) NOT NULL UNIQUE,
  `Password` VARCHAR(255) NOT NULL, -- Storing hashed passwords!
  `Role` ENUM('Staff', 'Customer') NOT NULL,
  `FirstName` VARCHAR(50) NULL,
  `LastName` VARCHAR(50) NULL,
  `Email` VARCHAR(100) NULL,
  `PhoneNumber` VARCHAR(20) NULL,
  `Address` VARCHAR(255) NULL,
  `ProfilePictureURL` VARCHAR(255) NULL,
  `RegistrationDate` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `LastLoginDate` TIMESTAMP NULL,
  PRIMARY KEY (`UserID`))
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `Restaurant_DB`.`MenuCategory`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Restaurant_DB`.`MenuCategory` (
  `CategoryID` INT NOT NULL AUTO_INCREMENT,
  `CategoryName` VARCHAR(100) NOT NULL,
  `Description` TEXT NULL,
  `ImageURL` VARCHAR(255) NULL,
  PRIMARY KEY (`CategoryID`))
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `Restaurant_DB`.`MenuItem`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Restaurant_DB`.`MenuItem` (
  `ItemID` INT NOT NULL AUTO_INCREMENT,
  `ItemName` VARCHAR(100) NOT NULL,
  `Description` TEXT NULL,
  `Price` DECIMAL(10,2) NOT NULL,
  `ImageURL` VARCHAR(255) NULL,
  `CategoryID` INT NOT NULL,
  `Availability` BOOLEAN DEFAULT TRUE,
  PRIMARY KEY (`ItemID`),
  INDEX `fk_MenuItem_MenuCategory_idx` (`CategoryID` ASC),
  CONSTRAINT `fk_MenuItem_MenuCategory`
    FOREIGN KEY (`CategoryID`)
    REFERENCES `Restaurant_DB`.`MenuCategory` (`CategoryID`)
    ON DELETE RESTRICT  -- Prevent deleting categories with items
    ON UPDATE CASCADE)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `Restaurant_DB`.`SpecialOffer`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Restaurant_DB`.`SpecialOffer` (
  `OfferID` INT NOT NULL AUTO_INCREMENT,
  `Description` VARCHAR(255) NULL,
  `DiscountPercentage` DECIMAL(5,2) NULL,
  `DiscountAmount` DECIMAL(10,2) NULL,
  `StartDate` DATETIME NOT NULL,
  `ExpiryDate` DATETIME NOT NULL,
  `ItemID` INT NOT NULL,
  `OfferCode` VARCHAR(50) NULL,
  PRIMARY KEY (`OfferID`),
  INDEX `fk_SpecialOffer_MenuItem1_idx` (`ItemID` ASC),
  CONSTRAINT `fk_SpecialOffer_MenuItem1`
    FOREIGN KEY (`ItemID`)
    REFERENCES `Restaurant_DB`.`MenuItem` (`ItemID`)
    ON DELETE CASCADE  -- Deleting an item removes its offers
    ON UPDATE CASCADE)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `Restaurant_DB`.`Order`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Restaurant_DB`.`Order` (
  `OrderID` INT NOT NULL AUTO_INCREMENT,
  `CustomerID` INT NOT NULL,
  `OrderDate` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `OrderStatus` ENUM('Pending', 'Preparing', 'Ready', 'Delivered', 'Cancelled') NOT NULL,
  `TotalAmount` DECIMAL(10,2) NOT NULL,
  `DeliveryAddress` VARCHAR(255) NULL,
  `Notes` TEXT NULL,
  PRIMARY KEY (`OrderID`),
  INDEX `fk_Order_User1_idx` (`CustomerID` ASC),
  CONSTRAINT `fk_Order_User1`
    FOREIGN KEY (`CustomerID`)
    REFERENCES `Restaurant_DB`.`User` (`UserID`)
    ON DELETE RESTRICT -- Prevent deleting users with orders
    ON UPDATE CASCADE)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `Restaurant_DB`.`OrderItem`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Restaurant_DB`.`OrderItem` (
  `OrderItemID` INT NOT NULL AUTO_INCREMENT,
  `OrderID` INT NOT NULL,
  `ItemID` INT NOT NULL,
  `Quantity` INT NOT NULL DEFAULT 1,
  `PriceAtTimeOfOrder` DECIMAL(10,2) NOT NULL,  -- Store price to track changes
  `Customizations` TEXT NULL, -- Store customizations as JSON or text
  PRIMARY KEY (`OrderItemID`),
  INDEX `fk_OrderItem_Order1_idx` (`OrderID` ASC),
  INDEX `fk_OrderItem_MenuItem1_idx` (`ItemID` ASC),
  CONSTRAINT `fk_OrderItem_Order1`
    FOREIGN KEY (`OrderID`)
    REFERENCES `Restaurant_DB`.`Order` (`OrderID`)
    ON DELETE CASCADE,  -- Deleting an order removes its items
  CONSTRAINT `fk_OrderItem_MenuItem1`
    FOREIGN KEY (`ItemID`)
    REFERENCES `Restaurant_DB`.`MenuItem` (`ItemID`)
    ON DELETE RESTRICT -- Prevent deleting items still in orders
    ON UPDATE CASCADE)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `Restaurant_DB`.`Payment`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Restaurant_DB`.`Payment` (
  `PaymentID` INT NOT NULL AUTO_INCREMENT,
  `OrderID` INT NULL, -- Optional: Payment might not be for a specific order
  `PaymentDate` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `PaymentMethod` ENUM('Credit Card', 'Cash', 'Online') NOT NULL,
  `Amount` DECIMAL(10,2) NOT NULL,
  `TransactionID` VARCHAR(100) NULL,
  PRIMARY KEY (`PaymentID`),
  INDEX `fk_Payment_Order1_idx` (`OrderID` ASC),
  CONSTRAINT `fk_Payment_Order1`
    FOREIGN KEY (`OrderID`)
    REFERENCES `Restaurant_DB`.`Order` (`OrderID`)
    ON DELETE SET NULL  -- Deleting an order doesn't remove the payment, just disconnects it
    ON UPDATE CASCADE)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `Restaurant_DB`.`Table`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Restaurant_DB`.`Table` (
  `TableID` INT NOT NULL AUTO_INCREMENT,
  `TableNumber` VARCHAR(50) NOT NULL,
  `Capacity` INT NOT NULL,
  `Location` VARCHAR(255) NULL,
  PRIMARY KEY (`TableID`)
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `Restaurant_DB`.`Reservation`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Restaurant_DB`.`Reservation` (
  `ReservationID` INT NOT NULL AUTO_INCREMENT,
  `CustomerName` VARCHAR(100) NOT NULL,
  `CustomerEmail` VARCHAR(100) NOT NULL,
  `CustomerPhone` VARCHAR(20) NOT NULL,
  `TableID` INT NOT NULL,
  `ReservationDate` DATE NOT NULL,
  `ReservationTime` TIME NOT NULL,
  `NumberOfGuests` INT NOT NULL,
  `ReservationStatus` ENUM('Pending', 'Confirmed', 'Cancelled', 'Completed') NOT NULL DEFAULT 'Pending',
  `Notes` TEXT NULL,
  PRIMARY KEY (`ReservationID`),
  INDEX `fk_Reservation_Table1_idx` (`TableID` ASC),
  CONSTRAINT `fk_Reservation_Table1`
    FOREIGN KEY (`TableID`)
    REFERENCES `Table` (`TableID`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `Restaurant_DB`.`Supplier`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Restaurant_DB`.`Supplier` (
  `SupplierID` INT NOT NULL AUTO_INCREMENT,
  `SupplierName` VARCHAR(100) NOT NULL,
  `ContactPerson` VARCHAR(100) NULL,
  `Email` VARCHAR(100) NULL,
  `PhoneNumber` VARCHAR(20) NULL,
  `Address` VARCHAR(255) NULL,
  PRIMARY KEY (`SupplierID`)
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `Restaurant_DB`.`SupplierOrders`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Restaurant_DB`.`SupplierOrders` (
    `OrderID` INT NOT NULL AUTO_INCREMENT,
    `SupplierID` INT NOT NULL,
    `OrderDate` DATE NOT NULL,
    `TotalAmount` DECIMAL(10, 2) NOT NULL,
    `Status` ENUM('Pending', 'Shipped', 'Delivered') DEFAULT 'Pending',
    PRIMARY KEY (`OrderID`),
    CONSTRAINT `fk_SupplierOrders_Supplier`
        FOREIGN KEY (`SupplierID`)
        REFERENCES `Supplier` (`SupplierID`)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table `Restaurant_DB`.`InventoryItem`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Restaurant_DB`.`InventoryItem` (
  `InventoryItemID` INT NOT NULL AUTO_INCREMENT,
  `ItemName` VARCHAR(100) NOT NULL,
  `QuantityInStock` DECIMAL(10,2) NOT NULL DEFAULT 0,
  `UnitOfMeasurement` VARCHAR(50) NOT NULL,
  `ReorderLevel` DECIMAL(10,2) NOT NULL DEFAULT 0,
  `SupplierID` INT NOT NULL,
  `LastPurchasedDate` TIMESTAMP NULL,
  PRIMARY KEY (`InventoryItemID`),
  INDEX `fk_InventoryItem_Supplier1_idx` (`SupplierID` ASC),
  CONSTRAINT `fk_InventoryItem_Supplier1`
    FOREIGN KEY (`SupplierID`)
    REFERENCES `Restaurant_DB`.`Supplier` (`SupplierID`)
    ON DELETE CASCADE  -- Delete inventory items when supplier is deleted
    ON UPDATE CASCADE)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `Restaurant_DB`.`Notification`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `Restaurant_DB`.`Notification` (
  `NotificationID` INT NOT NULL AUTO_INCREMENT,
  `UserID` INT NULL, -- Optional:  NULL for general notifications
  `OrderID` INT NULL,
  `ReservationID` INT NULL,
  `NotificationType` VARCHAR(100) NOT NULL,
  `Message` TEXT NOT NULL,
  `Timestamp` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `IsRead` BOOLEAN DEFAULT FALSE,
  PRIMARY KEY (`NotificationID`),
  INDEX `fk_Notification_User1_idx` (`UserID` ASC),
  INDEX `fk_Notification_Order1_idx` (`OrderID` ASC),
  INDEX `fk_Notification_Reservation1_idx` (`ReservationID` ASC),
    CONSTRAINT `fk_Notification_User1`
    FOREIGN KEY (`UserID`)
    REFERENCES `Restaurant_DB`.`User` (`UserID`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  CONSTRAINT `fk_Notification_Order1`
    FOREIGN KEY (`OrderID`)
    REFERENCES `Restaurant_DB`.`Order` (`OrderID`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_Notification_Reservation1`
    FOREIGN KEY (`ReservationID`)
    REFERENCES `Restaurant_DB`.`Reservation` (`ReservationID`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;