<?php
include('../includes/connect.php');
$id = (int) ($_GET['id'] ?? 0);
if ($id > 0) {
    $conn->query("UPDATE payroll_calcs SET isdeleted = 1 WHERE snd_id = $id");
}
header('location:../payroll_calcs.php');
