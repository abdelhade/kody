<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
require_once __DIR__ . '/includes/sidebar.php';
require_once __DIR__ . '/classes/InvoiceProcessor.php';

$typeParam = isset($_GET['t']) ? trim((string) $_GET['t']) : 'recive';
$isPayment = ($typeParam === 'payment' || $typeParam === '2');
$proTybe = $isPayment
    ? InvoiceProcessor::ACCOUNTING_TYPES['MULTI_PAYMENT']
    : InvoiceProcessor::ACCOUNTING_TYPES['MULTI_RECEIPT'];
$typeKey = $isPayment ? 'payment' : 'recive';
$voucherType = $isPayment ? 'سند دفع متعدد' : 'سند قبض متعدد';
$voucherIcon = $isPayment ? 'fa-money-bill-wave' : 'fa-hand-holding-usd';
$partyLabel = $isPayment
    ? 'المورد / الحساب المصروف له (مدين)'
    : 'العميل / الحساب المقبوض منه (دائن)';
$fundLabel = $isPayment
    ? 'حساب الخزينة / الصندوق الصادر منه (دائن)'
    : 'حساب الخزينة / الصندوق المستلم (مدين)';

$stmtNext = $conn->prepare('SELECT COALESCE(MAX(CAST(pro_id AS UNSIGNED)), 0) + 1 AS next_id FROM ot_head WHERE pro_tybe = ?');
$stmtNext->bind_param('i', $proTybe);
$stmtNext->execute();
$prId = (int) ($stmtNext->get_result()->fetch_assoc()['next_id'] ?? 1);
$stmtNext->close();

$partyOptions = '<option value=""></option>';
$resacc = $conn->query(
    "SELECT id, code, aname FROM acc_head
     WHERE is_basic = 0 AND is_fund != 1
     ORDER BY aname ASC"
);
if ($resacc) {
    while ($rowacc = $resacc->fetch_assoc()) {
        $label = $rowacc['code'] . ' - ' . $rowacc['aname'];
        $partyOptions .= '<option value="' . (int) $rowacc['id'] . '">'
            . htmlspecialchars($label, ENT_QUOTES, 'UTF-8')
            . '</option>';
    }
}

$formToken = bin2hex(random_bytes(16));
$_SESSION['multi_voucher_form_token'] = $formToken;

$lineHtml = <<<HTML
<tr class="mv-line">
  <td class="mv-idx">1</td>
  <td>
    <select class="mv-acc" name="party_account[]">{$partyOptions}</select>
  </td>
  <td>
    <input class="k-input mv-amt" type="text" name="party_amount[]" inputmode="decimal" autocomplete="off" placeholder="0.00">
  </td>
  <td>
    <input class="k-input mv-note" type="text" name="party_note[]" autocomplete="off" placeholder="بيان السطر (اختياري)">
  </td>
  <td>
    <button type="button" class="mv-del" title="حذف السطر" aria-label="حذف السطر">
      <i class="fas fa-times"></i>
    </button>
  </td>
</tr>
HTML;
?>

