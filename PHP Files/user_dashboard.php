<?php
session_start();

// Include the database connection
include('connection.php');

// Handle the form submission and save preferences in session
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data and sanitize it
    $User_name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $phone = htmlspecialchars(trim($_POST['phone']));
    $event_name = htmlspecialchars(trim($_POST['event']));

    // Collect preferences (if any)
    if (isset($_POST['preferences'])) {
        $preferences = $_POST['preferences'];  // Store preferences as an array in session
        $_SESSION['preferences'] = $preferences;  // Save preferences in session
    } else {
        $preferences = []; // No preferences selected
        $_SESSION['preferences'] = [];  // Ensure session is cleared if no preferences are selected
    }

    // Prepare the SQL query to insert data into the database
    $sql = "INSERT INTO user_bookings (User_name, email, phone, event_name, preferences) VALUES (?, ?, ?, ?, ?)";

    // Prepare and bind the statement
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("sssss", $User_name, $email, $phone, $event_name, implode(", ", $preferences));  // Store preferences as a string

        // Execute the query
        if ($stmt->execute()) {
            // Redirect to user dashboard page after successful form submission
            echo "<script>
                    window.location.href = 'user.php'; // Redirect to user_dashboard.php
                  </script>";
        } else {
            echo "<p>Error: " . $stmt->error . "</p>"; // Display SQL error message
        }

        // Close the statement
        $stmt->close();
    } else {
        echo "<p>Failed to prepare the SQL statement.</p>";
    }

    // Close the connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="../CSS Files/user.css">
    <style>
        /* General Styles */
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to right, #f4faff, #eef2ff);
            color: #333;
            line-height: 1.6;
        }

        /* Navbar Styles */
        nav.navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #4a90e2;
            color: white;
            padding: 15px 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .navbar-logo a {
            font-size: 24px;
            font-weight: bold;
            text-decoration: none;
            color: white;
        }
        .navbar-links {
            list-style: none;
            display: flex;
            gap: 20px;
        }
        .navbar-links li a {
            text-decoration: none;
            color: white;
            font-size: 16px;
            transition: color 0.3s ease;
        }
        .navbar-links li a:hover {
            color: #ff7f50;
        }
        .logout-btn {
            background: #ff6b6b;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            transition: background 0.3s ease;
        }
        .logout-btn:hover {
            background: #ff4d4d;
        }

        /* Container Styles */
        .container {
            display: flex;
            justify-content: space-between;
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Form Container */
        .form-container, .preferences-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 48%;
        }
        .form-container h2, .preferences-container h2 {
            margin-top: 0;
            font-size: 22px;
            color: #4a90e2;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        .submit-container {
            text-align: right;
        }
        .submit-btn {
            background: #4a90e2;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        .submit-btn:hover {
            background: #3a7bd5;
        }

        /* Preferences Container */
        .preferences-container label {
            display: inline-block;
            margin-right: 15px;
            margin-bottom: 10px;
            font-size: 16px;
            color: #555;
        }
        .preferences-container input[type="checkbox"] {
            margin-right: 5px;
        }
        #selectAll {
            font-weight: bold;
            color: #4a90e2;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }
            .form-container, .preferences-container {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="navbar-logo">
            <a href="#">Event Management System</a>
        </div>
        <ul class="navbar-links">
            <li><a href="home.php" class="logout-btn">Logout</a></li>
        </ul>
    </nav>

    <!-- Main Content -->
    <div class="container">
        <!-- Left Container: Form -->
        <div class="form-container">
            <h2>Enter Your Details</h2>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" onsubmit="return validateForm(event)">
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" 
                    placeholder="Enter your name"  required>
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email"
                    placeholder="Enter your email"  required>
                </div>

                <div class="form-group">
    <label for="phone">Phone No:</label>
    <input 
        type="tel" 
        id="phone" 
        name="phone" 
        pattern="[0-9]{10}" 
        placeholder="Enter 10-digit mobile number" 
        required
    >
  
</div>

                <div class="form-group">
                    <label for="event">Event Name:</label>
                    <input type="text" id="event" name="event" 
                    placeholder="Enter event name" required>
                </div>

              
           
        </div>

        <!-- Right Container: Preferences -->
        <div class="preferences-container">
            <h2>Select Your Preferences for Booking</h2>
            <label for="selectAll">
                <input type="checkbox" id="selectAll" onclick="toggleSelectAll(this)">
                Select All
            </label>
            <br><br>
            <label for="venues">
                <input type="checkbox" id="venues" name="preferences[]" value="VENUES">
                Venues
            </label>
            <label for="photographer">
                <input type="checkbox" id="photographer" name="preferences[]" value="PHOTOGRAPHERS">
                Photographer
            </label>
            <label for="caterer">
                <input type="checkbox" id="caterer" name="preferences[]" value="CATERERS">
                Caterer
            </label>
            <label for="music">
                <input type="checkbox" id="music" name="preferences[]" value="MUSICS">
                Music
            </label>
           
                    <button type="submit" class="submit-btn">Proceed with Booking</button>
         
            </form>
        </div>
    </div>

    <script>
        // Function to toggle checkboxes when "Select All" is clicked
        function toggleSelectAll(source) {
            var checkboxes = document.querySelectorAll('.preferences-container input[type="checkbox"]');
            checkboxes.forEach(function (checkbox) {
                checkbox.checked = source.checked;
            });
        }

        // Function to validate form and preferences
        function validateForm(event) {
            // Validate Form Fields
            var name = document.getElementById('name').value;
            var email = document.getElementById('email').value;
            var phone = document.getElementById('phone').value;
            var event_name = document.getElementById('event').value;

            if (!name || !email || !phone || !event_name) {
                alert('Please fill in all the required fields.');
                event.preventDefault(); // Prevent form submission
                return false;
            }

            // Validate Preferences (Checkboxes)
            var checkboxes = document.querySelectorAll('.preferences-container input[type="checkbox"]:checked');
            if (checkboxes.length === 0) {
                alert('Please select at least one preference.');
                event.preventDefault(); // Prevent form submission
                return false;
            }

            return true; // Allow form submission if all fields are valid
        }
    </script>
</body>
</html>