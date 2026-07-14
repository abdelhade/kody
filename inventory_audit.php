<?php include('includes/header.php') ?>
<?php include('includes/navbar.php') ?>
<?php include('includes/sidebar.php') ?>

<?php
// جلب جميع الأصناف النشطة مع وحدتها الافتراضية
$sql = "SELECT m.*, 
               (SELECT u.uname FROM myunits u 
                INNER JOIN item_units iu ON iu.unit_id = u.id 
                WHERE iu.item_id = m.id AND iu.def_stock = 1 
                LIMIT 1) AS unit_name,
               (SELECT u.uname FROM myunits u 
                INNER JOIN item_units iu ON iu.unit_id = u.id 
                WHERE iu.item_id = m.id 
                LIMIT 1) AS unit_name_fallback
        FROM myitems m 
        WHERE m.isdeleted = 0 
        ORDER BY m.iname";
$result = $conn->query($sql);
$items  = [];
while ($row = $result->fetch_assoc()) {
    $row['display_unit'] = $row['unit_name'] ?: ($row['unit_name_fallback'] ?: '-');
    $items[] = $row;
}
$total = count($items);
?>

<style>
/* ========== INVENTORY AUDIT PAGE STYLES ========== */
.audit-hero {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
    border-radius: 16px;
    padding: 28px 32px 20px;
    margin-bottom: 24px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 8px 32px rgba(15, 52, 96, 0.5);
}
.audit-hero::before {
    content: '';
    position: absolute;
    top: -60px; right: -60px;
    width: 200px; height: 200px;
    border-radius: 50%;
    background: rgba(100, 200, 255, 0.07);
}
.audit-hero::after {
    content: '';
    position: absolute;
    bottom: -40px; left: -40px;
    width: 150px; height: 150px;
    border-radius: 50%;
    background: rgba(100, 200, 255, 0.05);
}
.audit-hero h1 {
    color: #ffffff;
    font-size: 1.7rem;
    font-weight: 700;
    margin: 0 0 6px;
    text-shadow: 0 2px 8px rgba(0,0,0,0.3);
}
.audit-hero p {
    color: rgba(200, 230, 255, 0.75);
    margin: 0;
    font-size: 0.95rem;
}
.audit-hero .hero-icon {
    font-size: 3rem;
    color: rgba(100, 200, 255, 0.35);
    position: absolute;
    top: 20px;
    left: 30px;
}

