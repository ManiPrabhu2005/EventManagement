<?php
// Assuming your database connection is already set up and saved as $conn
include('connection.php');

// Fetch the count of records for each category
$queries = [
    'venues' => "SELECT COUNT(*) AS count FROM venues",
    'music' => "SELECT COUNT(*) AS count FROM music",
    'photographers' => "SELECT COUNT(*) AS count FROM photographers",
    'caterers' => "SELECT COUNT(*) AS count FROM caterers"
];

// Initialize counts array
$counts = [];

foreach ($queries as $category => $query) {
    $result = $conn->query($query);
    if ($result) {
        $counts[$category] = $result->fetch_assoc()['count'];
    } else {
        $counts[$category] = 0; // Default to 0 if query fails
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../CSS Files/adminpage.css"> <!-- Merged CSS File -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
        }

        .container {
            display: flex;
            height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            background-color: #333;
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        .sidebar .logo {
            text-align: center;
            margin-bottom: 20px;
        }

        .sidebar ul.menu {
            list-style: none;
            padding: 0;
        }

        .sidebar ul.menu li {
            margin: 15px 0;
        }

        .sidebar ul.menu li a {
            color: #fff;
            text-decoration: none;
            font-size: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar ul.menu li a:hover {
            color: #ff7f50;
        }

        .sidebar .menu li a i {
            font-size: 20px;
        }

        /* Main Content */
        .content {
            flex-grow: 1;
            padding: 20px;
            overflow-y: auto;
        }

        nav {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 20px;
        }

        nav a {
            text-decoration: none;
            color: #333;
            font-size: 18px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        nav a:hover {
            color: #ff7f50;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .main-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .card {
            background-color: black;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: left; /* Align text to the left */
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
            display: flex; /* Use flexbox for layout */
            align-items: center; /* Vertically center items */
            gap: 15px; /* Add spacing between icon, text, and count */
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .card i {
            font-size: 2rem; /* Larger icon size */
        }

        .card .info {
            flex-grow: 1; /* Allow text to take up remaining space */
        }

        .card h2 {
            font-size: 18px;
            margin: 0;
        }

        .card p {
            font-size: 24px;
            font-weight: bold;
            margin: 5px 0 0;
        }

        /* Category Icons */
        .venues i { color: #4CAF50; }
        .music i { color: #FFC107; }
        .photo i { color: #03A9F4; }
        .cater i { color: #E91E63; }
    </style>
</head>
<body>

    <!-- Main Container -->
    <div class="container">

        <!-- Sidebar -->
        <div class="sidebar">
            <div class="logo">
                <h2>Admin Panel</h2>
            </div>
            <ul class="menu">
                <li><a href="adminpage.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="#"><i class="fas fa-plus-circle"></i> Add List</a></li>
                <li><a href="venue.php"><i class="fas fa-building"></i> Venues</a></li>
                <li><a href="photo.php"><i class="fas fa-camera"></i> Photographers</a></li>
                <li><a href="caterer.php"><i class="fas fa-utensils"></i> Caterers</a></li>
                <li><a href="music.php"><i class="fas fa-music"></i> Music</a></li>
                <li><a href="#"><i class="fas fa-list"></i> View List</a></li>
                <li><a href="events.php"><i class="fas fa-calendar-alt"></i> Events</a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="content">

            <!-- Navigation Bar -->
            <nav>
            <a href="home.php" style="color: white; text-decoration: none;">
    <i class="fas fa-home"></i> Home
</a>            </nav>

            <!-- Header Section -->
            <div class="header">
                <h1>Welcome to Your Admin Dashboard</h1>
            </div>

            <!-- Dashboard Content (Cards for Counts) -->
            <div class="main-content">
                <!-- Card for Venues -->
                <div class="card venues" onclick="window.location.href='venues_details.php'">
                    <i class="fas fa-building"></i>
                    <div class="info">
                        <h2>Venues</h2>
                        <p><?php echo htmlspecialchars($counts['venues']); ?></p>
                    </div>
                </div>

                <!-- Card for Music -->
                <div class="card music" onclick="window.location.href='music_details.php'">
                    <i class="fas fa-music"></i>
                    <div class="info">
                        <h2>Music</h2>
                        <p><?php echo htmlspecialchars($counts['music']); ?></p>
                    </div>
                </div>

                <!-- Card for Photography -->
                <div class="card photo" onclick="window.location.href='photographers_details.php'">
                    <i class="fas fa-camera"></i>
                    <div class="info">
                        <h2>Photographers</h2>
                        <p><?php echo htmlspecialchars($counts['photographers']); ?></p>
                    </div>
                </div>

                <!-- Card for Caterers -->
                <div class="card cater" onclick="window.location.href='caterer_details.php'">
                    <i class="fas fa-utensils"></i>
                    <div class="info">
                        <h2>Caterers</h2>
                        <p><?php echo htmlspecialchars($counts['caterers']); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>