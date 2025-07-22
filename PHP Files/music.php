<?php
// Include database connection (make sure to define your connection in 'connection.php')
include('connection.php');

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form data
    $musicName = $_POST['musicName'];
    $contactNumber = $_POST['contactNumber'];
    $price = $_POST['price'];

    // Validate the form data (you can add further validation here)

    // SQL query to insert the data into the database
    $sql = "INSERT INTO music (music_name, contact_number, price) VALUES (?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        // Bind parameters to the prepared statement
        $stmt->bind_param("ssd", $musicName, $contactNumber, $price);

        // Execute the query
        if ($stmt->execute()) {
           echo"<script>alert('Music Details Inserted Successfully!'); window.location.href='adminpage.php';</script>";
        } else {
            $errorMessage = "Error: " . $stmt->error;
        }

        // Close the statement
        $stmt->close();
    } else {
        $errorMessage = "Error preparing the SQL statement.";
    }
    
    // Close the database connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Music Application Form</title>
    <link rel="stylesheet" href="../CSS Files/music.css">
</head>
<body>

    <div class="container">
        <h1>Music</h1><hr>

        <!-- Music Application Form -->
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" onsubmit="return showAlert();">
            <!-- Dropdown for Music Name -->
            <label for="musicName">Music Name:</label>
            <select id="musicName" name="musicName" required>
                <option value="Pandi Melam">Pandi Melam</option>
                <option value="Thavil Melam">Thavil Melam</option>
                <option value="Parai Melam">Parai Melam</option>
                <option value="Chenda Melam">Chenda Melam</option>
                <option value="Kondai Melam">Kondai Melam</option>
            </select>

            <label for="contactNumber">Contact Number:</label>
            <input type="tel" id="contactNumber" name="contactNumber" required>

            <label for="price">Price:</label>
            <input type="number" id="price" name="price" required>

            <div class="buttons">
                <button type="submit">Add</button>
                <button type="button" class="back-button" onclick="window.history.back();">Back</button>
            </div>
        </form>
    </div>

    <script>
        // JavaScript function to display an alert upon form submission
        function showAlert() {
            <?php
                // PHP checks for success or error and sets a message in JavaScript
                if (isset($successMessage)) {
                    echo "alert('$successMessage');";
                } elseif (isset($errorMessage)) {
                    echo "alert('$errorMessage');";
                }
            ?>
            return true; // Continue form submission after alert
        }
    </script>

</body>
</html>
