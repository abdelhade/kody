<!-- مودال الدفع (نفس واجهة pos_barcode) -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered payment-modal-dialog">
        <div class="modal-content payment-modal-content">
            <div class="modal-header payment-modal-header py-2 px-3">
                <h6 class="modal-title mb-0" id="paymentModalLabel">الدفع والإجماليات</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body payment-modal-body p-2">
                <div class="pay-summary-strip">
                    <div class="pay-stat">
                        <span class="pay-stat-lbl">الإجمالي</span>
                        <strong class="pay-stat-val text-primary" id="modal_total">0.00  </strong>
                    </div>
                    <div class="pay-stat">
                        <span class="pay-stat-lbl">الصافي</span>
                        <strong class="pay-stat-val text-success" id="modal_net">0.00  </strong>
                    </div>
                    <div class="pay-stat">
                        <span class="pay-stat-lbl">الباقي</span>
                        <strong class="pay-stat-val text-danger" id="modal_change">0.00  </strong>
                    </div>
                </div>

                <div class="row g-1 pay-row">
                    <div class="col-6">
                        <label class="pay-lbl" for="modal_discperc">خصم %</label>
                        <input class="form-control form-control-sm text-center pay-num" type="text" inputmode="decimal"
                            id="modal_discperc" value="0" autocomplete="off">
                    </div>
                    <div class="col-6">
                        <label class="pay-lbl" for="modal_discount">ق. الخصم</label>
                        <input class="form-control form-control-sm text-center pay-num" type="text" inputmode="decimal"
                            id="modal_discount" value="0" autocomplete="off">
                    </div>
                </div>

                <div class="pay-old-paid" id="old_payment_container" style="display: none;">
                    <span><i class="fas fa-history"></i> مدفوع سابقاً</span>
                    <strong class="text-info" id="old_paid_display">0.00  </strong>
                </div>

                <div class="pay-method-row">
                    <span class="pay-lbl mb-1">طريقة الدفع</span>
                    <div class="btn-group w-100 pay-method-group" role="group" id="pay_method_btns">
                        <button type="button" class="btn btn-success btn-sm active" data-pay-method="cash">
                            <i class="fas fa-money-bill-wave"></i> كاش
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-sm" data-pay-method="bank">
                            <i class="fas fa-university"></i> شبكة
                        </button>
                        <button type="button" class="btn btn-outline-warning btn-sm" data-pay-method="mixed">
                            <i class="fas fa-coins"></i> مختلط
                        </button>
                    </div>
                    <input type="hidden" id="pay_method" name="pay_method" value="cash">
                </div>

                <div class="row g-1 pay-row" id="pay_accounts_row">
                    <div class="col-12" id="pay_fund_col">
                        <label class="pay-lbl" for="payment_fund_id"><i class="fas fa-wallet"></i> الصندوق</label>
                        <select class="form-select form-select-sm pay-select" id="payment_fund_id">
                            <?php
                            $resfund = $conn->query("SELECT * FROM `acc_head` WHERE is_fund = 1 AND is_basic = 0 AND isdeleted = 0 ORDER BY aname");
                            while ($rowfund = $resfund->fetch_assoc()) {
                                $selected = '';
                                if (!empty($rowstg['def_pos_fund']) && $rowstg['def_pos_fund'] == $rowfund['id']) {
                                    $selected = 'selected';
                                }
                                if (($selected = UserDefaults::pick($conn, 'fund', $rowfund['id'], $selected, true)) === null) continue;
                            ?>
                            <option <?= $selected ?> value="<?= (int)$rowfund['id'] ?>"><?= htmlspecialchars($rowfund['aname']) ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-12 d-none" id="pay_bank_col">
                        <label class="pay-lbl" for="payment_bank_id"><i class="fas fa-university"></i> البنك / الشبكة</label>
                        <select class="form-select form-select-sm pay-select" id="payment_bank_id">
                            <option value="">-- اختر البنك --</option>
                            <?php
                            $banks_parent_id = 0;
                            $banksParentRes = $conn->query("SELECT id FROM acc_head WHERE code = '124' AND isdeleted = 0 LIMIT 1");
                            if ($banksParentRes && ($banksParentRow = $banksParentRes->fetch_assoc())) {
                                $banks_parent_id = (int)$banksParentRow['id'];
                            }
                            $bankSql = "SELECT * FROM `acc_head` WHERE is_basic = 0 AND isdeleted = 0 AND (code LIKE '124%'";
                            if ($banks_parent_id > 0) {
                                $bankSql .= " OR parent_id = " . $banks_parent_id;
                            }
                            $bankSql .= ") ORDER BY aname";
                            $resbank = $conn->query($bankSql);
                            $first_bank_selected = false;
                            while ($resbank && $rowbank = $resbank->fetch_assoc()) {
                                if ((string)$rowbank['code'] === '124') continue;
                                $bankSel = !$first_bank_selected ? 'selected' : '';
                                $first_bank_selected = true;
                            ?>
                            <option <?= $bankSel ?> value="<?= (int)$rowbank['id'] ?>"><?= htmlspecialchars($rowbank['aname']) ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="row g-1 pay-row" id="pay_amounts_row">
                    <div class="col-12" id="pay_cash_amount_col">
                        <label class="pay-lbl" for="modal_paid_cash"><i class="fas fa-money-bill"></i> المدفوع كاش</label>
                        <input class="form-control form-control-sm text-center fw-bold pay-num" type="text" inputmode="decimal"
                            id="modal_paid_cash" value="0.00" autocomplete="off">
                    </div>
                    <div class="col-6 d-none" id="pay_bank_amount_col">
                        <label class="pay-lbl" for="modal_paid_bank"><i class="fas fa-credit-card"></i> المدفوع شبكة</label>
                        <input class="form-control form-control-sm text-center fw-bold pay-num" type="text" inputmode="decimal"
                            id="modal_paid_bank" value="0.00" autocomplete="off" readonly>
                    </div>
                </div>
            </div>
            <div class="modal-footer payment-modal-footer py-2 px-2">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" id="paymentSaveBtn" class="btn btn-success btn-sm" onclick="submitPOS('save');">حفظ</button>
                <button type="button" id="paymentPrintBtn" class="btn btn-primary btn-sm" onclick="submitPOS('cash');">حفظ وطباعة</button>
            </div>
        </div>
    </div>
</div>
