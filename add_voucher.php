<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
require_once __DIR__ . '/includes/sidebar.php';

$is_edit = isset($_GET['edit']);
$edit_id = $is_edit ? (int)$_GET['edit'] : 0;
$rowedit = null;

if ($is_edit && $edit_id > 0) {
    $stmtEdit = $conn->prepare("SELECT * FROM ot_head WHERE id = ? LIMIT 1");
    $stmtEdit->bind_param("i", $edit_id);
    $stmtEdit->execute();
    $rowedit = $stmtEdit->get_result()->fetch_assoc();
    $stmtEdit->close();
}

// تحديد نوع السند
$typeParam = isset($_GET['t']) ? trim((string)$_GET['t']) : (isset($_GET['tybe']) ? trim((string)$_GET['tybe']) : '');
$voucher_type = 'سند قبض (وارد)';
$voucher_icon = 'fa-hand-holding-usd';
$pro_tybe = 1;
$typeKey = 'recive';

if ($typeParam === 'payment' || $typeParam === '2' || ($rowedit && (int)$rowedit['pro_tybe'] === 2)) {
    $voucher_type = 'سند صرف ودفع (صادر)';
    $voucher_icon = 'fa-money-bill-wave';
    $pro_tybe = 2;
    $typeKey = 'payment';
} elseif ($typeParam === 'transfer' || $typeParam === '16' || ($rowedit && (int)$rowedit['pro_tybe'] === 16)) {
    $voucher_type = 'تحويل نقدية (بين الصناديق والبنوك)';
    $voucher_icon = 'fa-exchange-alt';
    $pro_tybe = 16;
    $typeKey = 'transfer';
} else {
    $voucher_type = 'سند قبض (وارد)';
    $voucher_icon = 'fa-hand-holding-usd';
    $pro_tybe = 1;
    $typeKey = 'recive';
}

// حساب رقم السند التالي
$pr_id = 1;
if ($is_edit && $rowedit) {
    $pr_id = $rowedit['pro_id'];
} else {
    $rowid = $conn->query("SELECT pro_id FROM ot_head WHERE pro_tybe = $pro_tybe ORDER BY id DESC LIMIT 1")->fetch_assoc();
    if ($rowid && isset($rowid['pro_id'])) {
        $pr_id = (int)$rowid['pro_id'] + 1;
    }
}
?>

<style>
/* Compact Balance Strips */
.k-compact-balance {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 6px 12px;
    font-size: 0.825rem;
    margin-top: 5px;
    line-height: 1.35;
    transition: all 0.2s ease;
}
.k-compact-balance .badge {
    font-size: 0.725rem;
    font-weight: 600;
    padding: 2px 6px;
    vertical-align: middle;
}
.amount-large-input {
    font-size: 1.45rem !important;
    font-weight: 800 !important;
    text-align: center;
    letter-spacing: 0.02em;
    color: #059669;
    border-color: #10b981 !important;
    background: #f0fdf4 !important;
}
.amount-large-input:focus {
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2) !important;
}
.k-voucher-type-pills {
    display: flex;
    gap: 8px;
    margin-bottom: 16px;
    justify-content: center;
}
.k-voucher-type-pills a {
    border-radius: 30px;
    padding: 6px 18px;
    font-weight: 600;
    font-size: 0.875rem;
    transition: all 0.2s ease;
    text-decoration: none;
}
</style>

