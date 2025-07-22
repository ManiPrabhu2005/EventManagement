<?php
// Include the database connection
include('connection.php');

// Fetch caterer data from the database
$sql = "SELECT * FROM caterers";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Query preparation failed: " . $conn->error);
}

$stmt->execute();
$result = $stmt->get_result();

$caterers = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $caterers[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caterer Details</title>
    <link rel="stylesheet" href="../CSS Files/CATERERS.css">
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

        .caterer-card {
            border: 1px solid #ccc;
            padding: 15px;
            margin: 10px;
            border-radius: 8px;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .caterer-card:hover {
            transform: scale(1.05);
        }
    </style>
</head>
<body>

    <div class="navbar">
        <a href="user.php" class="back-arrow">&#8592; Back</a>
        <h1>Caterer's Details</h1>
    </div>

    <div class="caterer-container">
        <?php if (!empty($caterers)): ?>
            <?php foreach ($caterers as $caterer): ?>
                <div class="caterer-card" onclick="showCatererDetails(<?php echo htmlspecialchars(json_encode($caterer)); ?>)">
                    <h2 class="caterer-name"><?php echo htmlspecialchars($caterer['caterer_name']); ?></h2>
                    
                    <p><strong>ID:</strong> <?php echo htmlspecialchars($caterer['caterer_id']); ?></p>
                    <p><strong>Price:</strong> Rs.<?php echo number_format($caterer['price'], 2); ?></p>
                    <p><strong>Mopile:</strong> <?php echo htmlspecialchars($caterer['contact_number']); ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No caterers found.</p>
        <?php endif; ?>
    </div>

    <!-- Modal for caterer preview -->
    <div id="catererModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Caterer Details</h2>
            <h3 id="modalCatererName"></h3>
            <p><strong>ID:</strong> <span id="modalCatererId"></span></p>
            <p><strong>Price:</strong> Rs.<span id="modalCatererPrice"></span></p>
            <div class="modal-buttons">
                <button onclick="bookCaterer()">Book Now</button>
                <button onclick="closeModal()">Back</button>
            </div>
        </div>
    </div>

    <script>
        let selectedCaterer = {}; // Holds the selected caterer details

        function showCatererDetails(catererData) {
            if (!catererData || !catererData.caterer_name) {
                console.error("Invalid caterer data");
                return;
            }

            selectedCaterer = catererData;
            document.getElementById("modalCatererName").innerText = catererData.caterer_name;
            document.getElementById("modalCatererId").innerText = catererData.caterer_id;
            document.getElementById("modalCatererPrice").innerText = catererData.price;
            document.getElementById("catererModal").style.display = "flex";
        }

        function closeModal() {
            document.getElementById("catererModal").style.display = "none";
        }

        window.onclick = function(event) {
            const modal = document.getElementById('catererModal');
            if (event.target === modal) {
                closeModal();
            }
        };

        function bookCaterer() {
            const catererId = selectedCaterer.caterer_id;

            if (!catererId) {
                alert("Invalid caterer ID");
                return;
            }

            const url = `viewcaterer.php?caterer_id=${encodeURIComponent(catererId)}`;
            window.location.href = url;
        }
    </script>

</body>
</html>