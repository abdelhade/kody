<?php include('includes/header.php') ?>
<?php include('includes/navbar.php') ?>
<?php include('includes/sidebar.php') ?>
<?php
$a01 = "";$b1="";
if (isset($_GET)) {
    if (isset($_GET['acc'])) {  
        if ($_GET['acc'] == "clients") {
            $a01 = " AND code LIKE '122%' ";
            $code = 122;
            $b1 = "?parent_id=122"; 
        }
        if ($_GET['acc'] == "suppliers") {
            $a01 = " AND code LIKE '211%' ";
            $code = 211;
            $b1 = "?parent_id=211";
        }
        if ($_GET['acc'] == "funds") {
            $a01 = " AND code LIKE '121%' ";
            $code = 121;
            $b1 = "?parent_id=121";
        }
        if ($_GET['acc'] == "banks") {
            $a01 = " AND code LIKE '124%' ";
            $code = 124;
            $b1 = "?parent_id=124";
        }
        if ($_GET['acc'] == "expenses") {
            $a01 = " AND code LIKE '44%' ";
            $code = 44;
            $b1 = "?parent_id=44";
        }
        if ($_GET['acc'] == "revenous") {
            $a01 = " AND code LIKE '32%' ";
            $code = 32;
            $b1 = "?parent_id=32";
        }
        if ($_GET['acc'] == "creditors") {
            $a01 = " AND code LIKE '212%' ";
            $code = 212;
            $b1 = "?parent_id=212";
        }
        if ($_GET['acc'] == "depitors") {
            $a01 = " AND code LIKE '125%' ";
            $code = 125;
            $b1 = "?parent_id=125";
        }
        if ($_GET['acc'] == "partners") {
            $a01 = " AND code LIKE '221%' ";
            $code = 221;
            $b1 = "?parent_id=221";
        }
        if ($_GET['acc'] == "assets") {
            $a01 = " AND code LIKE '11%' ";
            $code = 11;
            $b1 = "?parent_id=11";
        }
        if ($_GET['acc'] == "employees") {
            $a01 = " AND code LIKE '213%' ";
            $code = 213;
            $b1 = "?parent_id=213";
        }
        if ($_GET['acc'] == "rentable") {
            $a01 = " AND code LIKE '112%' ";
            $code = 112;
            $b1 = "?parent_id=112";
        }
        if ($_GET['acc'] == "stores") {
            $a01 = " AND code LIKE '123%' ";
            $code = 123;
            $b1 = "?parent_id=123";
        }
        if ($_GET['acc'] == "delivery") {
            $a01 = " AND code LIKE '126%' ";
            $code = 126;
            $b1 = "?parent_id=126";
        }
}}

if (isset($_GET['acc']) && $_GET['acc'] == 'delivery' && (($role['show_delivery'] ?? 1) == 0)) {
?>
<div class="content-wrapper">
  <div class="container p-5">
    <div class="alert alert-danger text-center p-4">
      <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
      <h4>صلاحية غير كافية</h4>
      <p class="mb-0">ليس لديك صلاحية لعرض حسابات الدليفري.</p>
    </div>
  </div>
</div>
<?php
  include('includes/footer.php');
  exit;
}
?>
<?php
// ==========================================
// تصحيح الأرصدة قبل الدخول وعرض التقرير
// ==========================================
$is_manual_fix = isset($_POST['fix_balances']);
$corrected_count = 0;
$corrected_list = [];

// 1. التأكد من صحة الحسابات الرئيسية والفرعية (is_basic)
$main_accounts = ['122', '211', '121', '124', '44', '32', '212', '125', '221', '11', '213', '112', '123', '126'];
foreach ($main_accounts as $acc_code) {
    $conn->query("UPDATE acc_head SET is_basic = 1 WHERE code = '$acc_code' AND isdeleted = 0");
}
$conn->query("UPDATE acc_head SET is_basic = 0 WHERE code LIKE '122%' AND code != '122' AND is_basic = 1 AND isdeleted = 0");
$conn->query("UPDATE acc_head SET is_basic = 0 WHERE code LIKE '211%' AND code != '211' AND is_basic = 1 AND isdeleted = 0");
$conn->query("UPDATE acc_head SET is_basic = 0 WHERE code LIKE '213%' AND code != '213' AND is_basic = 1 AND isdeleted = 0");

// 2. فحص الأرصدة غير المطابقة بين acc_head و journal_entries
$check_sql = "SELECT a.id, a.code, a.aname, a.balance, 
              COALESCE(SUM(j.debit) - SUM(j.credit), 0) AS calc
              FROM acc_head a
              LEFT JOIN journal_entries j ON j.account_id = a.id AND j.isdeleted = 0
              WHERE a.isdeleted = 0
              GROUP BY a.id, a.code, a.aname, a.balance
              HAVING ROUND(COALESCE(a.balance, 0), 2) != ROUND(calc, 2)";

$check_res = $conn->query($check_sql);
if ($check_res && $check_res->num_rows > 0) {
    while ($row = $check_res->fetch_assoc()) {
        $acc_id = (int)$row['id'];
        $calc_bal = (float)$row['calc'];
        if ($conn->query("UPDATE acc_head SET balance = $calc_bal WHERE id = $acc_id")) {
            $corrected_count++;
            $acc_display = htmlspecialchars($row['aname'], ENT_QUOTES, 'UTF-8') . " (" . htmlspecialchars($row['code'], ENT_QUOTES, 'UTF-8') . ")";
            $corrected_list[] = $acc_display;
        }
    }
}