<style>
.mv-types { display: flex; gap: 8px; margin-bottom: 16px; justify-content: center; flex-wrap: wrap; }
.mv-types a {
  border-radius: 30px;
  padding: 6px 18px;
  font-weight: 600;
  font-size: 0.875rem;
  text-decoration: none;
}
.mv-meta {
  display: grid;
  grid-template-columns: 140px 180px minmax(0, 1fr);
  gap: 12px;
  margin-bottom: 14px;
}
.mv-fund-wrap { margin-bottom: 16px; }
.mv-table-wrap { max-height: min(48vh, 420px); overflow: auto; }
.mv-table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  table-layout: fixed;
}
.mv-table th {
  font-size: 0.8rem;
  font-weight: 700;
  padding: 6px 8px;
  border-bottom: 1px solid var(--k-border);
  background: #f8fafc;
  color: var(--k-text-muted);
  position: sticky;
  top: 0;
  z-index: 2;
}
.mv-table td {
  padding: 6px 8px;
  border-bottom: 1px solid #f1f5f9;
  vertical-align: middle;
}
.mv-table .mv-idx { width: 36px; color: var(--k-text-muted); font-weight: 700; text-align: center; }
.mv-table .mv-amt { text-align: center; font-weight: 700; }
.mv-del {
  border: 0;
  background: transparent;
  color: var(--k-danger-text);
  width: 32px;
  height: 32px;
  border-radius: 8px;
  cursor: pointer;
}
.mv-del:hover { background: var(--k-danger-light); }
.mv-add {
  margin-top: 10px;
  border: 1px dashed var(--k-border);
  background: #fff;
  color: var(--k-text-muted);
  border-radius: 10px;
  padding: 8px 12px;
  font-weight: 700;
  width: 100%;
  cursor: pointer;
}
.mv-add:hover { border-color: var(--k-primary); color: var(--k-primary); }
.mv-foot {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  flex-wrap: wrap;
  padding: 12px 14px;
  border-top: 1px solid var(--k-border);
  background: #f8fafc;
}
.mv-total strong { font-size: 1.2rem; color: <?= $isPayment ? 'var(--k-danger-text)' : 'var(--k-success)' ?>; }
.mv-page .select2-container { width: 100% !important; }
.amount-large-input {
  font-size: 1.35rem !important;
  font-weight: 800 !important;
  text-align: center;
  color: <?= $isPayment ? '#dc2626' : '#059669' ?>;
  border-color: <?= $isPayment ? '#f87171' : '#10b981' ?> !important;
  background: <?= $isPayment ? '#fef2f2' : '#f0fdf4' ?> !important;
}
@media (max-width: 768px) {
  .mv-meta { grid-template-columns: 1fr 1fr; }
  .mv-meta .mv-info { grid-column: 1 / -1; }
}
</style>

