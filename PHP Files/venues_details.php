<?php
// venus_details.php

include('connection.php');

// Fetch the details of venues
$venus_query = "SELECT * FROM venues";
$venus_result = $conn->query($venus_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Venues Details</title>
    <style>
        /* General Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        /* Sidebar Styling */
        .sidebar {
            width: 220px;
            height: 100%;
            background-color: #333;
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            padding-top: 20px;
        }

        .venue-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .sidebar .logo {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            padding-bottom: 30px;
        }

        .sidebar .menu {
            list-style-type: none;
            padding: 0;
        }

        .sidebar .menu li {
            padding: 15px;
            text-align: center;
        }

        .sidebar .menu li a {
            color: white;
            text-decoration: none;
            display: block;
            font-size: 18px;
        }

        .sidebar .menu li a:hover {
            background-color: #555;
        }

        /* Main Content Styling */
        .content {
            margin-left: 240px;
            padding: 20px;
            min-height: 100vh;
            background-color: #f4f4f4;
        }

        header {
            background-color: #007BFF;
            color: white;
            padding: 10px 20px;
            text-align: right;
            font-size: 18px;
            font-weight: bold;
        }

        header a {
            color: white;
            text-decoration: none;
            font-size: 18px;
        }

        header a:hover {
            color: #ddd;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        /* Back Button */
       

        /* Venue Card Styling */
        .main-content {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }

        .venue-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.3s ease;
            text-align: center;
        }

        .venue-card:hover {
            transform: translateY(-10px);
        }

        .venue-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-bottom: 1px solid #ddd;
        }

        .venue-card h3 {
            font-size: 22px;
            color: #2c3e50;
            margin: 15px 0;
        }

        .venue-card p {
            color: #7f8c8d;
            font-size: 16px;
        }

        .venue-card .price {
            color: #27ae60;
            font-size: 18px;
            font-weight: bold;
        }

        /* Responsive Design */
        @media screen and (max-width: 768px) {
            .sidebar {
                width: 180px;
            }

            .content {
                margin-left: 200px;
            }

            .venue-card {
                width: 100%;
                max-width: 300px;
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>

    <!-- Main Container -->
    <div class="container">

        <!-- Sidebar (Always visible) -->
        <div class="sidebar">
            <div class="logo">
                <h2>Venues Details</h2>
            </div>
            <ul class="menu">
                <li><a href="adminpage.php">Admin</a></li>
                <li><a href="venue.php">Venues</a></li>
                <!-- Other links here -->
            </ul>
        </div>

        <!-- Main Content -->
        <div class="content">
            <nav>
                <h2 align="right">
                    <li><a href="home.php"><i class="fas fa-home"></i> Home</a></li>
                </h2>
            </nav>

            <div class="header">
                <h1>Venues Details</h1>
            </div>

           

            <div class="venue-cards">
                <?php while ($venue = $venus_result->fetch_assoc()) { ?>
                    <div class="venue-card">
                       
                        <h3><?php echo $venue['venue_name']; ?></h3>
                        <p><strong>ID:</strong> <span class="id"><?php echo $venue['venue_id']; ?></span></p>
                        <p><strong>Price:</strong> <span class="price"><?php echo $venue['venue_price']; ?></span></p>
                        <p><strong>Capacity:</strong> <?php echo $venue['venue_capacity']; ?></p>
                        
                        <p><strong>Description:</strong> <?php echo $venue['venue_description']; ?></p>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>

</body>
</html>
