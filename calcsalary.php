<?php include('includes/header.php') ?>
<?php include('includes/navbar.php') ?>
<?php include('includes/sidebar.php') ?>

<?php
$filterEmp = isset($_GET['emp']) ? (int)$_GET['emp'] : 0;
$filterFrom = isset($_GET['from']) ? $conn->real_escape_string($_GET['from']) : '';
$filterTo = isset($_GET['to']) ? $conn->real_escape_string($_GET['to']) : '';
$companyName = $rowstg['company_name'] ?? 'FOCUS';
$hasFilter = $filterEmp > 0 || $filterFrom !== '' || $filterTo !== '';
?>

<div class="content-wrapper calcsalary-page">
  <section class="content-header no-print">
    <div class="container-fluid">
    <?php if ($role['show_attandance'] == 1) { ?>
      <div class="row mb-2 align-items-center">
        <div class="col-sm-6">
          <h1><?= $lang_attendance_processings_list ?? 'قائمة معالجات الحضور' ?></h1>
        </div>
        <div class="col-sm-6 text-left">
          <button type="button" class="btn btn-primary" onclick="printCalcsalary()">
            <i class="fas fa-print"></i> طباعة
          </button>
          <a href="add_calcsalary.php" class="btn btn-success"><?= $lang_add_new ?></a>
        </div>
      </div>
    <?php } ?>
    </div>
  </section>

  <section class="content">
    <div class="container-fluid" id="calcsalary-print-area">
    <?php if ($role['show_attandance'] == 1) { ?>

      <div class="card no-print mb-3">
        <div class="card-header">
          <h3 class="card-title mb-0"><i class="fas fa-filter"></i> فلتر</h3>
        </div>
        <div class="card-body">
          <form method="get" action="calcsalary.php" class="form-row align-items-end">
            <div class="form-group col-md-4">
              <label>الموظف</label>
              <select name="emp" class="form-control">
                <option value="">— الكل —</option>
                <?php
                $resEmpList = $conn->query("SELECT id, name FROM employees WHERE isdeleted != 1 OR isdeleted IS NULL ORDER BY name");
                while ($e = $resEmpList->fetch_assoc()) {
                    $sel = ($filterEmp === (int)$e['id']) ? 'selected' : '';
                    echo '<option value="' . (int)$e['id'] . '" ' . $sel . '>' . htmlspecialchars($e['name']) . '</option>';
                }
                ?>
              </select>
            </div>
            <div class="form-group col-md-3">
              <label>من تاريخ</label>
              <input type="date" name="from" class="form-control" value="<?= htmlspecialchars($filterFrom) ?>">
            </div>
            <div class="form-group col-md-3">
              <label>إلى تاريخ</label>
              <input type="date" name="to" class="form-control" value="<?= htmlspecialchars($filterTo) ?>">
            </div>
            <div class="form-group col-md-2">
              <button type="submit" class="btn btn-info btn-block">بحث</button>
              <?php if ($hasFilter) { ?>
              <a href="calcsalary.php" class="btn btn-outline-secondary btn-block mt-1">إلغاء</a>
              <?php } ?>
            </div>
          </form>
        </div>
      </div>

      <div class="print-only print-header">
        <h2><?= htmlspecialchars($companyName) ?></h2>
        <p><?= $lang_attendance_processings_print ?? 'قائمة معالجات الحضور والرواتب' ?></p>
        <?php if ($hasFilter) { ?>
        <p class="print-filters">
          <?php
          if ($filterEmp > 0) {
              $fn = $conn->query("SELECT name FROM employees WHERE id = $filterEmp")->fetch_assoc();
              echo 'الموظف: ' . htmlspecialchars($fn['name'] ?? '') . ' — ';
          }
          if ($filterFrom) echo 'من: ' . htmlspecialchars($filterFrom) . ' ';
          if ($filterTo) echo 'إلى: ' . htmlspecialchars($filterTo);
          ?>
        </p>
        <?php } ?>
        <p>تاريخ الطباعة: <?= date('Y-m-d H:i') ?></p>
      </div>

      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header no-print">
              <h3 class="card-title mb-0">النتائج</h3>
            </div>
            <div class="card-body table-responsive p-0">
              <table id="calcsalaryTable" class="table table-bordered table-hover mb-0">
                <thead class="bg-light text-sm">
                  <tr>
                    <th>م</th>
                    <th><?= $lang_publicname ?></th>
                    <th>من</th>
                    <th>إلى</th>
                    <th>الراتب</th>
                    <th>ايام الحضور</th>
                    <th>أجر اليوم</th>
                    <th>أجر الساعة</th>
                    <th>س ع المستحقه</th>
                    <th>س ع الفعليه</th>
                    <th>س الاضافي</th>
                    <th>المستحق (الصافي)</th>
                    <th>إضافي</th>
                    <th>خصم</th>
                    <th>الانتاجية</th>
                    <th class="no-print"><?= $lang_publicoperations ?></th>
                  </tr>
                </thead>
                <tbody>
                <?php
                $where = "isdeleted != 1";
                if ($filterEmp > 0) {
                    $where .= " AND empid = $filterEmp";
                }
                if ($filterFrom !== '') {
                    $where .= " AND todate >= '$filterFrom'";
                }
                if ($filterTo !== '') {
                    $where .= " AND fromdate <= '$filterTo'";
                }
                $sqldoc = "SELECT * FROM `attdocs` WHERE $where ORDER BY id DESC";
                $resdoc = $conn->query($sqldoc);
                $x = 0;
                $sumEntitle = 0;
                $sumProd = 0;
                $sumExtra = 0;
                $sumDeduct = 0;
                if ($resdoc && $resdoc->num_rows === 0) {
                ?>
                  <tr>
                    <td colspan="16" class="text-center text-muted py-4">لا توجد نتائج مطابقة للفلتر</td>
                  </tr>
                <?php
                }
                while ($resdoc && ($rowdoc = $resdoc->fetch_assoc())) {
                    $x++;
                    $empid = (int)$rowdoc['empid'];
                    $rowemp = $conn->query("SELECT * FROM employees WHERE id = $empid")->fetch_assoc();
                    if (!$rowemp) continue;
                    $startdate = $rowdoc['fromdate'];
                    $enddate = $rowdoc['todate'];
                    $rowsh = $conn->query("SELECT SUM(curhours - defhours) AS diffrence FROM attlog WHERE employee = '$empid' AND curhours > defhours AND day >= '$startdate' AND day <= '$enddate'")->fetch_assoc();
                    $rowsh1 = $conn->query("SELECT SUM(curhours) - SUM(defhours) AS diffrence FROM attlog WHERE employee = '$empid' AND day >= '$startdate' AND day <= '$enddate' AND statue != 0")->fetch_assoc();
                    $empname = $conn->real_escape_string($rowemp['name']);
                    $rowprod = $conn->query("SELECT SUM(value) AS prod_val FROM productions WHERE emp_name = '$empname' AND date >= '$startdate' AND date <= '$enddate'")->fetch_assoc();
                    $rowextra = $conn->query("SELECT SUM(amount) AS extra_val FROM financial_transactions WHERE emp_name = '$empname' AND type = 1 AND date >= '$startdate' AND date <= '$enddate'")->fetch_assoc();
                    $rowdeduct = $conn->query("SELECT SUM(amount) AS deduct_val FROM financial_transactions WHERE emp_name = '$empname' AND type = 0 AND date >= '$startdate' AND date <= '$enddate'")->fetch_assoc();
                    
                    $entitle = round((float)$rowdoc['entitle'], 2);
                    $prodVal = (float)($rowprod['prod_val'] ?? 0);
                    $extraVal = (float)($rowextra['extra_val'] ?? 0);
                    $deductVal = (float)($rowdeduct['deduct_val'] ?? 0);
                    
                    $sumEntitle += $entitle;
                    $sumProd += $prodVal;
                    $sumExtra += $extraVal;
                    $sumDeduct += $deductVal;
                ?>
                  <tr>
                    <td><?= $x ?></td>
                    <td>
                       <a href="accattlogs.php?id=<?= (int)$rowdoc['id'] ?>">
                        <?= (int)$rowdoc['id'] ?># <?= htmlspecialchars($rowemp['name']) ?>
                      </a>
                    </td>
                    <td><?= $rowdoc['fromdate'] ?></td>
                    <td><?= $rowdoc['todate'] ?></td>
                    <td><?= number_format((float)$rowemp['salary'], 2) ?></td>
                    <td><?= (int)$rowdoc['workdays'] ?> / <?= (int)$rowdoc['alldays'] ?></td>
                    <td><?= $rowdoc['workdays'] > 0 ? number_format($rowemp['salary'] / $rowdoc['workdays'], 2) : '0.00' ?></td>
                    <td><?= $rowdoc['exphours'] > 0 ? number_format($rowemp['salary'] / $rowdoc['exphours'], 2) : '0.00' ?></td>
                    <td><?= $rowdoc['exphours'] ?></td>
                    <td><?= $rowdoc['accualhours'] ?>h</td>
                    <td><?= number_format((float)($rowsh['diffrence'] ?? 0), 2) ?> / <?= number_format((float)($rowsh1['diffrence'] ?? 0), 2) ?></td>
                    <td class="bg-sky-100 font-weight-bold"><?= number_format($entitle, 2) ?></td>
                    <td class="text-success font-weight-bold"><?= number_format($extraVal, 2) ?></td>
                    <td class="text-danger font-weight-bold"><?= number_format($deductVal, 2) ?></td>
                    <td class="bg-sky-100"><?= number_format($prodVal, 2) ?></td>
                    <td class="no-print">
                      <a href="do/dodel_attdoc.php?doc=<?= (int)$rowdoc['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('حذف هذه المعالجة؟')">X</a>
                      <?php if (!empty($rowdoc['info'])) { ?>
                      <span title="<?= htmlspecialchars($rowdoc['info']) ?>" class="btn btn-sm btn-secondary">?</span>
                      <?php } ?>
                    </td>
                  </tr>
                <?php } ?>
                </tbody>
                <?php if ($x > 0) { ?>
                <tfoot>
                  <tr class="font-weight-bold totals-row">
                    <th colspan="11" class="text-left">الإجمالي (<?= $x ?> معالجة)</th>
                    <th><?= number_format($sumEntitle, 2) ?></th>
                    <th class="text-success"><?= number_format($sumExtra, 2) ?></th>
                    <th class="text-danger"><?= number_format($sumDeduct, 2) ?></th>
                    <th><?= number_format($sumProd, 2) ?></th>
                    <th class="no-print"></th>
                  </tr>
                </tfoot>
                <?php } ?>
              </table>
            </div>
          </div>
        </div>
      </div>

    <?php } else {
        echo $userErrorMassage;
    } ?>
    </div>
  </section>