/* Stats cards */
.stat-card {
    border-radius: 14px;
    padding: 20px 22px;
    text-align: center;
    position: relative;
    overflow: hidden;
    transition: transform 0.2s, box-shadow 0.2s;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    border: none;
}
.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 28px rgba(0,0,0,0.15);
}
.stat-card .stat-number {
    font-size: 2.4rem;
    font-weight: 800;
    line-height: 1;
    margin-bottom: 6px;
}
.stat-card .stat-label {
    font-size: 0.82rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    opacity: 0.85;
}
.stat-card .stat-icon {
    position: absolute;
    bottom: -5px; right: 12px;
    font-size: 3.5rem;
    opacity: 0.12;
}
.stat-total   { background: linear-gradient(135deg, #667eea, #764ba2); color: #fff; }
.stat-match   { background: linear-gradient(135deg, #11998e, #38ef7d); color: #fff; }
.stat-deficit { background: linear-gradient(135deg, #ff416c, #ff4b2b); color: #fff; }
.stat-surplus { background: linear-gradient(135deg, #f7971e, #ffd200); color: #fff; }

/* Main table card */
.audit-card {
    border-radius: 16px;
    border: none;
    box-shadow: 0 4px 24px rgba(0,0,0,0.08);
    overflow: hidden;
}
.audit-card .card-header {
    background: linear-gradient(90deg, #0f3460 0%, #16213e 100%);
    border-bottom: none;
    padding: 16px 24px;
}
.audit-card .card-header h5 {
    color: #fff;
    margin: 0;
    font-weight: 700;
    font-size: 1.05rem;
}
.audit-card .card-body { padding: 0; }

/* Table */
#auditTable thead tr {
    background: linear-gradient(90deg, #f0f4ff 0%, #e8f0fe 100%);
}
#auditTable thead th {
    font-weight: 700;
    font-size: 0.82rem;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    color: #3a4a6b;
    border-bottom: 2px solid #c5d0f0;
    padding: 13px 14px;
    white-space: nowrap;
}
#auditTable tbody tr {
    transition: background 0.15s;
}
#auditTable tbody tr:hover {
    background: #f4f7ff !important;
}
#auditTable tbody td {
    padding: 11px 14px;
    vertical-align: middle;
    font-size: 0.9rem;
    border-bottom: 1px solid #eef2ff;
}
.item-name {
    font-weight: 700;
    color: #1a1a2e;
}
.sys-qty {
    font-weight: 700;
    color: #5a6edb;
    font-size: 1rem;
}
.qty-input {
    width: 100px;
    text-align: center;
    font-weight: 700;
    border: 2px solid #d0d7ff;
    border-radius: 8px;
    padding: 5px 8px;
    font-size: 0.95rem;
    transition: border-color 0.2s, box-shadow 0.2s;
    background: #fff;
}
.qty-input:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
    outline: none;
}

/* Difference badge */
.diff-badge {
    display: inline-block;
    min-width: 80px;
    padding: 5px 10px;
    border-radius: 20px;
    font-weight: 800;
    font-size: 0.92rem;
    text-align: center;
    transition: all 0.25s;
}
.diff-zero    { background: #e8f5e9; color: #2e7d32; }
.diff-surplus { background: #e8f5e9; color: #1b5e20; }
.diff-deficit { background: #ffebee; color: #b71c1c; }

/* Row highlight for audited rows */
.row-audited    { background: rgba(56, 239, 125, 0.04) !important; }
.row-deficit    { background: rgba(255, 65, 108, 0.04) !important; }

/* Buttons */
.btn-save-all {
    background: linear-gradient(135deg, #11998e, #38ef7d);
    color: #fff;
    border: none;
    border-radius: 10px;
    font-weight: 700;
    padding: 10px 28px;
    font-size: 0.95rem;
    transition: transform 0.15s, box-shadow 0.15s;
    box-shadow: 0 4px 16px rgba(17, 153, 142, 0.35);
}
.btn-save-all:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(17, 153, 142, 0.5);
    color: #fff;
}
.btn-save-all:disabled {
    opacity: 0.6;
    transform: none;
}
.btn-settle-row {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 4px 12px;
    font-size: 0.78rem;
    font-weight: 600;
    cursor: pointer;
    transition: opacity 0.15s, transform 0.15s;
}
.btn-settle-row:hover {
    opacity: 0.85;
    transform: scale(1.04);
}
.btn-settle-row:disabled {
    opacity: 0.35;
    cursor: not-allowed;
}
.btn-reset-row {
    background: linear-gradient(135deg, #f7971e, #ffd200);
    color: #333;
    border: none;
    border-radius: 8px;
    padding: 4px 10px;
    font-size: 0.78rem;
    font-weight: 600;
    cursor: pointer;
    transition: opacity 0.15s;
}
.btn-reset-row:hover { opacity: 0.8; }

/* Search bar */
.audit-search {
    border: 2px solid #d0d7ff;
    border-radius: 10px;
    padding: 8px 16px;
    width: 280px;
    font-size: 0.92rem;
    transition: border-color 0.2s, box-shadow 0.2s;
}
.audit-search:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
    outline: none;
}

/* Toast notification */
.audit-toast {
    position: fixed;
    top: 20px;
    left: 50%;
    transform: translateX(-50%) translateY(-80px);
    z-index: 9999;
    padding: 14px 28px;
    border-radius: 12px;
    font-weight: 700;
    font-size: 0.95rem;
    box-shadow: 0 8px 32px rgba(0,0,0,0.2);
    transition: transform 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
    min-width: 250px;
    text-align: center;
}
.audit-toast.show { transform: translateX(-50%) translateY(0); }
.audit-toast.success { background: linear-gradient(135deg, #11998e, #38ef7d); color: #fff; }
.audit-toast.error   { background: linear-gradient(135deg, #ff416c, #ff4b2b); color: #fff; }

/* ========== AUDIT HISTORY SECTION ========== */
.history-section {
    margin-top: 32px;
}
.history-card {
    border-radius: 16px;
    border: none;
    box-shadow: 0 4px 24px rgba(0,0,0,0.08);
    overflow: hidden;
}
.history-card .card-header {
    background: linear-gradient(90deg, #1a1a2e 0%, #2d1b69 100%);
    border-bottom: none;
    padding: 16px 24px;
}
.history-card .card-header h5 {
    color: #fff;
    margin: 0;
    font-weight: 700;
    font-size: 1.05rem;
}
.history-filters {
    padding: 16px 24px;
    background: #f8f9ff;
    border-bottom: 1px solid #e8eeff;
    display: flex;
    gap: 12px;
    align-items: center;
    flex-wrap: wrap;
}
.history-filters label {
    font-weight: 600;
    font-size: 0.85rem;
    color: #3a4a6b;
    margin: 0;
}
.history-filters input[type=date] {
    border: 2px solid #d0d7ff;
    border-radius: 8px;
    padding: 6px 12px;
    font-size: 0.88rem;
    transition: border-color 0.2s;
}
.history-filters input[type=date]:focus {
    border-color: #667eea;
    outline: none;
    box-shadow: 0 0 0 3px rgba(102,126,234,0.15);
}
.btn-filter {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 7px 20px;
    font-weight: 700;
    font-size: 0.85rem;
    cursor: pointer;
    transition: transform 0.15s, box-shadow 0.15s;
}
.btn-filter:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(102,126,234,0.4);
}

/* Session card */
.session-card {
    border: 1px solid #e8eeff;
    border-radius: 12px;
    margin: 12px 16px;
    overflow: hidden;
    transition: box-shadow 0.2s;
}
.session-card:hover {
    box-shadow: 0 4px 16px rgba(0,0,0,0.08);
}
.session-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 14px 18px;
    background: linear-gradient(90deg, #f0f4ff, #f8f4ff);
    cursor: pointer;
    user-select: none;
    flex-wrap: wrap;
    gap: 8px;
}
.session-header:hover {
    background: linear-gradient(90deg, #e8eeff, #f0eaff);
}
.session-info {
    display: flex;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
}
.session-date {
    font-weight: 800;
    color: #1a1a2e;
    font-size: 0.95rem;
}
.session-user {
    font-size: 0.82rem;
    color: #667eea;
    font-weight: 600;
}
.session-badges {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
}
.session-badge {
    padding: 3px 10px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 700;
}
.session-badge.items   { background: #e8eeff; color: #3a4a6b; }
.session-badge.surplus  { background: #e8f5e9; color: #1b5e20; }
.session-badge.deficit  { background: #ffebee; color: #b71c1c; }
.session-badge.match    { background: #e0f7fa; color: #006064; }

.session-toggle {
    font-size: 1.1rem;
    color: #667eea;
    transition: transform 0.25s;
}
.session-toggle.open {
    transform: rotate(180deg);
}

.session-details {
    display: none;
    padding: 0;
}
.session-details.show {
    display: block;
}
.session-details table {
    width: 100%;
    margin: 0;
}
.session-details table thead tr {
    background: #f8f9ff;
}
.session-details table thead th {
    font-size: 0.78rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    color: #3a4a6b;
    padding: 10px 14px;
    border-bottom: 2px solid #e8eeff;
    white-space: nowrap;
}
.session-details table tbody td {
    padding: 9px 14px;
    font-size: 0.87rem;
    border-bottom: 1px solid #f0f4ff;
    vertical-align: middle;
}
.session-details table tbody tr:hover {
    background: #f8f9ff;
}

.history-empty {
    text-align: center;
    padding: 48px 24px;
    color: #aaa;
}
.history-empty i {
    font-size: 3rem;
    margin-bottom: 12px;
    color: #d0d7ff;
}
.history-empty p {
    font-size: 0.95rem;
    font-weight: 600;
}

.history-loading {
    text-align: center;
    padding: 40px;
    color: #667eea;
}
.history-loading i {
    font-size: 2rem;
    margin-bottom: 8px;
}

/* Print */
@media print {
    .no-print, .sidebar-mini, nav, .btn, .audit-search, .qty-input, .history-section { display: none !important; }
    #auditTable tbody td input { border: none !important; }
}
</style>

<div class="content-wrapper" style="background: #f5f7ff;">
<section class="content-header">
<div class="container-fluid">

<!-- Toast -->
<div id="auditToast" class="audit-toast"></div>

<!-- Hero Header -->
<div class="audit-hero mb-4">
    <i class="fas fa-boxes hero-icon"></i>
    <h1><i class="fas fa-clipboard-check me-2"></i> جرد المخزون</h1>
    <p>مقارنة الكمية الفعلية في المستودع مع الكمية المسجلة في النظام · تسوية الفروقات فوراً</p>
</div>

<!-- Stats Row -->
<div class="row mb-4" id="statsRow">
    <div class="col-6 col-md-3 mb-3">
        <div class="stat-card stat-total">
            <div class="stat-number" id="statTotal"><?= $total ?></div>
            <div class="stat-label">إجمالي الأصناف</div>
            <i class="fas fa-boxes stat-icon"></i>
        </div>
    </div>
    <div class="col-6 col-md-3 mb-3">
        <div class="stat-card stat-match">
            <div class="stat-number" id="statMatch">0</div>
            <div class="stat-label">مطابق</div>
            <i class="fas fa-check-circle stat-icon"></i>
        </div>
    </div>
    <div class="col-6 col-md-3 mb-3">
        <div class="stat-card stat-deficit">
            <div class="stat-number" id="statDeficit">0</div>
            <div class="stat-label">عجز</div>
            <i class="fas fa-arrow-down stat-icon"></i>
        </div>
    </div>
    <div class="col-6 col-md-3 mb-3">
        <div class="stat-card stat-surplus">
            <div class="stat-number" id="statSurplus">0</div>
            <div class="stat-label">زيادة</div>
            <i class="fas fa-arrow-up stat-icon"></i>
        </div>
    </div>
</div>

<!-- Main Card -->
<div class="audit-card card no-print mb-3">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h5><i class="fas fa-table ml-2"></i> جدول الجرد</h5>
        <div class="d-flex gap-2 flex-wrap align-items-center">
            <input type="text" id="auditSearch" class="audit-search frst no-print" placeholder="🔍 بحث عن صنف...">
            <button class="btn btn-outline-light btn-sm no-print" onclick="window.print()">
                <i class="fas fa-print ml-1"></i> طباعة
            </button>
            <button id="btnResetAll" class="btn btn-sm no-print" style="background:rgba(255,255,255,0.15);color:#fff;border:1px solid rgba(255,255,255,0.3);border-radius:8px;">
                <i class="fas fa-undo ml-1"></i> تصفير الجرد
            </button>
            <button id="btnSaveAll" class="btn-save-all no-print" disabled>
                <i class="fas fa-save ml-1"></i> حفظ وتسوية الجرد
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="auditTable" class="table mb-0">
                <thead>
                    <tr>
                        <th style="width:40px;">#</th>
                        <th>اسم الصنف</th>
                        <th>الباركود</th>
                        <th>الوحدة</th>
                        <th style="text-align:center;">الكمية بالسيستم</th>
                        <th style="text-align:center;">الكمية الفعلية (الجرد)</th>
                        <th style="text-align:center;">الفرق</th>
                        <th class="no-print" style="text-align:center;">تسوية</th>
                    </tr>
                </thead>
                <tbody>
                <?php $i = 0; foreach ($items as $row): $i++; ?>
                    <tr id="row-<?= $row['id'] ?>" data-id="<?= $row['id'] ?>" data-sys-qty="<?= floatval($row['itmqty']) ?>">
                        <td style="color:#aaa;font-size:0.8rem;"><?= $i ?></td>
                        <td>
                            <span class="item-name"><?= htmlspecialchars($row['iname']) ?></span>
                            <?php if (!empty($row['code'])): ?>
                                <br><small class="text-muted"><?= htmlspecialchars($row['code']) ?></small>
                            <?php endif; ?>
                        </td>
                        <td><code style="color:#667eea;font-size:0.85rem;"><?= htmlspecialchars($row['barcode'] ?? '') ?></code></td>
                        <td><span class="badge" style="background:#e8eeff;color:#3a4a6b;padding:4px 10px;border-radius:6px;"><?= htmlspecialchars($row['display_unit']) ?></span></td>
                        <td style="text-align:center;">
                            <span class="sys-qty" id="sys-<?= $row['id'] ?>"><?= number_format(floatval($row['itmqty']), 2) ?></span>
                        </td>
                        <td style="text-align:center;">
                            <input type="number"
                                   class="qty-input"
                                   id="actual-<?= $row['id'] ?>"
                                   data-row-id="<?= $row['id'] ?>"
                                   value="<?= floatval($row['itmqty']) ?>"
                                   step="0.01"
                                   min="0"
                                   onchange="calcDiff(<?= $row['id'] ?>, <?= floatval($row['itmqty']) ?>)"
                                   oninput="calcDiff(<?= $row['id'] ?>, <?= floatval($row['itmqty']) ?>)">
                        </td>
                        <td style="text-align:center;">
                            <span class="diff-badge diff-zero" id="diff-<?= $row['id'] ?>">0.00</span>
                        </td>
                        <td class="no-print" style="text-align:center;">
                            <button class="btn-settle-row"
                                    id="settle-<?= $row['id'] ?>"
                                    disabled
                                    onclick="settleRow(<?= $row['id'] ?>, <?= floatval($row['itmqty']) ?>)"
                                    title="تسوية هذا الصنف فقط">
                                <i class="fas fa-check"></i> تسوية
                            </button>
                            <button class="btn-reset-row mt-1"
                                    onclick="resetRow(<?= $row['id'] ?>, <?= floatval($row['itmqty']) ?>)"
                                    title="إعادة الكمية للقيمة بالسيستم">
                                <i class="fas fa-undo"></i>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Diff Summary Row -->
<div class="alert" id="diffSummaryBox" style="border-radius:12px;background:linear-gradient(90deg,#eef2ff,#f4f7ff);border:1px solid #c5d0f0;display:none;">
    <div class="row text-center">
        <div class="col-md-6">
            <strong style="color:#3a4a6b;">إجمالي الزيادة:</strong>
            <span id="totalSurplus" style="color:#1b5e20;font-weight:800;font-size:1.1rem;">0.00</span>
        </div>
        <div class="col-md-6">
            <strong style="color:#3a4a6b;">إجمالي العجز:</strong>
            <span id="totalDeficit" style="color:#b71c1c;font-weight:800;font-size:1.1rem;">0.00</span>
        </div>
    </div>
</div>

<!-- ========== AUDIT HISTORY SECTION ========== -->
<div class="history-section no-print">
    <div class="history-card card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5><i class="fas fa-history ml-2"></i> سجل الجرد السابق</h5>
        </div>
        <div class="history-filters">
            <label><i class="fas fa-calendar-alt ml-1"></i> من:</label>
            <input type="date" id="historyDateFrom">
            <label><i class="fas fa-calendar-alt ml-1"></i> إلى:</label>
            <input type="date" id="historyDateTo">
            <button class="btn-filter" onclick="loadAuditHistory()">
                <i class="fas fa-search ml-1"></i> عرض السجل
            </button>
            <button class="btn-filter" style="background:linear-gradient(135deg,#f7971e,#ffd200);color:#333;" onclick="loadAuditHistory('all')">
                <i class="fas fa-list ml-1"></i> عرض الكل
            </button>
        </div>
        <div id="historyContent">
            <div class="history-empty">
                <i class="fas fa-clipboard-list d-block"></i>
                <p>اختر تاريخ واضغط "عرض السجل" لعرض جرد سابق</p>
            </div>
        </div>
    </div>
</div>

</div>
</section>
</div>

<?php include('includes/footer.php') ?>

<script>
// ======================================================
// INVENTORY AUDIT — JAVASCRIPT
// ======================================================

const sysQties   = {};   // item_id => system qty
const diffMap    = {};   // item_id => diff value

// Init: read all system qtys on page load
document.querySelectorAll('tr[data-id]').forEach(function(tr) {
    const id  = parseInt(tr.dataset.id);
    const qty = parseFloat(tr.dataset.sysQty);
    sysQties[id] = qty;
    diffMap[id]  = 0;
});

// ─── Calculate difference for one row ─────────────────
function calcDiff(id, originalSysQty) {
    const actualInput = document.getElementById('actual-' + id);
    const diffBadge   = document.getElementById('diff-' + id);
    const settleBtn   = document.getElementById('settle-' + id);
    const tr          = document.getElementById('row-' + id);

    const actual  = parseFloat(actualInput.value) || 0;
    // Use the live sys qty (may have been settled)
    const sysQty  = sysQties[id];
    const diff    = actual - sysQty;

    diffMap[id] = diff;

    // Format diff
    const sign = diff > 0 ? '+' : '';
    diffBadge.textContent = sign + diff.toFixed(2);

    // Styling
    diffBadge.className = 'diff-badge';
    tr.classList.remove('row-audited', 'row-deficit');

    if (diff === 0) {
        diffBadge.classList.add('diff-zero');
    } else if (diff > 0) {
        diffBadge.classList.add('diff-surplus');
        tr.classList.add('row-audited');
    } else {
        diffBadge.classList.add('diff-deficit');
        tr.classList.add('row-deficit');
    }

    // Enable / disable settle button
    settleBtn.disabled = (diff === 0);

    // Update global stats & save button
    updateStats();
    updateSummary();
}

// ─── Update top stats cards ────────────────────────────
function updateStats() {
    let match = 0, deficit = 0, surplus = 0;
    Object.values(diffMap).forEach(function(d) {
        if (d === 0)      match++;
        else if (d < 0)   deficit++;
        else              surplus++;
    });
    document.getElementById('statMatch').textContent   = match;
    document.getElementById('statDeficit').textContent = deficit;
    document.getElementById('statSurplus').textContent = surplus;

    // Enable save-all only if there are differences
    const hasChanges = Object.values(diffMap).some(d => d !== 0);
    document.getElementById('btnSaveAll').disabled = !hasChanges;
}

// ─── Update totals summary bar ─────────────────────────
function updateSummary() {
    let totalSurplus = 0, totalDeficit = 0;
    Object.values(diffMap).forEach(function(d) {
        if (d > 0) totalSurplus += d;
        if (d < 0) totalDeficit += Math.abs(d);
    });
    const box = document.getElementById('diffSummaryBox');
    if (totalSurplus > 0 || totalDeficit > 0) {
        box.style.display = '';
    } else {
        box.style.display = 'none';
    }
    document.getElementById('totalSurplus').textContent = totalSurplus.toFixed(2);
    document.getElementById('totalDeficit').textContent = totalDeficit.toFixed(2);
}

// ─── Reset a single row ────────────────────────────────
function resetRow(id, sysQty) {
    const actualInput = document.getElementById('actual-' + id);
    actualInput.value = sysQties[id].toFixed(2);
    calcDiff(id, sysQties[id]);
}

// ─── Reset all rows ────────────────────────────────────
document.getElementById('btnResetAll').addEventListener('click', function() {
    if (!confirm('هل تريد إعادة الكميات الفعلية لتساوي الكميات بالسيستم؟')) return;
    document.querySelectorAll('tr[data-id]').forEach(function(tr) {
        const id = parseInt(tr.dataset.id);
        resetRow(id, sysQties[id]);
    });
});

// ─── Settle single row ─────────────────────────────────
function settleRow(id, originalSysQty) {
    const actual = parseFloat(document.getElementById('actual-' + id).value) || 0;
    const btn    = document.getElementById('settle-' + id);
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

    fetch('ajax/save_inventory_audit.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ items: [{ id: id, qty: actual }] })
    })
    .then(r => r.json())
    .then(function(res) {
        if (res.success) {
            // Update system qty to match new settled qty
            sysQties[id] = actual;
            diffMap[id]  = 0;
            document.getElementById('sys-' + id).textContent = actual.toFixed(2);
            document.getElementById('diff-' + id).textContent = '0.00';
            document.getElementById('diff-' + id).className  = 'diff-badge diff-zero';
            const tr = document.getElementById('row-' + id);
            tr.classList.remove('row-audited', 'row-deficit');
            btn.innerHTML = '<i class="fas fa-check"></i> تسوية';
            btn.disabled  = true;
            updateStats();
            updateSummary();
            showToast('تمت التسوية بنجاح ✔', 'success');
        } else {
            showToast('خطأ: ' + res.message, 'error');
            btn.innerHTML = '<i class="fas fa-check"></i> تسوية';
            btn.disabled  = false;
        }
    })
    .catch(function() {
        showToast('تعذّر الاتصال بالسيرفر', 'error');
        btn.innerHTML = '<i class="fas fa-check"></i> تسوية';
        btn.disabled  = false;
    });
}

// ─── Save all differences ──────────────────────────────
document.getElementById('btnSaveAll').addEventListener('click', function() {
    const items = [];
    document.querySelectorAll('tr[data-id]').forEach(function(tr) {
        const id  = parseInt(tr.dataset.id);
        if (diffMap[id] !== 0) {
            const actual = parseFloat(document.getElementById('actual-' + id).value) || 0;
            items.push({ id: id, qty: actual });
        }
    });
    if (items.length === 0) return;

    const btn = document.getElementById('btnSaveAll');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin ml-1"></i> جاري الحفظ...';

    fetch('ajax/save_inventory_audit.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ items: items })
    })
    .then(r => r.json())
    .then(function(res) {
        if (res.success) {
            // Update all settled rows
            items.forEach(function(item) {
                sysQties[item.id] = item.qty;
                diffMap[item.id]  = 0;
                document.getElementById('sys-' + item.id).textContent = item.qty.toFixed(2);
                document.getElementById('diff-' + item.id).textContent = '0.00';
                document.getElementById('diff-' + item.id).className   = 'diff-badge diff-zero';
                const tr = document.getElementById('row-' + item.id);
                tr.classList.remove('row-audited', 'row-deficit');
                const settleBtn = document.getElementById('settle-' + item.id);
                if (settleBtn) { settleBtn.disabled = true; settleBtn.innerHTML = '<i class="fas fa-check"></i> تسوية'; }
            });
            updateStats();
            updateSummary();
            showToast('تمت تسوية الجرد بنجاح ✔ (' + items.length + ' صنف)', 'success');
        } else {
            showToast('خطأ: ' + res.message, 'error');
        }
    })
    .catch(function() {
        showToast('تعذّر الاتصال بالسيرفر', 'error');
    })
    .finally(function() {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save ml-1"></i> حفظ وتسوية الجرد';
        updateStats();
    });
});

// ─── Live search ───────────────────────────────────────
document.getElementById('auditSearch').addEventListener('input', function() {
    const q = this.value.trim().toLowerCase();
    document.querySelectorAll('#auditTable tbody tr').forEach(function(tr) {
        if (!q) { tr.style.display = ''; return; }
        const text = tr.textContent.toLowerCase();
        tr.style.display = text.includes(q) ? '' : 'none';
    });
});

// ─── Toast ─────────────────────────────────────────────
function showToast(msg, type) {
    const toast = document.getElementById('auditToast');
    toast.textContent = msg;
    toast.className   = 'audit-toast ' + type;
    setTimeout(() => toast.classList.add('show'), 10);
    setTimeout(() => toast.classList.remove('show'), 3200);
}

// Initialize stats on load
updateStats();

// ======================================================
// AUDIT HISTORY — JAVASCRIPT
// ======================================================

function loadAuditHistory(mode) {
    const container = document.getElementById('historyContent');
    container.innerHTML = '<div class="history-loading"><i class="fas fa-spinner fa-spin d-block"></i><p>جاري تحميل السجل...</p></div>';

    let url = 'ajax/get_audit_history.php?';
    if (mode !== 'all') {
        const dateFrom = document.getElementById('historyDateFrom').value;
        const dateTo   = document.getElementById('historyDateTo').value;
        if (dateFrom) url += 'date_from=' + dateFrom + '&';
        if (dateTo)   url += 'date_to=' + dateTo + '&';
    }

    fetch(url)
    .then(r => r.json())
    .then(function(res) {
        if (!res.success) {
            container.innerHTML = '<div class="history-empty"><i class="fas fa-exclamation-triangle d-block" style="color:#ff416c;"></i><p>' + (res.message || 'حدث خطأ') + '</p></div>';
            return;
        }

        if (res.sessions.length === 0) {
            container.innerHTML = '<div class="history-empty"><i class="fas fa-inbox d-block"></i><p>لا يوجد سجل جرد في هذه الفترة</p></div>';
            return;
        }

        let html = '';
        res.sessions.forEach(function(s, idx) {
            const sessionDate = new Date(s.session_date);
            const dateStr = sessionDate.toLocaleDateString('ar-EG', {
                year: 'numeric', month: 'long', day: 'numeric',
                hour: '2-digit', minute: '2-digit'
            });

            html += '<div class="session-card">';
            html += '<div class="session-header" onclick="toggleSession(\'' + s.audit_session + '\', this)">';
            html += '<div class="session-info">';
            html += '<span class="session-date"><i class="fas fa-calendar-day ml-1"></i> ' + dateStr + '</span>';
            html += '<span class="session-user"><i class="fas fa-user ml-1"></i> ' + escHtml(s.user_name || 'غير معروف') + '</span>';
            html += '</div>';
            html += '<div class="d-flex align-items-center gap-2">';
            html += '<div class="session-badges">';
            html += '<span class="session-badge items">' + s.items_count + ' صنف</span>';
            if (parseFloat(s.total_surplus) > 0)
                html += '<span class="session-badge surplus">+' + parseFloat(s.total_surplus).toFixed(2) + ' زيادة</span>';
            if (parseFloat(s.total_deficit) > 0)
                html += '<span class="session-badge deficit">-' + parseFloat(s.total_deficit).toFixed(2) + ' عجز</span>';
            if (parseInt(s.match_count) > 0)
                html += '<span class="session-badge match">' + s.match_count + ' مطابق</span>';
            html += '</div>';
            html += '<i class="fas fa-chevron-down session-toggle"></i>';
            html += '</div>';
            html += '</div>';
            html += '<div class="session-details" id="details-' + s.audit_session + '"></div>';
            html += '</div>';
        });

        container.innerHTML = html;
    })
    .catch(function() {
        container.innerHTML = '<div class="history-empty"><i class="fas fa-exclamation-triangle d-block" style="color:#ff416c;"></i><p>تعذّر الاتصال بالسيرفر</p></div>';
    });
}

function toggleSession(sessionId, headerEl) {
    const details = document.getElementById('details-' + sessionId);
    const toggle  = headerEl.querySelector('.session-toggle');

    if (details.classList.contains('show')) {
        details.classList.remove('show');
        toggle.classList.remove('open');
        return;
    }

    // Load details if empty
    if (!details.innerHTML.trim()) {
        details.innerHTML = '<div class="history-loading" style="padding:20px;"><i class="fas fa-spinner fa-spin"></i> جاري التحميل...</div>';
        details.classList.add('show');
        toggle.classList.add('open');

        fetch('ajax/get_audit_history.php?session=' + encodeURIComponent(sessionId))
        .then(r => r.json())
        .then(function(res) {
            if (!res.success || !res.details || res.details.length === 0) {
                details.innerHTML = '<div style="padding:16px;text-align:center;color:#aaa;">لا توجد تفاصيل</div>';
                return;
            }

            let html = '<table>';
            html += '<thead><tr>';
            html += '<th>#</th>';
            html += '<th>اسم الصنف</th>';
            html += '<th>الباركود</th>';
            html += '<th style="text-align:center;">الكمية قبل</th>';
            html += '<th style="text-align:center;">الكمية بعد (الجرد)</th>';
            html += '<th style="text-align:center;">الفرق</th>';
            html += '</tr></thead>';
            html += '<tbody>';

            res.details.forEach(function(d, i) {
                const diff = parseFloat(d.qty_diff);
                let diffClass = 'diff-zero';
                let diffSign  = '';
                if (diff > 0)      { diffClass = 'diff-surplus'; diffSign = '+'; }
                else if (diff < 0) { diffClass = 'diff-deficit'; }

                html += '<tr>';
                html += '<td style="color:#aaa;font-size:0.8rem;">' + (i+1) + '</td>';
                html += '<td><strong>' + escHtml(d.item_name) + '</strong></td>';
                html += '<td><code style="color:#667eea;font-size:0.85rem;">' + escHtml(d.item_barcode || '') + '</code></td>';
                html += '<td style="text-align:center;font-weight:700;color:#5a6edb;">' + parseFloat(d.qty_before).toFixed(2) + '</td>';
                html += '<td style="text-align:center;font-weight:700;color:#1a1a2e;">' + parseFloat(d.qty_actual).toFixed(2) + '</td>';
                html += '<td style="text-align:center;"><span class="diff-badge ' + diffClass + '">' + diffSign + diff.toFixed(2) + '</span></td>';
                html += '</tr>';
            });

            html += '</tbody></table>';
            details.innerHTML = html;
        })
        .catch(function() {
            details.innerHTML = '<div style="padding:16px;text-align:center;color:#ff416c;">خطأ في تحميل التفاصيل</div>';
        });
    } else {
        details.classList.add('show');
        toggle.classList.add('open');
    }
}

function escHtml(str) {
    if (!str) return '';
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}
</script>
