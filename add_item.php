<?php include('includes/header.php') ?>
<?php
$isEdit = isset($_GET['edit']);
$editId = $isEdit ? (int) $_GET['edit'] : 0;
if ($isEdit && $editId < 1) {
    header('Location: dashboard.php');
    exit;
}
if ($isEdit) {
    $rowitm = $conn->query("SELECT * FROM myitems WHERE id = " . $editId)->fetch_assoc();
    if ($rowitm == null) {
        header('Location: dashboard.php');
        exit;
    }
}

$existingImg = null;
if ($isEdit) {
    $resImg = $conn->query("SELECT iname FROM imgs WHERE itemid = " . $editId . " LIMIT 1");
    if ($resImg && ($rowImg = $resImg->fetch_assoc())) {
        $existingImg = 'uploads/' . $rowImg['iname'];
    }
}

$addItemCssVer = is_file(__DIR__ . '/dist/css/add_item.css')
    ? (string) filemtime(__DIR__ . '/dist/css/add_item.css')
    : '1';
?>
<link rel="stylesheet" href="dist/css/add_item.css?v=<?= htmlspecialchars($addItemCssVer, ENT_QUOTES, 'UTF-8') ?>">
<?php include('includes/navbar.php') ?>
<?php include('includes/sidebar.php') ?>