<div class="content-wrapper k-page-wrapper">
  <?= kody_page_header(
      title: ($is_edit ? 'تعديل ' : 'إنشاء ') . $voucher_type,
      icon: $voucher_icon,
      breadcrumbs: [
          'الرئيسية' => 'dashboard.php',
          'السندات المالية' => 'vouchers.php' . ($pro_tybe === 1 ? '?t=recive' : ($pro_tybe === 16 ? '?t=transfer' : '?t=payment')),
          $voucher_type => ''
      ],
      actions: [
          [
              'label' => 'قائمة العمليات والسندات',
              'url'   => 'vouchers.php' . ($pro_tybe === 1 ? '?t=recive' : ($pro_tybe === 16 ? '?t=transfer' : '?t=payment')),
              'class' => 'k-btn-secondary',
              'icon'  => 'fa-arrow-right'
          ]
      ]
  ) ?>

  <div class="row justify-content-center">
    <div class="col-lg-10 col-xl-9">
      <?php if (!$is_edit): ?>
        <div class="k-voucher-type-pills flex-wrap">
          <a href="add_voucher.php?t=recive" class="k-btn <?= $pro_tybe === 1 ? 'k-btn-success text-white' : 'k-btn-outline text-muted' ?>">
            <i class="fas fa-hand-holding-usd ml-1"></i> سند قبض (وارد)
          </a>
          <a href="add_voucher.php?t=payment" class="k-btn <?= $pro_tybe === 2 ? 'k-btn-danger text-white' : 'k-btn-outline text-muted' ?>">
            <i class="fas fa-money-bill-wave ml-1"></i> سند صرف ودفع (صادر)
          </a>
          <a href="add_voucher.php?t=transfer" class="k-btn <?= $pro_tybe === 16 ? 'k-btn-info text-white' : 'k-btn-outline text-muted' ?>">
            <i class="fas fa-exchange-alt ml-1"></i> تحويل نقدية بين الصناديق
          </a>
        </div>

        <?php if ($pro_tybe === 2): ?>
          <div class="alert alert-light border d-flex align-items-center justify-content-between p-2 px-3 mb-3 small rounded shadow-sm">
            <span><i class="fas fa-info-circle text-primary ml-1"></i> هل تريد نقل أموال من خزينة لأخرى أو إيداع نقدية في البنك؟</span>
            <a href="add_voucher.php?t=transfer" class="k-btn k-btn-info k-btn-sm text-white py-1">
              <i class="fas fa-exchange-alt ml-1"></i> الذهاب لتحويل نقدية بين الصناديق
            </a>
          </div>
        <?php endif; ?>
      <?php endif; ?>

      <form action="<?= $is_edit ? ('do/doedit_voucher.php?id=' . $edit_id) : 'do/doadd_voucher.php' ?>" method="POST" id="voucherForm">
        <input type="hidden" name="tybe" value="<?= $pro_tybe ?>">
        <?php if (!$is_edit): 
            $formToken = bin2hex(random_bytes(16));
            $_SESSION['voucher_form_token'] = $formToken;
        ?>
          <input type="hidden" name="form_token" value="<?= $formToken ?>">
        <?php endif; ?>
        <?php if (isset($_GET['ins'])): ?>
          <input type="hidden" name="ins_id" value="<?= htmlspecialchars($_GET['ins']) ?>">
        <?php endif; ?>

        <!-- معلومات السند الأساسية -->
        <div class="k-card">
          <div class="k-card-header">
            <h3 class="k-card-title"><i class="fas fa-info-circle text-primary mr-1"></i> معلومات العملية الأساسية</h3>
            <span class="k-badge <?= $pro_tybe === 1 ? 'k-badge-success' : ($pro_tybe === 16 ? 'k-badge-info' : 'k-badge-danger') ?>">
              <i class="fas <?= $voucher_icon ?>"></i> <?= $voucher_type ?>
            </span>
          </div>

          <div class="k-card-body">
            <div class="row">
              <div class="col-md-3">
                <div class="k-form-group">
                  <label class="k-label">رقم السند</label>
                  <input type="text" name="voucher_id" class="k-input font-weight-bold bg-light" readonly value="<?= $pr_id ?>">
                </div>
              </div>

              <div class="col-md-3">
                <div class="k-form-group">
                  <label class="k-label required">تاريخ السند</label>
                  <input type="date" name="vdate" class="k-input" value="<?= $is_edit && $rowedit ? $rowedit['pro_date'] : date('Y-m-d') ?>" required>
                </div>
              </div>

              <div class="col-md-6">
                <div class="k-form-group">
                  <label class="k-label required font-weight-bold text-dark">مبلغ السند (القيمة)</label>
                  <input required type="number" name="val" class="k-input amount-large-input" 
                         id="voucherAmountInput" step="0.01" min="0.01" placeholder="0.00"
                         value="<?php 
                           if (isset($_GET['v'])) {
                               echo htmlspecialchars($_GET['v']);
                           } elseif ($is_edit && $rowedit) {
                               echo $rowedit['pro_value'];
                           }
                         ?>" autofocus>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- أطراف المعاملة المالية مع رصيد العميل والصندوق (قبل وبعد) -->
        <div class="k-card">
          <div class="k-card-header">
            <h3 class="k-card-title">
              <i class="fas <?= $pro_tybe === 16 ? 'fa-exchange-alt' : 'fa-wallet' ?> text-primary mr-1"></i> 
              <?= $pro_tybe === 16 ? 'أطراف التحويل النقدي (الصناديق والبنوك)' : 'أطراف المعاملة المالية والأرصدة' ?>
            </h3>
          </div>

          <div class="k-card-body">
            <?php if ($pro_tybe === 16): ?>
              <div id="sameFundAlert" class="alert alert-danger font-weight-bold mb-3" style="display: none;">
                <i class="fas fa-exclamation-triangle ml-1"></i> تنبيه: لا يمكن التحويل من وإلى نفس الصندوق! يرجى اختيار صندوقين مختلفين.
              </div>
            <?php endif; ?>

            <div class="row">
              <!-- طرف 1: في التحويل هو الصندوق المحول منه، وفي السند هو العميل/المورد -->
              <?php if ($pro_tybe === 16): ?>
                <!-- الصندوق المحول منه (المسلّم - دائن) -->
                <div class="col-md-6">
                  <div class="k-form-group mb-2">
                    <label class="k-label required mb-1 text-danger font-weight-bold">
                      <i class="fas fa-arrow-up ml-1"></i> الصندوق / الخزينة المحول منها (المسلّم - يخصم منه)
                    </label>
                    <select name="fund_account" class="k-select" id="fundAccountSelect" required>
                      <option value="">-- اختر الصندوق المسلّم --</option>
                      <?php
                      $defFund = (int)(getUserDefault('def_fund', $conn) ?? 0);
                      $resfunds = $conn->query("SELECT a.*, COALESCE((SELECT SUM(debit) - SUM(credit) FROM journal_entries WHERE account_id = a.id AND isdeleted = 0), 0) AS real_balance FROM acc_head a WHERE a.is_basic = 0 AND a.is_fund = 1 ORDER BY a.aname ASC");
                      if ($resfunds):
                          while ($rowf = $resfunds->fetch_assoc()):
                              if (!$is_edit && UserDefaults::hides($conn, 'fund', $rowf['id'])) continue;
                              $selectedF = '';
                              if ($is_edit && $rowedit) {
                                  if ((int)$rowf['id'] === (int)$rowedit['acc2']) $selectedF = 'selected';
                              } elseif (!$is_edit && $defFund > 0 && (int)$rowf['id'] === $defFund) {
                                  $selectedF = 'selected';
                              }
                              $fundBal = (float)($rowf['real_balance'] ?? 0);
                      ?>
                        <option value="<?= $rowf['id'] ?>" data-balance="<?= $fundBal ?>" <?= $selectedF ?>>
                          <?= htmlspecialchars($rowf['code'] . ' - ' . $rowf['aname']) ?>
                        </option>
                      <?php endwhile; endif; ?>
                    </select>

                    <!-- رصيد الصندوق المسلّم (قبل وبعد) -->
                    <div id="fundBalanceBox" class="k-compact-balance" style="display: none;">
                      <div class="d-flex align-items-center justify-content-between flex-wrap gap-1">
                        <div class="d-flex align-items-center gap-1">
                          <span class="text-muted small"><i class="fas fa-cash-register ml-1"></i>المسلّم قبل:</span>
                          <strong id="fundBalBefore" class="text-dark ml-1">0.00 ج.م</strong>
                          <span id="fundBalBeforeTag" class="badge badge-light border"></span>
                        </div>
                        <div class="d-flex align-items-center gap-1">
                          <i class="fas fa-arrow-left text-muted mx-1 small"></i>
                          <span class="text-muted small">المسلّم بعد:</span>
                          <strong id="fundBalAfter" class="font-weight-bold ml-1 text-primary">0.00 ج.م</strong>
                          <span id="fundBalAfterStatusTag" class="badge badge-light border"></span>
                          <span id="fundBalAfterTag" class="badge badge-light border"></span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- الصندوق المحول إليه (المستلم - مدين) -->
                <div class="col-md-6">
                  <div class="k-form-group mb-2">
                    <label class="k-label required mb-1 text-success font-weight-bold">
                      <i class="fas fa-arrow-down ml-1"></i> الصندوق / الخزينة المحول إليها (المستلم - يضاف إليه)
                    </label>
                    <select required name="account" class="k-select" id="accountSelect">
                      <option value="">-- اختر الصندوق المستلم --</option>
                      <?php
                      $resdestfunds = $conn->query("SELECT a.*, COALESCE((SELECT SUM(debit) - SUM(credit) FROM journal_entries WHERE account_id = a.id AND isdeleted = 0), 0) AS real_balance FROM acc_head a WHERE a.is_basic = 0 AND a.is_fund = 1 ORDER BY a.aname ASC");
                      if ($resdestfunds):
                          while ($rowd = $resdestfunds->fetch_assoc()):
                              $selectedD = '';
                              if ($is_edit && $rowedit) {
                                  if ((int)$rowd['id'] === (int)$rowedit['acc1']) $selectedD = 'selected';
                              }
                              $destBal = (float)($rowd['real_balance'] ?? 0);
                      ?>
                        <option value="<?= $rowd['id'] ?>" data-balance="<?= $destBal ?>" <?= $selectedD ?>>
                          <?= htmlspecialchars($rowd['code'] . ' - ' . $rowd['aname']) ?>
                        </option>
                      <?php endwhile; endif; ?>
                    </select>

                    <!-- رصيد الصندوق المستلم (قبل وبعد) -->
                    <div id="accBalanceBox" class="k-compact-balance" style="display: none;">
                      <div class="d-flex align-items-center justify-content-between flex-wrap gap-1">
                        <div>
                          <span class="text-muted small"><i class="fas fa-wallet ml-1"></i>المستلم قبل:</span>
                          <strong id="accBalBefore" class="text-dark ml-1">0.00 ج.م</strong>
                          <span id="accBalBeforeTag" class="badge badge-light border"></span>
                        </div>
                        <div>
                          <i class="fas fa-arrow-left text-muted mx-1 small"></i>
                          <span class="text-muted small">المستلم بعد:</span>
                          <strong id="accBalAfter" class="font-weight-bold ml-1 text-success">0.00 ج.م</strong>
                          <span id="accBalAfterTag" class="badge badge-light border"></span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

              <?php else: ?>
                <!-- طرف 1: العميل / الحساب المقابل (قبض أو صرف) -->
                <div class="col-md-6">
                  <div class="k-form-group mb-2">
                    <label class="k-label required mb-1">
                      <?= $pro_tybe === 1 ? 'العميل / الحساب المقبوض منه (دائن)' : 'المورد / الحساب المصروف له (مدين)' ?>
                    </label>
                    <select required name="account" class="k-select" id="accountSelect">
                      <option value="">-- اختر الحساب / العميل --</option>
                      <?php
                      $accUrl = isset($_GET['acc']) ? (int)$_GET['acc'] : 0;
                      if ($accUrl > 0) {
                          $resacc = $conn->query("SELECT a.*, COALESCE((SELECT SUM(debit) - SUM(credit) FROM journal_entries WHERE account_id = a.id AND isdeleted = 0), 0) AS real_balance FROM acc_head a WHERE a.id = $accUrl");
                      } else {
                          $resacc = $conn->query("SELECT a.*, COALESCE((SELECT SUM(debit) - SUM(credit) FROM journal_entries WHERE account_id = a.id AND isdeleted = 0), 0) AS real_balance FROM acc_head a WHERE a.is_basic = 0 AND a.is_fund != 1 ORDER BY a.aname ASC");
                      }
                      if ($resacc):
                          while ($rowacc = $resacc->fetch_assoc()):
                              $selected = '';
                              if ($is_edit && $rowedit) {
                                  if ($pro_tybe === 2 && (int)$rowacc['id'] === (int)$rowedit['acc1']) $selected = 'selected';
                                  if ($pro_tybe === 1 && (int)$rowacc['id'] === (int)$rowedit['acc2']) $selected = 'selected';
                              } elseif ($accUrl > 0 && (int)$rowacc['id'] === $accUrl) {
                                  $selected = 'selected';
                              }
                              $accBal = (float)($rowacc['real_balance'] ?? 0);
                      ?>
                        <option value="<?= $rowacc['id'] ?>" data-balance="<?= $accBal ?>" <?= $selected ?>>
                          <?= htmlspecialchars($rowacc['code'] . ' - ' . $rowacc['aname']) ?>
                        </option>
                      <?php endwhile; endif; ?>
                    </select>

                    <!-- رصيد العميل / الحساب (قبل وبعد) -->
                    <div id="accBalanceBox" class="k-compact-balance" style="display: none;">
                      <div class="d-flex align-items-center justify-content-between flex-wrap gap-1">
                        <div>
                          <span class="text-muted small">قبل:</span>
                          <strong id="accBalBefore" class="text-dark ml-1">0.00 ج.م</strong>
                          <span id="accBalBeforeTag" class="badge badge-light border"></span>
                        </div>
                        <div>
                          <i class="fas fa-arrow-left text-muted mx-1 small"></i>
                          <span class="text-muted small">بعد:</span>
                          <strong id="accBalAfter" class="font-weight-bold ml-1 text-success">0.00 ج.م</strong>
                          <span id="accBalAfterTag" class="badge badge-light border"></span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- طرف 2: الصندوق / الخزينة (قبض أو صرف) -->
                <div class="col-md-6">
                  <div class="k-form-group mb-2">
                    <label class="k-label required mb-1">
                      <?= $pro_tybe === 1 ? 'حساب الخزينة / الصندوق المستلم (مدين)' : 'حساب الخزينة / الصندوق الصادر منه (دائن)' ?>
                    </label>
                    <select name="fund_account" class="k-select" id="fundAccountSelect" required>
                      <?php
                      $defFund = (int)(getUserDefault('def_fund', $conn) ?? 0);
                      $resfunds = $conn->query("SELECT a.*, COALESCE((SELECT SUM(debit) - SUM(credit) FROM journal_entries WHERE account_id = a.id AND isdeleted = 0), 0) AS real_balance FROM acc_head a WHERE a.is_basic = 0 AND a.is_fund = 1 ORDER BY a.aname ASC");
                      if ($resfunds):
                          while ($rowf = $resfunds->fetch_assoc()):
                              if (!$is_edit && UserDefaults::hides($conn, 'fund', $rowf['id'])) continue;
                              $selectedF = '';
                              if ($is_edit && $rowedit) {
                                  if ($pro_tybe === 2 && (int)$rowf['id'] === (int)$rowedit['acc2']) $selectedF = 'selected';
                                  if ($pro_tybe === 1 && (int)$rowf['id'] === (int)$rowedit['acc1']) $selectedF = 'selected';
                              } elseif (!$is_edit && $defFund > 0 && (int)$rowf['id'] === $defFund) {
                                  $selectedF = 'selected';
                              }
                              $fundBal = (float)($rowf['real_balance'] ?? 0);
                      ?>
                        <option value="<?= $rowf['id'] ?>" data-balance="<?= $fundBal ?>" <?= $selectedF ?>>
                          <?= htmlspecialchars($rowf['code'] . ' - ' . $rowf['aname']) ?>
                        </option>
                      <?php endwhile; endif; ?>
                    </select>

                    <!-- رصيد الصندوق / الخزينة (قبل وبعد) -->
                    <div id="fundBalanceBox" class="k-compact-balance" style="display: none;">
                      <div class="d-flex align-items-center justify-content-between flex-wrap gap-1">
                        <div class="d-flex align-items-center gap-1">
                          <span class="text-muted small"><i class="fas fa-cash-register ml-1"></i>الخزينة قبل:</span>
                          <strong id="fundBalBefore" class="text-dark ml-1">0.00 ج.م</strong>
                          <span id="fundBalBeforeTag" class="badge badge-light border"></span>
                        </div>
                        <div class="d-flex align-items-center gap-1">
                          <i class="fas fa-arrow-left text-muted mx-1 small"></i>
                          <span class="text-muted small">الخزينة بعد:</span>
                          <strong id="fundBalAfter" class="font-weight-bold ml-1 text-primary">0.00 ج.م</strong>
                          <span id="fundBalAfterStatusTag" class="badge badge-light border"></span>
                          <span id="fundBalAfterTag" class="badge badge-light border"></span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <!-- التفاصيل والملاحظات -->
        <div class="k-card">
          <div class="k-card-header">
            <h3 class="k-card-title"><i class="fas fa-clipboard-list text-primary mr-1"></i> مركز التكلفة والبيان</h3>
          </div>

          <div class="k-card-body">
            <div class="row">
              <div class="col-md-4">
                <div class="k-form-group">
                  <label class="k-label">مركز التكلفة</label>
                  <select name="cost_center" class="k-select">
                    <option value="">-- بدون مركز تكلفة --</option>
                    <?php
                    $rescst = $conn->query("SELECT * FROM cost_centers ORDER BY cname ASC");
                    if ($rescst):
                        while ($rowcst = $rescst->fetch_assoc()):
                            $selectedCst = ($is_edit && $rowedit && (int)$rowcst['id'] === (int)$rowedit['cost_center']) ? 'selected' : '';
                    ?>
                      <option value="<?= $rowcst['id'] ?>" <?= $selectedCst ?>><?= htmlspecialchars($rowcst['cname']) ?></option>
                    <?php endwhile; endif; ?>
                  </select>
                </div>
              </div>

              <div class="col-md-8">
                <div class="k-form-group">
                  <label class="k-label">البيان / ملاحظات العملية</label>
                  <input type="text" name="info" class="k-input" placeholder="<?= $pro_tybe === 16 ? 'اكتب سبب التحويل أو رقم الإيصال/التحويل البنكي...' : 'اكتب بياناً تفصيلياً أو رقم الشيك/التحويل...' ?>" 
                         value="<?= $is_edit && $rowedit ? htmlspecialchars($rowedit['info'] ?? '') : '' ?>">
                </div>
              </div>
            </div>
          </div>

          <div class="k-card-header bg-light d-flex justify-content-between align-items-center flex-wrap gap-2 p-3">
            <div>
              <?php if ($is_edit && $edit_id > 0 && Can::check($pro_tybe === 1 ? 'receipts.delete' : 'payments.delete')): ?>
                <button type="button" class="k-btn k-btn-danger" id="btnDeleteCurrentVoucher" 
                        data-id="<?= $edit_id ?>" 
                        data-name="<?= ($pro_tybe === 16 ? 'تحويل نقدية رقم ' : ($pro_tybe === 1 ? 'سند قبض رقم ' : 'سند صرف رقم ')) . $pr_id ?>" 
                        data-type="<?= $pro_tybe ?>">
                  <i class="fas fa-trash-alt ml-1"></i> <?= $pro_tybe === 16 ? 'حذف التحويل' : 'حذف السند' ?>
                </button>
              <?php endif; ?>
            </div>
            <div class="d-flex align-items-center gap-2">
              <a href="vouchers.php<?= $pro_tybe === 1 ? '?t=recive' : ($pro_tybe === 16 ? '?t=transfer' : '?t=payment') ?>" class="k-btn k-btn-secondary ml-2">إلغاء</a>
              <button type="submit" class="k-btn k-btn-primary" id="btnSubmitVoucher">
                <i class="fas fa-save ml-1"></i> <?= $pro_tybe === 16 ? 'حفظ التحويل (F12)' : 'حفظ السند (F12)' ?>
              </button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<?php 
