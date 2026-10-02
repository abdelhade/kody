<?php
include('../includes/connect.php');
require_once __DIR__ . '/../includes/barcode_design.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit;
}

$design = kody_barcode_design_load(is_array($rowstg ?? null) ? $rowstg : []);
$codes = $_POST['code'] ?? [];
$barcodes = $_POST['barcode'] ?? [];
$names = $_POST['iname'] ?? [];
$prices = $_POST['price'] ?? [];
$quantities = $_POST['qty'] ?? [];
$plist1 = $_POST['plist1'] ?? [];
$plist2 = $_POST['plist2'] ?? [];
$plist3 = $_POST['plist3'] ?? [];
$printSource = (string) ($_POST['print_price_source'] ?? 'price1');
if (!in_array($printSource, ['price1', 'price2', 'price3'], true)) {
    $printSource = 'price1';
}

$pickPrinted = function ($index) use ($printSource, $plist1, $plist2, $plist3, $prices) {
    if ($printSource === 'price2') {
        $picked = $plist2[$index] ?? '';
    } elseif ($printSource === 'price3') {
        $picked = $plist3[$index] ?? '';
    } else {
        $picked = $plist1[$index] ?? '';
    }
    if ($picked === '' || $picked === null) {
        $picked = $prices[$index] ?? '';
    }
    return $picked;
};

if (!is_array($codes)) {
    exit;
}

if (empty($design['enabled'])) {
    $company_name = $rowstg['company_name'] ?? '';
    echo '<html><head>';
    echo '<script src="code.js"></script>';
    echo '</head><body style="margin:0px;font-size:12px" onload="window.print();">';
    foreach ($codes as $index => $code) {
        $name = htmlspecialchars((string) ($names[$index] ?? ''), ENT_QUOTES, 'UTF-8');
        $barcode = htmlspecialchars((string) ($barcodes[$index] ?? ''), ENT_QUOTES, 'UTF-8');
        $price = htmlspecialchars((string) $pickPrinted($index), ENT_QUOTES, 'UTF-8');
        $quantity = (int) ($quantities[$index] ?? 0);
        for ($i = 0; $i < $quantity; $i++) {
            echo "<center>";
            echo "<div style='margin-bottom: 0px;height:24mm'>";
            echo "<div style='width:33mm;background: black;color:white;margin:0px;padding:0px'>" . htmlspecialchars((string) $company_name, ENT_QUOTES, 'UTF-8') . "</div>";
            echo $name;
            echo "<br>";
            echo "<canvas id='barcode-$index-$i' width='143' height='85'></canvas>";
            echo "<script>
                    JsBarcode('#barcode-$index-$i', " . json_encode((string) ($barcodes[$index] ?? ''), JSON_UNESCAPED_UNICODE) . ", {
                        format: 'CODE128',
                        displayValue: false,
                        width: 1.5,
                        height: 15,
                        margin: 0
                    });
                  </script>";
            echo "<br>";
            echo $barcode;
            echo "<br>";
            echo "$price LE";
            echo "</div>";
            echo "</center>";
        }
    }
    echo '</body></html>';
    exit;
}

