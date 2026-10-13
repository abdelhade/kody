<?php
require_once __DIR__ . '/receipt_design.php';
$rd = kody_receipt_design_load($rowstg ?? []);

$_logo_file = !empty($rowstg['company_logo']) ? $rowstg['company_logo'] : 'logo.jpg';
$logo_src_preview = 'assets/logo/' . $_logo_file;
if (!file_exists(__DIR__ . '/../assets/logo/' . $_logo_file)) {
    $logo_src_preview = 'assets/logo/logo.jpg';
}
$company_name_val = !empty($rowstg['company_name']) ? $rowstg['company_name'] : 'Focus House';
$company_tel_val  = !empty($rowstg['company_tel'])  ? $rowstg['company_tel']  : '01000000000';
$company_add_val  = !empty($rowstg['company_add'])  ? $rowstg['company_add']  : 'القاهرة - مصر';
?>

<style>
/* تحسين مظهر وتأثيرات الأكورديون دون تعارض مع Bootstrap */
#receiptDesignAccordion .card {
  border-radius: 12px;
  border: 1px solid #e2e8f0;
}
#receiptDesignAccordion .card-header.rd-acc-header {
  cursor: pointer;
  user-select: none;
  transition: background-color 0.15s ease;
  border-radius: 11px;
}
#receiptDesignAccordion .card-header.rd-acc-header:not(.collapsed) {
  border-radius: 11px 11px 0 0;
  border-bottom: 1px solid #edf2f7;
}
#receiptDesignAccordion .card-header.rd-acc-header:hover {
  background-color: #f8fafc;
}
#receiptDesignAccordion .card-header.rd-acc-header .rd-acc-chevron {
  transition: transform 0.25s ease;
}
#receiptDesignAccordion .card-header.rd-acc-header:not(.collapsed) .rd-acc-chevron {
  transform: rotate(180deg);
}
#receiptDesignAccordion .card-header.rd-acc-header.collapsed .rd-acc-chevron {
  transform: rotate(0deg);
}

/* تنسيق المعاينة الحية لـ A4 ورول الكاشير */
.rd-a4-mode {
  width: 794px !important;
  max-width: 794px !important;
  min-height: 1040px !important;
  background: #ffffff !important;
  box-shadow: 0 14px 40px rgba(0,0,0,0.4) !important;
  border-radius: 4px;
  direction: rtl !important;
  box-sizing: border-box !important;
}
.rd-thermal-mode {
  box-shadow: 0 8px 25px rgba(0,0,0,0.3) !important;
  direction: rtl !important;
  box-sizing: border-box !important;
}

/* زر ملاءمة A4 */
.rd-a4-zoom-btn.active {
  background-color: #3b82f6 !important;
  border-color: #3b82f6 !important;
  color: #fff !important;
}
</style>

