<?php
require_once __DIR__ . '/barcode_design.php';
$bd = kody_barcode_design_load($rowstg ?? []);
$bdElements = kody_barcode_element_defs();
$bdPriceSources = [
    'printed' => 'السعر الظاهر في شاشة الطباعة',
    'price1' => 'سعر 1',
    'price2' => 'سعر 2',
    'price3' => 'سعر 3',
];
?>
<div class="card card-outline card-secondary shadow-sm border-0" style="border-radius: 12px;">
  <div class="card-header bg-white py-3">
    <h3 class="card-title text-dark font-weight-bold mb-0"><i class="fas fa-barcode ml-2"></i> تصميم ملصق الباركود</h3>
  </div>
  <div class="card-body">
    <p class="text-muted small mb-3">يُحفظ التصميم كاملًا كـ JSON داخل الإعدادات، وتستخدمه طباعة الباركود مباشرة.</p>
    <div class="row">
      <div class="col-xl-8">
        <h5 class="text-muted mb-3">معلومات الشركة</h5>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="bd_company_name">اسم الشركة</label>
              <input type="text" class="form-control bd-live" id="bd_company_name" name="bd_company_name" maxlength="200"
                     value="<?= htmlspecialchars($bd['company_name'], ENT_QUOTES, 'UTF-8') ?>">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="bd_align">محاذاة النص</label>
              <select class="form-control bd-live" id="bd_align" name="bd_align">
                <option value="center" <?= $bd['align'] === 'center' ? 'selected' : '' ?>>وسط</option>
                <option value="right" <?= $bd['align'] === 'right' ? 'selected' : '' ?>>يمين</option>
                <option value="left" <?= $bd['align'] === 'left' ? 'selected' : '' ?>>يسار</option>
              </select>
            </div>
          </div>
          <div class="col-md-6">
            <div class="custom-control custom-switch mb-3">
              <input type="checkbox" class="custom-control-input bd-live" id="bd_invert" name="bd_invert" value="1" <?= !empty($bd['invert']) ? 'checked' : '' ?>>
              <label class="custom-control-label" for="bd_invert">عكس الألوان (خلفية سوداء، نص أبيض)</label>
            </div>
          </div>
          <div class="col-md-6">
            <div class="custom-control custom-switch mb-3">
              <input type="checkbox" class="custom-control-input bd-live" id="bd_enabled" name="bd_enabled" value="1" <?= !empty($bd['enabled']) ? 'checked' : '' ?>>
              <label class="custom-control-label" for="bd_enabled">تفعيل الإعداد</label>
            </div>
          </div>
        </div>

        <h5 class="text-muted mb-3">حجم الورقة</h5>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="bd_paper_width">عرض الورقة (مم)</label>
              <input type="number" class="form-control bd-live" id="bd_paper_width" name="bd_paper_width" min="10" max="200" step="0.1"
                     value="<?= htmlspecialchars(kody_bd_fmt($bd['paper_width']), ENT_QUOTES, 'UTF-8') ?>">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="bd_paper_height">ارتفاع الورقة (مم)</label>
              <input type="number" class="form-control bd-live" id="bd_paper_height" name="bd_paper_height" min="10" max="300" step="0.1"
                     value="<?= htmlspecialchars(kody_bd_fmt($bd['paper_height']), ENT_QUOTES, 'UTF-8') ?>">
            </div>
          </div>
        </div>

        <h5 class="text-muted mb-3">الهوامش</h5>
        <div class="row">
          <div class="col-6 col-md-3">
            <div class="form-group">
              <label for="bd_margin_top">الهامش العلوي (مم)</label>
              <input type="number" class="form-control bd-live" id="bd_margin_top" name="bd_margin_top" min="0" max="40" step="0.1"
                     value="<?= htmlspecialchars(kody_bd_fmt($bd['margin_top']), ENT_QUOTES, 'UTF-8') ?>">
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="form-group">
              <label for="bd_margin_bottom">الهامش السفلي (مم)</label>
              <input type="number" class="form-control bd-live" id="bd_margin_bottom" name="bd_margin_bottom" min="0" max="40" step="0.1"
                     value="<?= htmlspecialchars(kody_bd_fmt($bd['margin_bottom']), ENT_QUOTES, 'UTF-8') ?>">
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="form-group">
              <label for="bd_margin_left">الهامش الأيسر (مم)</label>
              <input type="number" class="form-control bd-live" id="bd_margin_left" name="bd_margin_left" min="0" max="40" step="0.1"
                     value="<?= htmlspecialchars(kody_bd_fmt($bd['margin_left']), ENT_QUOTES, 'UTF-8') ?>">
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="form-group">
              <label for="bd_margin_right">الهامش الأيمن (مم)</label>
              <input type="number" class="form-control bd-live" id="bd_margin_right" name="bd_margin_right" min="0" max="40" step="0.1"
                     value="<?= htmlspecialchars(kody_bd_fmt($bd['margin_right']), ENT_QUOTES, 'UTF-8') ?>">
            </div>
          </div>
        </div>

        <h5 class="text-muted mb-2">العناصر</h5>
        <p class="small text-muted">اضبط إظهار كل عنصر وموضعه وحجمه داخل منطقة المحتوى (مم من داخل الهوامش).</p>
        <div class="table-responsive mb-4">
          <table class="table table-sm table-bordered mb-0">
            <thead class="thead-light">
              <tr>
                <th>العنصر</th>
                <th>إظهار</th>
                <th>الارتفاع</th>
                <th>العرض</th>
                <th>أعلى</th>
                <th>يسار</th>
                <th>حجم الخط</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($bdElements as $key => $meta):
                $el = $bd['elements'][$key];
              ?>
              <tr>
                <td class="align-middle"><?= htmlspecialchars($meta['label'], ENT_QUOTES, 'UTF-8') ?></td>
                <td class="text-center align-middle">
                  <input type="checkbox" class="bd-live" name="bd_el[<?= $key ?>][show]" value="1" <?= !empty($el['show']) ? 'checked' : '' ?>>
                </td>
                <td><input type="number" class="form-control form-control-sm bd-live" name="bd_el[<?= $key ?>][h]" min="0" max="200" step="0.1" value="<?= htmlspecialchars(kody_bd_fmt($el['h']), ENT_QUOTES, 'UTF-8') ?>"></td>
                <td><input type="number" class="form-control form-control-sm bd-live" name="bd_el[<?= $key ?>][w]" min="0" max="200" step="0.1" value="<?= htmlspecialchars(kody_bd_fmt($el['w']), ENT_QUOTES, 'UTF-8') ?>"></td>
                <td><input type="number" class="form-control form-control-sm bd-live" name="bd_el[<?= $key ?>][top]" min="0" max="300" step="0.1" value="<?= htmlspecialchars(kody_bd_fmt($el['top']), ENT_QUOTES, 'UTF-8') ?>"></td>
                <td><input type="number" class="form-control form-control-sm bd-live" name="bd_el[<?= $key ?>][left]" min="0" max="200" step="0.1" value="<?= htmlspecialchars(kody_bd_fmt($el['left']), ENT_QUOTES, 'UTF-8') ?>"></td>
                <td>
                  <?php if ($meta['font']): ?>
                    <input type="number" class="form-control form-control-sm bd-live" name="bd_el[<?= $key ?>][font]" min="1" max="72" step="1" value="<?= htmlspecialchars(kody_bd_fmt($el['font']), ENT_QUOTES, 'UTF-8') ?>">
                  <?php else: ?>
                    <span class="text-muted">—</span>
                  <?php endif; ?>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <h5 class="text-muted mb-2">باركود الوحدة الثانية</h5>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="bd_unit_barcode_add">الرقم الثابت</label>
              <input type="text" class="form-control" id="bd_unit_barcode_add" name="bd_unit_barcode_add" maxlength="10" inputmode="numeric"
                     value="<?= htmlspecialchars((string) ($bd['unit_barcode_add'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="مثال: 1000000">
              <small class="form-text text-muted">باركود الوحدة الثانية = باركود الوحدة الأولى + هذا الرقم. مثال: 10025 + 1000000 = 1010025.</small>
            </div>
          </div>
        </div>

        <h5 class="text-muted mb-2">تركيب الشفرة</h5>
        <p class="small text-muted mb-3">يُطبَّق على الشفرة فقط. الباركود الخطي والباركود النصي يبقيان على الباركود الأساسي للصنف. الشفرة = بداية + الباركود (اختياري) + كود الصنف + السعر كعدد صحيح + نهاية.</p>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="bd_code_prefix">بداية الشفرة</label>
              <input type="text" class="form-control bd-live" id="bd_code_prefix" name="bd_code_prefix" maxlength="40"
                     value="<?= htmlspecialchars($bd['code_prefix'], ENT_QUOTES, 'UTF-8') ?>" placeholder="نص يُضاف قبل الشفرة (اختياري)">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="bd_code_suffix">نهاية الشفرة</label>
              <input type="text" class="form-control bd-live" id="bd_code_suffix" name="bd_code_suffix" maxlength="40"
                     value="<?= htmlspecialchars($bd['code_suffix'], ENT_QUOTES, 'UTF-8') ?>" placeholder="نص يُضاف بعد الشفرة (اختياري)">
            </div>
          </div>
          <div class="col-md-6">
            <div class="custom-control custom-switch mb-3">
              <input type="checkbox" class="custom-control-input bd-live" id="bd_include_barcode" name="bd_include_barcode" value="1" <?= !empty($bd['include_barcode']) ? 'checked' : '' ?>>
              <label class="custom-control-label" for="bd_include_barcode">إدراج الباركود داخل الشفرة</label>
            </div>
          </div>
          <div class="col-md-6">
            <div class="custom-control custom-switch mb-3">
              <input type="checkbox" class="custom-control-input bd-live" id="bd_include_item_code" name="bd_include_item_code" value="1" <?= !empty($bd['include_item_code']) ? 'checked' : '' ?>>
              <label class="custom-control-label" for="bd_include_item_code">إدراج كود الصنف داخل الشفرة</label>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="bd_embed_prices">سعر مدمج في الشفرة</label>
              <select class="form-control bd-live" id="bd_embed_prices" name="bd_embed_prices">
                <option value="none" <?= $bd['embed_prices'] === 'none' ? 'selected' : '' ?>>بدون سعر</option>
                <option value="after" <?= $bd['embed_prices'] === 'after' ? 'selected' : '' ?>>سعر واحد</option>
                <option value="after_before" <?= $bd['embed_prices'] === 'after_before' ? 'selected' : '' ?>>سعرين</option>
              </select>
              <small class="form-text text-muted">السعر بدون رقم عشري: 12.50 تصبح 13.</small>
            </div>
          </div>
        </div>

        <h5 class="text-muted mb-3">مصادر الأسعار</h5>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="bd_price_before_source">السعر قبل الخصم</label>
              <select class="form-control bd-live" id="bd_price_before_source" name="bd_price_before_source">
                <?php foreach ($bdPriceSources as $src => $label): ?>
                  <option value="<?= $src ?>" <?= $bd['price_before_source'] === $src ? 'selected' : '' ?>><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="bd_price_after_source">السعر بعد الخصم</label>
              <select class="form-control bd-live" id="bd_price_after_source" name="bd_price_after_source">
                <?php foreach ($bdPriceSources as $src => $label): ?>
                  <option value="<?= $src ?>" <?= $bd['price_after_source'] === $src ? 'selected' : '' ?>><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="col-12">
            <div class="custom-control custom-switch mb-2">
              <input type="checkbox" class="custom-control-input bd-live" id="bd_strike_before" name="bd_strike_before" value="1" <?= !empty($bd['strike_before']) ? 'checked' : '' ?>>
              <label class="custom-control-label" for="bd_strike_before">شطب السعر قبل الخصم</label>
            </div>
          </div>
        </div>
        <p class="small mb-0">مثال الشفرة: <code id="bdCodeSample"></code></p>
      </div>
      <div class="col-xl-4 mt-4 mt-xl-0">
        <h5 class="text-muted mb-3">معاينة</h5>
        <div id="bdStage" style="background:#edf2f7;border-radius:12px;padding:16px 12px;overflow:auto;display:flex;justify-content:center;align-items:flex-start;min-height:220px;">
          <div id="bdPreviewScale">
            <div id="bdPreviewLabel"></div>
          </div>
        </div>
        <p class="small text-muted mt-2 mb-0">المعاينة بصنف تجريبي. الطباعة تستخدم بيانات الصنف الفعلية.</p>
      </div>
    </div>
  </div>
