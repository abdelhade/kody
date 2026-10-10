<?php
include '../includes/connect.php';

if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['aname'])) {
    $id = $_GET['id'];
    foreach ($_POST as $key => $value) {
        $$key = $value;
    }
   
    if (!isset($_POST['is_stock'])) {
        $is_stock = 0;
    }
    if (!isset($_POST['secret'])) {
        $secret = 0;
    }

    
    if (!isset($_POST['is_fund'])) {
        $is_fund = 0;
    }

    
    if (!isset($_POST['rentable'])) {
        $rentable = 0;
    }
    $priceSql = '';
    if (isset($_POST['price_list'])) {
        $price_list = max(1, min(3, (int) $_POST['price_list']));
        $priceSql = ",price_list='$price_list'";
    }
    $sql = "UPDATE acc_head SET code='$code',aname='$aname',is_fund='$is_fund',rentable='$rentable',is_stock='$is_stock',parent_id='$parent_id',is_basic='$is_basic',secret='$secret'$priceSql WHERE id = '$id' ";
    
    $conn->query($sql);
    header("location:../accounts.php");
}
