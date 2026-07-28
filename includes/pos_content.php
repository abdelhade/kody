<?php
if (!isset($action_url)) {
    $action_url = "do/doadd_invoice.php";
}
?>
<style>
/* إصلاح مشكلة التظليل في صفحة POS */
.modal-backdrop {
    display: none !important;
}
.modal {
    background-color: transparent !important;
}
body {
    background-color: #f5f7fa !important;
}
.content-wrapper {
    background-color: #f5f7fa !important;
}

/* Tables Modal - scrollable body with proper sizing */
#tablesModal .modal-dialog {
    width: 90% !important;
    max-width: 900px !important;
    margin: 1.75rem auto !important;
}
#tablesModal .modal-body {
    max-height: 70vh !important;
    overflow-y: auto !important;
}
</style>
<!-- Main Content -->
<form action="<?= $action_url ?>" method="post" id="posForm">
        <div class="container-fluid h-100" style="height: calc(100vh - 60px);">
            <div class="row h-100 g-1">
                <!-- القسم الأيمن - معلومات الطلب -->
                <div class="col-lg-4">
                    <div class="card shadow-sm h-100 d-flex flex-column">
                        <div
                            class="card-header bg-primary text-white py-2 d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">
                            معلومات الطلب
                            </h6>
                            <button type="button" id="recentOrdersBtn2" class="btn btn-light btn-sm recent-orders-btn">
                                 عرض الطلبات السابقة
                            </button>
                        </div>
<div class="card-body flex-grow-1 overflow-auto d-flex flex-column">
                            <!-- Hidden Fields -->
                            <input type="hidden" name="pro_tybe" value="9">
                            <input type="hidden" name="pro_serial" value="0">
                            <input type="hidden" name="pro_id" value="1">

                            <!-- نوع الطلب -->
                            <div class="mb-2">
                                <div class="btn-group w-100" role="group">
                                    <?php
                                    $order_type_val = 1; // Default تيك أواي
                                    if (isset($_GET['edit']) && isset($rowed['info'])) {
                                        $info_text = $rowed['info'];
                                        if (strpos($info_text, 'نوع الطلب: دليفري') !== false) {
                                            $order_type_val = 3;
                                        } elseif (strpos($info_text, 'طاولة:') !== false || strpos($info_text, 'نوع الطلب: طاولة') !== false) {
                                            $order_type_val = 2;
                                        } elseif (strpos($info_text, 'نوع الطلب: تيك أواي') !== false) {
                                            $order_type_val = 1;
                                        }
                                    }
                                    ?>
                                    <input type="radio" class="btn-check" id="age1" name="age" value="1" <?= $order_type_val == 1 ? 'checked' : '' ?>>
                                    <label class="btn btn-outline-primary btn-sm" for="age1">
                                        تيك اواي
                                    </label>

                                    <input type="radio" class="btn-check" id="age2" name="age" value="2"
                                        <?= ($order_type_val == 2 || isset($_GET['table'])) ? 'checked' : '' ?>>
                                    <label class="btn btn-outline-primary btn-sm" for="age2">
                                        طاولة
                                    </label>
