<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../../../../src/static/connection/db.php';
require __DIR__ . '/../../../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load the .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $guests = $_POST['guests'];
    $table_id = $_POST['table_id'];
    $message = $_POST['message'];

    try {

        $stmt = $conn->prepare("
            INSERT INTO Reservation (CustomerName, CustomerEmail, CustomerPhone, TableID, ReservationDate, ReservationTime, NumberOfGuests, ReservationStatus, Notes)
            VALUES (:name, :email, :phone, :table_id, :date, :time, :guests, 'Pending', :notes)
        ");

        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':phone' => $phone,
            ':table_id' => $table_id,
            ':date' => $date,
            ':time' => $time,
            ':guests' => $guests,
            ':notes' => $message
        ]);

        $reservation_id = $conn->lastInsertId();
        $mail = new PHPMailer(true);

        try {

            $mail->isSMTP();
            $mail->Host = 'smtp.sendgrid.net';
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['MAIL_USERNAME'];
            $mail->Password = $_ENV['SENDGRID_API_KEY'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->SMTPDebug = 0;

            $mail->setFrom('macawilo@asciibinder.net', 'El Chef');
            $mail->addAddress($email, $name);
            $mail->addReplyTo('macawilo@asciibinder.net', 'El-Chef');

            $mail->isHTML(true);
            $mail->Subject = 'Reservation Confirmation';
            $mail->Body = "Dear $name,<br><br>Thank you for your reservation!<br><br>
                              Reservation Details:<br>
                              Date: $date<br>
                              Time: $time<br>
                              Number of Guests: $guests<br>
                              Table ID: $table_id<br><br>
                              You can cancel or modify your reservation using the following links:<br>
                              <a href='http://localhost/restaurant-app/src/static/php/table-reservation/cancel_reservation.php?id=$reservation_id'>Cancel Reservation</a><br>
                              <a href='http://localhost/restaurant-app/src/static/php/table-reservation/modify_reservation.php?id=$reservation_id'>Modify Reservation</a><br><br>
                              Thank you for choosing us!";
            $mail->AltBody = "Dear $name,\n\nThank you for your reservation!\n\n
                              Reservation Details:\n
                              Date: $date\n
                              Time: $time\n
                              Number of Guests: $guests\n
                              Table ID: $table_id\n\n
                              You can cancel or modify your reservation using the following links:\n
                              Cancel Reservation: http://localhost/restaurant-app/src/static/php/table-reservation/cancel_reservation.php?id=$reservation_id\n
                              Modify Reservation: http://localhost/restaurant-app/src/static/php/table-reservation/modify_reservation.php?id=$reservation_id\n\n
                              Thank you for choosing us!";

            $mail->send();
            echo json_encode(["status" => "success", "message" => "Your reservation is completed. Please check your email."]);
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => "Failed to send confirmation email. Please contact support. Error: {$mail->ErrorInfo}"]);
        }
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "Failed to book the table. Please try again later."]);
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Table</title>
    <link href="../../../../src/assets/libraries/node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .resr {
            margin-top: 50px;
        }

        #reservation-pic {
            border-radius: 10px;
        }

        #rsrv-rest {
            color: #333;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container my-5 resr">
        <div class="row">
            <div class="col-md-6">
                <img src="../../../assets/images/reservation/reservation-pic.webp" class="img-fluid"
                    id="reservation-pic" alt="Book a Table">
            </div>
            <div class="col-md-6">
                <h2 class="text-center" id="rsrv-rest">Book a Table 🍽️</h2>
                <form id="bookingForm">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="text" class="form-control" id="phone" name="phone" required>
                    </div>
                    <div class="mb-3">
                        <label for="date" class="form-label">Booking Date</label>
                        <input type="date" class="form-control" id="date" name="date" required>
                    </div>
                    <div class="mb-3">
                        <label for="time" class="form-label">Booking Time</label>
                        <input type="time" class="form-control" id="time" name="time" required>
                    </div>
                    <div class="mb-3">
                        <label for="guests" class="form-label">Number of Guests</label>
                        <input type="number" class="form-control" id="guests" name="guests" required>
                    </div>
                    <div class="mb-3">
                        <label for="table_id" class="form-label">Select Table</label>
                        <select class="form-control" id="table_id" name="table_id" required>
                            <option value="">Select a table</option>

                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">Message</label>
                        <textarea class="form-control" id="message" name="message" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Book Now</button>
                </form>

                <div id="confirmationMessage" class="mt-3 alert alert-success d-none">
                    Thank you for your booking! Please check your email for confirmation.
                </div>
            </div>
        </div>
    </div>

    <script>

        function fetchAvailableTables() {
            const date = document.getElementById('date').value;
            const time = document.getElementById('time').value;
            const guests = document.getElementById('guests').value;

            console.log("Fetching available tables for:", date, time, guests);

            fetch(`get_available_tables.php?date=${date}&time=${time}&guests=${guests}`)
                .then(response => response.json())
                .then(data => {
                    console.log("Response from server:", data);

                    if (data.status === "success") {
                        const tableSelect = document.getElementById('table_id');
                        tableSelect.innerHTML = '<option value="">Select a table</option>';

                        data.tables.forEach(table => {
                            const option = document.createElement('option');
                            option.value = table.TableID;
                            option.textContent = `Table ${table.TableNumber} (Capacity: ${table.Capacity}, Location: ${table.Location})`;
                            tableSelect.appendChild(option);
                        });
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert("Failed to fetch available tables. Please try again later.");
                });
        }

        document.getElementById('date').addEventListener('change', fetchAvailableTables);
        document.getElementById('time').addEventListener('change', fetchAvailableTables);
        document.getElementById('guests').addEventListener('change', fetchAvailableTables);

        document.getElementById('bookingForm').addEventListener('submit', function (e) {
            e.preventDefault();
            console.log("Form submitted!");
            const formData = new FormData(this);

            fetch('book_table.php', {
                method: 'POST',
                body: formData
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status === "success") {
                        document.getElementById('confirmationMessage').classList.remove('d-none');
                        document.getElementById('confirmationMessage').textContent = data.message;
                        document.getElementById('bookingForm').reset();

                        fetchAvailableTables();
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert("An error occurred. Please check the console for details.");
                });
        });

        fetchAvailableTables();
    </script>
</body>

</html>