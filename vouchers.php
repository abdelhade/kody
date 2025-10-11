<?php
include('includes/header.php');
include('includes/navbar.php');
include('includes/sidebar.php');

// استعلام لتحميل البيانات باستخدام JOIN
if (isset($_GET['t'])){
    if ($_GET['t'] == 'recive') {
    $resvoucher = $conn->query("
    SELECT h.id ,h.pro_date, h.pro_tybe, h.pro_value, h.pro_id, 
           acc1.aname AS acc1_name, acc2.aname AS acc2_name
    FROM ot_head h
    JOIN acc_head acc1 ON h.acc1 = acc1.id
    JOIN acc_head acc2 ON h.acc2 = acc2.id
    where pro_tybe = 1
");
}elseif($_GET['t'] == 'payment'){
    $resvoucher = $conn->query("
    SELECT h.id ,h.pro_date, h.pro_tybe, h.pro_value, h.pro_id, 
           acc1.aname AS acc1_name, acc2.aname AS acc2_name
    FROM ot_head h
    JOIN acc_head acc1 ON h.acc1 = acc1.id
    JOIN acc_head acc2 ON h.acc2 = acc2.id
    where pro_tybe = 2 
");
}}

else{
$resvoucher = $conn->query("
    SELECT h.id ,h.pro_date, h.pro_tybe, h.pro_value, h.pro_id, 
           acc1.aname AS acc1_name, acc2.aname AS acc2_name
    FROM ot_head h
    JOIN acc_head acc1 ON h.acc1 = acc1.id
    JOIN acc_head acc2 ON h.acc2 = acc2.id
");}

if (isset($_POST['tybe'])) {
    $conditions = []; // مصفوفة لتجميع الشروط

    // التحقق من القيم المدخلة
    if (!empty($_POST['tybe'])) {
        $t1 = $conn->real_escape_string($_POST['tybe']);
        $conditions[] = "h.pro_tybe = '$t1'";
    }

    if (!empty($_POST['strt'])) {
        $t2 = $conn->real_escape_string($_POST['strt']);
        $conditions[] = "h.pro_date >= '$t2'";
    }

    if (!empty($_POST['end'])) {
        $t3 = $conn->real_escape_string($_POST['end']);
        $conditions[] = "h.pro_date <= '$t3'";
    }

    // بناء شروط WHERE
    $whereClause = "";
    if (!empty($conditions)) {
        $whereClause = " AND " . implode(" AND ", $conditions);
    }

    // الاستعلام النهائي
    $sqlserch = "
        SELECT h.id, h.pro_date, h.pro_tybe, h.pro_value, h.pro_id, 
               acc1.aname AS acc1_name, acc2.aname AS acc2_name
        FROM ot_head h
        JOIN acc_head acc1 ON h.acc1 = acc1.id
        JOIN acc_head acc2 ON h.acc2 = acc2.id
        WHERE h.id > 1 $whereClause
    ";

    // تنفيذ الاستعلام
    $resvoucher = $conn->query($sqlserch);

    if ($resvoucher === false) {
        echo "Error: " . $conn->error; // تصحيح أي أخطاء في الاستعلام
    }
}

?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                                <form action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
                    <div class="row">
                        <div class="col">
                            <h1>السندات</h1>
                        </div>
                        <div class="col-lg-2">
                        </div>
                        <div class="col-lg-2">
                            <a href="add_voucher.php?t=recive" class="btn btn-primary">سند قبض</a>
                            <a href="add_voucher.php?t=payment" class="btn btn-primary">سند دفع</a>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col">
                            <?php
                            // استرجاع القيم الافتراضية
                            $tybe = isset($_POST['tybe']) ? $_POST['tybe'] :'';
                            $strt = isset($_POST['strt']) ? $_POST['strt'] :'';
                            $end  = isset($_POST['end'])  ? $_POST['end']  :'';
                            ?>
                            <select class="" name="tybe">
                                <option value="">كل السندات</option>
                                <option value="1" <?= $tybe == '1' ? 'selected' : '' ?>>سندات قبض</option>
                                <option value="2" <?= $tybe == '2' ? 'selected' : '' ?>>سندات دفع</option>
                            </select>
                            
                                <label for="">من</label>
                                <input class="" type="date" name="strt" value="<?= $strt ?>">
                           
                            
                                <label for="">إلى</label>
                                <input class="" type="date" name="end" value="<?= $end ?>">
                           
                            <button class="btn btn-success">بحث</button>
                        </div>
                    </div>
                </form>

                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="myTable" class="table table-striped table-bordered" data-page-length='50'>
                            <thead>
                                <tr>
                                    <th>م</th>
                                    <th>التاريخ</th>
                                    <th>النوع</th>
                                    <th>قيمه السند</th>
                                    <th>الحساب الاساسي</th>
                                    <th>الحساب المقابل</th>
                                    <th>رقم السند</th>
                                    <th>عمليات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $x = 0;
                                while ($rowvoucher = $resvoucher->fetch_assoc()) {
                                    $x++;
                                    ?>
                                    <tr>
                                        <td><?= $x ?></td>
                                        <td><?= $rowvoucher['pro_date'] ?></td>
                                        <td><?= $rowvoucher['pro_tybe'] == 1 ? "سند قبض" : "سند دفع" ?></td>
                                        <td><?= $rowvoucher['pro_value'] ?></td>
                                        <td><?= $rowvoucher['acc1_name'] ?></td>
                                        <td><?= $rowvoucher['acc2_name'] ?></td>
                                        <td><?= $rowvoucher['pro_id'] ?></td>
                                        <td>
                                            <a href="add_voucher.php?edit=<?= $rowvoucher['id'] ?>&t=<?php 
                                            if($rowvoucher['pro_tybe'] == 1) echo "recive";
                                            elseif ($rowvoucher['pro_tybe'] == 2) {echo "payment";}
                                            ?>" class="btn btn-warning">
                                            <i class="fa fa-edit"></i>
                                            </a>
                                          
                                            <button type="button" class="btn btn-flat btn-sm btn-danger" data-toggle="modal" data-target="#deleteModal<?= $rowvoucher['id'] ?>">
                                            <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>

                                    

                                    <!-- Delete Confirmation Modal -->
                                    <div class="modal fade" id="deleteModal<?= $rowvoucher['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteModalLabel">تأكيد الحذف</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    هل أنت متأكد أنك تريد حذف هذا السند؟
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                                                    <a id="confirmDelete" href="do/dodel_voucher.php?del=<?= $rowvoucher['id'] ?>" class="btn btn-danger">حذف</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>








<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal<?= $rowvoucher['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">تأكيد الحذف</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                هل أنت متأكد أنك تريد حذف هذا السند؟
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                <a id="confirmDelete" href="" class="btn btn-danger">حذف</a>
            </div>
        </div>
    </div>
</div>
<?php include('includes/footer.php'); ?>
