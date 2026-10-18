<?php

function kody_receipt_design_defaults(): array
{
    return [
        'enabled' => true,
        // مقاس ونمط الصفحة
        'paper_width' => '78mm',     // '78mm' (80mm standard), '58mm', '100%' (A4), or custom number in mm
        'font_family' => 'Cairo',    // 'Cairo', 'Arial', 'Tahoma', 'Segoe UI', 'Courier New'
        'font_size' => 13,           // px (base font)
        'line_spacing' => 1.25,
        'margin_top' => 2,           // mm
        'margin_bottom' => 4,        // mm
        'margin_left' => 2,          // mm
        'margin_right' => 2,         // mm
        'auto_print' => true,        // فتح نافذة الطباعة تلقائياً
        'auto_close' => true,        // الرجوع/الإغلاق تلقائياً بعد اكتمال الطباعة
        'show_borders' => true,      // إطار خارجي للإيصال

        // الترويسة والشعار
        'header' => [
            'show_logo' => true,
            'logo_width' => 90,        // px
            'logo_align' => 'center',  // 'center', 'right', 'left'
            'show_company_name' => true,
            'company_name_size' => 16, // px
            'show_company_tel' => true,
            'show_company_add' => true,
            'show_tax_no' => false,
            'tax_no' => '',
            'show_cr_no' => false,
            'cr_no' => '',
            'header_text' => '',
            'align' => 'center',
        ],

        // بيانات الفاتورة
        'meta' => [
            'show_invoice_num' => true,
            'invoice_num_style' => 'badge', // 'badge', 'large', 'plain'
            'show_invoice_barcode' => true, // باركود خطي 128 لرقم الفاتورة
            'barcode_height' => 30,         // px
            'show_datetime' => true,
            'show_cashier' => true,
            'show_order_type' => true,     // دليفري / صالة / طاولة
            'show_driver' => true,         // الطيار / مندوب التوصيل
        ],

        // بيانات العميل
        'customer' => [
            'show' => true,
            'show_name' => true,
            'show_phone' => true,
            'show_address' => true,
            'show_balance' => true, // رصيد الحساب قبل وبعد الفاتورة
        ],

        // جدول الأصناف والأعمدة
        'items' => [
            'show_index' => false,       // مسلسل #
            'show_code' => false,        // كود الصنف
            'show_name' => true,         // اسم الصنف
            'show_qty' => true,          // الكمية
            'show_unit' => false,        // الوحدة
            'show_price' => true,        // السعر
            'show_disc' => false,        // عمود الخصم
            'show_total' => true,        // القيمة / الإجمالي
            'show_item_disc_subline' => true, // إظهار الخصم كسطر فرعي تحت اسم الصنف
            'border_style' => 'solid',   // 'solid', 'dashed', 'minimal', 'none'
            'font_size' => 13,
        ],

        // ملخص المبالغ والإجماليات
        'totals' => [
            'show_items_total' => true,  // إجمالي الأصناف
            'show_items_disc' => true,   // خصم الأصناف
            'show_subtotal' => true,     // الإجمالي
            'show_inv_disc' => true,     // خصم الفاتورة
            'show_extra' => true,        // الإضافي
            'show_net' => true,          // الصافي
            'show_paid' => true,         // المدفوع
            'show_change' => true,       // الباقي / المتبقي
            'highlight_net' => true,     // تمييز الصافي بخط أكبر
        ],

        // التذييل
        'footer' => [
            'show_footer_text' => true,
            'footer_text' => '❤ perfect place to grow',
            'show_notes' => true,
            'notes_text' => '',
            'align' => 'center',
        ],
    ];
}

function kody_receipt_design_ensure_column(mysqli $conn): void
{
    $chk = $conn->query("SHOW COLUMNS FROM settings LIKE 'receipt_design'");
    if ($chk && $chk->num_rows === 0) {
        $conn->query('ALTER TABLE settings ADD COLUMN receipt_design MEDIUMTEXT NULL');
    }
}

