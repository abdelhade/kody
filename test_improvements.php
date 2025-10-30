<?php
/**
 * Database Improvements Tester
 * Purpose: اختبار التحسينات والـ performance
 */

include('includes/connect.php');

?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>اختبار تحسينات القاعدة</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4">
    <h2 class="text-center mb-4">🧪 اختبار تحسينات قاعدة البيانات</h2>

    <?php
    // Test 1: Check Indexes
    echo '<div class="card mb-3">';
    echo '<div class="card-header bg-primary text-white"><h5>1️⃣ Indexes المضافة</h5></div>';
    echo '<div class="card-body"><div class="table-responsive"><table class="table table-sm">';
    echo '<thead><tr><th>الجدول</th><th>Index</th><th>الأعمدة</th></tr></thead><tbody>';
    
    $tables_to_check = ['myitems', 'ot_head', 'fat_details', 'acc_head'];
    foreach ($tables_to_check as $table) {
        $result = $conn->query("SHOW INDEX FROM $table");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr><td>$table</td><td>{$row['Key_name']}</td><td>{$row['Column_name']}</td></tr>";
            }
        }
    }
    echo '</tbody></table></div></div></div>';

    // Test 2: Check New Tables
    echo '<div class="card mb-3">';
    echo '<div class="card-header bg-success text-white"><h5>2️⃣ الجداول الجديدة</h5></div>';
    echo '<div class="card-body">';
    
    $new_tables = [
        'tables', 'payment_methods', 'invoice_payments', 'stock_movements_log',
        'offers', 'cash_register_sessions', 'audit_logs', 'return_invoices'
    ];
    
    foreach ($new_tables as $table) {
        $result = $conn->query("SHOW TABLES LIKE '$table'");
        $exists = $result && $result->num_rows > 0;
        $badge = $exists ? '<span class="badge bg-success">✅ موجود</span>' : '<span class="badge bg-danger">❌ غير موجود</span>';
        echo "<p><strong>$table:</strong> $badge</p>";
    }
    echo '</div></div>';

    // Test 3: Check Views
    echo '<div class="card mb-3">';
    echo '<div class="card-header bg-info text-white"><h5>3️⃣ الـ Views المنشأة</h5></div>';
    echo '<div class="card-body">';
    
    $result = $conn->query("SHOW FULL TABLES WHERE table_type = 'VIEW'");
    if ($result && $result->num_rows > 0) {
        echo '<ul class="list-group">';
        while ($row = $result->fetch_array()) {
            echo "<li class='list-group-item'>✅ {$row[0]}</li>";
        }
        echo '</ul>';
    } else {
        echo '<p class="text-warning">لم يتم العثور على Views</p>';
    }
    echo '</div></div>';

    // Test 4: Performance Test
    echo '<div class="card mb-3">';
    echo '<div class="card-header bg-warning"><h5>4️⃣ اختبار الأداء</h5></div>';
    echo '<div class="card-body">';
    
    // Test search by barcode
    $start = microtime(true);
    $result = $conn->query("SELECT * FROM myitems WHERE barcode = '1' LIMIT 1");
    $time1 = round((microtime(true) - $start) * 1000, 2);
    
    echo "<p><strong>البحث بالباركود:</strong> <span class='badge bg-info'>{$time1} ms</span></p>";
    
    // Test date range query
    $start = microtime(true);
    $result = $conn->query("SELECT COUNT(*) as cnt FROM ot_head WHERE pro_date >= '2024-01-01'");
    $time2 = round((microtime(true) - $start) * 1000, 2);
    
    echo "<p><strong>البحث بالتاريخ:</strong> <span class='badge bg-info'>{$time2} ms</span></p>";
    
    // Test join query
    $start = microtime(true);
    $result = $conn->query("
        SELECT m.iname, fd.qty_out, fd.price 
        FROM fat_details fd 
        INNER JOIN myitems m ON fd.item_id = m.id 
        LIMIT 100
    ");
    $time3 = round((microtime(true) - $start) * 1000, 2);
    
    echo "<p><strong>استعلام JOIN:</strong> <span class='badge bg-info'>{$time3} ms</span></p>";
    
    // Performance rating
    $avg_time = ($time1 + $time2 + $time3) / 3;
    $rating = $avg_time < 10 ? '🚀 ممتاز' : ($avg_time < 50 ? '✅ جيد' : '⚠️ يحتاج تحسين');
    
    echo "<hr><h6>التقييم العام: <span class='badge bg-primary'>$rating</span> (متوسط {$avg_time} ms)</h6>";
    echo '</div></div>';

    // Test 5: Database Statistics
    echo '<div class="card mb-3">';
    echo '<div class="card-header bg-secondary text-white"><h5>5️⃣ إحصائيات القاعدة</h5></div>';
    echo '<div class="card-body"><div class="row">';
    
    // Total products
    $result = $conn->query("SELECT COUNT(*) as cnt FROM myitems WHERE isdeleted = 0");
    $products = $result->fetch_assoc()['cnt'];
    echo "<div class='col-md-3 text-center'><h3 class='text-primary'>$products</h3><p>صنف</p></div>";
    
    // Total invoices
    $result = $conn->query("SELECT COUNT(*) as cnt FROM ot_head WHERE pro_tybe = 9");
    $invoices = $result->fetch_assoc()['cnt'];
    echo "<div class='col-md-3 text-center'><h3 class='text-success'>$invoices</h3><p>فاتورة</p></div>";
    
    // Total categories
    $result = $conn->query("SELECT COUNT(*) as cnt FROM item_group WHERE IFNULL(isdeleted, 0) = 0");
    $categories = $result->fetch_assoc()['cnt'];
    echo "<div class='col-md-3 text-center'><h3 class='text-info'>$categories</h3><p>تصنيف</p></div>";
    
    // Total customers
    $result = $conn->query("SELECT COUNT(*) as cnt FROM acc_head WHERE code LIKE '122%' AND isdeleted = 0");
    $customers = $result->fetch_assoc()['cnt'];
    echo "<div class='col-md-3 text-center'><h3 class='text-warning'>$customers</h3><p>عميل</p></div>";
    
    echo '</div></div></div>';

    // Test 6: Check New Columns
    echo '<div class="card mb-3">';
    echo '<div class="card-header bg-dark text-white"><h5>6️⃣ الأعمدة الجديدة</h5></div>';
    echo '<div class="card-body">';
    
    $columns_to_check = [
        'ot_head' => ['table_id', 'order_type', 'payment_status', 'isdeleted'],
        'myitems' => ['track_stock', 'reorder_level', 'is_active'],
        'fat_details' => ['isdeleted']
    ];
    
    foreach ($columns_to_check as $table => $columns) {
        echo "<h6>$table:</h6><ul>";
        $result = $conn->query("DESCRIBE $table");
        $existing_columns = [];
        while ($row = $result->fetch_assoc()) {
            $existing_columns[] = $row['Field'];
        }
        
        foreach ($columns as $col) {
            $exists = in_array($col, $existing_columns);
            $badge = $exists ? '<span class="badge bg-success">✅</span>' : '<span class="badge bg-danger">❌</span>';
            echo "<li>$badge $col</li>";
        }
        echo "</ul>";
    }
    echo '</div></div>';
    ?>

    <!-- Action Buttons -->
    <div class="card">
        <div class="card-body text-center">
            <h5>🎯 الخطوات التالية</h5>
            <div class="btn-group mt-3" role="group">
                <a href="run_migrations.php?confirm=yes" class="btn btn-lg btn-danger">
                    <i class="fas fa-play"></i> تنفيذ Migrations
                </a>
                <a href="pos_barcode.php" class="btn btn-lg btn-primary">
                    <i class="fas fa-cash-register"></i> اختبار POS
                </a>
                <a href="dashboard.php" class="btn btn-lg btn-success">
                    <i class="fas fa-home"></i> الرئيسية
                </a>
            </div>
        </div>
    </div>

</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>

