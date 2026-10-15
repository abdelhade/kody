<?php
session_start();
include('includes/connect.php');
require_once 'classes/ShiftCloser.php';

if (!isset($_SESSION['userid'])) {
    header('Location: login.php');
    exit;
}

$user_id = (int) $_SESSION['userid'];
$closer = new ShiftCloser($conn);
$p = $closer->preview($user_id);
$showExpected = ShiftCloser::canSeeExpected($rowstg ?? []);

$error = $_SESSION['error_message'] ?? '';
unset($_SESSION['error_message']);

function zr_money($v)
{
    return number_format((float) $v, 2);
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقفيل الشيفت</title>
    <link href="dist/css/z_report.css" rel="stylesheet">
    <link href="assets/libs/fontawesome.min.css" rel="stylesheet">
</head>
<body class="z-report-page">

<div class="ticket">
    <div class="ticket-header">
        <h4>تقفيل الشيفت</h4>
        <div><?= date('Y-m-d H:i') ?></div>
    </div>

    <?php if ($error !== ''): ?>
    <div style="background:#fdecea;color:#b71c1c;padding:8px;margin-bottom:8px;text-align:center;">
        <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <div class="ticket-info">
        <div><span>الكاشير:</span> <span><?= htmlspecialchars($p['cashier_name']) ?></span></div>
        <div><span>بداية الشيفت:</span> <span><?= htmlspecialchars($p['opened_at'] ?? '—') ?></span></div>
    </div>

    <div class="section-title">ملخص المبيعات</div>
    <table class="summary-table">
        <tr><td>عدد فواتير البيع</td><td class="amount"><?= (int) $p['orders_count'] ?></td></tr>
        <?php if ($p['returns_count'] > 0): ?>
        <tr><td>عدد المرتجعات</td><td class="amount"><?= (int) $p['returns_count'] ?></td></tr>
        <?php endif; ?>
        <?php if ($showExpected): ?>
        <tr><td>إجمالي المبيعات</td><td class="amount"><?= zr_money($p['sales_gross']) ?></td></tr>
        <tr><td>الخصومات</td><td class="amount"><?= zr_money($p['sales_discount']) ?></td></tr>
        <?php if ($p['returns_total'] > 0): ?>
        <tr><td>المرتجعات</td><td class="amount"><?= zr_money($p['returns_total']) ?></td></tr>
        <?php endif; ?>
        <tr class="total-row"><td>صافي المبيعات</td><td class="amount"><?= zr_money($p['net_sales']) ?></td></tr>
        <?php if ($p['expenses'] > 0): ?>
        <tr><td>مصروفات</td><td class="amount"><?= zr_money($p['expenses']) ?></td></tr>
        <?php endif; ?>
        <?php endif; ?>
    </table>

    <?php if (Can::check('pos.close_shift')) { ?>
    <div class="input-section no-print">
        <form action="do/doclose_shift.php" method="POST">
            <div class="section-title">جرد الخزائن</div>
            <?php if (!$showExpected): ?>
            <p style="font-size:0.85rem;color:#666;">أدخل المبالغ اللي عدّيتها فعلاً — الفرق هيظهر بعد التقفيل.</p>
            <?php endif; ?>

            <?php if (empty($p['accounts'])): ?>
            <p style="text-align:center;color:#666;">لا توجد حركات نقدية في الشيفت</p>
            <?php endif; ?>

            <?php foreach ($p['accounts'] as $acc): ?>
            <div class="form-group">
                <label>
                    <?= htmlspecialchars($acc['name']) ?>
                    <?php if ($showExpected): ?>
                    <small>(المتوقع: <?= zr_money($acc['expected']) ?>)</small>
                    <?php endif; ?>
                </label>
                <input type="number" step="0.01" min="0" name="counted[<?= (int) $acc['account_id'] ?>]" class="form-control" required placeholder="المبلغ المعدود">
            </div>
            <?php endforeach; ?>

            <?php if ($p['primary_fund'] > 0): ?>
            <div class="form-group">
                <label>يتساب في الدرج للشيفت الجاي</label>
                <input type="number" step="0.01" min="0" name="left_in_drawer" class="form-control" value="0">
            </div>
            <?php endif; ?>

            <div class="form-group">
                <label>ملاحظات</label>
                <input type="text" name="notes" class="form-control" maxlength="255" placeholder="أي ملاحظات للتقفيل">
            </div>

            <button type="submit" class="btn-close-shift" onclick="return confirm('متأكد من تقفيل الشيفت؟')">
                <i class="fas fa-lock me-2"></i> تأكيد وتقفيل الشيفت
            </button>
            <br><br>
            <a href="pos_barcode.php" style="display:block; text-align:center; color:#666;">رجوع لنقطة البيع</a>
        </form>
    </div>
    <?php } ?>
</div>

</body>
</html>
