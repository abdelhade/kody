<?php include('includes/header.php') ?>
<?php include('includes/navbar.php') ?>
<?php include('includes/sidebar.php') ?>

<?php
$copyId = isset($_GET['i']) ? (int)$_GET['i'] : 0;
$copyname = '';
$copyRows = [];

if ($copyId > 0) {
    $rowCopyEmp = $conn->query("SELECT name FROM employees WHERE id = $copyId")->fetch_assoc();
    $copyname = $rowCopyEmp['name'] ?? '';
    $resCopy = $conn->query("SELECT kbi_id, kbi_weight FROM emp_kbis WHERE emp_id = $copyId");
    while ($r = $resCopy->fetch_assoc()) {
        $copyRows[] = $r;
    }
}

$kbisList = [];
$resKbis = $conn->query("SELECT id, kname, info, ktybe FROM kbis WHERE isdeleted != 1 ORDER BY kname ASC");
while ($k = $resKbis->fetch_assoc()) {
    $kbisList[] = $k;
}

$initialRows = !empty($copyRows) ? $copyRows : [['kbi_id' => $kbisList[0]['id'] ?? '', 'kbi_weight' => '0.00']];
$hasKbis = count($kbisList) > 0;
?>

<div class="content-wrapper add-empkbi-page">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2 align-items-center">
        <div class="col-sm-6">
          <h1 class="m-0 text-dark">
            <i class="fas fa-chart-bar text-primary mr-2"></i>
            إضافة معدلات التقييم (KPI)
          </h1>
          <?php if ($copyId > 0 && $copyname !== '') { ?>
          <p class="text-muted mb-0 mt-1 small">
            <i class="fas fa-copy mr-1"></i>
            نسخ مؤشرات من: <strong><?= htmlspecialchars($copyname) ?></strong>
          </p>
          <?php } ?>
        </div>
        <div class="col-sm-6 text-left">
          <a href="emp_kbis.php" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-right ml-1"></i> رجوع للقائمة
          </a>
        </div>
      </div>
    </div>
  </section>

  <section class="content">
    <div class="container-fluid">
      <form id="empKbiForm" onsubmit="return validateTotalKBI()" action="do/doadd_empkbi.php" method="post">
        <div class="row">
          <div class="col-lg-8">
            <div class="card card-outline card-primary shadow-sm">
              <div class="card-header">
                <h3 class="card-title mb-0"><i class="fas fa-user-tie mr-1"></i> بيانات التقييم</h3>
              </div>
              <div class="card-body">
                <?php if (!$hasKbis) { ?>
                <div class="alert alert-warning">
                  <i class="fas fa-exclamation-triangle ml-1"></i>
                  لا توجد مؤشرات KPI معرّفة.
                  <a href="add_kbi.php" class="alert-link">أضف مؤشراً أولاً</a>
                </div>
                <?php } else { ?>
                <div class="form-group">
                  <label for="emp_id">اسم الموظف <span class="text-danger">*</span></label>
                  <select name="emp_id" id="emp_id" class="form-control select2-style" required>
                    <option value="">— اختر موظفاً بدون مؤشرات —</option>
                    <?php
                    $sql = "SELECT e.id, e.name, e.jop
                            FROM employees e
                            LEFT JOIN emp_kbis k ON e.id = k.emp_id
                            WHERE e.isdeleted != 1 AND k.emp_id IS NULL
                            ORDER BY e.name ASC";
                    $res = $conn->query($sql);
                    while ($row = $res->fetch_assoc()) {
                        $jopName = '';
                        if (!empty($row['jop'])) {
                            $jopId = (int)$row['jop'];
                            $rowjop = $conn->query("SELECT name FROM jops WHERE id = $jopId")->fetch_assoc();
                            $jopName = $rowjop['name'] ?? '';
                        }
                        $label = htmlspecialchars($row['name']);
                        if ($jopName !== '') {
                            $label .= ' — ' . htmlspecialchars($jopName);
                        }
                    ?>
                    <option value="<?= (int)$row['id'] ?>"><?= $label ?></option>
                    <?php } ?>
                  </select>
                  <small class="form-text text-muted">يظهر فقط الموظفون الذين لم تُسجَّل لهم مؤشرات بعد.</small>
                </div>

                <hr class="my-3">

                <div class="d-flex justify-content-between align-items-center mb-2">
                  <h5 class="mb-0 text-secondary"><i class="fas fa-bullseye mr-1"></i> المؤشرات والأوزان</h5>
                  <button type="button" class="btn btn-sm btn-outline-primary" id="addkbi">
                    <i class="fas fa-plus ml-1"></i> إضافة مؤشر
                  </button>
                </div>

                <div class="table-responsive kbi-rows-table">
                  <table class="table table-bordered table-hover mb-0">
                    <thead class="thead-light">
                      <tr>
                        <th style="min-width:220px">المؤشر (KPI)</th>
                        <th style="width:140px">الوزن %</th>
                        <th style="width:70px" class="text-center">حذف</th>
                      </tr>
                    </thead>
                    <tbody id="kbiRowsBody">
                      <?php foreach ($initialRows as $idx => $rowKbi) {
                          $selectedKbi = (int)($rowKbi['kbi_id'] ?? 0);
                          $weight = htmlspecialchars((string)($rowKbi['kbi_weight'] ?? '0.00'));
                      ?>
                      <tr class="kbi-row">
                        <td>
                          <select name="kbi_id[]" class="form-control form-control-sm" required>
                            <?php foreach ($kbisList as $kbi) {
                                $sel = ((int)$kbi['id'] === $selectedKbi) ? 'selected' : '';
                                $title = htmlspecialchars($kbi['info'] ?? '');
                                $ktybe = $kbi['ktybe'] ? ' [' . htmlspecialchars($kbi['ktybe']) . ']' : '';
                            ?>
                            <option value="<?= (int)$kbi['id'] ?>" title="<?= $title ?>" <?= $sel ?>>
                              <?= htmlspecialchars($kbi['kname']) . $ktybe ?>
                            </option>
                            <?php } ?>
                          </select>
                        </td>
                        <td>
                          <input type="text" name="kbi_weight[]" class="form-control form-control-sm weight-input text-center"
                                 pattern="^\d+(\.\d{0,2})?$" inputmode="decimal"
                                 value="<?= $weight ?>" required>
                        </td>
                        <td class="text-center align-middle">
                          <button type="button" class="btn btn-sm btn-outline-danger delete-kbi" <?= $idx === 0 && count($initialRows) === 1 ? 'disabled' : '' ?> title="حذف">
                            <i class="fas fa-trash-alt"></i>
                          </button>
                        </td>
                      </tr>
                      <?php } ?>
                    </tbody>
                    <tfoot class="bg-light">
                      <tr>
                        <th class="text-left">مجموع الأوزان</th>
                        <th colspan="2">
                          <div class="d-flex align-items-center flex-wrap">
                            <input type="text" name="total_kbi" id="total_kbi" class="form-control form-control-sm total-kbi-input text-center font-weight-bold" readonly value="0.00" required>
                            <span id="totalBadge" class="badge badge-secondary mr-2 mt-1 mt-md-0">يجب أن يساوي 100%</span>
                          </div>
                        </th>
                      </tr>
                    </tfoot>
                  </table>
                </div>
                <?php } ?>
              </div>
              <div class="card-footer d-flex justify-content-between align-items-center flex-wrap">
                <a href="kbis.php" class="btn btn-link text-muted p-0 mb-2 mb-md-0">
                  <i class="fas fa-cog ml-1"></i> إدارة تعريفات المؤشرات
                </a>
                <button type="submit" class="btn btn-success px-4" <?= $hasKbis ? '' : 'disabled' ?>>
                  <i class="fas fa-save ml-1"></i> حفظ التقييم
                </button>
              </div>
            </div>
          </div>

          <div class="col-lg-4">
            <div class="card card-outline card-info shadow-sm">
              <div class="card-header">
                <h3 class="card-title mb-0"><i class="fas fa-info-circle mr-1"></i> تعليمات</h3>
              </div>
              <div class="card-body">
                <ul class="mb-0 pr-3 text-muted" style="line-height: 1.8;">
                  <li>اختر موظفاً لم تُضف له مؤشرات KPI من قبل.</li>
                  <li>وزّع الأوزان بحيث يكون <strong>المجموع = 100%</strong>.</li>
                  <li>يمكنك إضافة أكثر من مؤشر لنفس الموظف.</li>
                  <li>للنسخ من موظف آخر، استخدم زر النسخ من قائمة التقييمات.</li>
                </ul>
                <div class="progress mt-3" style="height: 8px;">
                  <div id="weightProgress" class="progress-bar bg-success" role="progressbar" style="width: 0%"></div>
                </div>
                <p class="small text-muted mb-0 mt-2 text-center" id="weightProgressLabel">0% من 100%</p>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </section>
