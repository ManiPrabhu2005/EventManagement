<?php
// Include database connection
include('connection.php');

// Initialize variables for error handling
$username = $phone = $email = $password = "";
$usernameErr = $phoneErr = $emailErr = $passwordErr = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $username = $_POST['username'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Basic validation
    if (empty($username)) {
        $usernameErr = "Username is required";
    }
    if (empty($phone)) {
        $phoneErr = "Phone number is required";
    }
    if (empty($email)) {
        $emailErr = "Email is required";
    }
    if (empty($password)) {
        $passwordErr = "Password is required";
    }

    // If no errors, insert data into database
    if (empty($usernameErr) && empty($phoneErr) && empty($emailErr) && empty($passwordErr)) {
        // Insert the plain-text password directly into the database
        $sql = "INSERT INTO user(username, phone, email, password) VALUES (?, ?, ?, ?)";

        // Prepare and bind statement
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ssss", $username, $phone, $email, $password);

            // Execute the statement
            if ($stmt->execute()) {
                // Set a flag to show the success message
                $successMsg = true;
            } else {
                echo "<p>Error: " . $stmt->error . "</p>";
            }

            // Close the statement
            $stmt->close();
        } else {
            echo "<p>Prepared statement error: " . $conn->error . "</p>";
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
    <title>Registration Page</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Basic reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
        }

        /* Navigation Bar */
        nav {
            background-color:blue;
            padding:5px;
            color: white;
        }

        nav .title p {
            font-size: 25px;
            font-weight: bold;
        }

        nav ul {
            list-style: none;
            display: flex;
            justify-content: flex-end;
        }

        nav ul li {
            margin-left: 20px;
        }

        nav ul li a {
            color: white;
            text-decoration: none;
            font-size: 16px;
        }

        nav ul li a:hover {
            color: #28a745;
        }

        /* Registration Form */
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
            width: 400px;
            margin: 40px auto;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        label {
            font-size: 14px;
            color: #333;
        }

        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .error {
            color: red;
            font-size: 12px;
        }

        .submit-btn {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px;
            width: 100%;
            cursor: pointer;
            border-radius: 5px;
        }

        .submit-btn:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>

<!-- Navigation Bar -->
<nav>
    <div class="title">
        <p>Event Management System</p>
    </div>
    <ul class="nav-links">
        <li><a href="home.php"><i class="fas fa-home"></i> Home</a></li>
    </ul>
</nav>

<!-- Registration Form -->
<div class="container">
    <h1>Register</h1>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
        <!-- Username field -->
        <label for="username">Username:</label>
        <input type="text" id="username" name="username"    placeholder="Enter Username" value="<?php echo htmlspecialchars($username); ?>">
        <span class="error"><?php echo $usernameErr; ?></span>
       
        <!-- Phone field -->
        <label for="phone">Phone Number:</label>
        <input type="tel" id="phone" name="phone"  pattern="[0-9]{10}"   placeholder="Enter 10-digit mobile number"   value="<?php echo htmlspecialchars($phone); ?>">
        <span class="error"><?php echo $phoneErr; ?></span>

        <!-- Email field -->
        <label for="email">Email:</label>
        <input type="email" id="email" name="email"   placeholder="Enter your mail id" value="<?php echo htmlspecialchars($email); ?>">
        <span class="error"><?php echo $emailErr; ?></span>

        <!-- Password field -->
        <label for="password">Password:</label>
        <input type="password" id="password" name="password"    placeholder="Enter Password" value="<?php echo htmlspecialchars($password); ?>">
        <span class="error"><?php echo $passwordErr; ?></span>

        <!-- Submit button -->
        <button type="submit" class="submit-btn">Register</button>
    </form>
</div>

<?php
// JavaScript to show alert on successful registration and redirect
if (isset($successMsg) && $successMsg === true) {
    echo "<script>
            alert('Registration Successful!');
            window.location.href = 'home.php'; // Redirect to home page
          </script>";
}
?>

</body>
</html>
