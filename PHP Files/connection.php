<?php
// Database credentials
$servername = "localhost"; // Database server, usually localhost
$username_db = "root"; // Your database username (change if needed)
$password_db = ""; // Your database password (change if needed)
$dbname = "event"; // The name of your database

// Create a connection to the MySQL database
$conn = new mysqli($servername, $username_db, $password_db, $dbname);

// Check if the connection is successful
if ($conn->connect_error) {
    // If there is a connection error, stop the script and show the error message
    die("Connection failed: " . $conn->connect_error);
}

// Connection successful message (optional, for debugging)
// echo "Connected successfully to the database!";
?>
