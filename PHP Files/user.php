<?php
session_start();

// Get selected preferences from session
$selectedOptions = isset($_SESSION['preferences']) ? $_SESSION['preferences'] : [];

// Check if the user is an admin (using a session variable)
$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="../CSS Files/useruser.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <!-- Navigation Bar -->
    <nav>
        <ul>
            <li class="nav-right"><a href="design.php" class="home-btn"><i class="fas fa-arrow-left"></i> Home</a></li>
            <li class="nav-left"><a href="home.php" class="prev-btn"><i class="fas fa-home"></i> Exit</a></li>

            <!-- Add an "Admin Panel" button if the user is an admin -->
            
        </ul>

        <!-- Check if there are any selected preferences -->
        <?php if (!empty($selectedOptions)): ?>
            <ul>
                <?php
                // Loop through the selected preferences and display them as navigation items
                foreach ($selectedOptions as $option) {
                    // Sanitize the option for security reasons (e.g., to prevent XSS)
                    $option = htmlspecialchars($option);
                    echo "<li><a href='{$option}.php'>{$option}</a></li>";
                }
                ?>
            </ul>
        <?php else: ?>
            <!-- If no preferences are selected, display a message -->
            <ul>
                <li>No preferences selected.</li>
            </ul>
        <?php endif; ?>
    </nav>

    <!-- Content Section -->
    <div class="content">
        <h1>Welcome to Your Selection Page</h1>
    </div>
</body>
</html>