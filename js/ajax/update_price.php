<?php
include('../../includes/connect.php');

$id = (int) ($_POST['id'] ?? 0);
$price = (float) ($_POST['price'] ?? 0);
$field = $_POST['field'] ?? 'price1';
$allowed = ['price1' => 'price1', 'price2' => 'price2', 'price3' => 'price3'];
if (!isset($allowed[$field])) {
    $field = 'price1';
}
$column = $allowed[$field];

if ($id < 1) {
    echo 'error';
    $conn->close();
    exit;
}

if ($column === 'price3') {
    $stmt = $conn->prepare('UPDATE myitems SET price3 = ?, market_price = ? WHERE id = ?');
    $stmt->bind_param('ddi', $price, $price, $id);
} else {
    $stmt = $conn->prepare("UPDATE myitems SET {$column} = ? WHERE id = ?");
    $stmt->bind_param('di', $price, $id);
}
$result = $stmt->execute();
$stmt->close();

$unit = $conn->prepare("UPDATE item_units SET {$column} = ? WHERE item_id = ? AND u_val = 1");
if ($unit) {
    $unit->bind_param('di', $price, $id);
    $unit->execute();
    $unit->close();
}

echo $result ? 'success' : 'error';

$conn->close();
?>
