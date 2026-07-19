<?php 
include('includes/connect.php'); 
include('includes/header.php'); 
?>
<link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<?php include('includes/navbar.php'); ?>
<?php include('includes/sidebar.php'); ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">📦 تقرير مردود المشتريات</h3>
                </div>
                <div class="card-body">
                    <!-- نموذج الفلترة -->
                    <form method="GET" class="row mb-4">
                        <div class="col-md-3">
                            <label>من تاريخ:</label>
                            <input type="date" name="from" class="form-control" value="<?= $_GET['from'] ?? '' ?>">
                        </div>
                        <div class="col-md-3">
                            <label>إلى تاريخ:</label>
                            <input type="date" name="to" class="form-control" value="<?= $_GET['to'] ?? '' ?>">
                        </div>
                        <div class="col-md-3">
                            <label>المورد:</label>
                            <select name="supplier" class="form-control">
                                <option value="">الكل</option>
                                <?php
                                $suppliers = $conn->query("SELECT id, aname FROM acc_head WHERE parent_id = 33 AND isdeleted = 0 ORDER BY aname");
                                while($sup = $suppliers->fetch_assoc()):
                                ?>
                                <option value="<?= $sup['id'] ?>" <?= (isset($_GET['supplier']) && $_GET['supplier'] == $sup['id']) ? 'selected' : '' ?>>
                                    <?= $sup['aname'] ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>بحث برقم الفاتورة:</label>
                            <input type="text" name="invoice_no" class="form-control" placeholder="رقم الفاتورة" value="<?= $_GET['invoice_no'] ?? '' ?>">
                        </div>
                        <div class="col-md-2">
                            <label style="visibility: hidden;">عرض</label>
                            <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-search"></i> بحث</button>
                        </div>
                        <div class="col-md-2">
                            <label style="visibility: hidden;">طباعة</label>
                            <button type="button" onclick="window.print()" class="btn btn-success btn-block"><i class="fas fa-print"></i> طباعة</button>
                        </div>
                    </form>

                    <!-- جدول البيانات -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="purchaseReturnsTable">
                            <thead class="bg-dark text-white">
                                <tr>
                                    <th>#</th>
                                    <th>رقم الفاتورة</th>
                                    <th>التاريخ</th>
                                    <th>المورد</th>
                                    <th>الصنف</th>
                                    <th>الكمية الراجعة</th>
                                    <th>سعر الوحدة</th>
                                    <th>الإجمالي</th>
                                    <th>الملاحظات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $limit = 100;
                                $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
                                $offset = ($page - 1) * $limit;

                                $from = $_GET['from'] ?? null;
                                $to = $_GET['to'] ?? null;
                                $supplier = $_GET['supplier'] ?? null;
                                $invoice_no = $_GET['invoice_no'] ?? null;

                                $dateFilter = "";
                                if ($from && $to) {
                                    $dateFilter = "AND h.pro_date BETWEEN '$from' AND '$to'";
                                } elseif ($from) {
                                    $dateFilter = "AND h.pro_date >= '$from'";
                             } elseif ($to) {
                                    $dateFilter = "AND h.pro_date <= '$to'";
                                }

                                $supplierFilter = "";
                                if ($supplier) {
                                    $supplierFilter = "AND h.acc2 = $supplier";
                                }

                                $invoiceFilter = "";
                                if ($invoice_no) {
                                    $invoiceFilter = "AND h.pro_id LIKE '%$invoice_no%'";
                                }

                                // نوع فاتورة مردود المشتريات = 10
                                $query = "
                                    SELECT 
                                        h.id,
                                        h.pro_id,
                                        h.pro_date,
                                        h.acc2 AS supplier_id,
                                        s.aname as supplier_name,
                                        d.item_id,
                                        i.iname as item_name,
                                        i.code as item_code,
                                        d.qty_in as returned_qty,
                                        d.price as unit_price,
                                        d.det_value as total_value,
                                        h.info as notes
                                    FROM ot_head h
                                    LEFT JOIN acc_head s ON h.acc2 = s.id
                                    LEFT JOIN fat_details d ON h.id = d.fatid AND d.isdeleted = 0
                                    LEFT JOIN myitems i ON d.item_id = i.id
                                    WHERE h.pro_tybe = 10 
                                        AND h.isdeleted = 0
                                        $dateFilter
                                        $supplierFilter
                                        $invoiceFilter
                                    ORDER BY h.id DESC, d.id ASC
                                    LIMIT $limit OFFSET $offset
                                ";

                                $result = $conn->query($query);
                                $x = $offset;
                                $grand_total = 0;

                                if ($result && $result->num_rows > 0):
                                    while ($row = $result->fetch_assoc()):
                                        $x++;
                                        $grand_total += $row['total_value'];
                                ?>
                                <tr>
                                    <td><?= $x ?></td>
                                    <td><?= htmlspecialchars($row['pro_id']) ?></td>
                                    <td><?= htmlspecialchars($row['pro_date']) ?></td>
                                    <td><?= htmlspecialchars($row['supplier_name']) ?></td>
                                    <td>
                                        <?= htmlspecialchars($row['item_code']) ?> - 
                                        <?= htmlspecialchars($row['item_name']) ?>
                                    </td>
                                    <td class="text-center font-weight-bold text-danger">
                                        <?= number_format($row['returned_qty'], 2) ?>
                                    </td>
                                    <td class="text-center">
                                        <?= number_format($row['unit_price'], 2) ?>
                                    </td>
                                    <td class="text-center font-weight-bold">
                                        <?= number_format($row['total_value'], 2) ?>
                                    </td>
                                    <td><?= htmlspecialchars($row['notes']) ?></td>
                                </tr>
                                <?php 
                                    endwhile;
                                else:
                                ?>
                                <tr>
                                    <td colspan="9" class="text-center text-muted">لا توجد بيانات</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <td colspan="7" class="text-right font-weight-bold">الإجمالي الكلي:</td>
                                    <td class="text-center font-weight-bold text-primary">
                                        <?= number_format($grand_total, 2) ?>
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if (isset($total_pages) && $total_pages > 1): ?>
                    <nav>
                        <ul class="pagination justify-content-center">
                            <?php for($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>&from=<?= $from ?? '' ?>&to=<?= $to ?? '' ?>&supplier=<?= $supplier ?? '' ?>&invoice_no=<?= $invoice_no ?? '' ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include('includes/footer.php'); ?>

<script>
$(document).ready(function() {
    $('#purchaseReturnsTable').DataTable({
        'order': [[1, 'desc']],
        'language': {
            'url': '//cdn.datatables.net/plug-ins/1.13.6/i18n/ar.json'
        },
        'pageLength': 25
    });
});
</script>

<style>
@media print {
    .sidebar, .navbar, .card-header button, .no-print {
        display: none !important;
    }
    .content-wrapper {
        margin-left: 0 !important;
    }
    .card {
        box-shadow: none !important;
        border: 1px solid #ddd !important;
    }
}
</style>
