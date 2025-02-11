## **Reservation System Overview**

This system allows users to book, modify, and cancel table reservations at a restaurant. It includes the following key files:

1. **`book_table.php`**
2. **`get_available_tables.php`**
3. **`cancel_reservation.php`**
4. **`modify_reservation.php`**

---

### **1. `book_table.php`**

This file handles the booking of a table. It includes:

- **Form Submission**: Users fill out a form with their details (name, email, phone, date, time, number of guests, table selection, and message).
- **Database Insertion**: The reservation details are inserted into the `Reservation` table in the database.
- **Email Confirmation**: A confirmation email is sent to the user using **PHPMailer** and **SendGrid**.
- **Dynamic Table Fetching**: The available tables are fetched dynamically based on the selected date, time, and number of guests.

#### **Key Functions:**

- **`fetchAvailableTables()`**: Fetches available tables from the database based on the selected date, time, and number of guests.
- **Form Submission**: Submits the form data to `book_table.php` using `fetch()` (AJAX).
- **Email Sending**: Sends a confirmation email with links to modify or cancel the reservation.

---

### **2. `get_available_tables.php`**

This file fetches the available tables based on the selected date, time, and number of guests.

#### **Key Features:**

- **Overlap Checking**: Ensures that tables are not double-booked by checking for overlapping reservations.
- **SQL Query**: Filters tables that are not reserved during the requested time slot.
- **Response**: Returns a JSON response with the list of available tables.

#### **Example Query:**

```sql
SELECT t.TableID, t.TableNumber, t.Capacity, t.Location
FROM `Table` t
WHERE t.Capacity >= :guests
AND t.TableID NOT IN (
    SELECT r.TableID
    FROM Reservation r
    WHERE r.ReservationDate = :date
    AND (
        (r.ReservationTime >= :start_time AND r.ReservationTime < :end_time) OR
        (ADDTIME(r.ReservationTime, '02:00:00') > :start_time AND r.ReservationTime <= :start_time)
    )
    AND r.ReservationStatus IN ('Pending', 'Confirmed')
)
```

---

### **3. `cancel_reservation.php`**

This file allows users to cancel their reservation.

#### **Key Features:**

- **Reservation ID**: The reservation ID is passed as a query parameter (`?id=123`).
- **Status Update**: Updates the `ReservationStatus` to `Cancelled` in the database.
- **Response**: Displays a success or error message.

#### **Example Query:**

```sql
UPDATE Reservation
SET ReservationStatus = 'Cancelled'
WHERE ReservationID = :reservation_id
```

---

### **4. `modify_reservation.php`**

This file allows users to modify their reservation.

#### **Key Features:**

- **Form Submission**: Users can update their reservation details (name, email, phone, date, time, number of guests, table selection, and message).
- **Database Insertion**: Inserts the updated reservation details into the `Reservation` table.
- **Email Confirmation**: Sends a confirmation email with the updated reservation details.

#### **Key Functions:**

- **`fetchAvailableTables()`**: Fetches available tables dynamically.
- **Form Submission**: Submits the updated reservation details to `modify_reservation.php`.

---

### **Database Schema**

The system uses the following tables:

#### **`Reservation` Table**

```sql
CREATE TABLE IF NOT EXISTS `Reservation` (
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
```

#### **`Table` Table**

```sql
CREATE TABLE IF NOT EXISTS `Table` (
  `TableID` INT NOT NULL AUTO_INCREMENT,
  `TableNumber` VARCHAR(10) NOT NULL,
  `Capacity` INT NOT NULL,
  `Location` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`TableID`)
) ENGINE = InnoDB;
```

---

### **Key Dependencies**

- **PHPMailer**: Used for sending confirmation emails.
- **Dotenv**: Loads environment variables (e.g., SendGrid API key) from a `.env` file.
- **Bootstrap**: Used for styling the frontend.

---

### **Workflow**

1. **Book a Table**:

   - User fills out the booking form.
   - The system checks for available tables and inserts the reservation into the database.
   - A confirmation email is sent to the user.

2. **Modify a Reservation**:

   - User updates their reservation details.
   - The system updates the reservation in the database and sends a confirmation email.

3. **Cancel a Reservation**:

   - User clicks the "Cancel Reservation" link in the confirmation email.
   - The system updates the reservation status to `Cancelled`.

4. **Fetch Available Tables**:
   - The system dynamically fetches available tables based on the selected date, time, and number of guests.

---

### **Error Handling**

- **Database Errors**: Logged and displayed to the user in a user-friendly manner.
- **Email Errors**: Logged and displayed if the confirmation email fails to send.

---

### **Frontend**

- The frontend is built using **Bootstrap** for responsive design.
- JavaScript handles form submission and dynamic table fetching.
