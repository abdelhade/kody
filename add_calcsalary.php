<?php include('includes/header.php') ?>
<?php include('includes/navbar.php') ?>
<?php include('includes/sidebar.php') ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">

        <?php if (!empty($_SESSION['calcsalary_flash'])):
            $flash = $_SESSION['calcsalary_flash'];
            unset($_SESSION['calcsalary_flash']);
            $alertType = $flash['type'] ?? 'info';
            if (!in_array($alertType, ['success', 'danger', 'warning', 'info'], true)) {
                $alertType = 'info';
            }
            $icon = $alertType === 'success' ? 'check-circle' : ($alertType === 'danger' ? 'times-circle' : 'info-circle');
        ?>
        <div class="alert alert-<?= $alertType ?> alert-dismissible fade show shadow-sm mb-3" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="إغلاق"><span aria-hidden="true">&times;</span></button>
            <h5 class="alert-heading mb-2">
                <i class="fas fa-<?= $icon ?> ml-1"></i>
                <?= htmlspecialchars($flash['title'] ?? '') ?>
            </h5>
            <?php if (!empty($flash['source'])): ?>
            <p class="mb-2"><strong>مصدر المعالجة:</strong> <?= htmlspecialchars($flash['source']) ?></p>
            <?php endif; ?>
            <?php if (!empty($flash['lines'])): ?>
            <ul class="mb-2 pr-3">
                <?php foreach ($flash['lines'] as $line): ?>
                <li><?= htmlspecialchars($line) ?></li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>
            <?php if (!empty($flash['employees'])): ?>
            <div class="table-responsive">
                <table class="table table-sm table-bordered bg-white mb-2">
                    <thead>
                        <tr>
                            <th>الموظف</th>
                            <th>أيام الحضور</th>
                            <th>الساعات</th>
                            <th>الاستحقاق</th>
                            <th>الصافي</th>
                            <th>المستند</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($flash['employees'] as $emp): ?>
                        <tr>
                            <td><?= htmlspecialchars($emp['name'] ?? '') ?></td>
                            <td><?= (int) ($emp['attdays'] ?? 0) ?></td>
                            <td><?= htmlspecialchars((string) ($emp['hours'] ?? '0')) ?></td>
                            <td><?= number_format((float) ($emp['entitle'] ?? 0), 2) ?></td>
                            <td><?= number_format((float) ($emp['net_pay'] ?? 0), 2) ?></td>
                            <td>#<?= (int) ($emp['docid'] ?? 0) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
            <?php if (!empty($flash['link'])): ?>
            <a href="<?= htmlspecialchars($flash['link']) ?>" class="btn btn-sm btn-outline-<?= $alertType === 'success' ? 'success' : 'primary' ?>">
                <i class="fas fa-list ml-1"></i> عرض في قائمة المعالجات
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h2>معالجة البصمة لموظف واحد</h2>
            </div>
            <div class="card-body">
                <form action="do/doadd_calcsalary.php" method="post">
                <div class="row">
                    <div class="col">
                        
            <div class="form-group">
                <label for="">اسم الموظف</label>
                <select required class="form-control select2" name="employee" id="employee">
                    <?php
                    $resemp = $conn->query("SELECT * FROM `employees` WHERE (`isdeleted` != 1 OR `isdeleted` IS NULL) ORDER BY `name`");
                    while ($rowemp = $resemp->fetch_assoc()) { ?>
                    <option value="<?= $rowemp['id'] ?>"> <?= $rowemp['name'] ?></option>
                   <?php } ?>
                </select>
            </div>
                    </div>
                    <div class="col">
                        
            <div class="form-group">
                <label for="">من</label>
                <input required class="form-control" type="date" name="startdate" id="">
            </div>
                    </div>
                    <div class="col">
                              
            <div class="form-group">
                <label for="">الي</label>
                <input required class="form-control" type="date" name="enddate" id="">
            </div>

                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button class="btn btn-primary" type="submit">معالجة</button>
            </div>
            </form>
        </div>



        <div class="card">
            <div class="card-header">
                <h2>معالجة البصمة حسب الإدارة</h2>
            </div>
            <div class="card-body">
                <form action="do/doadd_calcgroup.php" method="post">
                <div class="row">
                    <div class="col">
                        
            <div class="form-group">
                <label for="">الادارة</label>
                <select required class="form-control select2" name="department" id="department">
                    <?php
                    $resdprt = $conn->query("SELECT * FROM `departments` WHERE (`isdeleted` != 1 OR `isdeleted` IS NULL) ORDER BY `name`");
                    while ($rowdprt = $resdprt->fetch_assoc()) { ?>
                    <option value="<?= $rowdprt['id'] ?>"> <?= $rowdprt['name'] ?></option>
                   <?php } ?>
                </select>
            </div>
                    </div>
                    <div class="col">
                        
            <div class="form-group">
                <label for="">من</label>
                <input required class="form-control" type="date" name="startdate" id="">
            </div>
                    </div>
                    <div class="col">
                              
            <div class="form-group">
                <label for="">الي</label>
                <input required class="form-control" type="date" name="enddate" id="">
            </div>

                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button class="btn btn-primary" type="submit">معالجة</button>
            </div>
            </form>
        </div>



<!-- /.col -->
</div>
<!-- /.row -->
</section>
<!-- /.content -->
</div>

<?php include('includes/footer.php') ?>
<script src="plugins/select2/js/i18n/ar.js"></script>
<script>
$(document).ready(function() {
    $('#employee, #department').select2({
        language: 'ar',
        dir: 'rtl',
        width: '100%'
    });
});
</script>