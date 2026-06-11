<?php
include('../includes/connect.php');
require_once('../includes/shift_attendance.php');
ensure_shift_single_fp_schema($conn);
$name = $_POST['name'];
    $shiftstart = $_POST['shiftstart'];
    $shiftend = $_POST['shiftend'];
    $instart = $_POST['instart'];
    $inend = $_POST['inend'];
    $outstart = $_POST['outstart'];
    $outend = $_POST['outend'];
    $latelimit = $_POST['latelimit'];
    $earlylimit = $_POST['earlylimit'];
    

    
// Create DateTime objects for start and end times
$endTime = new DateTime($shiftend);
$startTime = new DateTime($shiftstart);

// Calculate the difference in seconds
$timeDiffInSeconds = $endTime->getTimestamp() - $startTime->getTimestamp();

// Convert the difference to hours
$hours = $timeDiffInSeconds / 3600;

    $day = array();
if (isset($_POST['sat'])) {array_push($day,'6');}
if (isset($_POST['sun'])) {array_push($day,'7');}
if (isset($_POST['mon'])) {array_push($day,'1');}
if (isset($_POST['tus'])) {array_push($day,'2');}
if (isset($_POST['wed'])) {array_push($day,'3');}
if (isset($_POST['thur'])) {array_push($day,'4');}
if (isset($_POST['fri'])) {array_push($day,'5');}
$workingdays = implode(",",$day);

$single_fp_rule = normalize_single_fp_rule($_POST['single_fp_rule'] ?? 'half');

$sqlshft = "INSERT INTO shifts (name, shiftstart, shiftend, hours , instart, inend, outstart, outend, latelimit, earlylimit, workingdays, single_fp_rule) VALUES ('$name','$shiftstart','$shiftend','$hours','$instart','$inend','$outstart','$outend','$latelimit','$earlylimit','$workingdays','$single_fp_rule')";
$conn->query($sqlshft);
$conn->query("INSERT INTO `process`(`type`) VALUES ('add shift')");

header('location:../shifts.php');

?>