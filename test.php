<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>طباعة باركود</title>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
</head>
<body>
    <div id="barcode-container">
        <svg id="barcode"></svg>
    </div>

    <button onclick="window.print()" noprint>طباعة</button>

    <script>
        // توليد الباركود
        JsBarcode("#barcode", "123456789012", {
            format: "CODE128",  // نوع الباركود
            width: 2,           // عرض الباركود
            height: 100,        // ارتفاع الباركود
            displayValue: true  // عرض الرقم أسفل الباركود
        });
    </script>
</body>
</html>
