<!-- Modal إغلاق الشيفت -->
<div class="modal fade" id="closeShiftModal" tabindex="-1" aria-labelledby="closeShiftModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
            <div class="modal-header bg-warning text-dark border-0 py-3">
                <h5 class="modal-title fw-bold" id="closeShiftModalLabel">
                    <i class="fas fa-power-off me-2"></i>إغلاق الشيفت
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4 py-3">
                <div class="text-center mb-3">
                    <i class="fas fa-exclamation-triangle fa-2x text-warning mb-2"></i>
                    <h6 class="fw-bold text-dark">هل أنت متأكد من إغلاق الشيفت؟</h6>
                    <p class="text-muted small">سيتم احتساب مبيعات الوردية الحالية وإغلاقها.</p>
                </div>
                
                <!-- جدول ملخص المبيعات البسيط -->
                <div class="table-responsive mb-3">
                    <table class="table table-bordered table-sm text-center mb-0" style="font-size: 0.85rem;">
                        <thead class="bg-light text-dark">
                            <tr>
                                <th class="py-2">البيان</th>
                                <th class="py-2">القيمة</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-start py-2 px-3 fw-bold">عدد الطلبات</td>
                                <td class="py-2 px-3 fw-bold text-primary" id="tbl_total_orders">0</td>
                            </tr>
                            <tr>
                                <td class="text-start py-2 px-3">إجمالي المبيعات</td>
                                <td class="py-2 px-3" id="tbl_total_gross">0.00 ج.م</td>
                            </tr>
                            <tr>
                                <td class="text-start py-2 px-3">إجمالي الخصومات</td>
                                <td class="py-2 px-3 text-danger" id="tbl_total_discount">0.00 ج.م</td>
                            </tr>
                            <tr class="table-success fw-bold">
                                <td class="text-start py-2 px-3">صافي المبيعات</td>
                                <td class="py-2 px-3 text-success" id="tbl_total_net">0.00 ج.م</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- البيانات المالية لإغلاق الشيفت -->
                <div class="card bg-light border-0 mb-3" style="border-radius: 8px;">
                    <div class="card-body p-3">
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label small mb-1">مبلغ بداية الشيفت</label>
                                <input type="text" class="form-control form-control-sm text-center fw-bold bg-white" id="shift_start_cash" readonly value="0.00">
                            </div>
                            <div class="col-6">
                                <label class="form-label small mb-1">صافي النقدية الواردة</label>
                                <input type="text" class="form-control form-control-sm text-center fw-bold bg-white" id="shift_cash_received" readonly value="0.00">
                            </div>
                            <div class="col-6">
                                <label class="form-label small mb-1 text-danger fw-bold">المصروفات (مصروف)</label>
                                <input type="number" class="form-control form-control-sm text-center fw-bold border-danger" id="shift_expenses" placeholder="0.00" step="0.01" value="0">
                            </div>
                            <div class="col-6">
                                <label class="form-label small mb-1 text-success fw-bold">الباقي (مستنتج)</label>
                                <input type="text" class="form-control form-control-sm text-center fw-bold bg-white text-success border-success" id="shift_fund_after" readonly value="0.00">
                            </div>
                            <div class="col-12 mt-2">
                                <label class="form-label small mb-1">ملاحظات إضافية</label>
                                <textarea class="form-control form-control-sm" id="shift_notes" rows="2" placeholder="أدخل أي ملاحظات إضافية هنا..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 px-4">
                <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>إلغاء
                </button>
                <button type="button" class="btn btn-warning btn-sm px-4 fw-bold" onclick="closeShift()">
                    <i class="fas fa-check-circle me-1"></i>إغلاق الشيفت
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // متغيرات لحفظ القيم العددية
    let globalStartCash = 0;
    let globalNetSales = 0;

    // وظيفة إغلاق الشيفت
    function closeShift() {
        const expenses = parseFloat($('#shift_expenses').val()) || 0;
        const notes = $('#shift_notes').val() || '';
        
        // استنتاج الباقي (نهاية الدرج)
        const fundAfter = globalStartCash + globalNetSales - expenses;
        // الكاش الفعلي الوارد في هذا الشيفت
        const cash = globalNetSales;
        
        console.log('Closing Shift Data:', { expenses, cash, fundAfter, notes });
        
        // إنشاء نموذج وإرسال البيانات
        const form = $('<form>', {
            method: 'POST',
            action: 'close_shift.php'
        });
        
        form.append($('<input>', { type: 'hidden', name: 'expenses', value: expenses }));
        form.append($('<input>', { type: 'hidden', name: 'exp_notes', value: 'مصروفات شيفت' }));
        form.append($('<input>', { type: 'hidden', name: 'cash', value: cash }));
        form.append($('<input>', { type: 'hidden', name: 'fund_after', value: fundAfter }));
        form.append($('<input>', { type: 'hidden', name: 'fund_before', value: globalStartCash }));
        form.append($('<input>', { type: 'hidden', name: 'notes', value: notes }));
        
        $('body').append(form);
        form.submit();
    }
    
    // حساب الباقي المستنتج ديناميكياً عند تعديل المصروفات
    document.addEventListener('DOMContentLoaded', function() {
        // حدث تعديل المصروفات
        $('#shift_expenses').on('input', function() {
            calculateRemaining();
        });

        // حدث فتح المودال
        $('#closeShiftModal').on('show.bs.modal', function () {
            loadShiftPreview();
        });
    });

    function calculateRemaining() {
        const expenses = parseFloat($('#shift_expenses').val()) || 0;
        const remaining = globalStartCash + globalNetSales - expenses;
        $('#shift_fund_after').val(remaining.toFixed(2) + ' ج.م');
    }
    
    function loadShiftPreview() {
        $.ajax({
            url: 'do/get_shift_preview.php',
            method: 'GET',
            success: function(data) {
                try {
                    var response = (typeof data === 'object') ? data : JSON.parse(data);
                    
                    if (response.success) {
                        // حفظ القيم في المتغيرات العامة
                        globalStartCash = parseFloat(response.data.start_cash) || 0;
                        globalNetSales = parseFloat(response.data.total_net) || 0;
                        
                        // ملء جدول الملخص
                        $('#tbl_total_orders').text(response.data.total_orders);
                        $('#tbl_total_gross').text(parseFloat(response.data.total_gross).toFixed(2) + ' ج.م');
                        $('#tbl_total_discount').text(parseFloat(response.data.total_discount).toFixed(2) + ' ج.م');
                        $('#tbl_total_net').text(globalNetSales.toFixed(2) + ' ج.م');
                        
                        // ملء الحقول المالية
                        $('#shift_start_cash').val(globalStartCash.toFixed(2) + ' ج.م');
                        $('#shift_cash_received').val(globalNetSales.toFixed(2) + ' ج.م');
                        
                        // حساب الباقي
                        calculateRemaining();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ',
                            text: response.error || 'لا يمكن تحميل بيانات الوردية حالياً'
                        });
                    }
                } catch (e) {
                    console.error('Error parsing shift preview:', e);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
            }
        });
    }
</script>
