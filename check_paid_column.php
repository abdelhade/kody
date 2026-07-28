<?php
include('includes/connect.php');
$result = $conn->query("SHOW COLUMNS FROM ot_head");
echo "Columns in ot_head:\n\n";
while ($row = $result->fetch_assoc()) {
    echo $row['Field'] . " | " . $row['Type'] . " | Null: " . $row['Null'] . " | Default: " . ($row['Default'] ?? 'NULL') . "\n";
}
$conn->close();
?>