<div class="content-wrapper k-page-wrapper mv-page">
  <?= kody_page_header(
      title: $voucherType,
      icon: $voucherIcon,
      breadcrumbs: [
          'الرئيسية' => 'dashboard.php',
          'السندات' => 'vouchers.php?t=' . $typeKey,
          $voucherType => '',
      ],
      actions: [
          [
              'label' => 'قائمة السندات',
              'url' => 'vouchers.php?t=' . $typeKey,
              'class' => 'k-btn-secondary',
              'icon' => 'fa-list',
          ],
      ]
  ) ?>

  <?php if (isset($_GET['error'])): ?>
    <div class="k-card" style="padding: 12px 16px; margin-bottom: 12px; color: var(--k-danger-text); background: var(--k-danger-light);">
      السند مش اتسجل. راجع الصندوق والأسطر والمبالغ.
    </div>
  <?php endif; ?>

  <div class="mv-types">
    <a href="add_multi_voucher.php?t=recive" class="k-btn <?= !$isPayment ? 'k-btn-success text-white' : 'k-btn-outline text-muted' ?>">
      <i class="fas fa-hand-holding-usd ml-1"></i> سند قبض متعدد
    </a>
    <a href="add_multi_voucher.php?t=payment" class="k-btn <?= $isPayment ? 'k-btn-danger text-white' : 'k-btn-outline text-muted' ?>">
      <i class="fas fa-money-bill-wave ml-1"></i> سند دفع متعدد
    </a>
  </div>

  <form id="multiVoucherForm" action="do/doadd_multi_voucher.php" method="post" autocomplete="off">
    <input type="hidden" name="form_token" value="<?= htmlspecialchars($formToken, ENT_QUOTES, 'UTF-8') ?>">
    <input type="hidden" name="tybe" value="<?= (int) $proTybe ?>">
    <input type="hidden" name="voucher_kind" value="<?= htmlspecialchars($typeKey, ENT_QUOTES, 'UTF-8') ?>">
    <input type="hidden" name="val" id="totalField" value="0">

    <div class="k-card">
      <div class="k-card-header">
        <h3 class="k-card-title"><i class="fas fa-info-circle text-primary ml-1"></i> بيانات السند</h3>
        <span class="k-badge <?= $isPayment ? 'k-badge-danger' : 'k-badge-success' ?>">
          <i class="fas <?= $voucherIcon ?>"></i> <?= $voucherType ?>
        </span>
      </div>
      <div class="k-card-body">
        <div class="mv-meta">
          <div>
            <label class="k-label">رقم السند</label>
            <input type="text" class="k-input font-weight-bold bg-light" readonly value="<?= $prId ?>">
          </div>
          <div>
            <label class="k-label required">التاريخ</label>
            <input type="date" name="vdate" class="k-input" value="<?= date('Y-m-d') ?>" required>
          </div>
          <div class="mv-info">
            <label class="k-label">البيان</label>
            <input type="text" name="info" class="k-input" placeholder="بيان السند" autofocus>
          </div>
        </div>

        <div class="mv-fund-wrap">
          <label class="k-label required"><?= htmlspecialchars($fundLabel, ENT_QUOTES, 'UTF-8') ?></label>
          <select name="fund_account" id="fundAccountSelect" class="k-select" required>
            <option value="">-- اختر الصندوق --</option>
            <?php
            $defFund = (int) (getUserDefault('def_fund', $conn) ?? 0);
            $resfunds = $conn->query(
                "SELECT a.id, a.code, a.aname
                 FROM acc_head a
                 WHERE a.is_basic = 0 AND a.is_fund = 1
                 ORDER BY a.aname ASC"
            );
            if ($resfunds):
                while ($rowf = $resfunds->fetch_assoc()):
                    if (UserDefaults::hides($conn, 'fund', $rowf['id'])) {
                        continue;
                    }
                    $selectedF = ($defFund > 0 && (int) $rowf['id'] === $defFund) ? 'selected' : '';
                    ?>
                    <option value="<?= (int) $rowf['id'] ?>" <?= $selectedF ?>>
                      <?= htmlspecialchars($rowf['code'] . ' - ' . $rowf['aname'], ENT_QUOTES, 'UTF-8') ?>
                    </option>
                <?php endwhile;
            endif;
            ?>
          </select>
          <p class="text-muted small mt-2 mb-0">
            الصندوق طرف واحد، وتحته تضيف حسابات متعددة بمبالغها. إجمالي الأسطر = مبلغ السند.
          </p>
        </div>
      </div>
    </div>

    <div class="k-card">
      <div class="k-card-header">
        <h3 class="k-card-title"><i class="fas fa-list-ul text-primary ml-1"></i> <?= htmlspecialchars($partyLabel, ENT_QUOTES, 'UTF-8') ?></h3>
        <span class="k-badge k-badge-secondary" id="lineCount">1 سطر</span>
      </div>
      <div class="k-card-body">
        <div class="mv-table-wrap">
          <table class="mv-table">
            <colgroup>
              <col style="width:36px">
              <col>
              <col style="width:130px">
              <col style="width:28%">
              <col style="width:42px">
            </colgroup>
            <thead>
              <tr>
                <th>#</th>
                <th>الحساب</th>
                <th>المبلغ</th>
                <th>بيان</th>
                <th></th>
              </tr>
            </thead>
            <tbody id="lines">
              <?= $lineHtml ?>
            </tbody>
          </table>
        </div>
        <template id="lineTpl"><?= $lineHtml ?></template>
        <button type="button" class="mv-add" id="addLine"><i class="fas fa-plus"></i> إضافة حساب</button>
      </div>

      <div class="mv-foot">
        <div class="mv-total">
          <span class="text-muted ml-2">إجمالي السند</span>
          <strong id="sumTotal">0.00</strong>
        </div>
        <div class="d-flex align-items-center gap-2">
          <span class="text-muted small" id="mvStatus">اختر الصندوق وأضف حساب بمبلغ</span>
          <a href="vouchers.php?t=<?= $typeKey ?>" class="k-btn k-btn-secondary">إلغاء</a>
          <button type="submit" class="k-btn k-btn-primary" id="mvSave" disabled>
            <i class="fas fa-save ml-1"></i> حفظ السند
          </button>
        </div>
      </div>
    </div>
  </form>
</div>

