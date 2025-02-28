-- Create ReservationHistory table
CREATE TABLE IF NOT EXISTS ReservationHistory (
    HistoryID INT PRIMARY KEY AUTO_INCREMENT,
    ReservationID INT NOT NULL,
    Status VARCHAR(50) NOT NULL,
    Notes TEXT,
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ReservationID) REFERENCES Reservation(ReservationID)
);

-- Add trigger to track reservation status changes
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

-- Add trigger for new reservations
DELIMITER //
CREATE TRIGGER after_reservation_insert
AFTER INSERT ON Reservation
FOR EACH ROW
BEGIN
    INSERT INTO ReservationHistory (ReservationID, Status, Notes)
    VALUES (NEW.ReservationID, NEW.ReservationStatus, 'Reservation created');
END //
DELIMITER ; 