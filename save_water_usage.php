<?php
include('db.php');

$data = json_decode(file_get_contents('php://input'), true);
$usage = $data['usage'] ?? null;

if ($usage) {
    $stmt = $pdo->prepare("INSERT INTO water_usage (usage, date) VALUES (?, NOW())");
    if ($stmt->execute([$usage])) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to log water usage.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid usage.']);
}