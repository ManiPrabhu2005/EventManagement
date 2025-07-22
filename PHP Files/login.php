<?php
session_start();  // Start the session to store user data

// Include database connection
include('connection.php');

// Initialize variables for the form
$username = $password = "";
$usernameErr = $passwordErr = "";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Basic validation for empty fields
    if (empty($username)) {
        $usernameErr = "Username is required";
    }

    if (empty($password)) {
        $passwordErr = "Password is required";
    }

    // If there are no validation errors, proceed to check the database
    if (empty($usernameErr) && empty($passwordErr)) {
        // Query to get the user details based on the username
        $sql = "SELECT * FROM user WHERE username = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            // Check if the user exists
            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                
                // Check if the password matches (plaintext comparison)
                if ($password === $user['password']) {
                    // If password is correct, store user details in the session
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role'];  // Store the user's role (admin or user)

                    // Redirect user based on role
                    if ($user['role'] == 'admin') {
                        header("Location:adminpage.php");  // Redirect to admin dashboard
                    } else {
                        header("Location: design.php");   // Redirect to user dashboard
                    }
                    exit();  // Ensure the script stops execution after the redirect
                } else {
                    // If the password does not match, show the invalid password message
                    $passwordErr = "Incorrect password!";
                }
            } else {
                // If no user is found with that username, show the username error
                $usernameErr = "No user found with that username!";
            }
            $stmt->close();
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../CSS Files/login.css">  <!-- Link to CSS file -->
    <style>
        /* Style for the password eye icon */
        .password-container {
            position: relative;
        }

        .password-eye {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #aaa;
        }
    </style>
</head>
<body>
<nav>
    <div class="title">
        <p>Event Management System</p>
    </div>
    <ul class="nav-links">
        <li><a href="home.php"><i class="fas fa-home"></i> Home</a></li>
    </ul>
</nav>

<!-- Login Form Container -->
<div class="login-container">
    <h2>Login</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
        <!-- Username Field -->
        <label for="username">Username:</label>
        <input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
        <span class="error"><?php echo $usernameErr; ?></span>

        <!-- Password Field -->
        <label for="password">Password:</label>
        <div class="password-container">
            <input type="password" name="password" id="password" required>
            <i class="fas fa-eye password-eye" id="togglePassword"></i>
        </div>
        <span class="error"><?php echo $passwordErr; ?></span>

        <!-- Login Button -->
        <button type="submit">Login</button>
    </form>

    <!-- Link to Register Page -->
</div>

<script>
    // JavaScript to toggle password visibility
    const togglePassword = document.getElementById('togglePassword');
    const passwordField = document.getElementById('password');

    togglePassword.addEventListener('click', function () {
        const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordField.setAttribute('type', type);

        // Toggle the eye icon between open and closed
        this.classList.toggle('fa-eye');
        this.classList.toggle('fa-eye-slash');
    });
</script>

</body>
</html>