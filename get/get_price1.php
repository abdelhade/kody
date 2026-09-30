<?php
include('../includes/connect.php'); // تأكد أن ملف الاتصال موجود

header('Content-Type: application/json');

$barcode = $_GET['barcode'] ?? '';

if ($barcode) {
    $stmt = $conn->prepare("SELECT iname, price1, price2, price3, market_price FROM myitems WHERE barcode = ?");
    $stmt->bind_param("s", $barcode);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $price3 = (float) ($row['price3'] ?? 0);
        if ($price3 <= 0) {
            $price3 = (float) ($row['market_price'] ?? 0);
        }
        echo json_encode([
            'iname' => $row['iname'],
            'price1' => $row['price1'],
            'price2' => $row['price2'] ?? 0,
            'price3' => $price3
        ]);
    } else {
        echo json_encode(['iname' => null, 'price1' => null]);
    }
} else {
    echo json_encode(['iname' => null, 'price1' => null]);
}
?>
