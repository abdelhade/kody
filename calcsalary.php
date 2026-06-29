<?php
include('includes/header.php');
include('includes/navbar.php');
include('includes/sidebar.php');
require_once('includes/payroll_calcs_helper.php');
ensure_payroll_calcs_schema($conn);
?>

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
          <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteAllModal">
            <i class="fas fa-trash"></i> حذف الكل
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
                    <th class="no-print">م</th>
                    <th><?= $lang_publicname ?></th>
                    <th>من</th>
                    <th>إلى</th>
                    <th>الراتب</th>
                    <th>ايام الحضور</th>
                    <th class="no-print">أجر اليوم</th>
                    <th class="no-print">أجر الساعة</th>
                    <th>س ع المستحقه</th>
                    <th>س ع الفعليه</th>
                    <th class="no-print">س الاضافي</th>
                    <th class="no-print">المستحق الأساسي</th>
                    <th class="no-print">مكافأة (+)</th>
                    <th class="no-print">تأمين (−)</th>
                    <th class="no-print">ضريبة (−)</th>
                    <th class="no-print">خصم (−)</th>
                    <th>الراتب الصافي</th>
                    <th class="no-print">الانتاجية</th>
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
                $sumBonus = 0;
                $sumInsurance = 0;
                $sumTax = 0;
                $sumDeduction = 0;
                $sumNet = 0;
                $sumProd = 0;
                if ($resdoc && $resdoc->num_rows === 0) {
                ?>
                  <tr>
                    <td colspan="19" class="text-center text-muted py-4">لا توجد نتائج مطابقة للفلتر</td>
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
                    $entitle = round((float)$rowdoc['entitle'], 2);
                    $bonus = round((float)($rowdoc['bonus'] ?? 0), 2);
                    $insurance = round((float)($rowdoc['insurance'] ?? 0), 2);
                    $tax = round((float)($rowdoc['tax'] ?? 0), 2);
                    $deduction = round((float)($rowdoc['deduction'] ?? 0), 2);
                    $netPay = isset($rowdoc['net_pay']) && (float)$rowdoc['net_pay'] != 0
                        ? round((float)$rowdoc['net_pay'], 2)
                        : round($entitle + $bonus - $insurance - $tax - $deduction, 2);
                    $prodVal = (float)($rowprod['prod_val'] ?? 0);
                    $sumEntitle += $entitle;
                    $sumBonus += $bonus;
                    $sumInsurance += $insurance;
                    $sumTax += $tax;
                    $sumDeduction += $deduction;
                    $sumNet += $netPay;
                    $sumProd += $prodVal;
                ?>
                  <tr>
                    <td class="no-print"><?= $x ?></td>
                    <td>
                      <a href="accattlogs.php?id=<?= (int)$rowdoc['id'] ?>">
                        <?= (int)$rowdoc['id'] ?># <?= htmlspecialchars($rowemp['name']) ?>
                      </a>
                    </td>
                    <td><?= $rowdoc['fromdate'] ?></td>
                    <td><?= $rowdoc['todate'] ?></td>
                    <td><?= number_format((float)$rowemp['salary'], 2) ?></td>
                    <td><?= (int)$rowdoc['workdays'] ?> / <?= (int)$rowdoc['alldays'] ?></td>
                    <td class="no-print"><?= $rowdoc['workdays'] > 0 ? number_format($rowemp['salary'] / $rowdoc['workdays'], 2) : '0.00' ?></td>
                    <td class="no-print"><?= $rowdoc['exphours'] > 0 ? number_format($rowemp['salary'] / $rowdoc['exphours'], 2) : '0.00' ?></td>
                    <td><?= $rowdoc['exphours'] ?></td>
                    <td><?= $rowdoc['accualhours'] ?>h</td>
                    <td class="no-print"><?= number_format((float)($rowsh['diffrence'] ?? 0), 2) ?> / <?= number_format((float)($rowsh1['diffrence'] ?? 0), 2) ?></td>
                    <td class="bg-sky-100 font-weight-bold no-print"><?= number_format($entitle, 2) ?></td>
                    <td class="text-success font-weight-bold no-print"><?= $bonus > 0 ? '+' : '' ?><?= number_format($bonus, 2) ?></td>
                    <td class="text-danger no-print"><?= $insurance > 0 ? '−' : '' ?><?= number_format($insurance, 2) ?></td>
                    <td class="text-danger no-print"><?= $tax > 0 ? '−' : '' ?><?= number_format($tax, 2) ?></td>
                    <td class="text-danger font-weight-bold no-print"><?= $deduction > 0 ? '−' : '' ?><?= number_format($deduction, 2) ?></td>
                    <td class="bg-green-100 font-weight-bold"><?= number_format($netPay, 2) ?></td>
                    <td class="bg-sky-100 no-print"><?= number_format($prodVal, 2) ?></td>
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
                <tfoot class="no-print">
                  <tr class="font-weight-bold totals-row">
                    <th colspan="11" class="text-left">الإجمالي (<?= $x ?> معالجة)</th>
                    <th><?= number_format($sumEntitle, 2) ?></th>
                    <th><?= number_format($sumBonus, 2) ?></th>
                    <th><?= number_format($sumInsurance, 2) ?></th>
                    <th><?= number_format($sumTax, 2) ?></th>
                    <th><?= number_format($sumDeduction, 2) ?></th>
                    <th><?= number_format($sumNet, 2) ?></th>
                    <th><?= number_format($sumProd, 2) ?></th>
                    <th class="no-print"></th>
                  </tr>
                </tfoot>
                <?php } ?>
              </table>
            </div>
          </div>
          
          <?php if ($x > 0) { ?>
          <div class="print-only mt-4" style="page-break-inside: avoid;">
            <h4 style="font-weight:bold; margin-bottom: 10px; border-bottom: 2px solid #000; display: inline-block;">ملخص إجماليات الكشف</h4>
            <table class="table table-bordered mt-2" style="width: 60% !important; font-size: 10pt;">
              <tr><th style="width: 50%; background: #e9ecef !important; -webkit-print-color-adjust: exact; print-color-adjust: exact;">إجمالي المستحق الأساسي</th><td><?= number_format($sumEntitle, 2) ?></td></tr>
              <tr><th style="background: #e9ecef !important; -webkit-print-color-adjust: exact; print-color-adjust: exact;">إجمالي المكافآت (+)</th><td class="text-success font-weight-bold"><?= number_format($sumBonus, 2) ?></td></tr>
              <tr><th style="background: #e9ecef !important; -webkit-print-color-adjust: exact; print-color-adjust: exact;">إجمالي التأمينات (−)</th><td class="text-danger font-weight-bold"><?= number_format($sumInsurance, 2) ?></td></tr>
              <tr><th style="background: #e9ecef !important; -webkit-print-color-adjust: exact; print-color-adjust: exact;">إجمالي الضرائب (−)</th><td class="text-danger font-weight-bold"><?= number_format($sumTax, 2) ?></td></tr>
              <tr><th style="background: #e9ecef !important; -webkit-print-color-adjust: exact; print-color-adjust: exact;">إجمالي الخصومات (−)</th><td class="text-danger font-weight-bold"><?= number_format($sumDeduction, 2) ?></td></tr>
              <tr><th style="background: #e9ecef !important; -webkit-print-color-adjust: exact; print-color-adjust: exact;">إجمالي الانتاجية</th><td class="font-weight-bold"><?= number_format($sumProd, 2) ?></td></tr>
              <tr><th style="background: #ddd !important; font-size: 12pt; -webkit-print-color-adjust: exact; print-color-adjust: exact;">صافي الرواتب المستحقة</th><td style="background: #ddd !important; font-size: 12pt; font-weight:bold; -webkit-print-color-adjust: exact; print-color-adjust: exact;"><?= number_format($sumNet, 2) ?></td></tr>
            </table>
          </div>
          <?php } ?>

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
  @page { size: A4 landscape; margin: 5mm; }
  html, body { background: #fff !important; font-size: 10pt; direction: rtl; width: 100% !important; height: auto !important; min-height: auto !important; }
  .wrapper { width: 100% !important; min-height: auto !important; overflow: visible !important; }
  .main-header, .main-sidebar, .content-header, .no-print, .main-footer,
  .dataTables_filter, .dataTables_length, .dataTables_info, .dataTables_paginate { display: none !important; }
  .content-wrapper { margin: 0 !important; padding: 0 !important; background: #fff !important; min-height: auto !important; }
  .calcsalary-page .content { padding: 0 !important; }
  .calcsalary-page .card { border: none !important; box-shadow: none !important; }
  .calcsalary-page .table { font-size: 8pt; color: #000 !important; width: 100% !important; max-width: 100% !important; table-layout: auto !important; }
  .calcsalary-page .table th, .calcsalary-page .table td {
    border: 1px solid #000 !important;
    padding: 2px 4px !important;
    word-wrap: break-word;
  }
  .calcsalary-page .bg-light, .calcsalary-page .bg-sky-100, .calcsalary-page .bg-green-100 {
    background: #f5f5f5 !important;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
  }
  .calcsalary-page .text-success { color: #28a745 !important; }
  .calcsalary-page .text-danger { color: #dc3545 !important; }
  .calcsalary-page .totals-row th, .calcsalary-page .totals-row td {
    background: #e9ecef !important;
    font-weight: bold;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
  }
  .print-only { display: block !important; }
  .print-header { text-align: center; margin-bottom: 10px; border-bottom: 2px solid #000; padding-bottom: 6px; }
  .print-header h2 { margin: 0; font-size: 16pt; font-weight: bold; }
  .print-header p { margin: 2px 0; font-size: 10pt; }
  .calcsalary-page a { color: #000 !important; text-decoration: none; }
}
</style>

<!-- Delete All Modal -->
<div class="modal fade" id="deleteAllModal" tabindex="-1" role="dialog" aria-labelledby="deleteAllModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <form action="do/dodel_all_attdocs.php" method="POST">
        <div class="modal-header bg-danger text-white py-2">
          <h5 class="modal-title" id="deleteAllModalLabel">تأكيد الحذف الشامل</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p class="text-danger font-weight-bold">تحذير: سيتم حذف جميع معالجات الحضور والرواتب الحالية ولن يمكن التراجع عن هذه العملية.</p>
          <div class="form-group">
            <label for="admin_pass">كلمة المرور لتأكيد الحذف</label>
            <input type="password" class="form-control" name="admin_pass" id="admin_pass" required placeholder="أدخل كلمة المرور الخاصة بالتعديلات">
          </div>
        </div>
        <div class="modal-footer py-2">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
          <button type="submit" class="btn btn-danger">تأكيد الحذف</button>
        </div>
      </form>
    </div>
  </div>
</div>

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
    setTimeout(function() {
      window.print();
      calcsalaryDt.page.len(oldLen).draw(false);
    }, 500);
  } else {
    window.print();
  }
}
</script>

<?php include('includes/footer.php') ?>