<?php
$extra_footer_scripts = <<<'JS'
<script>
$(function () {
  var $lines = $('#lines');
  var $save = $('#mvSave');
  var $fund = $('#fundAccountSelect');
  var state = { ok: false, totalCents: 0 };

  function parseAmt(v) {
    v = String(v || '').replace(/,/g, '.').replace(/[^\d.]/g, '');
    var parts = v.split('.');
    if (parts.length > 2) v = parts.shift() + '.' + parts.join('');
    var n = parseFloat(v);
    return isNaN(n) ? 0 : n;
  }

  function formatCents(c) {
    return (c / 100).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
  }

  function initAcc($sel) {
    if ($sel.data('select2')) return;
    $sel.select2({
      theme: 'bootstrap4',
      width: '100%',
      dir: 'rtl',
      placeholder: 'ابحث عن حساب',
      allowClear: true,
      language: {
        noResults: function () { return 'لا توجد حسابات'; },
        searching: function () { return 'جارٍ البحث...'; }
      }
    });
  }

  function renumber() {
    var n = $lines.children().length;
    $lines.children().each(function (i) {
      $(this).find('.mv-idx').text(i + 1);
    });
    $('#lineCount').text(n + (n === 1 ? ' سطر' : ' أسطر'));
  }

  function ensureMinLines() {
    while ($lines.children().length < 1) {
      addLine(false);
    }
  }

  function addLine(focus) {
    var $row = $($('#lineTpl').html());
    $lines.append($row);
    initAcc($row.find('.mv-acc'));
    renumber();
    if (focus) $row.find('.mv-acc').select2('open');
    refresh();
  }

  function refresh() {
    var totalCents = 0;
    var linesOk = 0;
    var seen = {};
    var dup = false;

    $lines.children().each(function () {
      var $row = $(this);
      var acc = parseInt($row.find('.mv-acc').val() || '0', 10) || 0;
      var amt = parseAmt($row.find('.mv-amt').val());
      var cents = Math.round(amt * 100);
      if (acc > 0 && cents > 0) {
        if (seen[acc]) dup = true;
        seen[acc] = true;
        totalCents += cents;
        linesOk++;
      }
    });

    var fundOk = parseInt($fund.val() || '0', 10) > 0;
    state.totalCents = totalCents;
    state.ok = fundOk && linesOk >= 1 && totalCents > 0 && !dup;

    $('#sumTotal').text(formatCents(totalCents));
    $('#totalField').val((totalCents / 100).toFixed(2));
    $save.prop('disabled', !state.ok);

    var msg = 'اختر الصندوق وأضف حساب بمبلغ';
    if (!fundOk) msg = 'اختر الصندوق أولاً';
    else if (dup) msg = 'في حساب متكرر في الأسطر';
    else if (linesOk < 1) msg = 'أضف حساب واحد على الأقل بمبلغ';
    else if (state.ok) msg = 'جاهز للحفظ (' + linesOk + ' حساب)';
    $('#mvStatus').text(msg);
  }

  $fund.select2({
    theme: 'bootstrap4',
    width: '100%',
    dir: 'rtl',
    placeholder: '-- اختر الصندوق --'
  }).on('change', refresh);

  $lines.find('.mv-acc').each(function () { initAcc($(this)); });
  renumber();
  refresh();

  $('#addLine').on('click', function () { addLine(true); });

  $lines.on('click', '.mv-del', function () {
    if ($lines.children().length <= 1) {
      var $row = $(this).closest('tr');
      $row.find('.mv-acc').val(null).trigger('change');
      $row.find('.mv-amt, .mv-note').val('');
      refresh();
      return;
    }
    $(this).closest('tr').remove();
    renumber();
    refresh();
  });

  $lines.on('change', '.mv-acc', refresh);
  $lines.on('input', '.mv-amt', refresh);

  $lines.on('keydown', '.mv-amt, .mv-note', function (e) {
    if (e.key === 'Enter') {
      e.preventDefault();
      addLine(true);
    }
  });

  $('#multiVoucherForm').on('submit', function (e) {
    refresh();
    if (!state.ok) {
      e.preventDefault();
      return false;
    }
    // شيل الأسطر الفاضية قبل الإرسال
    $lines.children().each(function () {
      var $row = $(this);
      var acc = parseInt($row.find('.mv-acc').val() || '0', 10) || 0;
      var amt = parseAmt($row.find('.mv-amt').val());
      if (!(acc > 0 && amt > 0)) {
        $row.find('.mv-acc, .mv-amt, .mv-note').prop('disabled', true);
      }
    });
    $save.prop('disabled', true);
  });
});
</script>
JS;
require_once __DIR__ . '/includes/footer.php';
