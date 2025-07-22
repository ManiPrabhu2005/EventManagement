<?php
// Include the database connection
include('connection.php');

// Fetch music data from the database
$sql = "SELECT * FROM music";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Query preparation failed: " . $conn->error);
}

$stmt->execute();
$result = $stmt->get_result();

$tracks = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $tracks[] = $row;
    }
} else {
    $tracks = [];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Music Details</title>
    <link rel="stylesheet" href="../CSS Files/MUSICS.css">
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

        .music-card {
            border: 1px solid #ccc;
            padding: 15px;
            margin: 10px;
            border-radius: 8px;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .music-card:hover {
            transform: scale(1.05);
        }
    </style>
</head>
<body>

    <div class="navbar">
        <a href="user.php" class="back-arrow">&#8592; Back</a>
        <h1>Music Details</h1>
    </div>

    <div class="music-container">
        <?php if (!empty($tracks)): ?>
            <?php foreach ($tracks as $track): ?>
                <div class="music-card" onclick="showMusicDetails(<?php echo htmlspecialchars(json_encode($track)); ?>)">
                    <h2 class="music-name"><?php echo htmlspecialchars($track['music_name']); ?></h2>
                    <p><strong>ID:</strong> <?php echo htmlspecialchars($track['music_id']); ?></p>
                    <p><strong>Price:</strong> Rs.<?php echo number_format($track['price'], 2); ?></p>
                    <p><strong>Mopile:</strong><?php echo htmlspecialchars($track['contact_number']); ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No music found.</p>
        <?php endif; ?>
    </div>

    <!-- Modal for music preview -->
    <div id="musicModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Music Details</h2>
            <h3 id="modalMusicName"></h3>
            <p><strong>ID:</strong> <span id="modalMusicId"></span></p>
            <p><strong>Price:</strong> Rs.<span id="modalMusicPrice"></span></p>
            <div class="modal-buttons">
                <button onclick="bookMusic()">Book Now</button>
                <button onclick="closeModal()">Back</button>
            </div>
        </div>
    </div>

    <script>
        let selectedMusic = {}; // Holds the selected music details

        function showMusicDetails(musicData) {
            if (!musicData || !musicData.music_name) {
                console.error("Invalid music data");
                return;
            }

            selectedMusic = musicData;
            document.getElementById("modalMusicName").innerText = musicData.music_name;
            document.getElementById("modalMusicId").innerText = musicData.music_id;
            document.getElementById("modalMusicPrice").innerText = musicData.price;
            document.getElementById("musicModal").style.display = "flex";
        }

        function closeModal() {
            document.getElementById("musicModal").style.display = "none";
        }

        window.onclick = function(event) {
            const modal = document.getElementById('musicModal');
            if (event.target === modal) {
                closeModal();
            }
        };

        function bookMusic() {
            const musicId = selectedMusic.music_id;

            if (!musicId) {
                alert("Invalid music ID");
                return;
            }

            const url = `viewmusic.php?music_id=${encodeURIComponent(musicId)}`;
            window.location.href = url;
        }
    </script>

</body>
</html>