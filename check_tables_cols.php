<?php
include('includes/connect.php');

echo "<h3>Tables Table Columns:</h3>";
$r = $conn->query('SHOW COLUMNS FROM tables');
if ($r) {
    echo "<ul>";
    while ($f = $r->fetch_assoc()) {
        echo "<li>" . $f['Field'] . " | " . $f['Type'] . "</li>";
    }
    echo "</ul>";
} else {
    echo "Error: " . $conn->error;
}

echo "<h3>ot_head Table Columns (first 30):</h3>";
$r2 = $conn->query('SHOW COLUMNS FROM ot_head');
if ($r2) {
    echo "<ul>";
    $count = 0;
    while ($f = $r2->fetch_assoc()) {
        $count++;
        if ($count > 30) { echo "<li>...and more</li>"; break; }
        echo "<li>" . $f['Field'] . " | " . $f['Type'] . " | Default: " . ($f['Default'] ?? 'NULL') . "</li>";
    }
    echo "</ul>";
} else {
    echo "Error: " . $conn->error;
}

echo "<h3>ot_head columns 'paid', 'payment', 'remaining':</h3>";
$r3 = $conn->query("SHOW COLUMNS FROM ot_head WHERE Field LIKE '%paid%' OR Field LIKE '%payment%' OR Field LIKE '%remaining%' OR Field LIKE '%payment_notes%' OR Field LIKE '%payment_status%'");
if ($r3 && $r3->num_rows > 0) {
    echo "<ul>";
    while ($f = $r3->fetch_assoc()) {
        echo "<li><strong>" . $f['Field'] . "</strong> | " . $f['Type'] . " | Default: " . ($f['Default'] ?? 'NULL') . " | Null: " . $f['Null'] . "</li>";
    }
    echo "</ul>";
} else {
    echo "No matching columns found. Error: " . $conn->error;
}

echo "<h3>Does 'tables' have 'parent_table_id' column?</h3>";
$r4 = $conn->query("SHOW COLUMNS FROM tables WHERE Field = 'parent_table_id'");
if ($r4 && $r4->num_rows > 0) {
    echo "<strong>YES</strong> - Column exists";
} else {
    echo "<strong>NO</strong> - Column does not exist. Error: " . $conn->error;
}

echo "<h3>Does 'tables' have 'is_merged' column?</h3>";
$r5 = $conn->query("SHOW COLUMNS FROM tables WHERE Field = 'is_merged'");
if ($r5 && $r5->num_rows > 0) {
    echo "<strong>YES</strong> - Column exists";
} else {
    echo "<strong>NO</strong> - Column does not exist. Error: " . $conn->error;
}

$conn->close();
?>