function kody_rd_bool(array $input, string $key, bool $default): bool
{
    if (!array_key_exists($key, $input)) {
        return $default;
    }
    return filter_var($input[$key], FILTER_VALIDATE_BOOLEAN);
}

function kody_rd_num($value, float $min, float $max, float $fallback): float
{
    if (!is_numeric($value)) {
        return $fallback;
    }
    $n = round((float) $value, 2);
    if ($n < $min) {
        return $min;
    }
    if ($n > $max) {
        return $max;
    }
    return $n;
}

function kody_receipt_design_normalize(array $input): array
{
    $d = kody_receipt_design_defaults();
    $d['enabled'] = kody_rd_bool($input, 'enabled', $d['enabled']);

    // الورق والصفحة
    $pWidth = trim((string)($input['paper_width'] ?? $d['paper_width']));
    if (!in_array($pWidth, ['78mm', '58mm', '100%'], true)) {
        if (preg_match('/^\d+(\.\d+)?(mm)?$/i', $pWidth)) {
            $num = floatval($pWidth);
            $pWidth = ($num > 0) ? $num . 'mm' : '78mm';
        } else {
            $pWidth = '78mm';
        }
    }
    $d['paper_width'] = $pWidth;

    $fonts = ['Cairo', 'Arial', 'Tahoma', 'Segoe UI', 'Courier New'];
    $fontFamily = (string)($input['font_family'] ?? $d['font_family']);
    $d['font_family'] = in_array($fontFamily, $fonts, true) ? $fontFamily : 'Cairo';

    $d['font_size'] = (int)kody_rd_num($input['font_size'] ?? null, 8, 28, $d['font_size']);
    $d['line_spacing'] = kody_rd_num($input['line_spacing'] ?? null, 1.0, 2.5, $d['line_spacing']);
    $d['margin_top'] = kody_rd_num($input['margin_top'] ?? null, 0, 30, $d['margin_top']);
    $d['margin_bottom'] = kody_rd_num($input['margin_bottom'] ?? null, 0, 30, $d['margin_bottom']);
    $d['margin_left'] = kody_rd_num($input['margin_left'] ?? null, 0, 30, $d['margin_left']);
    $d['margin_right'] = kody_rd_num($input['margin_right'] ?? null, 0, 30, $d['margin_right']);

    $d['auto_print'] = kody_rd_bool($input, 'auto_print', $d['auto_print']);
    $d['auto_close'] = kody_rd_bool($input, 'auto_close', $d['auto_close']);
    $d['show_borders'] = kody_rd_bool($input, 'show_borders', $d['show_borders']);

    // Header
    $h = is_array($input['header'] ?? null) ? $input['header'] : [];
    $d['header']['show_logo'] = kody_rd_bool($h, 'show_logo', $d['header']['show_logo']);
    $d['header']['logo_width'] = (int)kody_rd_num($h['logo_width'] ?? null, 30, 250, $d['header']['logo_width']);
    $d['header']['logo_align'] = in_array($h['logo_align'] ?? '', ['center', 'right', 'left'], true) ? $h['logo_align'] : 'center';
    $d['header']['show_company_name'] = kody_rd_bool($h, 'show_company_name', $d['header']['show_company_name']);
    $d['header']['company_name_size'] = (int)kody_rd_num($h['company_name_size'] ?? null, 10, 32, $d['header']['company_name_size']);
    $d['header']['show_company_tel'] = kody_rd_bool($h, 'show_company_tel', $d['header']['show_company_tel']);
    $d['header']['show_company_add'] = kody_rd_bool($h, 'show_company_add', $d['header']['show_company_add']);
    $d['header']['show_tax_no'] = kody_rd_bool($h, 'show_tax_no', $d['header']['show_tax_no']);
    $d['header']['tax_no'] = trim((string)($h['tax_no'] ?? ''));
    $d['header']['show_cr_no'] = kody_rd_bool($h, 'show_cr_no', $d['header']['show_cr_no']);
    $d['header']['cr_no'] = trim((string)($h['cr_no'] ?? ''));
    $d['header']['header_text'] = trim((string)($h['header_text'] ?? ''));
    $d['header']['align'] = in_array($h['align'] ?? '', ['center', 'right', 'left'], true) ? $h['align'] : 'center';

    // Meta
    $m = is_array($input['meta'] ?? null) ? $input['meta'] : [];
    $d['meta']['show_invoice_num'] = kody_rd_bool($m, 'show_invoice_num', $d['meta']['show_invoice_num']);
    $d['meta']['invoice_num_style'] = in_array($m['invoice_num_style'] ?? '', ['badge', 'large', 'plain'], true) ? $m['invoice_num_style'] : 'badge';
    $d['meta']['show_invoice_barcode'] = kody_rd_bool($m, 'show_invoice_barcode', $d['meta']['show_invoice_barcode']);
    $d['meta']['barcode_height'] = (int)kody_rd_num($m['barcode_height'] ?? null, 15, 80, $d['meta']['barcode_height']);
    $d['meta']['show_datetime'] = kody_rd_bool($m, 'show_datetime', $d['meta']['show_datetime']);
    $d['meta']['show_cashier'] = kody_rd_bool($m, 'show_cashier', $d['meta']['show_cashier']);
    $d['meta']['show_order_type'] = kody_rd_bool($m, 'show_order_type', $d['meta']['show_order_type']);
    $d['meta']['show_driver'] = kody_rd_bool($m, 'show_driver', $d['meta']['show_driver']);

    // Customer
    $c = is_array($input['customer'] ?? null) ? $input['customer'] : [];
    $d['customer']['show'] = kody_rd_bool($c, 'show', $d['customer']['show']);
    $d['customer']['show_name'] = kody_rd_bool($c, 'show_name', $d['customer']['show_name']);
    $d['customer']['show_phone'] = kody_rd_bool($c, 'show_phone', $d['customer']['show_phone']);
    $d['customer']['show_address'] = kody_rd_bool($c, 'show_address', $d['customer']['show_address']);
    $d['customer']['show_balance'] = kody_rd_bool($c, 'show_balance', $d['customer']['show_balance']);

    // Items
    $it = is_array($input['items'] ?? null) ? $input['items'] : [];
    $d['items']['show_index'] = kody_rd_bool($it, 'show_index', $d['items']['show_index']);
    $d['items']['show_code'] = kody_rd_bool($it, 'show_code', $d['items']['show_code']);
    $d['items']['show_name'] = kody_rd_bool($it, 'show_name', $d['items']['show_name']);
    $d['items']['show_qty'] = kody_rd_bool($it, 'show_qty', $d['items']['show_qty']);
    $d['items']['show_unit'] = kody_rd_bool($it, 'show_unit', $d['items']['show_unit']);
    $d['items']['show_price'] = kody_rd_bool($it, 'show_price', $d['items']['show_price']);
    $d['items']['show_disc'] = kody_rd_bool($it, 'show_disc', $d['items']['show_disc']);
    $d['items']['show_total'] = kody_rd_bool($it, 'show_total', $d['items']['show_total']);
    $d['items']['show_item_disc_subline'] = kody_rd_bool($it, 'show_item_disc_subline', $d['items']['show_item_disc_subline']);
    $d['items']['border_style'] = in_array($it['border_style'] ?? '', ['solid', 'dashed', 'minimal', 'none'], true) ? $it['border_style'] : 'solid';
    $d['items']['font_size'] = (int)kody_rd_num($it['font_size'] ?? null, 8, 24, $d['font_size']);

    // Totals
    $t = is_array($input['totals'] ?? null) ? $input['totals'] : [];
    $d['totals']['show_items_total'] = kody_rd_bool($t, 'show_items_total', $d['totals']['show_items_total']);
    $d['totals']['show_items_disc'] = kody_rd_bool($t, 'show_items_disc', $d['totals']['show_items_disc']);
    $d['totals']['show_subtotal'] = kody_rd_bool($t, 'show_subtotal', $d['totals']['show_subtotal']);
    $d['totals']['show_inv_disc'] = kody_rd_bool($t, 'show_inv_disc', $d['totals']['show_inv_disc']);
    $d['totals']['show_extra'] = kody_rd_bool($t, 'show_extra', $d['totals']['show_extra']);
    $d['totals']['show_net'] = kody_rd_bool($t, 'show_net', $d['totals']['show_net']);
    $d['totals']['show_paid'] = kody_rd_bool($t, 'show_paid', $d['totals']['show_paid']);
    $d['totals']['show_change'] = kody_rd_bool($t, 'show_change', $d['totals']['show_change']);
    $d['totals']['highlight_net'] = kody_rd_bool($t, 'highlight_net', $d['totals']['highlight_net']);

    // Footer
    $f = is_array($input['footer'] ?? null) ? $input['footer'] : [];
    $d['footer']['show_footer_text'] = kody_rd_bool($f, 'show_footer_text', $d['footer']['show_footer_text']);
    $d['footer']['footer_text'] = trim((string)($f['footer_text'] ?? $d['footer']['footer_text']));
    $d['footer']['show_notes'] = kody_rd_bool($f, 'show_notes', $d['footer']['show_notes']);
    $d['footer']['notes_text'] = trim((string)($f['notes_text'] ?? ''));
    $d['footer']['align'] = in_array($f['align'] ?? '', ['center', 'right', 'left'], true) ? $f['align'] : 'center';

    return $d;
}

