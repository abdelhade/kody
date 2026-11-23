<?php
// روابط السندات المتعددة القديمة → الصفحة الجديدة
$legacyT = isset($_GET['t']) ? (string) $_GET['t'] : '';
if ($legacyT === 'recive' || $legacyT === 'payment') {
    header('Location: add_multi_voucher.php?t=' . $legacyT);
    exit;
}
?>
<?php include('includes/header.php') ?>
<?php include('includes/navbar.php') ?>
<?php include('includes/sidebar.php') ?>
<?php require_once __DIR__ . '/classes/InvoiceProcessor.php'; ?>

<?php
$typeName = 'قيد متعدد';
$typeId = InvoiceProcessor::ACCOUNTING_TYPES['MULTI_JOURNAL'];
$typeHint = 'قيد متعدد: أضف أسطر مدين ودائن متوازنة لأي حسابات.';
$pageIcon = 'fa-list-ol';

$nextRow = $conn->query('SELECT COALESCE(MAX(journal_id), 0) + 1 AS next_id FROM journal_heads')->fetch_assoc();
$jrnlid = (int) ($nextRow['next_id'] ?? 1);

$accountOptions = '<option value=""></option>';
$resacc = $conn->query('SELECT id, code, aname FROM acc_head WHERE is_basic = 0 ORDER BY code');
if ($resacc) {
    while ($rowacc = $resacc->fetch_assoc()) {
        $label = $rowacc['code'] . ' — ' . $rowacc['aname'];
        $accountOptions .= '<option value="' . (int) $rowacc['id'] . '">'
            . htmlspecialchars($label, ENT_QUOTES, 'UTF-8')
            . '</option>';
    }
}

$lineHtml = <<<HTML
<tr class="mj-line">
  <td class="mj-idx">1</td>
  <td>
    <select class="mj-acc">{$accountOptions}</select>
  </td>
  <td><input class="k-input mj-amt mj-debit" type="text" inputmode="decimal" autocomplete="off" placeholder="0.00"></td>
  <td><input class="k-input mj-amt mj-credit" type="text" inputmode="decimal" autocomplete="off" placeholder="0.00"></td>
  <td>
    <button type="button" class="mj-del" title="حذف السطر" aria-label="حذف السطر">
      <i class="fas fa-times"></i>
    </button>
  </td>
</tr>
HTML;

$headerActions = [];
if (Can::canVisit('daily_journal.php')) {
    $headerActions[] = [
        'label' => 'القيود اليومية',
        'url' => 'daily_journal.php',
        'class' => 'k-btn-secondary',
        'icon' => 'fa-list',
    ];
}
?>

