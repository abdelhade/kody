<?php include('includes/header.php') ?>
<?php include('includes/navbar.php') ?>
<?php include('includes/sidebar.php') ?>
<?php include('includes/connect.php') ?>

<?php
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$sqlemp = "SELECT * FROM `employees` WHERE id = '$id'";
$resemp = $conn->query($sqlemp);
$rowemp = $resemp ? $resemp->fetch_assoc() : null;
$empValid = $rowemp && isset($rowemp['id']);

$jopName = '';
$dprtName = '';
$townName = '';
if ($empValid) {
    if (!empty($rowemp['jop'])) {
        $rowjop = $conn->query("SELECT name FROM `jops` WHERE id = '" . (int) $rowemp['jop'] . "'")->fetch_assoc();
        $jopName = $rowjop['name'] ?? '';
    }
    if (!empty($rowemp['department'])) {
        $rowdprt = $conn->query("SELECT name FROM `departments` WHERE id = '" . (int) $rowemp['department'] . "'")->fetch_assoc();
        $dprtName = $rowdprt['name'] ?? '';
    }
    if (!empty($rowemp['town'])) {
        $rowtwn = $conn->query("SELECT name FROM towns WHERE id = '" . (int) $rowemp['town'] . "'")->fetch_assoc();
        $townName = $rowtwn['name'] ?? '';
    }
}
?>

