<?php 
include('includes/header.php'); 
include('includes/navbar.php'); 
include('includes/sidebar.php'); 


$q = isset($_GET['q']) ? $_GET['q'] : "all";  // استقبال قيمة q من GET
$strtdate = isset($_POST['strtdate']) ? $_POST['strtdate'] : null;
$enddate = isset($_POST['enddate']) ? $_POST['enddate'] : null;

$dateFilter = "";
if ($strtdate && $enddate) {
    $dateFilter = "AND pro_date BETWEEN '$strtdate' AND '$enddate'";
} else {
    $dateFilter = "AND pro_date = '$today'";
}

switch ($q) {
    case "sale":
        $report_name = "مشتريات";
        $resop = $conn->query("SELECT * FROM ot_head WHERE pro_tybe = 4 AND isdeleted != 1 $dateFilter ORDER BY id DESC");
        break;
    case "buy":
        $report_name = "مبيعات";
        $resop = $conn->query("SELECT * FROM ot_head WHERE (pro_tybe = 2 OR pro_tybe = 3 OR pro_tybe = 9) AND isdeleted != 1 $dateFilter ORDER BY id DESC");
        break;
    default:
        $report_name = "التقرير الشامل";
        $resop = $conn->query("SELECT * FROM ot_head WHERE isdeleted != 1 $dateFilter ORDER BY id DESC");
}
?>


