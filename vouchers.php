<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
require_once __DIR__ . '/includes/sidebar.php';
require_once __DIR__ . '/classes/InvoiceProcessor.php';

// Filter parameters (GET or POST)
$filterType = isset($_REQUEST['t']) ? trim((string)$_REQUEST['t']) : (isset($_REQUEST['tybe']) ? trim((string)$_REQUEST['tybe']) : '');
$startDate = isset($_REQUEST['strt']) ? trim((string)$_REQUEST['strt']) : '';
$endDate = isset($_REQUEST['end']) ? trim((string)$_REQUEST['end']) : '';

$where = ["h.isdeleted = 0"];
$params = [];
$types = "";

// 1 = Recive (قبض), 2 = Payment (صرف/دفع), 16 = Transfer (تحويل نقدية بين الصناديق)
if ($filterType === 'recive' || $filterType === '1') {
    $where[] = "h.pro_tybe = 1";
} elseif ($filterType === 'payment' || $filterType === '2') {
    $where[] = "h.pro_tybe = 2";
} elseif ($filterType === 'transfer' || $filterType === '16') {
    $where[] = "h.pro_tybe = 16";
} else {
    $where[] = "h.pro_tybe IN (1, 2, 16)";
}

if ($startDate !== '') {
    $where[] = "h.pro_date >= ?";
    $params[] = $startDate;
    $types .= "s";
}

if ($endDate !== '') {
    $where[] = "h.pro_date <= ?";
    $params[] = $endDate;
    $types .= "s";
}

$whereSql = implode(" AND ", $where);
$invoiceTypesSql = implode(',', array_map('intval', InvoiceProcessor::INVOICE_TYPES));
$sql = "SELECT h.id, h.pro_date, h.pro_tybe, h.pro_value, h.pro_id, h.info,
               acc1.aname AS acc1_name, acc2.aname AS acc2_name,
               inv.pro_id AS linked_invoice_no
        FROM ot_head h
        LEFT JOIN acc_head acc1 ON h.acc1 = acc1.id
        LEFT JOIN acc_head acc2 ON h.acc2 = acc2.id
        LEFT JOIN ot_head inv ON h.op2 > 0 AND inv.id = h.op2 AND inv.id <> h.id
             AND inv.isdeleted = 0 AND inv.pro_tybe IN ($invoiceTypesSql)
        WHERE $whereSql
        ORDER BY h.id DESC 
        LIMIT 300";

if (!empty($params)) {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $resvoucher = $stmt->get_result();
} else {
    $resvoucher = $conn->query($sql);
}

$voucherCount = $resvoucher ? $resvoucher->num_rows : 0;

// عدد السندات لكل نوع (بنفس فلتر التاريخ) لعرضه على تابات الفلتر
$countWhere = ["isdeleted = 0", "pro_tybe IN (1, 2, 16)"];
$countParams = [];
$countTypes = "";
if ($startDate !== '') {
    $countWhere[] = "pro_date >= ?";
    $countParams[] = $startDate;
    $countTypes .= "s";
}
if ($endDate !== '') {
    $countWhere[] = "pro_date <= ?";
    $countParams[] = $endDate;
    $countTypes .= "s";
}
$countStmt = $conn->prepare("SELECT pro_tybe, COUNT(*) AS cnt FROM ot_head WHERE " . implode(" AND ", $countWhere) . " GROUP BY pro_tybe");
if (!empty($countParams)) {
    $countStmt->bind_param($countTypes, ...$countParams);
}
$countStmt->execute();
$typeCounts = [1 => 0, 2 => 0, 16 => 0];
$countRes = $countStmt->get_result();
while ($c = $countRes->fetch_assoc()) {
    $typeCounts[(int)$c['pro_tybe']] = (int)$c['cnt'];
}
$countStmt->close();

