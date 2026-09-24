<?php
/** Full-screen tables panel modal for pos_barcode.php */
?>
<div class="modal fade" id="posTablesPanelModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content pos-tables-panel-content">
            <div class="modal-header pos-tables-panel-header py-2 px-3">
                <h5 class="modal-title mb-0 fw-bold">
                    <i class="fas fa-th-large me-2"></i>إدارة الطاولات
                </h5>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-light text-dark d-none" id="ptpModeBadge"></span>
                    <button type="button" class="btn btn-outline-light btn-sm" id="ptpBtnRefresh" title="تحديث">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
            </div>
            <div class="modal-body p-0 overflow-hidden">
                <div class="row g-0 h-100 pos-tables-panel-row">
                    <!-- Left: items + operations -->
                    <div class="col-5 col-lg-5 border-end d-flex flex-column pos-tables-left">
                        <div class="ptp-selected-header px-3 py-2 border-bottom flex-shrink-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 fw-bold" id="ptpSelectedTitle">
                                    <i class="fas fa-chair me-1 text-primary"></i>
                                    <span id="ptpSelectedName">اختر طاولة</span>
                                </h6>
                                <span class="badge bg-primary" id="ptpItemCount">0 صنف</span>
                            </div>
                        </div>

                        <div class="flex-grow-1 overflow-auto p-2" id="ptpItemsList">
                            <div class="text-center text-muted py-5">
                                <i class="fas fa-hand-pointer fa-3x mb-3 opacity-25"></i>
                                <p class="mb-0">اختر طاولة من الجانب</p>
                            </div>
                        </div>

                        <div class="ptp-totals-bar px-3 py-2 border-top flex-shrink-0">
                            <div class="row g-1 text-center small">
                                <div class="col-4">
                                    <span class="text-muted d-block">الإجمالي</span>
                                    <strong class="text-primary" id="ptpTotal">0.00</strong>
                                </div>
                                <div class="col-4">
                                    <span class="text-muted d-block">الصافي</span>
                                    <strong class="text-success" id="ptpNet">0.00</strong>
                                </div>
                                <div class="col-4">
                                    <span class="text-muted d-block">المحدد</span>
                                    <strong class="text-warning" id="ptpSelectedSum">0.00</strong>
                                </div>
                            </div>
                        </div>

                        <div class="ptp-ops-grid p-2 border-top flex-shrink-0">
                            <div class="row g-2">
                                <div class="col-12">
                                    <button type="button" class="btn btn-primary w-100 py-2 fw-bold" id="ptpBtnAddItems" disabled>
                                        <i class="fas fa-cart-plus me-1"></i>إضافة أصناف
                                    </button>
                                </div>
                                <div class="col-6">
                                    <button type="button" class="btn btn-success w-100 ptp-op-btn" id="ptpBtnPayClose" disabled>
                                        <i class="fas fa-check-circle d-block mb-1 fa-lg"></i>
                                        <span>دفع وإغلاق</span>
                                    </button>
                                </div>
                                <div class="col-6">
                                    <button type="button" class="btn btn-primary w-100 ptp-op-btn" id="ptpBtnPayItems" disabled>
                                        <i class="fas fa-list-check d-block mb-1 fa-lg"></i>
                                        <span>دفع أصناف</span>
                                    </button>
                                </div>
                                <div class="col-6">
                                    <button type="button" class="btn btn-warning w-100 ptp-op-btn" id="ptpBtnMerge" title="دمج طاولات">
                                        <i class="fas fa-object-group d-block mb-1 fa-lg"></i>
                                        <span>دمج</span>
                                    </button>
                                </div>
                                <div class="col-6">
                                    <button type="button" class="btn btn-outline-warning w-100 ptp-op-btn" id="ptpBtnUnmerge" disabled>
                                        <i class="fas fa-unlink d-block mb-1 fa-lg"></i>
                                        <span>فك الدمج</span>
                                    </button>
                                </div>
                                <div class="col-12">
                                    <button type="button" class="btn btn-info w-100 ptp-op-btn text-white" id="ptpBtnTransfer" disabled>
                                        <i class="fas fa-exchange-alt d-block mb-1 fa-lg"></i>
                                        <span>تغيير الطاولة</span>
                                    </button>
                                </div>
                                <div class="col-12">
                                    <button type="button" class="btn btn-outline-secondary w-100 py-2" id="ptpBtnPrint" disabled>
                                        <i class="fas fa-print me-1"></i>طباعة فاتورة
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: tables grid -->
                    <div class="col-7 col-lg-7 overflow-auto p-2 pos-tables-right" id="ptpTablesGrid">
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status"></div>
                            <p class="mt-2 text-muted">جاري تحميل الطاولات...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>