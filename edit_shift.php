<?php include('includes/header.php') ?>
<?php include('includes/navbar.php') ?>
<?php include('includes/sidebar.php') ?>
<?php include('includes/connect.php');
require_once('includes/shift_attendance.php');
ensure_shift_single_fp_schema($conn);
ensure_shift_grace_rules_schema($conn);

$id = $_GET['id'];
$sqlshift = "SELECT * FROM shifts WHERE id = '$id'";
$resshift = $conn->query($sqlshift);
$rowshift = $resshift->fetch_assoc();

?>

<style>
.content-wrapper {
    background: #f8f9fa;
}

.card {
    border: none;
    box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    border-radius: 8px;
    margin-bottom: 20px;
}

.card-header {
    background: #fff;
    border-bottom: 1px solid #e9ecef;
    padding: 1rem 1.25rem;
}

.card-header h3 {
    margin: 0;
    font-size: 1rem;
    font-weight: 600;
    color: #2c3e50;
}

.form-control {
    border-radius: 6px;
    border: 1px solid #dee2e6;
}

.form-control:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
}

.btn {
    border-radius: 6px;
    font-weight: 500;
}

label {
    font-weight: 500;
    color: #495057;
    margin-bottom: 0.5rem;
}

.form-check-label {
    font-weight: 400;
}

