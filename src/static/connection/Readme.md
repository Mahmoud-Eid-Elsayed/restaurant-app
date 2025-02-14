# Database Connection (`db.php`)

This file handles the connection to the MySQL database using PHP's PDO (PHP Data Objects). It is used by other PHP scripts to interact with the database.

## What it does:

- Establishes a connection to the MySQL database.
- Sets the error mode to exception for better error handling.
- If the connection fails, it terminates the script and displays an error message.

## Configuration:

- **Host**: `localhost`
- **Port**: `chnage it depen on your used port`
- **Database Name**: `Restaurant_DB`
- **Username**: `root`
- **Password**: (empty by default)

## Usage:

- Include this file in other PHP scripts using `require 'db.php';`.
- The `$conn` variable is the PDO connection object used for database queries.

## Example:

```php
require 'db.php';
$stmt = $conn->prepare("SELECT * FROM Reservation");
$stmt->execute();
$reservations = $stmt->fetchAll();
```
