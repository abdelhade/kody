<?php
/**
 * Employee Targets Tab content
 * Expects: $rowemp['id']
 */
$emp_id_for_target = (int)($rowemp['id'] ?? 0);
$current_year = (int)date('Y');
$years_range  = range($current_year - 2, $current_year + 2);
$months_ar = [
    1=>'يناير',2=>'فبراير',3=>'مارس',4=>'أبريل',5=>'مايو',6=>'يونيو',
    7=>'يوليو',8=>'أغسطس',9=>'سبتمبر',10=>'أكتوبر',11=>'نوفمبر',12=>'ديسمبر'
];
?>
<style>
#targets-tab-pane .target-card {
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    background: #fff;
    padding: 1rem;
    margin-bottom: 0.75rem;
    transition: box-shadow .2s;
}
#targets-tab-pane .target-card:hover { box-shadow: 0 2px 10px rgba(0,0,0,.08); }
#targets-tab-pane .badge-monthly { background:#3b82f6; color:#fff; }
#targets-tab-pane .badge-yearly  { background:#8b5cf6; color:#fff; }
#targets-tab-pane .target-value  { font-size:1.15rem; font-weight:700; color:#1e3a8a; }
</style>

<div id="targets-tab-pane" class="tab-pane fade" role="tabpanel">
    <div class="row mt-3">

        <!-- ── Add / Edit Form ── -->
        <div class="col-lg-5 mb-4">
            <div class="card shadow-sm border-0" style="border-radius:12px;">
                <div class="card-header" style="background:linear-gradient(135deg,#4B5694,#6b7fd4);border-radius:12px 12px 0 0;">
                    <h6 class="m-0 text-white"><i class="fas fa-bullseye ml-2"></i>إضافة / تعديل تارجيت</h6>
                </div>
                <div class="card-body">
                    <form id="targetForm">
                        <input type="hidden" name="employee_id" value="<?= $emp_id_for_target ?>">
                        <input type="hidden" name="action" value="save">

                        <div class="form-group">
                            <label class="font-weight-bold text-secondary" style="font-size:.85rem;">نوع الفترة</label>
                            <div class="d-flex gap-3">
                                <div class="icheck-primary d-inline ml-3">
                                    <input type="radio" id="pt_monthly" name="period_type" value="monthly" checked>
                                    <label for="pt_monthly">شهري</label>
                                </div>
                                <div class="icheck-purple d-inline">
                                    <input type="radio" id="pt_yearly" name="period_type" value="yearly">
                                    <label for="pt_yearly">سنوي</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-6" id="month_group">
                                <label class="font-weight-bold text-secondary" style="font-size:.85rem;">الشهر</label>
                                <select name="month" id="target_month" class="custom-select custom-select-sm">
                                    <?php foreach ($months_ar as $m => $mn): ?>
                                    <option value="<?= $m ?>" <?= $m == (int)date('n') ? 'selected' : '' ?>><?= $mn ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group col-6">
                                <label class="font-weight-bold text-secondary" style="font-size:.85rem;">السنة</label>
                                <select name="year" id="target_year" class="custom-select custom-select-sm">
                                    <?php foreach ($years_range as $y): ?>
                                    <option value="<?= $y ?>" <?= $y == $current_year ? 'selected' : '' ?>><?= $y ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold text-secondary" style="font-size:.85rem;">قيمة التارجيت (ج.م)</label>
                            <input type="number" step="0.01" min="0" name="target_value" id="target_value"
                                   class="form-control form-control-sm" placeholder="مثال: 50000" required>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold text-secondary" style="font-size:.85rem;">ملاحظات</label>
                            <input type="text" name="notes" id="target_notes" class="form-control form-control-sm" placeholder="اختياري">
                        </div>

                        <button type="submit" class="btn btn-primary btn-block btn-sm">
                            <i class="fas fa-save ml-1"></i> حفظ التارجيت
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- ── Targets List ── -->
        <div class="col-lg-7">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="font-weight-bold m-0"><i class="fas fa-list-ul ml-1 text-primary"></i>التارجيتات المسجلة</h6>
                <div class="d-flex align-items-center gap-2">
                    <label class="m-0 ml-2 text-secondary" style="font-size:.82rem;">سنة:</label>
                    <select id="filter_year" class="custom-select custom-select-sm" style="width:auto;">
                        <?php foreach ($years_range as $y): ?>
                        <option value="<?= $y ?>" <?= $y == $current_year ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button id="loadTargetsBtn" class="btn btn-outline-info btn-sm ml-2">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
            </div>
            <div id="targetsList">
                <div class="text-center text-muted py-4">
                    <i class="fas fa-spinner fa-spin fa-2x mb-2 d-block"></i> جاري التحميل...
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    var empId = <?= $emp_id_for_target ?>;
    var monthsAr = {
        1:'يناير',2:'فبراير',3:'مارس',4:'أبريل',5:'مايو',6:'يونيو',
        7:'يوليو',8:'أغسطس',9:'سبتمبر',10:'أكتوبر',11:'نوفمبر',12:'ديسمبر'
    };

    /* toggle month field */
    $('input[name="period_type"]').on('change', function () {
        if ($(this).val() === 'yearly') {
            $('#month_group').hide();
            $('#target_month').prop('disabled', true);
        } else {
            $('#month_group').show();
            $('#target_month').prop('disabled', false);
        }
    });

    /* load targets */
    function loadTargets() {
        var year = $('#filter_year').val();
        $('#targetsList').html('<div class="text-center text-muted py-4"><i class="fas fa-spinner fa-spin fa-2x mb-2 d-block"></i></div>');
        $.get('ajax/employee_targets.php', { action:'load', employee_id:empId, year:year }, function (resp) {
            if (!resp.success) { $('#targetsList').html('<p class="text-danger">' + resp.msg + '</p>'); return; }
            if (!resp.data.length) {
                $('#targetsList').html('<div class="text-center text-muted py-4"><i class="fas fa-inbox fa-2x mb-2 d-block"></i>لا توجد تارجيتات لهذه السنة</div>');
                return;
            }
            var html = '';
            resp.data.forEach(function (t) {
                var badge = t.period_type === 'yearly'
                    ? '<span class="badge badge-yearly px-2 py-1 ml-2">سنوي</span>'
                    : '<span class="badge badge-monthly px-2 py-1 ml-2">شهري</span>';
                var period = t.period_type === 'yearly'
                    ? 'السنة ' + t.year
                    : (monthsAr[parseInt(t.month)] || t.month) + ' ' + t.year;
                var notes  = t.notes ? '<small class="text-muted d-block mt-1"><i class="fas fa-comment-dots ml-1"></i>' + t.notes + '</small>' : '';
                html += '<div class="target-card d-flex justify-content-between align-items-center">'
                      + '<div>' + badge
                      + '<span class="font-weight-bold">' + period + '</span>'
                      + notes + '</div>'
                      + '<div class="target-value">' + parseFloat(t.target_value).toLocaleString('ar-EG', {minimumFractionDigits:2}) + ' ج.م</div>'
                      + '</div>';
            });
            $('#targetsList').html(html);
        }, 'json').fail(function () {
            $('#targetsList').html('<p class="text-danger">حدث خطأ أثناء التحميل</p>');
        });
    }

    /* save */
    $('#targetForm').on('submit', function (e) {
        e.preventDefault();
        var $btn = $(this).find('button[type=submit]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin ml-1"></i> جاري الحفظ...');
        $.post('ajax/employee_targets.php', $(this).serialize(), function (resp) {
            if (resp.success) {
                toastr ? toastr.success(resp.msg) : alert(resp.msg);
                loadTargets();
                $('#target_value').val('').focus();
                $('#target_notes').val('');
            } else {
                toastr ? toastr.error(resp.msg) : alert(resp.msg);
            }
        }, 'json').always(function () {
            $btn.prop('disabled', false).html('<i class="fas fa-save ml-1"></i> حفظ التارجيت');
        });
    });

    $('#loadTargetsBtn').on('click', loadTargets);
    $('#filter_year').on('change', loadTargets);

    /* auto-load when tab becomes active */
    $('a[href="#targets-tab-pane"]').on('shown.bs.tab', function () { loadTargets(); });
    /* load immediately if targets tab is somehow already active */
    if ($('#targets-tab-pane').hasClass('active')) { loadTargets(); }
})();
</script>
