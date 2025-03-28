# Reservation System - Installation Guide

Welcome to the **Reservation System** project! This system allows users to book, modify, and cancel table reservations at a restaurant. Follow the steps below to set up the project on your local machine.

---

## **Requirements**

Before starting, make sure you have the following installed:

1. **PHP** (7.4 or higher)
2. **Composer** (for managing PHP dependencies)
3. **MySQL** (for database)
4. **Git** (for cloning the repository)

---

## **Steps for Installation**

### 1. **Clone the Repository**

Clone the repository to your local machine using Git:

```bash
git clone https://github.com/Mahmoud-Eid-Elsayed/restaurant-app.git
cd restaurant-app
```

### 2. **Install PHP Dependencies**

Install the required PHP libraries using Composer:

```bash
composer install
```
```bash
composer require phpmailer/phpmailer
```
### To start using .env files and the Dotenv class
Install the vlucas/phpdotenv package via Composer:

```bash
composer require vlucas/phpdotenv
```

This will download all the necessary dependencies, including PHPMailer, and create a `vendor/` folder.

### 3. **Set Up Environment Variables**

Create a `.env` file in the project root directory to store sensitive data (e.g., SendGrid API key):

```
SENDGRID_API_KEY=your_sendgrid_api_key
```

This will keep your API keys and other sensitive data secure.

### 4. **Set Up the Database**

Make sure your MySQL database is set up with the following schema:

1. **Create the database** (if not already created):
   
   ```sql
   CREATE DATABASE restaurant_reservation;
   ```

2. **Create the tables** using the provided SQL schema files. You can import these from the project if available, or manually run the following commands to create the tables:

```sql
-- Reservation table
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

-- Table table
CREATE TABLE IF NOT EXISTS `Table` (
  `TableID` INT NOT NULL AUTO_INCREMENT,
  `TableNumber` VARCHAR(10) NOT NULL,
  `Capacity` INT NOT NULL,
  `Location` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`TableID`)
) ENGINE = InnoDB;
```

### 5. **Configure Your Web Server**

Ensure that you have a web server (like **XAMPP** or **Apache**) running PHP and can serve the files in the `src/` folder. Ensure PHP is set up to work with the database (MySQL).

### 6. **Configure SendGrid for Email**

PHPMailer is used to send emails through SendGrid. To enable this, create a SendGrid account and get your API key:

- Go to [SendGrid](https://sendgrid.com/), create an account, and get your API key.
- Add the SendGrid API key to the `.env` file as mentioned in step 3.

---

## **Running the Project Locally**

Once everything is set up:

1. **Start your web server** (e.g., Apache or XAMPP).
2. **Navigate to the project directory** and open the file `book_table.php` (or any other file) through the browser:
   
   ```bash
   http://localhost/restaurant-reservation/src/static/php/table-reservation/book_table.php
   ```

---

## **File Structure Overview**

Here is an overview of the project's key directories and files:

```
restaurant-reservation/
├── src/
│   └── static/
│       └── php/
│           └── table-reservation/
│               ├── book_table.php
│               ├── cancel_reservation.php
│               ├── get_available_tables.php
│               └── modify_reservation.php
├── vendor/                 # Composer dependencies
├── .env                    # Environment variables (SendGrid API key)
├── composer.json           # Composer configuration file
└── README.md               # Project documentation
```

---

## **Important Notes**

- **Do not push the `vendor/` folder to GitHub.** The `vendor/` folder contains external libraries and can be regenerated by running `composer install`.
- Ensure **PHP** and **MySQL** are properly configured in your local development environment.

---

## **Troubleshooting**

1. **Composer issues**: If you have issues running `composer install`, make sure **Composer** is installed and in your system’s PATH. You can verify by running `composer -v` in the terminal.
   
2. **SendGrid issues**: Ensure the correct SendGrid API key is added to the `.env` file and that your SendGrid account is active.

---

## **Contributing**

1. **Fork** the repository.
2. Make changes to your forked version.
3. **Create a pull request** for review.
