<?php
include('db.php');

$query = "SELECT id, name, quantity, weight_per_unit, category, cost_per_unit, expiration_date, date_registered,
                 (quantity * weight_per_unit) AS total_weight,
                 (quantity * cost_per_unit) AS total_cost
          FROM food_items";  

$stmt = $pdo->prepare($query);
$stmt->execute();
$food_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($food_items);
?>
