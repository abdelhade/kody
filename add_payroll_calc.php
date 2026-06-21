<?php
include('includes/header.php');
include('includes/navbar.php');
include('includes/sidebar.php');
require_once('includes/payroll_calcs_helper.php');
ensure_payroll_calcs_schema($conn);

$copyRows = [];
$copyDate = date('Y-m-d');
$copyInfo = '';
$opTybe = 1;
$isCopy = !empty($_GET['copy']);
if ($isCopy) {
    $copyId = (int) $_GET['copy'];
    $resCopy = $conn->query("SELECT * FROM payroll_calcs WHERE snd_id = $copyId AND isdeleted = 0 ORDER BY id ASC");
    while ($resCopy && ($r = $resCopy->fetch_assoc())) {
        $copyRows[] = $r;
        $copyDate = $r['date'];
        $copyInfo = $r['info'] ?? '';
        $opTybe = (int) $r['calc_tybe'];
    }
}
if (empty($copyRows)) {
    $copyRows[] = ['emp_id' => '', 'amount' => 0, 'percent' => 0, 'info2' => ''];
}

$rowprod = $conn->query("SELECT MAX(snd_id) as max_id FROM payroll_calcs")->fetch_assoc();
$next_id = ($rowprod['max_id'] == null) ? 1 : (int) $rowprod['max_id'] + 1;

$employees = [];
$resemp = $conn->query("SELECT id, name FROM employees WHERE isdeleted = 0 ORDER BY name");
while ($resemp && ($rowemp = $resemp->fetch_assoc())) {
    $employees[] = $rowemp;
}

