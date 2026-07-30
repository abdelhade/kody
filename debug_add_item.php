<?php
include('includes/connect.php');

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
$table_id = isset($_GET['table_id']) ? intval($_GET['table_id']) : 0;

echo "<h1>Debug: Add Item Flow Check</h1>";

if ($order_id > 0) {
    echo "<h2>1. Checking ot_head for order ID: $order_id</h2>";
    $r = $conn->query("SELECT id, pro_id, pro_tybe, fat_total, fat_net, info, 
                       table_id, paid_amount, payment_status,
                       isdeleted FROM ot_head WHERE id = $order_id");
    if ($r && $r->num_rows > 0) {
        $order = $r->fetch_assoc();
        echo "<pre>";
        print_r($order);
        echo "</pre>";
        
        echo "<h2>2. Checking fat_details for pro_id = $order_id (ot_head.id)</h2>";
        $r2 = $conn->query("SELECT fd.id as fat_id, fd.pro_id, fd.fatid, fd.item_id, 
                           m.iname, fd.qty_in, fd.qty_out, fd.price, fd.det_value,
                           (fd.qty_out - fd.qty_in) as actual_qty, fd.isdeleted
                           FROM fat_details fd
                           LEFT JOIN myitems m ON m.id = fd.item_id
                           WHERE fd.pro_id = $order_id AND fd.isdeleted = 0");
        echo "Found: " . ($r2 ? $r2->num_rows : 0) . " items<br>";
        if ($r2 && $r2->num_rows > 0) {
            echo "<pre>";
            while ($row = $r2->fetch_assoc()) {
                print_r($row);
            }
            echo "</pre>";
        } else {
            echo "<strong style='color:red'>❌ NO ITEMS FOUND in fat_details with pro_id = $order_id!</strong><br>";
            
            // Check if items exist with different pro_id
            echo "<h3>Checking fat_details with fatid = $order_id</h3>";
            $r3 = $conn->query("SELECT fd.id, fd.pro_id, fd.fatid, fd.item_id, 
                               m.iname, fd.qty_in, fd.qty_out,
                               (fd.qty_out - fd.qty_in) as actual_qty, fd.isdeleted
                               FROM fat_details fd
                               LEFT JOIN myitems m ON m.id = fd.item_id
                               WHERE fd.fatid = $order_id");
            echo "Found: " . ($r3 ? $r3->num_rows : 0) . " items with fatid<br>";
            if ($r3 && $r3->num_rows > 0) {
                echo "<pre>";
                while ($row = $r3->fetch_assoc()) {
                    print_r($row);
                }
                echo "</pre>";
            }
            
            // Check ALL fat_details for this order regardless
            echo "<h3>ALL fat_details (even deleted) for this order</h3>";
            $r4 = $conn->query("SELECT fd.id, fd.pro_id, fd.fatid, fd.item_id, 
                               m.iname, fd.qty_in, fd.qty_out, fd.isdeleted
                               FROM fat_details fd
                               LEFT JOIN myitems m ON m.id = fd.item_id
                               WHERE fd.pro_id = $order_id OR fd.fatid = $order_id");
            echo "Total: " . ($r4 ? $r4->num_rows : 0) . " records<br>";
            if ($r4 && $r4->num_rows > 0) {
                echo "<pre>";
                while ($row = $r4->fetch_assoc()) {
                    print_r($row);
                }
                echo "</pre>";
            }
        }
    } else {
        echo "<strong style='color:red'>❌ Order ID $order_id NOT FOUND in ot_head!</strong><br>";
    }
    
    echo "<h2>3. tables.php query simulation for table_id = $table_id</h2>";
    if ($table_id > 0) {
        $table_name = "طاولة " . $table_id;
        echo "Table name: $table_name<br>";
        
        // Simulate the condition from tables.php
        $conds = [];
        $has_table_id_col = false;
        $col_chk = $conn->query("SHOW COLUMNS FROM ot_head LIKE 'table_id'");
        if ($col_chk && $col_chk->num_rows > 0) $has_table_id_col = true;
        echo "Has table_id column: " . ($has_table_id_col ? "YES" : "NO") . "<br>";
        
        if ($has_table_id_col) {
            $conds[] = "table_id = $table_id";
        }
        $conds[] = "info LIKE '%طاولة $table_id%'";
        $conds[] = "info LIKE '%طاولة رقم $table_id%'";
        $conds[] = "info LIKE '%table $table_id%'";
        $clean_tname = $conn->real_escape_string($table_name);
        $conds[] = "info LIKE '%$clean_tname%'";
        
        $where = "(" . implode(" OR ", $conds) . ")";
        echo "SQL: SELECT * FROM ot_head WHERE $where AND pro_tybe = 9 AND isdeleted = 0 ORDER BY id DESC<br>";
        
        $r5 = $conn->query("SELECT id, pro_id, info, table_id, fat_total, fat_net, 
                           paid_amount, payment_status
                           FROM ot_head 
                           WHERE $where AND pro_tybe = 9 AND isdeleted = 0 
                           ORDER BY id DESC");
        echo "Found: " . ($r5 ? $r5->num_rows : 0) . " orders<br>";
        if ($r5 && $r5->num_rows > 0) {
            echo "<pre>";
            while ($row = $r5->fetch_assoc()) {
                print_r($row);
            }
            echo "</pre>";
        } else {
            echo "<strong style='color:red'>❌ NO ORDERS FOUND for table $table_id!</strong><br>";
            
            // Check each condition individually
            echo "<h4>Testing each condition separately:</h4>";
            foreach ($conds as $i => $cond) {
                $r6 = $conn->query("SELECT COUNT(*) as cnt FROM ot_head WHERE $cond AND pro_tybe = 9 AND isdeleted = 0");
                $cnt = $r6->fetch_assoc()['cnt'];
                echo "Condition $i: $cond → $cnt orders<br>";
            }
        }
    }
}

echo "<h2>4. Check last 10 POS orders</h2>";
$r7 = $conn->query("SELECT id, pro_id, fat_total, fat_net, info, table_id, 
                   paid_amount, payment_status, isdeleted, crtime 
                   FROM ot_head WHERE pro_tybe = 9 AND isdeleted = 0 
                   ORDER BY id DESC LIMIT 10");
if ($r7 && $r7->num_rows > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>pro_id</th><th>Total</th><th>Net</th><th>Table ID</th><th>Info</th><th>Paid</th><th>Payment Status</th></tr>";
    while ($row = $r7->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['pro_id'] . "</td>";
        echo "<td>" . $row['fat_total'] . "</td>";
        echo "<td>" . $row['fat_net'] . "</td>";
        echo "<td>" . ($row['table_id'] ?? 'NULL') . "</td>";
        echo "<td>" . htmlspecialchars($row['info'] ?? '') . "</td>";
        echo "<td>" . $row['paid_amount'] . "</td>";
        echo "<td>" . $row['payment_status'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

$conn->close();
?>
