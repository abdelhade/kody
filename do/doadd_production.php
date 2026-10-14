<?php
include('../includes/connect.php');
require_once('../includes/production_helper.php');

production_save_rows($conn, $_POST);

header('location:../production.php');
