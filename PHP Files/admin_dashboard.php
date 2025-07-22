<?php
session_start();

// Check if the user is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");  // Redirect to login page if not an admin
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
</head>
<body>

<h2>Welcome, Admin!</h2>
<p>This is the admin dashboard.</p>

<!-- Logout -->
<a href="logout.php">Logout</a>

</body>
</html>
