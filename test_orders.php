<?php
session_start();

// Simulate logged in user
$_SESSION['user_id'] = 1;

// Database connection
$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = '';
$dbname = 'focus';
$conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset('utf8mb4');

echo "<h2>Testing Orders Query</h2>";

// Test 1: Check if ot_head table exists
echo "<h3>Test 1: Check if ot_head table exists</h3>";
$result = $conn->query("SHOW TABLES LIKE 'ot_head'");
echo "Table exists: " . ($result->num_rows > 0 ? "YES" : "NO") . "<br>";

// Test 2: Count all orders
echo "<h3>Test 2: Count all orders</h3>";
$result = $conn->query("SELECT COUNT(*) as total FROM ot_head");
$row = $result->fetch_assoc();
echo "Total orders: " . $row['total'] . "<br>";

// Test 3: Count orders with pro_tybe = 9
echo "<h3>Test 3: Count orders with pro_tybe = 9</h3>";
$result = $conn->query("SELECT COUNT(*) as total FROM ot_head WHERE pro_tybe = 9");
$row = $result->fetch_assoc();
echo "Orders with pro_tybe = 9: " . $row['total'] . "<br>";

// Test 4: Count non-deleted orders with pro_tybe = 9
echo "<h3>Test 4: Count non-deleted orders with pro_tybe = 9</h3>";
$result = $conn->query("SELECT COUNT(*) as total FROM ot_head WHERE isdeleted = 0 AND pro_tybe = 9");
$row = $result->fetch_assoc();
echo "Non-deleted orders with pro_tybe = 9: " . $row['total'] . "<br>";

// Test 5: Get sample orders
echo "<h3>Test 5: Sample orders (last 5)</h3>";
$query = "SELECT id, pro_num, pro_date, fat_total, pro_tybe, isdeleted, closed 
          FROM ot_head 
          ORDER BY id DESC 
          LIMIT 5";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Invoice</th><th>Date</th><th>Total</th><th>Type</th><th>Deleted</th><th>Closed</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['pro_num'] . "</td>";
        echo "<td>" . $row['pro_date'] . "</td>";
        echo "<td>" . $row['fat_total'] . "</td>";
        echo "<td>" . $row['pro_tybe'] . "</td>";
        echo "<td>" . $row['isdeleted'] . "</td>";
        echo "<td>" . $row['closed'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No orders found<br>";
}

// Test 6: Get POS orders (pro_tybe = 9)
echo "<h3>Test 6: POS orders (pro_tybe = 9, last 5)</h3>";
$query = "SELECT 
            o.id,
            COALESCE(o.pro_num, CONCAT('ORD-', o.id)) as invoice_number,
            o.pro_date as date,
            o.fat_total as total,
            CASE 
                WHEN o.closed = 1 THEN 'مكتمل'
                ELSE 'قيد التنفيذ'
            END as status,
            o.acc2 as client_id,
            COALESCE((SELECT aname FROM acc_head WHERE id = o.acc1 LIMIT 1), 'عميل نقدي') as customer_name,
            o.pro_tybe as order_type,
            o.info as order_info,
            o.age as order_age
          FROM ot_head o
          WHERE o.isdeleted = 0 AND o.pro_tybe = 9
          ORDER BY o.id DESC
          LIMIT 5";
          
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Invoice</th><th>Date</th><th>Total</th><th>Status</th><th>Customer</th><th>Type</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['invoice_number'] . "</td>";
        echo "<td>" . $row['date'] . "</td>";
        echo "<td>" . $row['total'] . "</td>";
        echo "<td>" . $row['status'] . "</td>";
        echo "<td>" . $row['customer_name'] . "</td>";
        
        $orderAgeType = '';
        switch($row['order_age'] ?? 1) {
            case 1: $orderAgeType = 'تيك أواي'; break;
            case 2: $orderAgeType = 'طاولة'; break;
            case 3: $orderAgeType = 'دليفري'; break;
            default: $orderAgeType = 'تيك أواي';
        }
        echo "<td>" . $orderAgeType . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No POS orders found<br>";
}

$conn->close();
?>
