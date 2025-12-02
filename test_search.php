<?php
include('includes/connect.php');

$search = 'test';
$searchLike = "%{$search}%";

$sql = "SELECT * FROM myitems WHERE isdeleted = 0 AND (iname LIKE ? OR barcode LIKE ?) LIMIT 5";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $searchLike, $searchLike);
$stmt->execute();
$result = $stmt->get_result();

echo "Search results for: " . $search . "\n";
while ($row = $result->fetch_assoc()) {
    echo "ID: " . $row['id'] . " - Name: " . $row['iname'] . " - Barcode: " . $row['barcode'] . "\n";
}
?>