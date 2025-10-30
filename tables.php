<?php include('includes/header.php') ?>

<style>
.floating-pos-btn {
    position: fixed;
    bottom: 30px;
    left: 30px;
    width: 70px;
    height: 70px;
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
    border-radius: 50%;
    box-shadow: 0 8px 16px rgba(0,0,0,0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    z-index: 9999;
    transition: all 0.3s;
    cursor: pointer;
    text-decoration: none;
}
.floating-pos-btn:hover {
    transform: scale(1.1) rotate(-10deg);
    box-shadow: 0 12px 24px rgba(0,0,0,0.4);
    background: linear-gradient(135deg, #f5576c 0%, #f093fb 100%);
    color: white;
    text-decoration: none;
}
</style>
<?php
$sql = "CREATE TABLE IF NOT EXISTS tables (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tname VARCHAR(255) NOT NULL,
    table_case INT NOT NULL DEFAULT 0,
    crtime DATETIME DEFAULT CURRENT_TIMESTAMP,
    mdtime DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    isdeleted TINYINT(1) NOT NULL DEFAULT 0,
    branch VARCHAR(255) DEFAULT NULL,
    tatnet VARCHAR(255) DEFAULT NULL
)";
$conn->query($sql);

// إضافة طاولات تجريبية إذا لم تكن موجودة
$check_tables = $conn->query("SELECT COUNT(*) as count FROM tables WHERE isdeleted = 0");
$tables_count = $check_tables->fetch_assoc()['count'];

if ($tables_count == 0) {
    // إضافة 10 طاولات تجريبية
    for ($i = 1; $i <= 10; $i++) {
        $table_name = "طاولة " . $i;
        $conn->query("INSERT INTO tables (tname, table_case) VALUES ('$table_name', 0)");
    }
}

// جلب الطاولات من قاعدة البيانات
$tables_query = "SELECT * FROM tables WHERE isdeleted = 0 ORDER BY id ASC";
$tables_result = $conn->query($tables_query);

// الطاولة المختارة
$selected_table = isset($_GET['table_id']) ? intval($_GET['table_id']) : null;
$order_data = [];
$order_items = [];
$order_totals = [
    'total' => 0.00,
    'discount' => 0.00,
    'extra' => 0.00,
    'net' => 0.00,
    'paid' => 0.00,
    'remaining' => 0.00
];

// إذا تم اختيار طاولة، جلب بيانات الطلب
$selected_table_name = '';
if ($selected_table) {
    // جلب اسم الطاولة
    $table_name_query = "SELECT tname FROM tables WHERE id = $selected_table";
    $table_name_result = $conn->query($table_name_query);
    if ($table_name_result && $table_name_result->num_rows > 0) {
        $selected_table_name = $table_name_result->fetch_assoc()['tname'];
    }
    
    // جلب الطلب النشط للطاولة (يتم البحث باستخدام حقل info الذي يحتوي على اسم الطاولة)
    // ملاحظة: يمكن تحسين هذا لاحقاً بإضافة عمود table_id لجدول ot_head
    $order_query = "SELECT * FROM ot_head WHERE info LIKE '%$selected_table_name%' AND pro_tybe = 9 ORDER BY id DESC LIMIT 1";
    $order_result = $conn->query($order_query);
    
    if ($order_result && $order_result->num_rows > 0) {
        $order_data = $order_result->fetch_assoc();
        $order_id = $order_data['id'];
        
        // جلب أصناف الطلب من fat_details
        $items_query = "SELECT fd.*, i.iname, i.price1 as sprice 
                       FROM fat_details fd 
                       LEFT JOIN myitems i ON fd.item_id = i.id 
                       WHERE fd.pro_id = $order_id AND fd.isdeleted = 0";
        $items_result = $conn->query($items_query);
        
        if ($items_result) {
            while ($item = $items_result->fetch_assoc()) {
                $order_items[] = $item;
            }
        }
        
        // حساب الإجماليات
        $order_totals['total'] = floatval($order_data['fat_total'] ?? 0);
        $order_totals['discount'] = floatval($order_data['fat_disc'] ?? 0);
        $order_totals['extra'] = floatval($order_data['fat_plus'] ?? 0);
        $net = $order_totals['total'] - $order_totals['discount'] + $order_totals['extra'];
        $order_totals['net'] = $net;
        $order_totals['paid'] = 0; // يمكن إضافة حقل للمدفوع لاحقاً
        $order_totals['remaining'] = $net;
    }
}
?>

<div class="nav">

</div>
<div class="row">
    <div class="col-1">

    </div>
    <div class="col">
<div class="card">
    <div class="card-head">
    <center>
        <p class="bg-zinc-50 text-lg">ادارة الطاولات</p>
    </center>
    </div>

    <div class="card-body overflow-scroll max-h-96">
        <?php 
        if ($tables_result && $tables_result->num_rows > 0) {
            while ($table = $tables_result->fetch_assoc()) {
                $table_id = $table['id'];
                $table_name = $table['tname'];
                $table_case = $table['table_case'];
                
                // تحديد لون الطاولة حسب الحالة
                $bg_color = ($table_case == 0) ? 'bg-sky-400' : 'bg-red-300';
                
                // إضافة border للطاولة المختارة
                $selected_class = ($selected_table == $table_id) ? 'border-4 border-blue-800' : '';
                
                echo '<a href="tables.php?table_id=' . $table_id . '" class="btn ' . $bg_color . ' p-4 m-4 ' . $selected_class . '">';
                echo htmlspecialchars($table_name);
                echo '</a>';
            }
        } else {
            echo '<p class="text-center text-gray-500 p-4">لا توجد طاولات. يرجى إضافة طاولات من صفحة الإعدادات.</p>';
        }
        ?>
    </div>
   
   
   
    <div class="card-footer">
        <table>
            <thead>
                <tr>
                    <th class="border-4 m-2 p-2">قيمه الطلب</th>
                    <th class="border-4 m-2 p-2">خصم</th>
                    <th class="border-4 m-2 p-2">اضافي</th>
                    <th class="border-4 m-2 p-2">صافي</th>
                    <th class="border-4 m-2 p-2">مسدد</th>
                    <th class="border-4 m-2 p-2">باقي</th>
                </tr>
                
                <tr>
                    <th class="border-4 m-2 p-2 text-blue-700"><?= number_format($order_totals['total'], 2) ?></th>
                    <th class="border-4 m-2 p-2 text-blue-700"><?= number_format($order_totals['discount'], 2) ?></th>
                    <th class="border-4 m-2 p-2 text-blue-700"><?= number_format($order_totals['extra'], 2) ?></th>
                    <th class="border-4 m-2 p-2 text-blue-700"><?= number_format($order_totals['net'], 2) ?></th>
                    <th class="border-4 m-2 p-2 text-blue-700"><?= number_format($order_totals['paid'], 2) ?></th>
                    <th class="border-4 m-2 p-2 text-blue-700"><?= number_format($order_totals['remaining'], 2) ?></th>
                </tr>
            </thead>
        </table>
        <div class="row">
            <div class="col-8 border-4 p-4">
                <table class="table table-responsive table-bordered">
                    <thead>
                        <tr>
                        <th>م</th>
                        <th class="w-80">اسم الصنف</th>
                        <th class="w-64">ملاحظات</th>
                        <th>كميه</th>
                        <th>سعر</th>
                        <th>القيمه</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if (count($order_items) > 0) {
                            $counter = 1;
                            foreach ($order_items as $item) {
                                $item_name = $item['iname'] ?? 'غير محدد';
                                $quantity = $item['qty'] ?? 0;
                                $price = $item['sprice'] ?? 0;
                                $total = $quantity * $price;
                                $notes = $item['notes'] ?? '';
                                
                                echo '<tr>';
                                echo '<td>' . $counter . '</td>';
                                echo '<td>' . htmlspecialchars($item_name) . '</td>';
                                echo '<td>' . htmlspecialchars($notes) . '</td>';
                                echo '<td>' . number_format($quantity, 2) . '</td>';
                                echo '<td>' . number_format($price, 2) . '</td>';
                                echo '<td>' . number_format($total, 2) . '</td>';
                                echo '</tr>';
                                $counter++;
                            }
                        } else {
                            echo '<tr><td colspan="6" class="text-center text-gray-500">لا توجد أصناف في الطلب</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <div class="col-4 border-4 p-4">
                <div class="row">
                    <?php if ($selected_table): ?>
                        <a href="pos_barcode.php?table=<?= urlencode($selected_table_name) ?>" class="col-12 btn bg-sky-200 m-1 text-decoration-none">
                            <?= empty($order_data) ? 'طلب جديد' : 'تعديل الطلب' ?>
                        </a>
                        <button class="col-4 btn bg-zinc-200 m-1" onclick="alert('قريباً')">سداد نقدي</button>
                        <button class="col-4 btn bg-zinc-200 m-1" onclick="alert('قريباً')">طباعه التحضير</button>
                        <button class="col-4 btn bg-zinc-200 m-1" onclick="alert('قريباً')">طباعه </button>
                    <?php else: ?>
                        <div class="col-12 alert alert-info m-1">اختر طاولة لبدء الطلب</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

</div>
</div>

</div>

<!-- زر عائم للذهاب للـ POS -->
<a href="pos_barcode.php" class="floating-pos-btn" title="POS الكاشير">
    <i class="fas fa-cash-register"></i>
</a>

<?php include('includes/footer.php') ?>
