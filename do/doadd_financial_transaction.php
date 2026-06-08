<?php
include('../includes/connect.php');

$snd_id = (int)$_POST['snd_id'];
$date = $conn->real_escape_string($_POST['date']);
$emp_names = $_POST['emp_name'] ?? [];
$types = $_POST['type'] ?? [];
$amounts = $_POST['amount'] ?? [];
$reasons = $_POST['reason'] ?? [];
$notes = $_POST['notes'] ?? [];

$x = count($emp_names);
for ($i = 0; $i < $x; $i++) { 
    if (empty($emp_names[$i])) continue;
    $emp_name = $conn->real_escape_string($emp_names[$i]);
    $type = (int)$types[$i];
    $amount = (float)$amounts[$i];
    $reason = $conn->real_escape_string($reasons[$i]);
    $note = $conn->real_escape_string($notes[$i] ?? '');

    $sql = "INSERT INTO financial_transactions(snd_id, date, emp_name, type, amount, reason, notes) 
            VALUES ('$snd_id', '$date', '$emp_name', '$type', '$amount', '$reason', '$note')";
    $conn->query($sql);
}

header('location:../financial_transactions.php');
?>
