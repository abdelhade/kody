<?php
include('includes/connect.php');

$res = $conn->query('SELECT id, pro_tybe, fat_total FROM ot_head WHERE pro_tybe = 11 ORDER BY id DESC LIMIT 1');
$row = $res->fetch_assoc();
echo '<b>Last SALES_RETURN invoice (pro_tybe=11):</b><pre>'; print_r($row); echo '</pre>';

if ($row) {
    $id = $row['id'];

    $det = $conn->query("SELECT item_id, qty_in, qty_out, fatid, isdeleted FROM fat_details WHERE fatid = $id");
    echo '<b>fat_details rows:</b><pre>';
    $has = false;
    while ($d = $det->fetch_assoc()) { print_r($d); $has = true; $item_id = $d['item_id']; }
    if (!$has) echo 'NO ROWS FOUND in fat_details for fatid='.$id;
    echo '</pre>';

    // Check also by pro_id
    $det3 = $conn->query("SELECT item_id, qty_in, qty_out, pro_id, fatid, isdeleted FROM fat_details WHERE pro_id = $id");
    echo '<b>fat_details by pro_id:</b><pre>';
    while ($d = $det3->fetch_assoc()) { print_r($d); $item_id = $d['item_id']; }
    echo '</pre>';

    if (isset($item_id)) {
        $itm = $conn->query("SELECT id, iname, itmqty FROM myitems WHERE id = $item_id")->fetch_assoc();
        echo '<b>myitems current qty:</b><pre>'; print_r($itm); echo '</pre>';

        $real = $conn->query("SELECT COALESCE(SUM(qty_in)-SUM(qty_out),0) as real_qty FROM fat_details WHERE item_id=$item_id AND isdeleted=0")->fetch_assoc();
        echo '<b>Real qty from fat_details:</b><pre>'; print_r($real); echo '</pre>';
    }
}
?>
