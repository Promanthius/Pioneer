<?php
session_start();
require 'db.php';

// Check if the user is logged in and has admin privileges
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.html");
    exit();
}

// Handle user deletion
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    if ($id) {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            echo "User deleted successfully!";
        } else {
            echo "Error deleting user: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Handle user creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_user'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Validate input
    if (empty($first_name) || empty($last_name) || empty($password) || empty($role)) {
        echo "All fields are required!";
        exit();
    }

    // Check password strength
    if (strlen($password) < 8 ||
        !preg_match('/[A-Z]/', $password) ||
        !preg_match('/[a-z]/', $password) ||
        !preg_match('/[0-9]/', $password) ||
        !preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password) ||
        strpos($password, $first_name) !== false ||
        strpos($password, $last_name) !== false) {
        echo "Weak password. Please follow the password strength rules.";
        exit();
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, password_hash, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $first_name, $last_name, $hashed_password, $role);

    if ($stmt->execute()) {
        echo "User created successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch all users for display
$stmt = $conn->prepare("SELECT id, first_name, last_name, role FROM users");
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body, html {
            height: 100%;
            font-family: Arial, sans-serif;
            background-image: url('img/bg_food.png'); /* PNG background image */
            background-size: cover;
            background-position: center;
            color: #ecf0f1; /* Light text color for readability */
        }

        /* Header styling */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            background-color: rgba(0, 0, 0, 0.7);
            position: relative; /* Make sure the header is positioned relative */
        }

        .header .logo img {
            height: 50px;
        }

        .header .nav {
            display: flex;
            align-items: center;
            position: relative; /* Make sure the nav is positioned relative */
        }

        .header .nav .nav-button {
            margin-right: 20px;
            cursor: pointer;
            position: relative; /* Position relative to handle dropdown positioning */
            margin-left: 50px;
        }

        .header .nav .nav-button img {
            height: 30px;
        }

        .header .clock-calendar {
            display: flex;
            align-items: center;
            font-family: 'Courier New', Courier, monospace;
            font-size: 18px;
            letter-spacing: 1px;
            color: #ffe0b5;
        }

        .header .clock-calendar span {
            margin-left: 15px;
        }

        .header .profile {
            display: flex;
            align-items: center;
        }

        .header .profile .profile-name {
            margin-right: 10px;
        }

        .header .profile {
            cursor: pointer;
            color: #ffffff;
            background: none;
            border: none;
            font-size: 16px;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            background-color: #333;
            color: #fff;
            border-radius: 5px;
            padding: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            top: 100%; /* Position below the button */
            right: 10; /* Align right side */
            z-index: 1000; /* Ensure dropdown is on top */
        }

        .dropdown-menu a {
            color: #fff;
            text-decoration: none;
            display: block;
            padding: 8px 12px;
        }

        .dropdown-menu a:hover {
            background-color: #575757;
        }

        /* Show the dropdown menu when the button is clicked */
        .dropdown-button.active + .dropdown-menu {
            display: block;
        }

        .dropdown-button {
            width: 40px; /* Adjust width to fit your image */
            height: 40px; /* Adjust height to fit your image */
            background-color: white; /* Remove any default background */
            border-radius: 10px;
            border: none; /* Remove default border */
            padding: 0; /* Remove default padding */
            margin: 0; /* Remove default margin */
            cursor: pointer; /* Change cursor to pointer on hover */
            outline: none; /* Remove outline */
            background-size: cover; /* Cover the button area */
            background-repeat: no-repeat; /* Prevent repeating the image */
        }
        
        .logout {
            background-color: #ffffff;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            color: #000000;
            transition: background-color 0.3s ease-in-out;
        }

        .logout:hover {
            background-color: rgba(59, 59, 59, 0.4);
            color: white;
        }

        /* Main content styling */
        .content {
            padding: 20px;
            background: rgba(0, 0, 0, 0.5);
            margin-top: 20px;
        }

        /* Tables styling */
        .table-container {
            margin: 20px 0;
            background: rgba(255, 255, 255, 0.158);
            padding: 20px;
            border-radius: 10px;
            position: relative; /* For absolute positioning of the totals table and add button */
        }

        table {
            width: 100%;
            border-collapse: collapse;
            color: #000000;
            font-weight: bold;
        }

        th, td {
            background-color: #949494a4;
            padding: 10px;
            border: 1px solid #fafafa;
        }

        th {
            background: #c7c7c7;
        }

        /* Button styling */
        .add-button {
            background-color: #ffffff;
            color: rgb(0, 0, 0);
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            position: absolute;
            bottom: 100%; /* Adjust position as needed */
            right: 50%; /* Adjust position as needed */
        }

        /* Footer styling */
        .footer {
            padding: 10px;
            background-color: rgba(0, 0, 0, 0.6);
            color: #ecf0f1;
            text-align: center;
            bottom: 1000;
        }

        .edit  {
            border: none;
            padding: 3px 20px;
            margin-bottom: 5px;
            border-radius: 5px;
            background-color: #fff;
            color: black;
            font-family:'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif ;
        }

        .edit:hover {
            color: white;
            background-color: rgba(0, 0, 0, 0.6);
            transition: background-color 0.3s ease-in-out;
        }

        .delete  {
            border: none;
            padding: 3px 12px;
            border-radius: 5px;
            background-color: #fff;
            color: black;
            font-family:'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif ;
        }

        .delete:hover {
            color: white;
            background-color: rgba(0, 0, 0, 0.6);
            transition: background-color 0.3s ease-in-out;
        }

       /* Modal Styling */
    .modal {
        display: none; /* Hidden by default */
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.5); /* Black background with opacity */
        justify-content: center;
        align-items: center;
        transition: opacity 0.3s ease;
    }

    /* Modal Styling */
