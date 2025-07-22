<?php
// Initialize variables
$booking_data = [];
$music_data = [];
$event_date_taken = false; // Variable to track if event date is already booked

// Database connection details
include('connection.php');

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch the most recent booking details (if any)
$sql_booking = "SELECT User_name, email, phone, event_name FROM user_bookings ORDER BY id DESC LIMIT 1";
$result_booking = $conn->query($sql_booking);

if ($result_booking && $result_booking->num_rows > 0) {
    // Fetch the booking details and store them in the $booking_data array
    $booking_data = $result_booking->fetch_assoc();
}

// Fetch music details based on the selected music_id from the URL
$music_id = isset($_GET['music_id']) ? intval($_GET['music_id']) : null;

if ($music_id) {
    $sql_music = "SELECT music_name, contact_number, price FROM music WHERE music_id = ?";
    $stmt = $conn->prepare($sql_music);

    if (!$stmt) {
        die("Prepare failed for music details: " . $conn->error);
    }

    $stmt->bind_param("i", $music_id);
    $stmt->execute();
    $result_music = $stmt->get_result();

    if ($result_music && $result_music->num_rows > 0) {
        $music_data = $result_music->fetch_assoc();
    } else {
        echo "No music found with the provided ID.";
        exit;
    }
} else {
    echo "No music ID provided.";
    exit;
}

// Fetch all the booked event dates for the selected music
$sql_booked_dates = "SELECT event_date FROM music_booking WHERE music_id = ?";
$stmt_dates = $conn->prepare($sql_booked_dates);

if (!$stmt_dates) {
    die("Prepare failed for booked dates: " . $conn->error);
}

$stmt_dates->bind_param("i", $music_id);
$stmt_dates->execute();
$result_dates = $stmt_dates->get_result();

$booked_dates = [];
while ($row = $result_dates->fetch_assoc()) {
    $booked_dates[] = $row['event_date'];
}

// Convert booked dates to a JavaScript-friendly format (e.g., YYYY-MM-DD)
$booked_dates_js = json_encode($booked_dates);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Debug POST data
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";

    // Validate required fields
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $contact_number = trim($_POST['contact_number']);
    $event_name = trim($_POST['event_name']);
    $event_date = trim($_POST['event_date']);
    $music_name = trim($_POST['music_name']);
    $music_phone = trim($_POST['music_phone']);

    if (empty($name) || empty($email) || empty($contact_number) || empty($event_name) || empty($event_date) || empty($music_name) || empty($music_phone)) {
        die("All fields are required.");
    }

    // Check if the event date is already taken for the selected music
    $sql_check_date = "SELECT id FROM music_booking WHERE music_id = ? AND event_date = ?";
    $stmt_check = $conn->prepare($sql_check_date);

    if (!$stmt_check) {
        die("Prepare failed for checking event date: " . $conn->error);
    }

    $stmt_check->bind_param("is", $music_id, $event_date);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        $event_date_taken = true; // Mark that the event date is already booked
        echo "<script>alert('The selected date is already booked. Please choose another date.');</script>";
    } else {
        // Proceed to insert the booking if the date is available
        $sql_insert = "INSERT INTO music_booking (name, email, contact_number, event_name, event_date, music_name, music_phone, music_id)
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt_insert = $conn->prepare($sql_insert);

        if (!$stmt_insert) {
            die("Prepare failed for inserting booking: " . $conn->error);
        }

        $stmt_insert->bind_param("sssssssi", $name, $email, $contact_number, $event_name, $event_date, $music_name, $music_phone, $music_id);

        // Execute the insert query and check for errors
        if ($stmt_insert->execute()) {
            // Redirect to user.php after successful insertion
            echo "<script>
                    if (confirm('Booking successful! Do you want to navigate to the user dashboard?')) {
                        window.location.href = 'user.php';
                    }
                  </script>";
            exit;
        } else {
            echo "Error: " . $stmt_insert->error; // Log the error message
        }

        // Close the insert statement
        $stmt_insert->close();
    }

    $stmt_check->close();
}

