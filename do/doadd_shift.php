<?php
include('../includes/connect.php');
require_once('../includes/shift_attendance.php');
ensure_shift_single_fp_schema($conn);
ensure_shift_grace_rules_schema($conn);

$name = mysqli_real_escape_string($conn, $_POST['name'] ?? '');
$shiftstart = $_POST['shiftstart'] ?? '';
$shiftend = $_POST['shiftend'] ?? '';
$instart = $_POST['instart'] ?? '';
$inend = $_POST['inend'] ?? '';
$outstart = $_POST['outstart'] ?? '';
$outend = $_POST['outend'] ?? '';

$latelimit = (int)($_POST['latelimit'] ?? 0);
$earlylimit = (int)($_POST['earlylimit'] ?? 0);
$grace1_action = normalize_grace_action($_POST['grace1_action'] ?? 'actual', 'actual');

$latelimit_2 = (int)($_POST['latelimit_2'] ?? 0);
$earlylimit_2 = (int)($_POST['earlylimit_2'] ?? 0);
$grace2_action = normalize_grace_action($_POST['grace2_action'] ?? 'half', 'half');

// Create DateTime objects for start and end times
$endTime = new DateTime($shiftend);
$startTime = new DateTime($shiftstart);

// Calculate the difference in seconds
$timeDiffInSeconds = $endTime->getTimestamp() - $startTime->getTimestamp();
if ($timeDiffInSeconds <= 0) {
    $timeDiffInSeconds += 24 * 3600;
}

// Convert the difference to hours
$hours = round($timeDiffInSeconds / 3600, 2);

$day = array();
if (isset($_POST['sat'])) { array_push($day, '6'); }
if (isset($_POST['sun'])) { array_push($day, '7'); }
if (isset($_POST['mon'])) { array_push($day, '1'); }
if (isset($_POST['tus'])) { array_push($day, '2'); }
if (isset($_POST['wed'])) { array_push($day, '3'); }
if (isset($_POST['thur'])) { array_push($day, '4'); }
if (isset($_POST['fri'])) { array_push($day, '5'); }
$workingdays = implode(",", $day);

$single_fp_rule = normalize_single_fp_rule($_POST['single_fp_rule'] ?? 'half');
$ignore_early_in = isset($_POST['ignore_early_in']) ? 1 : 0;
$ignore_late_out = isset($_POST['ignore_late_out']) ? 1 : 0;

$sqlshft = "INSERT INTO shifts (
    name, shiftstart, shiftend, hours, instart, inend, outstart, outend,
    latelimit, earlylimit, grace1_action, latelimit_2, earlylimit_2, grace2_action,
    workingdays, single_fp_rule, ignore_early_in, ignore_late_out
) VALUES (
    '$name', '$shiftstart', '$shiftend', '$hours', '$instart', '$inend', '$outstart', '$outend',
    '$latelimit', '$earlylimit', '$grace1_action', '$latelimit_2', '$earlylimit_2', '$grace2_action',
    '$workingdays', '$single_fp_rule', '$ignore_early_in', '$ignore_late_out'
)";

$conn->query($sqlshft);
$conn->query("INSERT INTO `process`(`type`) VALUES ('add shift')");

header('location:../shifts.php');
exit;