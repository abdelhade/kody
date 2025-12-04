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
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                        <h3>الشيفتات المغلقة</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">
    <div class="table-responsive">
        <table class="table table-hover table-striped table-bordered table-sm">
            <thead class="thead-dark">
                <tr>
                    <th>الشيفت</th>
                    <th>التاريخ</th>
                    <th>المستخدم</th>
                    <th>وقت الانهاء</th>
                    <th>اجمالي المبيعات</th>
                    <th>المصاريف</th>
                    <th>بيان المصاريف</th>
                    <th>تسليم الكاش</th>
                    <th>نهاية الدرج</th>
                    <th>ملاحظات</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $x = 0;
                $res_closed = $conn->query("SELECT * FROM closed_orders ORDER BY id DESC");
                while ($rowcl = $res_closed->fetch_assoc()) {
                    $x++;
                ?> 
                <tr>
                    <td><?= $rowcl['shift'] ?></td>
                    <td><?= $rowcl['date'] ?></td>
                    <td class="bg-primary text-white"><?= $rowcl['user'] ?></td>
                    <td><?= $rowcl['endtime'] ?></td>
                    <td class="bg-success text-white"><?= $rowcl['total_sales'] ?></td>
                    <td class="bg-danger text-white"><?= $rowcl['expenses'] ?></td>
                    <td><?= $rowcl['exp_notes'] ?></td>
                    <td class="bg-secondary text-white"><?= $rowcl['cash'] ?></td>
                    <td class="bg-light"><?= $rowcl['fund_after'] ?></td>
                    <td><?= $rowcl['info'] ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

          
            </div>





        </div>
    </section>
</div>




<?php include('includes/footer.php') ?>
