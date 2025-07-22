<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection
include('connection.php');

// Get the event date from the POST request
$data = json_decode(file_get_contents("php://input"), true);
$event_date = isset($data['event_date']) ? $data['event_date'] : null;

if ($event_date) {
    // Prepare the SQL query to delete the record
    $sql = "DELETE FROM caterer_booking WHERE event_date = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("s", $event_date);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => $stmt->error]);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to prepare query']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request. Missing event date.']);
}

$conn->close();
?>