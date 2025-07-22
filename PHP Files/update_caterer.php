<?php
include('connection.php');

// Fetch event_date and caterer_id from GET parameters
$event_date = isset($_GET['event_date']) ? trim($_GET['event_date']) : '';
$caterer_id = isset($_GET['caterer_id']) ? intval($_GET['caterer_id']) : null;

// Validate event_date format (YYYY-MM-DD)
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $event_date)) {
    die("Invalid event date format. Please use YYYY-MM-DD.");
}

// Validate caterer_id
if ($caterer_id <= 0) {
    die("Invalid caterer ID.");
}

// Ensure both values are present
if (empty($event_date) || empty($caterer_id)) {
    die("Event date and caterer ID are required.");
}

// Fetch existing booking details
$sql = "SELECT * FROM caterer_booking WHERE event_date = ? AND caterer_id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("si", $event_date, $caterer_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $booking = $result->fetch_assoc();
} else {
    die("No booking found for the specified date and caterer.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize input
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    if (empty($name) || empty($email) || empty($phone)) {
        die("All fields are required.");
    }

    // Update the booking in the database
    $sql_update = "UPDATE caterer_booking 
                   SET name = ?, email = ?, contact_number = ? 
                   WHERE event_date = ? AND caterer_id = ?";
    $stmt_update = $conn->prepare($sql_update);
    if (!$stmt_update) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt_update->bind_param("ssssi", $name, $email, $phone, $event_date, $caterer_id);

    if ($stmt_update->execute()) {
        echo "Booking updated successfully.";
    } else {
        echo "Error updating booking: " . $stmt_update->error;
    }
    $stmt_update->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Caterer Booking</title>
</head>
<body>
    <h2>Edit Caterer Booking</h2>
    <form method="post" action="update_caterer.php">
        <input type="hidden" name="event_date" value="<?php echo htmlspecialchars($event_date); ?>">
        <input type="hidden" name="caterer_id" value="<?php echo htmlspecialchars($caterer_id); ?>">
        <label for="name">User Name:</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($booking['name']); ?>" required>
        <br>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($booking['email']); ?>" required>
        <br>
        <label for="phone">Phone:</label>
        <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($booking['contact_number']); ?>" required>
        <br>
        <button type="submit">Save Changes</button>
    </form>
</body>
</html>