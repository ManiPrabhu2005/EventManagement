<?php
// Database connection
include('connection.php');

// Retrieve and validate form data
$event_date = isset($_POST['event_date']) ? trim($_POST['event_date']) : '';
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$contact_number = isset($_POST['contact_number']) ? trim($_POST['contact_number']) : '';
$event_name = isset($_POST['event_name']) ? trim($_POST['event_name']) : '';
$num_guests = isset($_POST['num_guests']) ? trim($_POST['num_guests']) : '';

if (empty($event_date) || empty($name) || empty($email) || empty($contact_number) || empty($event_name) || empty($num_guests)) {
    die("Error: Missing required fields.");
}

// Update the venue booking in the database
$sql = "UPDATE venue_booking 
        SET name = ?, email = ?, contact_number = ?, event_name = ?, num_guests = ?
        WHERE event_date = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Error preparing statement: " . $conn->error);
}

$stmt->bind_param("ssssis", $name, $email, $contact_number, $event_name, $num_guests, $event_date);

if ($stmt->execute()) {
    // Redirect with success status
    header("Location: adminpage.php?status=success");
} else {
    // Redirect with error status
    header("Location: adminpage.php?status=error");
}

// Close resources
$stmt->close();
$conn->close();

exit();
?>