<?php
// Include the database connection
include('connection.php');

// Fetch photographer data from the database
$sql = "SELECT * FROM photographers";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Query preparation failed: " . $conn->error);
}

$stmt->execute();
$result = $stmt->get_result();

$photographers = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $photographers[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Photographer Details</title>
    <link rel="stylesheet" href="../CSS Files/PHOTOGRAPHERS.css">
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
            max-width: 400px;
            width: 100%;
        }

        .close {
            float: right;
            cursor: pointer;
        }
        .navbar {
    background-color: #333;/* Light gray background */
    padding: 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: white;
    border-bottom: 2px solid #ddd;
    
}

.navbar h1 {
    font-size: 24px;
}

.back-arrow {
    font-size: 20px;
    text-decoration: none;
    color: white;
    cursor: pointer;
}

        .photographer-card {
            border: 1px solid #ccc;
            padding: 15px;
            margin: 10px;
            border-radius: 8px;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .photographer-card:hover {
            transform: scale(1.05);
        }
    </style>
</head>
<body>

<div class="navbar">
        <a href="user.php" class="back-arrow">&#8592; Back</a>
        <h1>PhotographerDetails</h1>
    </div>


    <div class="photographer-container">
        <?php if (!empty($photographers)): ?>
            <?php foreach ($photographers as $photographer): ?>
                <div class="photographer-card" onclick="showPhotographerDetails(<?php echo htmlspecialchars(json_encode($photographer)); ?>)">
                    <h2 class="photographer-name"><?php echo htmlspecialchars($photographer['photographer_name']); ?></h2>
                    <p><strong>ID:</strong> <?php echo htmlspecialchars($photographer['photographer_id']); ?></p>
                    <p><strong>Price:</strong> Rs.<?php echo number_format($photographer['price'], 2); ?></p>
                    <p><strong>Mobile:</strong> <?php echo htmlspecialchars($photographer['contact_number']); ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No photographers found.</p>
        <?php endif; ?>
    </div>

    <!-- Modal for photographer preview -->
    <div id="photographerModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Photographer Details</h2>
            <h3 id="modalPhotographerName"></h3>
            <p><strong>ID:</strong> <span id="modalPhotographerId"></span></p>
            <p><strong>Price:</strong> Rs.<span id="modalPhotographerPrice"></span></p>
            <div class="modal-buttons">
                <button onclick="bookPhotographer()">Book Now</button>
                <button onclick="closeModal()">Back</button>
            </div>
        </div>
    </div>

    <script>
        let selectedPhotographer = {}; // Holds the selected photographer details

        function showPhotographerDetails(photographerData) {
            if (!photographerData || !photographerData.photographer_name) {
                console.error("Invalid photographer data");
                return;
            }

            selectedPhotographer = photographerData;
            document.getElementById("modalPhotographerName").innerText = photographerData.photographer_name;
            document.getElementById("modalPhotographerId").innerText = photographerData.photographer_id;
            document.getElementById("modalPhotographerPrice").innerText = photographerData.price;
            document.getElementById("photographerModal").style.display = "flex";
        }

        function closeModal() {
            document.getElementById("photographerModal").style.display = "none";
        }

        window.onclick = function(event) {
            const modal = document.getElementById('photographerModal');
            if (event.target === modal) {
                closeModal();
            }
        };

        function bookPhotographer() {
            const photographerId = selectedPhotographer.photographer_id;

            if (!photographerId) {
                alert("Invalid photographer ID");
                return;
            }

            const url = `viewphoto.php?photographer_id=${encodeURIComponent(photographerId)}`;
            window.location.href = url;
        }
    </script>

</body>
</html>