<?php
$todayEsc = $conn->real_escape_string($today);
$cntEmployees = $conn->query("SELECT COUNT(*) AS c FROM employees WHERE isdeleted != 1 OR isdeleted IS NULL")->fetch_assoc();
$cntTodayFp = $conn->query("SELECT COUNT(*) AS c FROM attandance WHERE fpdate = '$todayEsc'")->fetch_assoc();
$cntAttdocs = $conn->query("SELECT COUNT(*) AS c FROM attdocs WHERE isdeleted != 1 OR isdeleted IS NULL")->fetch_assoc();
$cntDepartments = $conn->query("SELECT COUNT(*) AS c FROM departments")->fetch_assoc();
$cntKbis = $conn->query("SELECT COUNT(*) AS c FROM kbis WHERE isdeleted = 0 OR isdeleted IS NULL")->fetch_assoc();
$cntEmpWithKbi = $conn->query("SELECT COUNT(DISTINCT emp_id) AS c FROM emp_kbis WHERE isdeleted != 1 OR isdeleted IS NULL")->fetch_assoc();
$avgKbiTotal = $conn->query("
    SELECT AVG(t.total_kbi) AS avg_total FROM (
        SELECT SUM(kbi_sum) AS total_kbi FROM emp_kbis
        WHERE isdeleted != 1 OR isdeleted IS NULL
        GROUP BY emp_id
    ) t
")->fetch_assoc();
?>
<div class="dashboard-widgets mt-4">
    <div class="row g-4 mb-2">
        <div class="col-12">
            <h4 class="mb-0"><i class="fas fa-users-cog text-primary mr-2"></i> الموارد البشرية</h4>
            <p class="text-muted small mb-0">ملخص سريع للموظفين والحضور ومؤشرات الأداء KPI</p>
        </div>
    </div>
    <div class="row g-4">
        <!-- إحصائيات HR -->
        <div class="col-xl-4 col-lg-6">
            <div class="modern-card card-sales card-hr-stats">
                <div class="card-header">
                    <div class="header-content">
                        <div class="icon-wrapper">
                            <i class="fas fa-id-badge header-icon"></i>
                        </div>
                        <div class="header-text">
                            <h4>إحصائيات الموارد البشرية</h4>
                            <p class="card-subtitle">أرقام اليوم والإجمالي</p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="sales-stats hr-stats-compact">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-value"><?= (int)($cntEmployees['c'] ?? 0) ?></div>
                                <div class="stat-label">الموظفون النشطون</div>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-fingerprint"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-value"><?= (int)($cntTodayFp['c'] ?? 0) ?></div>
                                <div class="stat-label">بصمات اليوم</div>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-book"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-value"><?= (int)($cntAttdocs['c'] ?? 0) ?></div>
                                <div class="stat-label"><?= $lang_attendance_processings_stat ?? 'معالجات الحضور' ?></div>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-sitemap"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-value"><?= (int)($cntDepartments['c'] ?? 0) ?></div>
                                <div class="stat-label">الأقسام</div>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-value"><?= (int)($cntKbis['c'] ?? 0) ?></div>
                                <div class="stat-label">مؤشرات KPI</div>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-user-check"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-value"><?= (int)($cntEmpWithKbi['c'] ?? 0) ?></div>
                                <div class="stat-label">موظفون بـ KPI</div>
                            </div>
                        </div>
                        <div class="stat-card highlight">
                            <div class="stat-icon">
                                <i class="fas fa-star"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-value"><?= number_format((float)($avgKbiTotal['avg_total'] ?? 0), 1) ?></div>
                                <div class="stat-label">متوسط التقييم</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="employees.php" class="view-all-btn">
                        <span>عرض الموظفين</span>
                        <i class="fas fa-arrow-left"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- آخر الموظفين -->
        <div class="col-xl-4 col-lg-6">
            <div class="modern-card card-accounts">
                <div class="card-header">
                    <div class="header-content">
                        <div class="icon-wrapper">
                            <i class="fas fa-user-plus header-icon"></i>
                        </div>
                        <div class="header-text">
                            <h4>آخر الموظفين</h4>
                            <p class="card-subtitle">أحدث 5 موظفين مضافين</p>
                        </div>
                    </div>
                    <div class="header-badge">
                        <span>5</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-wrapper">
                        <div class="modern-table">
                            <div class="table-header">
                                <div class="table-row header-row">
                                    <div class="table-cell">#</div>
                                    <div class="table-cell">الاسم</div>
                                    <div class="table-cell">القسم</div>
                                </div>
                            </div>
                            <div class="table-body">
                                <?php
                                $resEmp = $conn->query("SELECT id, name, department FROM employees WHERE isdeleted != 1 OR isdeleted IS NULL ORDER BY id DESC LIMIT 5");
                                $x = 0;
                                while ($rowEmp = $resEmp->fetch_assoc()) {
                                    $x++;
                                    $depName = '-';
                                    if (!empty($rowEmp['department'])) {
                                        $depid = (int)$rowEmp['department'];
                                        $rowdep = $conn->query("SELECT name FROM departments WHERE id = $depid")->fetch_assoc();
                                        $depName = $rowdep ? $rowdep['name'] : '-';
                                    }
                                ?>
                                <div class="table-row">
                                    <div class="table-cell">
                                        <span class="serial-number"><?= $x ?></span>
                                    </div>
                                    <div class="table-cell">
                                        <div class="account-info">
                                            <div class="main-text">
                                                <a href="emprofile.php?id=<?= (int)$rowEmp['id'] ?>"><?= htmlspecialchars($rowEmp['name']) ?></a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="table-cell">
                                        <span class="parent-name"><?= htmlspecialchars($depName) ?></span>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="employees.php" class="view-all-btn">
                        <span>عرض جميع الموظفين</span>
                        <i class="fas fa-arrow-left"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- آخر بصمات الحضور -->
        <div class="col-xl-4 col-lg-6">
            <div class="modern-card card-operations">
                <div class="card-header">
                    <div class="header-content">
                        <div class="icon-wrapper">
                            <i class="fas fa-fingerprint header-icon"></i>
                        </div>
                        <div class="header-text">
                            <h4>آخر بصمات الحضور</h4>
                            <p class="card-subtitle">أحدث 5 سجلات</p>
                        </div>
                    </div>
                    <div class="header-badge">
                        <span>5</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-wrapper">
                        <div class="modern-table">
                            <div class="table-header">
                                <div class="table-row header-row">
                                    <div class="table-cell">#</div>
                                    <div class="table-cell">الموظف</div>
                                    <div class="table-cell">التاريخ</div>
                                    <div class="table-cell">الوقت</div>
                                </div>
                            </div>
                            <div class="table-body">
                                <?php
                                $resFp = $conn->query("SELECT employee, fpdate, time FROM attandance ORDER BY id DESC LIMIT 5");
                                $x = 0;
                                while ($rowFp = $resFp->fetch_assoc()) {
                                    $x++;
                                    $empName = '__';
                                    $eid = (int)$rowFp['employee'];
                                    if ($eid > 0) {
                                        $rowE = $conn->query("SELECT name FROM employees WHERE id = $eid")->fetch_assoc();
                                        $empName = $rowE ? $rowE['name'] : '__';
                                    }
                                ?>
                                <div class="table-row">
                                    <div class="table-cell">
                                        <span class="serial-number"><?= $x ?></span>
                                    </div>
                                    <div class="table-cell">
                                        <div class="main-text"><?= htmlspecialchars($empName) ?></div>
                                    </div>
                                    <div class="table-cell">
                                        <div class="date-display"><?= htmlspecialchars($rowFp['fpdate']) ?></div>
                                    </div>
                                    <div class="table-cell">
                                        <span class="operation-badge"><?= htmlspecialchars(substr($rowFp['time'], 0, 8)) ?></span>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="manualattandance.php" class="view-all-btn">
                        <span>سجل الحضور اليدوي</span>
                        <i class="fas fa-arrow-left"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- أعلى الموظفين حسب KPI -->
        <div class="col-xl-4 col-lg-6">
            <div class="modern-card card-sales">
                <div class="card-header">
                    <div class="header-content">
                        <div class="icon-wrapper">
                            <i class="fas fa-trophy header-icon"></i>
                        </div>
                        <div class="header-text">
                            <h4>أعلى الموظفين تقييماً</h4>
                            <p class="card-subtitle">حسب مجموع مؤشرات KPI</p>
                        </div>
                    </div>
                    <div class="header-badge">
                        <span>5</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-wrapper">
                        <div class="modern-table">
                            <div class="table-header">
                                <div class="table-row header-row">
                                    <div class="table-cell">#</div>
                                    <div class="table-cell">الموظف</div>
                                    <div class="table-cell">المجموع</div>
                                </div>
                            </div>
                            <div class="table-body">
                                <?php
                                $resTopKbi = $conn->query("
                                    SELECT e.id, e.name, SUM(ek.kbi_sum) AS total_kbi
                                    FROM employees e
                                    INNER JOIN emp_kbis ek ON e.id = ek.emp_id
                                    WHERE (e.isdeleted != 1 OR e.isdeleted IS NULL)
                                      AND (ek.isdeleted != 1 OR ek.isdeleted IS NULL)
                                    GROUP BY e.id, e.name
                                    ORDER BY total_kbi DESC
                                    LIMIT 5
                                ");
                                $x = 0;
                                if ($resTopKbi) {
                                    while ($rowTop = $resTopKbi->fetch_assoc()) {
                                        $x++;
                                ?>
                                <div class="table-row">
                                    <div class="table-cell">
                                        <span class="serial-number"><?= $x ?></span>
                                    </div>
                                    <div class="table-cell">
                                        <div class="main-text">
                                            <a href="emprofile.php?id=<?= (int)$rowTop['id'] ?>"><?= htmlspecialchars($rowTop['name']) ?></a>
                                        </div>
                                    </div>
                                    <div class="table-cell">
                                        <div class="amount-display positive"><?= number_format((float)$rowTop['total_kbi'], 2) ?></div>
                                    </div>
                                </div>
                                <?php
                                    }
                                }
                                if ($x === 0) {
                                ?>
                                <div class="table-row">
                                    <div class="table-cell text-muted" style="grid-column: 1 / -1;">لا توجد تقييمات KPI مسجّلة بعد</div>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="emp_kbis.php" class="view-all-btn">
                        <span>تقييمات الموظفين</span>
                        <i class="fas fa-arrow-left"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- مؤشرات KPI -->
        <div class="col-xl-8 col-lg-12">
            <div class="modern-card card-items">
                <div class="card-header">
                    <div class="header-content">
                        <div class="icon-wrapper">
                            <i class="fas fa-bullseye header-icon"></i>
                        </div>
                        <div class="header-text">
                            <h4>مؤشرات الأداء KPI</h4>
                            <p class="card-subtitle">متوسط التقييم وعدد الموظفين لكل مؤشر</p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-wrapper">
                        <div class="modern-table">
                            <div class="table-header">
                                <div class="table-row header-row">
                                    <div class="table-cell">#</div>
                                    <div class="table-cell">المؤشر</div>
                                    <div class="table-cell">التصنيف</div>
                                    <div class="table-cell">الموظفون</div>
                                    <div class="table-cell">متوسط %</div>
                                    <div class="table-cell">متوسط القيمة</div>
                                </div>
                            </div>
                            <div class="table-body">
                                <?php
                                $resKbiStats = $conn->query("
                                    SELECT k.id, k.kname, k.ktybe,
                                           COUNT(ek.id) AS emp_cnt,
                                           AVG(ek.kbi_rate) AS avg_rate,
                                           AVG(ek.kbi_sum) AS avg_sum
                                    FROM kbis k
                                    LEFT JOIN emp_kbis ek ON k.id = ek.kbi_id
                                        AND (ek.isdeleted != 1 OR ek.isdeleted IS NULL)
                                    WHERE k.isdeleted = 0 OR k.isdeleted IS NULL
                                    GROUP BY k.id, k.kname, k.ktybe
                                    ORDER BY emp_cnt DESC, k.id ASC
                                    LIMIT 8
                                ");
                                $x = 0;
                                if ($resKbiStats) {
                                    while ($rowKbi = $resKbiStats->fetch_assoc()) {
                                        $x++;
                                ?>
                                <div class="table-row">
                                    <div class="table-cell">
                                        <span class="serial-number"><?= $x ?></span>
                                    </div>
                                    <div class="table-cell">
                                        <div class="account-info">
                                            <div class="main-text"><?= htmlspecialchars($rowKbi['kname']) ?></div>
                                        </div>
                                    </div>
                                    <div class="table-cell">
                                        <span class="operation-badge"><?= htmlspecialchars($rowKbi['ktybe'] ?: '-') ?></span>
                                    </div>
                                    <div class="table-cell">
                                        <span class="parent-name"><?= (int)$rowKbi['emp_cnt'] ?></span>
                                    </div>
                                    <div class="table-cell">
                                        <div class="amount-display <?= (float)$rowKbi['avg_rate'] >= 80 ? 'positive' : 'due' ?>">
                                            <?= $rowKbi['avg_rate'] !== null ? number_format((float)$rowKbi['avg_rate'], 1) . '%' : '-' ?>
                                        </div>
                                    </div>
                                    <div class="table-cell">
                                        <div class="amount-display paid">
                                            <?= $rowKbi['avg_sum'] !== null ? number_format((float)$rowKbi['avg_sum'], 2) : '-' ?>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                    }
                                }
                                if ($x === 0) {
                                ?>
                                <div class="table-row">
                                    <div class="table-cell text-muted" style="grid-column: 1 / -1;">لا توجد مؤشرات KPI معرّفة — <a href="add_kbi.php">إضافة مؤشر</a></div>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="kbis.php" class="view-all-btn">
                        <span>إدارة مؤشرات KPI</span>
                        <i class="fas fa-arrow-left"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- آخر معالجات الحضور -->
        <div class="col-xl-12 col-lg-12">
            <div class="modern-card card-installments">
                <div class="card-header">
                    <div class="header-content">
                        <div class="icon-wrapper">
                            <i class="fas fa-calendar-check header-icon"></i>
                        </div>
                        <div class="header-text">
                            <h4><?= $lang_latest_attendance_processings ?? 'آخر معالجات الحضور' ?></h4>
                            <p class="card-subtitle"><?= $lang_latest_attendance_processings_sub ?? 'أحدث 5 معالجات' ?></p>
                        </div>
                    </div>
                    <div class="header-badge">
                        <span>5</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-wrapper">
                        <div class="modern-table">
                            <div class="table-header">
                                <div class="table-row header-row">
                                    <div class="table-cell">#</div>
                                    <div class="table-cell">الموظف</div>
                                    <div class="table-cell">من</div>
                                    <div class="table-cell">إلى</div>
                                    <div class="table-cell">أيام العمل</div>
                                    <div class="table-cell">الغياب</div>
                                </div>
                            </div>
                            <div class="table-body">
                                <?php
                                $resDoc = $conn->query("SELECT id, empid, fromdate, todate, workdays, absdays FROM attdocs WHERE isdeleted != 1 OR isdeleted IS NULL ORDER BY id DESC LIMIT 5");
                                $x = 0;
                                while ($rowDoc = $resDoc->fetch_assoc()) {
                                    $x++;
                                    $empName = '__';
                                    $eid = (int)$rowDoc['empid'];
                                    if ($eid > 0) {
                                        $rowE = $conn->query("SELECT name FROM employees WHERE id = $eid")->fetch_assoc();
                                        $empName = $rowE ? $rowE['name'] : '__';
                                    }
                                ?>
                                <div class="table-row">
                                    <div class="table-cell">
                                        <span class="serial-number"><?= $x ?></span>
                                    </div>
                                    <div class="table-cell">
                                        <span class="client-name"><?= htmlspecialchars($empName) ?></span>
                                    </div>
                                    <div class="table-cell">
                                        <div class="date-display"><?= htmlspecialchars($rowDoc['fromdate'] ?? '-') ?></div>
                                    </div>
                                    <div class="table-cell">
                                        <div class="date-display"><?= htmlspecialchars($rowDoc['todate']) ?></div>
                                    </div>
                                    <div class="table-cell">
                                        <div class="amount-display positive"><?= number_format((float)$rowDoc['workdays'], 0) ?></div>
                                    </div>
                                    <div class="table-cell">
                                        <div class="amount-display <?= (int)$rowDoc['absdays'] > 0 ? 'negative' : 'paid' ?>"><?= (int)$rowDoc['absdays'] ?></div>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="calcsalary.php" class="view-all-btn">
                        <span><?= $lang_view_all_attendance_processings ?? 'عرض جميع معالجات الحضور' ?></span>
                        <i class="fas fa-arrow-left"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
