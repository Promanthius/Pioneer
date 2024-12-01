<?php
session_start();
require 'db.php'; // Include the database connection

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

// Fetch food items from the database
$query = "SELECT id, name, quantity, weight_per_unit, category, cost_per_unit, expiration_date FROM food_items";
$stmt = $pdo->prepare($query);
$stmt->execute();
$food_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate total weight and cost
$total_weight = 0;
$total_cost = 0;
foreach ($food_items as $item) {
    $total_weight += $item['quantity'] * $item['weight_per_unit'];
    $total_cost += $item['quantity'] * $item['cost_per_unit'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Storage Management</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Include Chart.js -->
</head>
<body onload="updateClock()">

    <!-- Header section -->
    <div class="header">
        <div class="nav">
            <button class="dropdown-button" onclick="toggleDropdown()">
                <img src="img/menu.png" alt="Dropdown">
            </button>
            <div class="dropdown-menu">
                <a href="index.php">Home</a>
                <a href="#">Fuel/Solar Management</a>
                <a href="#">Foraging Guide</a>
            </div>
        </div>
        <div class="clock-calendar">
            <span id="calendar"></span>
            <span id="clock"></span>
        </div>
        <div class="profile">
            <span class="profile-name">Welcome, <?php echo htmlspecialchars($username); ?>!</span>
            <a href="logout.php" class="logout">Log Out</a>
        </div>
    </div>

    <!-- Main content -->
    <div class="content">
        <div class="table-container">
            <!-- Main table -->
            <table>
                <thead>
                    <tr>
                        <th>Item ID</th>
                        <th>Item Name</th>
                        <th>Quantity</th>
                        <th>Weight per Unit (kg)</th>
                        <th>Category</th>
                        <th>Cost per Unit (PHP)</th>
                        <th>Expiration Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="food-items-table-body">
                    <?php foreach ($food_items as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['id']); ?></td>
                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                        <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                        <td><?php echo htmlspecialchars($item['weight_per_unit']); ?></td>
                        <td><?php echo htmlspecialchars($item['category']); ?></td>
                        <td><?php echo htmlspecialchars($item['cost_per_unit']); ?></td>
                        <td><?php echo htmlspecialchars($item['expiration_date']); ?></td>
                        <td>
                            <button class="edit" onclick="openEditModal(<?php echo htmlspecialchars($item['id']); ?>)">Edit</button>
                            <button class="delete" onclick="deleteItem(<?php echo htmlspecialchars($item['id']); ?>)">Delete</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Totals table -->
            <table class="totals-table">
                <tbody>
                    <tr>
                        <td><strong>Total Weight:</strong></td>
                        <td id="total-weight"><?php echo $total_weight; ?> kg</td>
                    </tr>
                    <tr>
                        <td><strong>Total Cost:</strong></td>
                        <td id="total-cost">₱<?php echo number_format($total_cost, 2); ?></td>
                    </tr>
                </tbody>
            </table>
            <button class="add-button" onclick="openAddModal()">Add Item</button>
        </div>

        <!-- Chart Section -->
        <div class="chart-container">
            <canvas id="foodChart"></canvas>
 <script>
                const ctx = document.getElementById('foodChart').getContext('2d');
                const foodChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: <?php echo json_encode(array_column($food_items, 'name')); ?>,
                        datasets: [{
                            label: 'Quantity',
                            data: <?php echo json_encode(array_column($food_items, 'quantity')); ?>,
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            </script>
        </div>
    </div>

    <!-- Modals for Add/Edit -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeAddModal()">&times;</span>
            <h2>Add Food Item</h2>
            <form id="addItemForm" onsubmit="addItem(event)">
                <label for="name">Item Name:</label>
                <input type="text" id="name" required>
                <label for="quantity">Quantity:</label>
                <input type="number" id="quantity" required>
                <label for="weight_per_unit">Weight per Unit (kg):</label>
                <input type="number" id="weight_per_unit" required>
                <label for="category">Category:</label>
                <input type="text" id="category" required>
                <label for="cost_per_unit">Cost per Unit (PHP):</label>
                <input type="number" id="cost_per_unit" required>
                <label for="expiration_date">Expiration Date:</label>
                <input type="date" id="expiration_date" required>
                <button type="submit">Add Item</button>
            </form>
        </div>
    </div>

    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <h2>Edit Food Item</h2>
            <form id="editItemForm" onsubmit="editItem(event)">
                <input type="hidden" id="edit_item_id">
                <label for="edit_name">Item Name:</label>
                <input type="text" id="edit_name" required>
                <label for="edit_quantity">Quantity:</label>
                <input type="number" id="edit_quantity" required>
                <label for="edit_weight_per_unit">Weight per Unit (kg):</label>
                <input type="number" id="edit_weight_per_unit" required>
                <label for="edit_category">Category:</label>
                <input type="text" id="edit_category" required>
                <label for="edit_cost_per_unit">Cost per Unit (PHP):</label>
                <input type="number" id="edit_cost_per_unit" required>
                <label for="edit_expiration_date">Expiration Date:</label>
                <input type="date" id="edit_expiration_date" required>
                <button type="submit">Update Item</button>
            </form>
        </div>
    </div>

    <script>
        function openAddModal() {
            document.getElementById('addModal').style.display = 'block';
        }

        function closeAddModal() {
            document.getElementById('addModal').style.display = 'none';
        }

        function openEditModal(id) {
            // Fetch item data and populate the edit form
            // This is a placeholder for the actual implementation
            document.getElementById('edit_item_id').value = id;
            document.getElementById('edit_name').value = ''; // Fetch from DB
            document.getElementById('edit_quantity').value = ''; // Fetch from DB
            document.getElementById('edit_weight_per_unit').value = ''; // Fetch from DB
            document.getElementById('edit_category').value = ''; // Fetch from DB
            document.getElementById('edit_cost_per_unit').value = ''; // Fetch from DB
            document.getElementById('edit_expiration_date').value = ''; // Fetch from DB
            document.getElementById('editModal').style.display = 'block';
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        function addItem(event) {
            event.preventDefault();
            // Add item logic here
        // Add item logic here
            const name = document.getElementById('name').value;
            const quantity = document.getElementById('quantity').value;
            const weight_per_unit = document.getElementById('weight_per_unit').value;
            const category = document.getElementById('category').value;
            const cost_per_unit = document.getElementById('cost_per_unit').value;
            const expiration_date = document.getElementById('expiration_date').value;

            // AJAX request to add item to the database
            fetch('add_item.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    name,
                    quantity,
                    weight_per_unit,
                    category,
                    cost_per_unit,
                    expiration_date
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload(); // Reload the page to see the new item
                } else {
                    alert('Error adding item: ' + data.message);
                }
            })
            .catch(error => console.error('Error:', error));
        }

        function editItem(event) {
            event.preventDefault();
            const id = document.getElementById('edit_item_id').value;
            const name = document.getElementById('edit_name').value;
            const quantity = document.getElementById('edit_quantity').value;
            const weight_per_unit = document.getElementById('edit_weight_per_unit').value;
            const category = document.getElementById('edit_category').value;
            const cost_per_unit = document.getElementById('edit_cost_per_unit').value;
            const expiration_date = document.getElementById('edit_expiration_date').value;

            // AJAX request to update item in the database
            fetch('edit_item.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    id,
                    name,
                    quantity,
                    weight_per_unit,
                    category,
                    cost_per_unit,
                    expiration_date
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload(); // Reload the page to see the updated item
                } else {
                    alert('Error updating item: ' + data.message);
                }
            })
            .catch(error => console.error('Error:', error));
        }

        function deleteItem(id) {
            if (confirm('Are you sure you want to delete this item?')) {
                // AJAX request to delete item from the database
                fetch('delete_item.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ id })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload(); // Reload the page to see the changes
                    } else {
                        alert('Error deleting item: ' + data.message);
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        }

        function updateClock() {
            const now = new Date();
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            document.getElementById('calendar').innerText = now.toLocaleDateString(undefined, options);
            document.getElementById('clock').innerText = now.toLocaleTimeString();
            setTimeout(updateClock, 1000);
        }

        // Close modals when clicking outside of them
        window.onclick = function(event) {
            const addModal = document.getElementById('addModal');
            const editModal = document.getElementById('editModal');
            if (event.target == addModal) {
                closeAddModal();
            } else if (event.target == editModal) {
                closeEditModal();
            }
        }
    </script>

</body>
</html>