<?php
session_start();
require_once 'db.php';  // Include the database connection file

// Ensure user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}

$username = $_SESSION['username'];

// Fetch the user's storage units using PDO
try {
    // Prepare the query to get storage units based on the logged-in user
    $stmt = $pdo->prepare("SELECT id, name FROM storage_units WHERE user_id = (SELECT id FROM users WHERE username = :username)");
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);  // Bind the username to the query
    $stmt->execute();

    // Fetch all storage units associated with the user
    $storage_units = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Check if storage units exist
    if (count($storage_units) === 0) {
        $no_storage_units = true;  // Flag to indicate no storage units found
    } else {
        $no_storage_units = false; // User has storage units
    }
} catch (PDOException $e) {
    die("Error fetching storage units: " . $e->getMessage());
}

// Handle new storage unit creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_storage_unit'])) {
    $unit_name = trim($_POST['unit_name']);

    // Check if the unit name is valid
    if (!empty($unit_name)) {
        try {
            // Prepare statement to insert the new storage unit into the database
            $stmt = $pdo->prepare("INSERT INTO storage_units (name, user_id) VALUES (:name, (SELECT id FROM users WHERE username = :username))");
            $stmt->bindParam(':name', $unit_name, PDO::PARAM_STR);
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();

            // Redirect to the same page to refresh and show the new storage unit
            header("Location: index.php");
            exit();
        } catch (PDOException $e) {
            die("Error creating storage unit: " . $e->getMessage());
        }
    } else {
        $error_message = "Storage unit name cannot be empty.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pioneer Prepper Management System</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body, html {
            height: 100%;
            font-family: Arial, sans-serif;
            overflow-y: auto
        }

        /* Background */
        .homepage {
            background-image: url('img/Bunker01.jpg');
            background-size: cover;
            background-position: center;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: #fff;
            overflow-y: auto;
        }

        .container, .logo, .storage-unit-selector, .feature, .footer {
            box-sizing: border-box;
        }

        /* Header styling */
        .header {
            position: absolute;
            top: 0;
            width: 100%;
            height: 60px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: rgba(0, 0, 0, 0.7);
            padding: 10px 30px;
            color: #fff;
            font-size: 18px;
        }

        /* Profile name on the left */
        .header .profile-name {
            position: absolute;
            font-size: 20px;
            font-weight: bold;
            padding-left: 43%;
        }

        /* Rustic clock/calendar style */
        .header .clock-calendar {
            display: flex;
            align-items: center;
            font-family: 'Courier New', Courier, monospace;
            font-size: 18px;
            letter-spacing: 1px;
            color: #ffe0b5;
        }

        .clock-calendar span {
            margin-left: 15px;
        }

        /* Log out button on the right */
        .header .logout {
            background-color: #ffffff;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            color: #000000;
            transition: background-color 0.3s ease-in-out;
        }

        .header .logout:hover {
            background-color: rgba(59, 59, 59, 0.4);
            color: white;
        }

        /* Logo styling */
        .logo {
            margin-bottom: 50px;
        }

        .logo img {
            width: 350px; /* Increased size for logo */
            height: auto;
        }

        /* Container for the three options */
        .container {
            display: flex;
            justify-content: space-around;
            width: 80%;
        }

        /* Style for each feature card */
        .feature {
            position: relative;
            width: 300px;
            height: 300px;
            border-radius: 15px;
            overflow: hidden;
            cursor: pointer;
            transition: transform 0.3s ease-in-out;
        }

        .feature:hover {
            transform: scale(1.05);
        }

        /* Images inside the feature cards */
        .feature img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: filter 0.3s ease-in-out;
        }

        /* Blurring image on hover */
        .feature:hover img {
            filter: blur(5px);
        }

        /* Text overlay inside the feature card */
        .feature .text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 24px;
            font-weight: bold;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);
            text-align: center;
            transition: font-size 0.3s ease-in-out;
        }

        /* Expanding text on hover */
        .feature:hover .text {
            font-size: 28px;
        }

        /* Footer styling */
        .footer {
            position: relative;
            bottom: 0;
            width: 100%;
            background-color: rgba(0, 0, 0, 0.4);
            color: #fff;
            text-align: center;
            padding: 10px;
        }

        .footer a {
            color: #fff;
            text-decoration: none;
            margin: 0 10px;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        /* Modal Styling */
        .modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 400px;
            color: #000;
        }

        .modal .close {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            position: absolute;
            top: 0;
            right: 20px;
        }

        .close:hover,
        .close:focus {
            color: black;
            cursor: pointer;
        }

        /* Form Styling inside the Modal */
        .form-container input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .form-container button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            border: none;
            color: white;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
        }

        .form-container button:hover {
            background-color: #45a049;
        }

                /* Style for the storage unit selector with a blurred, see-through platform */
        .storage-unit-selector {
            position: relative;
            background: rgba(0, 0, 0, 0.6); /* Semi-transparent background */
            backdrop-filter: blur(10px); /* Blur effect on the background */
            border-radius: 10px;
            padding: 20px;
            max-width: 400px;
            width: 90%;
            margin: 0 auto;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.3);
            color: #fff;
            font-family: Arial, sans-serif;
        }

        .storage-unit-selector label {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
            display: block;
            color: #fff;
        }

        .storage-unit-selector select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: rgba(255, 255, 255, 0.8);
            color: #333;
            font-size: 16px;
        }

        .storage-unit-selector select:focus {
            outline: none;
            border-color: #4CAF50;
        }

        .storage-unit-selector button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            border: none;
            color: white;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            margin-top: 10px;
        }

        .storage-unit-selector button:hover {
            background-color: #45a049;
        }

        /* Ensuring the modal open button is visible */
        .storage-unit-selector button {
            font-weight: bold;
        }

    </style>

    <script>
        // Function to update the clock
        function updateClock() {
            var now = new Date();
            var hours = now.getHours();
            var minutes = now.getMinutes();
            var seconds = now.getSeconds();
            var day = now.getDate();
            var month = now.getMonth() + 1;  // Months are 0-based
            var year = now.getFullYear();
            
            // Ensure two-digit formatting for hours, minutes, and seconds
            hours = hours < 10 ? '0' + hours : hours;
            minutes = minutes < 10 ? '0' + minutes : minutes;
            seconds = seconds < 10 ? '0' + seconds : seconds;

            // Format the clock time
            var timeString = hours + ":" + minutes + ":" + seconds;
            document.getElementById('clock').textContent = timeString;

            // Format the calendar date
            var dateString = month + "/" + day + "/" + year;
            document.getElementById('calendar').textContent = dateString;

            // Update the clock every 1000ms (1 second)
            setTimeout(updateClock, 1000);
        }

        // Function to open the modal for creating a new storage unit
        function openModal() {
            document.getElementById("storageUnitModal").style.display = "block";
        }

        // Function to close the modal
        function closeModal() {
            document.getElementById("storageUnitModal").style.display = "none";
        }

        // Close the modal if the user clicks outside of it
        window.onclick = function(event) {
            if (event.target == document.getElementById("storageUnitModal")) {
                closeModal();
            }
        }
    </script>