<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3> - محلل العمل اليومي <?= $report_name ?></h3>
              
                    <form action="" method="post">
                    <?php
                        $strtdate = isset($_POST['strtdate']) ? $_POST['strtdate'] : date("Y-m-d");
                        $enddate = isset($_POST['enddate']) ? $_POST['enddate'] : date("Y-m-d");
                        ?>

                        <div class="row">
                            <div class="col-md-4 col-sm-6 col-12 mb-2">
                                <label>من</label>
                                <input class="form-control" type="date" value="<?= $strtdate ?>" name="strtdate">
                            </div>
                            <div class="col-md-4 col-sm-6 col-12 mb-2">
                                <label>إلى</label>
                                <input class="form-control" type="date" value="<?= $enddate ?>" name="enddate">
                            </div>
                            <div class="col-md-4 col-12 mb-2">
                                <label class="d-none d-md-block">&nbsp;</label>
                                <button class="btn btn-primary btn-block" type="submit">
                                    <i class="fa fa-search"></i> بحث
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-sm" id="" data-page-length="10">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>الوقت و التاريخ</th>
                                    <th>اسم العملية</th>
                                    <th>قيمة العملية</th>
                                    <th>صافي العملية</th>
                                    <th>الحساب</th>
                                    <th>الحساب المقابل</th>
                                    <th>المخزن</th>
                                    <th>الموظف</th>
                                    <th>الربح</th>
                                    <th>المستخدم</th>
                                    <th>معرف</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $x = 0;
                                while ($rowop = $resop->fetch_assoc()) {
                                    $x++;
                                    $proid = $rowop['id'];
                                    $tybe = $rowop['pro_tybe'];
                                    ?>
                                    <tr>
                                        <td><?= $x ?></td>
                                        <td><?= $rowop['crtime'] ?></td>
                                        <td>
                                            <a class="btn btn-block btn-light border" href="print/<?= ($tybe == 4 || $tybe == 3) ? 'print_sales' : 'receipt' ?>.php?id=<?= $proid ?>" target="_blank">
                                                <?= $conn->query("SELECT pname FROM pro_tybes WHERE id = $tybe")->fetch_assoc()['pname'] ?>
                                            </a>
                                        </td>
                                         <td class="value"><?= $rowop['pro_value'] ?></td>
                                        <td class="fatnet <?php if($rowop['pro_value'] != $rowop['fat_net']){echo "bg-yellow-300";} ?>"><?= $rowop['fat_net'] ?></td>
                                        <td><?= $conn->query("SELECT aname FROM acc_head WHERE id = {$rowop['acc1']}")->fetch_assoc()['aname'] ?></td>
                                        <td><?= $conn->query("SELECT aname FROM acc_head WHERE id = {$rowop['acc2']}")->fetch_assoc()['aname'] ?></td>
                                        <td><?= $rowop['store_id'] > 0 ? $conn->query("SELECT aname FROM acc_head WHERE id = {$rowop['store_id']}")->fetch_assoc()['aname'] : '' ?></td>
                                        <td><?= $conn->query("SELECT aname FROM acc_head WHERE id = {$rowop['emp_id']}")->fetch_assoc()['aname'] ?></td>
                                         <td class="prft"><?= $rowop['profit'] ?></td>
                                        <td><?= $conn->query("SELECT uname FROM users WHERE id = {$rowop['user']}")->fetch_assoc()['uname'] ?></td>
                                        <td>
                                            <?= $rowop['id'] ?>
                                            <a href="inv_operations.php?h=<?= md5($proid) ?>&q=<?= $proid ?>&t=<?= md5($tybe) ?>">
                                                <i class="fa fa-barcode"></i>
                                            </a>
                                            <?php $proid = $rowop['id']?>
                                            
                                            <!-- زر التعديل -->
                                            <?php if(in_array($tybe, [3, 4, 9])) { // مبيعات، مشتريات، كاشير ?>
                                            <a href="sales.php?edit_id=<?= $rowop['id'] ?>" class="btn btn-sm btn-warning" title="تعديل">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <?php } ?>

                                            <!-- زر الحذف -->
                                            <a href="#" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteModal<?= $rowop['id']?>" data-id="<?= $id; ?>">
                                                <i class="fa fa-trash"></i>
                                            </a>

                                            <form action="do/dodel_invoice.php?id=<?= $rowop['id'] ?>" method="post">
                                                <input type="hidden" name="q" value="<?= $q ?>">
                                            
                                            <div class="modal fade" id="deleteModal<?= $rowop['id']?>" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="deleteModalLabel">تأكيد الحذف <?= $rowop['id']?></h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="إغلاق">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>هل أنت متأكد من أنك تريد حذف هذه الفاتورة؟</p>
                                                            <p><strong>رقم الفاتورة:</strong> <?= $rowop['id'] ?></p>
                                                            <p><strong>نوع العملية:</strong> <?= $conn->query("SELECT pname FROM pro_tybes WHERE id = $tybe")->fetch_assoc()['pname'] ?></p>
                                                            <label for="pass">كلمة المرور:</label>
                                                        </div>
                                                        <input type="password" name="pass" class="form-control hover:bg-orange-300" placeholder="أدخل كلمة مرور الحذف" required>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                                                            <button type="submit" class="btn btn-danger">حذف</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            </form>

                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>
                            </tbody>
                        </table>
                        <table>
                        <tbody>
                                <tr>
                                    <td> اجمالي </td>
                                    <td class="bg-zinc-100" id="total"></td>
                                    <td> _       _ </td>
                                    <td></td>
                                    <td> صافي </td>
                                    <td class="" id="fatnet"></td>
                                    <td> _       _ </td>

                                    <td> ارباح </td>
                                    <td class="" id="profit"></td>
                                    <td></td>
                                </tr>
                            </tbody>
                       
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const total = Array.from(document.querySelectorAll(".value")).reduce((sum, el) => sum + parseFloat(el.textContent || 0), 0);
        const fatnet = Array.from(document.querySelectorAll(".fatnet")).reduce((sum, el) => sum + parseFloat(el.textContent || 0), 0);
        const profit = Array.from(document.querySelectorAll(".prft")).reduce((sum, el) => sum + parseFloat(el.textContent || 0), 0);
        document.getElementById("total").textContent = total.toFixed(2);
        document.getElementById("fatnet").textContent = fatnet.toFixed(2);
        document.getElementById("profit").textContent = profit.toFixed(2);
    });
</script>
<?php include('includes/footer.php'); ?>
