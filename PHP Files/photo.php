<?php
// Include the database connection
include('connection.php'); // Ensure this file connects to your database

// Initialize the photographer_id
$photographerId = null;

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data (no need to get photographerId as it's auto-generated)
    $photographerName = $_POST['photographerName'];
    $contactNumber = $_POST['contactNumber'];
    $price = $_POST['price'];

    // SQL query to insert the data into the photographers table (no need to specify photographer_id)
    $sql = "INSERT INTO photographers (photographer_name, contact_number, price) 
            VALUES (?, ?, ?)";

    // Prepare the statement
    if ($stmt = $conn->prepare($sql)) {
        // Bind parameters (s = string, d = decimal)
        $stmt->bind_param("ssd", $photographerName, $contactNumber, $price);

        // Execute the query
        if ($stmt->execute()) {
            // Get the auto-generated ID of the inserted photographer
            $photographerId = $stmt->insert_id;

            // Display success message and redirect
            echo "<script>alert('Photographer Details Inserted Successfully!'); window.location.href='adminpage.php';</script>";
        } else {
            echo "<p>Error: " . $stmt->error . "</p>";
        }

        // Close the statement
        $stmt->close();
    } else {
        echo "<p>Error: Unable to prepare the SQL statement.</p>";
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
    <title>Photographer Application Form</title>
    <link rel="stylesheet" href="../CSS Files/photo.css">
</head>
<body>

    <div class="container">
        <h1>Photographer</h1><hr>

        <!-- Photographer Application Form -->
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
            
           

            <label for="photographerName">Photographer Name:</label>
            <input type="text" id="photographerName" name="photographerName" required>

            <label for="contactNumber">Contact Number:</label>
            <input type="tel" id="contactNumber" name="contactNumber" required>

            <label for="price">Price:</label>
            <input type="number" id="price" name="price" required>

            <div class="buttons">
                <button type="submit">Add</button>
                <button type="button" class="back-button" onclick="window.history.back();">Back</button>
            </div>
        </form>

        <!-- Display the Photographer ID after submission (if it exists) -->
        <?php if ($photographerId): ?>
            <p><strong>New Photographer ID: </strong><?php echo $photographerId; ?></p>
        <?php endif; ?>
    </div>

</body>
</html>
