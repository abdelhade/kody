<!-- Modal تقفيل الشيفت (مشترك بين شاشات POS) -->
<div class="modal fade" id="closeShiftModal" tabindex="-1" aria-labelledby="closeShiftModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
            <div class="modal-header bg-warning text-dark border-0 py-3">
                <h5 class="modal-title fw-bold" id="closeShiftModalLabel">
                    <i class="fas fa-power-off me-2"></i>تقفيل الشيفت
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4 py-3">
                <div id="scm_loading" class="text-center py-4">
                    <div class="spinner-border text-warning" role="status"></div>
                    <p class="mt-2 text-muted small">جاري تحميل بيانات الشيفت...</p>
                </div>

                <div id="scm_body" style="display:none;">
                    <div class="d-flex justify-content-between small text-muted mb-2">
                        <span><i class="fas fa-user me-1"></i><span id="scm_cashier"></span></span>
                        <span><i class="far fa-clock me-1"></i>من: <span id="scm_opened"></span></span>
                    </div>

                    <table class="table table-bordered table-sm text-center mb-3" style="font-size: 0.85rem;">
                        <tbody id="scm_summary"></tbody>
                    </table>

                    <div class="fw-bold mb-1">جرد الخزائن</div>
                    <p class="text-muted small mb-2" id="scm_blind_note" style="display:none;">
                        أدخل المبالغ اللي عدّيتها فعلاً — الفرق هيظهر بعد التقفيل.
                    </p>
                    <table class="table table-sm align-middle mb-3" style="font-size: 0.85rem;">
                        <thead class="bg-light">
                            <tr>
                                <th>الخزنة</th>
                                <th class="scm-exp text-center">المتوقع</th>
                                <th class="text-center" style="width: 130px;">المعدود</th>
                                <th class="scm-exp text-center">الفرق</th>
                            </tr>
                        </thead>
                        <tbody id="scm_accounts"></tbody>
                    </table>

                    <div class="row g-2">
                        <div class="col-6" id="scm_left_wrap">
                            <label class="form-label small mb-1">يتساب في الدرج للشيفت الجاي</label>
                            <input type="number" class="form-control form-control-sm text-center" id="scm_left" value="0" step="0.01" min="0">
                        </div>
                        <div class="col-12">
                            <label class="form-label small mb-1">ملاحظات</label>
                            <textarea class="form-control form-control-sm" id="scm_notes" rows="2" maxlength="255"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 px-4">
                <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>إلغاء
                </button>
                <button type="button" class="btn btn-warning btn-sm px-4 fw-bold" id="scm_submit" disabled>
                    <i class="fas fa-check-circle me-1"></i>تقفيل الشيفت
                </button>
            </div>
        </div>
    </div>
</div>

