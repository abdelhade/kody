<?php 
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
require_once __DIR__ . '/includes/sidebar.php';

// Single joined query for table view
$sqlTable = "SELECT a.*, p.aname AS parent_name 
             FROM acc_head a 
             LEFT JOIN acc_head p ON a.parent_id = p.id 
             WHERE a.isdeleted = 0 
             ORDER BY a.code ASC";
$resTable = $conn->query($sqlTable);
$totalAccounts = $resTable ? $resTable->num_rows : 0;
?>

<style>
/* Account Tree Styles */
.acc-tree-container {
  padding: 1.25rem;
  background: var(--k-bg-card);
  border-radius: var(--k-radius-md);
}
.acc-tree-list, .acc-tree-list ul {
  list-style: none;
  padding-right: 1.25rem;
  margin: 0;
}
.acc-tree-node {
  margin: 0.35rem 0;
  position: relative;
}
.acc-caret {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.45rem 0.85rem;
  border-radius: var(--k-radius-md);
  background: #f8fafc;
  border: 1px solid var(--k-border);
  cursor: pointer;
  user-select: none;
  transition: var(--k-transition);
  font-size: 0.9rem;
}
.acc-caret:hover {
  background: var(--k-primary-light);
  border-color: var(--k-primary);
  color: var(--k-primary);
}
.acc-caret.no-children {
  cursor: default;
  background: #ffffff;
}
.acc-caret.no-children:hover {
  background: #ffffff;
  border-color: var(--k-border);
  color: inherit;
}
.acc-caret i.folder-icon {
  color: #f59e0b;
  font-size: 1.1rem;
}
.acc-nested {
  display: none;
  margin-top: 0.25rem;
  border-right: 2px dashed #cbd5e1;
}
.acc-nested.active {
  display: block;
}
.acc-code-badge {
  font-family: monospace;
  font-weight: 700;
  color: var(--k-primary);
  background: rgba(59, 130, 246, 0.1);
  padding: 0.15rem 0.45rem;
  border-radius: 4px;
}
</style>

