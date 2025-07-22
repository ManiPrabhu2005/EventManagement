<?php
// Start the session
session_start();

// Default filter is "All"
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

// Check if the user is logged in as an admin
$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;

// Database connection
include('connection.php');

// Fetch all bookings grouped by date with optional filtering
$sql = "
    SELECT 
        event_date,
        GROUP_CONCAT(DISTINCT venue_name ORDER BY venue_name ASC SEPARATOR ', ') AS venues,
        GROUP_CONCAT(DISTINCT photographer_name ORDER BY photographer_name ASC SEPARATOR ', ') AS photographers,
        GROUP_CONCAT(DISTINCT caterer_name ORDER BY caterer_name ASC SEPARATOR ', ') AS caterers,
        GROUP_CONCAT(DISTINCT music_name ORDER BY music_name ASC SEPARATOR ', ') AS music
    FROM (
        SELECT 
            vb.event_date,
            v.venue_name,
            NULL AS photographer_name,
            NULL AS caterer_name,
            NULL AS music_name
        FROM venue_booking vb
        LEFT JOIN venues v ON vb.venue_id = v.venue_id
        UNION ALL
        SELECT 
            pb.event_date,
            NULL AS venue_name,
            p.photographer_name,
            NULL AS caterer_name,
            NULL AS music_name
        FROM photo_booking pb
        LEFT JOIN photographers p ON pb.photographer_id = p.photographer_id
        UNION ALL
        SELECT 
            cb.event_date,
            NULL AS venue_name,
            NULL AS photographer_name,
            c.caterer_name,
            NULL AS music_name
        FROM caterer_booking cb
        LEFT JOIN caterers c ON cb.caterer_id = c.caterer_id
        UNION ALL
        SELECT 
            mb.event_date,
            NULL AS venue_name,
            NULL AS photographer_name,
            NULL AS caterer_name,
            m.music_name
        FROM music_booking mb
        LEFT JOIN music m ON mb.music_id = m.music_id
    ) AS combined_bookings
";

// Add filtering logic
if ($filter === 'this_month') {
    $sql .= " WHERE MONTH(event_date) = MONTH(CURDATE()) AND YEAR(event_date) = YEAR(CURDATE())";
} elseif ($filter === 'next_month') {
    $sql .= " WHERE MONTH(event_date) = MONTH(DATE_ADD(CURDATE(), INTERVAL 1 MONTH)) AND YEAR(event_date) = YEAR(DATE_ADD(CURDATE(), INTERVAL 1 MONTH))";
}

$sql .= " GROUP BY event_date ORDER BY event_date ASC;";

$result = $conn->query($sql);
if (!$result) {
    die("Query failed: " . $conn->error);
}

$bookings = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $bookings[] = $row;
    }
} else {
    $bookings = [];
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Event Bookings</title>
    <link rel="stylesheet" href="../CSS Files/adminpage.css"> <!-- Merged CSS File -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f9;
        }
        /* Navbar Styles */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #333;
            color: white;
            padding: 10px 20px;
        }
        .navbar h1 {
            margin: 0;
            font-size: 24px;
        }
        .navbar ul {
            list-style: none;
            display: flex;
            gap: 20px;
        }
        .navbar ul li a {
            text-decoration: none;
            color: white;
            font-size: 16px;
        }
        .navbar ul li a:hover {
            color: #ff7f50;
        }
        h1 {
            text-align: center;
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #333;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .no-data {
            text-align: center;
            font-size: 18px;
            color: #666;
        }
        .action-icons {
            display: flex;
            gap: 10px;
            float: right;
        }
        .action-icons a {
            text-decoration: none;
            color: inherit;
        }
        .edit-icon {
            color: #28a745; /* Green for Edit */
        }
        .delete-icon {
            color: #dc3545; /* Red for Delete */
        }
        .edit-icon:hover {
            color: #218838;
        }
        .delete-icon:hover {
            color: #c82333;
        }
        .filter-container {
            margin-bottom: 20px;
            text-align: right;
        }
        .filter-container select {
            padding: 5px;
            font-size: 14px;
        }
        .admin-btn {
            background-color: #007bff;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 14px;
        }
        .admin-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <div class="navbar">
        <h1>Event Management System</h1>
        <ul>
            <li><a href="adminpage.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="venue.php"><i class="fas fa-building"></i> Venues</a></li>
            <li><a href="photo.php"><i class="fas fa-camera"></i> Photographers</a></li>
            <li><a href="caterer.php"><i class="fas fa-utensils"></i> Caterers</a></li>
            <li><a href="music.php"><i class="fas fa-music"></i> Music</a></li>
           
                <li><a href="admin_user_dashboard.php" class="admin-btn"><i class="fas fa-pencil-alt"></i> Edit Property</a></li>
      
        </ul>
    </div>
    <h1>Event Bookings Overview</h1>
    <!-- Filter Dropdown -->
    
    <?php if (!empty($bookings)): ?>
        <table>
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>Date</th>
                    <th>Venues</th>
                    <th>Photographers</th>
                    <th>Caterers</th>
                    <th>Music</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                // Initialize a counter for the serial number
                $s_no = 1;
                foreach ($bookings as $booking): ?>
                    <tr>
                        <td><?php echo $s_no++; ?></td>
                        <td><?php echo htmlspecialchars($booking['event_date']); ?></td>
                        <td>
                            <?php echo $booking['venues'] ? htmlspecialchars($booking['venues']) : '-'; ?>
                            <div class="action-icons">
                                <a href="#" onclick="handleDelete('venue', '<?php echo urlencode($booking['event_date']); ?>')" class="delete-icon" title="Delete Venue">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </td>
                        <td>
                            <?php echo $booking['photographers'] ? htmlspecialchars($booking['photographers']) : '-'; ?>
                            <div class="action-icons">
                                <a href="#" onclick="handleDelete('photographer', '<?php echo urlencode($booking['event_date']); ?>')" class="delete-icon" title="Delete Photographer">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </td>
                        <td>
                            <?php echo $booking['caterers'] ? htmlspecialchars($booking['caterers']) : '-'; ?>
                            <div class="action-icons">
                                <a href="#" onclick="handleDelete('caterer', '<?php echo urlencode($booking['event_date']); ?>')" class="delete-icon" title="Delete Caterer">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </td>
                        <td>
                            <?php echo $booking['music'] ? htmlspecialchars($booking['music']) : '-'; ?>
                            <div class="action-icons">
                                <a href="#" onclick="handleDelete('music', '<?php echo urlencode($booking['event_date']); ?>')" class="delete-icon" title="Delete Music">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="no-data">No bookings found.</p>
    <?php endif; ?>
    <script>
        function applyFilter() {
            const filterValue = document.getElementById('filter').value;
            window.location.href = `adminpage.php?filter=${filterValue}`;
        }
        function handleDelete(property, eventDate) {
            const confirmDelete = confirm(`Are you sure you want to delete this ${property}?`);
            if (confirmDelete) {
                fetch(`delete_${property}.php`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ event_date: eventDate })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        alert(`${property} deleted successfully.`);
                        location.reload(); // Reload the page to reflect changes
                    } else {
                        alert(`Failed to delete ${property}. Please try again.`);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while deleting the record.');
                });
            }
        }
    </script>
</body>
</html>