function kody_receipt_design_load(?array $rowstg): array
{
    $defaults = kody_receipt_design_defaults();

    // إذا كانت هناك قيم قديمة مخزنة في settings، نستخدمها كأساس افتراضي
    if (!empty($rowstg['receipt_paper_width'])) {
        $defaults['paper_width'] = $rowstg['receipt_paper_width'];
    }
    if (!empty($rowstg['receipt_font_size'])) {
        $defaults['font_size'] = (int)$rowstg['receipt_font_size'];
        $defaults['items']['font_size'] = (int)$rowstg['receipt_font_size'];
    }
    if (isset($rowstg['receipt_show_logo'])) {
        $defaults['header']['show_logo'] = !empty($rowstg['receipt_show_logo']);
    }
    if (isset($rowstg['receipt_show_client'])) {
        $defaults['customer']['show'] = !empty($rowstg['receipt_show_client']);
    }
    if (isset($rowstg['receipt_header_text'])) {
        $defaults['header']['header_text'] = (string)$rowstg['receipt_header_text'];
    }
    if (isset($rowstg['receipt_footer_text'])) {
        $defaults['footer']['footer_text'] = (string)$rowstg['receipt_footer_text'];
    }
    if (isset($rowstg['receipt_notes_text'])) {
        $defaults['footer']['notes_text'] = (string)$rowstg['receipt_notes_text'];
    }

    $raw = $rowstg['receipt_design'] ?? '';
    if (!is_string($raw) || trim($raw) === '') {
        return $defaults;
    }
    $decoded = json_decode($raw, true);
    if (!is_array($decoded)) {
        return $defaults;
    }

    return kody_receipt_design_normalize(array_replace_recursive($defaults, $decoded));
}

