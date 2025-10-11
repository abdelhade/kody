<?php
include('../../includes/connect.php');

$search = isset($_GET['search']) ? $_GET['search'] : '';

$sql = "SELECT id, iname FROM myitems WHERE iname LIKE ? order by iname limit 5";
$stmt = $conn->prepare($sql);
$searchTerm = "%".$search."%";
$stmt->bind_param("s", $searchTerm);
$stmt->execute();
$result = $stmt->get_result();



$data = array();
while ($row = $result->fetch_assoc()) {
    $data[] = array("id" => $row['id'], "text" => $row['iname']);
}

$stmt->close();
$conn->close();

echo json_encode($data);
?>