$activeTab = match (true) {
    $filterType === 'recive' || $filterType === '1' => 'recive',
    $filterType === 'payment' || $filterType === '2' => 'payment',
    $filterType === 'transfer' || $filterType === '16' => 'transfer',
    default => '',
};
$dateQuery = array_filter(['strt' => $startDate, 'end' => $endDate], fn ($v) => $v !== '');
$filterTabs = [
    ''         => ['label' => 'الكل',          'icon' => 'fa-list',         'class' => 'k-btn-secondary', 'count' => array_sum($typeCounts)],
    'recive'   => ['label' => 'سندات القبض',   'icon' => 'fa-arrow-down',   'class' => 'k-btn-success',   'count' => $typeCounts[1]],
    'payment'  => ['label' => 'سندات الدفع',   'icon' => 'fa-arrow-up',     'class' => 'k-btn-danger',    'count' => $typeCounts[2]],
    'transfer' => ['label' => 'تحويلات نقدية', 'icon' => 'fa-exchange-alt', 'class' => 'k-btn-info',      'count' => $typeCounts[16]],
];
?>

<div class="content-wrapper k-page-wrapper">
  <?= kody_page_header(
      title: 'السندات والتحويلات المالية',
      icon: 'fa-money-check-alt',
      breadcrumbs: [
          'الرئيسية' => 'dashboard.php',
          'الحسابات' => 'accounts.php',
          'السندات المالية' => ''
      ],
      actions: array_values(array_filter([
          [
              'label' => 'سند قبض جديد (وارد)',
              'url'   => 'add_voucher.php?t=recive',
              'class' => 'k-btn-success',
              'icon'  => 'fa-arrow-down'
          ],
          [
              'label' => 'سند صرف جديد (صادر)',
              'url'   => 'add_voucher.php?t=payment',
              'class' => 'k-btn-danger',
              'icon'  => 'fa-arrow-up'
          ],
          [
              'label' => 'تحويل نقدية (من صندوق لصندوق)',
              'url'   => 'add_voucher.php?t=transfer',
              'class' => 'k-btn-info text-white font-weight-bold',
              'icon'  => 'fa-exchange-alt'
          ]
      ], fn ($a) => Can::canVisit($a['url']))),
      badge: $voucherCount . ' عملية'
  ) ?>

  <div class="k-card">
    <div class="d-flex flex-wrap p-3 border-bottom" style="gap: .5rem;">
      <?php foreach ($filterTabs as $tabKey => $tab):
          $tabUrl = 'vouchers.php' . (($q = http_build_query(array_filter(['t' => $tabKey] + $dateQuery, fn ($v) => $v !== ''))) !== '' ? '?' . $q : '');
          $isActive = $activeTab === $tabKey;
      ?>
        <a href="<?= htmlspecialchars($tabUrl) ?>" class="k-btn k-btn-sm <?= $isActive ? $tab['class'] . ' text-white' : 'k-btn-outline' ?>">
          <i class="fas <?= $tab['icon'] ?>"></i> <?= $tab['label'] ?>
          <span class="k-badge <?= $isActive ? 'bg-white text-dark' : 'k-badge-secondary' ?> mr-1"><?= $tab['count'] ?></span>
        </a>
      <?php endforeach; ?>
    </div>
    <div class="k-toolbar">
      <div class="k-toolbar-start">
        <form method="GET" action="vouchers.php" class="d-flex align-items-center flex-wrap gap-2 m-0">
          <?php if ($activeTab !== ''): ?>
            <input type="hidden" name="t" value="<?= $activeTab ?>">
          <?php endif; ?>

          <input type="date" name="strt" value="<?= htmlspecialchars($startDate) ?>" class="k-input form-control-sm" style="width: 145px;" title="من تاريخ">
          <input type="date" name="end" value="<?= htmlspecialchars($endDate) ?>" class="k-input form-control-sm" style="width: 145px;" title="إلى تاريخ">

          <button type="submit" class="k-btn k-btn-secondary k-btn-sm">
            <i class="fas fa-filter"></i> تصفية
          </button>
          <?php if ($startDate !== '' || $endDate !== '' || $filterType !== ''): ?>
            <a href="vouchers.php" class="k-btn k-btn-outline k-btn-sm text-muted" title="إلغاء الفلتر">إلغاء</a>
          <?php endif; ?>
        </form>
      </div>

      <div class="k-toolbar-end">
        <div class="k-search-input-wrap">
          <i class="fas fa-search"></i>
          <input type="text" placeholder="بحث برقم السند أو الحساب..." data-kody-table-search="#vouchersTable">
        </div>
      </div>
    </div>

    <?php if ($voucherCount === 0): ?>
      <?= kody_empty_state(
          title: 'لا توجد سجلات مطابقة لخيارات البحث',
          desc: 'يمكنك إنشاء سندات قبض للمبالغ الواردة، أو سندات صرف للمصروفات، أو تحويل مبالغ نقدية مباشرة بين الصناديق والبنوك.',
          icon: 'fa-money-check-alt',
          cta: Can::canVisit($filterType === 'transfer' ? 'add_voucher.php?t=transfer' : ($filterType === 'payment' ? 'add_voucher.php?t=payment' : 'add_voucher.php?t=recive')) ? [
              'label' => ($filterType === 'transfer' ? 'تحويل نقدية جديد' : ($filterType === 'payment' ? 'سند صرف جديد' : 'سند قبض جديد')),
              'url' => ($filterType === 'transfer' ? 'add_voucher.php?t=transfer' : ($filterType === 'payment' ? 'add_voucher.php?t=payment' : 'add_voucher.php?t=recive')),
              'icon' => ($filterType === 'transfer' ? 'fa-exchange-alt' : ($filterType === 'payment' ? 'fa-arrow-up' : 'fa-arrow-down'))
          ] : null
      ) ?>
    <?php else: ?>
      <div class="k-table-wrap">
        <table class="k-table" id="vouchersTable">
          <thead>
            <tr>
              <th style="width: 50px;">#</th>
              <th style="width: 90px;">رقم السند</th>
              <th style="width: 110px;">التاريخ</th>
              <th style="width: 130px;">النوع</th>
              <th>قيمة السند / التحويل</th>
              <th>الطرف الأول (المصدر / الصندوق)</th>
              <th>الطرف الثاني (المستلم / المقابل)</th>
              <th>البيان والملاحظات</th>
              <th style="width: 120px;" class="text-center">العمليات</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $x = 0;
            while ($row = $resvoucher->fetch_assoc()):
                $x++;
                $vid = (int)$row['id'];
                $proId = htmlspecialchars($row['pro_id'] ?? $vid, ENT_QUOTES, 'UTF-8');
                $proDate = htmlspecialchars($row['pro_date'] ?? '', ENT_QUOTES, 'UTF-8');
                $proType = (int)$row['pro_tybe'];
                $val = (float)($row['pro_value'] ?? 0);
                $acc1 = htmlspecialchars($row['acc1_name'] ?? '—', ENT_QUOTES, 'UTF-8');
                $acc2 = htmlspecialchars($row['acc2_name'] ?? '—', ENT_QUOTES, 'UTF-8');
                $info = htmlspecialchars($row['info'] ?? '', ENT_QUOTES, 'UTF-8');

                if ($proType === 1) {
                    $typeKey = 'recive';
                    $valColor = 'text-success';
                    $fromDisplay = '<span class="text-dark font-weight-bold">' . $acc1 . '</span>';
                    $toDisplay = '<span class="text-dark">' . $acc2 . '</span>';
                } elseif ($proType === 16) {
                    $typeKey = 'transfer';
                    $valColor = 'text-info';
                    // في التحويل: acc2 هو الصندوق المحول منه (دائن)، acc1 هو الصندوق المحول إليه (مدين)
                    $fromDisplay = '<span class="text-danger font-weight-bold"><i class="fas fa-arrow-up text-danger small ml-1"></i>من: ' . $acc2 . '</span>';
                    $toDisplay = '<span class="text-success font-weight-bold"><i class="fas fa-arrow-down text-success small ml-1"></i>إلى: ' . $acc1 . '</span>';
                } else {
                    $typeKey = 'payment';
                    $valColor = 'text-danger';
                    $fromDisplay = '<span class="text-dark font-weight-bold">' . $acc2 . '</span>';
                    $toDisplay = '<span class="text-dark">' . $acc1 . '</span>';
                }
            ?>
              <tr>
                <td><span class="text-muted small font-weight-bold"><?= $x ?></span></td>
                <td><span class="k-badge k-badge-secondary font-weight-bold"><?= $proId ?></span></td>
                <td><?= $proDate ?></td>
                <td>
                  <?php if ($proType === 1): ?>
                    <span class="k-badge k-badge-success"><i class="fas fa-arrow-down ml-1"></i> قبض</span>
                  <?php elseif ($proType === 16): ?>
                    <span class="k-badge k-badge-info"><i class="fas fa-exchange-alt ml-1"></i> تحويل نقدية</span>
                  <?php else: ?>
                    <span class="k-badge k-badge-danger"><i class="fas fa-arrow-up ml-1"></i> صرف</span>
                  <?php endif; ?>
                </td>
                <td>
                  <strong class="font-size-15 <?= $valColor ?>">
                    <?= number_format($val, 2) ?>
                  </strong>
                </td>
                <td><?= $fromDisplay ?></td>
                <td><?= $toDisplay ?></td>
                <td><?= $info !== '' ? $info : '<span class="text-muted small">—</span>' ?></td>
                <td class="text-center">
                  <div class="k-actions">
                    <?php if (Can::canVisit('add_voucher.php?edit=' . $vid . '&t=' . $typeKey)) { ?>
                    <?= kody_action_btn('edit', 'add_voucher.php?edit=' . $vid . '&t=' . $typeKey, ['title' => 'تعديل ' . ($proType === 16 ? 'التحويل' : 'السند')]) ?>
                    <?php } ?>
                    <?php if ($row['linked_invoice_no'] !== null) { ?>
                    <span class="k-btn-action text-muted" title="مرتبط بالفاتورة رقم <?= htmlspecialchars((string) $row['linked_invoice_no'], ENT_QUOTES, 'UTF-8') ?> ولا يمكن حذفه"><i class="fas fa-lock"></i></span>
                    <?php } elseif (Can::check($proType === 1 ? 'receipts.delete' : 'payments.delete')) { ?>
                    <button type="button" 
                            class="k-btn-action k-btn-action-del btn-delete-voucher" 
                            data-id="<?= $vid ?>" 
                            data-number="<?= $proId ?>" 
                            data-type="<?= $proType ?>" 
                            data-name="<?= ($proType === 16 ? 'تحويل نقدية رقم ' : ($proType === 1 ? 'سند قبض رقم ' : 'سند صرف رقم ')) . $proId ?>" 
                            title="حذف <?= ($proType === 16 ? 'التحويل' : 'السند') ?>">
                      <i class="fas fa-trash-alt"></i>
                    </button>
                    <?php } ?>
                  </div>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
  var isDeletingVoucher = false;

  $(document).on("click", ".btn-delete-voucher", function(e) {
    e.preventDefault();
    if (isDeletingVoucher) return false;

    var $btn = $(this);
    if ($btn.hasClass("disabled") || $btn.prop("disabled")) return false;

    var vid = $btn.data("id");
    var vName = $btn.data("name") || ("سند رقم " + vid);
    var vType = parseInt($btn.data("type"), 10);
    var typeTitle = (vType === 16) ? "التحويل" : (vType === 1 ? "سند القبض" : "سند الصرف");
    var $tr = $btn.closest("tr");

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
          text: "تم حذف " + vName + " بنجاح وتحديث الحسابات المرتبطة.",
          timer: 1600,
          showConfirmButton: false
        });

        $tr.css("background-color", "#fee2e2").fadeOut(400, function() {
          $(this).remove();
          var remaining = $("#vouchersTable tbody tr").length;
          if (remaining === 0) {
            location.reload();
          }
        });
      } else {
        isDeletingVoucher = false;
        $btn.prop("disabled", false).removeClass("disabled");
      }
    });
  });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