$w = (float) $design['paper_width'];
$h = (float) $design['paper_height'];
$align = $design['align'];
$justify = $align === 'left' ? 'flex-start' : ($align === 'right' ? 'flex-end' : 'center');
?>
<!DOCTYPE html>
<html lang="ar">
<head>
<meta charset="utf-8">
<title>باركود</title>
<script src="code.js"></script>
<style>
  @page { size: <?= $w ?>mm <?= $h ?>mm; margin: 0; }
  html, body { margin: 0; padding: 0; background: #fff; }
  .bd-label {
    position: relative;
    width: <?= $w ?>mm;
    height: <?= $h ?>mm;
    overflow: hidden;
    page-break-after: always;
    break-after: page;
    background: #fff;
  }
  .bd-label:last-child { page-break-after: auto; break-after: auto; }
  .bd-content { position: absolute; }
  .bd-el {
    position: absolute;
    overflow: hidden;
    box-sizing: border-box;
    display: flex;
    align-items: center;
    justify-content: <?= $justify ?>;
    text-align: <?= htmlspecialchars($align, ENT_QUOTES, 'UTF-8') ?>;
    direction: rtl;
    line-height: 1;
    white-space: nowrap;
    color: #000;
    font-family: Tahoma, Arial, sans-serif;
  }
  .bd-el.bd-name {
    white-space: normal;
    line-height: 1.1;
    align-items: center;
  }
  .bd-el.bd-name span {
    display: block;
    width: 100%;
    min-width: 0;
    white-space: normal;
    overflow-wrap: anywhere;
    word-break: break-word;
    text-align: inherit;
  }
  .bd-el svg { width: 100%; height: 100%; display: block; }
</style>
</head>
<body onload="window.print();">
<?php
$barcodeJobs = [];
foreach ($codes as $index => $itemCodeRaw) {
    $itemCode = (string) $itemCodeRaw;
    $barcodeValue = (string) ($barcodes[$index] ?? '');
    $itemName = (string) ($names[$index] ?? '');
    $quantity = (int) ($quantities[$index] ?? 0);
    if ($quantity < 1) {
        continue;
    }
    $priceBag = [
        'printed' => $pickPrinted($index),
        'price1' => $plist1[$index] ?? '',
        'price2' => $plist2[$index] ?? '',
        'price3' => $plist3[$index] ?? '',
    ];
    $priceBeforeRaw = kody_barcode_pick_price($design['price_before_source'], $priceBag);
    $priceAfterRaw = kody_barcode_pick_price($design['price_after_source'], $priceBag);
    $priceBefore = kody_barcode_format_price($priceBeforeRaw);
    $priceAfter = kody_barcode_format_price($priceAfterRaw);
    $composed = kody_barcode_compose_code($design, $itemCode, $priceBeforeRaw, $priceAfterRaw);

    for ($i = 0; $i < $quantity; $i++) {
        $svgId = 'bc-' . $index . '-' . $i;
        echo '<div class="bd-label">';
        echo '<div class="bd-content" style="top:' . kody_bd_fmt($design['margin_top']) . 'mm;right:' . kody_bd_fmt($design['margin_right']) . 'mm;bottom:' . kody_bd_fmt($design['margin_bottom']) . 'mm;left:' . kody_bd_fmt($design['margin_left']) . 'mm;">';
        foreach (kody_barcode_element_defs() as $key => $meta) {
            $el = $design['elements'][$key];
            if (empty($el['show'])) {
                continue;
            }
            $box = 'top:' . kody_bd_fmt($el['top']) . 'mm;left:' . kody_bd_fmt($el['left']) . 'mm;width:' . kody_bd_fmt($el['w']) . 'mm;height:' . kody_bd_fmt($el['h']) . 'mm;';
            if ($key === 'barcode_linear') {
                if ($barcodeValue === '') {
                    continue;
                }
                echo '<div class="bd-el" style="' . $box . 'display:block;"><svg id="' . $svgId . '"></svg></div>';
                $barcodeJobs[] = ['id' => $svgId, 'value' => $barcodeValue];
                continue;
            }
            $font = 'font-size:' . kody_bd_fmt($el['font']) . 'pt;';
            $extra = '';
            $text = '';
            if ($key === 'company') {
                $text = (string) $design['company_name'];
                if (!empty($design['invert'])) {
                    $extra = 'background:#000;color:#fff;';
                }
            } elseif ($key === 'item_name') {
                $text = $itemName;
                echo '<div class="bd-el bd-name" style="' . $box . $font . $extra . '"><span>' . htmlspecialchars($text, ENT_QUOTES, 'UTF-8') . '</span></div>';
                continue;
            } elseif ($key === 'barcode_text') {
                $text = $barcodeValue;
            } elseif ($key === 'code') {
                $text = $composed;
            } elseif ($key === 'price_before') {
                $text = $priceBefore;
                if (!empty($design['strike_before'])) {
                    $extra = 'text-decoration:line-through;';
                }
            } elseif ($key === 'price_after') {
                $text = $priceAfter;
                $extra = 'font-weight:700;';
            }
            echo '<div class="bd-el" style="' . $box . $font . $extra . '">' . htmlspecialchars($text, ENT_QUOTES, 'UTF-8') . '</div>';
        }
        echo '</div></div>';
    }
}
?>
<script>
(function () {
  var jobs = <?= json_encode($barcodeJobs, JSON_UNESCAPED_UNICODE) ?>;
  jobs.forEach(function (job) {
    var el = document.getElementById(job.id);
    if (!el || !window.JsBarcode || !job.value) return;
    try {
      JsBarcode(el, job.value, {
        format: 'CODE128',
        displayValue: false,
        margin: 0,
        background: 'transparent',
        lineColor: '#000',
        width: 1,
        height: 40
      });
      el.setAttribute('preserveAspectRatio', 'none');
      el.style.width = '100%';
      el.style.height = '100%';
    } catch (e) {}
  });
})();
</script>
</body>
</html>
