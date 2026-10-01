<?php

function kody_barcode_element_defs(): array
{
    return [
        'company' => ['label' => 'اسم الشركة', 'font' => true],
        'item_name' => ['label' => 'اسم الصنف', 'font' => true],
        'barcode_linear' => ['label' => 'الباركود الخطي', 'font' => false],
        'barcode_text' => ['label' => 'الباركود النصي', 'font' => true],
        'code' => ['label' => 'الشفرة', 'font' => true],
        'price_before' => ['label' => 'السعر قبل الخصم', 'font' => true],
        'price_after' => ['label' => 'السعر بعد الخصم', 'font' => true],
    ];
}

function kody_barcode_design_defaults(): array
{
    return [
        'enabled' => true,
        'company_name' => 'focus house',
        'align' => 'center',
        'invert' => true,
        'paper_width' => 25,
        'paper_height' => 38,
        'margin_top' => 2,
        'margin_bottom' => 2,
        'margin_left' => 2,
        'margin_right' => 2,
        'elements' => [
            'company' => ['show' => true, 'h' => 4, 'w' => 21, 'top' => 0, 'left' => 0, 'font' => 10],
            'item_name' => ['show' => true, 'h' => 4, 'w' => 21, 'top' => 4, 'left' => 0, 'font' => 8],
            'barcode_linear' => ['show' => true, 'h' => 15, 'w' => 21, 'top' => 8, 'left' => 0, 'font' => 0],
            'barcode_text' => ['show' => true, 'h' => 3.5, 'w' => 21, 'top' => 20, 'left' => 0, 'font' => 8],
            'code' => ['show' => true, 'h' => 3.5, 'w' => 21, 'top' => 23.5, 'left' => 0, 'font' => 8],
            'price_before' => ['show' => true, 'h' => 3, 'w' => 21, 'top' => 27, 'left' => 0, 'font' => 9],
            'price_after' => ['show' => true, 'h' => 3.5, 'w' => 21, 'top' => 30, 'left' => 0, 'font' => 9],
        ],
        'code_prefix' => '',
        'code_suffix' => '',
        'include_item_code' => true,
        'embed_prices' => 'none',
        'price_before_source' => 'price1',
        'price_after_source' => 'printed',
        'strike_before' => true,
    ];
}

function kody_barcode_design_ensure_column(mysqli $conn): void
{
    $chk = $conn->query("SHOW COLUMNS FROM settings LIKE 'barcode_design'");
    if ($chk && $chk->num_rows === 0) {
        $conn->query('ALTER TABLE settings ADD COLUMN barcode_design MEDIUMTEXT NULL');
    }
}

function kody_bd_bool(array $input, string $key, bool $default): bool
{
    if (!array_key_exists($key, $input)) {
        return $default;
    }
    return filter_var($input[$key], FILTER_VALIDATE_BOOLEAN);
}

function kody_bd_num($value, float $min, float $max, float $fallback): float
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

function kody_barcode_design_normalize(array $input): array
{
    $d = kody_barcode_design_defaults();
    $d['enabled'] = kody_bd_bool($input, 'enabled', $d['enabled']);
    $d['invert'] = kody_bd_bool($input, 'invert', $d['invert']);
    $d['include_item_code'] = kody_bd_bool($input, 'include_item_code', $d['include_item_code']);
    $d['strike_before'] = kody_bd_bool($input, 'strike_before', $d['strike_before']);

    $name = trim((string) ($input['company_name'] ?? $d['company_name']));
    if (function_exists('mb_substr')) {
        $name = mb_substr($name, 0, 200);
    } else {
        $name = substr($name, 0, 200);
    }
    $d['company_name'] = $name;

    $align = (string) ($input['align'] ?? 'center');
    $d['align'] = in_array($align, ['center', 'right', 'left'], true) ? $align : 'center';

    $d['paper_width'] = kody_bd_num($input['paper_width'] ?? null, 10, 200, $d['paper_width']);
    $d['paper_height'] = kody_bd_num($input['paper_height'] ?? null, 10, 300, $d['paper_height']);
    $d['margin_top'] = kody_bd_num($input['margin_top'] ?? null, 0, 40, $d['margin_top']);
    $d['margin_bottom'] = kody_bd_num($input['margin_bottom'] ?? null, 0, 40, $d['margin_bottom']);
    $d['margin_left'] = kody_bd_num($input['margin_left'] ?? null, 0, 40, $d['margin_left']);
    $d['margin_right'] = kody_bd_num($input['margin_right'] ?? null, 0, 40, $d['margin_right']);

    $incoming = is_array($input['elements'] ?? null) ? $input['elements'] : [];
    foreach ($d['elements'] as $key => $el) {
        $src = is_array($incoming[$key] ?? null) ? $incoming[$key] : [];
        $d['elements'][$key] = [
            'show' => kody_bd_bool($src, 'show', $el['show']),
            'h' => kody_bd_num($src['h'] ?? null, 0, 200, $el['h']),
            'w' => kody_bd_num($src['w'] ?? null, 0, 200, $el['w']),
            'top' => kody_bd_num($src['top'] ?? null, 0, 300, $el['top']),
            'left' => kody_bd_num($src['left'] ?? null, 0, 200, $el['left']),
            'font' => kody_bd_num($src['font'] ?? null, 0, 72, $el['font']),
        ];
    }

    $prefix = trim((string) ($input['code_prefix'] ?? ''));
    $suffix = trim((string) ($input['code_suffix'] ?? ''));
    $d['code_prefix'] = function_exists('mb_substr') ? mb_substr($prefix, 0, 40) : substr($prefix, 0, 40);
    $d['code_suffix'] = function_exists('mb_substr') ? mb_substr($suffix, 0, 40) : substr($suffix, 0, 40);

    $embed = (string) ($input['embed_prices'] ?? 'none');
    $allowedEmbed = ['none', 'before', 'after', 'before_after', 'after_before'];
    $d['embed_prices'] = in_array($embed, $allowedEmbed, true) ? $embed : 'none';

    $sources = ['printed', 'price1', 'price2', 'price3'];
    $beforeSrc = (string) ($input['price_before_source'] ?? $d['price_before_source']);
    $afterSrc = (string) ($input['price_after_source'] ?? $d['price_after_source']);
    $d['price_before_source'] = in_array($beforeSrc, $sources, true) ? $beforeSrc : 'price1';
    $d['price_after_source'] = in_array($afterSrc, $sources, true) ? $afterSrc : 'printed';

    return $d;
}