<div class="card card-outline card-primary shadow-sm border-0 mb-4" style="border-radius: 14px; overflow: hidden;">
  <div class="card-header bg-white py-3 d-flex flex-wrap align-items-center justify-content-between">
    <div class="d-flex align-items-center mb-2 mb-md-0">
      <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center ml-3" style="width: 44px; height: 44px; font-size: 1.25rem;">
        <i class="fas fa-receipt"></i>
      </div>
      <div>
        <h4 class="card-title text-dark font-weight-bold mb-0">مصمم قوالب الفواتير والإيصالات الديناميكي</h4>
        <small class="text-muted d-block">تحكم كامل في مظهر الفاتورة والريسيت، وتفعيل الحقول والأعمدة مع معاينة حية فورية</small>
      </div>
    </div>
    <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
      <button type="button" class="btn btn-outline-info btn-sm font-weight-bold" id="btnTestPrintReceipt">
        <i class="fas fa-print ml-1"></i> تجربة طباعة المعاينة
      </button>
      <button type="button" class="btn btn-outline-secondary btn-sm" id="btnResetReceiptDefaults">
        <i class="fas fa-undo ml-1"></i> استعادة الافتراضي
      </button>
    </div>
  </div>

  <div class="card-body bg-light p-3 p-md-4">
    <!-- شريط القوالب الجاهزة السريعة -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; background: #ffffff;">
      <div class="card-body py-3 px-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between">
          <div class="d-flex align-items-center mb-2 mb-lg-0">
            <i class="fas fa-magic text-warning ml-2 font-weight-bold" style="font-size: 1.1rem;"></i>
            <span class="font-weight-bold text-dark ml-3">قوالب طباعة جاهزة بضغطة زر:</span>
          </div>
          <div class="btn-group btn-group-sm flex-wrap" role="group">
            <button type="button" class="btn btn-outline-primary active rd-preset-btn" data-preset="80mm">
              <i class="fas fa-scroll ml-1"></i> ريسيت كاشير 80mm
            </button>
            <button type="button" class="btn btn-outline-primary rd-preset-btn" data-preset="58mm">
              <i class="fas fa-receipt ml-1"></i> طابعة حرارية 58mm
            </button>
            <button type="button" class="btn btn-outline-primary rd-preset-btn" data-preset="a4">
              <i class="fas fa-file-invoice ml-1"></i> فاتورة مبيعات A4
            </button>
            <button type="button" class="btn btn-outline-primary rd-preset-btn" data-preset="restaurant">
              <i class="fas fa-utensils ml-1"></i> مطاعم ودليفري
            </button>
            <button type="button" class="btn btn-outline-primary rd-preset-btn" data-preset="minimal">
              <i class="fas fa-compress-alt ml-1"></i> إيصال مختصر سوبرماركت
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <!-- عمود عناصر التحكم والإعدادات -->
      <div class="col-xl-7 col-lg-6 mb-4">
        
        <!-- مفتاح تفعيل التصميم الديناميكي -->
        <div class="card border-0 shadow-sm mb-3" style="border-radius: 12px;">
          <div class="card-body py-3 px-4 d-flex justify-content-between align-items-center">
            <div>
              <h6 class="mb-0 font-weight-bold text-dark"><i class="fas fa-toggle-on text-success ml-2"></i> تفعيل المحرك الديناميكي للطباعة</h6>
              <small class="text-muted">عند التفعيل يتم تطبيق جميع التخصيصات والأعمدة والأحجام تلقائياً على فواتير الكاشير والمبيعات</small>
            </div>
            <div class="custom-control custom-switch custom-switch-lg">
              <input type="checkbox" class="custom-control-input rd-live" id="rd_enabled" name="rd_enabled" value="1" <?= !empty($rd['enabled']) ? 'checked' : '' ?>>
              <label class="custom-control-label" for="rd_enabled"></label>
            </div>
          </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3 px-1">
          <small class="text-muted font-weight-bold"><i class="fas fa-layer-group ml-1"></i> أقسام تخصيص الفاتورة:</small>
          <div class="btn-group btn-group-sm">
            <button type="button" class="btn btn-outline-primary btn-sm font-weight-bold" id="rdBtnExpandAll" style="font-size: 0.8rem;">
              <i class="fas fa-angle-double-down ml-1"></i> فتح جميع الأقسام
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm" id="rdBtnCollapseAll" style="font-size: 0.8rem;">
              <i class="fas fa-angle-double-up ml-1"></i> طي جميع الأقسام
            </button>
          </div>
        </div>

        <div class="accordion" id="receiptDesignAccordion">

          <!-- 1. مقاس الورقة وسلوك الطباعة -->
          <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white py-3 rd-acc-header" id="headingPaper" style="cursor: pointer;" data-toggle="collapse" data-target="#collapsePaper" aria-expanded="true">
              <div class="d-flex justify-content-between align-items-center">
                <span class="font-weight-bold text-dark">
                  <i class="fas fa-ruler-combined text-primary ml-2"></i> 1. مقاس الورقة، الخطوط وسلوك الطباعة
                </span>
                <i class="fas fa-chevron-down text-muted rd-acc-chevron"></i>
              </div>
            </div>
            <div id="collapsePaper" class="collapse show">
              <div class="card-body">
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="rd_paper_width" class="font-weight-bold">عرض ورقة الطباعة</label>
                      <select class="form-control rd-live" id="rd_paper_width" name="rd_paper_width">
                        <option value="78mm" <?= $rd['paper_width'] === '78mm' ? 'selected' : '' ?>>80mm قياسي (الفعلي 78mm)</option>
                        <option value="58mm" <?= $rd['paper_width'] === '58mm' ? 'selected' : '' ?>>58mm طابعة حرارية صغيرة</option>
                        <option value="100%" <?= $rd['paper_width'] === '100%' ? 'selected' : '' ?>>100% كامل الشاشة / A4</option>
                        <option value="custom" <?= (!in_array($rd['paper_width'], ['78mm', '58mm', '100%'], true)) ? 'selected' : '' ?>>مقاس مخصص (مم)</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-6" id="rd_custom_width_wrapper" style="<?= (!in_array($rd['paper_width'], ['78mm', '58mm', '100%'], true)) ? '' : 'display:none;' ?>">
                    <div class="form-group">
                      <label for="rd_paper_width_custom" class="font-weight-bold">المقاس المخصص (بالـ مم)</label>
                      <input type="number" class="form-control rd-live" id="rd_paper_width_custom" name="rd_paper_width_custom" min="40" max="250" step="1"
                             value="<?= preg_replace('/\D/', '', $rd['paper_width']) ?: 78 ?>">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="rd_font_family" class="font-weight-bold">نوع الخط (Font Family)</label>
                      <select class="form-control rd-live" id="rd_font_family" name="rd_font_family">
                        <option value="Cairo" <?= $rd['font_family'] === 'Cairo' ? 'selected' : '' ?>>Cairo (كايرو - حديث وأنيق)</option>
                        <option value="Arial" <?= $rd['font_family'] === 'Arial' ? 'selected' : '' ?>>Arial (عريض وواضح)</option>
                        <option value="Tahoma" <?= $rd['font_family'] === 'Tahoma' ? 'selected' : '' ?>>Tahoma (تاهوما الكلاسيكي)</option>
                        <option value="Segoe UI" <?= $rd['font_family'] === 'Segoe UI' ? 'selected' : '' ?>>Segoe UI</option>
                        <option value="Courier New" <?= $rd['font_family'] === 'Courier New' ? 'selected' : '' ?>>Courier New (طابعات نقطية)</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="rd_font_size" class="font-weight-bold">حجم الخط الأساسي (px)</label>
                      <div class="input-group">
                        <input type="number" class="form-control rd-live" id="rd_font_size" name="rd_font_size" min="9" max="24"
                               value="<?= (int)$rd['font_size'] ?>">
                        <div class="input-group-append"><span class="input-group-text">px</span></div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="rd_line_spacing" class="font-weight-bold">تباعد الأسطر</label>
                      <select class="form-control rd-live" id="rd_line_spacing" name="rd_line_spacing">
                        <option value="1.1" <?= $rd['line_spacing'] == 1.1 ? 'selected' : '' ?>>مضغوط (1.1)</option>
                        <option value="1.25" <?= $rd['line_spacing'] == 1.25 ? 'selected' : '' ?>>طبيعي (1.25)</option>
                        <option value="1.4" <?= $rd['line_spacing'] == 1.4 ? 'selected' : '' ?>>متباعد (1.4)</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="font-weight-bold">حدود وإطار الإيصال</label>
                      <div class="custom-control custom-switch pt-1">
                        <input type="checkbox" class="custom-control-input rd-live" id="rd_show_borders" name="rd_show_borders" value="1" <?= !empty($rd['show_borders']) ? 'checked' : '' ?>>
                        <label class="custom-control-label" for="rd_show_borders">إظهار إطار حدود خارجي للإيصال</label>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="font-weight-bold">الطباعة التلقائية</label>
                      <div class="custom-control custom-switch pt-1">
                        <input type="checkbox" class="custom-control-input rd-live" id="rd_auto_print" name="rd_auto_print" value="1" <?= !empty($rd['auto_print']) ? 'checked' : '' ?>>
                        <label class="custom-control-label" for="rd_auto_print">فتح نافذة الطباعة فور فتح الصفحة</label>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="font-weight-bold">الرجوع التلقائي</label>
                      <div class="custom-control custom-switch pt-1">
                        <input type="checkbox" class="custom-control-input rd-live" id="rd_auto_close" name="rd_auto_close" value="1" <?= !empty($rd['auto_close']) ? 'checked' : '' ?>>
                        <label class="custom-control-label" for="rd_auto_close">إغلاق أو رجوع للصفحة السابقة بعد أمر الطباعة</label>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- الهوامش -->
                <h6 class="text-muted mt-2 mb-2 font-weight-bold"><i class="fas fa-arrows-alt ml-1"></i> هوامش الورقة (مم)</h6>
                <div class="row">
                  <div class="col-3">
                    <div class="form-group mb-0">
                      <label class="small text-muted mb-1">علوي</label>
                      <input type="number" class="form-control form-control-sm rd-live" id="rd_margin_top" name="rd_margin_top" min="0" max="25" step="0.5" value="<?= $rd['margin_top'] ?>">
                    </div>
                  </div>
                  <div class="col-3">
                    <div class="form-group mb-0">
                      <label class="small text-muted mb-1">سفلي</label>
                      <input type="number" class="form-control form-control-sm rd-live" id="rd_margin_bottom" name="rd_margin_bottom" min="0" max="25" step="0.5" value="<?= $rd['margin_bottom'] ?>">
                    </div>
                  </div>
                  <div class="col-3">
                    <div class="form-group mb-0">
                      <label class="small text-muted mb-1">أيمن</label>
                      <input type="number" class="form-control form-control-sm rd-live" id="rd_margin_right" name="rd_margin_right" min="0" max="25" step="0.5" value="<?= $rd['margin_right'] ?>">
                    </div>
                  </div>
                  <div class="col-3">
                    <div class="form-group mb-0">
                      <label class="small text-muted mb-1">أيسر</label>
                      <input type="number" class="form-control form-control-sm rd-live" id="rd_margin_left" name="rd_margin_left" min="0" max="25" step="0.5" value="<?= $rd['margin_left'] ?>">
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- 2. الترويسة والشعار وبيانات الشركة -->
          <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white py-3 rd-acc-header collapsed" id="headingHeader" style="cursor: pointer;" data-toggle="collapse" data-target="#collapseHeader" aria-expanded="false">
              <div class="d-flex justify-content-between align-items-center">
                <span class="font-weight-bold text-dark">
                  <i class="fas fa-building text-info ml-2"></i> 2. الشعار، اسم الشركة وبيانات الترويسة
                </span>
                <i class="fas fa-chevron-down text-muted rd-acc-chevron"></i>
              </div>
            </div>
            <div id="collapseHeader" class="collapse">
              <div class="card-body">
                <div class="row">
                  <div class="col-md-6">
                    <div class="custom-control custom-switch mb-3">
                      <input type="checkbox" class="custom-control-input rd-live" id="rd_show_logo" name="rd_show_logo" value="1" <?= !empty($rd['header']['show_logo']) ? 'checked' : '' ?>>
                      <label class="custom-control-label font-weight-bold" for="rd_show_logo">إظهار شعار الشركة (اللوجو)</label>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="custom-control custom-switch mb-3">
                      <input type="checkbox" class="custom-control-input rd-live" id="rd_show_company_name" name="rd_show_company_name" value="1" <?= !empty($rd['header']['show_company_name']) ? 'checked' : '' ?>>
                      <label class="custom-control-label font-weight-bold" for="rd_show_company_name">إظهار اسم الشركة</label>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="rd_logo_width" class="small font-weight-bold">عرض الشعار (px)</label>
                      <input type="number" class="form-control form-control-sm rd-live" id="rd_logo_width" name="rd_logo_width" min="30" max="220" value="<?= $rd['header']['logo_width'] ?>">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="rd_logo_align" class="small font-weight-bold">محاذاة الشعار</label>
                      <select class="form-control form-control-sm rd-live" id="rd_logo_align" name="rd_logo_align">
                        <option value="center" <?= $rd['header']['logo_align'] === 'center' ? 'selected' : '' ?>>وسط</option>
                        <option value="right" <?= $rd['header']['logo_align'] === 'right' ? 'selected' : '' ?>>يمين</option>
                        <option value="left" <?= $rd['header']['logo_align'] === 'left' ? 'selected' : '' ?>>يسار</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="custom-control custom-switch mb-2">
                      <input type="checkbox" class="custom-control-input rd-live" id="rd_show_company_tel" name="rd_show_company_tel" value="1" <?= !empty($rd['header']['show_company_tel']) ? 'checked' : '' ?>>
                      <label class="custom-control-label" for="rd_show_company_tel">إظهار هاتف الشركة</label>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="custom-control custom-switch mb-2">
                      <input type="checkbox" class="custom-control-input rd-live" id="rd_show_company_add" name="rd_show_company_add" value="1" <?= !empty($rd['header']['show_company_add']) ? 'checked' : '' ?>>
                      <label class="custom-control-label" for="rd_show_company_add">إظهار عنوان الشركة</label>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="custom-control custom-switch mb-2">
                      <input type="checkbox" class="custom-control-input rd-live" id="rd_show_tax_no" name="rd_show_tax_no" value="1" <?= !empty($rd['header']['show_tax_no']) ? 'checked' : '' ?>>
                      <label class="custom-control-label" for="rd_show_tax_no">إظهار الرقم الضريبي (VAT)</label>
                    </div>
                    <input type="text" class="form-control form-control-sm rd-live mt-1" id="rd_tax_no" name="rd_tax_no" placeholder="الرقم الضريبي..." value="<?= htmlspecialchars($rd['header']['tax_no'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                  </div>
                  <div class="col-md-6">
                    <div class="custom-control custom-switch mb-2">
                      <input type="checkbox" class="custom-control-input rd-live" id="rd_show_cr_no" name="rd_show_cr_no" value="1" <?= !empty($rd['header']['show_cr_no']) ? 'checked' : '' ?>>
                      <label class="custom-control-label" for="rd_show_cr_no">إظهار السجل التجاري (CR)</label>
                    </div>
                    <input type="text" class="form-control form-control-sm rd-live mt-1" id="rd_cr_no" name="rd_cr_no" placeholder="السجل التجاري..." value="<?= htmlspecialchars($rd['header']['cr_no'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                  </div>
                  <div class="col-12 mt-3">
                    <div class="form-group mb-0">
                      <label for="rd_header_text" class="font-weight-bold">نص إضافي في أعلى الفاتورة (الهيدر)</label>
                      <textarea class="form-control rd-live" id="rd_header_text" name="rd_header_text" rows="2" placeholder="مثال: أهلاً وسهلاً بكم في متجرنا"><?= htmlspecialchars($rd['header']['header_text'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- 3. بيانات الفاتورة والعميل -->
          <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white py-3 rd-acc-header collapsed" id="headingMeta" style="cursor: pointer;" data-toggle="collapse" data-target="#collapseMeta" aria-expanded="false">
              <div class="d-flex justify-content-between align-items-center">
                <span class="font-weight-bold text-dark">
                  <i class="fas fa-file-invoice text-success ml-2"></i> 3. بيانات الفاتورة، الباركود والعميل
                </span>
                <i class="fas fa-chevron-down text-muted rd-acc-chevron"></i>
              </div>
            </div>
            <div id="collapseMeta" class="collapse">
              <div class="card-body">
                <div class="row">
                  <div class="col-md-6">
                    <div class="custom-control custom-switch mb-2">
                      <input type="checkbox" class="custom-control-input rd-live" id="rd_show_invoice_num" name="rd_show_invoice_num" value="1" <?= !empty($rd['meta']['show_invoice_num']) ? 'checked' : '' ?>>
                      <label class="custom-control-label font-weight-bold" for="rd_show_invoice_num">إظهار رقم الفاتورة</label>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="rd_invoice_num_style" class="small font-weight-bold">شكل رقم الفاتورة</label>
                      <select class="form-control form-control-sm rd-live" id="rd_invoice_num_style" name="rd_invoice_num_style">
                        <option value="badge" <?= $rd['meta']['invoice_num_style'] === 'badge' ? 'selected' : '' ?>>شارة مميزة بإطار (#1025)</option>
                        <option value="large" <?= $rd['meta']['invoice_num_style'] === 'large' ? 'selected' : '' ?>>خط كبير واضح</option>
                        <option value="plain" <?= $rd['meta']['invoice_num_style'] === 'plain' ? 'selected' : '' ?>>نص عادي</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="custom-control custom-switch mb-2">
                      <input type="checkbox" class="custom-control-input rd-live" id="rd_show_invoice_barcode" name="rd_show_invoice_barcode" value="1" <?= !empty($rd['meta']['show_invoice_barcode']) ? 'checked' : '' ?>>
                      <label class="custom-control-label font-weight-bold" for="rd_show_invoice_barcode">باركود رقم الفاتورة (Barcode 128)</label>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="rd_barcode_height" class="small font-weight-bold">ارتفاع باركود الفاتورة (px)</label>
                      <input type="number" class="form-control form-control-sm rd-live" id="rd_barcode_height" name="rd_barcode_height" min="15" max="60" value="<?= $rd['meta']['barcode_height'] ?>">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="custom-control custom-switch mb-2">
                      <input type="checkbox" class="custom-control-input rd-live" id="rd_show_datetime" name="rd_show_datetime" value="1" <?= !empty($rd['meta']['show_datetime']) ? 'checked' : '' ?>>
                      <label class="custom-control-label" for="rd_show_datetime">إظهار التاريخ والوقت</label>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="custom-control custom-switch mb-2">
                      <input type="checkbox" class="custom-control-input rd-live" id="rd_show_cashier" name="rd_show_cashier" value="1" <?= !empty($rd['meta']['show_cashier']) ? 'checked' : '' ?>>
                      <label class="custom-control-label" for="rd_show_cashier">إظهار اسم الكاشير / الموظف</label>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="custom-control custom-switch mb-2">
                      <input type="checkbox" class="custom-control-input rd-live" id="rd_show_order_type" name="rd_show_order_type" value="1" <?= !empty($rd['meta']['show_order_type']) ? 'checked' : '' ?>>
                      <label class="custom-control-label" for="rd_show_order_type">إظهار نوع الطلب (دليفري/طاولة/صالة)</label>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="custom-control custom-switch mb-2">
                      <input type="checkbox" class="custom-control-input rd-live" id="rd_show_driver" name="rd_show_driver" value="1" <?= !empty($rd['meta']['show_driver']) ? 'checked' : '' ?>>
                      <label class="custom-control-label" for="rd_show_driver">إظهار اسم الطيار (الدليفري)</label>
                    </div>
                  </div>

                  <!-- بيانات العميل -->
                  <div class="col-12"><hr class="my-3"></div>
                  <div class="col-12 mb-2 font-weight-bold text-dark"><i class="fas fa-user ml-1 text-primary"></i> بيانات العميل</div>
                  <div class="col-md-6">
                    <div class="custom-control custom-switch mb-2">
                      <input type="checkbox" class="custom-control-input rd-live" id="rd_show_customer" name="rd_show_customer" value="1" <?= !empty($rd['customer']['show']) ? 'checked' : '' ?>>
                      <label class="custom-control-label font-weight-bold" for="rd_show_customer">إظهار كتلة العميل بالكامل</label>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="custom-control custom-switch mb-2">
                      <input type="checkbox" class="custom-control-input rd-live" id="rd_show_customer_name" name="rd_show_customer_name" value="1" <?= !empty($rd['customer']['show_name']) ? 'checked' : '' ?>>
                      <label class="custom-control-label" for="rd_show_customer_name">اسم العميل</label>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="custom-control custom-switch mb-2">
                      <input type="checkbox" class="custom-control-input rd-live" id="rd_show_customer_phone" name="rd_show_customer_phone" value="1" <?= !empty($rd['customer']['show_phone']) ? 'checked' : '' ?>>
                      <label class="custom-control-label" for="rd_show_customer_phone">هاتف العميل</label>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="custom-control custom-switch mb-2">
                      <input type="checkbox" class="custom-control-input rd-live" id="rd_show_customer_address" name="rd_show_customer_address" value="1" <?= !empty($rd['customer']['show_address']) ? 'checked' : '' ?>>
                      <label class="custom-control-label" for="rd_show_customer_address">عنوان العميل</label>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- 4. جدول الأصناف وتخصيص الأعمدة -->
          <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white py-3 rd-acc-header collapsed" id="headingItems" style="cursor: pointer;" data-toggle="collapse" data-target="#collapseItems" aria-expanded="false">
              <div class="d-flex justify-content-between align-items-center">
                <span class="font-weight-bold text-dark">
                  <i class="fas fa-th-list text-warning ml-2"></i> 4. جدول الأصناف وتخصيص الأعمدة
                </span>
                <i class="fas fa-chevron-down text-muted rd-acc-chevron"></i>
              </div>
            </div>
            <div id="collapseItems" class="collapse">
              <div class="card-body">
                <p class="small text-muted mb-3">اختر الأعمدة التي تظهر في جدول الأصناف في الفاتورة المطبوعة:</p>
                <div class="row">
                  <div class="col-6 col-md-3">
                    <div class="custom-control custom-switch mb-3">
                      <input type="checkbox" class="custom-control-input rd-live" id="rd_show_col_index" name="rd_show_col_index" value="1" <?= !empty($rd['items']['show_index']) ? 'checked' : '' ?>>
                      <label class="custom-control-label" for="rd_show_col_index">مسلسل (#)</label>
                    </div>
                  </div>
                  <div class="col-6 col-md-3">
                    <div class="custom-control custom-switch mb-3">
                      <input type="checkbox" class="custom-control-input rd-live" id="rd_show_col_code" name="rd_show_col_code" value="1" <?= !empty($rd['items']['show_code']) ? 'checked' : '' ?>>
                      <label class="custom-control-label" for="rd_show_col_code">كود الصنف</label>
                    </div>
                  </div>
                  <div class="col-6 col-md-3">
                    <div class="custom-control custom-switch mb-3">
                      <input type="checkbox" class="custom-control-input rd-live" id="rd_show_col_name" name="rd_show_col_name" value="1" <?= !empty($rd['items']['show_name']) ? 'checked' : '' ?>>
                      <label class="custom-control-label font-weight-bold" for="rd_show_col_name">اسم الصنف</label>
                    </div>
                  </div>
                  <div class="col-6 col-md-3">
                    <div class="custom-control custom-switch mb-3">
                      <input type="checkbox" class="custom-control-input rd-live" id="rd_show_col_qty" name="rd_show_col_qty" value="1" <?= !empty($rd['items']['show_qty']) ? 'checked' : '' ?>>
                      <label class="custom-control-label font-weight-bold" for="rd_show_col_qty">الكمية</label>
                    </div>
                  </div>
                  <div class="col-6 col-md-3">
                    <div class="custom-control custom-switch mb-3">
                      <input type="checkbox" class="custom-control-input rd-live" id="rd_show_col_unit" name="rd_show_col_unit" value="1" <?= !empty($rd['items']['show_unit']) ? 'checked' : '' ?>>
                      <label class="custom-control-label" for="rd_show_col_unit">الوحدة</label>
                    </div>
                  </div>
                  <div class="col-6 col-md-3">
                    <div class="custom-control custom-switch mb-3">
                      <input type="checkbox" class="custom-control-input rd-live" id="rd_show_col_price" name="rd_show_col_price" value="1" <?= !empty($rd['items']['show_price']) ? 'checked' : '' ?>>
                      <label class="custom-control-label font-weight-bold" for="rd_show_col_price">السعر</label>
                    </div>
                  </div>
                  <div class="col-6 col-md-3">
                    <div class="custom-control custom-switch mb-3">
                      <input type="checkbox" class="custom-control-input rd-live" id="rd_show_col_disc" name="rd_show_col_disc" value="1" <?= !empty($rd['items']['show_disc']) ? 'checked' : '' ?>>
                      <label class="custom-control-label" for="rd_show_col_disc">عمود الخصم</label>
                    </div>
                  </div>
                  <div class="col-6 col-md-3">
                    <div class="custom-control custom-switch mb-3">
                      <input type="checkbox" class="custom-control-input rd-live" id="rd_show_col_total" name="rd_show_col_total" value="1" <?= !empty($rd['items']['show_total']) ? 'checked' : '' ?>>
                      <label class="custom-control-label font-weight-bold" for="rd_show_col_total">الإجمالي (القيمة)</label>
                    </div>
                  </div>
                </div>

                <div class="row pt-2 border-top">
                  <div class="col-md-6">
                    <div class="custom-control custom-switch pt-2">
                      <input type="checkbox" class="custom-control-input rd-live" id="rd_show_item_disc_subline" name="rd_show_item_disc_subline" value="1" <?= !empty($rd['items']['show_item_disc_subline']) ? 'checked' : '' ?>>
                      <label class="custom-control-label" for="rd_show_item_disc_subline">إظهار خصم الصنف كسطر فرعي تحت الاسم</label>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group mb-0">
                      <label for="rd_table_border_style" class="small font-weight-bold">نمط حدود الجدول</label>
                      <select class="form-control form-control-sm rd-live" id="rd_table_border_style" name="rd_table_border_style">
                        <option value="solid" <?= $rd['items']['border_style'] === 'solid' ? 'selected' : '' ?>>خطوط كاملة (Solid)</option>
                        <option value="dashed" <?= $rd['items']['border_style'] === 'dashed' ? 'selected' : '' ?>>خطوط متقطعة (Dashed)</option>
                        <option value="minimal" <?= $rd['items']['border_style'] === 'minimal' ? 'selected' : '' ?>>خطوط أفقية خفيفة (Minimal)</option>
                        <option value="none" <?= $rd['items']['border_style'] === 'none' ? 'selected' : '' ?>>بدون حدود داخلية</option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- 5. الإجماليات والمبالغ -->
          <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white py-3 rd-acc-header collapsed" id="headingTotals" style="cursor: pointer;" data-toggle="collapse" data-target="#collapseTotals" aria-expanded="false">
              <div class="d-flex justify-content-between align-items-center">
                <span class="font-weight-bold text-dark">
                  <i class="fas fa-calculator text-danger ml-2"></i> 5. الإجماليات، الخصومات والمدفوع
                </span>
                <i class="fas fa-chevron-down text-muted rd-acc-chevron"></i>
              </div>
            </div>
            <div id="collapseTotals" class="collapse">
              <div class="card-body">
                <div class="row">
                  <div class="col-6 col-md-4">
                    <div class="custom-control custom-switch mb-3">
                      <input type="checkbox" class="custom-control-input rd-live" id="rd_show_items_total" name="rd_show_items_total" value="1" <?= !empty($rd['totals']['show_items_total']) ? 'checked' : '' ?>>
                      <label class="custom-control-label" for="rd_show_items_total">إجمالي الأصناف</label>
                    </div>
                  </div>
                  <div class="col-6 col-md-4">
                    <div class="custom-control custom-switch mb-3">
                      <input type="checkbox" class="custom-control-input rd-live" id="rd_show_items_disc" name="rd_show_items_disc" value="1" <?= !empty($rd['totals']['show_items_disc']) ? 'checked' : '' ?>>
                      <label class="custom-control-label" for="rd_show_items_disc">خصم الأصناف</label>
                    </div>
                  </div>
                  <div class="col-6 col-md-4">
                    <div class="custom-control custom-switch mb-3">
                      <input type="checkbox" class="custom-control-input rd-live" id="rd_show_subtotal" name="rd_show_subtotal" value="1" <?= !empty($rd['totals']['show_subtotal']) ? 'checked' : '' ?>>
                      <label class="custom-control-label font-weight-bold" for="rd_show_subtotal">الإجمالي</label>
                    </div>
                  </div>
                  <div class="col-6 col-md-4">
                    <div class="custom-control custom-switch mb-3">
                      <input type="checkbox" class="custom-control-input rd-live" id="rd_show_inv_disc" name="rd_show_inv_disc" value="1" <?= !empty($rd['totals']['show_inv_disc']) ? 'checked' : '' ?>>
                      <label class="custom-control-label" for="rd_show_inv_disc">خصم الفاتورة</label>
                    </div>
                  </div>
                  <div class="col-6 col-md-4">
                    <div class="custom-control custom-switch mb-3">
                      <input type="checkbox" class="custom-control-input rd-live" id="rd_show_extra" name="rd_show_extra" value="1" <?= !empty($rd['totals']['show_extra']) ? 'checked' : '' ?>>
                      <label class="custom-control-label" for="rd_show_extra">الإضافي / التوصيل</label>
                    </div>
                  </div>
                  <div class="col-6 col-md-4">
                    <div class="custom-control custom-switch mb-3">
                      <input type="checkbox" class="custom-control-input rd-live" id="rd_show_net" name="rd_show_net" value="1" <?= !empty($rd['totals']['show_net']) ? 'checked' : '' ?>>
                      <label class="custom-control-label font-weight-bold" for="rd_show_net">الصافي</label>
                    </div>
                  </div>
                  <div class="col-6 col-md-4">
                    <div class="custom-control custom-switch mb-3">
                      <input type="checkbox" class="custom-control-input rd-live" id="rd_show_paid" name="rd_show_paid" value="1" <?= !empty($rd['totals']['show_paid']) ? 'checked' : '' ?>>
                      <label class="custom-control-label" for="rd_show_paid">المدفوع</label>
                    </div>
                  </div>
                  <div class="col-6 col-md-4">
                    <div class="custom-control custom-switch mb-3">
                      <input type="checkbox" class="custom-control-input rd-live" id="rd_show_change" name="rd_show_change" value="1" <?= !empty($rd['totals']['show_change']) ? 'checked' : '' ?>>
                      <label class="custom-control-label" for="rd_show_change">الباقي / المتبقي</label>
                    </div>
                  </div>
                  <div class="col-6 col-md-4">
                    <div class="custom-control custom-switch mb-3">
                      <input type="checkbox" class="custom-control-input rd-live" id="rd_highlight_net" name="rd_highlight_net" value="1" <?= !empty($rd['totals']['highlight_net']) ? 'checked' : '' ?>>
                      <label class="custom-control-label font-weight-bold text-primary" for="rd_highlight_net">تمييز الصافي بخط بارز</label>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- 6. التذييل والملاحظات -->
          <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white py-3 rd-acc-header collapsed" id="headingFooter" style="cursor: pointer;" data-toggle="collapse" data-target="#collapseFooter" aria-expanded="false">
              <div class="d-flex justify-content-between align-items-center">
                <span class="font-weight-bold text-dark">
                  <i class="fas fa-comment-dots text-purple ml-2"></i> 6. التذييل والشروط والأحكام
                </span>
                <i class="fas fa-chevron-down text-muted rd-acc-chevron"></i>
              </div>
            </div>
            <div id="collapseFooter" class="collapse">
              <div class="card-body">
                <div class="row">
                  <div class="col-12">
                    <div class="custom-control custom-switch mb-2">
                      <input type="checkbox" class="custom-control-input rd-live" id="rd_show_footer_text" name="rd_show_footer_text" value="1" <?= !empty($rd['footer']['show_footer_text']) ? 'checked' : '' ?>>
                      <label class="custom-control-label font-weight-bold" for="rd_show_footer_text">إظهار نص تذييل الفاتورة (الفوتر)</label>
                    </div>
                    <textarea class="form-control rd-live mb-3" id="rd_footer_text" name="rd_footer_text" rows="2" placeholder="مثال: شكرًا لزيارتكم! نتشرف بخدمتكم دائمًا"><?= htmlspecialchars($rd['footer']['footer_text'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                  </div>
                  <div class="col-12">
                    <div class="custom-control custom-switch mb-2">
                      <input type="checkbox" class="custom-control-input rd-live" id="rd_show_notes" name="rd_show_notes" value="1" <?= !empty($rd['footer']['show_notes']) ? 'checked' : '' ?>>
                      <label class="custom-control-label font-weight-bold" for="rd_show_notes">إظهار الملاحظات / الشروط وسياسة الاسترجاع</label>
                    </div>
                    <textarea class="form-control rd-live" id="rd_notes_text" name="rd_notes_text" rows="3" placeholder="مثال: الاستبدال والاسترجاع خلال 14 يوم بموجب الفاتورة الأصلية"><?= htmlspecialchars($rd['footer']['notes_text'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div> <!-- /accordion -->
      </div>

      <!-- عمود المعاينة الحية الفورية (Live Sticky Preview) -->
      <div class="col-xl-5 col-lg-6">
        <div class="sticky-top" style="top: 20px; z-index: 10;">
          <div class="card border-0 shadow-sm" style="border-radius: 14px; background: #2d3748; color: #fff;">
            <div class="card-header bg-transparent border-bottom border-secondary py-2 d-flex justify-content-between align-items-center flex-wrap" style="gap: 8px;">
              <div class="d-flex align-items-center" style="gap: 8px;">
                <span class="small font-weight-bold text-light">
                  <i class="fas fa-eye ml-1 text-warning"></i> معاينة حية فورية
                </span>
                <span class="badge badge-primary" id="rdPreviewWidthBadge">80mm</span>
              </div>
              <!-- أزرار التحكم بعرض A4 -->
              <div id="rdA4ZoomControls" style="display: none; gap: 4px;" class="btn-group btn-group-sm">
                <button type="button" class="btn btn-xs btn-outline-light active rd-a4-zoom-btn" data-zoom="fit" title="ملاءمة العرض لشاشة المعاينة">
                  <i class="fas fa-compress-arrows-alt ml-1"></i> ملاءمة
                </button>
                <button type="button" class="btn btn-xs btn-outline-light rd-a4-zoom-btn" data-zoom="100" title="الحجم الحقيقي 100% مع تمرير">
                  <i class="fas fa-search-plus ml-1"></i> 100%
                </button>
                <button type="button" class="btn btn-xs btn-info" id="btnOpenA4Modal" title="معاينة في نافذة مكبّرة كاملة">
                  <i class="fas fa-expand"></i>
                </button>
              </div>
            </div>
            
            <div class="card-body p-3 text-center" id="rdPreviewScrollArea" style="background: #1a202c; max-height: calc(100vh - 120px); overflow: auto;">
              <!-- وعاء محاكاة وتوسيط الورقة -->
              <div id="rdReceiptStageWrapper" style="position: relative; margin: 0 auto; vertical-align: top; transition: all 0.2s ease;">
                <div id="rdReceiptStage" class="rd-thermal-mode" style="background: #fff; color: #000; box-shadow: 0 8px 30px rgba(0,0,0,0.3); transition: all 0.2s ease;">
                  <div id="rdReceiptContent" style="padding: 8px;">
                    
                    <!-- ترويسة الفاتورة المجمعة (شعار وبيانات + رقم الفاتورة والباركود) -->
                    <div id="prevTopHeaderWrap">
                      <!-- ترويسة المعاينة (بيانات الشركة) -->
                      <div id="prevHeaderSection" style="text-align: center;">
                        <div id="prevLogoWrapper" style="margin-bottom: 4px;">
                          <img id="prevLogoImg" src="<?= htmlspecialchars($logo_src_preview, ENT_QUOTES, 'UTF-8') ?>" alt="" style="max-height: 80px; width: 90px; object-fit: contain; display: inline-block;">
                        </div>
                        <div id="prevCompanyName" style="font-weight: bold; font-size: 16px; margin: 2px 0;">
                          <?= htmlspecialchars($company_name_val, ENT_QUOTES, 'UTF-8') ?>
                        </div>
                        <div id="prevCompanyTel" style="font-size: 11px;"><?= htmlspecialchars($company_tel_val, ENT_QUOTES, 'UTF-8') ?></div>
                        <div id="prevCompanyAdd" style="font-size: 11px;"><?= htmlspecialchars($company_add_val, ENT_QUOTES, 'UTF-8') ?></div>
                        <div id="prevTaxNo" style="font-size: 11px; display: none;">الرقم الضريبي: <span id="prevTaxNoVal"></span></div>
                        <div id="prevCrNo" style="font-size: 11px; display: none;">السجل التجاري: <span id="prevCrNoVal"></span></div>
                        <div id="prevHeaderText" style="font-size: 11px; margin: 4px 0; white-space: pre-wrap;"></div>
                      </div>

                      <!-- رقم الفاتورة والباركود والميتا -->
                      <div id="prevMetaSection" style="text-align: center; margin: 6px 0;">
                        <div id="prevInvoiceTitle" style="display: none; font-weight: bold; font-size: 13px; color: #1e293b; border-bottom: 1px solid #cbd5e1; padding-bottom: 4px; margin-bottom: 6px;">فاتورة مبيعات</div>
                        <div id="prevInvoiceNumRow" style="margin: 3px 0;">
                          <div id="prevInvoiceNum" style="display: inline-block; border: 1px solid #000; padding: 2px 10px; font-weight: bold; font-size: 13px;">
                            #1025
                          </div>
                        </div>
                        <div id="prevOrderBanner" style="border: 1px dashed #000; padding: 2px; font-weight: bold; font-size: 12px; margin: 4px 0;">
                          دليفري — كابتن محمود
                        </div>
                        <div id="prevDateTime" style="font-size: 11px; color: #333; margin: 2px 0;">
                          <?= date('Y-m-d H:i') ?>
                        </div>
                        <div id="prevCashier" style="font-size: 11px; color: #333; margin: 2px 0;">
                          الكاشير: أحمد مصطفى
                        </div>
                        <div id="prevBarcodeWrapper" style="margin: 6px 0;">
                          <svg id="prevBarcodeSvg" style="display: block; margin: 0 auto; max-width: 100%; height: 32px;"></svg>
                        </div>
                      </div>
                    </div>

                    <!-- بيانات العميل -->
                    <div id="prevCustomerSection" style="border-top: 1px dashed #000; padding: 4px 0; margin: 4px 0; font-size: 12px;">
                      <div id="prevCustTitle" style="font-weight: bold; margin-bottom: 4px; display: none; color: #1e293b;"><i class="fas fa-user-tag ml-1 text-primary"></i> بيانات العميل:</div>
                      <div id="prevCustFields" style="display: flex; flex-wrap: wrap; gap: 15px;">
                        <div id="prevCustName"><strong>العميل:</strong> محمد إبراهيم</div>
                        <div id="prevCustPhone"><strong>الهاتف:</strong> 01122334455</div>
                        <div id="prevCustAddress"><strong>العنوان:</strong> مدينة نصر - الحي السابع</div>
                      </div>
                    </div>

                    <!-- جدول الأصناف -->
                    <div id="prevItemsSection" style="margin: 6px 0;">
                      <table id="prevItemsTable" style="width: 100%; border-collapse: collapse; font-size: 13px;">
                        <thead>
                          <tr id="prevTheadRow" style="border-bottom: 1px solid #000; border-top: 1px solid #000;">
                            <!-- يتم ملؤها ديناميكياً بواسطة جافاسكربت -->
                          </tr>
                        </thead>
                        <tbody id="prevTbodyRows">
                          <!-- يتم ملؤها ديناميكياً بواسطة جافاسكربت -->
                        </tbody>
                      </table>
                    </div>

                    <!-- قسم الإجماليات -->
                    <div id="prevTotalsSection" style="margin: 6px 0;">
                      <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                        <tr id="prevRowItemsTotal" style="border-bottom: 1px dashed #aaa;">
                          <td style="padding: 3px 0;">إجمالي الأصناف</td>
                          <td style="text-align: left; padding: 3px 0;">215.00</td>
                        </tr>
                        <tr id="prevRowItemsDisc" style="border-bottom: 1px dashed #aaa; color: #c00;">
                          <td style="padding: 3px 0;">خصم الأصناف</td>
                          <td style="text-align: left; padding: 3px 0;">- 15.00</td>
                        </tr>
                        <tr id="prevRowSubtotal" style="border-bottom: 1px dashed #000; font-weight: bold;">
                          <td style="padding: 3px 0;">الإجمالي</td>
                          <td style="text-align: left; padding: 3px 0;">200.00</td>
                        </tr>
                        <tr id="prevRowInvDisc" style="border-bottom: 1px dashed #aaa; color: #c00;">
                          <td style="padding: 3px 0;">الخصم (10%)</td>
                          <td style="text-align: left; padding: 3px 0;">- 20.00</td>
                        </tr>
                        <tr id="prevRowExtra" style="border-bottom: 1px dashed #aaa;">
                          <td style="padding: 3px 0;">خدمة توصيل</td>
                          <td style="text-align: left; padding: 3px 0;">+ 20.00</td>
                        </tr>
                        <tr id="prevRowNet" style="border-bottom: 2px solid #000; font-weight: bold; font-size: 15px;">
                          <td style="padding: 4px 0;">الصافي</td>
                          <td style="text-align: left; padding: 4px 0;">200.00</td>
                        </tr>
                        <tr id="prevRowPaid" style="border-bottom: 1px dashed #aaa;">
                          <td style="padding: 3px 0;">المدفوع</td>
                          <td style="text-align: left; padding: 3px 0;">250.00</td>
                        </tr>
                        <tr id="prevRowChange" style="font-weight: bold;">
                          <td style="padding: 3px 0;">الباقي</td>
                          <td style="text-align: left; padding: 3px 0;">50.00</td>
                        </tr>
                      </table>
                    </div>

                    <!-- تذييل الفاتورة والملاحظات -->
                    <div id="prevFooterSection" style="text-align: center; margin-top: 8px; border-top: 1px dashed #000; padding-top: 6px;">
                      <div id="prevFooterText" style="font-weight: bold; font-size: 12px; margin-bottom: 4px;">
                        ❤ perfect place to grow
                      </div>
                      <div id="prevNotesText" style="font-size: 10px; color: #444; border-top: 1px dashed #ccc; padding-top: 4px; white-space: pre-wrap;"></div>
                    </div>

                  </div>
                </div>
              </div>
            </div>
            
            <div class="card-footer bg-transparent border-top border-secondary py-2 text-center text-muted small">
              <i class="fas fa-info-circle ml-1"></i> تتحدث المعاينة تلقائياً مع كل تغيير في الإعدادات
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- نافذة منبثقة لمعاينة A4 بالحجم الكامل -->
<div class="modal fade" id="rdA4Modal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document" style="max-width: 860px;">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 14px;">
      <div class="modal-header bg-white py-3 px-4 d-flex justify-content-between align-items-center">
        <h5 class="modal-title font-weight-bold mb-0 text-dark">
          <i class="fas fa-file-invoice text-primary ml-2"></i> معاينة فاتورة A4 بحجم كامل
        </h5>
        <button type="button" class="close ml-0" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body p-4 text-center" style="background-color: #525659; overflow-y: auto;">
        <div id="rdModalA4Container" style="display: inline-block; background: #fff; text-align: right; box-shadow: 0 10px 40px rgba(0,0,0,0.5); min-height: 1000px; width: 794px; padding: 25px; border-radius: 4px;">
        </div>
      </div>
      <div class="modal-footer py-2 bg-light d-flex justify-content-between">
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">إغلاق</button>
        <button type="button" class="btn btn-primary btn-sm" id="btnModalPrintTest">
          <i class="fas fa-print ml-1"></i> تجربة الطباعة
        </button>
      </div>
    </div>
  </div>
</div>

<script src="print/code.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  var stage = document.getElementById('rdReceiptStage');
  var content = document.getElementById('rdReceiptContent');
  var stageWrapper = document.getElementById('rdReceiptStageWrapper');
  if (!stage || !content) return;

  var currentA4Zoom = 'fit'; // 'fit' أو '100'

  var sampleItems = [
    { idx: '1', code: '101', name: 'وجبة كومبو عائلية', qty: '1.00', unit: 'وجبة', price: '120.00', disc: '10.00', total: '110.00' },
    { idx: '2', code: '205', name: 'ساندوتش كرسبي حار', qty: '2.00', unit: 'قطعة', price: '35.00', disc: '5.00', total: '65.00' },
    { idx: '3', code: '312', name: 'بيبسي كانز 330 مل', qty: '2.00', unit: 'علبة', price: '12.50', disc: '0.00', total: '25.00' }
  ];

  function getVal(id) {
    var el = document.getElementById(id);
    return el ? el.value : '';
  }
  function isChecked(id) {
    var el = document.getElementById(id);
    return el ? el.checked : false;
  }
  function setVal(id, val) {
    var el = document.getElementById(id);
    if (el) el.value = val;
  }
  function setChecked(id, state) {
    var el = document.getElementById(id);
    if (el) el.checked = !!state;
  }

  // تطبيق التكبير/الملاءمة على A4
  function applyA4Zoom() {
    var widthSelect = getVal('rd_paper_width');
    var isA4 = (widthSelect === '100%');

    if (!isA4) {
      stage.style.transform = 'none';
      stage.style.position = 'relative';
      stage.style.top = 'auto';
      stage.style.left = 'auto';
      stage.style.right = 'auto';
      stage.style.margin = '0 auto';
      if (stageWrapper) {
        stageWrapper.style.direction = 'rtl';
        stageWrapper.style.position = 'relative';
        stageWrapper.style.width = 'auto';
        stageWrapper.style.height = 'auto';
        stageWrapper.style.display = 'inline-block';
        stageWrapper.style.overflow = 'visible';
      }
      return;
    }

    var scrollArea = document.getElementById('rdPreviewScrollArea');
    var availW = 380;
    if (scrollArea && scrollArea.clientWidth > 100) {
      availW = scrollArea.clientWidth - 28;
    }

    var a4RealWidth = 794;

    if (currentA4Zoom === 'fit') {
      var scale = Math.min(1, Math.max(0.25, availW / a4RealWidth));

      // إعادة ضبط مؤقت لقراءة الارتفاع الطبيعي لمحتوى A4 بدقة
      stage.style.transform = 'none';
      stage.style.position = 'relative';
      stage.style.top = 'auto';
      stage.style.left = 'auto';
      stage.style.right = 'auto';
      stage.style.width = a4RealWidth + 'px';
      stage.style.margin = '0';

      var naturalH = Math.max(1050, stage.scrollHeight || stage.offsetHeight || 1050);

      // تطبيق التكبير المثبت في أعلى يسار الوعاء (LTR wrapper) لمنع أي انزياح أو قص
      stage.style.position = 'absolute';
      stage.style.top = '0';
      stage.style.left = '0';
      stage.style.transformOrigin = '0 0';
      stage.style.transform = 'scale(' + scale + ')';

      if (stageWrapper) {
        stageWrapper.style.direction = 'ltr';
        stageWrapper.style.position = 'relative';
        stageWrapper.style.display = 'block';
        stageWrapper.style.margin = '0 auto';
        stageWrapper.style.width = Math.round(a4RealWidth * scale) + 'px';
        stageWrapper.style.height = Math.round(naturalH * scale + 15) + 'px';
        stageWrapper.style.overflow = 'hidden';
      }
    } else {
      // وضع 100% الحقيقي مع شريط تمرير
      stage.style.transform = 'none';
      stage.style.position = 'relative';
      stage.style.top = 'auto';
      stage.style.left = 'auto';
      stage.style.right = 'auto';
      stage.style.margin = '0 auto';
      stage.style.width = a4RealWidth + 'px';
      if (stageWrapper) {
        stageWrapper.style.direction = 'rtl';
        stageWrapper.style.position = 'relative';
        stageWrapper.style.display = 'inline-block';
        stageWrapper.style.margin = '0 auto';
        stageWrapper.style.width = a4RealWidth + 'px';
        stageWrapper.style.height = 'auto';
        stageWrapper.style.overflow = 'visible';
      }
    }
  }

  // تحديث المعاينة الحية بالكامل
  function updateLivePreview() {
    var widthSelect = getVal('rd_paper_width');
    var customWidthWrap = document.getElementById('rd_custom_width_wrapper');
    var isA4 = (widthSelect === '100%');
    var a4Controls = document.getElementById('rdA4ZoomControls');
    var stageWidth = '78mm';
    var badgeText = '80mm';

    if (widthSelect === 'custom') {
      if (customWidthWrap) customWidthWrap.style.display = 'block';
      var cW = parseFloat(getVal('rd_paper_width_custom')) || 78;
      stageWidth = cW + 'mm';
      badgeText = cW + 'mm';
    } else {
      if (customWidthWrap) customWidthWrap.style.display = 'none';
      if (widthSelect === '58mm') {
        stageWidth = '58mm';
        badgeText = '58mm';
      } else if (isA4) {
        stageWidth = '794px';
        badgeText = 'A4 (210×297 مم)';
      } else {
        stageWidth = '78mm';
        badgeText = '80mm';
      }
    }

    var badge = document.getElementById('rdPreviewWidthBadge');
    if (badge) badge.textContent = badgeText;

    // إظهار أو إخفاء أدوات تحكم A4
    if (a4Controls) {
      a4Controls.style.display = isA4 ? 'inline-flex' : 'none';
    }

    // تبديل فئات النمط (A4 vs Thermal)
    if (isA4) {
      stage.className = 'rd-a4-mode';
      stage.style.width = '794px';
      stage.style.maxWidth = '794px';
      stage.style.minHeight = '1040px';
    } else {
      stage.className = 'rd-thermal-mode';
      stage.style.width = stageWidth;
      stage.style.maxWidth = '380px';
      stage.style.minHeight = 'auto';
    }

    stage.style.border = isChecked('rd_show_borders') ? '1px solid #000' : 'none';
    stage.style.fontFamily = getVal('rd_font_family') || 'Cairo, sans-serif';

    // الخط والهوامش
    var baseFontSize = parseInt(getVal('rd_font_size')) || (isA4 ? 14 : 13);
    content.style.fontSize = baseFontSize + 'px';
    content.style.lineHeight = getVal('rd_line_spacing') || '1.25';

    if (isA4) {
      var mTop = (parseFloat(getVal('rd_margin_top')) || 8);
      var mBottom = (parseFloat(getVal('rd_margin_bottom')) || 8);
      var mRight = (parseFloat(getVal('rd_margin_right')) || 8);
      var mLeft = (parseFloat(getVal('rd_margin_left')) || 8);
      content.style.paddingTop = mTop + 'mm';
      content.style.paddingBottom = mBottom + 'mm';
      content.style.paddingRight = mRight + 'mm';
      content.style.paddingLeft = mLeft + 'mm';
    } else {
      content.style.paddingTop = (parseFloat(getVal('rd_margin_top')) || 2) + 'mm';
      content.style.paddingBottom = (parseFloat(getVal('rd_margin_bottom')) || 4) + 'mm';
      content.style.paddingRight = (parseFloat(getVal('rd_margin_right')) || 2) + 'mm';
      content.style.paddingLeft = (parseFloat(getVal('rd_margin_left')) || 2) + 'mm';
    }

    // هيكل الترويسة العليا (A4 layout vs Thermal layout)
    var topHeaderWrap = document.getElementById('prevTopHeaderWrap');
    var headerSec = document.getElementById('prevHeaderSection');
    var metaSec = document.getElementById('prevMetaSection');
    var invTitle = document.getElementById('prevInvoiceTitle');

    if (topHeaderWrap && headerSec && metaSec) {
      if (isA4) {
        topHeaderWrap.style.display = 'flex';
        topHeaderWrap.style.justifyContent = 'space-between';
        topHeaderWrap.style.alignItems = 'flex-start';
        topHeaderWrap.style.borderBottom = '2px solid #0f172a';
        topHeaderWrap.style.paddingBottom = '14px';
        topHeaderWrap.style.marginBottom = '14px';
        
        headerSec.style.textAlign = 'right';
        headerSec.style.flex = '1';
        headerSec.style.paddingLeft = '20px';

        metaSec.style.textAlign = 'right';
        metaSec.style.flex = '0 0 270px';
        metaSec.style.background = '#f8fafc';
        metaSec.style.border = '1px solid #cbd5e1';
        metaSec.style.borderRadius = '8px';
        metaSec.style.padding = '10px 14px';
        metaSec.style.margin = '0';
        if (invTitle) invTitle.style.display = 'block';
      } else {
        topHeaderWrap.style.display = 'block';
        topHeaderWrap.style.borderBottom = 'none';
        topHeaderWrap.style.paddingBottom = '0';
        topHeaderWrap.style.marginBottom = '0';

        headerSec.style.textAlign = 'center';
        headerSec.style.flex = 'none';
        headerSec.style.paddingLeft = '0';

        metaSec.style.textAlign = 'center';
        metaSec.style.flex = 'none';
        metaSec.style.background = 'transparent';
        metaSec.style.border = 'none';
        metaSec.style.borderRadius = '0';
        metaSec.style.padding = '0';
        metaSec.style.margin = '6px 0';
        if (invTitle) invTitle.style.display = 'none';
      }
    }

    // الشعار والاسم
    var showLogo = isChecked('rd_show_logo');
    var logoWrap = document.getElementById('prevLogoWrapper');
    var logoImg = document.getElementById('prevLogoImg');
    if (logoWrap) logoWrap.style.display = showLogo ? 'block' : 'none';
    if (logoImg) {
      logoImg.style.width = (parseInt(getVal('rd_logo_width')) || (isA4 ? 120 : 90)) + 'px';
      var lAlign = getVal('rd_logo_align') || (isA4 ? 'right' : 'center');
      if (logoWrap) logoWrap.style.textAlign = lAlign;
    }

    var showCompName = isChecked('rd_show_company_name');
    var compNameEl = document.getElementById('prevCompanyName');
    if (compNameEl) {
      compNameEl.style.display = showCompName ? 'block' : 'none';
      compNameEl.style.fontSize = (baseFontSize + (isA4 ? 4 : 3)) + 'px';
    }

    var telEl = document.getElementById('prevCompanyTel');
    if (telEl) telEl.style.display = isChecked('rd_show_company_tel') ? 'block' : 'none';

    var addEl = document.getElementById('prevCompanyAdd');
    if (addEl) addEl.style.display = isChecked('rd_show_company_add') ? 'block' : 'none';

    var taxEl = document.getElementById('prevTaxNo');
    var taxVal = getVal('rd_tax_no').trim();
    if (taxEl) {
      taxEl.style.display = (isChecked('rd_show_tax_no') && taxVal !== '') ? 'block' : 'none';
      var taxSpan = document.getElementById('prevTaxNoVal');
      if (taxSpan) taxSpan.textContent = taxVal;
    }

    var crEl = document.getElementById('prevCrNo');
    var crVal = getVal('rd_cr_no').trim();
    if (crEl) {
      crEl.style.display = (isChecked('rd_show_cr_no') && crVal !== '') ? 'block' : 'none';
      var crSpan = document.getElementById('prevCrNoVal');
      if (crSpan) crSpan.textContent = crVal;
    }

    var headerTextEl = document.getElementById('prevHeaderText');
    var headerText = getVal('rd_header_text').trim();
    if (headerTextEl) {
      headerTextEl.style.display = headerText !== '' ? 'block' : 'none';
      headerTextEl.textContent = headerText;
    }

    // رقم الفاتورة والباركود
    var invNumEl = document.getElementById('prevInvoiceNum');
    var showInvNum = isChecked('rd_show_invoice_num');
    if (invNumEl) {
      invNumEl.style.display = showInvNum ? 'inline-block' : 'none';
      var numStyle = getVal('rd_invoice_num_style');
      if (numStyle === 'badge') {
        invNumEl.style.border = '1px solid #000';
        invNumEl.style.padding = '2px 10px';
        invNumEl.style.fontSize = baseFontSize + 'px';
      } else if (numStyle === 'large') {
        invNumEl.style.border = 'none';
        invNumEl.style.padding = '0';
        invNumEl.style.fontSize = (baseFontSize + 6) + 'px';
      } else {
        invNumEl.style.border = 'none';
        invNumEl.style.padding = '0';
        invNumEl.style.fontSize = baseFontSize + 'px';
      }
    }

    var barcodeWrap = document.getElementById('prevBarcodeWrapper');
    var showBarcode = isChecked('rd_show_invoice_barcode');
    if (barcodeWrap) barcodeWrap.style.display = showBarcode ? 'block' : 'none';
    if (showBarcode && window.JsBarcode) {
      try {
        var bHeight = parseInt(getVal('rd_barcode_height')) || (isA4 ? 32 : 30);
        window.JsBarcode('#prevBarcodeSvg', '1025', {
          format: 'CODE128',
          height: bHeight,
          width: isA4 ? 1.6 : 1.5,
          displayValue: false,
          margin: 0
        });
      } catch (err) {}
    }

    var orderBanner = document.getElementById('prevOrderBanner');
    if (orderBanner) orderBanner.style.display = (isChecked('rd_show_order_type') || isChecked('rd_show_driver')) ? 'block' : 'none';

    var dtEl = document.getElementById('prevDateTime');
    if (dtEl) dtEl.style.display = isChecked('rd_show_datetime') ? 'block' : 'none';

    var cashierEl = document.getElementById('prevCashier');
    if (cashierEl) cashierEl.style.display = isChecked('rd_show_cashier') ? 'block' : 'none';

    // بيانات العميل
    var custSection = document.getElementById('prevCustomerSection');
    var custTitle = document.getElementById('prevCustTitle');
    var showCust = isChecked('rd_show_customer');
    if (custSection) {
      if (isA4) {
        custSection.style.display = showCust ? 'block' : 'none';
        custSection.style.background = '#f8fafc';
        custSection.style.border = '1px solid #cbd5e1';
        custSection.style.borderRadius = '8px';
        custSection.style.padding = '10px 16px';
        custSection.style.margin = '12px 0 16px 0';
        if (custTitle) custTitle.style.display = 'block';
      } else {
        custSection.style.display = showCust ? 'block' : 'none';
        custSection.style.background = 'transparent';
        custSection.style.border = 'none';
        custSection.style.borderTop = '1px dashed #000';
        custSection.style.padding = '4px 0';
        custSection.style.margin = '4px 0';
        custSection.style.borderRadius = '0';
        if (custTitle) custTitle.style.display = 'none';
      }
      var cName = document.getElementById('prevCustName');
      var cPhone = document.getElementById('prevCustPhone');
      var cAdd = document.getElementById('prevCustAddress');
      if (cName) cName.style.display = isChecked('rd_show_customer_name') ? 'block' : 'none';
      if (cPhone) cPhone.style.display = isChecked('rd_show_customer_phone') ? 'block' : 'none';
      if (cAdd) cAdd.style.display = isChecked('rd_show_customer_address') ? 'block' : 'none';
    }

    // بناء جدول الأصناف ديناميكياً
    var colIndex = isChecked('rd_show_col_index');
    var colCode  = isChecked('rd_show_col_code');
    var colName  = isChecked('rd_show_col_name');
    var colQty   = isChecked('rd_show_col_qty');
    var colUnit  = isChecked('rd_show_col_unit');
    var colPrice = isChecked('rd_show_col_price');
    var colDisc  = isChecked('rd_show_col_disc');
    var colTotal = isChecked('rd_show_col_total');
    var showDiscSub = isChecked('rd_show_item_disc_subline');
    var borderStyle = getVal('rd_table_border_style') || 'solid';

    var borderCss = '1px solid #000';
    if (borderStyle === 'dashed') borderCss = '1px dashed #000';
    else if (borderStyle === 'minimal') borderCss = '1px solid #cbd5e1';
    else if (borderStyle === 'none') borderCss = 'none';

    var cellPad = isA4 ? 'padding:8px 10px;' : 'padding:4px 2px;';
    var theadRow = document.getElementById('prevTheadRow');
    if (theadRow) {
      if (isA4) {
        theadRow.style.background = '#f1f5f9';
        theadRow.style.color = '#0f172a';
      } else {
        theadRow.style.background = 'transparent';
        theadRow.style.color = '#000';
      }
      var thHtml = '';
      if (colIndex) thHtml += '<th style="' + cellPad + ' text-align:center; border:' + borderCss + ';' + (isA4 ? ' width:5%;' : '') + '">#</th>';
      if (colCode)  thHtml += '<th style="' + cellPad + ' text-align:center; border:' + borderCss + ';' + (isA4 ? ' width:12%;' : '') + '">كود</th>';
      if (colName)  thHtml += '<th style="' + cellPad + ' text-align:right; border:' + borderCss + ';' + (isA4 ? ' width:35%;' : '') + '">الصنف</th>';
      if (colQty)   thHtml += '<th style="' + cellPad + ' text-align:center; border:' + borderCss + ';' + (isA4 ? ' width:10%;' : '') + '">كمية</th>';
      if (colUnit)  thHtml += '<th style="' + cellPad + ' text-align:center; border:' + borderCss + ';' + (isA4 ? ' width:8%;' : '') + '">وحدة</th>';
      if (colPrice) thHtml += '<th style="' + cellPad + ' text-align:center; border:' + borderCss + ';' + (isA4 ? ' width:11%;' : '') + '">سعر</th>';
      if (colDisc)  thHtml += '<th style="' + cellPad + ' text-align:center; border:' + borderCss + ';' + (isA4 ? ' width:9%;' : '') + '">خصم</th>';
      if (colTotal) thHtml += '<th style="' + cellPad + ' text-align:center; border:' + borderCss + ';' + (isA4 ? ' width:12%;' : '') + '">إجمالي</th>';
      theadRow.innerHTML = thHtml;
    }

    var tbody = document.getElementById('prevTbodyRows');
    if (tbody) {
      var tbHtml = '';
      sampleItems.forEach(function (itm) {
        tbHtml += '<tr style="' + (isA4 ? 'border-bottom:1px solid #e2e8f0;' : '') + '">';
        if (colIndex) tbHtml += '<td style="' + cellPad + ' text-align:center; border:' + borderCss + ';">' + itm.idx + '</td>';
        if (colCode)  tbHtml += '<td style="' + cellPad + ' text-align:center; border:' + borderCss + ';">' + itm.code + '</td>';
        if (colName) {
          tbHtml += '<td style="' + cellPad + ' text-align:right; border:' + borderCss + ';">' + itm.name;
          if (showDiscSub && parseFloat(itm.disc) > 0 && !colDisc) {
            tbHtml += '<div style="font-size:10px; color:#555; font-weight:normal;">(خصم: ' + itm.disc + ')</div>';
          }
          tbHtml += '</td>';
        }
        if (colQty)   tbHtml += '<td style="' + cellPad + ' text-align:center; border:' + borderCss + ';">' + itm.qty + '</td>';
        if (colUnit)  tbHtml += '<td style="' + cellPad + ' text-align:center; border:' + borderCss + ';">' + itm.unit + '</td>';
        if (colPrice) tbHtml += '<td style="' + cellPad + ' text-align:center; border:' + borderCss + ';">' + itm.price + '</td>';
        if (colDisc)  tbHtml += '<td style="' + cellPad + ' text-align:center; border:' + borderCss + '; color:#c00;">' + (parseFloat(itm.disc) > 0 ? itm.disc : '-') + '</td>';
        if (colTotal) tbHtml += '<td style="' + cellPad + ' text-align:center; border:' + borderCss + ';">' + itm.total + '</td>';
        tbHtml += '</tr>';
      });
      tbody.innerHTML = tbHtml;
    }

    // قسم الإجماليات (A4 vs Thermal)
    var totalsTable = document.querySelector('#prevTotalsSection table');
    if (totalsTable) {
      if (isA4) {
        totalsTable.style.width = '320px';
        totalsTable.style.marginRight = 'auto'; // في RTL يتم وضعه يسار الورقة
        totalsTable.style.marginLeft = '0';
        totalsTable.style.border = '1px solid #cbd5e1';
        totalsTable.style.background = '#f8fafc';
        totalsTable.style.borderRadius = '8px';
        totalsTable.style.overflow = 'hidden';
      } else {
        totalsTable.style.width = '100%';
        totalsTable.style.marginRight = '0';
        totalsTable.style.marginLeft = '0';
        totalsTable.style.border = 'none';
        totalsTable.style.background = 'transparent';
        totalsTable.style.borderRadius = '0';
      }
    }

    document.querySelectorAll('#prevTotalsSection td').forEach(function(td) {
      td.style.padding = isA4 ? '6px 12px' : '3px 0';
    });

    var rowItemsTotal = document.getElementById('prevRowItemsTotal');
    if (rowItemsTotal) rowItemsTotal.style.display = isChecked('rd_show_items_total') ? 'table-row' : 'none';

    var rowItemsDisc = document.getElementById('prevRowItemsDisc');
    if (rowItemsDisc) rowItemsDisc.style.display = isChecked('rd_show_items_disc') ? 'table-row' : 'none';

    var rowSubtotal = document.getElementById('prevRowSubtotal');
    if (rowSubtotal) rowSubtotal.style.display = isChecked('rd_show_subtotal') ? 'table-row' : 'none';

    var rowInvDisc = document.getElementById('prevRowInvDisc');
    if (rowInvDisc) rowInvDisc.style.display = isChecked('rd_show_inv_disc') ? 'table-row' : 'none';

    var rowExtra = document.getElementById('prevRowExtra');
    if (rowExtra) rowExtra.style.display = isChecked('rd_show_extra') ? 'table-row' : 'none';

    var rowNet = document.getElementById('prevRowNet');
    if (rowNet) {
      rowNet.style.display = isChecked('rd_show_net') ? 'table-row' : 'none';
      if (isA4) {
        rowNet.style.background = '#e2e8f0';
        rowNet.style.borderTop = '2px solid #334155';
        rowNet.style.borderBottom = 'none';
      } else {
        rowNet.style.background = 'transparent';
        rowNet.style.borderTop = 'none';
        rowNet.style.borderBottom = '2px solid #000';
      }
      rowNet.style.fontSize = isChecked('rd_highlight_net') ? (baseFontSize + (isA4 ? 4 : 3)) + 'px' : baseFontSize + 'px';
    }

    var rowPaid = document.getElementById('prevRowPaid');
    if (rowPaid) rowPaid.style.display = isChecked('rd_show_paid') ? 'table-row' : 'none';

    var rowChange = document.getElementById('prevRowChange');
    if (rowChange) rowChange.style.display = isChecked('rd_show_change') ? 'table-row' : 'none';

    // التذييل
    var footerTextEl = document.getElementById('prevFooterText');
    var showFooterText = isChecked('rd_show_footer_text');
    var footerTextVal = getVal('rd_footer_text').trim();
    if (footerTextEl) {
      footerTextEl.style.display = (showFooterText && footerTextVal !== '') ? 'block' : 'none';
      footerTextEl.textContent = footerTextVal;
    }

    var notesTextEl = document.getElementById('prevNotesText');
    var showNotes = isChecked('rd_show_notes');
    var notesTextVal = getVal('rd_notes_text').trim();
    if (notesTextEl) {
      notesTextEl.style.display = (showNotes && notesTextVal !== '') ? 'block' : 'none';
      notesTextEl.textContent = notesTextVal;
    }

    // تطبيق مقياس العرض لـ A4
    applyA4Zoom();
  }

  // الاستماع لجميع التغييرات على الحقول
  document.querySelectorAll('.rd-live').forEach(function (input) {
    input.addEventListener('input', updateLivePreview);
    input.addEventListener('change', updateLivePreview);
  });

  // تبديل أوضاع تكبير A4
  document.querySelectorAll('.rd-a4-zoom-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
      document.querySelectorAll('.rd-a4-zoom-btn').forEach(function (b) { b.classList.remove('active'); });
      btn.classList.add('active');
      currentA4Zoom = btn.getAttribute('data-zoom');
      applyA4Zoom();
    });
  });

  // فتح نافذة المعاينة المكبّرة لـ A4
  var btnA4Modal = document.getElementById('btnOpenA4Modal');
  if (btnA4Modal) {
    btnA4Modal.addEventListener('click', function () {
      var modalContainer = document.getElementById('rdModalA4Container');
      if (modalContainer && stage) {
        var clone = stage.cloneNode(true);
        clone.id = 'rdReceiptStageModalClone';
        clone.style.transform = 'none';
        clone.style.margin = '0 auto';
        clone.style.boxShadow = 'none';
        clone.style.position = 'relative';
        clone.style.top = 'auto';
        clone.style.left = 'auto';
        clone.style.width = '794px';
        modalContainer.innerHTML = '';
        modalContainer.appendChild(clone);
      }
      if (window.jQuery && $('#rdA4Modal').length) {
        $('#rdA4Modal').modal('show');
      }
    });
  }

  // طباعة تجريبية من داخل نافذة المعاينة
  var btnModalPrint = document.getElementById('btnModalPrintTest');
  if (btnModalPrint) {
    btnModalPrint.addEventListener('click', function () {
      var btnMainPrint = document.getElementById('btnTestPrintReceipt');
      if (btnMainPrint) btnMainPrint.click();
    });
  }

  // إعادة ضبط مقياس العرض عند تغيير حجم النافذة
  var resizeDebounceTimer = null;
  window.addEventListener('resize', function () {
    clearTimeout(resizeDebounceTimer);
    resizeDebounceTimer = setTimeout(function () {
      if (getVal('rd_paper_width') === '100%') {
        applyA4Zoom();
      }
    }, 100);
  });

  // مزامنة عند فتح تبويب الطباعة في صفحة الإعدادات
  if (window.jQuery) {
    $('a[href="#tab-print"], #print-tab').on('shown.bs.tab', function () {
      setTimeout(function () {
        updateLivePreview();
      }, 50);
    });
  }

  // إدارة الأكورديون ومزامنة الأسهم دون أي تعارض
  if (window.jQuery) {
    // فتح جميع الأقسام
    $('#rdBtnExpandAll').on('click', function (e) {
      e.preventDefault();
      $('#receiptDesignAccordion .collapse').collapse('show');
    });

    // طي جميع الأقسام
    $('#rdBtnCollapseAll').on('click', function (e) {
      e.preventDefault();
      $('#receiptDesignAccordion .collapse').collapse('hide');
    });

    // مزامنة حالة السهم عند الفتح والطي مع أحداث Bootstrap
    $('#receiptDesignAccordion').on('show.bs.collapse', function (e) {
      $(e.target).prev('.card-header').removeClass('collapsed').attr('aria-expanded', 'true');
    });

    $('#receiptDesignAccordion').on('hide.bs.collapse', function (e) {
      $(e.target).prev('.card-header').addClass('collapsed').attr('aria-expanded', 'false');
    });
  }

  // تطبيق القوالب الجاهزة (Presets)
  document.querySelectorAll('.rd-preset-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
      document.querySelectorAll('.rd-preset-btn').forEach(function (b) { b.classList.remove('active'); });
      btn.classList.add('active');

      var preset = btn.getAttribute('data-preset');
      if (preset === '80mm') {
        setVal('rd_paper_width', '78mm');
        setVal('rd_font_size', 13);
        setChecked('rd_show_borders', true);
        setVal('rd_logo_width', 90);
        setVal('rd_logo_align', 'center');
        setVal('rd_margin_top', 2);
        setVal('rd_margin_bottom', 4);
        setVal('rd_margin_right', 2);
        setVal('rd_margin_left', 2);
        setChecked('rd_show_logo', true);
        setChecked('rd_show_company_name', true);
        setChecked('rd_show_invoice_num', true);
        setVal('rd_invoice_num_style', 'badge');
        setChecked('rd_show_invoice_barcode', true);
        setChecked('rd_show_datetime', true);
        setChecked('rd_show_cashier', true);
        setChecked('rd_show_order_type', true);
        setChecked('rd_show_driver', true);
        setChecked('rd_show_customer', true);
        setChecked('rd_show_col_index', false);
        setChecked('rd_show_col_code', false);
        setChecked('rd_show_col_name', true);
        setChecked('rd_show_col_qty', true);
        setChecked('rd_show_col_unit', false);
        setChecked('rd_show_col_price', true);
        setChecked('rd_show_col_disc', false);
        setChecked('rd_show_col_total', true);
        setChecked('rd_show_item_disc_subline', true);
        setVal('rd_table_border_style', 'solid');
        setChecked('rd_show_items_total', true);
        setChecked('rd_show_subtotal', true);
        setChecked('rd_show_net', true);
        setChecked('rd_show_paid', true);
        setChecked('rd_show_change', true);
      } else if (preset === '58mm') {
        setVal('rd_paper_width', '58mm');
        setVal('rd_font_size', 11);
        setChecked('rd_show_borders', false);
        setVal('rd_logo_width', 60);
        setVal('rd_logo_align', 'center');
        setVal('rd_margin_top', 1);
        setVal('rd_margin_bottom', 2);
        setVal('rd_margin_right', 1);
        setVal('rd_margin_left', 1);
        setChecked('rd_show_logo', true);
        setChecked('rd_show_company_name', true);
        setChecked('rd_show_invoice_num', true);
        setVal('rd_invoice_num_style', 'plain');
        setChecked('rd_show_invoice_barcode', false);
        setChecked('rd_show_datetime', true);
        setChecked('rd_show_cashier', true);
        setChecked('rd_show_order_type', true);
        setChecked('rd_show_driver', false);
        setChecked('rd_show_customer', true);
        setChecked('rd_show_col_index', false);
        setChecked('rd_show_col_code', false);
        setChecked('rd_show_col_name', true);
        setChecked('rd_show_col_qty', true);
        setChecked('rd_show_col_unit', false);
        setChecked('rd_show_col_price', false);
        setChecked('rd_show_col_disc', false);
        setChecked('rd_show_col_total', true);
        setChecked('rd_show_item_disc_subline', false);
        setVal('rd_table_border_style', 'dashed');
        setChecked('rd_show_items_total', false);
        setChecked('rd_show_subtotal', true);
        setChecked('rd_show_net', true);
        setChecked('rd_show_paid', true);
        setChecked('rd_show_change', true);
      } else if (preset === 'a4') {
        setVal('rd_paper_width', '100%');
        setVal('rd_font_size', 14);
        setChecked('rd_show_borders', true);
        setVal('rd_logo_width', 130);
        setVal('rd_logo_align', 'right');
        setVal('rd_margin_top', 8);
        setVal('rd_margin_bottom', 8);
        setVal('rd_margin_right', 8);
        setVal('rd_margin_left', 8);
        setChecked('rd_show_logo', true);
        setChecked('rd_show_company_name', true);
        setChecked('rd_show_company_tel', true);
        setChecked('rd_show_company_add', true);
        setChecked('rd_show_tax_no', true);
        setChecked('rd_show_cr_no', true);
        setChecked('rd_show_invoice_num', true);
        setVal('rd_invoice_num_style', 'badge');
        setChecked('rd_show_invoice_barcode', true);
        setChecked('rd_show_datetime', true);
        setChecked('rd_show_cashier', true);
        setChecked('rd_show_customer', true);
        setChecked('rd_show_customer_name', true);
        setChecked('rd_show_customer_phone', true);
        setChecked('rd_show_customer_address', true);
        setChecked('rd_show_col_index', true);
        setChecked('rd_show_col_code', true);
        setChecked('rd_show_col_name', true);
        setChecked('rd_show_col_qty', true);
        setChecked('rd_show_col_unit', true);
        setChecked('rd_show_col_price', true);
        setChecked('rd_show_col_disc', true);
        setChecked('rd_show_col_total', true);
        setVal('rd_table_border_style', 'solid');
        setChecked('rd_show_items_total', true);
        setChecked('rd_show_subtotal', true);
        setChecked('rd_show_net', true);
        setChecked('rd_show_paid', true);
        setChecked('rd_show_change', true);
      } else if (preset === 'restaurant') {
        setVal('rd_paper_width', '78mm');
        setVal('rd_font_size', 14);
        setChecked('rd_show_borders', true);
        setVal('rd_logo_width', 90);
        setVal('rd_logo_align', 'center');
        setVal('rd_margin_top', 2);
        setVal('rd_margin_bottom', 4);
        setVal('rd_margin_right', 2);
        setVal('rd_margin_left', 2);
        setChecked('rd_show_order_type', true);
        setChecked('rd_show_driver', true);
        setChecked('rd_show_customer', true);
        setChecked('rd_show_col_name', true);
        setChecked('rd_show_col_qty', true);
        setChecked('rd_show_col_price', true);
        setChecked('rd_show_col_total', true);
        setChecked('rd_show_item_disc_subline', true);
        setChecked('rd_show_net', true);
        setChecked('rd_show_paid', true);
        setChecked('rd_show_change', true);
      } else if (preset === 'minimal') {
        setVal('rd_paper_width', '78mm');
        setVal('rd_font_size', 12);
        setChecked('rd_show_borders', false);
        setVal('rd_logo_width', 60);
        setVal('rd_logo_align', 'center');
        setVal('rd_margin_top', 1);
        setVal('rd_margin_bottom', 2);
        setVal('rd_margin_right', 1);
        setVal('rd_margin_left', 1);
        setChecked('rd_show_logo', false);
        setChecked('rd_show_company_name', true);
        setChecked('rd_show_company_tel', false);
        setChecked('rd_show_company_add', false);
        setChecked('rd_show_invoice_barcode', false);
        setChecked('rd_show_order_type', false);
        setChecked('rd_show_driver', false);
        setChecked('rd_show_customer', false);
        setChecked('rd_show_col_index', false);
        setChecked('rd_show_col_code', false);
        setChecked('rd_show_col_name', true);
        setChecked('rd_show_col_qty', true);
        setChecked('rd_show_col_unit', false);
        setChecked('rd_show_col_price', true);
        setChecked('rd_show_col_disc', false);
        setChecked('rd_show_col_total', true);
        setVal('rd_table_border_style', 'minimal');
        setChecked('rd_show_items_total', false);
        setChecked('rd_show_subtotal', true);
        setChecked('rd_show_net', true);
        setChecked('rd_show_paid', false);
        setChecked('rd_show_change', false);
      }
      updateLivePreview();
    });
  });

  // تجربة طباعة المعاينة (Test Print)
  var btnTestPrint = document.getElementById('btnTestPrintReceipt');
  if (btnTestPrint) {
    btnTestPrint.addEventListener('click', function () {
      var isA4Print = (getVal('rd_paper_width') === '100%');
      var printWindow = window.open('', '_blank', isA4Print ? 'width=850,height=900' : 'width=450,height=650');
      if (!printWindow) {
        alert('يرجى السماح بالنوافذ المنبثقة لإجراء معاينة الطباعة');
        return;
      }
      var clone = stage.cloneNode(true);
      clone.style.transform = 'none';
      clone.style.margin = '0 auto';
      clone.style.boxShadow = 'none';
      var cloneHtml = clone.outerHTML;
      var fontFam = getVal('rd_font_family') || 'Cairo, sans-serif';
      var doc = printWindow.document;
      doc.open();
      doc.write('<!DOCTYPE html><html lang="ar" dir="rtl"><head><meta charset="utf-8">');
      doc.write('<title>معاينة طباعة تجريبية</title>');
      doc.write('<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap">');
      doc.write('<style>');
      doc.write('@page { size: ' + (isA4Print ? 'A4' : 'auto') + '; margin: 0; }');
      doc.write('body { margin: 0; padding: ' + (isA4Print ? '20px' : '10px') + '; background: #fff; font-family: ' + fontFam + '; direction: rtl; }');
      doc.write('@media print { body { padding: 0; } }');
      doc.write('</style></head><body>');
      doc.write(cloneHtml);
      doc.write('<script>window.onload = function() { setTimeout(function(){ window.print(); }, 250); };<\/script>');
      doc.write('</body></html>');
      doc.close();
    });
  }

  // استعادة الافتراضي
  var btnReset = document.getElementById('btnResetReceiptDefaults');
  if (btnReset) {
    btnReset.addEventListener('click', function () {
      if (!confirm('هل تريد استعادة إعدادات الطباعة الافتراضية؟')) return;
      var defaultBtn = document.querySelector('.rd-preset-btn[data-preset="80mm"]');
      if (defaultBtn) defaultBtn.click();
    });
  }

  // تهيئة المعاينة الأولية
  updateLivePreview();
});
</script>