<style>
.emprofile-page .content-wrapper { background: #f4f6f9; }

/* Map .text-teal helper to primary color */
.text-teal { color: var(--primary-color) !important; }

.emprofile-page .page-hero {
    background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-color) 55%, var(--primary-light) 100%);
    border-radius: 12px;
    color: #fff;
    padding: 1.25rem 1.5rem;
    margin-bottom: 1.25rem;
    box-shadow: 0 4px 14px rgba(75, 86, 148, 0.25);
}
.emprofile-page .page-hero h1 { font-size: 1.35rem; font-weight: 700; margin: 0; color: #fff; }
.emprofile-page .page-hero .badge-id {
    background: rgba(255,255,255,0.2);
    border: 1px solid rgba(255,255,255,0.35);
    font-weight: 600;
    padding: 0.35rem 0.65rem;
    border-radius: 8px;
}
.emprofile-page .breadcrumb { background: transparent; padding: 0; margin: 0.5rem 0 0; }
.emprofile-page .breadcrumb a { color: rgba(255,255,255,0.9); }
.emprofile-page .breadcrumb-item.active { color: rgba(255,255,255,0.75); }

.emprofile-page .card {
    border: none;
    border-radius: 12px;
    box-shadow: 0 1px 4px rgba(15, 23, 42, 0.06);
    margin-bottom: 1.25rem;
    overflow: hidden;
}
.emprofile-page .card-header {
    background: #fff;
    border-bottom: 1px solid #eef1f5;
    padding: 0.9rem 1.25rem;
}
.emprofile-page .card-header h3,
.emprofile-page .card-header h5 { margin: 0; font-size: 1rem; font-weight: 600; color: #1e293b; }

.emprofile-page .profile-card .profile-banner {
    height: 72px;
    background: linear-gradient(135deg, var(--primary-dark), var(--primary-light));
}
.emprofile-page .profile-avatar-wrap {
    margin-top: -48px;
    padding: 0 1.25rem;
}
.emprofile-page .profile-user-img {
    width: 96px;
    height: 96px;
    object-fit: cover;
    border: 4px solid #fff;
    box-shadow: 0 4px 12px rgba(0,0,0,0.12);
}
.emprofile-page .profile-username {
    font-size: 1.15rem;
    font-weight: 700;
    color: #1e293b;
    margin: 0.75rem 0 0.25rem;
}
.emprofile-page .profile-meta { font-size: 0.85rem; color: #64748b; margin-bottom: 1rem; }

.emprofile-page .stat-chip {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.65rem 0.85rem;
    background: #f8fafc;
    border-radius: 8px;
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
}
.emprofile-page .stat-chip b { color: #64748b; font-weight: 500; }
.emprofile-page .stat-chip span { color: #0f172a; font-weight: 600; }
.emprofile-page .kbi-score-wrap {
    text-align: center;
    padding: 0.75rem;
    background: linear-gradient(180deg, var(--neutral-100), var(--neutral-200));
    border-radius: 10px;
    margin: 0.5rem 0 1rem;
}
.emprofile-page .kbi-score-wrap label { font-size: 0.75rem; color: #64748b; margin: 0; display: block; }
.emprofile-page #totalSum {
    border: none;
    background: transparent;
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--primary-color);
    width: 100%;
    text-align: center;
    padding: 0;
}

.emprofile-page .about-item {
    display: flex;
    gap: 0.75rem;
    padding: 0.65rem 0;
    border-bottom: 1px solid #f1f5f9;
}
.emprofile-page .about-item:last-child { border-bottom: none; }
.emprofile-page .about-item i {
    width: 32px;
    height: 32px;
    line-height: 32px;
    text-align: center;
    background: var(--neutral-100);
    color: var(--primary-color);
    border-radius: 8px;
    flex-shrink: 0;
}
.emprofile-page .about-item strong { display: block; font-size: 0.8rem; color: #64748b; font-weight: 600; }
.emprofile-page .about-item p { margin: 0.15rem 0 0; color: #334155; font-size: 0.9rem; }

.emprofile-page .nav-pills { gap: 0.35rem; flex-wrap: wrap; }
.emprofile-page .nav-pills .nav-link {
    color: #64748b;
    border-radius: 8px;
    padding: 0.5rem 1rem;
    font-weight: 600;
    font-size: 0.875rem;
    border: 1px solid transparent;
}
.emprofile-page .nav-pills .nav-link:hover { background: #f1f5f9; color: var(--primary-dark); }
.emprofile-page .nav-pills .nav-link.active {
    background: var(--primary-color);
    color: #fff;
    box-shadow: 0 2px 8px rgba(75, 86, 148, 0.35);
}

.emprofile-page .detail-grid { padding: 0.25rem 0; }
.emprofile-page .detail-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 1rem;
    padding: 0.85rem 1.25rem;
    border-bottom: 1px solid #f1f5f9;
    font-size: 0.9rem;
}
.emprofile-page .detail-row:last-child { border-bottom: none; }
.emprofile-page .detail-row b {
    color: #64748b;
    font-weight: 600;
    min-width: 140px;
    flex-shrink: 0;
}
.emprofile-page .detail-row span { color: #1e293b; text-align: left; word-break: break-word; }

.emprofile-page .table thead th {
    background: #f8fafc;
    border-bottom: 2px solid #e2e8f0;
    color: #475569;
    font-weight: 600;
    font-size: 0.85rem;
}
.emprofile-page .table td,
.emprofile-page .table th { vertical-align: middle; }
.emprofile-page .kbi-name { margin: 0; font-weight: 600; color: #1e293b; font-size: 0.9rem; }
.emprofile-page .kbi-form .form-control { border-radius: 8px; max-width: 120px; }
.emprofile-page .btn-edit-emp {
    background: #f59e0b;
    border: none;
    color: #1e293b;
    font-weight: 600;
    border-radius: 8px;
    padding: 0.6rem 1rem;
}
.emprofile-page .btn-edit-emp:hover { background: #d97706; color: #fff; }

.emprofile-page .alert-invalid {
    border-radius: 12px;
    padding: 2rem;
    text-align: center;
}
</style>

<div class="content-wrapper emprofile-page">
    <section class="content-header">
        <div class="container-fluid">
            <?php if (!$empValid): ?>
            <div class="alert alert-danger alert-invalid shadow-sm">
                <i class="fas fa-exclamation-triangle fa-2x mb-3 d-block"></i>
                <h4 class="mb-2">غير مسموح بالوصول</h4>
                <p class="mb-3 text-muted">تم الدخول من رابط غير صالح. الرجاء العودة من القائمة الرسمية.</p>
                <a href="dashboard.php" class="btn btn-success btn-lg"><i class="fas fa-home ml-1"></i> الرئيسية</a>
            </div>
            <?php else: ?>
            <div class="page-hero">
                <div class="d-flex flex-wrap justify-content-between align-items-start">
                    <div>
                        <h1><i class="fas fa-user-tie ml-2"></i><?= htmlspecialchars($rowemp['name']) ?></h1>
                        <span class="badge badge-id mt-2">#<?= (int) $rowemp['id'] ?></span>
                    </div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb float-sm-right mb-0">
                            <li class="breadcrumb-item"><a href="dashboard.php"><?= $lang_main ?></a></li>
                            <li class="breadcrumb-item"><a href="employees.php"><?= $lang_employeeslist ?></a></li>
                            <li class="breadcrumb-item active">الملف الشخصي</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <?php if ($empValid): ?>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-4 col-xl-3">
                    <div class="card profile-card">
                        <div class="profile-banner"></div>
                        <div class="card-body text-center pb-0">
                            <div class="profile-avatar-wrap">
                                <img onerror="this.src='assets/alt/altemprofile.png';"
                                     class="profile-user-img img-fluid img-circle"
                                     src="assets/<?= htmlspecialchars($rowemp['imgs']) ?>"
                                     alt="<?= htmlspecialchars($rowemp['name']) ?>">
                            </div>
                            <h3 class="profile-username"><?= htmlspecialchars($rowemp['name']) ?></h3>
                            <?php if (!empty($rowemp['info'])): ?>
                            <p class="profile-meta"><?= htmlspecialchars($rowemp['info']) ?></p>
                            <?php endif; ?>

                            <div class="text-right px-1">
                                <div class="stat-chip">
                                    <b><i class="fas fa-briefcase ml-1 text-teal"></i> الوظيفة</b>
                                    <span><?= htmlspecialchars($jopName ?: '—') ?></span>
                                </div>
                                <div class="stat-chip">
                                    <b><i class="fas fa-building ml-1 text-teal"></i> الإدارة</b>
                                    <span><?= htmlspecialchars($dprtName ?: '—') ?></span>
                                </div>
                                <div class="stat-chip">
                                    <b><i class="fas fa-money-bill-wave ml-1 text-teal"></i> المرتب</b>
                                    <span><?= number_format((float) $rowemp['salary']) ?></span>
                                </div>
                            </div>

                            <div class="kbi-score-wrap">
                                <label>التقييم العام (KBI)</label>
                                <input type="text" id="totalSum" readonly aria-label="التقييم العام">
                            </div>

                            <a href="edit_employee.php?id=<?= $id ?>" class="btn btn-edit-emp btn-block mb-3">
                                <i class="fas fa-edit ml-1"></i> تعديل البيانات
                            </a>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3><i class="fas fa-info-circle text-teal ml-1"></i> نبذة عني</h3>
                        </div>
                        <div class="card-body pt-2">
                            <div class="about-item">
                                <i class="fas fa-graduation-cap"></i>
                                <div>
                                    <strong>التعليم</strong>
                                    <p><?= htmlspecialchars($rowemp['education'] ?: '—') ?></p>
                                </div>
                            </div>
                            <div class="about-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <div>
                                    <strong>الموقع</strong>
                                    <p><?= htmlspecialchars(trim(($townName ? $townName . '، ' : '') . ($rowemp['address'] ?? ''), '، ') ?: '—') ?></p>
                                </div>
                            </div>
                            <div class="about-item">
                                <i class="fas fa-tools"></i>
                                <div>
                                    <strong>المهارات</strong>
                                    <p><?= htmlspecialchars($rowemp['skills'] ?: '—') ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8 col-xl-9">
                    <div class="card">
                        <div class="card-header">
                            <ul class="nav nav-pills card-header-pills">
                                <li class="nav-item">
                                    <a class="nav-link active" href="#activity" data-toggle="tab">
                                        <i class="fas fa-id-card ml-1"></i> <?= $lang_emprofilemainentry ?>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#emprofilejop" data-toggle="tab">
                                        <i class="fas fa-briefcase ml-1"></i> <?= $lang_emprofilejopentry ?>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#timeline" data-toggle="tab">
                                        <i class="fas fa-chart-line ml-1"></i> التقييم (KBI)
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body p-0">
                            <div class="tab-content">
                                <div class="active tab-pane" id="activity">
                                    <div class="card-header border-0 bg-light">
                                        <h5 class="mb-0"><?= $lang_addemployee_personalinfo ?></h5>
                                    </div>
                                    <div class="detail-grid">
                                        <div class="detail-row"><b><?= $lang_publicname ?></b><span><?= htmlspecialchars($rowemp['name']) ?></span></div>
                                        <div class="detail-row"><b><?= $lang_addemployee_email ?></b><span><?= htmlspecialchars($rowemp['email'] ?: '—') ?></span></div>
                                        <div class="detail-row"><b><?= $lang_addemployee_phone ?></b><span><?= htmlspecialchars($rowemp['number'] ?: '—') ?></span></div>
                                        <div class="detail-row"><b><?= $lang_addemployee_dateofbirth ?></b><span><?= htmlspecialchars($rowemp['dateofbirth'] ?: '—') ?></span></div>
                                        <div class="detail-row"><b><?= $lang_addemployee_gender ?></b><span><?= $rowemp['gender'] == 0 ? 'ذكر' : 'أنثى' ?></span></div>
                                        <div class="detail-row"><b><?= $lang_addemployee_info ?></b><span><?= htmlspecialchars($rowemp['info'] ?: '—') ?></span></div>
                                        <div class="detail-row"><b><?= $lang_addemployee_address1 ?></b><span><?= htmlspecialchars($rowemp['address'] ?: '—') ?></span></div>
                                        <div class="detail-row"><b><?= $lang_addemployee_address2 ?></b><span><?= htmlspecialchars($rowemp['address2'] ?: '—') ?></span></div>
                                        <div class="detail-row"><b><?= $lang_addemployee_country ?></b><span><?= htmlspecialchars($townName ?: '—') ?></span></div>
                                    </div>
                                </div>

                                <div class="tab-pane" id="emprofilejop">
                                    <div class="card-header border-0 bg-light">
                                        <h5 class="mb-0"><?= $lang_emprofilejop ?></h5>
                                    </div>
                                    <div class="detail-grid">
                                        <?php
                                        $tybName = 'N/A';
                                        if (!empty($rowemp['joptybe'])) {
                                            $rowtyb = $conn->query("SELECT name FROM joptybes WHERE id = '" . (int) $rowemp['joptybe'] . "'")->fetch_assoc();
                                            $tybName = $rowtyb['name'] ?? 'N/A';
                                        }
                                        $shftName = 'N/A';
                                        if (!empty($rowemp['shift'])) {
                                            $rowshft = $conn->query("SELECT name FROM shifts WHERE id = '" . (int) $rowemp['shift'] . "'")->fetch_assoc();
                                            $shftName = $rowshft['name'] ?? 'N/A';
                                        }
                                        ?>
                                        <div class="detail-row"><b><?= $lang_addemployee_job ?></b><span><?= htmlspecialchars($jopName ?: 'N/A') ?></span></div>
                                        <div class="detail-row"><b><?= $lang_addemployee_jobdepart ?></b><span><?= htmlspecialchars($dprtName ?: 'N/A') ?></span></div>
                                        <div class="detail-row"><b><?= $lang_addemployee_jobtype ?></b><span><?= htmlspecialchars($tybName) ?></span></div>
                                        <div class="detail-row"><b><?= $lang_addemployee_jobstart ?></b><span><?= htmlspecialchars($rowemp['dateofhire'] ?: '—') ?></span></div>
                                        <div class="detail-row"><b><?= $lang_addemployee_jobend ?></b><span><?= htmlspecialchars($rowemp['dateofend'] ?: '—') ?></span></div>
                                        <div class="detail-row"><b><?= $lang_addemployee_salary ?></b><span><?= number_format((float) $rowemp['salary']) ?></span></div>
                                        <div class="detail-row"><b><?= $lang_addemployee_shift ?></b><span><?= htmlspecialchars($shftName) ?></span></div>
                                    </div>
                                </div>

                                <div class="tab-pane" id="timeline">
                                    <div class="card-body kbi-form">
                                        <form id="kbiForm" action="" method="post">
                                            <div class="table-responsive">
                                                <table id="mytable" class="table table-hover mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th>المؤشر</th>
                                                            <th>الوزن</th>
                                                            <th>التقييم %</th>
                                                            <th>القيمة</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $resemkbi = $conn->query("SELECT * FROM `emp_kbis` WHERE emp_id = '$id'");
                                                        while ($rowemkbi = $resemkbi->fetch_assoc()):
                                                            $rowkname = null;
                                                            if (!empty($rowemkbi['kbi_id'])) {
                                                                $rowkname = $conn->query("SELECT * FROM kbis WHERE id = '" . (int) $rowemkbi['kbi_id'] . "'")->fetch_assoc();
                                                            }
                                                        ?>
                                                        <tr>
                                                            <td>
                                                                <input type="hidden" name="kbi_id[]" value="<?= (int) $rowemkbi['id'] ?>">
                                                                <p class="kbi-name" title="<?= htmlspecialchars($rowkname['info'] ?? '') ?>">
                                                                    <?= htmlspecialchars($rowkname['kname'] ?? 'N/A') ?>
                                                                </p>
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control form-control-sm decimalInput kbi-weight"
                                                                       pattern="^\d+(\.\d{0,2})?$" name="kbi_weight[]" required
                                                                       value="<?= htmlspecialchars($rowemkbi['kbi_weight']) ?>">
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control form-control-sm decimalInput kbi-rate"
                                                                       pattern="^\d+(\.\d{0,2})?$" name="kbi_rate[]" required
                                                                       value="<?= htmlspecialchars($rowemkbi['kbi_rate']) ?>">
                                                            </td>
                                                            <td>
                                                                <input readonly type="text" class="form-control form-control-sm kbi-sum"
                                                                       name="kbi_sum[]" required
                                                                       value="<?= htmlspecialchars($rowemkbi['kbi_sum']) ?>">
                                                            </td>
                                                        </tr>
                                                        <?php endwhile; ?>
                                                    </tbody>
                                                    <tfoot class="bg-light">
                                                        <tr>
                                                            <th>الإجمالي</th>
                                                            <th><span id="total_weight" class="text-teal font-weight-bold">0</span></th>
                                                            <th></th>
                                                            <th></th>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                            <div class="p-3 border-top">
                                                <button type="submit" class="btn btn-success">
                                                    <i class="fas fa-save ml-1"></i> حفظ التعديلات
                                                </button>
                                            </div>
                                        </form>
                                        <div id="successMessage" style="display:none;" class="alert alert-success mx-3"></div>
                                        <div id="errorMessage" style="display:none;" class="alert alert-danger mx-3"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>
</div>

<?php if ($empValid): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    function updateRowSum(row) {
        const w = parseFloat(row.querySelector('.kbi-weight')?.value) || 0;
        const r = parseFloat(row.querySelector('.kbi-rate')?.value) || 0;
        const sumEl = row.querySelector('.kbi-sum');
        if (sumEl) sumEl.value = (w * (r / 100)).toFixed(2);
    }

    function updateTotalSum() {
        let total = 0;
        document.querySelectorAll('.kbi-sum').forEach(function(input) {
            total += parseFloat(input.value) || 0;
        });
        const el = document.getElementById('totalSum');
        if (el) el.value = total.toFixed(2);
    }

    function updateTotalWeight() {
        let sum = 0;
        document.querySelectorAll('[name="kbi_weight[]"]').forEach(function(input) {
            sum += parseFloat(input.value) || 0;
        });
        const el = document.getElementById('total_weight');
        if (el) el.textContent = sum.toFixed(2);
    }

    document.querySelectorAll('.decimalInput').forEach(function(input) {
        input.addEventListener('input', function() {
            const row = this.closest('tr');
            if (row) updateRowSum(row);
            updateTotalSum();
            updateTotalWeight();
        });
    });

    document.querySelectorAll('#mytable tbody tr').forEach(updateRowSum);
    updateTotalSum();
    updateTotalWeight();

    document.getElementById('kbiForm').addEventListener('submit', function(e) {
        e.preventDefault();
        fetch('js/ajax/update_kbi.php', {
            method: 'POST',
            body: new URLSearchParams(new FormData(this))
        })
        .then(function(response) { return response.text(); })
        .then(function() {
            var ok = document.getElementById('successMessage');
            ok.textContent = 'تم التعديل بنجاح';
            ok.style.display = 'block';
            setTimeout(function() { ok.style.display = 'none'; }, 2000);
            updateTotalSum();
        })
        .catch(function() {
            var err = document.getElementById('errorMessage');
            err.textContent = 'تأكد من البيانات والاتصال';
            err.style.display = 'block';
            setTimeout(function() { err.style.display = 'none'; }, 2000);
        });
    });
});
</script>
<?php endif; ?>

<?php include('includes/footer.php') ?>
