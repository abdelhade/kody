<?php include('includes/header.php') ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<style>
.bg-sky-400 { background-color: #38bdf8 !important; color: white !important; }
.bg-red-300 { background-color: #fca5a5 !important; color: white !important; }
.bg-sky-200 { background-color: #bae6fd !important; }
.bg-zinc-200 { background-color: #e4e4e7 !important; }
.border-blue-800 { border-color: #1e40af !important; }
.text-blue-700 { color: #1d4ed8 !important; }

.table-btn {
    min-height: 80px;
    font-size: 1.1rem;
    font-weight: bold;
    border-radius: 15px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    margin: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.table-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(0,0,0,0.15);
}

.table-btn.selected {
    border: 3px solid #1e40af;
    box-shadow: 0 0 0 2px rgba(30, 64, 175, 0.2);
}

.summary-card {
    border-radius: 15px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    border: none;
}

.action-btn {
    border-radius: 10px;
    font-weight: 600;
    padding: 12px 20px;
    transition: all 0.3s ease;
}

.action-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.page-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 15px;
    padding: 20px;
    margin-bottom: 30px;
    text-align: center;
}

.tables-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 15px;
    padding: 20px;
}
</style>

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
        $items_query = "SELECT fd.*, i.iname, i.price1 as sprice,
                       (fd.qty_out - fd.qty_in) as actual_qty
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

<div class="container-fluid py-4">
    <!-- Header -->
    <div class="page-header">
        <h2 class="mb-0">
            <i class="fas fa-utensils me-3"></i>إدارة الطاولات
        </h2>
        <p class="mb-0 mt-2 opacity-75">اختر طاولة لبدء الطلب أو إدارة الطلبات الحالية</p>
    </div>

    <div class="row">
        <!-- Tables Grid -->
        <div class="col-lg-8">
            <div class="card summary-card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-table me-2 text-primary"></i>الطاولات
                    </h5>
                </div>
                <div class="card-body" style="max-height: 70vh; overflow-y: auto;">
                    <div class="tables-grid">
                        <?php 
                        if ($tables_result && $tables_result->num_rows > 0) {
                            while ($table = $tables_result->fetch_assoc()) {
                                $table_id = $table['id'];
                                $table_name = $table['tname'];
                                $table_case = $table['table_case'];
                                
                                $bg_color = ($table_case == 0) ? 'bg-success' : 'bg-danger';
                                $icon = ($table_case == 0) ? 'fas fa-check-circle' : 'fas fa-clock';
                                $status = ($table_case == 0) ? 'فارغة' : 'محجوزة';
                                $selected_class = ($selected_table == $table_id) ? 'selected' : '';
                                
                                echo '<a href="tables.php?table_id=' . $table_id . '" class="btn ' . $bg_color . ' table-btn text-white text-decoration-none ' . $selected_class . '">';
                                echo '<div class="text-center">';
                                echo '<i class="' . $icon . ' fa-lg mb-2"></i><br>';
                                echo '<strong>' . htmlspecialchars($table_name) . '</strong><br>';
                                echo '<small>' . $status . '</small>';
                                echo '</div>';
                                echo '</a>';
                            }
                        } else {
                            echo '<div class="col-12 text-center text-muted p-4">';
                            echo '<i class="fas fa-table fa-3x mb-3"></i><br>';
                            echo 'لا توجد طاولات متاحة';
                            echo '</div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="col-lg-4" style="max-height: 70vh; overflow-y: auto;">
            <?php if ($selected_table): ?>
                <div class="card summary-card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-receipt me-2"></i><?= htmlspecialchars($selected_table_name) ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="border-end">
                                    <h4 class="text-primary mb-1"><?= number_format($order_totals['total'], 2) ?></h4>
                                    <small class="text-muted">الإجمالي</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <h4 class="text-success mb-1"><?= number_format($order_totals['net'], 2) ?></h4>
                                <small class="text-muted">الصافي</small>
                            </div>
                        </div>
                        <hr>
                        <div class="d-grid gap-2">
                            <?php if (!empty($order_data)): ?>
                                <button class="btn btn-success action-btn" onclick="processTablePayment(<?= $selected_table ?>)">
                                    <i class="fas fa-money-bill-wave me-2"></i>سداد نقدي
                                </button>
                                <button class="btn btn-info action-btn" onclick="printInvoice(<?= $selected_table ?>)">
                                    <i class="fas fa-print me-2"></i>طباعة الفاتورة
                                </button>
                                <button class="btn btn-outline-success action-btn" onclick="clearTableNormal(<?= $selected_table ?>)">
                                    <i class="fas fa-broom me-2"></i>تفريغ عادي
                                </button>
                                <button class="btn btn-warning action-btn" onclick="clearTableDirect(<?= $selected_table ?>)">
                                    <i class="fas fa-times me-2"></i>تفريغ مباشر
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="card summary-card">
                    <div class="card-body text-center">
                        <i class="fas fa-hand-pointer fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">اختر طاولة</h5>
                        <p class="text-muted">اضغط على أي طاولة لبدء العمل</p>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($order_items)): ?>
                <div class="card summary-card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="fas fa-list me-2 text-primary"></i>أصناف الطلب
                        </h6>
                    </div>
                    <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                        <?php foreach ($order_items as $item): ?>
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                <div>
                                    <strong><?= htmlspecialchars($item['iname'] ?? 'غير محدد') ?></strong><br>
                                    <small class="text-muted">الكمية: <?= number_format($item['actual_qty'] ?? 0, 2) ?></small>
                                </div>
                                <div class="text-end">
                                    <strong class="text-primary"><?= number_format(($item['actual_qty'] ?? 0) * ($item['price'] ?? 0), 2) ?> ج.م</strong>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>


<!-- زر عائم للذهاب للـ POS -->
<a href="pos_barcode.php" class="floating-pos-btn" title="POS الكاشير">
    <i class="fas fa-cash-register"></i>
</a>

<script>
function processTablePayment(tableId) {
    // جلب بيانات الطاولة والمبلغ المطلوب
    $.ajax({
        url: 'ajax/get_table_amount.php',
        method: 'POST',
        data: { table_id: tableId },
        dataType: 'json',
        success: function(data) {
            console.log('بيانات الطاولة:', data);
            if (data.success) {
                $('#currentTableId').val(tableId);
                $('#modal_total').text(data.total.toFixed(2) + ' ج.م');
                $('#modal_discount').val(data.discount || 0);
                const net = data.total - (data.discount || 0);
                $('#modal_net').text(net.toFixed(2) + ' ج.م');
                $('#posPaymentModal').modal('show');
            } else {
                alert('خطأ في جلب بيانات الطاولة: ' + (data.message || 'خطأ غير معروف'));
            }
        },
        error: function() {
            alert('خطأ في الاتصال بالخادم');
        }
    });
}

function clearTable(tableId) {
    processTablePayment(tableId);
}

function activateTable(tableId) {
    $.ajax({
        url: 'ajax/update_table_status.php',
        method: 'POST',
        data: { table_id: tableId, action: 'activate' },
        success: function(response) {
            alert('تم تشغيل الطاولة');
            location.reload();
        },
        error: function() {
            alert('حدث خطأ');
        }
    });
}

function printPreparation(tableId) {
    window.open('print/preparation.php?table_id=' + tableId, '_blank');
}

function printInvoice(tableId) {
    window.open('print/table_invoice.php?table_id=' + tableId, '_blank');
}

function clearTableDirect(tableId) {
    if (confirm('هل أنت متأكد من تفريغ الطاولة رقم ' + tableId + '؟')) {
        $.ajax({
            url: 'ajax/clear_table.php',
            method: 'POST',
            data: { table_id: tableId },
            success: function(response) {
                alert('تم تفريغ الطاولة بنجاح');
                location.reload();
            },
            error: function() {
                alert('حدث خطأ في تفريغ الطاولة');
            }
        });
    }
}
</script>

<!-- مودال الدفع المتقدم -->
<div class="modal fade" id="posPaymentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-cash-register me-2"></i>الدفع والإجماليات
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="currentTableId">
                <input type="hidden" id="currentOrderId">
                <div class="row g-3">
                    <!-- الإجمالي -->
                    <div class="col-12">
                        <div class="card bg-light">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-4">
                                        <label class="mb-0 fw-bold text-primary">
                                            <i class="fas fa-coins me-2"></i>الإجمالي
                                        </label>
                                    </div>
                                    <div class="col-8">
                                        <h4 class="mb-0 text-primary text-end" id="modal_total">0.00 ج.م</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- الخصم -->
                    <div class="col-12">
                        <div class="card border-primary">
                            <div class="card-header bg-primary bg-opacity-10">
                                <h6 class="mb-0 text-primary">
                                    <i class="fas fa-percentage me-2"></i>الخصم
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <label class="form-label fw-bold">الخصم %</label>
                                        <div class="input-group">
                                            <input class="form-control text-center" 
                                                   type="number" id="modal_discperc" value="0" min="0" max="100" step="0.1">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label fw-bold">قيمة الخصم</label>
                                        <div class="input-group">
                                            <input class="form-control text-center" 
                                                   type="number" id="modal_discount" value="0" step="0.01">
                                            <span class="input-group-text bg-primary text-white">ج.م</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- الصافي -->
                    <div class="col-12">
                        <div class="card bg-success bg-opacity-10 border-success">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-4">
                                        <label class="mb-0 fw-bold text-success">
                                            <i class="fas fa-check-circle me-2"></i>الصافي
                                        </label>
                                    </div>
                                    <div class="col-8">
                                        <h3 class="mb-0 text-success text-end" id="modal_net">0.00 ج.م</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- المدفوع والباقي -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold">
                            <i class="fas fa-money-bill-wave me-2"></i>المدفوع
                        </label>
                        <div class="input-group input-group-lg">
                            <input class="form-control text-center fw-bold" 
                                   type="number" id="modal_paid" value="0.00" step="0.01">
                            <span class="input-group-text">ج.م</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">
                            <i class="fas fa-arrow-left me-2"></i>الباقي
                        </label>
                        <div class="input-group input-group-lg">
                            <input class="form-control text-center fw-bold bg-danger text-white" 
                                   type="text" id="modal_change" value="0.00" readonly>
                            <span class="input-group-text bg-danger text-white">ج.م</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="closeModal()">
                    <i class="fas fa-times me-1"></i>إلغاء
                </button>
                <button type="button" class="btn btn-success" onclick="processAdvancedPayment()" id="paymentConfirmBtn">
                    <i class="fas fa-print me-1"></i>سداد وطباعة
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// حساب الخصم والصافي
$(document).ready(function() {
    $(document).on('input', '#modal_discperc, #modal_discount', function() {
        const total = parseFloat($('#modal_total').text().replace(' ج.م', '')) || 0;
        let discount = 0;
        
        if ($(this).attr('id') === 'modal_discperc') {
            const discPerc = parseFloat($(this).val()) || 0;
            discount = (total * discPerc) / 100;
            $('#modal_discount').val(discount.toFixed(2));
        } else {
            discount = parseFloat($(this).val()) || 0;
            const discPerc = total > 0 ? (discount / total) * 100 : 0;
            $('#modal_discperc').val(discPerc.toFixed(1));
        }
        
        const net = total - discount;
        $('#modal_net').text(net.toFixed(2) + ' ج.م');
        calculateChange();
    });

    // حساب الباقي
    $(document).on('input', '#modal_paid', calculateChange);
});

function calculateChange() {
    const net = parseFloat($('#modal_net').text().replace(' ج.م', '')) || 0;
    const paid = parseFloat($('#modal_paid').val()) || 0;
    const change = paid - net;
    $('#modal_change').val(change.toFixed(2));
    
    // تغيير لون الباقي
    const changeInput = $('#modal_change');
    const changeSpan = changeInput.next('.input-group-text');
    
    if (change >= 0) {
        changeInput.removeClass('bg-danger text-white').addClass('bg-success text-white');
        changeSpan.removeClass('bg-danger text-white').addClass('bg-success text-white');
    } else {
        changeInput.removeClass('bg-success text-white').addClass('bg-danger text-white');
        changeSpan.removeClass('bg-success text-white').addClass('bg-danger text-white');
    }
}

function processAdvancedPayment() {
    console.log('تم استدعاء processAdvancedPayment');
    
    const tableId = $('#currentTableId').val();
    const total = parseFloat($('#modal_total').text().replace(' ج.م', '')) || 0;
    const discount = parseFloat($('#modal_discount').val()) || 0;
    const net = parseFloat($('#modal_net').text().replace(' ج.م', '')) || 0;
    const paid = parseFloat($('#modal_paid').val()) || 0;
    
    console.log('بيانات الدفع:', { tableId, total, discount, net, paid });
    
    if (!tableId) {
        alert('يرجى اختيار طاولة');
        return;
    }
    
    if (paid <= 0) {
        alert('يرجى إدخال مبلغ صحيح');
        return;
    }
    
    $.ajax({
        url: 'ajax/process_table_payment.php',
        method: 'POST',
        data: { 
            table_id: tableId,
            total: total,
            discount: discount,
            net: net,
            paid: paid
        },
        dataType: 'json',
        success: function(data) {
            console.log('استجابة الخادم:', data);
            if (data.success) {
                closeModal();
                const orderId = $('#currentOrderId').val();
                // التحويل مباشرة إلى صفحة الفاتورة
                window.location.href = 'print/receipt.php?id=' + orderId;
            } else {
                alert('حدث خطأ: ' + (data.message || 'خطأ غير محدد'));
            }
        },
        error: function(xhr, status, error) {
            console.error('Ajax error:', {
                status: status,
                error: error,
                responseText: xhr.responseText
            });
            alert('حدث خطأ في الاتصال: ' + error);
        }
    });
}

function processTablePayment(tableId) {
    // جلب بيانات الطاولة والمبلغ المطلوب
    $.ajax({
        url: 'ajax/get_table_amount.php',
        method: 'POST',
        data: { table_id: tableId },
        dataType: 'json',
        success: function(data) {
            console.log('بيانات الطاولة:', data);
            if (data.success) {
                $('#currentTableId').val(tableId);
                $('#currentOrderId').val(data.order_id); // حفظ معرف الطلب
                $('#modal_total').text(data.total.toFixed(2) + ' ج.م');
                $('#modal_discount').val(data.discount || 0);
                const net = data.total - (data.discount || 0);
                $('#modal_net').text(net.toFixed(2) + ' ج.م');
                $('#modal_paid').val(net.toFixed(2));
                $('#modal_discperc').val('0.0');
                
                // حساب الباقي
                calculateChange();
                
                // فتح المودال
                $('#posPaymentModal').modal('show');
            } else {
                alert('خطأ في جلب بيانات الطاولة: ' + (data.message || 'خطأ غير معروف'));
            }
        },
        error: function() {
            alert('خطأ في الاتصال بالخادم');
        }
    });
}

function clearTableNormal(tableId) {
    if(confirm('هل تريد تفريغ الطاولة تفريغ عادي؟\nسيتم حفظ الطلب في النظام وتفريغ الطاولة')) {
        $.ajax({
            url: 'ajax/clear_table_normal.php',
            method: 'POST',
            data: { table_id: tableId, table_name: 'Table ' + tableId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('تم تفريغ الطاولة بنجاح\nإجمالي المبيعات: ' + response.total + ' ج.م');
                    location.reload();
                } else {
                    alert('خطأ: ' + response.message);
                }
            },
            error: function() {
                alert('حدث خطأ في الاتصال بالخادم');
            }
        });
    }
}

function clearTableDirect(tableId) {
    if(confirm('هل تريد تفريغ الطاولة مباشرة بدون سداد؟')) {
        $.ajax({
            url: 'ajax/update_table_status.php',
            method: 'POST',
            data: { table_id: tableId, action: 'clear' },
            success: function(response) {
                alert('تم تفريغ الطاولة بنجاح');
                location.reload();
            }
        });
    }
}

function printPreparation(tableId) {
    window.open('print/preparation.php?table_id=' + tableId, '_blank');
}

function printInvoice(tableId) {
    // جلب معرف الطلب أولاً
    $.ajax({
        url: 'ajax/get_table_amount.php',
        method: 'POST',
        data: { table_id: tableId },
        dataType: 'json',
        success: function(data) {
            if (data.success && data.order_id) {
                window.open('print/receipt.php?id=' + data.order_id, '_blank');
            } else {
                alert('لا يوجد طلب نشط لهذه الطاولة');
            }
        },
        error: function() {
            alert('خطأ في الاتصال بالخادم');
        }
    });
}

function closeModal() {
    $('#posPaymentModal').modal('hide');
}
</script>

<?php include('includes/footer.php') ?>
