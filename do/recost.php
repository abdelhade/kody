<?php
include('../includes/connect.php');

$res = $conn->query("SELECT * FROM fat_details WHERE isdeleted = 0 ORDER BY crtime");

while ($row = $res->fetch_assoc()) {
    print_r($row);
    echo "<br><hr>";    
        $curqty = $row['qty_in'];
        $curprice = $row['price'];
        $crtime = $row['crtime'];
        $item = $row['item_id'];
        $id = $row['id'];
            $oldqtyRow = $conn->query("SELECT SUM(qty_in) - SUM(qty_out) AS old_qty 
                        FROM fat_details 
                        WHERE isdeleted = 0 
                        AND item_id = '$item'
                        AND crtime < '$crtime'")->fetch_assoc();
        $oldqty = $oldqtyRow['old_qty'] ?? 0;
            $oldpriceRow =$conn->query("SELECT cost_price FROM fat_details WHERE isdeleted = 0 AND pro_tybe = 4 AND item_id = '$item' AND crtime < '$crtime' order by crtime desc")->fetch_assoc();
        $oldprice = $oldpriceRow['cost_price'] ?? 0;

        $new_cost = ($curprice * $curqty + $oldprice * $oldqty) / ($curqty + $oldqty);
        if ($row['pro_tybe'] == 4) {
        if ($oldqty == 0 ) {
            $conn->query("UPDATE fat_details set cost_price = '$curprice' where id = '$id'");
        }else{
            $conn->query("UPDATE fat_details set cost_price = '$new_cost' where id = '$id'");
        }}
    

    if ($row['pro_tybe'] == 3 || $row['pro_tybe'] == 9 ) {
        $profit = $row['qty_out'] * ($row['price'] - $oldprice);
        if ($oldqty == 0) {
        $conn->query("UPDATE fat_details set cost_price = 0 ,profit = $profit where id = '$id'");
        echo "--------------------------";
        }else{
        $conn->query("UPDATE fat_details set profit = $profit ,cost_price = $new_cost where id = '$id'");

        }
    }
}

 header('location:../myitems.php');
?>
