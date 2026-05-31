<?php
include('includes/connect.php');

$query = "
SELECT 
    i.id, i.code, i.iname, i.price1, i.cost_price,
    COALESCE(SUM(d.qty_out), 0) AS total_qty,
    COALESCE(SUM(d.det_value), 0) AS total_value
FROM myitems i 
LEFT JOIN fat_details d ON d.item_id = i.id 
    AND d.isdeleted = 0 
    AND (d.fat_tybe = 9 OR d.fat_tybe = 3)
WHERE i.isdeleted = 0
GROUP BY i.id
ORDER BY (total_value - (total_qty * i.cost_price)) DESC
LIMIT 5 OFFSET 0
";

try {
    $res = $conn->query($query);
    if(!$res) {
        echo "Error: " . $conn->error;
    } else {
        while($row = $res->fetch_assoc()){
            print_r($row);
        }
    }
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage();
}
?>
