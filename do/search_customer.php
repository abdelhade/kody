<?php
header('Content-Type: application/json; charset=utf-8');

include(__DIR__ . '/../includes/connect.php');

$phone = trim($_POST['phone'] ?? '');

if ($phone === '') {
    echo json_encode(['found' => false, 'error' => 'Phone number is required'], JSON_UNESCAPED_UNICODE);
    exit;
}

// البحث أولاً في جدول عملاء الدليفري
$stmt = $conn->prepare(
    "SELECT client_name, address FROM delivery_clients WHERE phone = ? AND isdeleted = 0 LIMIT 1"
);

if ($stmt) {
    $stmt->bind_param('s', $phone);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        echo json_encode([
            'found' => true,
            'name' => $row['client_name'],
            'address' => $row['address']
        ], JSON_UNESCAPED_UNICODE);
        $stmt->close();
        $conn->close();
        exit;
    }
    $stmt->close();
}

// البحث في جدول العملاء الرئيسي (acc_head)
$stmt = $conn->prepare(
    "SELECT aname, phone, address FROM acc_head WHERE code LIKE '122%' AND isdeleted = 0 AND (phone = ? OR aname LIKE ?) LIMIT 1"
);

if ($stmt) {
    $phonePattern = '%' . $phone . '%';
    $stmt->bind_param('ss', $phone, $phonePattern);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        echo json_encode([
            'found' => true,
            'name' => $row['aname'],
            'address' => $row['address'] ?? ''
        ], JSON_UNESCAPED_UNICODE);
        $stmt->close();
        $conn->close();
        exit;
    }
    $stmt->close();
}

echo json_encode(['found' => false], JSON_UNESCAPED_UNICODE);
$conn->close();
