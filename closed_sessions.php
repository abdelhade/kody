<?php include('includes/header.php') ?>
<?php include('includes/navbar.php') ?>
<?php include('includes/sidebar.php') ?>

<div class="content-wrapper">
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