hr {
    border-top: 1px solid #e9ecef;
    margin: 1.5rem 0;
}
</style>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3><?= $lang_infoshift ?></h3>
                </div>
                <form role="form" action="do/doedit_shift.php?id=<?= $id ?>" method="POST">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name"><?= $lang_addhicont_name ?></label>
                                    <input name="name" value="<?= $rowshift['name'] ?>" id="name" type="text" class="form-control" placeholder="اسم الورديه" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?= $lang_workday ?></label>
                                    <?php
                                    $workingdayssql = "SELECT workingdays FROM shifts WHERE id = '$id'";
                                    $wdresult = $conn->query($workingdayssql);
                                    $workingdaysrow = $wdresult->fetch_row();
                                    $workingdays = explode(',', $workingdaysrow[0]);
                                    ?>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-check form-switch">
                                                <input name="sat" class="form-check-input" type="checkbox" role="switch" id="sat" <?php if (in_array(6, $workingdays)) echo "checked" ?>>
                                                <label class="form-check-label" for="sat"><?= $lang_addsh_sat ?></label>
                                            </div>
                                            <div class="form-check form-switch">
                                                <input name="sun" class="form-check-input" type="checkbox" role="switch" id="sun" <?php if (in_array(7, $workingdays)) echo "checked" ?>>
                                                <label class="form-check-label" for="sun"><?= $lang_addsh_sun ?></label>
                                            </div>
                                            <div class="form-check form-switch">
                                                <input name="mon" class="form-check-input" type="checkbox" role="switch" id="mon" <?php if (in_array(1, $workingdays)) echo "checked" ?>>
                                                <label class="form-check-label" for="mon"><?= $lang_addsh_mon ?></label>
                                            </div>
                                            <div class="form-check form-switch">
                                                <input name="tus" class="form-check-input" type="checkbox" role="switch" id="tus" <?php if (in_array(2, $workingdays)) echo "checked" ?>>
                                                <label class="form-check-label" for="tus"><?= $lang_addsh_tue ?></label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-check form-switch">
                                                <input name="wed" class="form-check-input" type="checkbox" role="switch" id="wed" <?php if (in_array(3, $workingdays)) echo "checked" ?>>
                                                <label class="form-check-label" for="wed"><?= $lang_addsh_wed ?></label>
                                            </div>
                                            <div class="form-check form-switch">
                                                <input name="thur" class="form-check-input" type="checkbox" role="switch" id="thu" <?php if (in_array(4, $workingdays)) echo "checked" ?>>
                                                <label class="form-check-label" for="thu"><?= $lang_addsh_thu ?></label>
                                            </div>
                                            <div class="form-check form-switch">
                                                <input name="fri" class="form-check-input" type="checkbox" role="switch" id="fri" <?php if (in_array(5, $workingdays)) echo "checked" ?>>
                                                <label class="form-check-label" for="fri"><?= $lang_addsh_fri ?></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <h5 class="mb-3"><?= $lang_Attendance_rules ?></h5>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?= $lang_addsh_start ?></label>
                                    <input value="<?= $rowshift['shiftstart'] ?>" name="shiftstart" type="time" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?= $lang_addsh_end ?></label>
                                    <input value="<?= $rowshift['shiftend'] ?>" name="shiftend" type="time" class="form-control" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?= $lang_addsh_stardatt ?></label>
                                    <input value="<?= $rowshift['instart'] ?>" name="instart" type="time" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?= $lang_addsh_endatt ?></label>
                                    <input value="<?= $rowshift['inend'] ?>" name="inend" type="time" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?= $lang_addsh_startout ?></label>
                                    <input value="<?= $rowshift['outstart'] ?>" name="outstart" type="time" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?= $lang_addsh_endout ?></label>
                                    <input value="<?= $rowshift['outend'] ?>" name="outend" type="time" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>بصمة واحدة فقط (حضور أو انصراف بدون الأخرى)</label>
                                    <?php $singleFpRule = normalize_single_fp_rule($rowshift['single_fp_rule'] ?? 'half'); ?>
                                    <select name="single_fp_rule" class="form-control">
                                        <option value="none" <?= $singleFpRule === 'none' ? 'selected' : '' ?>>لا تعليق</option>
                                        <option value="half" <?= $singleFpRule === 'half' ? 'selected' : '' ?>>احتساب نصف يوم</option>
                                        <option value="cancel" <?= $singleFpRule === 'cancel' ? 'selected' : '' ?>>إلغاء اليوم (لا يُحسب حضور)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <?php
                        $grace1Action = normalize_grace_action($rowshift['grace1_action'] ?? 'actual', 'actual');
                        $grace2Action = normalize_grace_action($rowshift['grace2_action'] ?? 'half', 'half');
                        ?>

                        <!-- فترة السماح الأولى -->
                        <div class="card p-3 mb-3" style="background: #f8f9fa; border: 1px solid #e2e8f0;">
                            <h6 class="font-weight-bold text-primary mb-2">
                                <i class="fas fa-stopwatch mr-1"></i> فترة السماح الأولى (1)
                            </h6>
                            <p class="text-muted small mb-2">إذا حضر الموظف أو انصرف ضمن هذه الدقائق لا يُخصم منه شيء. وإذا تجاوزها يُطبق الإجراء المحدد.</p>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group mb-2 mb-md-0">
                                        <label><?= $lang_addsh_delaylimits ?></label>
                                        <input value="<?= htmlspecialchars((string)($rowshift['latelimit'] ?? 0)) ?>" name="latelimit" type="number" min="0" class="form-control" placeholder="0">
                                        <small class="text-muted">دقائق سماح التأخير (حضور)</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-2 mb-md-0">
                                        <label><?= $lang_addsh_earlylimits ?></label>
                                        <input value="<?= htmlspecialchars((string)($rowshift['earlylimit'] ?? 0)) ?>" name="earlylimit" type="number" min="0" class="form-control" placeholder="0">
                                        <small class="text-muted">دقائق سماح التبكير (انصراف)</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-0">
                                        <label>الإجراء بعد تجاوز فترة سماح 1</label>
                                        <select name="grace1_action" class="form-control">
                                            <option value="actual" <?= $grace1Action === 'actual' ? 'selected' : '' ?>>خصم وقت عادي</option>
                                            <option value="half" <?= $grace1Action === 'half' ? 'selected' : '' ?>>خصم نصف يوم</option>
                                            <option value="full" <?= $grace1Action === 'full' ? 'selected' : '' ?>>خصم يوم كامل</option>
                                        </select>
                                        <small class="text-muted">الإجراء المطبق لتجاوز سماح 1</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- فترة السماح الثانية -->
                        <div class="card p-3 mb-3" style="background: #fffbf0; border: 1px solid #fed7aa;">
                            <h6 class="font-weight-bold text-warning mb-2" style="color: #b45309 !important;">
                                <i class="fas fa-history mr-1"></i> فترة السماح الثانية (2)
                            </h6>
                            <p class="text-muted small mb-2">حد زمني ثانٍ: إذا تجاوز الموظف هذا الحد يُطبق إجراء الخصم الثاني (اتركه 0 إذا لم ترغب بفترة ثانية).</p>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group mb-2 mb-md-0">
                                        <label>حد تأخير الحضور 2 (بالدقائق)</label>
                                        <input value="<?= htmlspecialchars((string)($rowshift['latelimit_2'] ?? 0)) ?>" name="latelimit_2" type="number" min="0" class="form-control" placeholder="0">
                                        <small class="text-muted">الحد الأقصى للتأخير الثاني</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-2 mb-md-0">
                                        <label>حد تبكير الانصراف 2 (بالدقائق)</label>
                                        <input value="<?= htmlspecialchars((string)($rowshift['earlylimit_2'] ?? 0)) ?>" name="earlylimit_2" type="number" min="0" class="form-control" placeholder="0">
                                        <small class="text-muted">الحد الأقصى للانصراف الثاني</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-0">
                                        <label>الإجراء بعد تجاوز فترة سماح 2</label>
                                        <select name="grace2_action" class="form-control">
                                            <option value="half" <?= $grace2Action === 'half' ? 'selected' : '' ?>>خصم نصف يوم</option>
                                            <option value="full" <?= $grace2Action === 'full' ? 'selected' : '' ?>>خصم يوم كامل</option>
                                            <option value="actual" <?= $grace2Action === 'actual' ? 'selected' : '' ?>>خصم وقت عادي</option>
                                        </select>
                                        <small class="text-muted">الإجراء المطبق لتجاوز سماح 2</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-6">
                                <div class="custom-control custom-switch mt-2">
                                    <input name="ignore_early_in" class="custom-control-input" type="checkbox" id="ignore_early_in" value="1" <?= (!empty($rowshift['ignore_early_in']) ? 'checked' : '') ?>>
                                    <label class="custom-control-label font-weight-bold" for="ignore_early_in">تجاهل التبكير (عدم احتساب الدقائق قبل بداية الوردية)</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="custom-control custom-switch mt-2">
                                    <input name="ignore_late_out" class="custom-control-input" type="checkbox" id="ignore_late_out" value="1" <?= (!empty($rowshift['ignore_late_out']) ? 'checked' : '') ?>>
                                    <label class="custom-control-label font-weight-bold" for="ignore_late_out">تجاهل التأخير في الانصراف (عدم احتساب الدقائق بعد نهاية الوردية)</label>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-success btn-lg btn-block">
                                <i class="fas fa-save mr-2"></i><?= $lang_addhicont_confirm ?>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>



<?php include('includes/footer.php') ?>