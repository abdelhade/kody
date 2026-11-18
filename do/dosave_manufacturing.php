<?php
session_start();
include('../includes/connect.php');
require_once('../classes/Manufacturing.php');
require_once('../classes/UserDefaults.php');

$orderId = (int) ($_POST['order_id'] ?? 0);
Can::authorize($orderId > 0 ? 'manufacturing.edit' : 'manufacturing.add');

if ($orderId < 1) {
    UserDefaults::enforce($conn, $_POST, ['store_id' => 'store']);
}

$back = $orderId > 0 ? '../manufacturing.php?e=' . $orderId : '../manufacturing.php';

try {
    $saved = Manufacturing::save(
        $conn,
        $orderId,
        (int) ($_POST['store_id'] ?? 0),
        (int) ($_POST['item_id'] ?? 0),
        (float) ($_POST['produce_qty'] ?? 0),
        (string) ($_POST['pro_date'] ?? ''),
        (string) ($_POST['note'] ?? ''),
        is_array($_POST['comp_id'] ?? null) ? $_POST['comp_id'] : [],
        is_array($_POST['comp_qty'] ?? null) ? $_POST['comp_qty'] : [],
        (int) ($_SESSION['userid'] ?? 0)
    );
    header('Location: ../manufacturing.php?e=' . $saved . '&ok=1');
} catch (RuntimeException $e) {
    $_SESSION['mfg_error'] = $e->getMessage();
    $_SESSION['mfg_old'] = $_POST;
    header('Location: ' . $back);
}
exit;