// 3. إعداد بيانات SweetAlert
$swal_show = false;
$swal_icon = 'success';
$swal_title = '';
$swal_html = '';

if ($corrected_count > 0) {
    $swal_show = true;
    $swal_icon = 'success';
    $swal_title = 'تم تصحيح الأرصدة';

    if ($corrected_count == 1) {
        $count_text = 'رصيد حساب واحد';
    } elseif ($corrected_count == 2) {
        $count_text = 'رصيدي حسابين';
    } elseif ($corrected_count <= 10) {
        $count_text = "{$corrected_count} أرصدة";
    } else {
        $count_text = "{$corrected_count} رصيداً";
    }

    $swal_html = "تم تصحيح <b>{$count_text}</b> بنجاح ومطابقتها مع القيود المحاسبية.";
    if (!empty($corrected_list)) {
        $acc_names_preview = array_slice($corrected_list, 0, 5);
        $swal_html .= "<br><div class='text-muted mt-2' style='font-size:0.85em; text-align:right; max-height:120px; overflow-y:auto; padding:5px; background:#f8f9fa; border-radius:4px;'><strong>الحسابات المصححة:</strong><ul class='mb-0 pr-3'>";
        foreach ($acc_names_preview as $name) {
            $swal_html .= "<li>" . $name . "</li>";
        }
        if (count($corrected_list) > 5) {
            $swal_html .= "<li>و " . (count($corrected_list) - 5) . " حسابات أخرى...</li>";
        }
        $swal_html .= "</ul></div>";
    }
} elseif ($is_manual_fix) {
    $swal_show = true;
    $swal_icon = 'info';
    $swal_title = 'الأرصدة سليمة';
    $swal_html = 'جميع أرصدة الحسابات مضبوطة بالفعل ومطابقة تماماً للقيود المحاسبية.';
}
?>
<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
        <div class="card card">
            <div class="card-header">
                <div class="row">
                    <div class="col">
                <h3 class="hazaz"> قائمة الحسابات <?php
                    if (isset($code)) {
                    $rowname = $conn->query("SELECT `aname` FROM acc_head where code = $code")->fetch_assoc();
                     echo " \ ".$rowname['aname'];} ?></h3>
                    </div>

                    <div class="col">
                        <form method="post" class="d-inline">
                            <button type="submit" name="fix_balances" class="btn btn-warning float-right mx-2"><i class="fa fa-sync"></i> تصحيح الأرصدة</button>
                        </form>
                        <a href="add_account.php<?= $b1?>"><div class="btn btn-info float-right hadi-white-flash" id="addNewElement">جديد</div></a>
                    </div>
                </div>
                <div class="row">
                    <div class="col"></div>
                    <div class="col"><input class="form-control form-control-sm frst" type="text" name="" id="itmsearch" placeholder="بحث بالكود | اسم الحساب | id"></div>
                    
                </div>
            </div>

            <div class="card-body">
            <div class="table-responsive">
            <table id="horsTable" class="table table-hover table-stripped " data-page-length='50'>
                <thead>
                   <tr>
                    <th>#</th>
                    <th>الاسم</th>
                    <th>الرصيد</th>
                    <th>العنوان</th>
                    <th>التليفون</th>
                    <th>id</th>
                    <th>عمليات</th>
                   </tr>
                </thead>
                <tbody>
                <?php  
                $sqlacc="SELECT * from acc_head where is_basic = 0  $a01 AND isdeleted = 0 order by aname " ;
                $resacc = $conn->query($sqlacc);
                $x = 0;
                while ($rowacc = $resacc->fetch_assoc()) {
                  $x++
                ?>
                   <tr class="tr1">
                    <td><?= $x ?></td>
                    <td><form action="summary.php" method="post"><input type="text" hidden value="<?= $rowacc['id'] ?>" name="acc_id"><button class="btn btn-light btn-block" tybe="submit"><?= $rowacc['code'] ?>-<?= $rowacc['aname'] ?></button></form></td>
                    <td><?= $rowacc['balance'] ?></td>
                    <td><?= $rowacc['address'] ?></td>
                    <td><?= $rowacc['phone'] ?></td>
                    <td><?= $rowacc['id'] ?></td>
                    <td>
                        <a href="edit_account.php?id=<?= $rowacc['id']?>" class="btn btn-warning"><i class="fa fa-pen text-yellow-50"></i></a>
                        <a href="do/dodel_account.php?id=<?= $rowacc['id']?>" class="btn btn-danger"><i class="fa fa-trash text-yellow-50"></i></a>
                </td>
                   </tr>
              <?php } ?>
                </tbody>
                <tfoot>
                   <tr>
                    <th>#</th>
                    <th>الاسم</th>
                    <th>الرصيد</th>
                    <th>العنوان</th>
                    <th>التليفون</th>
                    <th>id</th>
                    <th>عمليات</th>
                   </tr>
                </tfoot>

            </table>
        </div>
            </div>
        </div>
    </div>
    </section>
</div>

<?php include('includes/footer.php') ?>
<?php if (!empty($swal_show)): ?>
<script>
document.addEventListener("DOMContentLoaded", function() {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: <?= json_encode($swal_icon, JSON_UNESCAPED_UNICODE) ?>,
            title: <?= json_encode($swal_title, JSON_UNESCAPED_UNICODE) ?>,
            html: <?= json_encode($swal_html, JSON_UNESCAPED_UNICODE) ?>,
            confirmButtonColor: '#28a745',
            confirmButtonText: 'حسناً',
            timer: 5000,
            timerProgressBar: true
        });
    }
});
</script>
<?php endif; ?>
