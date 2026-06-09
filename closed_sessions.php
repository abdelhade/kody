<?php include('includes/header.php') ?>
<?php include('includes/navbar.php') ?>
<?php include('includes/sidebar.php') ?>

<!-- إضافة CSS التحسينات -->
<link href="dist/css/shift_notifications.css" rel="stylesheet">

<div class="content-wrapper">

<!-- عرض رسالة إغلاق الشيفت -->
<?php if (isset($_SESSION['success_message'])): ?>
<div class="container-fluid mt-3">
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        <strong><?= htmlspecialchars($_SESSION['success_message']) ?></strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
</div>
<?php 
    unset($_SESSION['success_message']);
endif; 
?>

<!-- عرض رسائل الخطأ -->
<?php if (isset($_SESSION['error_message'])): ?>
<div class="container-fluid mt-3">
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle"></i> <?= $_SESSION['error_message'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
</div>
<?php 
    unset($_SESSION['error_message']);
endif; 
?>
  <!-- Content Header (Page header) -->
  <section class="content-header">
        <div class="container-fluid">
            <div class="card shadow-sm border-0" style="border-radius: 15px;">
                <div class="card-header bg-white border-0 py-3">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="card-title text-dark fw-bold mb-0" style="font-size: 1.5rem; font-weight: 700;">
                                <i class="fas fa-history text-primary me-2"></i>
                                الشيفتات المغلقة
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-hover table-striped mb-0 align-middle text-center" style="font-size: 0.9rem;">
            <thead class="bg-dark text-white" style="background-color: #1a202c !important;">
                <tr>
                    <th class="py-3 border-0">الشيفت</th>
                    <th class="py-3 border-0">التاريخ</th>
                    <th class="py-3 border-0">المستخدم</th>
                    <th class="py-3 border-0">وقت الانهاء</th>
                    <th class="py-3 border-0">اجمالي المبيعات</th>
                    <th class="py-3 border-0">المصاريف</th>
                    <th class="py-3 border-0">بيان المصاريف</th>
                    <th class="py-3 border-0">تسليم الكاش</th>
                    <th class="py-3 border-0">نهاية الدرج</th>
                    <th class="py-3 border-0">ملاحظات</th>
                    <th class="py-3 border-0">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                // إعدادات الترقيم
                $records_per_page = 20;
                $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                $page = max(1, $page);
                $offset = ($page - 1) * $records_per_page;
                
                // حساب إجمالي السجلات
                $total_count = $conn->query("SELECT COUNT(*) as count FROM closed_orders")->fetch_assoc()['count'];
                $total_pages = ceil($total_count / $records_per_page);
                
                // جلب السجلات للصفحة الحالية
                $x = $total_count - $offset;
                $res_closed = $conn->query("SELECT * FROM closed_orders ORDER BY id DESC LIMIT $offset, $records_per_page");
                
                while ($rowcl = $res_closed->fetch_assoc()) {
                ?> 
                <tr class="align-middle">
                    <td class="fw-bold text-muted"><?= $x ?></td>
                    <td>
                        <span class="badge badge-light px-2 py-1" style="font-size: 0.85rem;"><i class="far fa-calendar-alt text-secondary me-1"></i><?= $rowcl['date'] ?></span>
                    </td>
                    <td>
                        <span class="badge badge-pill badge-primary px-3 py-1 font-weight-bold" style="font-size: 0.85rem; letter-spacing: 0.5px; box-shadow: 0 2px 4px rgba(0, 123, 255, 0.2);"><i class="fas fa-user-circle me-1"></i><?= htmlspecialchars($rowcl['user']) ?></span>
                    </td>
                    <td>
                        <span class="badge badge-light px-2 py-1" style="font-size: 0.85rem;"><i class="far fa-clock text-secondary me-1"></i><?= $rowcl['endtime'] ?></span>
                    </td>
                    <td>
                        <span class="badge badge-success px-3 py-1.5 font-weight-bold" style="font-size: 0.9rem; border-radius: 6px; box-shadow: 0 2px 4px rgba(40, 167, 69, 0.2);"><?= number_format($rowcl['total_sales'], 2) ?> ج.م</span>
                    </td>
                    <td>
                        <?php if ($rowcl['expenses'] > 0): ?>
                            <span class="badge badge-danger px-3 py-1.5 font-weight-bold" style="font-size: 0.9rem; border-radius: 6px; box-shadow: 0 2px 4px rgba(220, 53, 69, 0.2);"><?= number_format($rowcl['expenses'], 2) ?> ج.م</span>
                        <?php else: ?>
                            <span class="text-muted">-</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-wrap" style="max-width: 150px;"><?= htmlspecialchars($rowcl['exp_notes'] ?? '') ?: '<span class="text-muted">-</span>' ?></td>
                    <td>
                        <span class="badge badge-info px-3 py-1.5 font-weight-bold" style="font-size: 0.9rem; border-radius: 6px; box-shadow: 0 2px 4px rgba(23, 162, 184, 0.2);"><?= number_format($rowcl['cash'], 2) ?> ج.م</span>
                    </td>
                    <td>
                        <span class="badge badge-secondary px-3 py-1.5 font-weight-bold" style="font-size: 0.9rem; border-radius: 6px; box-shadow: 0 2px 4px rgba(108, 117, 125, 0.15);"><?= number_format($rowcl['fund_after'], 2) ?> ج.م</span>
                    </td>
                    <td class="text-wrap" style="max-width: 180px;"><?= htmlspecialchars($rowcl['info'] ?? '') ?: '<span class="text-muted">-</span>' ?></td>
                    <td class="text-center">
                        <div class="btn-group">
                            <a href="print/closed_session_receipt.php?id=<?= $rowcl['id'] ?>" class="btn btn-sm btn-outline-info me-1" target="_blank" title="طباعة ملخص الشيفت" style="border-radius: 6px;">
                                <i class="fas fa-print"></i> ملخص
                            </a>
                            <a href="print/closed_session_items.php?id=<?= $rowcl['id'] ?>" class="btn btn-sm btn-outline-warning" target="_blank" title="طباعة الأصناف المباعة" style="border-radius: 6px;">
                                <i class="fas fa-list"></i> الأصناف
                            </a>
                        </div>
                    </td>
                </tr>
                <?php $x--; } ?>
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
    <div class="card-footer">
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center mb-0">
                <!-- زر الصفحة السابقة -->
                <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $page - 1 ?>" aria-label="السابق">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
                
                <?php
                // عرض أرقام الصفحات
                $start_page = max(1, $page - 2);
                $end_page = min($total_pages, $page + 2);
                
                if ($start_page > 1) {
                    echo '<li class="page-item"><a class="page-link" href="?page=1">1</a></li>';
                    if ($start_page > 2) {
                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                    }
                }
                
                for ($i = $start_page; $i <= $end_page; $i++) {
                    $active = ($i == $page) ? 'active' : '';
                    echo '<li class="page-item ' . $active . '"><a class="page-link" href="?page=' . $i . '">' . $i . '</a></li>';
                }
                
                if ($end_page < $total_pages) {
                    if ($end_page < $total_pages - 1) {
                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                    }
                    echo '<li class="page-item"><a class="page-link" href="?page=' . $total_pages . '">' . $total_pages . '</a></li>';
                }
                ?>
                
                <!-- زر الصفحة التالية -->
                <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $page + 1 ?>" aria-label="التالي">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
        <div class="text-center mt-2">
            <small class="text-muted">
                الصفحة <?= $page ?> من <?= $total_pages ?> (إجمالي <?= $total_count ?> شيفت)
            </small>
        </div>
    </div>
    <?php endif; ?>
</div>

          
            </div>





        </div>
    </section>
</div>




<?php include('includes/footer.php') ?>
