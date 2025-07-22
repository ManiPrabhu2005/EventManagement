<?php
// Database connection
include('connection.php');

// Retrieve the event date from the query string
$event_date = isset($_GET['date']) ? $_GET['date'] : null;

if ($event_date) {
    // Fetch the existing booking details for the given event date
    $sql_fetch = "SELECT * FROM photo_booking WHERE event_date = ?";
    $stmt_fetch = $conn->prepare($sql_fetch);

    if ($stmt_fetch) {
        $stmt_fetch->bind_param("s", $event_date);
        $stmt_fetch->execute();
        $result_fetch = $stmt_fetch->get_result();

        if ($result_fetch->num_rows > 0) {
            $booking = $result_fetch->fetch_assoc();
        } else {
            die("No booking found for the specified date.");
        }
    } else {
        die("Prepare failed: " . $conn->error);
    }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Retrieve form data
        $new_event_date = trim($_POST['event_date']);
        $photographer_id = trim($_POST['photographer_id']);
        $user_name = trim($_POST['user_name']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);

        // Update the booking in the database
        $sql_update = "UPDATE photo_booking SET event_date = ?, photographer_id = ?, User_name = ?, email = ?, phone = ? WHERE event_date = ?";
        $stmt_update = $conn->prepare($sql_update);

        if ($stmt_update) {
            $stmt_update->bind_param("sissss", $new_event_date, $photographer_id, $user_name, $email, $phone, $event_date);

            if ($stmt_update->execute()) {
                echo "<script>alert('Booking updated successfully.'); window.location.href = 'adminpage.php';</script>";
                exit;
            } else {
                echo "Error updating booking: " . $stmt_update->error;
            }

            $stmt_update->close();
        } else {
            echo "Prepare failed: " . $conn->error;
        }
    }
} else {
    die("Invalid request. Missing event date.");
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Photographer Booking</title>
    <style>
        /* Same styles as update_venue.php */
    </style>
</head>
<body>
    <h1>Edit Photographer Booking</h1>
    <form method="POST">
        <label for="event_date">Event Date:</label>
        <input type="date" id="event_date" name="event_date" value="<?= htmlspecialchars($booking['event_date']) ?>" required>

        <label for="photographer_id">Photographer ID:</label>
        <input type="number" id="photographer_id" name="photographer_id" value="<?= htmlspecialchars($booking['photographer_id']) ?>" required>

        <label for="user_name">User Name:</label>
        <input type="text" id="user_name" name="user_name" value="<?= htmlspecialchars($booking['User_name']) ?>" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($booking['email']) ?>" required>

        <label for="phone">Phone:</label>
        <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($booking['phone']) ?>" required>

        <button type="submit">Save Changes</button>
    </form>
</body>
</html>