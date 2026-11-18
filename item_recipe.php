<?php
include('includes/header.php');
include('includes/navbar.php');
include('includes/sidebar.php');
require_once __DIR__ . '/classes/Manufacturing.php';

$itemId = (int) ($_GET['id'] ?? 0);
$stmt = $conn->prepare('SELECT id, iname, barcode, is_assembly FROM myitems WHERE id = ? AND isdeleted = 0 LIMIT 1');
$stmt->bind_param('i', $itemId);
$stmt->execute();
$item = $stmt->get_result()->fetch_assoc();
$stmt->close();

$recipe = $item ? Manufacturing::recipe($conn, $itemId) : [];
$error = $_SESSION['recipe_error'] ?? '';
unset($_SESSION['recipe_error']);
$saved = isset($_GET['ok']);
?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">وصفة التصنيع</h1>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <?php if (!$item) { ?>
                <div class="alert alert-danger">الصنف غير موجود.</div>
            <?php } else { ?>
                <?php if ($saved) { ?>
                    <div class="alert alert-success">تم حفظ الوصفة. الصنف بقى مجمع.</div>
                <?php } ?>
                <?php if ($error !== '') { ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
                <?php } ?>
                <div class="card">
                    <div class="card-header">
                        <strong><?= htmlspecialchars($item['iname'], ENT_QUOTES, 'UTF-8') ?></strong>
                        <span class="text-muted">— الكميات دي لوحدة واحدة. اختار الخام من القائمة، الكتابة لوحدها مش بتتحفظ.</span>
                        <a class="btn btn-sm btn-outline-secondary float-left" href="add_item.php?edit=<?= (int) $item['id'] ?>">رجوع للصنف</a>
                    </div>
                    <form method="post" action="do/dosave_item_recipe.php">
                        <input type="hidden" name="item_id" value="<?= (int) $item['id'] ?>">
                        <div class="card-body p-0">
                            <table class="table table-sm mb-0" id="bom-table">
                                <thead>
                                    <tr>
                                        <th>الصنف الخام</th>
                                        <th style="width:140px">الكمية</th>
                                        <th style="width:50px"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                $rows = $recipe !== [] ? $recipe : [['component_id' => 0, 'qty' => '', 'iname' => '']];
                                foreach ($rows as $line) { ?>
                                    <tr>
                                        <td style="position:relative">
                                            <input type="text" class="form-control form-control-sm bom-search" value="<?= htmlspecialchars($line['iname'], ENT_QUOTES, 'UTF-8') ?>" placeholder="ابحث بالاسم أو الباركود" autocomplete="off">
                                            <input type="hidden" name="component_id[]" value="<?= (int) $line['component_id'] ?>">
                                            <div class="bom-suggest list-group" style="position:absolute;z-index:20;display:none;width:100%"></div>
                                        </td>
                                        <td>
                                            <input type="number" name="qty[]" class="form-control form-control-sm" step="any" min="0" value="<?= htmlspecialchars((string) $line['qty'], ENT_QUOTES, 'UTF-8') ?>">
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-danger bom-del">&times;</button>
                                        </td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-primary btn-sm" id="bom-add">سطر خام</button>
                            <button type="submit" class="btn btn-primary">حفظ الوصفة</button>
                        </div>
                    </form>
                </div>
            <?php } ?>
        </div>
    </section>
</div>
<?php
$extra_footer_scripts = <<<'HTML'
<script>
(function () {
  var table = document.getElementById('bom-table');
  if (!table) return;
  var parentId = String(document.querySelector('input[name="item_id"]').value || '');

  function rowHtml() {
    return '<tr><td style="position:relative"><input type="text" class="form-control form-control-sm bom-search" placeholder="ابحث بالاسم أو الباركود" autocomplete="off"><input type="hidden" name="component_id[]" value="0"><div class="bom-suggest list-group" style="position:absolute;z-index:20;display:none;width:100%"></div></td><td><input type="number" name="qty[]" class="form-control form-control-sm" step="any" min="0"></td><td><button type="button" class="btn btn-sm btn-outline-danger bom-del">&times;</button></td></tr>';
  }

  document.getElementById('bom-add').addEventListener('click', function () {
    table.querySelector('tbody').insertAdjacentHTML('beforeend', rowHtml());
  });

  table.addEventListener('click', function (e) {
    if (e.target.classList.contains('bom-del')) {
      var tr = e.target.closest('tr');
      if (table.querySelectorAll('tbody tr').length > 1) tr.remove();
    }
    var pick = e.target.closest('.bom-pick');
    if (pick) {
      var td = pick.closest('td');
      td.querySelector('.bom-search').value = pick.getAttribute('data-name');
      td.querySelector('input[type="hidden"]').value = pick.getAttribute('data-id');
      td.querySelector('.bom-suggest').style.display = 'none';
    }
  });

  var timer = null;
  table.addEventListener('input', function (e) {
    if (!e.target.classList.contains('bom-search')) return;
    var input = e.target;
    var box = input.parentElement.querySelector('.bom-suggest');
    input.parentElement.querySelector('input[type="hidden"]').value = '0';
    clearTimeout(timer);
    timer = setTimeout(function () {
      var term = input.value.trim();
      if (term.length < 1) { box.style.display = 'none'; return; }
      fetch('ajax/search_items_autocomplete.php?term=' + encodeURIComponent(term))
        .then(function (r) { return r.json(); })
        .then(function (items) {
          box.innerHTML = '';
          (items || []).forEach(function (it) {
            if (String(it.id) === parentId) return;
            var a = document.createElement('button');
            a.type = 'button';
            a.className = 'list-group-item list-group-item-action py-1 bom-pick';
            a.setAttribute('data-id', it.id);
            a.setAttribute('data-name', it.value || '');
            a.textContent = it.label || it.value || '';
            box.appendChild(a);
          });
          box.style.display = box.childElementCount ? 'block' : 'none';
        });
    }, 250);
  });
})();
</script>
HTML;
include('includes/footer.php');
