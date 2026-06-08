<?php
include('../includes/connect.php');
$id = (int)$_GET['edit'];

$date = $conn->real_escape_string($_POST['date']);
$emp_names = $_POST['emp_name'] ?? [];
$types = $_POST['type'] ?? [];
$amounts = $_POST['amount'] ?? [];
$reasons = $_POST['reason'] ?? [];
$notes = $_POST['notes'] ?? [];

// حذف القديم
$conn->query("DELETE FROM financial_transactions WHERE snd_id = '$id'");

// إدخال الجديد
$x = count($emp_names);
for ($i = 0; $i < $x; $i++) { 
    if (empty($emp_names[$i])) continue;
    $emp_name = $conn->real_escape_string($emp_names[$i]);
    $type = (int)$types[$i];
    $amount = (float)$amounts[$i];
    $reason = $conn->real_escape_string($reasons[$i]);
    $note = $conn->real_escape_string($notes[$i] ?? '');

    $sql = "INSERT INTO financial_transactions(snd_id, date, emp_name, type, amount, reason, notes) 
            VALUES ('$id', '$date', '$emp_name', '$type', '$amount', '$reason', '$note')";
    $conn->query($sql);
}

header('location:../financial_transactions.php');
?>
