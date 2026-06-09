<?php
header('Content-Type: application/json; charset=utf-8');

include(__DIR__ . '/../includes/connect.php');

$phone = trim($_POST['phone'] ?? '');

if ($phone === '') {
    echo json_encode(['found' => false, 'error' => 'Phone number is required'], JSON_UNESCAPED_UNICODE);
    exit;
}

$stmt = $conn->prepare(
    "SELECT client_name, address FROM delivery_clients WHERE phone = ? AND isdeleted = 0 LIMIT 1"
);

if (!$stmt) {
    echo json_encode(['found' => false, 'error' => 'Database query failed'], JSON_UNESCAPED_UNICODE);
    exit;
}

$stmt->bind_param('s', $phone);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode([
        'found' => true,
        'name' => $row['client_name'],
        'address' => $row['address']
    ], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(['found' => false], JSON_UNESCAPED_UNICODE);
}

$stmt->close();
$conn->close();