<script>
(function (init) {
    if (window.jQuery && document.readyState !== 'loading') {
        init(window.jQuery);
    } else {
        document.addEventListener('DOMContentLoaded', function () { init(window.jQuery); });
    }
})(function ($) {
    function money(v) {
        return (parseFloat(v) || 0).toFixed(2);
    }
    function esc(s) {
        return $('<div>').text(s == null ? '' : String(s)).html();
    }
    function notify(icon, title, text) {
        if (typeof Swal !== 'undefined') {
            return Swal.fire({ icon: icon, title: title, text: text });
        }
        alert(title + (text ? '\n' + text : ''));
        return Promise.resolve();
    }

    var state = { blind: true };

    function render(d) {
        state.blind = !!d.blind;
        $('#scm_cashier').text(d.cashier_name || '');
        $('#scm_opened').text(d.opened_at || '—');

        var s = '<tr><td class="text-start">عدد فواتير البيع</td><td>' + (d.total_orders || 0) + '</td></tr>';
        if (d.returns_count) {
            s += '<tr><td class="text-start">عدد المرتجعات</td><td>' + d.returns_count + '</td></tr>';
        }
        if (!state.blind) {
            s += '<tr><td class="text-start">صافي فواتير البيع</td><td>' + money(d.sales_net) + '</td></tr>';
            if (parseFloat(d.returns_total)) {
                s += '<tr><td class="text-start">المرتجعات</td><td class="text-danger">' + money(d.returns_total) + '</td></tr>';
            }
            s += '<tr class="table-success fw-bold"><td class="text-start">صافي المبيعات</td><td>' + money(d.total_net) + '</td></tr>';
            if (parseFloat(d.expenses)) {
                s += '<tr><td class="text-start">مصروفات</td><td class="text-danger">' + money(d.expenses) + '</td></tr>';
            }
            if (parseFloat(d.start_cash)) {
                s += '<tr><td class="text-start">رصيد بداية الدرج</td><td>' + money(d.start_cash) + '</td></tr>';
            }
        }
        $('#scm_summary').html(s);

        var rows = '';
        var hasPrimary = false;
        (d.accounts || []).forEach(function (a) {
            if (a.is_primary) hasPrimary = true;
            rows += '<tr data-acc="' + a.account_id + '" data-exp="' + (state.blind ? '' : money(a.expected)) + '">' +
                '<td>' + esc(a.name) + (a.is_primary ? ' <span class="badge bg-secondary">الدرج</span>' : '') + '</td>' +
                '<td class="scm-exp text-center">' + (state.blind ? '' : money(a.expected)) + '</td>' +
                '<td><input type="number" step="0.01" min="0" class="form-control form-control-sm text-center scm-count" placeholder="0.00"></td>' +
                '<td class="scm-exp text-center scm-diff"></td>' +
                '</tr>';
        });
        if (!rows) {
            rows = '<tr><td colspan="4" class="text-muted text-center">لا توجد حركات نقدية في الشيفت</td></tr>';
        }
        $('#scm_accounts').html(rows);
        $('#closeShiftModal .scm-exp').toggle(!state.blind);
        $('#scm_blind_note').toggle(state.blind);
        $('#scm_left_wrap').toggle(hasPrimary);
        $('#scm_left').val('0');
        $('#scm_notes').val('');
    }

    function load() {
        $('#scm_loading').show();
        $('#scm_body').hide();
        $('#scm_submit').prop('disabled', true);
        $.getJSON('do/get_shift_preview.php').done(function (r) {
            if (!r || !r.success) {
                notify('error', 'خطأ', (r && r.error) || 'لا يمكن تحميل بيانات الشيفت');
                return;
            }
            render(r.data);
            $('#scm_loading').hide();
            $('#scm_body').show();
            $('#scm_submit').prop('disabled', false);
        }).fail(function () {
            notify('error', 'خطأ', 'لا يمكن تحميل بيانات الشيفت');
        });
    }

    $(document).on('show.bs.modal', '#closeShiftModal', load);

    $(document).on('input', '#scm_accounts .scm-count', function () {
        if (state.blind) return;
        var $tr = $(this).closest('tr');
        var diff = (parseFloat($(this).val()) || 0) - (parseFloat($tr.data('exp')) || 0);
        $tr.find('.scm-diff')
            .text(money(diff))
            .toggleClass('text-danger', diff < -0.005)
            .toggleClass('text-success', diff > 0.005);
    });

    $(document).on('click', '#scm_submit', function () {
        var data = { ajax: 1, left_in_drawer: $('#scm_left').val() || 0, notes: $('#scm_notes').val() || '' };
        var empty = false;
        $('#scm_accounts tr[data-acc]').each(function () {
            var v = $(this).find('.scm-count').val();
            if (v === '') empty = true;
            data['counted[' + $(this).data('acc') + ']'] = v || 0;
        });

        var go = function () {
            var $btn = $('#scm_submit').prop('disabled', true);
            $.post('do/doclose_shift.php', data, null, 'json').done(function (r) {
                if (!r || !r.success) {
                    $btn.prop('disabled', false);
                    notify('error', 'لم يتم التقفيل', (r && r.error) || 'حدث خطأ');
                    return;
                }
                var icon = Math.abs(r.total_diff) < 0.01 ? 'success' : 'warning';
                notify(icon, 'تم تقفيل الشيفت', r.message).then(function () {
                    if (r.receipt_url) window.open(r.receipt_url, '_blank');
                    location.reload();
                });
            }).fail(function () {
                $btn.prop('disabled', false);
                notify('error', 'لم يتم التقفيل', 'تعذر الاتصال بالخادم');
            });
        };

        var msg = empty ? 'فيه خزائن ماتكتبش ليها مبلغ وهتتحسب صفر. متأكد من التقفيل؟' : 'متأكد من تقفيل الشيفت؟';
        if (typeof Swal !== 'undefined') {
            Swal.fire({ icon: 'question', title: msg, showCancelButton: true, confirmButtonText: 'تقفيل', cancelButtonText: 'رجوع' })
                .then(function (res) { if (res.isConfirmed) go(); });
        } else if (confirm(msg)) {
            go();
        }
    });
});
</script>