<div class="content-wrapper add-item-page">
    <section class="content-header py-2">
        <div class="item-page-wrap">
            <div class="row align-items-center">
                <div class="col">
                    <h1 class="m-0 text-dark page-title-compact">
                        <i class="fas fa-<?= $isEdit ? 'pen' : 'plus-circle' ?> text-primary ml-1"></i>
                        <?= $isEdit ? 'تعديل صنف' : 'إضافة صنف' ?>
                        <?php if ($isEdit): ?>
                            <small class="text-muted">— <?= htmlspecialchars($rowitm['iname'], ENT_QUOTES, 'UTF-8') ?></small>
                        <?php endif; ?>
                    </h1>
                </div>
                <div class="col-auto d-none d-lg-block">
                    <ol class="breadcrumb m-0 bg-transparent p-0 breadcrumb-xs">
                        <li class="breadcrumb-item"><a href="dashboard.php">الرئيسية</a></li>
                        <li class="breadcrumb-item"><a href="myitems.php">الأصناف</a></li>
                        <li class="breadcrumb-item active"><?= $isEdit ? 'تعديل' : 'إضافة' ?></li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content pt-0">
        <div class="item-page-wrap">

            <?php if (isset($_GET['saved']) && $_GET['saved'] === '1'): ?>
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="إغلاق">&times;</button>
                    <i class="fas fa-check-circle ml-1"></i>
                    تم الحفظ بنجاح.
                </div>
            <?php endif; ?>
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="إغلاق">&times;</button>
                    <i class="fas fa-exclamation-triangle ml-1"></i>
                    <?php
                    $err = isset($_GET['error']) ? $_GET['error'] : '';
                    if ($err === 'duplicate_barcode') {
                        echo 'الباركود مستخدم مسبقاً، يرجى إدخال باركود فريد.';
                    } elseif ($err === 'duplicate_name') {
                        echo 'يوجد صنف بنفس الاسم، اختر اسماً مختلفاً.';
                    } elseif ($err === 'save_failed') {
                        echo 'تعذّر حفظ البيانات. حاول مرة أخرى.';
                    } elseif ($err === 'invalid_image') {
                        echo 'صيغة الصورة غير مسموحة. استخدم jpg أو png أو gif أو jpeg أو webp.';
                    } elseif ($err === 'no_units') {
                        echo 'لا يمكن حفظ صنف بدون وحدات.';
                    } else {
                        echo 'حدث خطأ أثناء الحفظ.';
                    }
                    ?>
                </div>
            <?php endif; ?>

            <?php if ($role['add_items'] == 1): ?>

                <?php if (!$isEdit): ?>
                    <form action="do/doadd_item.php" method="post" enctype="multipart/form-data" id="item-main-form">
                <?php else: ?>
                    <form action="do/doedit_item.php?edit=<?= $editId ?>" method="post" enctype="multipart/form-data" id="item-main-form">
                <?php endif; ?>

                <?php
                $rowlstitm = $conn->query('SELECT MAX(code) AS max_code FROM myitems')->fetch_assoc();
                $maxCode = $rowlstitm['max_code'] ?? null;
                if ($maxCode === null) {
                    $itmid = 1;
                } elseif ($isEdit) {
                    $itmid = $rowitm['code'];
                } else {
                    $itmid = (int) $maxCode + 1;
                }
                
                // Get the last barcode and increment by 1
                $rowlstbarcode = $conn->query('SELECT MAX(CAST(barcode AS UNSIGNED)) AS max_barcode FROM myitems WHERE barcode REGEXP \'^[0-9]+$\'')->fetch_assoc();
                $maxBarcode = $rowlstbarcode['max_barcode'] ?? null;
                if ($maxBarcode === null) {
                    $newBarcode = 1;
                } elseif ($isEdit) {
                    $newBarcode = $rowitm['barcode'];
                } else {
                    $newBarcode = (int) $maxBarcode + 1;
                }
                ?>

                <div class="card modern-card compact-card">
                    <div class="card-body">
                        <div class="item-fields-inline">
                            <div class="ifld ifld-code">
                                <label>كود</label>
                                <input readonly value="<?= htmlspecialchars((string) $itmid, ENT_QUOTES, 'UTF-8') ?>" class="form-control form-control-sm bg-light text-center" type="text" name="code">
                            </div>
                            <div class="ifld ifld-barcode">
                                <label for="barcode">باركود<span class="text-danger">*</span></label>
                                <input id="barcode" required value="<?= htmlspecialchars((string) $newBarcode, ENT_QUOTES, 'UTF-8') ?>" class="form-control form-control-sm text-center" type="text" name="barcode" data-id="<?= $isEdit ? $editId : 0 ?>">
                                <small class="text-danger d-none" id="barcodeError"></small>
                            </div>
                            <div class="ifld ifld-name">
                                <label for="iname">الاسم<span class="text-danger">*</span></label>
                                <input id="iname" required class="form-control form-control-sm" type="text" name="iname"
                                       value="<?= $isEdit ? htmlspecialchars($rowitm['iname'], ENT_QUOTES, 'UTF-8') : '' ?>"
                                       placeholder="اسم الصنف" data-id="<?= $isEdit ? $editId : 0 ?>" autofocus>
                                <small class="text-danger d-none" id="inameError"></small>
                            </div>
                            <div class="ifld ifld-name2">
                                <label for="name2">ثاني</label>
                                <input id="name2" class="form-control form-control-sm" type="text" name="name2"
                                       value="<?= $isEdit ? htmlspecialchars((string) ($rowitm['name2'] ?? ''), ENT_QUOTES, 'UTF-8') : '' ?>">
                            </div>
                            <div class="ifld ifld-group">
                                <label for="group1">مجموعة</label>
                                <select id="group1" name="group1" class="form-control form-control-sm select2-item">
                                    <option value="">—</option>
                                    <?php
                                    $resgroup1 = $conn->query('SELECT * FROM item_group WHERE isdeleted = 0');
                                    while ($rowgroup1 = $resgroup1->fetch_assoc()) { ?>
                                        <option value="<?= (int) $rowgroup1['id'] ?>" <?= ($isEdit && (int) $rowgroup1['id'] === (int) $rowitm['group1']) ? 'selected' : '' ?>><?= htmlspecialchars($rowgroup1['gname'], ENT_QUOTES, 'UTF-8') ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="ifld ifld-group">
                                <label for="group2">تصنيف</label>
                                <select id="group2" name="group2" class="form-control form-control-sm select2-item">
                                    <option value="">—</option>
                                    <?php
                                    $resgroup2 = $conn->query('SELECT * FROM item_group2 WHERE isdeleted = 0');
                                    while ($rowgroup2 = $resgroup2->fetch_assoc()) { ?>
                                        <option value="<?= (int) $rowgroup2['id'] ?>" <?= ($isEdit && (int) $rowgroup2['id'] === (int) $rowitm['group2']) ? 'selected' : '' ?>><?= htmlspecialchars($rowgroup2['gname'], ENT_QUOTES, 'UTF-8') ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="ifld ifld-info">
                                <label for="info">ملاحظات</label>
                                <input id="info" class="form-control form-control-sm" type="text" name="info"
                                       value="<?= $isEdit ? htmlspecialchars((string) ($rowitm['info'] ?? ''), ENT_QUOTES, 'UTF-8') : '' ?>">
                            </div>
                            <div class="ifld ifld-img" id="itemImagePanel">
                                <div class="item-thumb" id="itemImagePreview">
                                    <?php if ($existingImg): ?>
                                        <img src="<?= htmlspecialchars($existingImg, ENT_QUOTES, 'UTF-8') ?>" alt="" id="itemPreviewImg" onerror="this.style.display='none';">
                                    <?php else: ?>
                                        <span class="thumb-empty" id="itemImagePlaceholder"><i class="fas fa-image"></i></span>
                                        <img src="" alt="" id="itemPreviewImg" class="d-none">
                                    <?php endif; ?>
                                </div>
                                <label for="imgs" class="btn-img-pick" title="اختر صورة"><i class="fas fa-camera"></i></label>
                                <input type="file" name="imgs[]" class="d-none" id="imgs" multiple accept="image/*,.jpg,.jpeg,.png,.gif,.webp">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card modern-card units-card mt-2">
                    <div class="card-header card-header-compact d-flex align-items-center justify-content-between">
                        <span class="card-title-compact"><i class="fas fa-layer-group ml-1"></i> الوحدات والأسعار</span>
                        <button type="button" id="addUnit" class="btn btn-xs btn-primary py-0">
                            <i class="fas fa-plus"></i> وحدة
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="units-table-wrap">
                            <table class="table table-sm table-bordered mb-0 units-table text-center">
                                <colgroup>
                                    <col class="c-unit"><col class="c-val"><col class="c-bc">
                                    <col class="c-price"><col class="c-price"><col class="c-price"><col class="c-price">
                                    <col class="c-del">
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th>وحدة</th>
                                        <th>المعامل</th>
                                        <th>باركود</th>
                                        <th>التكلفة</th>
                                        <th>قطاعي</th>
                                        <th>جملة</th>
                                        <th>السوق</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="unitsContainer">
                                <?php if (!$isEdit) { ?>
                                    <tr class="urow">
                                        <td>
                                            <select name="unit_id[]" class="form-control form-control-sm">
                                                <?php
                                                $resunit = $conn->query('SELECT * FROM myunits');
                                                while ($rowunit = $resunit->fetch_assoc()) { ?>
                                                    <option value="<?= (int) $rowunit['id'] ?>"><?= htmlspecialchars($rowunit['uname'], ENT_QUOTES, 'UTF-8') ?></option>
                                                <?php } ?>
                                            </select>
                                        </td>
                                        <td><input class="form-control form-control-sm text-center" type="number" readonly name="u_val[]" value="1" step="0.001"></td>
                                        <td>
                                            <input class="form-control form-control-sm unit-barcode-input" type="text" name="unit_barcode[]" value="<?= htmlspecialchars((string) $newBarcode, ENT_QUOTES, 'UTF-8') ?>" data-id="<?= $isEdit ? $editId : 0 ?>">
                                            <small class="text-danger d-none unit-barcode-error" style="font-size:0.65rem;">مستخدم</small>
                                        </td>
                                        <td><input type="number" name="cost_price[]" class="form-control form-control-sm" value="0" step="0.001" min="0"></td>
                                        <td><input type="number" name="price1[]" class="form-control form-control-sm" value="0" step="0.001" min="0"></td>
                                        <td><input type="number" name="price2[]" class="form-control form-control-sm" value="0" step="0.001" min="0"></td>
                                        <td><input type="number" name="market_price[]" class="form-control form-control-sm" value="0" step="0.001" min="0"></td>
                                        <td class="text-center align-middle p-0">
                                            <button type="button" class="btn btn-link btn-del-row deleteRow" title="حذف"><i class="fas fa-times text-danger"></i></button>
                                        </td>
                                    </tr>
                                <?php } else {
                                    $resunt = $conn->query("SELECT * FROM item_units WHERE item_id = " . $editId);
                                    while ($rowunt = $resunt->fetch_assoc()) { ?>
                                        <tr class="urow">
                                            <td>
                                                <select name="unit_id[]" class="form-control form-control-sm">
                                                    <?php
                                                    $resunit = $conn->query('SELECT * FROM myunits');
                                                    while ($rowunit = $resunit->fetch_assoc()) { ?>
                                                        <option <?= ((int) $rowunit['id'] === (int) $rowunt['unit_id']) ? 'selected' : '' ?> value="<?= (int) $rowunit['id'] ?>"><?= htmlspecialchars($rowunit['uname'], ENT_QUOTES, 'UTF-8') ?></option>
                                                    <?php } ?>
                                                </select>
                                            </td>
                                            <td><input class="form-control form-control-sm text-center" type="number" name="u_val[]" value="<?= htmlspecialchars((string) $rowunt['u_val'], ENT_QUOTES, 'UTF-8') ?>" step="0.001"></td>
                                            <td>
                                                <input class="form-control form-control-sm unit-barcode-input" type="text" name="unit_barcode[]" value="<?= htmlspecialchars((string) $rowunt['unit_barcode'], ENT_QUOTES, 'UTF-8') ?>" data-id="<?= $isEdit ? $editId : 0 ?>">
                                                <small class="text-danger d-none unit-barcode-error" style="font-size:0.65rem;">مستخدم</small>
                                            </td>
                                            <td><input type="number" name="cost_price[]" class="form-control form-control-sm" value="<?= htmlspecialchars((string) $rowunt['cost_price'], ENT_QUOTES, 'UTF-8') ?>" step="0.001" min="0"></td>
                                            <td><input type="number" name="price1[]" class="form-control form-control-sm" value="<?= htmlspecialchars((string) $rowunt['price1'], ENT_QUOTES, 'UTF-8') ?>" step="0.001" min="0"></td>
                                            <td><input type="number" name="price2[]" class="form-control form-control-sm" value="<?= htmlspecialchars((string) $rowunt['price2'], ENT_QUOTES, 'UTF-8') ?>" step="0.001" min="0"></td>
                                            <td><input type="number" name="market_price[]" class="form-control form-control-sm" value="<?= htmlspecialchars((string) $rowunt['price3'], ENT_QUOTES, 'UTF-8') ?>" step="0.001" min="0"></td>
                                            <td class="text-center align-middle p-0">
                                                <button type="button" class="btn btn-link btn-del-row deleteRow" title="حذف"><i class="fas fa-times text-danger"></i></button>
                                            </td>
                                        </tr>
                                    <?php }
                                } ?>
                                </tbody>
                            </table>
                        </div>
                        <p class="units-tip mb-0" title="السطر الأول = الوحدة الأساسية"><i class="fas fa-info-circle ml-1"></i> الأسعار تُحسب تلقائياً حسب المعامل</p>
                    </div>
                    <div class="item-form-actions is-sticky">
                        <div class="d-flex align-items-center flex-wrap" style="gap:0.3rem;">
                            <a href="myitems.php" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-right"></i> رجوع</a>
                            <?php if (!$isEdit): ?>
                            <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#importItemsModal">
                                <i class="fas fa-file-excel"></i> استيراد
                            </button>
                            <?php endif; ?>
                            <small class="text-muted d-none d-md-inline">F2</small>
                        </div>
                        <button type="submit" class="btn btn-<?= $isEdit ? 'warning' : 'primary' ?> btn-sm btn-save-item">
                            <i class="fas fa-save ml-1"></i> <?= $isEdit ? 'تحديث' : 'حفظ' ?>
                        </button>
                    </div>
                </div>

                </form>

                <?php if (!$isEdit): ?>
                <div class="modal fade" id="importItemsModal" tabindex="-1" role="dialog" aria-labelledby="importItemsModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                        <div class="modal-content">
                            <form action="do/uploaditems.php" method="post" enctype="multipart/form-data" id="import-items-form">
                                <div class="modal-header py-2">
                                    <h5 class="modal-title" id="importItemsModalLabel">
                                        <i class="fas fa-file-excel text-success ml-1"></i> استيراد أصناف
                                    </h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="إغلاق">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body py-3">
                                    <label for="excel-file" class="lbl-sm d-block mb-1">ملف Excel</label>
                                    <div class="custom-file custom-file-sm">
                                        <input type="file" class="custom-file-input" name="file" id="excel-file" required accept=".xlsx,.xls,.csv,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel">
                                        <label class="custom-file-label" for="excel-file" data-browse="استعراض">اختر ملف</label>
                                    </div>
                                    <small class="text-muted d-block mt-2">xlsx, xls, csv</small>
                                </div>
                                <div class="modal-footer py-2">
                                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">إلغاء</button>
                                    <button type="submit" class="btn btn-info btn-sm">
                                        <i class="fas fa-upload ml-1"></i> رفع واستيراد
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

            <?php else: ?>
                <div class="card card-outline card-danger shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-ban fa-3x text-danger mb-3"></i>
                        <h4>ليس لديك صلاحية</h4>
                        <p class="text-muted mb-0">لا يمكنك إضافة أو تعديل الأصناف. راجع مدير النظام.</p>
                        <a href="dashboard.php" class="btn btn-primary mt-3">الرئيسية</a>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </section>
</div>

<script src="js/additem.js"></script>
<?php include('includes/footer.php') ?>
