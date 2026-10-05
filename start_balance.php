<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">




        <?php if (!empty($_SESSION['start_balance_error'])): ?>
            <div class="alert alert-danger" id="startBalanceAlert">
                <?= htmlspecialchars($_SESSION['start_balance_error'], ENT_QUOTES, 'UTF-8') ?>
            </div>
            <?php unset($_SESSION['start_balance_error']); ?>
        <?php endif; ?>
        <?php if (!empty($_SESSION['start_balance_ok'])): ?>
            <div class="alert alert-success" id="startBalanceAlert">
                <?= htmlspecialchars($_SESSION['start_balance_ok'], ENT_QUOTES, 'UTF-8') ?>
            </div>
            <?php unset($_SESSION['start_balance_ok']); ?>
        <?php endif; ?>
        <div class="alert alert-danger" id="startBalanceClientAlert" style="display:none"></div>

        <form action="do/doadd_start_balance.php" method="post" id="startBalanceForm">
            <input type="hidden" name="plug_manual" id="plug_manual" value="0">
            <div class="card">
                <div class="card-header">
                    <div class="filter">
                        <div class="row">
                            <div class="col-md-4">
                                فلتر
                                <select name="" id="accountFilter" class="form form-control">
                                    <option value="">كل الحسابات</option>
                                    <?php
                                    $sqlbasic = "SELECT * FROM acc_head WHERE isdeleted = 0 AND is_basic = 1";
                                    $resbasic = $conn->query($sqlbasic);
                                    while($rowbasic = $resbasic->fetch_assoc()) {
                                    ?>    
                                    <option value="<?= htmlspecialchars($rowbasic['code'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($rowbasic['aname'], ENT_QUOTES, 'UTF-8') ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                بحث    
                                <input type="text" id="searchInput" class="form form-control" placeholder="بحث">
                            </div>
                            <div class="col-md-4">
                                <div class="btn bg-yellow-400" id="edit_balance">
                                    <i class="fa fa-pen"></i>
                                    <br>
                                    <p>بدأ تعديل الارصدة الافتتاحية</p>
                                </div>

                                <button class="btn bg-green-400" type="submit" name="save_balance">
                                    <i class="fa fa-save"></i>
                                    <br>
                                    <p>حفظ التعديلات</p>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table">
                        <table class="table table-stripped table-hover table-sortable" id="accountsTable">
                            <thead>
                                <tr>
                                    <th>الكود</th>
                                    <th>اسم الحساب</th>
                                    <th>الرصيد الافتتاحي الجديد</th>
                                    <th>قيمة التسوية</th>
                                    <th>الرصيد الافتتاحي السابق</th>
                                    <th>الرصيد الحالي</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $sqlacc = "SELECT a.id, a.code, a.aname, a.balance, a.editable,
                                    (
                                        SELECT COALESCE(SUM(je.debit) - SUM(je.credit), 0)
                                        FROM journal_entries je
                                        INNER JOIN journal_heads jh ON jh.id = je.journal_id
                                        WHERE je.account_id = a.id
                                          AND je.isdeleted = 0
                                          AND jh.isdeleted = 0
                                          AND jh.details IN ('قيد الأرصدة الافتتاحية', 'أرصدة افتتاحية حسابات (قفل مدة)')
                                    ) AS opening_balance
                                    FROM acc_head a
                                    WHERE a.isdeleted = 0 AND a.is_basic = 0
                                    ORDER BY a.code";
                                $resacc = $conn->query($sqlacc);
                                while($rowacc = $resacc->fetch_assoc()) {
                                    $opening = (float) $rowacc['opening_balance'];
                                    $current = (float) $rowacc['balance'];
                                    $openingClass = $opening < 0 ? 'text-red-500' : '';
                                    $currentClass = $current < 0 ? 'text-red-500' : '';
                                    $isPlug = ($rowacc['code'] === '2211' || $rowacc['aname'] === 'الشريك الرئيسي');
                                    $locked = ((int) $rowacc['editable'] === 0) && !$isPlug;
                                ?>
                                    <tr data-code="<?= htmlspecialchars($rowacc['code'], ENT_QUOTES, 'UTF-8') ?>"<?= $isPlug ? ' data-plug="1"' : '' ?>>
                                        <td><?= htmlspecialchars($rowacc['code'], ENT_QUOTES, 'UTF-8') ?><input name="acc_id[]" type="text" class="acc_id" value="<?= (int) $rowacc['id'] ?>" hidden></td>
                                        <td><?= htmlspecialchars($rowacc['aname'], ENT_QUOTES, 'UTF-8') ?><?php if ($isPlug): ?><br><small>فرق الميزانية يُرحّل هنا ويمكن تعديله</small><?php endif; ?></td>
                                        <td><input name="newbalance[]" type="number" step="1" class="form form-control new-balance font-bold m-0 p-0 <?= $openingClass ?>" value="<?= htmlspecialchars((string) $opening, ENT_QUOTES, 'UTF-8') ?>" <?= $isPlug ? '' : 'readonly' ?> data-editable="<?= $locked ? '0' : '1' ?>"></td>
                                        <td><input type="text" readonly class="form form-control settle m-0 p-0" value="0.00"></td>
                                        <td class="old-balance <?= $openingClass ?>"><?= htmlspecialchars((string) $opening, ENT_QUOTES, 'UTF-8') ?></td>
                                        <td class="<?= $currentClass ?>"><?= htmlspecialchars((string) $current, ENT_QUOTES, 'UTF-8') ?></td>
                                    </tr>
                                <?php }?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col">
                            <b>اجمالي مدين</b>
                            <input type="text" id="total_debit" value="00.00" disabled>
                            <b>اجمالي دائن</b>
                            <input type="text" id="total_credit" value="00.00" disabled>
                            <p>الفرق</p>
                            <input type="text" id="total_diff" value="00.00" disabled>
                        </div>
                        <div class="col">
                            <b>فرق الميزانية</b>
                            <input type="text" id="total_diff_budget" value="00.00" disabled>
                        </div>
                    </div>
                </div>
            </div>
        </form>







        </div>
    </section>
</div>

<?php include 'includes/footer.php'; ?>

<script>
    function showClientError(message) {
        const box = document.getElementById('startBalanceClientAlert');
        box.textContent = message;
        box.style.display = 'block';
    }

    function hideClientError() {
        const box = document.getElementById('startBalanceClientAlert');
        box.style.display = 'none';
        box.textContent = '';
    }

    function paintBalance(input) {
        const row = input.closest('tr');
        const value = parseFloat(input.value);
        const shown = Number.isFinite(value) ? value : 0;
        if (shown < 0) {
            input.classList.add('text-red-500');
        } else {
            input.classList.remove('text-red-500');
        }
        const oldBalance = parseFloat(row.querySelector('.old-balance').textContent) || 0;
        row.querySelector('.settle').value = (oldBalance - shown).toFixed(2);
    }

    function applyPlug() {
        const plug = document.querySelector('tr[data-plug="1"] .new-balance');
        if (!plug) {
            return false;
        }
        let others = 0;
        document.querySelectorAll('tbody tr').forEach(function(row) {
            if (row.getAttribute('data-plug') === '1') {
                return;
            }
            others += parseFloat(row.querySelector('.new-balance').value) || 0;
        });
        plug.value = String(Math.round(-others));
        paintBalance(plug);
        return true;
    }

    // Enable editing of balances that are allowed to change
    document.getElementById('edit_balance').addEventListener('click', function() {
        const newBalances = document.querySelectorAll('.new-balance');
        newBalances.forEach(function(input) {
            if (input.getAttribute('data-editable') === '1' || input.closest('tr').getAttribute('data-plug') === '1') {
                input.readOnly = false;
            }
        });
    });

    // Filter by account code
    document.getElementById('accountFilter').addEventListener('change', function() {
        const selectedCode = this.value;
        filterAccounts(selectedCode);
    });

    // Search functionality
    document.getElementById('searchInput').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        searchAccounts(searchTerm);
    });

    // Update totals dynamically
    function updateTotals() {
        let totalDebit = 0;
        let totalCredit = 0;
        let totalDiff = 0;
        const rows = document.querySelectorAll('tbody tr');
        rows.forEach(function(row) {
            const newBalance = parseFloat(row.querySelector('.new-balance').value) || 0;
            const oldBalance = parseFloat(row.querySelector('.old-balance').textContent) || 0;
            const settle = parseFloat(row.querySelector('.settle').value) || 0;

            if (newBalance > 0) {
                totalDebit += newBalance;
            } else {
                totalCredit += Math.abs(newBalance);
            }
            totalDiff += Math.abs(newBalance - oldBalance);
        });

        document.getElementById('total_debit').value = totalDebit.toFixed(2);
        document.getElementById('total_credit').value = totalCredit.toFixed(2);
        document.getElementById('total_diff').value = totalDiff.toFixed(2);
        document.getElementById('total_diff_budget').value = Math.abs(totalDebit - totalCredit).toFixed(2);
    }

    // Call updateTotals on input change
    const balanceInputs = document.querySelectorAll('.new-balance');
    balanceInputs.forEach(function(input) {
        input.addEventListener('input', function() {
            const row = input.closest('tr');
            if (row.getAttribute('data-plug') === '1') {
                document.getElementById('plug_manual').value = '1';
                paintBalance(input);
            } else {
                document.getElementById('plug_manual').value = '0';
                paintBalance(input);
                applyPlug();
            }
            updateTotals();
            const debit = parseFloat(document.getElementById('total_debit').value) || 0;
            const credit = parseFloat(document.getElementById('total_credit').value) || 0;
            if (Math.abs(debit - credit) > 0.001) {
                showClientError('القيد غير متوازن. إجمالي المدين (' + debit.toFixed(2) + ') لا يساوي إجمالي الدائن (' + credit.toFixed(2) + '). الفرق: ' + (debit - credit).toFixed(2) + '. عدّل الشريك الرئيسي أو باقي الحسابات قبل الحفظ.');
            } else {
                hideClientError();
            }
        });
    });

    // Search accounts based on search input
    function searchAccounts(term) {
        const rows = document.querySelectorAll('tbody tr');
        rows.forEach(function(row) {
            const accountName = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
            if (accountName.includes(term)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    // Filter accounts by selected parent code
    function filterAccounts(code) {
        const rows = document.querySelectorAll('tbody tr');
        rows.forEach(function(row) {
            const accountCode = row.getAttribute('data-code') || '';
            if (!code || accountCode.indexOf(code) === 0) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    document.getElementById('startBalanceForm').addEventListener('submit', function(e) {
        updateTotals();
        const debit = parseFloat(document.getElementById('total_debit').value) || 0;
        const credit = parseFloat(document.getElementById('total_credit').value) || 0;
        const diff = debit - credit;
        if (Math.abs(diff) > 0.001) {
            e.preventDefault();
            showClientError('القيد غير متوازن. إجمالي المدين (' + debit.toFixed(2) + ') لا يساوي إجمالي الدائن (' + credit.toFixed(2) + '). الفرق: ' + diff.toFixed(2) + '. لم يُحفظ أي تغيير.');
            return;
        }

        const inputs = document.querySelectorAll('.new-balance');
        for (let i = 0; i < inputs.length; i++) {
            const value = parseFloat(inputs[i].value);
            if (!Number.isFinite(value) || Math.abs(value - Math.round(value)) > 0.0001) {
                e.preventDefault();
                showClientError('الرصيد الافتتاحي يجب أن يكون عدداً صحيحاً بدون كسور.');
                return;
            }
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        if (!applyPlug() ) {
            updateTotals();
            const debit = parseFloat(document.getElementById('total_debit').value) || 0;
            const credit = parseFloat(document.getElementById('total_credit').value) || 0;
            const diff = debit - credit;
            if (Math.abs(diff) > 0.001 && !document.getElementById('startBalanceAlert')) {
                showClientError('الرصيد الافتتاحي الحالي غير متوازن. إجمالي المدين (' + debit.toFixed(2) + ') لا يساوي إجمالي الدائن (' + credit.toFixed(2) + '). الفرق: ' + diff.toFixed(2) + '. حساب الشريك الرئيسي غير موجود لترحيل الفرق.');
            }
            return;
        }
        updateTotals();
    });

</script>