function render_line_row($idx, $cr, $employees) {
    ?>
    <tr class="line-row">
        <td class="text-center text-muted line-num align-middle"><?= $idx + 1 ?></td>
        <td class="align-middle">
            <select name="emp_id[]" class="form-control form-control-sm" required>
                <?php foreach ($employees as $rowemp) {
                    $sel = ((int) ($cr['emp_id'] ?? 0) === (int) $rowemp['id']) ? 'selected' : '';
                ?>
                <option value="<?= (int) $rowemp['id'] ?>" <?= $sel ?>><?= htmlspecialchars($rowemp['name']) ?></option>
                <?php } ?>
            </select>
        </td>
        <td class="align-middle">
            <input type="text" class="form-control form-control-sm amount" name="amount[]"
                pattern="[0-9]*\.?[0-9]+" value="<?= htmlspecialchars((string) ($cr['amount'] ?? 0)) ?>" placeholder="0">
        </td>
        <td class="align-middle">
            <input type="text" class="form-control form-control-sm percent" name="percent[]"
                pattern="[0-9]*\.?[0-9]+" value="<?= htmlspecialchars((string) ($cr['percent'] ?? 0)) ?>" placeholder="0">
        </td>
        <td class="align-middle">
            <input type="text" class="form-control form-control-sm info2" name="info2[]"
                value="<?= htmlspecialchars($cr['info2'] ?? '') ?>" placeholder="ملاحظات">
        </td>
        <td class="text-center align-middle">
            <button type="button" class="btn btn-outline-danger btn-sm delete-line" title="حذف">
                <i class="fas fa-times"></i>
            </button>
        </td>
    </tr>
    <?php
}
?>
<style>
.payroll-calc-page .content-wrapper { background: #f4f6f9; }
.payroll-calc-page .page-hero {
    background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-color) 55%, var(--primary-light) 100%);
    border-radius: 12px; color: #fff; padding: 1rem 1.25rem; margin-bottom: 1rem;
    box-shadow: 0 4px 14px rgba(75, 86, 148, 0.25);
}
.payroll-calc-page .page-hero h1 { font-size: 1.2rem; font-weight: 700; margin: 0; color: #fff; }
.payroll-calc-page .card { border: none; border-radius: 12px; box-shadow: 0 1px 4px rgba(15,23,42,0.06); }
.payroll-calc-page .lines-table thead th {
    background: #f8fafc; font-size: 0.8rem; font-weight: 600; color: #475569;
    border-bottom: 2px solid #e2e8f0; white-space: nowrap;
}
.payroll-calc-page .lines-table td { vertical-align: middle; padding: 0.4rem 0.35rem; }
.payroll-calc-page .line-row-add td { background: #f8fafc; }
.payroll-calc-page .op-number-badge {
    font-size: 1rem; padding: 0.35rem 0.7rem; border-radius: 8px;
    background: rgba(255,255,255,0.2); color: #fff;
}
</style>

<div class="payroll-calc-page">
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="page-hero d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h1>
                        <i class="fas fa-<?= $isCopy ? 'copy' : 'plus-circle' ?> ml-2"></i>
                        <?= $isCopy ? 'نسخ عملية احتساب' : 'إضافة عملية احتساب' ?>
                    </h1>
                    <small class="d-block mt-1 opacity-90">نوع واحد للعملية — بنود متعددة للموظفين</small>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="op-number-badge">#<?= $next_id ?></span>
                    <a href="payroll_calcs.php" class="btn btn-outline-light btn-sm">
                        <i class="fas fa-arrow-right ml-1"></i> القائمة
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="content pb-4">
        <div class="container-fluid">
            <form action="do/doadd_payroll_calc.php" method="post">
                <input type="hidden" name="snd_id" value="<?= $next_id ?>">

                <div class="card mb-3">
                    <div class="card-header py-2 bg-white">
                        <strong><i class="fas fa-cog ml-1 text-primary"></i> بيانات العملية</strong>
                    </div>
                    <div class="card-body py-3">
                        <div class="row align-items-end">
                            <div class="form-group col-md-3 mb-md-0">
                                <label class="small font-weight-bold mb-1">نوع الاحتساب</label>
                                <select name="calc_tybe" class="form-control form-control-sm" required>
                                    <?php for ($t = 1; $t <= 4; $t++) { ?>
                                    <option value="<?= $t ?>" <?= ($opTybe === $t) ? 'selected' : '' ?>>
                                        <?= payroll_calc_type_label($t, true) ?>
                                    </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group col-md-3 mb-md-0">
                                <label class="small font-weight-bold mb-1">التاريخ</label>
                                <input type="date" name="date" class="form-control form-control-sm" required
                                    value="<?= htmlspecialchars($copyDate) ?>">
                            </div>
                            <div class="form-group col-md-6 mb-0">
                                <label class="small font-weight-bold mb-1">بيان العملية</label>
                                <input type="text" name="info" class="form-control form-control-sm"
                                    value="<?= htmlspecialchars($copyInfo) ?>" placeholder="اختياري">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header py-2 d-flex justify-content-between align-items-center bg-white">
                        <span class="font-weight-bold">
                            <i class="fas fa-list ml-1 text-primary"></i>
                            بنود العملية <span class="badge badge-primary" id="lineCount"><?= count($copyRows) ?></span>
                        </span>
                        <button type="button" id="addLine" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> بند
                        </button>
                    </div>
                    <div class="card-body p-0 table-responsive">
                        <table class="table table-sm table-hover mb-0 lines-table">
                            <thead>
                                <tr>
                                    <th style="width:36px">#</th>
                                    <th style="min-width:180px">الموظف</th>
                                    <th style="width:110px">مبلغ</th>
                                    <th style="width:80px">%</th>
                                    <th>ملاحظات</th>
                                    <th style="width:44px"></th>
                                </tr>
                            </thead>
                            <tbody id="lineItems">
                                <?php foreach ($copyRows as $idx => $cr) {
                                    render_line_row($idx, $cr, $employees);
                                } ?>
                            </tbody>
                            <tfoot>
                                <tr class="line-row-add">
                                    <td colspan="6" class="py-2 px-3">
                                        <button type="button" class="btn btn-outline-primary btn-sm" id="addLine2">
                                            <i class="fas fa-plus ml-1"></i> إضافة بند
                                        </button>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="card-footer d-flex justify-content-end gap-2 py-2">
                        <a href="payroll_calcs.php" class="btn btn-default btn-sm">إلغاء</a>
                        <button type="submit" class="btn btn-success btn-sm px-4">
                            <i class="fas fa-check ml-1"></i> حفظ
                        </button>
                    </div>
                </div>

                <table class="d-none"><tbody id="lineTemplate">
                    <?php render_line_row(0, ['emp_id' => '', 'amount' => 0, 'percent' => 0, 'info2' => ''], $employees); ?>
                </tbody></table>
            </form>
        </div>
    </section>
</div>
</div>

<script>
$(function () {
    function updateLineNumbers() {
        $('#lineItems .line-row').each(function (i) {
            $(this).find('.line-num').text(i + 1);
        });
        $('#lineCount').text($('#lineItems .line-row').length);
    }

    function addLine() {
        var $row = $('#lineTemplate .line-row').clone();
        $row.find('.amount, .percent').val('0');
        $row.find('.info2').val('');
        $('#lineItems').append($row);
        updateLineNumbers();
    }

    $('#addLine, #addLine2').on('click', addLine);

    $('#lineItems').on('click', '.delete-line', function () {
        if ($('#lineItems .line-row').length <= 1) {
            alert('يجب أن تحتوي العملية على بند واحد على الأقل');
            return;
        }
        $(this).closest('.line-row').remove();
        updateLineNumbers();
    });

    updateLineNumbers();
});
</script>

<?php include('includes/footer.php'); ?>
