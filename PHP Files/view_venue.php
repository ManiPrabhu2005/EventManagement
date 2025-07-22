<?php
// Initialize variables
$booking_data = [];
$venue_data = [];
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

// Fetch venue details based on the selected venue_id from the URL
$venue_id = isset($_GET['venue_id']) ? $_GET['venue_id'] : null;

if ($venue_id) {
    $sql_venue = "SELECT venue_name, venue_price, venue_capacity, venue_facilities, venue_description FROM venues WHERE venue_id = ?";
    $stmt = $conn->prepare($sql_venue);
    $stmt->bind_param("i", $venue_id);
    $stmt->execute();
    $result_venue = $stmt->get_result();

    if ($result_venue && $result_venue->num_rows > 0) {
        $venue_data = $result_venue->fetch_assoc();
    } else {
        echo "No venue found with the provided ID.";
        exit;
    }
} else {
    echo "No venue ID provided.";
    exit;
}



// Fetch all the booked event dates for the selected venue
$sql_booked_dates = "SELECT event_date FROM venue_booking WHERE venue_id = ?";
$stmt_dates = $conn->prepare($sql_booked_dates);
$stmt_dates->bind_param("i", $venue_id);
$stmt_dates->execute();
$result_dates = $stmt_dates->get_result();

$booked_dates = [];
while ($row = $result_dates->fetch_assoc()) {
    $booked_dates[] = $row['event_date'];
}

// Convert booked dates to a JavaScript-friendly format (e.g., YYYY-MM-DD)
$booked_dates_js = json_encode($booked_dates);

// Check if the event date already exists in the database before inserting
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_date = $_POST['event_date'];

    // Check if the event date is already taken for the selected venue
    $sql_check_date = "SELECT id FROM venue_booking WHERE venue_id = ? AND event_date = ?";
    $stmt_check = $conn->prepare($sql_check_date);
    $stmt_check->bind_param("is", $venue_id, $event_date);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        $event_date_taken = true; // Mark that the event date is already booked
    } else {
        // Proceed to insert the booking if the date is available
        $name = $_POST['name'];
        $email = $_POST['email'];
        $contact_number = $_POST['contact_number'];
        $event_name = $_POST['event_name'];
        $estimated_price = $_POST['estimated_price'];
        $capacity = $_POST['capacity'];
        $num_guests = $_POST['num_guests'];
        $hall_name = $_POST['hall_name'];
        $facilities = $_POST['facilities'];
        $description = $_POST['description'];

        // Prepare the insert query
        $sql_insert = "INSERT INTO venue_booking (name, email, contact_number, event_name, event_date, estimated_price, num_guests, hall_name, venue_id)
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        // Bind the parameters with appropriate data types
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("sssssissi", $name, $email, $contact_number, $event_name, $event_date, $estimated_price, $num_guests, $hall_name, $venue_id);

        /*<script type="text/javascript">
        function confirmBooking() {
            var userConfirmed = confirm("Are you sure you want to submit the booking?");
            if (userConfirmed) {
                document.getElementById("bookingForm").submit();
                
            }
        }
    </script>*/
        // Execute the insert query and check for errors
        if ($stmt_insert->execute()) {
            echo "<script>
                    if (confirm('Booking successful! Would you like to proceed to your bookings page?')) {
                        window.location.href = 'admin_user.php'; // Redirect to user.php after success
                    } 
                  </script>";
        } else {
            echo "Error: " . $stmt_insert->error;
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
    <title>Function Hall Booking Form</title>
    <link rel="stylesheet" href="../CSS Files/viewvenue.css"> <!-- Linking to external CSS -->
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
            background-color: red !important; /* Darker red on hover */
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
    </script>
</head>
<body>
    <div class="form-container">
        <h2>Function Hall Booking Form</h2>

        <form id="bookingForm" action="viewvenue.php?venue_id=<?= $venue_id ?>" method="post" enctype="multipart/form-data">
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
                <input type="text" id="event_date" name="event_date" value="<?= isset($booking_data['event_date']) ? htmlspecialchars($booking_data['event_date']) : '' ?>" required>

                <label for="estimated_price">Estimated Price (in Rs.):</label>
                <input type="number" id="estimated_price" name="estimated_price" value="<?= isset($venue_data['venue_price']) ? htmlspecialchars($venue_data['venue_price']) : '' ?>" required readonly>

                <label for="capacity">Hall Capacity:</label>
                <input type="number" id="capacity" name="capacity" value="<?= isset($venue_data['venue_capacity']) ? htmlspecialchars($venue_data['venue_capacity']) : '' ?>" required readonly>

                <label for="num_guests">Number of Guests:</label>
                <input type="number" id="num_guests" name="num_guests" required>
            </fieldset>

            <fieldset>
                <legend>Hall Information</legend>
                <label for="hall_name">Hall Name/Type:</label>
                <input type="text" id="hall_name" name="hall_name" value="<?= isset($venue_data['venue_name']) ? htmlspecialchars($venue_data['venue_name']) : '' ?>" readonly>

                <label for="facilities">Hall Facilities:</label>
                <textarea id="facilities" name="facilities" rows="4" readonly><?= isset($venue_data['venue_facilities']) ? htmlspecialchars($venue_data['venue_facilities']) : '' ?></textarea>

                <label for="description">Hall Description:</label>
                <textarea id="description" name="description" rows="4" readonly><?= isset($venue_data['venue_description']) ? htmlspecialchars($venue_data['venue_description']) : '' ?></textarea>
            </fieldset>

            <div class="button-container">
                <button type="button" class="back-btn" onclick="window.history.back()">Back</button>
                <button type="submit" class="submit-btn" onclick="confirmBooking()">Submit</button>
            </div>

            
        </form>
    </div>

   
</body>
</html>
