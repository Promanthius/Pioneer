<?php
require 'db.php'; // Ensure your db.php file contains the database connection logic

// Handle form submission when POST request is made
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form values
    $first_name = isset($_POST['first_name']) ? $_POST['first_name'] : '';
    $last_name = isset($_POST['last_name']) ? $_POST['last_name'] : '';
    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

    // Check if password matches confirmation
    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match!');</script>";
        exit();
    }

    // Password validation
    if (strlen($password) < 8 || 
        !preg_match('/[A-Z]/', $password) || 
        !preg_match('/[a-z]/', $password) || 
        !preg_match('/[0-9]/', $password) || 
        !preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password) ||
        strpos($password, $first_name) !== false ||
        strpos($password, $last_name) !== false ||
        strpos($password, $username) !== false) {
        echo "<script>alert('Weak password. Please follow the password strength rules (At least 1 Upper Case, Lower Case, Special Character, and not similar to your name or username).');</script>";
        exit();
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Prepare SQL statement to insert new user
    $stmt = $conn->prepare("INSERT INTO users (username, first_name, last_name, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $first_name, $last_name, $hashed_password);

    // Execute query and provide feedback
    if ($stmt->execute()) {
        echo "<script>
                alert('Signup successful!');
                if (confirm('Signup successful! Do you want to go to the login page?')) {
                    window.location.href = 'login.php';
                }
              </script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pioneer Prepper Management System - Sign Up</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body, html {
            height: 100%;
            font-family: 'Roboto', sans-serif;
        }

        /* Main container */
        .container {
            display: flex;
            height: calc(100vh);
            align-items: center;
            justify-content: center;
            background: url('img/forest.png') no-repeat center center;
            background-size: cover;
        }

        /* Form section */
        .form-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            max-width: 400px;
            padding-left: 30px;
            padding-right: 10px;
        }

        .form-section h1 {
            margin-bottom: 20px;
            font-size: 30px;
            color: #fafafa;
            text-align: center;
        }

        .form-section form {
            width: 100%;
            background: #ffffff69;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .form-section form input {
            width: calc(100%);
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ffffff;
            border-radius: 4px;
            font-size: 16px;
        }

        .form-section form button {
            width: 100%;
            padding: 10px;
            background-color: #000000;
            border: none;
            border-radius: 4px;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .form-section form button:hover {
            background-color: #6c6c6d;
        }

        .form-section p {
            color: white; 
        }

        .form-section a {
            color: white; 
        }

        .form-section a:hover {
            color: #ffffff69;
            transition: background-color 0.3s;
        }

        /* Logo and slogan section */
        .logo-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: #fff;
            padding: 20px;
            height: 100%;
        }

        .logo-section img {
            width: 700px;
            height: auto;
            margin-bottom: 20px;
        }

        .logo-section h2 {
            margin-bottom: 20px;
            font-family: Cambria, Cochin, Georgia, Times, 'Times New Roman', serif;
            font-size: 40px;
        }

        .logo-section p {
            font-size: 18px;
            font-weight: 300;
        }

        /* Footer */
        .footer {
            background: rgba(0, 0, 0, 0.4);
            color: #ffffff;
            text-align: center;
            padding: 10px;
            position: fixed;
            bottom: 0;
            width: 100%;
        }

        .footer p {
            margin: 0;
        }
    </style>
</head>
<body>

    <!-- Main container -->
    <div class="container">
        
        <!-- Form Section -->
        <div class="form-section">
            <h1>Sign Up</h1>
            <form action="signup.php" method="POST">
                <input type="text" name="first_name" placeholder="First Name" required>
                <input type="text" name="last_name" placeholder="Last Name" required>
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" id="password" placeholder="Password" required>
                <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required>
                <input type="checkbox" id="showPassword" onclick="togglePassword()"> Show Password
                <button type="submit">Sign Up</button>
            </form>
            <p>Already have an account? <a href="login.php">Log in now!</a></p>
        </div>

        <!-- Logo and Slogan Section -->
        <div class="logo-section">
            <img src="img/Logo.png" alt="Pioneer Logo">
            <h2>Even If the World Falls, You Won't</h2>
        </div>

    </div>

    <!-- Footer -->
    <div class="footer">
        <p>&copy; 2024 Pioneer Prepper Management System. All rights reserved.</p>
    </div>

    <!-- Toggle password visibility -->
    <script>
        function togglePassword() {
            const passwordField = document.getElementById("password");
            const confirmPasswordField = document.getElementById("confirm_password");
            if (passwordField.type === "password") {
                passwordField.type = "text";
                confirmPasswordField.type = "text";
            } else {
                passwordField.type = "password";
                confirmPasswordField.type = "password";
            }
        }
    </script>

</body>
</html>
