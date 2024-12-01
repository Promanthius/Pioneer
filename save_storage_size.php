<?php
include('db.php');

$data = json_decode(file_get_contents('php://input'), true);
$size = $data['size'] ?? null;

if ($size) {
    $stmt = $pdo->prepare("INSERT INTO water_storage (size, date_set) VALUES (?, NOW())");
    if ($stmt->execute([$size])) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to save storage size.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid size.']);
}