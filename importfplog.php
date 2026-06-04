<?php include('includes/header.php')?>
<?php include('includes/navbar.php')?>
<?php include('includes/sidebar.php')?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark"><i class="fas fa-fingerprint text-primary mr-2"></i> استيراد ملفات البصمة</h1>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-8 offset-md-2">
                    <div class="card card-outline card-primary shadow-sm">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-upload mr-1"></i> رفع ملف الإكسيل</h3>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info bg-light text-info border-info" style="font-size:0.95rem;">
                                <h5><i class="icon fas fa-info-circle"></i> ملاحظات هامة:</h5>
                                <ul class="mb-0 pr-3">
                                    <li>يُقبل الملفات بصيغة <b>.xls</b> و <b>.xlsx</b> فقط.</li>
                                    <li>يجب أن يحتوي الملف على عمود <b>رقم الجهاز (AC-No)</b> وعمود <b>الوقت (Time)</b>.</li>
                                    <li>النظام يتعرف تلقائياً على الأعمدة باللغة العربية أو الإنجليزية.</li>
                                </ul>
                            </div>

                            <form action="do/doimportfp.php" method="post" enctype="multipart/form-data" class="mt-4">
                                
                                <div class="form-group">
                                    <label for="sheetFile"><i class="fas fa-file-excel text-success mr-1"></i> اختر الملف:</label>
                                    <div class="custom-file">
                                        <input required type="file" class="custom-file-input" name="sheet" id="sheetFile" accept=".xls,.xlsx,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
                                        <label class="custom-file-label" for="sheetFile">اختر ملف البصمة...</label>
                                    </div>
                                </div>

                                <div class="form-group mt-4">
                                    <label for="basmaModel"><i class="fas fa-hdd text-secondary mr-1"></i> نوع جهاز البصمة:</label>
                                    <select required class="form-control select2" name="basma_model" id="basmaModel">
                                        <option value="zkt">ZKTeco</option>
                                        <option value="advision">Advision</option>
                                        <option value="hikvision">Hikvision</option>
                                    </select>
                                </div>

                                <div class="form-group mt-5 text-center">
                                    <button type="submit" class="btn btn-primary btn-lg px-5 shadow-sm" style="border-radius: 30px;">
                                        <i class="fas fa-cloud-upload-alt mr-2"></i> بدء الاستيراد
                                    </button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </section>
</div>






<?php include('includes/footer.php')?>
