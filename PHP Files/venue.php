<?php
session_start();  // Start the session to store user data

// Include database connection
include('connection.php');

// Define variables for form data and error messages
$venueName = $venuePrice = $venueCapacity = $venueFacilities = $venueDescription = $imagePath = "";
$venueNameErr = $venuePriceErr = $venueCapacityErr = $venueFacilitiesErr = $venueDescriptionErr = "";

// Path to the uploads directory
$targetDirectory = "uploads/";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $venueName = $_POST['venueName'];
    $venuePrice = $_POST['venuePrice'];
    $venueCapacity = $_POST['venueCapacity'];
    $venueFacilities = $_POST['venueFacilities'];
    $venueDescription = $_POST['venueDescription'];

    // Validate form fields
    if (empty($venueName)) { $venueNameErr = "Venue Name is required"; }
    if (empty($venuePrice)) { $venuePriceErr = "Venue Price is required"; }
    if (empty($venueCapacity)) { $venueCapacityErr = "Venue Capacity is required"; }
    if (empty($venueFacilities)) { $venueFacilitiesErr = "Venue Facilities are required"; }
    if (empty($venueDescription)) { $venueDescriptionErr = "Venue Description is required"; }

    // Handle image upload
    if (isset($_FILES['venueImage']) && $_FILES['venueImage']['error'] == 0) {
        // Ensure the uploads directory exists and is writable
        if (!is_dir($targetDirectory)) {
            mkdir($targetDirectory, 0777, true); // Create the uploads directory if it doesn't exist
        }

        $imageFileType = strtolower(pathinfo($_FILES['venueImage']['name'], PATHINFO_EXTENSION));
        $targetFile = $targetDirectory . basename($_FILES['venueImage']['name']);

        // Check if the file is an image
        if (getimagesize($_FILES['venueImage']['tmp_name']) !== false) {
            // Check if file already exists
            if (!file_exists($targetFile)) {
                // Check file size (max 2MB)
                if ($_FILES['venueImage']['size'] <= 2000000) {
                    // Allow certain file formats (JPG, JPEG, PNG, GIF)
                    if ($imageFileType == "jpg" || $imageFileType == "jpeg" || $imageFileType == "png" || $imageFileType == "jfif"  || $imageFileType == "webp") {
                        // Move the uploaded file to the target directory
                        if (move_uploaded_file($_FILES['venueImage']['tmp_name'], $targetFile)) {
                            $imagePath = $targetFile; // Store the image path
                        } else {
                            echo "<p class='error-message'>Sorry, there was an error uploading your file.</p>";
                        }
                    } else {
                        echo "<p class='error-message'>Sorry, only JPG, JPEG, PNG & GIF files are allowed.</p>";
                    }
                } else {
                    echo "<p class='error-message'>Sorry, your file is too large.</p>";
                }
            } else {
                echo "<p class='error-message'>Sorry, file already exists.</p>";
            }
        } else {
            echo "<p class='error-message'>File is not an image.</p>";
        }
    } else {
        echo "<p class='error-message'>No image uploaded or error with the image file.</p>";
    }

    // If no errors, insert the data into the database
    if (empty($venueNameErr) && empty($venuePriceErr) && empty($venueCapacityErr) && empty($venueFacilitiesErr) && empty($venueDescriptionErr)) {
        // SQL query to insert the data into the database without the venue_id (auto-incremented by DB)
        $sql = "INSERT INTO venues (venue_name, venue_price, venue_capacity, venue_facilities, venue_description, venue_image)
                VALUES (?, ?, ?, ?, ?, ?)";

        // Prepare and bind the statement
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ssisss", $venueName, $venuePrice, $venueCapacity, $venueFacilities, $venueDescription, $imagePath);

            // Execute the query
            if ($stmt->execute()) {
                echo "<script>
                        alert('Venue Details Added Successfully!');
                        window.location.href = 'adminpage.php'; // Redirect to admin dashboard
                    </script>";
            } else {
                echo "<p class='error-message'>Error: " . $stmt->error . "</p>";
            }
            $stmt->close();
        } else {
            echo "<p class='error-message'>Error preparing the SQL statement.</p>";
        }
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Venue Details Form</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../CSS Files/venue.css"> <!-- External CSS file linked -->
</head>
<body>

<div class="container">
    <h1 align="center">Venue Details</h1>
    <hr>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
        <div class="form-section">
            <!-- Remove Venue ID input field, as it will be auto-generated by the DB -->
            <label for="venueName">Venue Name:</label>
            <input type="text" id="venueName" name="venueName" value="<?php echo htmlspecialchars($venueName); ?>" required>
            <span class="error"><?php echo $venueNameErr; ?></span>

            <label for="venueImage">Venue Image:</label>
            <input type="file" id="venueImage" name="venueImage" accept="image/*" required>

            <label for="venuePrice">Venue Price:</label>
            <input type="number" id="venuePrice" name="venuePrice" value="<?php echo htmlspecialchars($venuePrice); ?>" required>
            <span class="error"><?php echo $venuePriceErr; ?></span>

            <label for="venueCapacity">Venue Capacity:</label>
            <input type="number" id="venueCapacity" name="venueCapacity" value="<?php echo htmlspecialchars($venueCapacity); ?>" required>
            <span class="error"><?php echo $venueCapacityErr; ?></span>

            <label for="venueFacilities">Venue Facilities:</label>
            <textarea id="venueFacilities" name="venueFacilities" rows="4" required><?php echo htmlspecialchars($venueFacilities); ?></textarea>
            <span class="error"><?php echo $venueFacilitiesErr; ?></span>

            <label for="venueDescription">Venue Description:</label>
            <textarea id="venueDescription" name="venueDescription" rows="4" required><?php echo htmlspecialchars($venueDescription); ?></textarea>
            <span class="error"><?php echo $venueDescriptionErr; ?></span>

            <div style="text-align: center;">
                <button type="submit">Add Venue</button>
                <button type="button" class="back-button" onclick="window.history.back();">Back</button>
            </div>
        </div>
    </form>

    <div class="image-preview">
        <h3>Image Preview</h3>
        <div id="imagePreview" class="preview-container">
            <!-- Image previews will appear here -->
        </div>
    </div>
</div>

<script>
    // Image preview script
    document.getElementById('venueImage').addEventListener('change', function(event) {
        const previewContainer = document.getElementById('imagePreview');
        previewContainer.innerHTML = ''; // Clear any existing preview

        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.alt = "Venue Image Preview";
                img.classList.add('preview-image');
                previewContainer.appendChild(img);
            }
            reader.readAsDataURL(file);
        }
    });
</script>

</body>
</html>
