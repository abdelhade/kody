<?php
include('includes/connect.php');
$r = $conn->query("SELECT id, tname, is_merged, parent_table_id FROM tables WHERE isdeleted=0 ORDER BY id");
echo "<table border=1 cellpadding=5>";
echo "<tr><th>ID</th><th>Name</th><th>is_merged</th><th>parent_table_id</th></tr>";
while($row = $r->fetch_assoc()) {
    echo "<tr><td>{$row['id']}</td><td>{$row['tname']}</td><td>{$row['is_merged']}</td><td>{$row['parent_table_id']}</td></tr>";
}
echo "</table>";