$stmt_dates->close();
$conn->close(); // Close the database connection
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Music Booking Form</title>
    <link rel="stylesheet" href="../CSS Files/viewmusic.css"> <!-- Linking to external CSS -->
    <!-- jQuery UI for the datepicker -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <style>
        /* Custom CSS to highlight booked dates */
        .ui-datepicker .ui-state-booked {
            background-color: red !important; /* Bright red color */
            color: white !important;
            font-weight: bold;
        }
        .ui-datepicker .ui-state-booked:hover {
            background-color: darkred !important; /* Darker red on hover */
        }
    </style>
    <script type="text/javascript">
        $(document).ready(function() {
            // Array of booked dates (passed from PHP to JavaScript)
            var bookedDates = <?= $booked_dates_js ?>;
            // Function to check if a date is booked
            function isBooked(date) {
                var dateStr = $.datepicker.formatDate('yy-mm-dd', date);
                return bookedDates.indexOf(dateStr) !== -1;
            }
            // Initialize the datepicker
            $("#event_date").datepicker({
                beforeShowDay: function(date) {
                    // Check if the current date is in the booked dates array
                    if (isBooked(date)) {
                        return [false, "ui-state-booked", "Booked"];
                    }
                    return [true, "", ""];
                },
                dateFormat: 'yy-mm-dd', // Format date to match database format
            });
        });

        function confirmBooking() {
            var userConfirmed = confirm("Are you sure you want to submit the booking?");
            if (userConfirmed) {
                document.getElementById("bookingForm").submit();
            }
        }
    </script>
</head>
<body>
    <div class="form-container">
        <h2>Music Booking Form</h2>
        <form id="bookingForm" method="post" enctype="multipart/form-data">
            <fieldset>
                <legend>Personal Details</legend>
                <label for="name">Full Name:</label>
                <input type="text" id="name" name="name" value="<?= isset($booking_data['User_name']) ? htmlspecialchars($booking_data['User_name']) : '' ?>" required>
                <label for="email">Email Address:</label>
                <input type="email" id="email" name="email" value="<?= isset($booking_data['email']) ? htmlspecialchars($booking_data['email']) : '' ?>" required>
                <label for="contact_number">Contact Number:</label>
                <input type="tel" id="contact_number" name="contact_number" value="<?= isset($booking_data['phone']) ? htmlspecialchars($booking_data['phone']) : '' ?>" required>
            </fieldset>
            <fieldset>
                <legend>Event Details</legend>
                <label for="event_name">Event Name:</label>
                <input type="text" id="event_name" name="event_name" value="<?= isset($booking_data['event_name']) ? htmlspecialchars($booking_data['event_name']) : '' ?>" required>
                <label for="event_date">Event Date:</label>
                <input type="text" id="event_date" name="event_date" required>
            </fieldset>
            <fieldset>
                <legend>Music Information</legend>
                <label for="music_name">Music Name:</label>
                <input type="text" id="music_name" name="music_name" value="<?= isset($music_data['music_name']) ? htmlspecialchars($music_data['music_name']) : '' ?>" readonly>
                <label for="music_phone">Music Phone Number:</label>
                <input type="text" id="music_phone" name="music_phone" value="<?= isset($music_data['contact_number']) ? htmlspecialchars($music_data['contact_number']) : '' ?>" readonly>
                <label for="price">Price:</label>
                <input type="text" id="price" name="price" value="<?= isset($music_data['price']) ? htmlspecialchars($music_data['price']) : '' ?>" readonly>
                <label for="music_id">Music ID:</label>
                <input type="text" id="music_id" name="music_id" value="<?= htmlspecialchars($music_id) ?>" readonly>
            </fieldset>
            <div class="button-container">
                <button type="button" class="back-btn" onclick="window.history.back()">Back</button>
                <button type="button" class="submit-btn" onclick="confirmBooking()">Submit</button>
            </div>
        </form>
    </div>
</body>
</html>