<div class="content-wrapper k-page-wrapper">
  <?= kody_page_header(
      title: 'شجرة ودليل الحسابات العامة',
      icon: 'fa-sitemap',
      breadcrumbs: [
          'الرئيسية' => 'dashboard.php',
          'الحسابات' => ''
      ],
      actions: [
          [
              'label' => 'إضافة حساب جديد',
              'url'   => 'add_account.php',
              'class' => 'k-btn-primary',
              'icon'  => 'fa-plus'
          ],
          [
              'label' => 'ميزان المراجعة',
              'url'   => 'balance_sheet.php',
              'class' => 'k-btn-secondary',
              'icon'  => 'fa-balance-scale'
          ]
      ],
      badge: $totalAccounts . ' حساب'
  ) ?>

  <div class="k-card">
    <div class="k-card-header">
      <ul class="nav nav-pills" id="accTabs" role="tablist">
        <li class="nav-item">
          <a class="nav-link active font-weight-bold" id="tree-tab" data-toggle="pill" href="#tab-tree" role="tab">
            <i class="fas fa-network-wired ml-1"></i> الشجرة المحاسبية (الهيكل)
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link font-weight-bold" id="table-tab" data-toggle="pill" href="#tab-table" role="tab">
            <i class="fas fa-table ml-1"></i> العرض الجدولي والتفصيلي
          </a>
        </li>
      </ul>
    </div>

    <div class="tab-content" id="accTabsContent">
      <!-- تبويب الشجرة -->
      <div class="tab-pane fade show active p-3" id="tab-tree" role="tabpanel">
        <div class="acc-tree-container">
          <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
            <span class="text-muted small">انقر على المجلد لعرض وتفريغ الحسابات الفرعية التابعة له.</span>
            <div>
              <button type="button" class="k-btn k-btn-secondary k-btn-sm" id="btnExpandAll">
                <i class="fas fa-folder-open ml-1"></i> فتح الكل
              </button>
              <button type="button" class="k-btn k-btn-secondary k-btn-sm" id="btnCollapseAll">
                <i class="fas fa-folder ml-1"></i> إغلاق الكل
              </button>
            </div>
          </div>

          <ul class="acc-tree-list pr-0" id="accountsRoot">
            <?php
            // Recursive or Multi-level Tree rendering (Level 1 root accounts: parent_id = 0)
            $resL1 = $conn->query("SELECT a.*, (SELECT COUNT(*) FROM acc_head WHERE parent_id = a.id AND isdeleted = 0) AS children_count FROM acc_head a WHERE a.parent_id = 0 AND a.isdeleted = 0 ORDER BY a.code");
            while ($r1 = $resL1->fetch_assoc()):
                $hasKids1 = ((int)$r1['children_count'] > 0);
            ?>
              <li class="acc-tree-node">
                <span class="acc-caret <?= $hasKids1 ? '' : 'no-children' ?>">
                  <i class="fas <?= $hasKids1 ? 'fa-folder' : 'fa-file-invoice' ?> folder-icon"></i>
                  <span class="acc-code-badge"><?= htmlspecialchars($r1['code']) ?></span>
                  <strong><?= htmlspecialchars($r1['aname']) ?></strong>
                </span>

                <?php if ($hasKids1): ?>
                  <ul class="acc-nested">
                    <?php
                    $p1 = (int)$r1['id'];
                    $resL2 = $conn->query("SELECT a.*, (SELECT COUNT(*) FROM acc_head WHERE parent_id = a.id AND isdeleted = 0) AS children_count FROM acc_head a WHERE a.parent_id = $p1 AND a.isdeleted = 0 ORDER BY a.code");
                    while ($r2 = $resL2->fetch_assoc()):
                        $hasKids2 = ((int)$r2['children_count'] > 0);
                    ?>
                      <li class="acc-tree-node">
                        <span class="acc-caret <?= $hasKids2 ? '' : 'no-children' ?>">
                          <i class="fas <?= $hasKids2 ? 'fa-folder' : 'fa-file-alt' ?> folder-icon"></i>
                          <span class="acc-code-badge"><?= htmlspecialchars($r2['code']) ?></span>
                          <span><?= htmlspecialchars($r2['aname']) ?></span>
                        </span>

                        <?php if ($hasKids2): ?>
                          <ul class="acc-nested">
                            <?php
                            $p2 = (int)$r2['id'];
                            $resL3 = $conn->query("SELECT a.*, (SELECT COUNT(*) FROM acc_head WHERE parent_id = a.id AND isdeleted = 0) AS children_count FROM acc_head a WHERE a.parent_id = $p2 AND a.isdeleted = 0 ORDER BY a.code");
                            while ($r3 = $resL3->fetch_assoc()):
                                $hasKids3 = ((int)$r3['children_count'] > 0);
                            ?>
                              <li class="acc-tree-node">
                                <span class="acc-caret <?= $hasKids3 ? '' : 'no-children' ?>">
                                  <i class="fas <?= $hasKids3 ? 'fa-folder' : 'fa-file' ?> folder-icon"></i>
                                  <span class="acc-code-badge"><?= htmlspecialchars($r3['code']) ?></span>
                                  <span><?= htmlspecialchars($r3['aname']) ?></span>
                                </span>

                                <?php if ($hasKids3): ?>
                                  <ul class="acc-nested">
                                    <?php
                                    $p3 = (int)$r3['id'];
                                    $resL4 = $conn->query("SELECT a.*, (SELECT COUNT(*) FROM acc_head WHERE parent_id = a.id AND isdeleted = 0) AS children_count FROM acc_head a WHERE a.parent_id = $p3 AND a.isdeleted = 0 ORDER BY a.code");
                                    while ($r4 = $resL4->fetch_assoc()):
                                    ?>
                                      <li class="acc-tree-node">
                                        <span class="acc-caret no-children">
                                          <i class="fas fa-file text-muted"></i>
                                          <span class="acc-code-badge"><?= htmlspecialchars($r4['code']) ?></span>
                                          <span><?= htmlspecialchars($r4['aname']) ?></span>
                                        </span>
                                      </li>
                                    <?php endwhile; ?>
                                  </ul>
                                <?php endif; ?>
                              </li>
                            <?php endwhile; ?>
                          </ul>
                        <?php endif; ?>
                      </li>
                    <?php endwhile; ?>
                  </ul>
                <?php endif; ?>
              </li>
            <?php endwhile; ?>
          </ul>
        </div>
      </div>

      <!-- تبويب الجدول -->
      <div class="tab-pane fade p-0" id="tab-table" role="tabpanel">
        <div class="k-toolbar border-top-0">
          <div class="k-toolbar-start">
            <div class="k-search-input-wrap">
              <i class="fas fa-search"></i>
              <input type="text" placeholder="بحث بكود أو اسم الحساب أو الحساب الأب..." data-kody-table-search="#accountsTable">
            </div>
          </div>
          <div class="k-toolbar-end">
            <span class="text-muted small">إجمالي الحسابات: <strong><?= $totalAccounts ?></strong></span>
          </div>
        </div>

        <div class="k-table-wrap">
          <table class="k-table" id="accountsTable">
            <thead>
              <tr>
                <th style="width: 50px;">#</th>
                <th style="width: 110px;">كود الحساب</th>
                <th>اسم الحساب</th>
                <th>نوع القائمة</th>
                <th>التصنيف</th>
                <th>الحساب الرئيسي التابع له</th>
                <th>الرصيد الدفتري</th>
                <th style="width: 110px;" class="text-center">العمليات</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $x = 0;
              while ($row = $resTable->fetch_assoc()):
                  $x++;
                  $aid = (int)$row['id'];
                  $code = htmlspecialchars($row['code'] ?? '', ENT_QUOTES, 'UTF-8');
                  $name = htmlspecialchars($row['aname'] ?? '', ENT_QUOTES, 'UTF-8');
                  $pname = htmlspecialchars($row['parent_name'] ?? '', ENT_QUOTES, 'UTF-8');
                  $kind = (int)($row['kind'] ?? 0);
                  $isBasic = ((int)($row['is_basic'] ?? 0) === 1);
                  $bal = (float)($row['balance'] ?? 0);
              ?>
                <tr>
                  <td><span class="text-muted small font-weight-bold"><?= $x ?></span></td>
                  <td><span class="k-badge k-badge-secondary font-weight-bold"><?= $code ?></span></td>
                  <td>
                    <strong class="text-dark font-size-15"><?= $name ?></strong>
                  </td>
                  <td>
                    <?php if ($kind === 1): ?>
                      <span class="k-badge k-badge-primary">ميزانية عمومية</span>
                    <?php elseif ($kind === 2): ?>
                      <span class="k-badge k-badge-success">أرباح وخسائر</span>
                    <?php else: ?>
                      <span class="text-muted small">—</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php if ($isBasic): ?>
                      <span class="k-badge k-badge-warning">رئيسي</span>
                    <?php else: ?>
                      <span class="k-badge k-badge-info">فرعي (حركة)</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?= $pname !== '' ? '<span class="text-muted">' . $pname . '</span>' : '<span class="text-muted small">—</span>' ?>
                  </td>
                  <td>
                    <span class="font-weight-bold <?= $bal >= 0 ? 'text-success' : 'text-danger' ?>">
                      <?= number_format($bal, 2) ?>
                    </span>
                  </td>
                  <td class="text-center">
                    <div class="k-actions">
                      <?= kody_action_btn('edit', 'edit_account.php?id=' . $aid, ['title' => 'تعديل الحساب']) ?>
                      <?php if (!$isBasic && ((int)($row['deletable'] ?? 1) === 1)): ?>
                        <?= kody_action_btn('delete', 'do/dodel_account.php?id=' . $aid, [
                            'title' => 'حذف الحساب',
                            'item_name' => $name
                        ]) ?>
                      <?php else: ?>
                        <span class="k-btn-action text-muted" style="cursor: not-allowed; opacity: 0.5;" title="حساب أساسي لا يمكن حذفه">
                          <i class="fas fa-lock"></i>
                        </span>
                      <?php endif; ?>
                    </div>
                  </td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<?php 
$extra_footer_scripts = '
<script>
document.addEventListener("DOMContentLoaded", function() {
  // Tree carets toggle
  $(document).on("click", ".acc-caret:not(.no-children)", function() {
    var $nested = $(this).siblings(".acc-nested");
    var $icon = $(this).find("i.folder-icon");
    if ($nested.length) {
      $nested.toggleClass("active");
      if ($nested.hasClass("active")) {
        $icon.removeClass("fa-folder").addClass("fa-folder-open");
      } else {
        $icon.removeClass("fa-folder-open").addClass("fa-folder");
      }
    }
  });

  // Expand all
  $("#btnExpandAll").on("click", function() {
    $(".acc-nested").addClass("active");
    $(".acc-caret:not(.no-children) i.folder-icon").removeClass("fa-folder").addClass("fa-folder-open");
  });

  // Collapse all
  $("#btnCollapseAll").on("click", function() {
    $(".acc-nested").removeClass("active");
    $(".acc-caret:not(.no-children) i.folder-icon").removeClass("fa-folder-open").addClass("fa-folder");
  });
});
</script>
';
require_once __DIR__ . '/includes/footer.php'; 
?>