</div>
<script src="print/code.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  var root = document.getElementById('bdPreviewLabel');
  if (!root) return;

  var sample = {
    item: 'صنف تجريبي',
    barcode: '6221234567890',
    code: '10025',
    price1: 150,
    price2: 120,
    price3: 99,
    printed: 120
  };

  function val(name) {
    var el = document.querySelector('[name="' + name + '"]');
    return el ? el.value : '';
  }
  function checked(name) {
    var el = document.querySelector('[name="' + name + '"]');
    return !!(el && el.checked);
  }
  function num(name, fallback) {
    var n = parseFloat(val(name));
    return isNaN(n) ? fallback : n;
  }
  function encodePrice(amount) {
    var n = parseFloat(amount);
    if (isNaN(n)) n = 0;
    return String(Math.round(n));
  }
  function pick(source) {
    if (Object.prototype.hasOwnProperty.call(sample, source)) return sample[source];
    return '';
  }
  function money(amount) {
    var n = parseFloat(amount);
    if (isNaN(n)) return '';
    return String(Math.round(n));
  }
  function compose() {
    var body = (checked('bd_include_barcode') ? sample.barcode : '') + (checked('bd_include_item_code') ? sample.code : '');
    var mode = val('bd_embed_prices');
    var before = pick(val('bd_price_before_source'));
    var after = pick(val('bd_price_after_source'));
    var encoded = '';
    if (mode === 'before') encoded = encodePrice(before);
    else if (mode === 'after') encoded = encodePrice(after);
    else if (mode === 'before_after') encoded = encodePrice(before) + encodePrice(after);
    else if (mode === 'after_before') encoded = encodePrice(after) + encodePrice(before);
    return val('bd_code_prefix') + body + encoded + val('bd_code_suffix');
  }
  function elBox(key) {
    return {
      show: checked('bd_el[' + key + '][show]'),
      h: num('bd_el[' + key + '][h]', 0),
      w: num('bd_el[' + key + '][w]', 0),
      top: num('bd_el[' + key + '][top]', 0),
      left: num('bd_el[' + key + '][left]', 0),
      font: num('bd_el[' + key + '][font]', 8)
    };
  }
  function textStyle(box, extra) {
    var align = val('bd_align') || 'center';
    return 'position:absolute;overflow:hidden;box-sizing:border-box;display:flex;align-items:center;justify-content:' +
      (align === 'left' ? 'flex-start' : (align === 'right' ? 'flex-end' : 'center')) +
      ';text-align:' + align + ';direction:rtl;line-height:1;white-space:nowrap;' +
      'top:' + box.top + 'mm;left:' + box.left + 'mm;width:' + box.w + 'mm;height:' + box.h + 'mm;font-size:' + box.font + 'pt;' +
      (extra || '');
  }

  function render() {
    var w = num('bd_paper_width', 25);
    var h = num('bd_paper_height', 38);
    var mt = num('bd_margin_top', 0);
    var mr = num('bd_margin_right', 0);
    var mb = num('bd_margin_bottom', 0);
    var ml = num('bd_margin_left', 0);
    var before = money(pick(val('bd_price_before_source')));
    var after = money(pick(val('bd_price_after_source')));
    var codeText = compose();
    var sampleEl = document.getElementById('bdCodeSample');
    if (sampleEl) sampleEl.textContent = codeText || '—';

    var html = '';
    html += '<div style="position:relative;background:#fff;width:' + w + 'mm;height:' + h + 'mm;box-shadow:0 1px 4px rgba(0,0,0,.15);">';
    html += '<div style="position:absolute;top:' + mt + 'mm;right:' + mr + 'mm;bottom:' + mb + 'mm;left:' + ml + 'mm;outline:1px dashed #cbd5e0;">';

    var company = elBox('company');
    if (company.show) {
      var invert = checked('bd_invert');
      html += '<div style="' + textStyle(company, invert ? 'background:#000;color:#fff;' : 'color:#000;') + '">' +
        escapeHtml(val('bd_company_name')) + '</div>';
    }
    var item = elBox('item_name');
    if (item.show) html += '<div style="' + textStyle(item, 'color:#000;') + '">' + escapeHtml(sample.item) + '</div>';
    var linear = elBox('barcode_linear');
    if (linear.show) {
      html += '<div style="position:absolute;overflow:hidden;top:' + linear.top + 'mm;left:' + linear.left + 'mm;width:' + linear.w + 'mm;height:' + linear.h + 'mm;">' +
        '<svg id="bdPreviewBarcode" style="width:100%;height:100%;"></svg></div>';
    }
    var btext = elBox('barcode_text');
    if (btext.show) html += '<div style="' + textStyle(btext, 'color:#000;') + '">' + escapeHtml(sample.barcode) + '</div>';
    var code = elBox('code');
    if (code.show) html += '<div style="' + textStyle(code, 'color:#000;') + '">' + escapeHtml(codeText) + '</div>';
    var pb = elBox('price_before');
    if (pb.show) html += '<div style="' + textStyle(pb, 'color:#000;' + (checked('bd_strike_before') ? 'text-decoration:line-through;' : '')) + '">' + escapeHtml(before) + '</div>';
    var pa = elBox('price_after');
    if (pa.show) html += '<div style="' + textStyle(pa, 'color:#000;font-weight:700;') + '">' + escapeHtml(after) + '</div>';
    var pr = elBox('price_retail');
    if (pr.show) html += '<div style="' + textStyle(pr, 'color:#000;') + '">قطاعي: ' + escapeHtml(money(sample.price1)) + '</div>';
    var pw = elBox('price_wholesale');
    if (pw.show) html += '<div style="' + textStyle(pw, 'color:#000;') + '">جملة: ' + escapeHtml(money(sample.price2)) + '</div>';

    html += '</div></div>';
    root.innerHTML = html;
    fitPreview(w, h);

    var svg = document.getElementById('bdPreviewBarcode');
    if (svg && window.JsBarcode) {
      try {
        JsBarcode(svg, sample.barcode, {
          format: 'CODE128',
          displayValue: false,
          margin: 0,
          background: 'transparent',
          lineColor: '#000',
          width: 1,
          height: 40
        });
        svg.setAttribute('preserveAspectRatio', 'none');
        svg.style.width = '100%';
        svg.style.height = '100%';
      } catch (e) {}
    }
  }

  function escapeHtml(s) {
    return String(s == null ? '' : s)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
  }

  function fitPreview(paperW, paperH) {
    var scaleWrap = document.getElementById('bdPreviewScale');
    if (!scaleWrap) return;
    var scale = 2.2;
    scaleWrap.style.width = paperW + 'mm';
    if ('zoom' in scaleWrap.style) {
      scaleWrap.style.zoom = String(scale);
      root.style.transform = '';
      root.style.width = '';
      root.style.margin = '';
      return;
    }
    var mm = 96 / 25.4;
    scaleWrap.style.width = Math.ceil(paperW * mm * scale) + 'px';
    scaleWrap.style.height = Math.ceil(paperH * mm * scale) + 'px';
    root.style.width = paperW + 'mm';
    root.style.margin = '0 auto';
    root.style.transform = 'scale(' + scale + ')';
    root.style.transformOrigin = 'top center';
  }

  document.querySelectorAll('.bd-live').forEach(function (el) {
    el.addEventListener('input', render);
    el.addEventListener('change', render);
  });
  if (window.jQuery) {
    jQuery(document).on('shown.bs.tab', '#barcode-tab', render);
  }
  render();
});
</script>
