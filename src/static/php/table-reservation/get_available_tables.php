<?php
require '../../../../src/static/connection/db.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {

    // Check if required parameters are set
    if (!isset($_GET['date']) || !isset($_GET['time']) || !isset($_GET['guests'])) {
        echo json_encode(["status" => "error", "message" => "Missing required parameters: date, time, or guests."]);
        exit;
    }

    // Sanitize input
    $date = filter_input(INPUT_GET, 'date', FILTER_SANITIZE_STRING);
    $time = filter_input(INPUT_GET, 'time', FILTER_SANITIZE_STRING);
    $guests = filter_input(INPUT_GET, 'guests', FILTER_SANITIZE_NUMBER_INT);

    // Validate date and time
    if (empty($date) || empty($time)) {
        echo json_encode(["status" => "error", "message" => "Date and time cannot be empty."]);
        exit;
    }

    // Validate date format (YYYY-MM-DD)
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
        echo json_encode(["status" => "error", "message" => "Invalid date format. Expected format: YYYY-MM-DD."]);
        exit;
    }

    // Validate time format (HH:MM)
    if (!preg_match('/^\d{2}:\d{2}$/', $time)) {
        echo json_encode(["status" => "error", "message" => "Invalid time format. Expected format: HH:MM."]);
        exit;
    }

    error_log("Fetching tables for date: $date, time: $time, guests: $guests");

    try {
        // Check if the database connection is valid
        if (!$conn) {
            throw new Exception("Database connection failed.");
        }

        // Calculate the end time based on the duration (2 hours)
        $requestedTime = new DateTime($time);
        $duration = 2;
        $requestedEndTime = (clone $requestedTime)->modify("+$duration hours");

        // Prepare the SQL query
        $sql = "
            SELECT t.TableID, t.TableNumber, t.Capacity, t.Location
            FROM `table` t
            WHERE t.Capacity >= :guests
            AND t.TableID NOT IN (
                SELECT r.TableID
                FROM reservation r
                WHERE r.ReservationDate = :date
                AND (
                    (r.ReservationTime >= :start_time AND r.ReservationTime < :end_time) OR
                    (ADDTIME(r.ReservationTime, '02:00:00') > :start_time AND r.ReservationTime <= :start_time)
                )
                AND r.ReservationStatus IN ('Pending', 'Confirmed')
            )
        ";

        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            throw new Exception("Failed to prepare the SQL statement.");
        }

        // Execute the query with parameters
        $stmt->execute([
            ':date' => $date,
            ':start_time' => $requestedTime->format('H:i:s'),
            ':end_time' => $requestedEndTime->format('H:i:s'),
            ':guests' => $guests
        ]);

        // Fetch the results
        $tables = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($tables === false) {
            throw new Exception("Failed to fetch available tables.");
        }

        // Return the results as JSON
        echo json_encode(["status" => "success", "tables" => $tables]);

    } catch (PDOException $e) {
        // Log the database error
        error_log("Database error: " . $e->getMessage());
        echo json_encode(["status" => "error", "message" => "Database error occurred: " . $e->getMessage()]);
    } catch (Exception $e) {
        // Log any other exceptions
        error_log("Error: " . $e->getMessage());
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
    exit;
}
?>