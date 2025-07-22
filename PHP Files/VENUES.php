<?php
// Include the database connection
include('connection.php');

// Fetch venue data from the database
$sql = "SELECT * FROM venues";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Query preparation failed: " . $conn->error);
}

$stmt->execute();
$result = $stmt->get_result();

$venues = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $venues[] = $row;
    }
} else {
    $venues = [];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Venue Details</title>
    <link rel="stylesheet" href="../CSS Files/VENUES.css">
    <style>
        /* Basic styling for modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            max-width: 600px;
            width: 100%;
        }

        .close {
            float: right;
            cursor: pointer;
        }

        .venue-card {
            border: 1px solid #ccc;
            padding: 15px;
            margin: 10px;
            border-radius: 8px;
            cursor: pointer;
            transition: transform 0.2s;
            text-align: center;
        }

        .venue-card:hover {
            transform: scale(1.05);
        }

        .venue-image {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 10px;
        }

        .modal-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

    <div class="navbar">
        <a href="user.php" class="back-arrow">&#8592; Back</a>
        <h1>Venue's Details</h1>
    </div>

    <div class="venue-container">
        <?php if (!empty($venues)): ?>
            <?php foreach ($venues as $venue): ?>
                <div class="venue-card" onclick="showVenueDetails(<?php echo htmlspecialchars(json_encode($venue)); ?>)">
                    <!-- Venue Image -->
                    <img src="<?php echo htmlspecialchars($venue['venue_image']); ?>" alt="<?php echo htmlspecialchars($venue['venue_name']); ?>" class="venue-image">
                    <!-- Venue Details -->
                    <h2 class="venue-name"><?php echo htmlspecialchars($venue['venue_name']); ?></h2>
                    <p><strong>ID:</strong> <?php echo htmlspecialchars($venue['venue_id']); ?></p>
                    <p><strong>Price:</strong> Rs.<?php echo number_format($venue['venue_price'], 2); ?></p>
                    <p><strong>Capacity (Persons):</strong> <?php echo $venue['venue_capacity']; ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No venues found.</p>
        <?php endif; ?>
    </div>

    <!-- Modal for venue preview -->
    <div id="venueModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Venue Details</h2>
            <!-- Venue Image -->
            <img id="modalVenueImage" src="" alt="" class="modal-image">
            <!-- Venue Details -->
            <h3 id="modalVenueName"></h3>
            <p><strong>ID:</strong> <span id="modalVenueId"></span></p>
            <p><strong>Price:</strong> Rs.<span id="modalVenuePrice"></span></p>
            <p><strong>Capacity (Persons):</strong> <span id="modalVenueCapacity"></span></p>
            <div class="modal-buttons">
                <button onclick="bookVenue()">Book Now</button>
                <button onclick="closeModal()">Back</button>
            </div>
        </div>
    </div>

    <script>
        let selectedVenue = {}; // Holds the selected venue details

        function showVenueDetails(venueData) {
            if (!venueData || !venueData.venue_name) {
                console.error("Invalid venue data");
                return;
            }

            selectedVenue = venueData;
            document.getElementById("modalVenueName").innerText = venueData.venue_name;
            document.getElementById("modalVenueId").innerText = venueData.venue_id;
            document.getElementById("modalVenuePrice").innerText = venueData.venue_price;
            document.getElementById("modalVenueCapacity").innerText = venueData.venue_capacity;
            document.getElementById("modalVenueImage").src = venueData.venue_image; // Set the modal image source
            document.getElementById("venueModal").style.display = "flex";
        }

        function closeModal() {
            document.getElementById("venueModal").style.display = "none";
        }

        window.onclick = function(event) {
            const modal = document.getElementById('venueModal');
            if (event.target === modal) {
                closeModal();
            }
        };

        function bookVenue() {
            const venueId = selectedVenue.venue_id;

            if (!venueId) {
                alert("Invalid venue ID");
                return;
            }

            const url = `viewvenue.php?venue_id=${encodeURIComponent(venueId)}`;
            window.location.href = url;
        }
    </script>

</body>
</html>