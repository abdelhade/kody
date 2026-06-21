<?php
include('includes/header.php');
include('includes/navbar.php');
include('includes/sidebar.php');
require_once('includes/payroll_calcs_helper.php');
ensure_payroll_calcs_schema($conn);

$id = (int) ($_GET['edit'] ?? 0);
$row = $conn->query("SELECT * FROM payroll_calcs WHERE snd_id = $id AND isdeleted = 0 LIMIT 1")->fetch_assoc();
if (!$row) {
    echo '<div class="content-wrapper"><div class="alert alert-danger m-3">العملية غير موجودة</div></div>';
    include('includes/footer.php');
    exit;
}

$opTybe = (int) $row['calc_tybe'];
$lines = [];
$respro = $conn->query("SELECT * FROM payroll_calcs WHERE snd_id = $id AND isdeleted = 0 ORDER BY id ASC");
while ($respro && ($rowpro = $respro->fetch_assoc())) {
    $lines[] = $rowpro;
}

$employees = [];
$resemp = $conn->query("SELECT id, name FROM employees WHERE isdeleted = 0 ORDER BY name");
while ($resemp && ($rowemp = $resemp->fetch_assoc())) {
    $employees[] = $rowemp;
}

function render_edit_line_row($idx, $cr, $employees) {
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
                value="<?= htmlspecialchars((string) $cr['amount']) ?>" placeholder="0">
        </td>
        <td class="align-middle">
            <input type="text" class="form-control form-control-sm percent" name="percent[]"
                value="<?= htmlspecialchars((string) $cr['percent']) ?>" placeholder="0">
        </td>
        <td class="align-middle">
            <input type="text" class="form-control form-control-sm info2" name="info2[]"
                value="<?= htmlspecialchars($cr['info2'] ?? '') ?>" placeholder="ملاحظات">
        </td>
        <td class="text-center align-middle">
            <button type="button" class="btn btn-outline-danger btn-sm delete-line"><i class="fas fa-times"></i></button>
        </td>
    </tr>
    <?php
}
?>
<style>
.payroll-calc-page .content-wrapper { background: #f4f6f9; }
.payroll-calc-page .card { border: none; border-radius: 12px; box-shadow: 0 1px 4px rgba(15,23,42,0.06); }
.payroll-calc-page .lines-table thead th { background: #f8fafc; font-size: 0.8rem; font-weight: 600; }
.payroll-calc-page .lines-table td { padding: 0.4rem 0.35rem; vertical-align: middle; }
</style>

<div class="payroll-calc-page">
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="h4 mb-0">تعديل عملية #<?= $id ?></h1>
                <a href="payroll_calcs.php" class="btn btn-secondary btn-sm">رجوع</a>
            </div>
        </div>
    </section>

    <section class="content pb-4">
        <div class="container-fluid">
            <button type="button" class="btn btn-danger btn-sm mb-2" data-toggle="modal" data-target="#deleteModal">
                <i class="fa fa-trash"></i> حذف العملية
            </button>
            <div class="modal fade" id="deleteModal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header"><h4 class="modal-title">حذف العملية</h4></div>
                        <div class="modal-body"><p>هل أنت متأكد؟</p></div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">إلغاء</button>
                            <a href="do/dodel_payroll_calc.php?id=<?= $id ?>" class="btn btn-danger">حذف</a>
                        </div>
                    </div>
                </div>
            </div>

            <form action="do/doedit_payroll_calc.php?edit=<?= $id ?>" method="post">
                <input type="hidden" name="snd_id" value="<?= $id ?>">

                <div class="card mb-3">
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
                                    value="<?= htmlspecialchars($row['date']) ?>">
                            </div>
                            <div class="form-group col-md-6 mb-0">
                                <label class="small font-weight-bold mb-1">بيان العملية</label>
                                <input type="text" name="info" class="form-control form-control-sm"
                                    value="<?= htmlspecialchars($row['info'] ?? '') ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header py-2 d-flex justify-content-between align-items-center bg-white">
                        <span class="font-weight-bold">البنود <span class="badge badge-primary" id="lineCount"><?= count($lines) ?></span></span>
                        <button type="button" id="addLine" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> بند</button>
                    </div>
                    <div class="card-body p-0 table-responsive">
                        <table class="table table-sm table-hover mb-0 lines-table">
                            <thead>
                                <tr>
                                    <th style="width:36px">#</th>
                                    <th>الموظف</th>
                                    <th style="width:110px">مبلغ</th>
                                    <th style="width:80px">%</th>
                                    <th>ملاحظات</th>
                                    <th style="width:44px"></th>
                                </tr>
                            </thead>
                            <tbody id="lineItems">
                                <?php foreach ($lines as $idx => $cr) {
                                    render_edit_line_row($idx, $cr, $employees);
                                } ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer text-right py-2">
                        <button type="submit" class="btn btn-warning btn-sm px-4">حفظ التعديل</button>
                    </div>
                </div>

                <table class="d-none"><tbody id="lineTemplate">
                    <?php render_edit_line_row(0, ['emp_id' => '', 'amount' => 0, 'percent' => 0, 'info2' => ''], $employees); ?>
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
    $('#addLine').on('click', function () {
        var $row = $('#lineTemplate .line-row').clone();
        $row.find('.amount, .percent').val('0');
        $row.find('.info2').val('');
        $('#lineItems').append($row);
        updateLineNumbers();
    });
    $('#lineItems').on('click', '.delete-line', function () {
        if ($('#lineItems .line-row').length > 1) {
            $(this).closest('.line-row').remove();
            updateLineNumbers();
        }
    });
});
</script>

<?php include('includes/footer.php'); ?>
