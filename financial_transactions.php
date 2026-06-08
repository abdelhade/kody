<?php 
include('includes/header.php');
include('includes/navbar.php');
include('includes/sidebar.php');
?>
<div class="content-wrapper">
    <section class="content-header">
      <div class="container-fluid">
        <div class="card shadow">
            <div class="card-header bg-gradient-info text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="card-title m-0 font-weight-bold">
                        <i class="fas fa-money-bill-wave me-2"></i> حركات الخصومات والإضافيات المالية للموظفين
                    </h3>
                    <a href="add_financial_transaction.php" class="btn btn-success shadow-sm">
                        <i class="fas fa-plus"></i> إضافة حركة جديدة
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover text-center align-middle" id="financialTable">
                        <thead class="bg-light text-secondary">
                            <tr>
                                <th style="width: 5%;">م</th>
                                <th style="width: 8%;">رقم الحركة</th>
                                <th style="width: 12%;">التاريخ</th>
                                <th>اسم الموظف</th>
                                <th style="width: 10%;">النوع</th>
                                <th style="width: 12%;">المبلغ</th>
                                <th style="width: 20%;">السبب</th>
                                <th style="width: 20%;">ملاحظات</th>
                                <th style="width: 8%;">العمليات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT * FROM `financial_transactions` ORDER BY date DESC, id DESC";
                            $result = $conn->query($sql);
                            $i = 0;
                            while($row = $result->fetch_assoc()){
                                $i ++; 
                                $badgeClass = $row['type'] == 1 ? 'badge-success bg-success-light text-success' : 'badge-danger bg-danger-light text-danger';
                                $typeName = $row['type'] == 1 ? 'إضافي' : 'خصم فلوس';
                                ?>
                            <tr>
                                <td><?= $i ?></td>
                                <td><span class="badge badge-secondary shadow-sm">#<?= $row['snd_id'] ?></span></td>
                                <td><?= $row['date'] ?></td>
                                <td class="font-weight-bold text-dark"><?= htmlspecialchars($row['emp_name']) ?></td>
                                <td>
                                    <span class="badge px-3 py-2 <?= $badgeClass ?>" style="font-size: 0.9rem; border-radius: 20px;">
                                        <?= $typeName ?>
                                    </span>
                                </td>
                                <td class="font-weight-bold <?= $row['type'] == 1 ? 'text-success' : 'text-danger' ?>" style="font-size: 1.1rem;">
                                    <?= $row['type'] == 1 ? '+' : '-' ?><?= number_format($row['amount'], 2) ?> ج.م
                                </td>
                                <td><?= htmlspecialchars($row['reason']) ?></td>
                                <td class="text-muted"><?= htmlspecialchars($row['notes'] ?? '—') ?></td>
                                <td>
                                    <a href="edit_financial_transaction.php?edit=<?= $row['snd_id'] ?>" class="btn btn-sm btn-outline-warning shadow-sm" title="تعديل">
                                        <i class="fa fa-pen"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php }?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
      </div>
    </section>
</div>

<style>
.bg-success-light {
    background-color: rgba(40, 167, 69, 0.15) !important;
}
.bg-danger-light {
    background-color: rgba(220, 53, 69, 0.15) !important;
}
#financialTable th {
    vertical-align: middle;
}
#financialTable td {
    vertical-align: middle;
}
</style>

<script>
$(document).ready(function() {
    if ($.fn.DataTable) {
        $('#financialTable').DataTable({
            paging: true,
            lengthChange: true,
            searching: true,
            ordering: true,
            info: true,
            autoWidth: false,
            responsive: true,
            language: {
                search: 'بحث سريع:',
                lengthMenu: 'عرض _MENU_ حركات',
                info: 'عرض _START_ إلى _END_ من _TOTAL_ حركة',
                paginate: { first: 'الأول', last: 'الأخير', next: 'التالي', previous: 'السابق' },
                zeroRecords: 'لا توجد نتائج',
                emptyTable: 'لا توجد بيانات مسجلة'
            }
        });
    }
});
</script>

<?php include('includes/footer.php');?>