function kody_receipt_design_from_post(array $post): array
{
    $pWidth = trim((string)($post['rd_paper_width'] ?? '78mm'));
    if ($pWidth === 'custom') {
        $customVal = floatval($post['rd_paper_width_custom'] ?? 78);
        $pWidth = ($customVal > 0 ? $customVal : 78) . 'mm';
    }

    $input = [
        'enabled' => isset($post['rd_enabled']),
        'paper_width' => $pWidth,
        'font_family' => $post['rd_font_family'] ?? 'Cairo',
        'font_size' => $post['rd_font_size'] ?? 13,
        'line_spacing' => $post['rd_line_spacing'] ?? 1.25,
        'margin_top' => $post['rd_margin_top'] ?? 2,
        'margin_bottom' => $post['rd_margin_bottom'] ?? 4,
        'margin_left' => $post['rd_margin_left'] ?? 2,
        'margin_right' => $post['rd_margin_right'] ?? 2,
        'auto_print' => isset($post['rd_auto_print']),
        'auto_close' => isset($post['rd_auto_close']),
        'show_borders' => isset($post['rd_show_borders']),

        'header' => [
            'show_logo' => isset($post['rd_show_logo']),
            'logo_width' => $post['rd_logo_width'] ?? 90,
            'logo_align' => $post['rd_logo_align'] ?? 'center',
            'show_company_name' => isset($post['rd_show_company_name']),
            'company_name_size' => $post['rd_company_name_size'] ?? 16,
            'show_company_tel' => isset($post['rd_show_company_tel']),
            'show_company_add' => isset($post['rd_show_company_add']),
            'show_tax_no' => isset($post['rd_show_tax_no']),
            'tax_no' => $post['rd_tax_no'] ?? '',
            'show_cr_no' => isset($post['rd_show_cr_no']),
            'cr_no' => $post['rd_cr_no'] ?? '',
            'header_text' => $post['rd_header_text'] ?? '',
            'align' => $post['rd_header_align'] ?? 'center',
        ],

        'meta' => [
            'show_invoice_num' => isset($post['rd_show_invoice_num']),
            'invoice_num_style' => $post['rd_invoice_num_style'] ?? 'badge',
            'show_invoice_barcode' => isset($post['rd_show_invoice_barcode']),
            'barcode_height' => $post['rd_barcode_height'] ?? 30,
            'show_datetime' => isset($post['rd_show_datetime']),
            'show_cashier' => isset($post['rd_show_cashier']),
            'show_order_type' => isset($post['rd_show_order_type']),
            'show_driver' => isset($post['rd_show_driver']),
        ],

        'customer' => [
            'show' => isset($post['rd_show_customer']),
            'show_name' => isset($post['rd_show_customer_name']),
            'show_phone' => isset($post['rd_show_customer_phone']),
            'show_address' => isset($post['rd_show_customer_address']),
            'show_balance' => isset($post['rd_show_customer_balance']),
        ],

        'items' => [
            'show_index' => isset($post['rd_show_col_index']),
            'show_code' => isset($post['rd_show_col_code']),
            'show_name' => isset($post['rd_show_col_name']),
            'show_qty' => isset($post['rd_show_col_qty']),
            'show_unit' => isset($post['rd_show_col_unit']),
            'show_price' => isset($post['rd_show_col_price']),
            'show_disc' => isset($post['rd_show_col_disc']),
            'show_total' => isset($post['rd_show_col_total']),
            'show_item_disc_subline' => isset($post['rd_show_item_disc_subline']),
            'border_style' => $post['rd_table_border_style'] ?? 'solid',
            'font_size' => $post['rd_items_font_size'] ?? 13,
        ],

        'totals' => [
            'show_items_total' => isset($post['rd_show_items_total']),
            'show_items_disc' => isset($post['rd_show_items_disc']),
            'show_subtotal' => isset($post['rd_show_subtotal']),
            'show_inv_disc' => isset($post['rd_show_inv_disc']),
            'show_extra' => isset($post['rd_show_extra']),
            'show_net' => isset($post['rd_show_net']),
            'show_paid' => isset($post['rd_show_paid']),
            'show_change' => isset($post['rd_show_change']),
            'highlight_net' => isset($post['rd_highlight_net']),
        ],

        'footer' => [
            'show_footer_text' => isset($post['rd_show_footer_text']),
            'footer_text' => $post['rd_footer_text'] ?? '❤ perfect place to grow',
            'show_notes' => isset($post['rd_show_notes']),
            'notes_text' => $post['rd_notes_text'] ?? '',
            'align' => $post['rd_footer_align'] ?? 'center',
        ],
    ];

    return kody_receipt_design_normalize($input);
}

function kody_receipt_design_json(array $design): string
{
    return json_encode(kody_receipt_design_normalize($design), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}
