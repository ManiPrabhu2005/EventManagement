<?php
// Include the database connection
include('connection.php'); // Make sure this file connects to your database

// Initialize the caterer_id
$catererId = null;

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data (no need to get catererId as it's auto-generated)
    $catererName = $_POST['catererName'];
    $contactNumber = $_POST['contactNumber'];
    $price = $_POST['price'];

    // SQL query to insert the data into the caterers table (no need to specify caterer_id)
    $sql = "INSERT INTO caterers (caterer_name, contact_number, price) 
            VALUES (?, ?, ?)";

    // Prepare the statement
    if ($stmt = $conn->prepare($sql)) {
        // Bind parameters (i = integer, s = string, d = decimal)
        $stmt->bind_param("ssd", $catererName, $contactNumber, $price);

        // Execute the query
        if ($stmt->execute()) {
            // Get the auto-generated ID of the inserted caterer
            $catererId = $stmt->insert_id;

            // Display success message
            echo "<script>alert('Caterer Application Submitted Successfully!'); window.location.href='adminpage.php';</script>";
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
    <title>Caterer</title>
    <link rel="stylesheet" href="../CSS Files/caterer.css">
</head>
<body>

    <div class="container">
        <h1>Caterer</h1><hr>

        <!-- Caterer Application Form -->
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
           

            <label for="catererName">Caterer Name:</label>
            <input type="text" id="catererName" name="catererName" required>

            <label for="contactNumber">Contact Number:</label>
            <input type="tel" id="contactNumber" name="contactNumber" required>

            <label for="price">Price:</label>
            <input type="number" id="price" name="price" step="0.01" required>

            <div class="buttons">
                <button type="submit">Add</button>
                <button type="button" class="back-button" onclick="window.history.back();">Back</button>
            </div>
        </form>

        <!-- Display the Caterer ID after submission (if it exists) -->
        <?php if ($catererId): ?>
            <p><strong>New Caterer ID: </strong><?php echo $catererId; ?></p>
        <?php endif; ?>
    </div>

</body>
</html>
