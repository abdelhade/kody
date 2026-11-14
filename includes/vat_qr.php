<?php
/**
 * QR ضريبة القيمة المضافة بنمط ZATCA (TLV → Base64)
 * للعرض على الفاتورة/الإيصال — بدون ربط بمنصة إلكترونية.
 */

/**
 * @return array{rate:float, inclusive:bool, total:float, exclusive:float, vat:float}|null
 */
function kody_vat_amounts(float $net, array $design, ?array $invoice = null): ?array
{
    $rate = (float) ($design['totals']['vat_rate'] ?? 0);
    if ($rate <= 0) {
        return null;
    }

    $storedTax = $invoice !== null ? (float) ($invoice['fat_tax'] ?? 0) : 0.0;
    $storedPer = $invoice !== null ? (float) ($invoice['fat_tax_per'] ?? 0) : 0.0;
    if ($storedTax > 0.0001) {
        $vat = round($storedTax, 2);
        $total = round($net, 2);
        $exclusive = round($total - $vat, 2);
        if ($exclusive < 0) {
            $exclusive = 0.0;
        }
        $useRate = $storedPer > 0 ? $storedPer : $rate;
        return [
            'rate' => $useRate,
            'inclusive' => true,
            'total' => $total,
            'exclusive' => $exclusive,
            'vat' => $vat,
        ];
    }

    $inclusive = !empty($design['totals']['vat_inclusive']);
    $net = round($net, 2);
    if ($inclusive) {
        $vat = round($net * $rate / (100 + $rate), 2);
        $exclusive = round($net - $vat, 2);
        $total = $net;
    } else {
        $exclusive = $net;
        $vat = round($net * $rate / 100, 2);
        $total = round($exclusive + $vat, 2);
    }

    return [
        'rate' => $rate,
        'inclusive' => $inclusive,
        'total' => $total,
        'exclusive' => $exclusive,
        'vat' => $vat,
    ];
}

function kody_zatca_tlv_field(int $tag, string $value): string
{
    $bytes = $value;
    $len = strlen($bytes);
    if ($len > 255) {
        $bytes = substr($bytes, 0, 255);
        $len = 255;
    }
    return chr($tag) . chr($len) . $bytes;
}

/**
 * يبني حمولة QR (Base64) وفق حقول ZATCA الخمسة.
 */
function kody_zatca_qr_payload(
    string $sellerName,
    string $vatNumber,
    string $timestampIso,
    float $totalWithVat,
    float $vatAmount
): string {
    $tlv = kody_zatca_tlv_field(1, $sellerName)
        . kody_zatca_tlv_field(2, $vatNumber)
        . kody_zatca_tlv_field(3, $timestampIso)
        . kody_zatca_tlv_field(4, number_format($totalWithVat, 2, '.', ''))
        . kody_zatca_tlv_field(5, number_format($vatAmount, 2, '.', ''));

    return base64_encode($tlv);
}

/**
 * @return array{payload:string, amounts:array, seller:string, vat_no:string, timestamp:string}|null
 */
function kody_vat_qr_build(array $design, array $settings, array $invoice): ?array
{
    if (empty($design['totals']['show_vat_qr'])) {
        return null;
    }

    $vatNo = trim((string) ($design['header']['tax_no'] ?? ''));
    if ($vatNo === '') {
        return null;
    }

    $seller = trim((string) ($settings['company_name'] ?? ''));
    if ($seller === '') {
        $seller = 'Kody';
    }

    $net = (float) ($invoice['fat_net'] ?? 0);
    $amounts = kody_vat_amounts($net, $design, $invoice);
    if ($amounts === null) {
        return null;
    }

    $rawTime = trim((string) ($invoice['crtime'] ?? ''));
    if ($rawTime === '') {
        $rawTime = trim((string) ($invoice['pro_date'] ?? '')) . ' ' . date('H:i:s');
    }
    $ts = strtotime($rawTime);
    $timestamp = $ts ? date('c', $ts) : date('c');

    $payload = kody_zatca_qr_payload(
        $seller,
        $vatNo,
        $timestamp,
        $amounts['total'],
        $amounts['vat']
    );

    return [
        'payload' => $payload,
        'amounts' => $amounts,
        'seller' => $seller,
        'vat_no' => $vatNo,
        'timestamp' => $timestamp,
    ];
}