.modal {
    display: none; /* Hidden by default */
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.5); /* Black background with opacity */
    justify-content: center;
    align-items: center;
    transition: opacity 0.3s ease;
}

.modal-content {
    background-color: #ffffff; /* White background */
    margin: 5% auto; /* Margin from top and center horizontally */
    padding: 20px;
    border: 1px solid #ccc; /* Light border */
    width: 80%;
    max-width: 500px; /* Adjusted for a more compact look */
    border-radius: 8px; /* Rounded corners */
    color: #333; /* Dark text color for readability */
    font-weight: normal;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2); /* Subtle shadow for depth */
    position: relative;
}

.modal-header {
    padding-bottom: 15px;
    border-bottom: 1px solid #eee;
    margin-bottom: 20px;
}

.modal-header h2 {
    margin: 0;
    font-size: 20px;
}

.close {
    color: #aaa;
    float: right;
    font-size: 24px;
    font-weight: bold;
    cursor: pointer;
}

.close:hover,
.close:focus {
    color: #333;
    text-decoration: none;
}

.modal-body {
    margin-bottom: 20px;
}

.modal-footer {
    display: flex;
    justify-content: flex-end;
    padding-top: 15px;
    border-top: 1px solid #eee;
}

.modal-footer button {
    background-color: #28a745; /* Green button for primary actions */
    color: white;
    border: none;
    padding: 10px 15px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    margin-left: 10px;
}

.modal-footer button.secondary {
    background-color: #6c757d; /* Gray button for secondary actions */
}

.modal-footer button:hover {
    background-color: #218838;
    transition: background-color 0.3s ease;
}

.modal-footer button.secondary:hover {
    background-color: #5a6268;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
}

.form-group input {
    width: calc(100% - 22px); /* Adjusted to fit within the form */
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

    </style>
</head>
<body>
    <h1>Admin Dashboard</h1>
    <a href="index.php">Back to Home</a>

    <h2>Create New User</h2>
    <form method="POST">
        <label for="first_name">First Name:</label>
        <input type="text" id="first_name" name="first_name" required><br>
        <label for="last_name">Last Name:</label>
        <input type="text" id="last_name" name="last_name" required><br>
        <label for="username">Username:</label>
        <input type="text" id="username" name="username"required><br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br>
        <label for="role">Role:</label>
        <select id="role" name="role" required>
            <option value="admin">Admin</option>
            <option value="staff">Staff</option>
            <option value="customer">Customer</option>
        </select><br>
        <button type="submit" name="create_user">Create User</button>
    </form>

    <h2>Manage Users</h2>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($user = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($user['id']); ?></td>
                <td><?php echo htmlspecialchars($user['first_name']); ?></td>
                <td><?php echo htmlspecialchars($user['last_name']); ?></td>
                <td><?php echo htmlspecialchars($user['role']); ?></td>
                <td>
                    <a href="?delete=<?php echo htmlspecialchars($user['id']); ?>" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
