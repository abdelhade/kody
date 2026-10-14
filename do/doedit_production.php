<?php
include('../includes/connect.php');
require_once('../includes/production_helper.php');

$id = (int) ($_GET['edit'] ?? 0);
$conn->query("DELETE FROM productions WHERE snd_id = $id");
production_save_rows($conn, $_POST);

header('location:../production.php');
