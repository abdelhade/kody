<?php
include('includes/connect.php');
$conn->query("ALTER TABLE fat_details ADD COLUMN disc_pct DECIMAL(10,2) DEFAULT 0.00 AFTER discount;");
echo "Done.";
?>
