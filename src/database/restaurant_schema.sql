-- Restaurant Management System Database Schema
-- Version: 2.0
-- Description: Comprehensive schema for restaurant management including users, orders, menu, inventory, and reservations

-- Drop database if exists and create new one
DROP DATABASE IF EXISTS Restaurant_DB;
CREATE DATABASE Restaurant_DB;
USE Restaurant_DB;

-- Set character set
SET NAMES utf8mb4;
SET character_set_client = utf8mb4;

-- User Management
CREATE TABLE User (
    UserID INT NOT NULL AUTO_INCREMENT,
    Username VARCHAR(50) NOT NULL UNIQUE,
    Password VARCHAR(255) NOT NULL,
    Role ENUM('Admin', 'Staff', 'Customer') NOT NULL DEFAULT 'Customer',
    FirstName VARCHAR(50),
    LastName VARCHAR(50),
    Email VARCHAR(100) NOT NULL UNIQUE,
    PhoneNumber VARCHAR(20),
    Address TEXT,
    ProfilePictureURL VARCHAR(255),
    RegistrationDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    LastLoginDate TIMESTAMP NULL,
    LastModified TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    Status ENUM('active', 'inactive') DEFAULT 'active',
    PRIMARY KEY (UserID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Customer Statistics
CREATE TABLE CustomerStats (
    CustomerID INT NOT NULL,
    TotalSpent DECIMAL(10,2) DEFAULT 0.00,
    LastOrderDate DATETIME,
    OrderCount INT DEFAULT 0,
    PRIMARY KEY (CustomerID),
    FOREIGN KEY (CustomerID) REFERENCES User(UserID) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Menu Categories
CREATE TABLE MenuCategory (
    CategoryID INT NOT NULL AUTO_INCREMENT,
    CategoryName VARCHAR(100) NOT NULL,
    Description TEXT,
    ImageURL VARCHAR(255),
    PRIMARY KEY (CategoryID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Menu Items
CREATE TABLE MenuItem (
    ItemID INT NOT NULL AUTO_INCREMENT,
    ItemName VARCHAR(100) NOT NULL,
    Description TEXT,
    Price DECIMAL(10,2) NOT NULL,
    ImageURL VARCHAR(255),
    CategoryID INT NOT NULL,
    Availability BOOLEAN DEFAULT TRUE,
    PRIMARY KEY (ItemID),
    FOREIGN KEY (CategoryID) REFERENCES MenuCategory(CategoryID) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Suppliers
CREATE TABLE Supplier (
    SupplierID INT NOT NULL AUTO_INCREMENT,
    SupplierName VARCHAR(100) NOT NULL,
    ContactPerson VARCHAR(100),
    Email VARCHAR(100),
    Phone VARCHAR(20),
    Address TEXT,
    PRIMARY KEY (SupplierID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Inventory Management
CREATE TABLE InventoryItem (
    InventoryItemID INT NOT NULL AUTO_INCREMENT,
    ItemName VARCHAR(100) NOT NULL,
    QuantityInStock DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    UnitOfMeasurement VARCHAR(50) NOT NULL,
    ReorderLevel DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    SupplierID INT NOT NULL,
    LastPurchasedDate TIMESTAMP NULL,
    PRIMARY KEY (InventoryItemID),
    FOREIGN KEY (SupplierID) REFERENCES Supplier(SupplierID) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tables Management
CREATE TABLE `Table` (
    TableID INT NOT NULL AUTO_INCREMENT,
    TableNumber VARCHAR(10) NOT NULL,
    Capacity INT NOT NULL,
    Location VARCHAR(50) NOT NULL,
    Status ENUM('Available', 'Occupied', 'Reserved', 'Maintenance') DEFAULT 'Available',
    PRIMARY KEY (TableID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Reservations
CREATE TABLE Reservation (
    ReservationID INT NOT NULL AUTO_INCREMENT,
    CustomerID INT,
    TableID INT NOT NULL,
    CustomerName VARCHAR(100) NOT NULL,
    CustomerEmail VARCHAR(100) NOT NULL,
    CustomerPhone VARCHAR(20) NOT NULL,
    ReservationDate DATE NOT NULL,
    ReservationTime TIME NOT NULL,
    NumberOfGuests INT NOT NULL,
    ReservationStatus ENUM('Pending', 'Confirmed', 'Cancelled', 'Completed') DEFAULT 'Pending',
    Notes TEXT,
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    LastModified TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (ReservationID),
    FOREIGN KEY (CustomerID) REFERENCES User(UserID) ON DELETE SET NULL,
    FOREIGN KEY (TableID) REFERENCES `Table`(TableID) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Reservation History
CREATE TABLE ReservationHistory (
    HistoryID INT NOT NULL AUTO_INCREMENT,
    ReservationID INT NOT NULL,
    Status VARCHAR(50) NOT NULL,
    Notes TEXT,
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (HistoryID),
    FOREIGN KEY (ReservationID) REFERENCES Reservation(ReservationID) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Orders
CREATE TABLE `Order` (
    OrderID INT NOT NULL AUTO_INCREMENT,
    CustomerID INT NOT NULL,
    OrderDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    OrderStatus ENUM('Pending', 'Processing', 'Completed', 'Cancelled', 'Refunded') NOT NULL DEFAULT 'Pending',
    TotalAmount DECIMAL(10,2) NOT NULL,
    DeliveryAddress TEXT,
    Notes TEXT,
    LastModified TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    RefundReason TEXT,
    RefundDate TIMESTAMP NULL,
    PRIMARY KEY (OrderID),
    FOREIGN KEY (CustomerID) REFERENCES User(UserID) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Order Items
CREATE TABLE OrderItem (
    OrderItemID INT NOT NULL AUTO_INCREMENT,
    OrderID INT NOT NULL,
    ItemID INT NOT NULL,
    Quantity INT NOT NULL DEFAULT 1,
    PriceAtTimeOfOrder DECIMAL(10,2) NOT NULL,
    Customizations TEXT,
    PRIMARY KEY (OrderItemID),
    FOREIGN KEY (OrderID) REFERENCES `Order`(OrderID) ON DELETE CASCADE,
    FOREIGN KEY (ItemID) REFERENCES MenuItem(ItemID) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Order Status History
CREATE TABLE OrderStatusHistory (
    HistoryID INT NOT NULL AUTO_INCREMENT,
    OrderID INT NOT NULL,
    StatusFrom ENUM('Pending', 'Processing', 'Completed', 'Cancelled', 'Refunded') NOT NULL,
    StatusTo ENUM('Pending', 'Processing', 'Completed', 'Cancelled', 'Refunded') NOT NULL,
    ChangedAt TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    Notes TEXT,
    ChangedBy VARCHAR(50) DEFAULT 'admin',
    PRIMARY KEY (HistoryID),
    FOREIGN KEY (OrderID) REFERENCES `Order`(OrderID) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Payments
CREATE TABLE Payment (
    PaymentID INT NOT NULL AUTO_INCREMENT,
    OrderID INT NOT NULL,
    Amount DECIMAL(10,2) NOT NULL,
    PaymentMethod ENUM('Cash', 'Credit Card', 'Debit Card', 'Online') NOT NULL,
    PaymentStatus ENUM('Pending', 'Completed', 'Failed', 'Refunded') NOT NULL DEFAULT 'Pending',
    TransactionID VARCHAR(100),
    PaymentDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (PaymentID),
    FOREIGN KEY (OrderID) REFERENCES `Order`(OrderID) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Refunds
CREATE TABLE Refund (
    RefundID INT NOT NULL AUTO_INCREMENT,
    OrderID INT NOT NULL,
    RefundAmount DECIMAL(10,2) NOT NULL,
    RefundDate TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    RefundReason TEXT NOT NULL,
    Status ENUM('Pending', 'Processed', 'Failed') NOT NULL DEFAULT 'Pending',
    ProcessedAt TIMESTAMP NULL,
    Notes TEXT,
    PRIMARY KEY (RefundID),
    FOREIGN KEY (OrderID) REFERENCES `Order`(OrderID) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Notifications
CREATE TABLE Notification (
    NotificationID INT NOT NULL AUTO_INCREMENT,
    UserID INT,
    OrderID INT,
    ReservationID INT,
    NotificationType VARCHAR(100) NOT NULL,
    Message TEXT NOT NULL,
    Timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    IsRead BOOLEAN DEFAULT FALSE,
    PRIMARY KEY (NotificationID),
    FOREIGN KEY (UserID) REFERENCES User(UserID) ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY (OrderID) REFERENCES `Order`(OrderID) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (ReservationID) REFERENCES Reservation(ReservationID) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Special Offers
CREATE TABLE SpecialOffer (
    OfferID INT NOT NULL AUTO_INCREMENT,
    OfferName VARCHAR(100) NOT NULL,
    Description TEXT,
    DiscountPercentage DECIMAL(5,2),
    StartDate DATE NOT NULL,
    EndDate DATE NOT NULL,
    IsActive BOOLEAN DEFAULT TRUE,
    PRIMARY KEY (OfferID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Revenue Tracking
CREATE TABLE Revenue (
    RevenueID INT NOT NULL AUTO_INCREMENT,
    Date DATE NOT NULL,
    TotalRevenue DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    OrderCount INT DEFAULT 0,
    AverageOrderValue DECIMAL(10,2) DEFAULT 0.00,
    Notes TEXT,
    PRIMARY KEY (RevenueID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Reviews
CREATE TABLE Review (
    ReviewID INT NOT NULL AUTO_INCREMENT,
    CustomerID INT,
    OrderID INT,
    Rating INT NOT NULL CHECK (Rating >= 1 AND Rating <= 5),
    Comment TEXT,
    ReviewDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    IsPublished BOOLEAN DEFAULT TRUE,
    PRIMARY KEY (ReviewID),
    FOREIGN KEY (CustomerID) REFERENCES User(UserID) ON DELETE SET NULL,
    FOREIGN KEY (OrderID) REFERENCES `Order`(OrderID) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create triggers for tracking changes

-- Trigger for updating LastModified in User table
DELIMITER //
CREATE TRIGGER before_user_update
BEFORE UPDATE ON User
FOR EACH ROW
BEGIN
    SET NEW.LastModified = CURRENT_TIMESTAMP;
END //
DELIMITER ;

-- Trigger for reservation status changes
DELIMITER //
CREATE TRIGGER after_reservation_update
AFTER UPDATE ON Reservation
FOR EACH ROW
BEGIN
    IF OLD.ReservationStatus != NEW.ReservationStatus THEN
        INSERT INTO ReservationHistory (ReservationID, Status, Notes)
        VALUES (NEW.ReservationID, NEW.ReservationStatus, 
                CONCAT('Status changed from ', OLD.ReservationStatus, ' to ', NEW.ReservationStatus));
    END IF;
END //
DELIMITER ;

-- Trigger for new reservations
DELIMITER //
CREATE TRIGGER after_reservation_insert
AFTER INSERT ON Reservation
FOR EACH ROW
BEGIN
    INSERT INTO ReservationHistory (ReservationID, Status, Notes)
    VALUES (NEW.ReservationID, NEW.ReservationStatus, 'Reservation created');
END //
DELIMITER ;

-- Trigger for updating customer stats after order
DELIMITER //
CREATE TRIGGER after_order_update
AFTER UPDATE ON `Order`
FOR EACH ROW
BEGIN
    IF NEW.OrderStatus = 'Completed' AND OLD.OrderStatus != 'Completed' THEN
        INSERT INTO CustomerStats (CustomerID, TotalSpent, LastOrderDate, OrderCount)
        VALUES (NEW.CustomerID, NEW.TotalAmount, NEW.OrderDate, 1)
        ON DUPLICATE KEY UPDATE
            TotalSpent = TotalSpent + NEW.TotalAmount,
            LastOrderDate = NEW.OrderDate,
            OrderCount = OrderCount + 1;
    END IF;
END //
DELIMITER ; 