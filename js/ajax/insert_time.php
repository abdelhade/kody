<?php
include('../../includes/connect.php');


$startTime = $_POST['start_time'];
$endTime = $_POST['end_time'];
$clientid = $_GET['id'];
$q = $_GET['q'];

$rowres = $conn->query("SELECT id FROM `reservations` where client = '$clientid' order by id DESC")->fetch_assoc();
$resid = $rowres['id'];


$startDateTime = new DateTime($startTime);
$endDateTime = new DateTime($endTime);
$durationS = $endDateTime->getTimestamp() - $startDateTime->getTimestamp();
$duration = $durationS/60;


if ($q == 0) {
$sql = "UPDATE reservations SET start_time = '$startTime' WHERE id = '$resid'";
}elseif ($q == 1) {
$sql = "UPDATE reservations SET end_time = '$endTime' , duration = $duration WHERE id = '$resid'";

}
if ($conn->query($sql) === TRUE) {
    echo "Record inserted successfully ".$endTime;
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