$extra_footer_scripts = '
<script>
$(document).ready(function() {
  const proType = ' . $pro_tybe . '; // 1 = recive (قبض), 2 = payment (صرف)
  const isEdit = ' . ($is_edit ? 'true' : 'false') . ';
  const editOpId = ' . (int)$edit_id . ';

  let clientBeforeBalance = 0;
  let hasClient = false;

  let fundBeforeBalance = 0;
  let hasFund = false;

  // Initialize Select2
  if ($("#accountSelect").length) {
    $("#accountSelect").select2({
      placeholder: "-- اختر الحساب أو العميل --",
      dir: "rtl",
      width: "100%"
    });
  }

  function formatMoney(amount, showSign = false) {
    const isNegative = amount < -0.001;
    const abs = Math.abs(amount);
    const formatted = abs.toLocaleString("en-US", { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + " ج.م";
    if (showSign && isNegative) {
      return "-" + formatted;
    }
    return formatted;
  }

  // 1. تحديث رصيد الطرف الأول / العميل / الخزينة المستلمة (قبل وبعد)
  function updateClientBalance() {
    if (!hasClient) {
      $("#accBalanceBox").slideUp(150);
      return;
    }
    $("#accBalanceBox").slideDown(150);

    const voucherAmount = parseFloat($("#voucherAmountInput").val()) || 0;

    // قبل السند / التحويل
    $("#accBalBefore").text(formatMoney(clientBeforeBalance, true));
    if (proType === 16) {
      // في التحويل: هذا صندوق مستلم
      if (clientBeforeBalance > 0.001) {
        $("#accBalBeforeTag").attr("class", "badge badge-success text-white").text("متوفر بالخزينة");
      } else if (clientBeforeBalance < -0.001) {
        $("#accBalBeforeTag").attr("class", "badge badge-danger text-white").text("عجز / مكشوف");
      } else {
        $("#accBalBeforeTag").attr("class", "badge badge-secondary").text("0.00");
      }
    } else {
      if (clientBeforeBalance > 0.001) {
        $("#accBalBeforeTag").attr("class", "badge badge-warning text-dark").text("مدين (عليه)");
      } else if (clientBeforeBalance < -0.001) {
        $("#accBalBeforeTag").attr("class", "badge badge-info text-white").text("دائن (له)");
      } else {
        $("#accBalBeforeTag").attr("class", "badge badge-secondary").text("0.00");
      }
    }

    // بعد السند / التحويل
    // في القبض (1): العميل يدفع، رصيده المدين ينقص
    // في الصرف (2): العميل يستلم، رصيده المدين يزيد
    // في التحويل (16): الصندوق المستلم يستلم، رصيده يزيد (+voucherAmount)
    let clientAfter;
    if (proType === 1) {
      clientAfter = clientBeforeBalance - voucherAmount;
    } else if (proType === 16) {
      clientAfter = clientBeforeBalance + voucherAmount;
    } else {
      clientAfter = clientBeforeBalance + voucherAmount;
    }
    $("#accBalAfter").text(formatMoney(clientAfter, true));

    if (proType === 16) {
      if (clientAfter > 0.001) {
        $("#accBalAfter").attr("class", "font-weight-bold ml-1 text-success");
      } else if (clientAfter < -0.001) {
        $("#accBalAfter").attr("class", "font-weight-bold ml-1 text-danger");
      } else {
        $("#accBalAfter").attr("class", "font-weight-bold ml-1 text-secondary");
      }
      if (voucherAmount > 0) {
        $("#accBalAfterTag").attr("class", "badge badge-success").text("+" + formatMoney(voucherAmount) + " مودع بالخزينة المستلمة");
      } else {
        $("#accBalAfterTag").attr("class", "badge badge-light border").text("لا تغيير");
      }
    } else {
      if (Math.abs(clientAfter) < 0.009) {
        $("#accBalAfter").attr("class", "font-weight-bold ml-1 text-success");
        $("#accBalAfterTag").attr("class", "badge badge-success").text("خالص تماماً");
      } else if (clientAfter > 0.009) {
        $("#accBalAfter").attr("class", "font-weight-bold ml-1 text-warning");
        $("#accBalAfterTag").attr("class", "badge badge-warning text-dark").text("متبقي عليه");
      } else {
        $("#accBalAfter").attr("class", "font-weight-bold ml-1 text-primary");
        $("#accBalAfterTag").attr("class", "badge badge-info text-white").text("له رصيد دائن");
      }
    }
  }

  // 2. تحديث رصيد الصندوق المسلّم / الخزينة (قبل وبعد)
  function updateFundBalance() {
    if (!hasFund) {
      $("#fundBalanceBox").slideUp(150);
      return;
    }
    $("#fundBalanceBox").slideDown(150);

    const voucherAmount = parseFloat($("#voucherAmountInput").val()) || 0;

    // الخزينة قبل السند / التحويل
    $("#fundBalBefore").text(formatMoney(fundBeforeBalance, true));
    if (fundBeforeBalance > 0.001) {
      $("#fundBalBeforeTag")
        .attr("class", "badge badge-success text-white")
        .text("متوفر بالخزينة");
    } else if (fundBeforeBalance < -0.001) {
      $("#fundBalBeforeTag")
        .attr("class", "badge badge-danger text-white")
        .text("عجز / مكشوف");
    } else {
      $("#fundBalBeforeTag")
        .attr("class", "badge badge-secondary")
        .text("0.00");
    }

    // الخزينة بعد السند / التحويل
    // في القبض (1): الصندوق يستلم وارد (+voucherAmount)
    // في الصرف (2): الصندوق يدفع صادر (-voucherAmount)
    // في التحويل (16): الصندوق المسلّم يخصم منه (-voucherAmount)
    let fundAfter = (proType === 1) ? (fundBeforeBalance + voucherAmount) : (fundBeforeBalance - voucherAmount);
    $("#fundBalAfter").text(formatMoney(fundAfter, true));

    // شارة حالة الخزينة بعد
    if (fundAfter > 0.001) {
      $("#fundBalAfter").attr("class", "font-weight-bold ml-1 text-success");
      $("#fundBalAfterStatusTag")
        .attr("class", "badge badge-success text-white")
        .text("متوفر بالخزينة");
    } else if (fundAfter < -0.001) {
      $("#fundBalAfter").attr("class", "font-weight-bold ml-1 text-danger");
      $("#fundBalAfterStatusTag")
        .attr("class", "badge badge-danger text-white")
        .text("عجز متبقي");
    } else {
      $("#fundBalAfter").attr("class", "font-weight-bold ml-1 text-secondary");
      $("#fundBalAfterStatusTag")
        .attr("class", "badge badge-secondary")
        .text("رصيد صفر");
    }

    // شارة حركة المبلغ
    if (voucherAmount > 0) {
      if (proType === 1) {
        $("#fundBalAfterTag")
          .attr("class", "badge badge-primary")
          .text("+" + formatMoney(voucherAmount) + " وارد للخزينة");
      } else if (proType === 16) {
        $("#fundBalAfterTag")
          .attr("class", "badge badge-danger text-white")
          .text("-" + formatMoney(voucherAmount) + " مسحوب من الخزينة المسلّمة");
      } else {
        $("#fundBalAfterTag")
          .attr("class", "badge badge-warning text-dark")
          .text("-" + formatMoney(voucherAmount) + " صادر من الخزينة");
      }
    } else {
      $("#fundBalAfterTag").attr("class", "badge badge-light border").text("لا تغيير بالمبلغ");
    }
  }

  function checkDuplicateFunds() {
    if (proType === 16) {
      const src = $("#fundAccountSelect").val();
      const dst = $("#accountSelect").val();
      if (src && dst && String(src) === String(dst)) {
        $("#sameFundAlert").slideDown(150);
        $("#btnSubmitVoucher").prop("disabled", true).addClass("disabled");
        return false;
      } else {
        $("#sameFundAlert").slideUp(150);
        $("#btnSubmitVoucher").prop("disabled", false).removeClass("disabled");
        return true;
      }
    }
    return true;
  }

  // عند اختيار الطرف المقابل / الصندوق المستلم
  $("#accountSelect").on("change", function() {
    const accountId = $(this).val();
    checkDuplicateFunds();
    if (!accountId) {
      hasClient = false;
      updateClientBalance();
      return;
    }

    hasClient = true;
    clientBeforeBalance = parseFloat($(this).find("option:selected").data("balance")) || 0;
    updateClientBalance();

    let url = "ajax/account_balance.php?id=" + accountId;
    if (isEdit && editOpId > 0) {
      url += "&exclude_op=" + editOpId;
    }

    $.getJSON(url, function(res) {
      if (res && res.success) {
        clientBeforeBalance = parseFloat(res.balance) || 0;
        updateClientBalance();
      }
    });
  });

  // عند اختيار الصندوق / الخزينة المسلّمة
  $("#fundAccountSelect").on("change", function() {
    const fundId = $(this).val();
    checkDuplicateFunds();
    if (!fundId) {
      hasFund = false;
      updateFundBalance();
      return;
    }

    hasFund = true;
    fundBeforeBalance = parseFloat($(this).find("option:selected").data("balance")) || 0;
    updateFundBalance();

    let url = "ajax/account_balance.php?id=" + fundId;
    if (isEdit && editOpId > 0) {
      url += "&exclude_op=" + editOpId;
    }

    $.getJSON(url, function(res) {
      if (res && res.success) {
        fundBeforeBalance = parseFloat(res.balance) || 0;
        updateFundBalance();
      }
    });
  });

  // عند كتابة أو تعديل مبلغ السند / التحويل
  $("#voucherAmountInput").on("input keyup change", function() {
    updateClientBalance();
    updateFundBalance();
  });

  // تفعيل التحديث عند تحميل الصفحة
  if ($("#accountSelect").val()) {
    $("#accountSelect").trigger("change");
  }
  if ($("#fundAccountSelect").val()) {
    $("#fundAccountSelect").trigger("change");
  }

  var isSubmitting = false;

  $("#voucherForm").on("submit", function(e) {
    if (proType === 16 && !checkDuplicateFunds()) {
      e.preventDefault();
      if (typeof Swal !== "undefined") {
        Swal.fire({
          type: "warning",
          icon: "warning",
          title: "تنبيه",
          text: "لا يمكن التحويل من وإلى نفس الصندوق!"
        });
      } else {
        alert("لا يمكن التحويل من وإلى نفس الصندوق!");
      }
      return false;
    }

    var valAmount = parseFloat($("#voucherAmountInput").val()) || 0;
    if (valAmount <= 0) {
      e.preventDefault();
      if (typeof Swal !== "undefined") {
        Swal.fire({
          type: "warning",
          icon: "warning",
          title: "تنبيه",
          text: "يرجى كتابة مبلغ صحيح أكبر من الصفر للسند!"
        });
      } else {
        alert("يرجى كتابة مبلغ صحيح أكبر من الصفر للسند!");
      }
      return false;
    }

    // منع الإرسال المتكرر (Prevent Double Submit)
    if (isSubmitting) {
      e.preventDefault();
      return false;
    }

    isSubmitting = true;
    var $submitBtn = $("#btnSubmitVoucher");
    $submitBtn.prop("disabled", true).addClass("disabled");
    $submitBtn.html('<i class="fas fa-spinner fa-spin ml-1"></i> جاري الحفظ...');
  });

  // حذف السند الحالي بواسطة SweetAlert
  var isDeletingVoucher = false;
  $("#btnDeleteCurrentVoucher").on("click", function(e) {
    e.preventDefault();
    if (isDeletingVoucher) return false;

    var $btn = $(this);
    if ($btn.hasClass("disabled") || $btn.prop("disabled")) return false;

    var vid = $btn.data("id");
    var vName = $btn.data("name") || ("سند رقم " + vid);
    var vType = parseInt($btn.data("type"), 10);
    var typeTitle = (vType === 16) ? "التحويل" : (vType === 1 ? "سند القبض" : "سند الصرف");
    var backUrl = "vouchers.php" + (vType === 1 ? "?t=recive" : (vType === 16 ? "?t=transfer" : "?t=payment"));

    if (typeof Swal === "undefined") {
      if (confirm("هل أنت متأكد من حذف " + vName + "؟")) {
        window.location.href = "do/dodel_voucher.php?del=" + vid;
      }
      return;
    }

    Swal.fire({
      title: "تأكيد حذف " + typeTitle,
      html: "هل أنت متأكد من رغبتك في حذف <strong>" + vName + "</strong>؟<br><small class=\"text-danger mt-2 d-inline-block\"><i class=\"fas fa-exclamation-triangle ml-1\"></i>سيتم إلغاء أثر هذا السند المالي وحذف كافة قيوده المحاسبية نهائياً.</small>",
      type: "warning",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#ef4444",
      cancelButtonColor: "#64748b",
      confirmButtonText: "<i class=\"fas fa-trash-alt ml-1\"></i> نعم، احذف",
      cancelButtonText: "إلغاء",
      reverseButtons: true,
      focusCancel: true,
      showLoaderOnConfirm: true,
      preConfirm: function() {
        if (isDeletingVoucher) return false;
        isDeletingVoucher = true;
        $btn.prop("disabled", true).addClass("disabled");

        return $.ajax({
          url: "do/dodel_voucher.php",
          type: "POST",
          data: { del: vid, ajax: 1 },
          dataType: "json"
        }).then(function(res) {
          if (!res || !res.success) {
            isDeletingVoucher = false;
            $btn.prop("disabled", false).removeClass("disabled");
            throw new Error(res && res.error ? res.error : "تعذر حذف السند");
          }
          return res;
        }).catch(function(err) {
          isDeletingVoucher = false;
          $btn.prop("disabled", false).removeClass("disabled");
          var msg = (err.responseJSON && err.responseJSON.error) 
                    ? err.responseJSON.error 
                    : (err.message || "حدث خطأ أثناء الاتصال بالخادم");
          Swal.showValidationMessage(msg);
        });
      },
      allowOutsideClick: function() { return !Swal.isLoading(); }
    }).then(function(result) {
      if (result.value || result.isConfirmed) {
        Swal.fire({
          type: "success",
          icon: "success",
          title: "تم الحذف بنجاح",
          text: "تم حذف " + vName + " بنجاح، جاري تحويلك...",
          timer: 1600,
          showConfirmButton: false
        }).then(function() {
          window.location.href = backUrl;
        });
        setTimeout(function() {
          window.location.href = backUrl;
        }, 1600);
      } else {
        isDeletingVoucher = false;
        $btn.prop("disabled", false).removeClass("disabled");
      }
    });
  });

  // اختصار F12 للحفظ
  $(document).keydown(function(e) {
    if (e.key === "F12") {
      e.preventDefault();
      if (!isSubmitting) {
        $("#voucherForm").submit();
      }
    }
  });
});
</script>
';
require_once __DIR__ . '/includes/footer.php'; 
?>
