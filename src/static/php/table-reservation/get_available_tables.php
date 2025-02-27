<?php
require '../../../../src/static/connection/db.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {

    if (!isset($_GET['date']) || !isset($_GET['time']) || !isset($_GET['guests'])) {
        echo json_encode(["status" => "error", "message" => "Missing required parameters: date, time, or guests."]);
        exit;
    }


    $date = $_GET['date'];
    $time = $_GET['time'];
    $guests = $_GET['guests'];

    error_log("Fetching tables for date: $date, time: $time, guests: $guests");

    try {

        $requestedTime = new DateTime($time);
        $duration = 2;
        $requestedEndTime = (clone $requestedTime)->modify("+$duration hours");


        $stmt = $conn->prepare("
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
        ");


        $stmt->execute([
            ':date' => $date,
            ':start_time' => $requestedTime->format('H:i:s'),
            ':end_time' => $requestedEndTime->format('H:i:s'),
            ':guests' => $guests
        ]);


        $tables = $stmt->fetchAll(PDO::FETCH_ASSOC);


        echo json_encode(["status" => "success", "tables" => $tables]);
    } catch (PDOException $e) {

        error_log("Database error: " . $e->getMessage());
        echo json_encode(["status" => "error", "message" => "Failed to fetch available tables."]);
    }
    exit;
}
?>