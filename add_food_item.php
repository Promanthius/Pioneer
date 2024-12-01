<?php
include('db.php');

$name = $_POST['name'];
$quantity = $_POST['quantity'];
$weight = $_POST['weight'];
$category = $_POST['category'];
$cost = $_POST['cost'];
$expiration_date = $_POST['expiration_date'];

$query = "INSERT INTO food_items (name, quantity, weight_per_unit, category, cost_per_unit, expiration_date) 
          VALUES (?, ?, ?, ?, ?, ?)";

$stmt = $pdo->prepare($query);
$stmt->execute([$name, $quantity, $weight, $category, $cost, $expiration_date]);

echo "Food item added successfully!";
?>
