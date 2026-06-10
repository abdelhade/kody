<?php
/**
 * Shared tabbed employee form body.
 * Expects: $empFormMode ('add'|'edit'), $rowemp, $empFormId, $empUnlockBtnId
 */
$isEdit = ($empFormMode ?? 'add') === 'edit';
$e = function ($key, $default = '') use ($rowemp) {
    return htmlspecialchars((string) ($rowemp[$key] ?? $default), ENT_QUOTES, 'UTF-8');
};
$sel = function ($key, $val) use ($rowemp) {
    return (isset($rowemp[$key]) && (string) $rowemp[$key] === (string) $val) ? 'selected' : '';
};
$chkActive = !empty($rowemp['active']) && $rowemp['active'] != '0';
?>
<style>
.emp-form-page .content-wrapper { background: #f4f6f9; }
.emp-form-page .page-hero {
    background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-color) 55%, var(--primary-light) 100%);
    border-radius: 12px; color: #fff; padding: 1.1rem 1.35rem; margin-bottom: 1.25rem;
    box-shadow: 0 4px 14px rgba(75, 86, 148, 0.25);
}
.emp-form-page .page-hero h1 { font-size: 1.25rem; font-weight: 700; margin: 0; color: #fff; }
.emp-form-page .card { border: none; border-radius: 12px; box-shadow: 0 1px 4px rgba(15,23,42,0.06); overflow: hidden; }
.emp-form-page .nav-pills .nav-link {
    color: #64748b; border-radius: 8px; padding: 0.55rem 1rem; font-weight: 600; font-size: 0.875rem;
}
.emp-form-page .nav-pills .nav-link.active {
    background: var(--primary-color); color: #fff; box-shadow: 0 2px 8px rgba(75, 86, 148, 0.35);
}
.emp-form-page .tab-pane { padding: 1.25rem 1.5rem 0.5rem; }
.emp-form-page label { font-weight: 600; color: #475569; font-size: 0.875rem; }
.emp-form-page .form-control, .emp-form-page .custom-select { border-radius: 8px; }
.emp-form-page .form-footer {
    padding: 1rem 1.5rem 1.25rem; border-top: 1px solid #eef1f5; background: #fafbfc;
}
.emp-form-page .btn-unlock { border-radius: 8px; font-weight: 600; }
</style>

<div class="emp-form-page">
    <section class="content-header">
        <div class="container-fluid">
            <div class="page-hero d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h1>
                    <i class="fas fa-<?= $isEdit ? 'user-edit' : 'user-plus' ?> ml-2"></i>
                    <?= $isEdit ? 'تعديل موظف' : ($lang_addemployee_title ?? $lang_add_new) ?>
                    <?php if ($isEdit && !empty($rowemp['name'])): ?>
                    <small class="d-block mt-1 opacity-90" style="font-size:0.85rem;"><?= $e('name') ?></small>
                    <?php endif; ?>
                </h1>
                <div class="d-flex flex-wrap gap-2">
                    <?php if ($isEdit): ?>
                    <a href="emprofile.php?id=<?= (int) ($rowemp['id'] ?? 0) ?>" class="btn btn-light btn-sm">
                        <i class="fas fa-eye ml-1"></i> الملف
                    </a>
                    <?php endif; ?>
                    <a href="employees.php" class="btn btn-outline-light btn-sm">
                        <i class="fas fa-arrow-right ml-1"></i> <?= $lang_employeeslist ?? 'الموظفين' ?>
                    </a>
                    <button type="button" id="<?= htmlspecialchars($empUnlockBtnId, ENT_QUOTES) ?>" class="btn btn-warning btn-sm btn-unlock">
                        <i class="fas fa-pen ml-1"></i> <?= $isEdit ? 'تفعيل التعديل' : 'تفعيل الحقول' ?>
                    </button>
                </div>
            </div>
        </div>
    </section>

    <section class="content pb-4">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header bg-white">
                    <ul class="nav nav-pills card-header-pills flex-wrap" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#tab-personal" role="tab">
                                <i class="fas fa-id-card ml-1"></i> <?= $lang_addemployee_personalinfo ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#tab-address" role="tab">
                                <i class="fas fa-map-marker-alt ml-1"></i> <?= $lang_addemployee_details ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#tab-job" role="tab">
                                <i class="fas fa-briefcase ml-1"></i> <?= $lang_addemployee_jobinfo ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#tab-salary" role="tab">
                                <i class="fas fa-money-check-alt ml-1"></i> <?= $lang_addemployee_salaries ?>
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="tab-content">
                    <!-- Personal -->
                    <div class="tab-pane fade show active" id="tab-personal" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="emp_name"><?= $lang_addemployee_name ?></label>
                                    <input type="text" data-parsley-trigger="keyup" <?= $isEdit ? '' : 'required minlength="6" data-parsley-length="[6, 50]"' ?>
                                           value="<?= $e('name') ?>" class="form-control" id="emp_name" name="name"
                                           placeholder="<?= $lang_pbholder_name ?>" <?= $isEdit ? '' : 'autocomplete="off"' ?>>
                                </div>
                                <div class="form-group">
                                    <label for="emp_phone"><?= $lang_addemployee_phone ?></label>
                                    <input type="text" data-parsley-type="digits" data-parsley-trigger="keyup"
                                           value="<?= $e('number') ?>" class="form-control" name="number" id="emp_phone"
                                           placeholder="<?= $lang_pbholder_phone ?>" autocomplete="off">
                                </div>
                                <div class="form-group">
                                    <label for="emp_dob"><?= $lang_addemployee_dateofbirth ?></label>
                                    <input type="date" data-parsley-trigger="keyup" value="<?= $e('dateofbirth') ?>"
                                           class="form-control" name="dateofbirth" id="emp_dob" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="emp_email"><?= $lang_addemployee_email ?></label>
                                    <input type="email" data-parsley-type="email" data-parsley-trigger="keyup"
                                           value="<?= $e('email') ?>" class="form-control" name="email" id="emp_email"
                                           placeholder="<?= $lang_pbholder_email ?>" autocomplete="off">
                                </div>
                                <div class="form-group">
                                    <label for="emp_img"><?= $lang_addemployee_image ?></label>
                                    <div class="custom-file">
                                        <input name="imgs" type="file" class="custom-file-input" id="emp_img">
                                        <label class="custom-file-label" for="emp_img"><?= $lang_pbholder_file ?></label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label><?= $lang_addemployee_gender ?></label>
                                    <select name="gender" class="custom-select">
                                        <option value="0" <?= $sel('gender', '0') ?>><?= $lang_addemployee_male ?></option>
                                        <option value="1" <?= $sel('gender', '1') ?>><?= $lang_addemployee_female ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="emp_info"><?= $lang_addemployee_info ?></label>
                            <textarea name="info" class="form-control" data-parsley-trigger="keyup" rows="3" id="emp_info"
                                      placeholder="معلومات...."><?= $e('info') ?></textarea>
                        </div>
                        <div class="form-group mb-0">
                            <div class="custom-control custom-checkbox">
                                <input name="active" class="custom-control-input" type="checkbox" id="emp_active"
                                       value="1" <?= $chkActive ? 'checked' : '' ?>>
                                <label class="custom-control-label" for="emp_active"><?= $lang_addemployee_active ?></label>
                            </div>
                        </div>
                    </div>

                    <!-- Address -->
                    <div class="tab-pane fade" id="tab-address" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="emp_address"><?= $lang_addemployee_address1 ?></label>
                                    <input type="text" data-parsley-trigger="keyup" value="<?= $e('address') ?>"
                                           class="form-control" id="emp_address" name="address"
                                           placeholder="<?= $lang_pbholder_address ?>" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="emp_address2"><?= $lang_addemployee_address2 ?></label>
                                    <input type="text" data-parsley-trigger="keyup" value="<?= $e('address2') ?>"
                                           class="form-control" id="emp_address2" name="address2"
                                           placeholder="<?= $lang_pbholder_address ?>" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-md-0">
                                    <label><?= $lang_addemployee_country ?></label>
                                    <select name="town" class="custom-select">
                                        <?php
                                        $restwn = $conn->query('SELECT * FROM towns ORDER BY name');
                                        while ($rowtwn = $restwn->fetch_assoc()):
                                        ?>
                                        <option value="<?= (int) $rowtwn['id'] ?>" <?= $sel('town', $rowtwn['id']) ?>>
                                            <?= htmlspecialchars($rowtwn['name']) ?>
                                        </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Job -->
                    <div class="tab-pane fade" id="tab-job" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?= $lang_addemployee_job ?></label>
                                    <select name="jop" class="custom-select">
                                        <?php
                                        $resjop = $conn->query('SELECT * FROM jops ORDER BY name');
                                        while ($rowjop = $resjop->fetch_assoc()):
                                        ?>
                                        <option value="<?= (int) $rowjop['id'] ?>" <?= $sel('jop', $rowjop['id']) ?>>
                                            <?= htmlspecialchars($rowjop['name']) ?>
                                        </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label><?= $lang_addemployee_jobdepart ?></label>
                                    <select name="department" class="custom-select">
                                        <?php
                                        $resdprt = $conn->query('SELECT * FROM departments ORDER BY name');
                                        while ($rowdprt = $resdprt->fetch_assoc()):
                                        ?>
                                        <option value="<?= (int) $rowdprt['id'] ?>" <?= $sel('department', $rowdprt['id']) ?>>
                                            <?= htmlspecialchars($rowdprt['name']) ?>
                                        </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?= $lang_addemployee_joplevel ?></label>
                                    <select name="joplevel" class="custom-select">
                                        <?php
                                        $resjplvl = $conn->query('SELECT * FROM joplevels ORDER BY name');
                                        while ($rowjplvl = $resjplvl->fetch_assoc()):
                                        ?>
                                        <option value="<?= (int) $rowjplvl['id'] ?>" <?= $sel('joplevel', $rowjplvl['id']) ?>>
                                            <?= htmlspecialchars($rowjplvl['name']) ?>
                                        </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label><?= $lang_addemployee_jobtype ?></label>
                                    <select name="joptybe" class="custom-select">
                                        <?php
                                        $restybe = $conn->query('SELECT * FROM joptybes ORDER BY name');
                                        while ($rowtybe = $restybe->fetch_assoc()):
                                        ?>
                                        <option value="<?= (int) $rowtybe['id'] ?>" <?= $sel('joptybe', $rowtybe['id']) ?>>
                                            <?= htmlspecialchars($rowtybe['name']) ?>
                                        </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="emp_hire"><?= $lang_addemployee_jobstart ?></label>
                                    <input type="date" value="<?= $e('dateofhire') ?>" class="form-control"
                                           name="dateofhire" id="emp_hire" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label for="emp_end"><?= $lang_addemployee_jobend ?></label>
                                    <input type="date" value="<?= $e('dateofend') ?>" class="form-control"
                                           name="dateofend" id="emp_end" autocomplete="off">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Salary -->
                    <div class="tab-pane fade" id="tab-salary" role="tabpanel">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="emp_salary"><?= $lang_addemployee_salary ?></label>
                                    <input type="text" data-parsley-trigger="keyup" data-parsley-type="digits"
                                           value="<?= $e('salary', $isEdit ? '0' : '') ?>" class="form-control"
                                           id="emp_salary" name="salary" placeholder="00" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>فترة الراتب</label>
                                    <select name="calc_type" class="custom-select">
                                        <option value="monthly" <?= $sel('calc_type', 'monthly') ?>>شهري</option>
                                        <option value="daily" <?= $sel('calc_type', 'daily') ?>>يومي</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><?= $lang_addemployee_shift ?></label>
                                    <select name="shift" class="custom-select">
                                        <?php
                                        $resshft = $conn->query('SELECT * FROM shifts ORDER BY name');
                                        while ($rowshft = $resshft->fetch_assoc()):
                                        ?>
                                        <option value="<?= (int) $rowshft['id'] ?>" <?= $sel('shift', $rowshft['id']) ?>>
                                            <?= htmlspecialchars($rowshft['name']) ?>
                                        </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>نوع الاستحقاق</label>
                                    <select name="ent_tybe" class="custom-select">
                                        <?php
                                        $resentitle = $conn->query('SELECT * FROM entitles ORDER BY id');
                                        while ($rowentitle = $resentitle->fetch_assoc()):
                                        ?>
                                        <option value="<?= (int) $rowentitle['id'] ?>" <?= $sel('ent_tybe', $rowentitle['id']) ?>
                                                title="<?= htmlspecialchars($rowentitle['info'] ?? '') ?>">
                                            <?= htmlspecialchars($rowentitle['tybe']) ?>
                                        </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="hour_extra">الساعة الإضافية تحسب كـ</label>
                                    <input type="number" step=".01" class="form-control" id="hour_extra" name="hour_extra"
                                           value="<?= $e('hour_extra', '1.50') ?>" autocomplete="off">
                                </div>
                            </div>
                            <?php if ($isEdit): ?>
                            <input type="hidden" name="day_extra" value="<?= $e('day_extra', '1.50') ?>">
                            <?php else: ?>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="day_extra">اليوم الإضافي يحسب كـ</label>
                                    <input type="number" step=".01" class="form-control" id="day_extra" name="day_extra"
                                           value="<?= $e('day_extra', '1.50') ?>" autocomplete="off">
                                </div>
                            </div>
                            <?php endif; ?>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="basma_id"><?= $lang_addemployee_basmaid ?></label>
                                    <input type="text" data-parsley-type="integer" class="form-control" name="<?= $isEdit ? 'basma_id' : 'basmaid' ?>"
                                           id="basma_id" value="<?= $e('basma_id') ?>" placeholder="ادخل" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="basma_name"><?= $lang_addemployee_basmaname ?></label>
                                    <input type="text" class="form-control" name="<?= $isEdit ? 'basma_name' : 'basmaname' ?>"
                                           id="basma_name" value="<?= $e('basma_name') ?>" placeholder="ادخل" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-0">
                                    <label for="emp_password"><?= $lang_addemployee_password ?></label>
                                    <input type="password" class="form-control" name="password" id="emp_password"
                                           value="<?= $isEdit ? $e('password') : '' ?>" placeholder="باسورد الهاتف" autocomplete="off">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-footer">
                    <button type="submit" id="emp_submit" class="btn btn-success btn-lg px-5" disabled>
                        <i class="fas fa-check ml-1"></i>
                        <?= $isEdit ? $lang_publicconfirm : $lang_addemployee_confirm ?>
                    </button>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
$(function() {
    var $form = $('#<?= htmlspecialchars($empFormId, ENT_QUOTES) ?>');
    var $fields = $form.find('input:not([type=submit]):not([type=hidden]), select, textarea');
    var $submit = $('#emp_submit');
    var $unlock = $('#<?= htmlspecialchars($empUnlockBtnId, ENT_QUOTES) ?>');

    $fields.prop('disabled', true);
    $submit.prop('disabled', true);

    $unlock.on('click', function(ev) {
        ev.preventDefault();
        $fields.prop('disabled', false);
        $submit.prop('disabled', false);
        $(this).prop('disabled', true).html('<i class="fas fa-lock-open ml-1"></i> تم التفعيل');
    });

    if (typeof bsCustomFileInput !== 'undefined') {
        bsCustomFileInput.init();
    }
});
</script>
