<?php
include('../includes/connect.php');
require_once('../includes/EntitlesSeeder.php');

if ($_SERVER['REQUEST_METHOD'] == "POST") {

	$id = $_GET['id'];

	foreach ($_POST as $key => $value) {
		$$key = $value;
	}

	if (!isset($calc_type) || !in_array($calc_type, ['monthly', 'daily'], true)) {
		$calc_type = 'monthly';
	}
	$ent_tybe = EntitlesSeeder::normalize((int) ($_POST['ent_tybe'] ?? EntitlesSeeder::HOURS_ONLY));

	$salary = (isset($_POST['salary']) && $_POST['salary'] !== '' && is_numeric($_POST['salary'])) ? floatval($_POST['salary']) : 0.00;
	$active = isset($_POST['active']) ? 1 : 0;


	if ($_FILES['imgs']['size'] !== 0) {

		$filekvr = $_FILES['imgs']['name'];
		$kvr_uploaded_size = $_FILES['imgs']['size'];
		$kvr_name = $_FILES['imgs']['tmp_name'];
		$arrkvr = explode(".", $filekvr);
		$kvr_ext = ($arrkvr['1']);
		$allow_ext = ["jpg", "png", "gif", "jpeg"];
		if (!in_array($kvr_ext, $allow_ext)) {
			echo $kvr_ext . "<h2>الملف المحمل ليس صوره او امتداد غير مسموح به</h2>";
			exit();
		}
		if ($kvr_uploaded_size > 20000000) {
			echo "<h2>بعض الملفات اكبر من اللازم  20 ميجا بايت </h2>";
			exit();
		}
		$new_kvr_name = $arrkvr['0'] . rand(1, 1000000) . "." . $arrkvr['1'];
		move_uploaded_file($kvr_name, "../assets/$new_kvr_name");
	}

	$shift_group_sql = !empty($_POST['shift_group_id']) ? (int) $_POST['shift_group_id'] : 'NULL';

	$sql = "UPDATE `employees`
	SET `name` = '$name' ,
	`number` = '$number' ,
	`email` = '$email' ,
	`dateofbirth` = '$dateofbirth' ,
	`gender` = '$gender' ,
	`info` = '$info' ,
	`active` = '$active' , 
	`address` = '$address' , 
	`address2` = '$address2' , 
	`town` = '$town' , 
	`jop` = '$jop' , 
	`department` = '$department' , 
	`joplevel` = '$joplevel' , 
	`joptybe` = '$joptybe' ,
	`dateofhire` = '$dateofhire' , 
	`dateofend` = '$dateofend' , 
	`salary` = '$salary' , 
	`shift` = '$shift' , 
	`shift_group_id` = $shift_group_sql , 
	`basma_id` = '$basma_id' , 
	`basma_name` = '$basma_name' , 
	`password` = '$password' , 
	`hour_extra` = '$hour_extra' , 
	`day_extra` = '$day_extra' , 
	`ent_tybe` = '$ent_tybe',
	`calc_type` = '$calc_type'
	WHERE id = $id";



	$conn->query($sql);
	header('location:../employees.php');
}