<style>
.mj-page .mj-card { overflow: visible; }
.mj-page .k-card-body { padding: 12px 14px 8px; }
.mj-meta {
  display: grid;
  grid-template-columns: 150px 180px minmax(0, 1fr);
  gap: 12px;
  margin-bottom: 14px;
}
.mj-hint {
  margin: 0 0 14px;
  color: var(--k-text-muted);
  font-size: 0.85rem;
  font-weight: 500;
}
.mj-table-wrap { max-height: min(52vh, 480px); overflow: auto; }
.mj-table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  table-layout: fixed;
}
.mj-table th {
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
.mj-table td {
  padding: 6px;
  border-bottom: 1px solid var(--k-border-subtle);
  vertical-align: middle;
}
.mj-table tbody tr:hover td { background: #f8fafc; }
.mj-idx { text-align: center; color: var(--k-text-light); font-weight: 700; }
.mj-th-debit { color: var(--k-success-text); background: var(--k-success-light); text-align: center; }
.mj-th-credit { color: var(--k-primary-text); background: var(--k-primary-light); text-align: center; }
.mj-amt {
  text-align: center;
  font-weight: 700;
  font-variant-numeric: tabular-nums;
  font-size: 0.95rem;
  padding: 0.4rem 0.35rem;
}
.mj-debit.has-val {
  background: var(--k-success-light);
  color: var(--k-success-text);
  border-color: #6ee7b7;
}
.mj-credit.has-val {
  background: var(--k-primary-light);
  color: var(--k-primary-text);
  border-color: #93c5fd;
}
.mj-amt:disabled {
  background: #f8fafc;
  color: #cbd5e1;
  cursor: not-allowed;
}
.mj-line.is-debit td.mj-idx { box-shadow: inset -3px 0 0 var(--k-success); }
.mj-line.is-credit td.mj-idx { box-shadow: inset -3px 0 0 var(--k-primary); }
.mj-line.is-invalid td.mj-idx { box-shadow: inset -3px 0 0 var(--k-danger); }
.mj-line.is-invalid .select2-selection {
  border-color: var(--k-danger) !important;
}
.mj-del {
  width: 34px;
  height: 34px;
  border-radius: 8px;
  border: 1px solid transparent;
  background: transparent;
  color: #94a3b8;
  cursor: pointer;
}
.mj-del:hover { background: var(--k-danger-light); color: var(--k-danger); }
.mj-add {
  width: 100%;
  margin-top: 10px;
  border: 1px dashed #cbd5e1;
  background: #f8fafc;
  color: var(--k-text-muted);
  border-radius: 10px;
  padding: 10px;
  font-weight: 700;
  cursor: pointer;
}
.mj-add:hover { background: var(--k-primary-light); color: var(--k-primary-text); border-color: #93c5fd; }
.mj-foot {
  display: grid;
  gap: 10px;
  padding: 12px 14px;
  background: #fff;
  border-top: 1px solid var(--k-border);
  border-radius: 0 0 var(--k-radius-lg) var(--k-radius-lg);
}
.mj-totals {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 8px;
}
.mj-total {
  border: 1px solid var(--k-border);
  border-radius: 10px;
  padding: 8px 12px;
  background: #fff;
}
.mj-total span {
  display: block;
  font-size: 0.75rem;
  color: var(--k-text-muted);
  font-weight: 600;
}
.mj-total strong {
  display: block;
  margin-top: 2px;
  font-size: 1.15rem;
  font-variant-numeric: tabular-nums;
  color: var(--k-text-main);
}
.mj-total.is-debit strong { color: var(--k-success-text); }
.mj-total.is-credit strong { color: var(--k-primary-text); }
.mj-total.is-ok { background: var(--k-success-light); border-color: #6ee7b7; }
.mj-total.is-ok strong { color: var(--k-success-text); }
.mj-total.is-bad { background: var(--k-danger-light); border-color: #fca5a5; }
.mj-total.is-bad strong { color: var(--k-danger-text); }
.mj-actions { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
.mj-status { font-size: 0.8rem; font-weight: 600; color: var(--k-text-muted); min-width: 180px; }
.mj-page .select2-container { width: 100% !important; max-width: 100%; }
.mj-page .select2-container--bootstrap4 .select2-selection__rendered {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
.mj-page .select2-container--bootstrap4 .select2-selection {
  min-height: 38px;
  border-radius: 10px;
  border-color: var(--k-border);
}
.mj-page button.k-btn:disabled { opacity: 0.5; cursor: not-allowed; box-shadow: none; }
@media (max-width: 768px) {
  .mj-meta { grid-template-columns: 1fr 1fr; }
  .mj-details { grid-column: 1 / -1; }
  .mj-foot { flex-direction: column; align-items: stretch; }
  .mj-actions { justify-content: stretch; }
  .mj-actions .k-btn { flex: 1; }
}
</style>

<div class="content-wrapper k-page-wrapper mj-page">
  <?= kody_page_header(
      title: $typeName,
      icon: $pageIcon,
      breadcrumbs: [
          'الرئيسية' => 'dashboard.php',
          'الحسابات العامة' => '',
          $typeName => '',
      ],
      actions: $headerActions
  ) ?>

  <?php if (isset($_GET['error'])): ?>
    <div class="k-card" style="padding: 12px 16px; margin-bottom: 12px; color: var(--k-danger-text); background: var(--k-danger-light);">
      القيد مش اتسجل. راجع إن الطرفين متوازنين والحسابات مختارة.
    </div>
  <?php endif; ?>

  <form id="journalForm" action="do/doadd_multijournal.php" method="post" autocomplete="off">
    <input type="hidden" name="pro_tybe" value="<?= (int) $typeId ?>">
    <input type="hidden" name="total" id="totalField" value="0">
    <div id="payload"></div>

    <div class="k-card mj-card">
      <div class="k-card-header">
        <h3 class="k-card-title"><i class="fas fa-list-ol text-primary"></i> أسطر القيد</h3>
        <div class="k-card-tools"><span class="k-badge k-badge-secondary" id="lineCount">2 أسطر</span></div>
      </div>
      <div class="k-card-body">
        <div class="mj-meta">
          <div>
            <label class="k-label" for="journal_id">رقم دفتري</label>
            <input id="journal_id" name="journal_id" type="text" class="k-input" value="<?= $jrnlid ?>">
          </div>
          <div>
            <label class="k-label" for="jdate">التاريخ</label>
            <input id="jdate" name="jdate" type="date" class="k-input" value="<?= date('Y-m-d') ?>" required>
          </div>
          <div class="mj-details">
            <label class="k-label required" for="details">البيان</label>
            <input id="details" name="details" type="text" class="k-input" placeholder="بيان القيد" autofocus>
          </div>
        </div>
        <p class="mj-hint"><?= htmlspecialchars($typeHint, ENT_QUOTES, 'UTF-8') ?> الحفظ يتفتح لما الطرفين يتساووا.</p>

        <div class="mj-table-wrap">
          <table class="mj-table">
            <colgroup>
              <col style="width:36px">
              <col>
              <col style="width:108px">
              <col style="width:108px">
              <col style="width:42px">
            </colgroup>
            <thead>
              <tr>
                <th>#</th>
                <th>الحساب</th>
                <th class="mj-th-debit">مدين</th>
                <th class="mj-th-credit">دائن</th>
                <th></th>
              </tr>
            </thead>
            <tbody id="lines">
              <?= $lineHtml . $lineHtml ?>
            </tbody>
          </table>
        </div>
        <template id="lineTpl"><?= $lineHtml ?></template>
        <button type="button" class="mj-add" id="addLine"><i class="fas fa-plus"></i> إضافة سطر</button>
      </div>

      <div class="mj-foot">
        <div class="mj-totals">
          <div class="mj-total is-debit">
            <span>إجمالي مدين</span>
            <strong id="sumDebit">0.00</strong>
          </div>
          <div class="mj-total is-credit">
            <span>إجمالي دائن</span>
            <strong id="sumCredit">0.00</strong>
          </div>
          <div class="mj-total" id="diffBox">
            <span>الفرق</span>
            <strong id="sumDiff">0.00</strong>
          </div>
        </div>
        <div class="mj-actions">
          <span class="mj-status" id="mjStatus">اكتب البيان وأضف سطر مدين وسطر دائن</span>
          <button type="button" class="k-btn k-btn-secondary" id="mjClear"><i class="fas fa-eraser"></i> مسح</button>
          <button type="submit" class="k-btn k-btn-primary" id="mjSave" disabled><i class="fas fa-check"></i> حفظ القيد</button>
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
  var $save = $('#mjSave');
  var state = { ok: false, debitCents: 0, creditCents: 0 };

  function parseAmt(v) {
    v = String(v || '').replace(/,/g, '.').replace(/[^\d.]/g, '');
    var parts = v.split('.');
    if (parts.length > 2) {
      v = parts.shift() + '.' + parts.join('');
    }
    var n = parseFloat(v);
    return isNaN(n) ? 0 : n;
  }

  function formatCents(c) {
    var neg = c < 0;
    var s = (Math.abs(c) / 100).toFixed(2);
    s = s.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    return neg ? '-' + s : s;
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
      $(this).find('.mj-idx').text(i + 1);
    });
    $('#lineCount').text(n + (n === 1 ? ' سطر' : ' أسطر'));
  }

  function addLine(open) {
    var node = document.getElementById('lineTpl').content.cloneNode(true);
    $lines.append(node);
    var $row = $lines.children().last();
    initAcc($row.find('.mj-acc'));
    renumber();
    if (open) {
      $row.find('.mj-acc').select2('open');
    }
  }

  function clearRow($tr) {
    var $acc = $tr.find('.mj-acc');
    $acc.val(null).trigger('change');
    $tr.find('.mj-amt').val('').prop('disabled', false).removeClass('has-val');
    $tr.removeClass('is-debit is-credit is-invalid');
  }

  function syncSide($input) {
    var $tr = $input.closest('tr');
    var $debit = $tr.find('.mj-debit');
    var $credit = $tr.find('.mj-credit');
    var debit = parseAmt($debit.val());
    var credit = parseAmt($credit.val());
    if ($input.hasClass('mj-debit') && debit > 0) {
      $credit.val('').prop('disabled', true).removeClass('has-val');
      credit = 0;
    } else if ($input.hasClass('mj-credit') && credit > 0) {
      $debit.val('').prop('disabled', true).removeClass('has-val');
      debit = 0;
    } else if (debit === 0 && credit === 0) {
      $debit.add($credit).prop('disabled', false);
    }
    $debit.toggleClass('has-val', debit > 0);
    $credit.toggleClass('has-val', credit > 0);
    $tr.toggleClass('is-debit', debit > 0);
    $tr.toggleClass('is-credit', credit > 0);
    var acc = $tr.find('.mj-acc').val();
    $tr.toggleClass('is-invalid', (debit > 0 || credit > 0) && !acc);
  }

  function recalc() {
    var debit = 0;
    var credit = 0;
    var missingAccount = false;
    var debitLines = 0;
    var creditLines = 0;

    $lines.children().each(function () {
      var $tr = $(this);
      var d = parseAmt($tr.find('.mj-debit').val());
      var c = parseAmt($tr.find('.mj-credit').val());
      var acc = $tr.find('.mj-acc').val();
      if (d > 0) {
        debit += Math.round(d * 100);
        debitLines++;
        if (!acc) missingAccount = true;
      } else if (c > 0) {
        credit += Math.round(c * 100);
        creditLines++;
        if (!acc) missingAccount = true;
      }
    });

    var diff = debit - credit;
    var details = $.trim($('#details').val());
    var msg = 'اكتب البيان وأضف سطر مدين وسطر دائن';
    var ok = false;

    if (missingAccount) {
      msg = 'اختَر حساب لكل سطر فيه مبلغ';
    } else if (!details) {
      msg = 'اكتب بيان القيد';
    } else if (debitLines === 0 || creditLines === 0) {
      msg = 'محتاج سطر مدين وسطر دائن على الأقل';
    } else if (diff !== 0) {
      msg = 'الفرق ' + formatCents(diff) + ' — لازم المدين يساوي الدائن';
    } else if (debit <= 0) {
      msg = 'أدخل مبالغ أكبر من صفر';
    } else {
      msg = 'القيد متوازن وجاهز للحفظ';
      ok = true;
    }

    state.ok = ok;
    state.debitCents = debit;
    state.creditCents = credit;
    $('#sumDebit').text(formatCents(debit));
    $('#sumCredit').text(formatCents(credit));
    $('#sumDiff').text(formatCents(diff));
    $('#diffBox').toggleClass('is-ok', ok).toggleClass('is-bad', diff !== 0 && (debit > 0 || credit > 0));
    $('#mjStatus').text(msg).css('color', ok ? '#065f46' : '#64748b');
    $save.prop('disabled', !ok);
  }

  $lines.find('.mj-acc').each(function () { initAcc($(this)); });
  renumber();
  recalc();

  $('#addLine').on('click', function () { addLine(true); });

  $lines.on('input', '.mj-amt', function () {
    syncSide($(this));
    recalc();
  });

  $lines.on('blur', '.mj-amt', function () {
    var n = parseAmt($(this).val());
    $(this).val(n > 0 ? n.toFixed(2) : '');
    syncSide($(this));
    recalc();
  });

  $lines.on('select2:select select2:clear', '.mj-acc', function () {
    var $tr = $(this).closest('tr');
    syncSide($tr.find('.mj-debit'));
    recalc();
  });

  $lines.on('select2:select', '.mj-acc', function () {
    var $debit = $(this).closest('tr').find('.mj-debit');
    if (!$debit.prop('disabled')) {
      $debit.trigger('focus');
    } else {
      $(this).closest('tr').find('.mj-credit').trigger('focus');
    }
  });

  $lines.on('keydown', '.mj-amt', function (e) {
    if (e.key !== 'Enter') return;
    e.preventDefault();
    var $tr = $(this).closest('tr');
    var $next = $tr.next();
    if ($next.length) {
      $next.find('.mj-acc').select2('open');
    } else {
      addLine(true);
    }
  });

  $lines.on('click', '.mj-del', function () {
    var $tr = $(this).closest('tr');
    if ($lines.children().length <= 2) {
      clearRow($tr);
    } else {
      var $acc = $tr.find('.mj-acc');
      if ($acc.data('select2')) $acc.select2('destroy');
      $tr.remove();
    }
    renumber();
    recalc();
  });

  $('#details').on('input', recalc);

  $('#mjClear').on('click', function () {
    var dirty = state.debitCents || state.creditCents || $.trim($('#details').val()) !== '';
    if (dirty && !window.confirm('مسح البيان وكل الأسطر؟')) return;
    $('#details').val('');
    $lines.children().each(function () {
      var $acc = $(this).find('.mj-acc');
      if ($acc.data('select2')) $acc.select2('destroy');
    });
    $lines.empty();
    for (var i = 0; i < 2; i++) addLine(false);
    recalc();
    $('#details').trigger('focus');
  });

  $('#journalForm').on('submit', function (e) {
    recalc();
    if (!state.ok) {
      e.preventDefault();
      return;
    }
    var html = '';
    $lines.children().each(function () {
      var acc = $(this).find('.mj-acc').val();
      var d = parseAmt($(this).find('.mj-debit').val());
      var c = parseAmt($(this).find('.mj-credit').val());
      if (!acc) return;
      if (d > 0) {
        html += '<input type="hidden" name="depitval[]" value="' + d.toFixed(2) + '">';
        html += '<input type="hidden" name="depitname[]" value="' + String(acc).replace(/[^\d]/g, '') + '">';
      } else if (c > 0) {
        html += '<input type="hidden" name="creditval[]" value="' + c.toFixed(2) + '">';
        html += '<input type="hidden" name="creditname[]" value="' + String(acc).replace(/[^\d]/g, '') + '">';
      }
    });
    $('#payload').html(html);
    $('#totalField').val((state.debitCents / 100).toFixed(2));
    $save.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> جارٍ الحفظ...');
  });

  $(document).on('keydown', function (e) {
    if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
      e.preventDefault();
      if (state.ok) $('#journalForm').trigger('submit');
    }
  });
});
</script>
JS;
include('includes/footer.php');
