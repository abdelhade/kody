<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = '';
$dbname = 'focus';

// Create connection
$conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("فشل الاتصال بقاعدة البيانات: " . $conn->connect_error);
}

// Set charset to UTF-8
$conn->set_charset("utf8");

// Get recent orders
$query = "SELECT 
            o.id,
            o.pro_num as invoice_number,
            o.pro_date as date,
            o.fat_total as total,
            o.pro_tybe,
            CASE 
                WHEN o.closed = 1 THEN 'مكتمل'
                ELSE 'قيد التنفيذ'
            END as status,
            o.acc2 as client_id,
            (SELECT name FROM clients WHERE id = o.acc2 LIMIT 1) as customer_name,
            o.isdeleted
          FROM ot_head o
          WHERE o.isdeleted = 0
          ORDER BY o.id DESC
          LIMIT 10";

$result = $conn->query($query);

if ($result === false) {
    die("خطأ في الاستعلام: " . $conn->error);
}

$orders = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
}
?>
<!DOCTYPE html>
<html dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>اختبار الطلبات السابقة</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <style>
        body { padding: 20px; }
        .table { margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center mb-4">اختبار الطلبات السابقة</h1>
        
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">الاستعلام</h5>
            </div>
            <div class="card-body">
                <pre><?= htmlspecialchars($query) ?></pre>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">النتائج (<?= count($orders) ?>)</h5>
            </div>
            <div class="card-body">
                <?php if (count($orders) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>رقم الفاتورة</th>
                                    <th>التاريخ</th>
                                    <th>العميل</th>
                                    <th>الإجمالي</th>
                                    <th>النوع</th>
                                    <th>الحالة</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $index => $order): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= htmlspecialchars($order['invoice_number'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($order['date'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($order['customer_name'] ?? 'عميل نقدي') ?></td>
                                    <td><?= number_format($order['total'] ?? 0, 2) ?> ج.م</td>
                                    <td><?= htmlspecialchars($order['pro_tybe'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($order['status'] ?? 'N/A') ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning">
                        لا توجد طلبات متاحة
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">بيانات SQL الخام</h5>
            </div>
            <div class="card-body">
                <pre><?php print_r($orders) ?></pre>
            </div>
        </div>
    </div>
</body>
</html>
