# TODO: ربط صفحة الطاولات مع POS بعد إضافة صنف

## الخطوات:

### ✅ الخطوة 1: tables.php
- [x] تعديل رابط "إضافة صنف" ليحتوي على `table_id` كمعامل إضافي

### ✅ الخطوة 2: pos_barcode.php
- [x] استخراج `table_id` من GET وتمريره إلى pos_content.php عبر متغير `$table_id_from_get`

### ✅ الخطوة 3: includes/pos_content.php
- [x] إضافة حقل مخفي `table_id` في الفورم بقيمة من `$table_id_from_get`

### ✅ الخطوة 4: do/doadd_invoice.php
- [x] إضافة منطق التوجيه: عند وجود `table_id` في POST، التوجيه إلى `tables.php?table_id=X`

## ملخص التغييرات:

تم تعديل 4 ملفات لربط صفحة الطاولات مع POS بعد إضافة صنف:

1. **tables.php**: تعديل رابط "إضافة صنف" ليحوي `table_id` (تم مسبقاً)
2. **pos_barcode.php**: إضافة متغير `$table_id_from_get` لاستخراج `table_id` من GET
3. **includes/pos_content.php**: تعيين قيمة الحقل المخفي `table_id` من `$table_id_from_get`
4. **do/doadd_invoice.php**: إضافة توجيه إلى `tables.php?table_id=X` عند وجود `table_id` في POST

