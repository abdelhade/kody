<?php
header('Content-Type: application/json; charset=utf-8');

include(__DIR__ . '/../includes/connect.php');

$phone = trim($_POST['phone'] ?? '');
$name = trim($_POST['name'] ?? '');
$address = trim($_POST['address'] ?? '');

if ($phone === '' || $name === '' || $address === '') {
    echo json_encode(['success' => false, 'error' => 'Missing required fields'], JSON_UNESCAPED_UNICODE);
    exit;
}

$stmt = $conn->prepare(
    "INSERT INTO delivery_clients (client_name, phone, address)
     VALUES (?, ?, ?)
     ON DUPLICATE KEY UPDATE client_name = VALUES(client_name), address = VALUES(address), isdeleted = 0"
);

if (!$stmt) {
    echo json_encode(['success' => false, 'error' => $conn->error], JSON_UNESCAPED_UNICODE);
    exit;
}

$stmt->bind_param('sss', $name, $phone, $address);

if ($stmt->execute()) {
    echo json_encode(['success' => true], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(['success' => false, 'error' => $stmt->error], JSON_UNESCAPED_UNICODE);
}

$stmt->close();
$conn->close();