</div>

<style>
.calcsalary-page .print-only { display: none; }
@media print {
  @page { size: A4 landscape; margin: 10mm; }
  body { background: #fff !important; font-size: 10pt; direction: rtl; }
  .main-header, .main-sidebar, .content-header, .no-print, .main-footer,
  .dataTables_filter, .dataTables_length, .dataTables_info, .dataTables_paginate { display: none !important; }
  .content-wrapper { margin: 0 !important; padding: 0 !important; background: #fff !important; min-height: auto !important; }
  .calcsalary-page .content { padding: 0 !important; }
  .calcsalary-page .card { border: none !important; box-shadow: none !important; }
  .calcsalary-page .table { font-size: 9pt; color: #000 !important; }
  .calcsalary-page .table th, .calcsalary-page .table td {
    border: 1px solid #333 !important;
    padding: 3px 5px !important;
  }
  .calcsalary-page .bg-light, .calcsalary-page .bg-sky-100 {
    background: #eee !important;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
  }
  .calcsalary-page .totals-row th, .calcsalary-page .totals-row td {
    background: #ddd !important;
    font-weight: bold;
  }
  .print-only { display: block !important; }
  .print-header { text-align: center; margin-bottom: 10px; border-bottom: 2px solid #000; padding-bottom: 6px; }
  .print-header h2 { margin: 0; font-size: 16pt; }
  .print-header p { margin: 2px 0; font-size: 10pt; }
  .calcsalary-page a { color: #000 !important; text-decoration: none; }
}
</style>

<script>
var calcsalaryDt = null;
$(function () {
  if ($.fn.DataTable && $('#calcsalaryTable tbody tr td[colspan]').length === 0) {
    calcsalaryDt = $('#calcsalaryTable').DataTable({
      paging: true,
      lengthChange: true,
      pageLength: 25,
      searching: true,
      ordering: true,
      info: true,
      autoWidth: false,
      responsive: false,
      language: {
        search: 'بحث سريع:',
        lengthMenu: 'عرض _MENU_',
        info: 'عرض _START_ إلى _END_ من _TOTAL_',
        paginate: { first: 'الأول', last: 'الأخير', next: 'التالي', previous: 'السابق' },
        zeroRecords: 'لا توجد نتائج',
        emptyTable: 'لا توجد بيانات'
      }
    });
  }
});

function printCalcsalary() {
  if (calcsalaryDt) {
    var oldLen = calcsalaryDt.page.len();
    calcsalaryDt.page.len(-1).draw(false);
    window.print();
    calcsalaryDt.page.len(oldLen).draw(false);
  } else {
    window.print();
  }
}
</script>

<?php include('includes/footer.php') ?>
