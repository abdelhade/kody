<?php
include('../includes/connect.php');
$pro_tybe = $_GET['t'] ?? '3';

if (!empty($rowstg['allow_all_accounts'])) {
    $resclients = $conn->query("SELECT * FROM `acc_head` WHERE (code like '122%' OR code like '211%') AND is_basic = 0 ORDER BY aname;");
} elseif ($pro_tybe == '4' || $pro_tybe == '10' || $pro_tybe == '12') {
    $resclients = $conn->query("SELECT * FROM `acc_head` WHERE code like '211%' AND is_basic = 0 ORDER BY id;");
} else {
    $resclients = $conn->query("SELECT * FROM `acc_head` WHERE code like '122%' AND is_basic = 0 ORDER BY id;");
}

if ($resclients) {
    while ($rowclients = $resclients->fetch_assoc()) {
        $plistAttr = '';
        if (strpos((string)$rowclients['code'], '122') === 0) {
            $plistAttr = ' data-price-list="' . max(1, (int) ($rowclients['price_list'] ?? 1)) . '"';
        }
        $isSup = (!empty($rowstg['allow_all_accounts']) && strpos((string)$rowclients['code'], '211') === 0);
        $displayName = htmlspecialchars($rowclients['aname']) . ($isSup ? ' (مورد)' : '');
        echo '<option value="' . $rowclients['id'] . '"' . $plistAttr . '>' . $displayName . '</option>';
    }
}
?>
