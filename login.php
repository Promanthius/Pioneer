<?php
session_start();
include('db.php');

// Handle login when form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    // Prepare and execute query to check credentials
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Store user info in session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header("Location: index.php");  // Redirect to home page
        exit();
    } else {
        $error_message = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pioneer Prepper Management System - Login</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    
    <!-- Embedded CSS -->
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

        /* Error message */
        .error-message {
            color: red;
            font-size: 16px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

    <!-- Main container -->
    <div class="container">
        
        <!-- Form Section -->
        <div class="form-section">
            <h1>Welcome Back!</h1><br><br>
            <h1>Login</h1>
            
            <?php if (isset($error_message)): ?>
                <div class="error-message"><?= $error_message; ?></div>
            <?php endif; ?>

            <!-- Updated Form -->
            <form action="login.php" method="POST">
                <input type="text" name="username" placeholder="Username" required> 
                <input type="password" id="password" name="password" placeholder="Password" required>
                <input type="checkbox" id="togglePassword"> Show Password
                <button type="submit">Login</button>
            </form><br><br>
            <p>New Here? <a href="signup.php">Sign up now!</a></p>
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

    <!-- JavaScript for Password Visibility Toggle -->
    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');

        togglePassword.addEventListener('change', function () {
            // Toggle the type attribute
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
        });
    </script>
</body>
</html>