function kody_barcode_design_load(?array $rowstg): array
{
    $defaults = kody_barcode_design_defaults();
    $raw = $rowstg['barcode_design'] ?? '';
    if (!is_string($raw) || trim($raw) === '') {
        return $defaults;
    }
    $decoded = json_decode($raw, true);
    if (!is_array($decoded)) {
        return $defaults;
    }
    return kody_barcode_design_normalize($decoded);
}

function kody_barcode_design_from_post(array $post): array
{
    $elements = [];
    $postedElements = is_array($post['bd_el'] ?? null) ? $post['bd_el'] : [];
    foreach (kody_barcode_element_defs() as $key => $meta) {
        $src = is_array($postedElements[$key] ?? null) ? $postedElements[$key] : [];
        $elements[$key] = [
            'show' => isset($src['show']),
            'h' => $src['h'] ?? null,
            'w' => $src['w'] ?? null,
            'top' => $src['top'] ?? null,
            'left' => $src['left'] ?? null,
            'font' => $meta['font'] ? ($src['font'] ?? null) : 0,
        ];
    }

    return kody_barcode_design_normalize([
        'enabled' => isset($post['bd_enabled']),
        'company_name' => $post['bd_company_name'] ?? '',
        'align' => $post['bd_align'] ?? 'center',
        'invert' => isset($post['bd_invert']),
        'paper_width' => $post['bd_paper_width'] ?? null,
        'paper_height' => $post['bd_paper_height'] ?? null,
        'margin_top' => $post['bd_margin_top'] ?? null,
        'margin_bottom' => $post['bd_margin_bottom'] ?? null,
        'margin_left' => $post['bd_margin_left'] ?? null,
        'margin_right' => $post['bd_margin_right'] ?? null,
        'elements' => $elements,
        'code_prefix' => $post['bd_code_prefix'] ?? '',
        'code_suffix' => $post['bd_code_suffix'] ?? '',
        'include_item_code' => isset($post['bd_include_item_code']),
        'embed_prices' => $post['bd_embed_prices'] ?? 'none',
        'price_before_source' => $post['bd_price_before_source'] ?? 'price1',
        'price_after_source' => $post['bd_price_after_source'] ?? 'printed',
        'strike_before' => isset($post['bd_strike_before']),
    ]);
}

function kody_barcode_design_json(array $design): string
{
    return json_encode(kody_barcode_design_normalize($design), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

function kody_barcode_encode_price($amount): string
{
    if (!is_numeric($amount) || $amount === '') {
        return '0';
    }
    return (string) (int) round(((float) $amount) * 100);
}

function kody_barcode_compose_code(array $design, string $itemCode, $priceBefore, $priceAfter): string
{
    $body = !empty($design['include_item_code']) ? $itemCode : '';
    $mode = (string) ($design['embed_prices'] ?? 'none');
    $encoded = '';
    if ($mode === 'before') {
        $encoded = kody_barcode_encode_price($priceBefore);
    } elseif ($mode === 'after') {
        $encoded = kody_barcode_encode_price($priceAfter);
    } elseif ($mode === 'before_after') {
        $encoded = kody_barcode_encode_price($priceBefore) . kody_barcode_encode_price($priceAfter);
    } elseif ($mode === 'after_before') {
        $encoded = kody_barcode_encode_price($priceAfter) . kody_barcode_encode_price($priceBefore);
    }
    return (string) ($design['code_prefix'] ?? '') . $body . $encoded . (string) ($design['code_suffix'] ?? '');
}

function kody_barcode_format_price($amount): string
{
    if ($amount === '' || $amount === null || !is_numeric($amount)) {
        return '';
    }
    $n = (float) $amount;
    if (abs($n - round($n)) < 0.001) {
        return (string) (int) round($n);
    }
    return rtrim(rtrim(number_format($n, 2, '.', ''), '0'), '.');
}

function kody_barcode_pick_price(string $source, array $prices)
{
    $allowed = ['printed', 'price1', 'price2', 'price3'];
    if (!in_array($source, $allowed, true)) {
        return '';
    }
    return $prices[$source] ?? '';
}

function kody_bd_fmt($n): string
{
    $s = rtrim(rtrim(number_format((float) $n, 2, '.', ''), '0'), '.');
    return $s === '' ? '0' : $s;
}
