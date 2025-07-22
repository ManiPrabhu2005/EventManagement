<?php
// Database connection
include('connection.php');

// Retrieve the event date from the query string
$event_date = isset($_GET['date']) ? $_GET['date'] : null;

if ($event_date) {
    // Fetch the existing booking details for the given event date
    $sql_fetch = "SELECT * FROM music_booking WHERE event_date = ?";
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
        $music_id = trim($_POST['music_id']);
        $user_name = trim($_POST['user_name']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        $event_name = trim($_POST['event_name']);
        $people_capacity = (int) $_POST['people_capacity'];
        $food_preference = trim($_POST['food_preference']);
        $description = trim($_POST['description']);
        $music_name = trim($_POST['music_name']);
        $music_phone = trim($_POST['music_phone']);

        // Update the booking in the database
        $sql_update = "
            UPDATE music_booking 
            SET event_date = ?, music_id = ?, name = ?, email = ?, contact_number = ?, 
                event_name = ?, people_capacity = ?, food_preference = ?, description = ?, 
                music_name = ?, music_phone = ? 
            WHERE event_date = ?
        ";
        $stmt_update = $conn->prepare($sql_update);

        if ($stmt_update) {
            $stmt_update->bind_param(
                "sissssisssss", 
                $new_event_date, $music_id, $user_name, $email, $phone, 
                $event_name, $people_capacity, $food_preference, $description, 
                $music_name, $music_phone, $event_date
            );

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
    <title>Edit Music Booking</title>
    <style>
        /* Basic styles for the form */
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        form {
            max-width: 500px;
            margin: 0 auto;
        }
        label {
            display: block;
            margin-top: 10px;
        }
        input, select, textarea {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
        }
        button {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <h1>Edit Music Booking</h1>
    <form method="POST">
        <label for="event_date">Event Date:</label>
        <input type="date" id="event_date" name="event_date" value="<?= htmlspecialchars($booking['event_date']) ?>" required>

        <label for="music_id">Music ID:</label>
        <input type="number" id="music_id" name="music_id" value="<?= htmlspecialchars($booking['music_id']) ?>" required>

        <label for="user_name">Full Name:</label>
        <input type="text" id="user_name" name="user_name" value="<?= htmlspecialchars($booking['name']) ?>" required>

        <label for="email">Email Address:</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($booking['email']) ?>" required>

        <label for="phone">Contact Number:</label>
        <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($booking['contact_number']) ?>" required>

        <label for="event_name">Event Name:</label>
        <input type="text" id="event_name" name="event_name" value="<?= htmlspecialchars($booking['event_name']) ?>" required>

        <label for="people_capacity">People Capacity:</label>
        <input type="number" id="people_capacity" name="people_capacity" value="<?= htmlspecialchars($booking['people_capacity']) ?>" required>

        <label for="food_preference">Food Preference:</label>
        <select id="food_preference" name="food_preference" required>
            <option value="Veg" <?= $booking['food_preference'] === 'Veg' ? 'selected' : '' ?>>Veg</option>
            <option value="Non-Veg" <?= $booking['food_preference'] === 'Non-Veg' ? 'selected' : '' ?>>Non-Veg</option>
        </select>

        <label for="description">Event Description:</label>
        <textarea id="description" name="description" rows="4"><?= htmlspecialchars($booking['description']) ?></textarea>

        <label for="music_name">Music Name:</label>
        <input type="text" id="music_name" name="music_name" value="<?= htmlspecialchars($booking['music_name']) ?>" readonly>

        <label for="music_phone">Music Phone Number:</label>
        <input type="text" id="music_phone" name="music_phone" value="<?= htmlspecialchars($booking['music_phone']) ?>" readonly>

        <button type="submit">Save Changes</button>
    </form>
</body>
</html>