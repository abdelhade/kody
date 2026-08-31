/**
 * POS Tables Panel — full-screen modal controller
 */
(function ($) {
    'use strict';

    const API = 'ajax/tables_panel.php';

    const state = {
        tables: [],
        selected: null,
        mode: 'normal', // normal | transfer
        selectedItemIds: [],
    };

    function fmt(n) {
        return (parseFloat(n) || 0).toFixed(2);
    }

    function post(action, data) {
        return $.ajax({
            url: API,
            method: 'POST',
            data: Object.assign({ action: action }, data || {}),
            dataType: 'json',
        });
    }

    function applyServerState(data) {
        if (data.tables) state.tables = data.tables;
        if (data.selected !== undefined) state.selected = data.selected;
        renderGrid();
        renderItems();
        updateOps();
    }

    function loadState(selectedId) {
        const sid = selectedId || (state.selected ? state.selected.table_id : 0);
        return post('state', { selected_table_id: sid })
            .done(function (data) {
                if (data.success) {
                    applyServerState(data);
                } else {
                    $('#ptpTablesGrid').html('<div class="text-center text-danger py-5"><p>' + (data.message || 'خطأ في التحميل') + '</p></div>');
                }
            })
            .fail(function (xhr) {
                let msg = 'فشل الاتصال بالخادم';
                try {
                    const j = JSON.parse(xhr.responseText);
                    if (j.message) msg = j.message;
                } catch (e) { /* ignore */ }
                $('#ptpTablesGrid').html('<div class="text-center text-danger py-5"><p>' + msg + '</p></div>');
            });
    }

    function statusLabel(s) {
        const map = {
            available: 'متاحة',
            occupied: 'مشغولة',
            merged: 'مدمجة',
            merged_occupied: 'مدمجة ومشغولة',
        };
        return map[s] || s;
    }

    function statusIcon(s) {
        if (s === 'available') return 'fa-check-circle';
        if (s === 'merged') return 'fa-object-group';
        return 'fa-utensils';
    }

    function renderGrid() {
        const $grid = $('#ptpTablesGrid');
        if (!state.tables.length) {
            $grid.html('<div class="text-center text-muted py-5"><p>لا توجد طاولات</p></div>');
            return;
        }

        let html = '<div class="row g-2">';
        state.tables.forEach(function (t) {
            const sel = state.selected && state.selected.table_id === t.id;
            const transferOk = state.mode === 'transfer' && t.status === 'available' && (!state.selected || t.id !== state.selected.table_id);

            let cls = 'ptp-table-card status-' + t.status;
            if (sel && state.mode === 'normal') cls += ' is-selected-panel';
            if (transferOk) cls += ' is-transfer-target';

            html += '<div class="col-4 col-md-3">';
            html += '<button type="button" class="' + cls + '" data-id="' + t.id + '" data-order-id="' + (t.order_id || '') + '" data-status="' + t.status + '">';
            html += '<i class="fas ' + statusIcon(t.status) + ' fa-lg"></i>';
            html += '<span>' + escapeHtml(t.name) + '</span>';
            html += '<small>' + statusLabel(t.status) + '</small>';
            if (t.order_total > 0) {
                html += '<span class="ptp-order-total">' + fmt(t.order_total) + ' ج.م</span>';
            }
            html += '</button></div>';
        });
        html += '</div>';

        if (state.mode === 'transfer') {
            html += '<div class="p-2 text-center border-top mt-2 alert alert-info mb-0">';
            html += '<i class="fas fa-hand-pointer me-1"></i>اختر الطاولة الهدف (متاحة)';
            html += ' <button type="button" class="btn btn-sm btn-outline-secondary ms-2" id="ptpCancelMode">إلغاء</button>';
            html += '</div>';
        }

        $grid.html(html);
    }

    function escapeHtml(s) {
        return $('<div>').text(s || '').html();
    }

    function renderItems() {
        const sel = state.selected;
        const $list = $('#ptpItemsList');

        if (!sel) {
            $('#ptpSelectedName').text('اختر طاولة');
            $('#ptpItemCount').text('0 صنف');
            $('#ptpTotal, #ptpNet, #ptpSelectedSum').text('0.00');
            $list.html('<div class="text-center text-muted py-5"><i class="fas fa-hand-pointer fa-3x mb-3 opacity-25"></i><p class="mb-0">اختر طاولة من الجانب</p></div>');
            state.selectedItemIds = [];
            return;
        }

        $('#ptpSelectedName').text(sel.table_name);
        const items = sel.items || [];
        $('#ptpItemCount').text(items.length + ' صنف');
        const totals = sel.totals || {};
        $('#ptpTotal').text(fmt(totals.total));
        $('#ptpNet').text(fmt(totals.net));
        updateSelectedSum();

        if (!items.length) {
            $list.html('<div class="text-center text-muted py-4"><p class="mb-0">لا توجد أصناف — انقر الطاولة لإضافة أصناف</p></div>');
            return;
        }

        let html = '';
        items.forEach(function (it) {
            const checked = state.selectedItemIds.indexOf(it.id) >= 0 ? ' checked' : '';
            const selCls = checked ? ' selected' : '';
            html += '<label class="ptp-item-row' + selCls + '" data-id="' + it.id + '">';
            html += '<input type="checkbox" class="ptp-item-chk" value="' + it.id + '"' + checked + '>';
            html += '<div class="ptp-item-info"><div class="iname">' + escapeHtml(it.iname) + '</div>';
            html += '<div class="iqty">× ' + fmt(it.qty) + ' @ ' + fmt(it.price) + '</div></div>';
            html += '<div class="ptp-item-price">' + fmt(it.line_total) + '</div>';
            html += '</label>';
        });
        $list.html(html);
    }

    function updateSelectedSum() {
        if (!state.selected || !state.selected.items) {
            $('#ptpSelectedSum').text('0.00');
            return;
        }
        let sum = 0;
        state.selected.items.forEach(function (it) {
            if (state.selectedItemIds.indexOf(it.id) >= 0) sum += it.line_total;
        });
        $('#ptpSelectedSum').text(fmt(sum));
    }

    function updateOps() {
        const hasOrder = state.selected && state.selected.order_id;
        const hasItems = state.selected && state.selected.items && state.selected.items.length > 0;
        const hasChecked = state.selectedItemIds.length > 0;

        $('#ptpBtnPayClose').prop('disabled', !hasOrder);
        $('#ptpBtnPayItems').prop('disabled', !hasChecked);
        $('#ptpBtnTransfer').prop('disabled', !hasOrder);
        $('#ptpBtnPrint').prop('disabled', !hasOrder);
        $('#ptpBtnAddItems').prop('disabled', !state.selected);

        const badge = $('#ptpModeBadge');
        if (state.mode === 'transfer') {
            badge.removeClass('d-none').text('وضع النقل');
        } else {
            badge.addClass('d-none').text('');
        }
    }

    function setMode(mode) {
        state.mode = mode;
        renderGrid();
        updateOps();
    }

    /** شارة اسم الطاولة في الـ navbar */
    function updateNavBadge(tableName) {
        const $badge = $('#navSelectedTable');
        if (!$badge.length) return;
        if (tableName) {
            $badge.removeClass('d-none').find('#navSelectedTableName').text(tableName);
        } else {
            $badge.addClass('d-none').find('#navSelectedTableName').text('');
        }
    }

    function closePanel() {
        const el = document.getElementById('posTablesPanelModal');
        if (!el) return;
        const modal = bootstrap.Modal.getInstance(el);
        if (modal) modal.hide();
    }

    function setFinalizeFlag(on) {
        const form = document.getElementById('posForm');
        if (!form) return;
        let input = form.querySelector('input[name="finalize_order"]');
        if (!input) {
            input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'finalize_order';
            form.appendChild(input);
        }
        input.value = on ? '1' : '0';
    }

    /**
     * وجهة الفاتورة: المجموعة المدمجة لها فاتورة واحدة على الطاولة الرئيسية،
     * فالضغط على طاولة تابعة يجب أن يفتح فاتورة الرئيسية لا أن يربطها بالتابعة.
     */
    function billingTarget(sel) {
        const id = sel.primary_table_id || sel.table_id;
        const name = sel.primary_table_name || sel.table_name;
        const extra = (sel.merged_with || []).length;
        return { id: id, name: name, badge: extra > 0 ? name + ' +' + extra : name };
    }

    /**
     * تحميل طلب في سلة الـ POS مع ضبط سياق الطاولة.
     * onReady تُنادى بعد اكتمال تحميل الأصناف وحساب الإجماليات.
     */
    function loadOrderIntoPos(opts, onReady) {
        const tableId = opts.tableId || 0;
        const tableName = opts.tableName || '';
        const orderId = opts.orderId || 0;
        // اسم المجموعة للعرض فقط؛ الاسم المُرسَل للخادم يبقى نظيفاً لأنه يُخزَّن في info
        const badgeName = opts.badgeName || tableName;

        // detach: طلب مستقل عن الطاولة (سداد أصناف) — لا يُربط بها عند الحفظ
        if (opts.detach) {
            $('#selected_table_id').val(0);
            $('#selected_table_name').val('');
            $('#age1').prop('checked', true);
            updateNavBadge(tableName + ' — سداد أصناف');
        } else {
            $('#selected_table_id').val(tableId);
            $('#selected_table_name').val(tableName);
            $('#age2').prop('checked', true);
            updateNavBadge(badgeName);

            if ($('#selectedTableDisplay').length) {
                $('#selectedTableDisplay').show().find('#selectedTableName').text(badgeName);
            }
        }

        // prop لا يُطلق change، فتُزامن الأزرار التابعة يدوياً
        if (typeof window.syncHoldButton === 'function') window.syncHoldButton();

        setFinalizeFlag(!!opts.finalize);
        closePanel();

        if (orderId && typeof window.loadExistingOrder === 'function') {
            $('#edit_order_id').val(orderId);
            $('#selected_order_id').val(orderId);
            window.loadExistingOrder(orderId, tableName, onReady);
        } else {
            $('#edit_order_id').val('');
            $('#selected_order_id').val('');
            $('#itemData').empty();
            if (typeof window.updateItemCount === 'function') window.updateItemCount();
            if (typeof window.updateTotal === 'function') window.updateTotal();
            if (typeof onReady === 'function') onReady(true);
        }
    }

    /** انتظار إخفاء لوحة الطاولات قبل فتح مودال الدفع (تجنّب تراكب المودالات) */
    function openPaymentModalSafely() {
        const panel = document.getElementById('posTablesPanelModal');
        const show = function () { $('#paymentModal').modal('show'); };

        if (!panel || !panel.classList.contains('show')) {
            show();
            return;
        }
        $(panel).one('hidden.bs.modal', function () {
            setTimeout(show, 150);
        });
    }

    /** دفع وإغلاق: يفتح مودال الدفع الوحيد بعد تحميل الطلب */
    function payAndClose() {
        const sel = state.selected;
        if (!sel || !sel.order_id) return;

        const target = billingTarget(sel);

        loadOrderIntoPos({
            tableId: target.id,
            tableName: target.name,
            badgeName: target.badge,
            orderId: sel.order_id,
            finalize: true,
        }, function (ok) {
            // فاتورة بصافي صفر تعني أن الأصناف لم تُحمَّل — الدفع هنا يُنشئ قيوداً صفرية
            const net = parseFloat($('#net_val').val()) || 0;
            if (!ok || net <= 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'تعذّر تحميل الفاتورة',
                    text: 'صافي الطلب صفر — أعد اختيار الطاولة وتأكد من ظهور الأصناف قبل الدفع',
                });
                return;
            }
            openPaymentModalSafely();
        });
    }

    /** دفع أصناف: يفصلها في طلب مستقل ثم يدفعه من نفس مودال الـ POS */
    function paySelectedItems() {
        const sel = state.selected;
        if (!sel || !sel.order_id || !state.selectedItemIds.length) return;

        post('split_items', {
            table_id: billingTarget(sel).id,
            order_id: sel.order_id,
            item_ids: state.selectedItemIds,
        }).done(function (data) {
            if (!data.success) {
                Swal.fire({ icon: 'error', title: 'خطأ', text: data.message });
                return;
            }
            state.selectedItemIds = [];
            loadOrderIntoPos({
                detach: true,
                tableName: data.table_name || sel.table_name,
                orderId: data.split_order_id,
                finalize: true,
            }, openPaymentModalSafely);
        }).fail(function () {
            Swal.fire({ icon: 'error', title: 'خطأ', text: 'فشل الاتصال بالخادم' });
        });
    }

    function bindEvents() {
        $(document).on('click', '.ptp-table-card', function () {
            const id = parseInt($(this).data('id'));
            const orderId = parseInt($(this).data('order-id')) || 0;
            const table = state.tables.find(function (t) { return t.id === id; });
            if (!table) return;

            if (state.mode === 'transfer') {
                if (table.status !== 'available') {
                    Swal.fire({ icon: 'warning', title: 'غير متاحة', text: 'اختر طاولة فارغة' });
                    return;
                }
                if (!state.selected) return;
                Swal.fire({
                    title: 'نقل إلى ' + table.name + '؟',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'نعم',
                    cancelButtonText: 'إلغاء',
                }).then(function (r) {
                    if (!r.isConfirmed) return;
                    post('transfer', {
                        old_table_id: state.selected.table_id,
                        new_table_id: id,
                    }).done(function (data) {
                        if (data.success) {
                            setMode('normal');
                            applyServerState(data);
                            Swal.fire({ icon: 'success', title: 'تم النقل', timer: 1200, showConfirmButton: false });
                        } else {
                            Swal.fire({ icon: 'error', text: data.message });
                        }
                    });
                });
                return;
            }

            // Normal: select table in panel (stay open for ops)
            post('state', { selected_table_id: id }).done(function (data) {
                if (data.success) {
                    state.selected = data.selected;
                    state.selectedItemIds = [];
                    renderGrid();
                    renderItems();
                    updateOps();
                } else {
                    Swal.fire({ icon: 'error', text: data.message || 'تعذر تحميل الطاولة' });
                }
            }).fail(function () {
                Swal.fire({ icon: 'error', text: 'فشل الاتصال بالخادم' });
            });
        });

        $('#ptpBtnAddItems').on('click', function () {
            if (!state.selected) return;
            const target = billingTarget(state.selected);
            const t = state.tables.find(function (x) { return x.id === target.id; });
            const orderId = state.selected.order_id || (t ? t.order_id : 0);
            loadOrderIntoPos({
                tableId: target.id,
                tableName: target.name,
                badgeName: target.badge,
                orderId: orderId,
                finalize: false,
            });
        });

        $(document).on('change', '.ptp-item-chk', function () {
            const id = parseInt($(this).val());
            const idx = state.selectedItemIds.indexOf(id);
            if (this.checked && idx < 0) state.selectedItemIds.push(id);
            if (!this.checked && idx >= 0) state.selectedItemIds.splice(idx, 1);
            $(this).closest('.ptp-item-row').toggleClass('selected', this.checked);
            updateSelectedSum();
            updateOps();
        });

        $('#ptpBtnPayClose').on('click', payAndClose);

        $('#ptpBtnPayItems').on('click', paySelectedItems);

        $('#ptpBtnTransfer').on('click', function () {
            if (!state.selected || !state.selected.order_id) return;
            setMode('transfer');
        });

        $(document).on('click', '#ptpCancelMode', function () {
            setMode('normal');
        });

        $('#ptpBtnPrint').on('click', function () {
            if (!state.selected || !state.selected.order_id) return;
            window.open('print/receipt.php?id=' + state.selected.order_id, '_blank');
        });

        $('#ptpBtnRefresh').on('click', function () {
            loadState();
        });

        $('#posTablesPanelModal').on('shown.bs.modal', function () {
            setMode('normal');
            loadState();
        });

        // لو صرف النظر عن الدفع، لا يبقى علَم الإغلاق معلّقاً على أي حفظ لاحق
        $(document).on('click', '#paymentModal [data-bs-dismiss="modal"], #paymentModal .btn-close', function () {
            setFinalizeFlag(false);
        });

        // إلغاء اختيار الطاولة من شارة الـ navbar
        $(document).on('click', '#navClearTable', function (e) {
            e.preventDefault();
            e.stopPropagation();
            $('#selected_table_id').val(0);
            $('#selected_table_name').val('');
            $('#edit_order_id').val('');
            $('#selected_order_id').val('');
            $('#age1').prop('checked', true);
            setFinalizeFlag(false);
            updateNavBadge('');
            if (typeof window.syncHoldButton === 'function') window.syncHoldButton();
            $('#selectedTableDisplay').hide();
            $('#itemData').empty();
            if (typeof window.updateItemCount === 'function') window.updateItemCount();
            if (typeof window.updateTotal === 'function') window.updateTotal();
        });
    }

    window.PosTablesPanel = {
        open: function () {
            const el = document.getElementById('posTablesPanelModal');
            if (!el) return;
            bootstrap.Modal.getOrCreateInstance(el).show();
        },
        refresh: function () {
            return loadState();
        },
        loadState: loadState,
        setTableBadge: updateNavBadge,
        clearFinalizeFlag: function () {
            setFinalizeFlag(false);
        },
    };

    $(document).ready(function () {
        bindEvents();

        // عرض الطاولة المختارة مسبقاً (وضع إضافة صنف عبر الرابط)
        const preName = $('#selected_table_name').val();
        if (preName) updateNavBadge(preName);
    });

})(jQuery);
