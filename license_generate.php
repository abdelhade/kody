<?php
/**
 * صفحة مستقلة لتوليد مفتاح الترخيص من عنوان MAC.
 * ليست جزءاً من قائمة البرنامج.
 */
require_once __DIR__ . '/includes/license.php';

$macInput = isset($_POST['mac']) ? (string) $_POST['mac'] : '';
$key = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $key = kody_license_key_from_mac($macInput) ?? '';
    if ($key === '') {
        $error = 'عنوان MAC غير صالح. مثال: DC-4A-3E-6D-22-38';
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>توليد ترخيص | كودي 2</title>
    <link rel="stylesheet" href="assets/fonts/fonts.css">
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <style>
        :root {
            --primary: #4f46e5;
            --bg: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
        }
        * { box-sizing: border-box; font-family: 'Cairo', 'Segoe UI', Tahoma, sans-serif; }
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--bg);
            color: #f8fafc;
            padding: 24px;
        }
        .card {
            width: 100%;
            max-width: 480px;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 20px;
            padding: 32px;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,.45);
        }
        h1 { margin: 0 0 8px; font-size: 22px; }
        p { margin: 0 0 20px; color: #94a3b8; font-size: 14px; }
        label { display: block; margin-bottom: 8px; font-size: 14px; }
        input[type="text"] {
            width: 100%;
            padding: 12px 14px;
            border-radius: 10px;
            border: 1px solid rgba(255,255,255,0.15);
            background: rgba(15,23,42,0.6);
            color: #fff;
            font-size: 16px;
            letter-spacing: 0.04em;
        }
        button, .copy-btn {
            margin-top: 14px;
            width: 100%;
            padding: 12px 16px;
            border: 0;
            border-radius: 10px;
            background: var(--primary);
            color: #fff;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
        }
        .copy-btn { background: transparent; border: 1px solid rgba(255,255,255,0.2); margin-top: 8px; }
        .result, .err {
            margin-top: 16px;
            padding: 12px 14px;
            border-radius: 10px;
            font-size: 18px;
            letter-spacing: 0.08em;
            text-align: center;
        }
        .result { background: rgba(34,197,94,0.12); color: #86efac; }
        .err { background: rgba(239,68,68,0.12); color: #fca5a5; letter-spacing: 0; font-size: 14px; }
    </style>
</head>
<body>
    <div class="card">
        <h1><i class="fas fa-key"></i> توليد مفتاح الترخيص</h1>
        <p>أدخل عنوان MAC الظاهر في إعدادات الجهاز. المفتاح يُحسب بمعادلة ثابتة على هذا العنوان فقط.</p>
        <form method="post">
            <label for="mac">عنوان MAC</label>
            <input type="text" id="mac" name="mac" value="<?= htmlspecialchars($macInput, ENT_QUOTES, 'UTF-8') ?>" placeholder="DC-4A-3E-6D-22-38" autocomplete="off" required>
            <button type="submit">توليد المفتاح</button>
        </form>
        <?php if ($error !== ''): ?>
            <div class="err"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
        <?php elseif ($key !== ''): ?>
            <div class="result" id="licenseKey"><?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8') ?></div>
            <button type="button" class="copy-btn" id="copyKey">نسخ المفتاح</button>
        <?php endif; ?>
    </div>
    <script>
        var copyBtn = document.getElementById('copyKey');
        if (copyBtn) {
            copyBtn.addEventListener('click', function () {
                var text = (document.getElementById('licenseKey') || {}).textContent || '';
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(text).then(function () {
                        copyBtn.textContent = 'تم النسخ';
                    });
                } else {
                    window.prompt('انسخ المفتاح:', text);
                }
            });
        }
    </script>
</body>
</html>
