<?php
include('../includes/connect.php');
$pro_tybe = $_GET['t'];
echo $protybe;
if ($pro_tybe == '4') {
    $resclients = $conn->query("SELECT * FROM `acc_head` WHERE code like '211%'  AND is_basic = 0;");
}elseif ($pro_tybe == '3') {
    $resclients = $conn->query("SELECT * FROM `acc_head` WHERE code like '122%'  AND is_basic = 0;");
}
while ($rowclients = $resclients->fetch_assoc()) {
    $plistAttr = ($pro_tybe == '3') ? ' data-price-list="' . max(1, (int) ($rowclients['price_list'] ?? 1)) . '"' : '';
    echo '<option value="' . $rowclients['id'] . '"' . $plistAttr . '>' . $rowclients['aname'] . '</option>';
}
?>
