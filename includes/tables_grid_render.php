<?php
// Ensure database columns exist
$chk_col = $conn->query("SHOW COLUMNS FROM tables LIKE 'parent_table_id'");
if ($chk_col && $chk_col->num_rows == 0) {
    $conn->query("ALTER TABLE tables ADD COLUMN parent_table_id INT DEFAULT NULL");
}
$chk_col2 = $conn->query("SHOW COLUMNS FROM tables LIKE 'is_merged'");
if ($chk_col2 && $chk_col2->num_rows == 0) {
    $conn->query("ALTER TABLE tables ADD COLUMN is_merged TINYINT(1) DEFAULT 0");
}

// Fetch all tables with parent table info and active orders count
$restables = $conn->query("SELECT t.*, 
    p.tname as parent_tname,
    (SELECT COUNT(*) FROM ot_head o 
     WHERE (o.info LIKE CONCAT('%', t.tname, '%') 
            OR (p.tname IS NOT NULL AND o.info LIKE CONCAT('%', p.tname, '%')))
     AND o.pro_tybe = 9 
     AND o.isdeleted = 0 
     AND o.fat_net > 0) as has_active_order
FROM tables t 
LEFT JOIN tables p ON t.parent_table_id = p.id
WHERE t.isdeleted = 0 
ORDER BY CAST(SUBSTRING_INDEX(t.tname, ' ', -1) AS UNSIGNED), t.tname");

if ($restables && $restables->num_rows > 0) {
    $merged_children = [];
    $all_tables = [];
    while ($r = $restables->fetch_assoc()) {
        $all_tables[] = $r;
        $pid = intval($r['parent_table_id'] ?? 0);
        if ($pid > 0) {
            $merged_children[$pid][] = $r['tname'];
        }
    }

    foreach ($all_tables as $rowtable) {
        $tableId = $rowtable['id'];
        $tableName = htmlspecialchars($rowtable['tname']);
        $hasActiveOrder = $rowtable['has_active_order'] > 0;
        $isMerged = intval($rowtable['is_merged']) == 1;
        $parentTableId = intval($rowtable['parent_table_id']) > 0 ? intval($rowtable['parent_table_id']) : null;
        $parentTableName = htmlspecialchars($rowtable['parent_tname'] ?? '');

        // Determine effective case
        $tableCase = ($hasActiveOrder || $isMerged) ? 1 : 0;
        
        // Status styling
        if ($isMerged && $hasActiveOrder) {
            $statusClass = 'btn-danger text-white border-warning shadow-sm';
            $statusIcon = 'fa-utensils';
            if ($parentTableId) {
                $statusText = 'محجوزة ومدمجة مع ' . $parentTableName;
            } elseif (isset($merged_children[$tableId]) && count($merged_children[$tableId]) > 0) {
                $statusText = 'محجوزة ومدمجة (مع ' . implode(', ', $merged_children[$tableId]) . ')';
            } else {
                $statusText = 'محجوزة ومدمجة';
            }
        } elseif ($isMerged) {
            $statusClass = 'btn-warning text-dark border-warning shadow-sm';
            $statusIcon = 'fa-object-group';
            if ($parentTableId) {
                $statusText = 'مدمجة مع ' . $parentTableName;
            } elseif (isset($merged_children[$tableId]) && count($merged_children[$tableId]) > 0) {
                $statusText = 'مدمجة (مع ' . implode(', ', $merged_children[$tableId]) . ')';
            } else {
                $statusText = 'مدمجة';
            }
        } elseif ($hasActiveOrder) {
            $statusClass = 'btn-danger text-white shadow-sm';
            $statusIcon = 'fa-utensils';
            $statusText = 'مشغولة';
        } else {
            $statusClass = 'btn-success text-white shadow-sm';
            $statusIcon = 'fa-check-circle';
            $statusText = 'متاحة';
        }

        // Get order total & orderId
        $orderTotal = 0;
        $orderId = null;
        $targetSearchName = $parentTableName ?: $tableName;
        
        if ($hasActiveOrder) {
            $orderQuery = $conn->query("
                SELECT id, fat_net 
                FROM ot_head 
                WHERE info LIKE '%$targetSearchName%' 
                AND pro_tybe = 9 
                AND isdeleted = 0
                AND fat_net > 0
                ORDER BY id DESC 
                LIMIT 1");
            if ($orderQuery && $orderQuery->num_rows > 0) {
                $orderData = $orderQuery->fetch_assoc();
                $orderId = $orderData['id'];
                $orderTotal = floatval($orderData['fat_net']);
            }
        }
?>
<div class="col-3 position-relative table-card-wrapper" data-table-id="<?= $tableId ?>">
    <div class="card h-100 border-2 overflow-hidden shadow-sm table-card-box position-relative" style="transition: all 0.2s ease;">
        <!-- Checkbox overlay for merge mode -->
        <div class="form-check position-absolute top-0 end-0 m-2 merge-checkbox-wrapper" style="display: none; z-index: 25;">
            <input class="form-check-input table-merge-checkbox" 
                   type="checkbox" 
                   value="<?= $tableId ?>" 
                   id="chk_table_<?= $tableId ?>" 
                   style="width: 24px; height: 24px; cursor: pointer; border: 2px solid #0d6efd;">
        </div>

        <?php if ($isMerged): ?>
        <!-- الطاولات المدمجة: نستخدم div بدل button لتجنب nested buttons -->
        <div
            class="btn <?= $statusClass ?> w-100 h-100 table-select-btn p-3 position-relative d-flex flex-column justify-content-between"
            data-table-id="<?= $tableId ?>" 
            data-table-name="<?= $tableName ?>"
            data-table-case="<?= $tableCase ?>" 
            data-order-id="<?= $orderId ?>"
            data-is-merged="1"
            data-parent-id="<?= $parentTableId ?: '' ?>"
            role="button"
            style="min-height: 120px; font-size: 1.05rem; cursor: pointer;">
            
            <div class="d-flex flex-column align-items-center justify-content-center w-100">
                <i class="fas <?= $statusIcon ?> fa-2x mb-2"></i>
                <h6 class="fw-bold mb-1"><?= $tableName ?></h6>
                <small class="d-flex align-items-center fw-semibold">
                    <i class="fas <?= $statusIcon ?> me-1"></i>
                    <?= $statusText ?>
                </small>

                <?php if ($orderTotal > 0): ?>
                <div class="mt-2 badge bg-white text-dark border shadow-sm px-2 py-1 fs-6">
                    <?= number_format($orderTotal, 2) ?> ج.م
                </div>
                <?php endif; ?>
            </div>

            <div class="w-100 mt-2 text-center">
                <button type="button" 
                        class="btn btn-sm btn-outline-dark bg-white py-0 px-2 text-danger fw-bold shadow-sm" 
                        onclick="event.stopPropagation(); unmergeSingleTable(<?= $tableId ?>)" 
                        title="فك دمج هذه الطاولة">
                    <i class="fas fa-unlink me-1"></i>فك الدمج
                </button>
            </div>
        </div>
        <?php else: ?>
        <button type="button"
            class="btn <?= $statusClass ?> w-100 h-100 table-select-btn p-3 position-relative d-flex flex-column justify-content-center align-items-center"
            data-table-id="<?= $tableId ?>" 
            data-table-name="<?= $tableName ?>"
            data-table-case="<?= $tableCase ?>" 
            data-order-id="<?= $orderId ?>"
            data-is-merged="0"
            data-parent-id=""
            style="min-height: 120px; font-size: 1.05rem;">
            
            <i class="fas <?= $statusIcon ?> fa-2x mb-2"></i>
            <h6 class="fw-bold mb-1"><?= $tableName ?></h6>
            <small class="d-flex align-items-center fw-semibold">
                <i class="fas <?= $statusIcon ?> me-1"></i>
                <?= $statusText ?>
            </small>

            <?php if ($orderTotal > 0): ?>
            <div class="mt-2 badge bg-white text-dark border shadow-sm px-2 py-1 fs-6">
                <?= number_format($orderTotal, 2) ?> ج.م
            </div>
            <?php endif; ?>
        </button>
        <?php endif; ?>
    </div>
</div>
<?php
    }
} else {
    echo '<div class="col-12 text-center text-muted py-4">
            <i class="fas fa-exclamation-circle fa-3x mb-3 text-secondary"></i>
            <p class="fs-5">لا توجد طاولات متاحة</p>
          </div>';
}
?>
