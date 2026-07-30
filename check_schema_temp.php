<?php
include('includes/connect.php');

echo "=== fat_details columns ===\n";
$r = $conn->query("SHOW COLUMNS FROM fat_details");
while ($f = $r->fetch_assoc()) {
    echo $f['Field'] . " | " . $f['Type'] . " | Key: " . $f['Key'] . " | Default: " . ($f['Default'] ?? 'NULL') . "\n";
}

echo "\n=== ot_head columns (selected) ===\n";
$r = $conn->query("SHOW COLUMNS FROM ot_head WHERE Field IN ('id','pro_id','fatid','pro_tybe','isdeleted','table_id','fat_total','fat_net','fat_disc','paid','paid_amount','paid_cash','paid_bank','remaining_amount','payment_status','payment_notes','acc1','acc2','emp_id','store_id','info')");
while ($f = $r->fetch_assoc()) {
    echo $f['Field'] . " | " . $f['Type'] . " | Default: " . ($f['Default'] ?? 'NULL') . "\n";
}

echo "\n=== Does ot_head have 'paid' and 'payment_notes' columns? ===\n";
$r = $conn->query("SHOW COLUMNS FROM ot_head LIKE 'paid_amount'");
echo "paid_amount: " . ($r->num_rows > 0 ? "YES" : "NO") . "\n";

$r = $conn->query("SHOW COLUMNS FROM ot_head LIKE 'payment_notes'");
echo "payment_notes: " . ($r->num_rows > 0 ? "YES" : "NO") . "\n";

$r = $conn->query("SHOW COLUMNS FROM ot_head LIKE 'payment_status'");
echo "payment_status: " . ($r->num_rows > 0 ? "YES" : "NO") . "\n";

$r = $conn->query("SHOW COLUMNS FROM ot_head LIKE 'paid'");
echo "paid: " . ($r->num_rows > 0 ? "YES" : "NO") . "\n";

$r = $conn->query("SHOW COLUMNS FROM ot_head LIKE 'op2'");
echo "op2: " . ($r->num_rows > 0 ? "YES" : "NO") . "\n";

$conn->close();
?>