</div>

<style>
.add-empkbi-page .kbi-rows-table .table td,
.add-empkbi-page .kbi-rows-table .table th {
  vertical-align: middle;
}
.add-empkbi-page .total-kbi-input {
  max-width: 100px;
  font-size: 1.1rem;
}
.add-empkbi-page .total-kbi-input.is-valid-total {
  border-color: #28a745;
  background-color: #f0fff4;
}
.add-empkbi-page .total-kbi-input.is-invalid-total {
  border-color: #dc3545;
  background-color: #fff5f5;
}
</style>

<script>
(function() {
  var kbiOptionsHtml = <?= json_encode(array_reduce($kbisList, function ($html, $kbi) {
      $title = htmlspecialchars($kbi['info'] ?? '', ENT_QUOTES, 'UTF-8');
      $ktybe = $kbi['ktybe'] ? ' [' . htmlspecialchars($kbi['ktybe'], ENT_QUOTES, 'UTF-8') . ']' : '';
      $name = htmlspecialchars($kbi['kname'], ENT_QUOTES, 'UTF-8');
      return $html . '<option value="' . (int)$kbi['id'] . '" title="' . $title . '">' . $name . $ktybe . '</option>';
  }, ''), JSON_UNESCAPED_UNICODE) ?>;

  function updateTotalWeight() {
    var total = 0;
    $('input[name="kbi_weight[]"]').each(function() {
      total += parseFloat($(this).val()) || 0;
    });
    total = Math.round(total * 100) / 100;
    var $total = $('#total_kbi');
    $total.val(total.toFixed(2));

    var ok = total === 100;
    $total.toggleClass('is-valid-total', ok).toggleClass('is-invalid-total', !ok);
    $('#totalBadge')
      .toggleClass('badge-success', ok)
      .toggleClass('badge-danger', !ok && total > 0)
      .toggleClass('badge-secondary', total === 0)
      .text(ok ? 'صحيح — 100%' : (total > 100 ? 'تجاوز 100%' : 'يجب أن يساوي 100%'));

    var pct = Math.min(100, total);
    $('#weightProgress').css('width', pct + '%')
      .removeClass('bg-success bg-warning bg-danger')
      .addClass(ok ? 'bg-success' : (total > 100 ? 'bg-danger' : 'bg-warning'));
    $('#weightProgressLabel').text(total.toFixed(2) + '% من 100%');

    $('.delete-kbi').prop('disabled', $('.kbi-row').length <= 1);
  }

  function buildRow(selectedId, weight) {
    var $row = $('<tr class="kbi-row"></tr>');
    var $select = $('<select name="kbi_id[]" class="form-control form-control-sm" required></select>').html(kbiOptionsHtml);
    if (selectedId) $select.val(String(selectedId));
    $row.append($('<td></td>').append($select));
    $row.append($('<td></td>').append(
      $('<input type="text" name="kbi_weight[]" class="form-control form-control-sm weight-input text-center" pattern="^\\d+(\\.\\d{0,2})?$" inputmode="decimal" required>')
        .val(weight != null ? weight : '0.00')
    ));
    $row.append($('<td class="text-center align-middle"></td>').append(
      $('<button type="button" class="btn btn-sm btn-outline-danger delete-kbi" title="حذف"><i class="fas fa-trash-alt"></i></button>')
    ));
    return $row;
  }

  $('#addkbi').on('click', function(e) {
    e.preventDefault();
    $('#kbiRowsBody').append(buildRow(null, '0.00'));
    updateTotalWeight();
  });

  $(document).on('click', '.delete-kbi', function() {
    if ($('.kbi-row').length > 1) {
      $(this).closest('.kbi-row').remove();
      updateTotalWeight();
    }
  });

  $(document).on('input', 'input[name="kbi_weight[]"]', updateTotalWeight);

  window.validateTotalKBI = function() {
    var total = parseFloat(document.getElementById('total_kbi').value) || 0;
    if (total !== 100) {
      alert('مجموع الأوزان يجب أن يساوي 100% (الحالي: ' + total.toFixed(2) + '%)');
      return false;
    }
    if (!$('#emp_id').val()) {
      alert('يرجى اختيار الموظف');
      return false;
    }
    return true;
  };

  $(document).ready(updateTotalWeight);
})();
</script>

<?php include('includes/footer.php') ?>