</head>
<body onload="updateClock()">

    <!-- Header -->
    <div class="header">
        <div class="clock-calendar">
            <span id="calendar"></span>
            <span id="clock"></span>
        </div>
        <div class="profile-name">Welcome, <?php echo htmlspecialchars($username); ?>!</div>
        <a href="logout.php?logout=true" class="logout">Log Out</a>
    </div>

    <!-- Main container with background -->
    <div class="homepage">

        <!-- Logo section -->
        <div class="logo">
            <img src="img/Logo.png" alt="Prepper Management Logo">
        </div>

        <!-- Storage Unit Selector -->
        <div class="storage-unit-selector">
            <form method="GET" action="index.php">
                <label for="storage_unit">Select Storage Unit:</label>
                    <select name="storage_unit" id="storage_unit" onchange="this.form.submit()">
                        <?php
                        // Check if a storage unit is selected via GET
                        $selected_unit = isset($_GET['storage_unit']) ? $_GET['storage_unit'] : null;

                        // Loop through each storage unit and create an option in the dropdown
                        foreach ($storage_units as $unit) {
                            $selected = ($unit['id'] == $selected_unit) ? 'selected' : ''; // Mark the selected unit
                            echo "<option value='{$unit['id']}' $selected>{$unit['name']}</option>";
                        }
                        ?>
                    </select>
            </form>
            <button onclick="openModal()">Create New Storage Unit</button>
        </div>

        <!-- Modal for creating new storage unit -->
        <div id="storageUnitModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal()">&times;</span>
                <h2>Create New Storage Unit</h2>
                <form method="POST" class="form-container">
                    <?php if (isset($error_message)) echo "<p style='color: red;'>$error_message</p>"; ?>
                    <input type="text" name="unit_name" placeholder="Enter storage unit name" required>
                    <button type="submit" name="create_storage_unit">Create</button>
                </form>
            </div>
        </div><br><br>

        <!-- Options for the 3 features -->
        <div class="container">
            <div class="feature" onclick="location.href='food.php';">
                <img src="img/Food.jpg" alt="Food Storage">
                <div class="text">Food Storage Management</div>
            </div>
            <div class="feature" onclick="location.href='fuel.php';">
                <img src="img/Fuel.jpg" alt="Fuel/Solar Power">
                <div class="text">Fuel/Solar Power Management</div>
            </div>
            <div class="feature" onclick="location.href='forage.html';">
                <img src="img/Forage.jpg" alt="Foraging Guide">
                <div class="text">Foraging Guide</div>
            </div>
        </div>
    </div>

    <!-- Footer section -->
    <div class="footer">
        <p>&copy; 2024 Pioneer Prepper Management System</p>
    </div>

</body>
</html>
