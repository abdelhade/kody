# TODO - حفظ بيانات الدفع مع الطلب عند التعديل ✅

## Steps:
1. ✅ إنشاء أعمدة قاعدة البيانات - موجودة بالفعل (paid_amount, remaining_amount, payment_status, payment_notes)
2. ✅ تحديث `pos_barcode.php` - إضافة hidden fields لبيانات الدفع المحفوظة
3. ✅ تحديث `js/pos_barcode.js` - ملء حقول مودال الدفع من hidden fields عند فتح المودال
4. ✅ تحديث `js/pos_barcode.js` - دالة `loadExistingOrder` لتحديث hidden edit payment fields
5. ✅ تحديث `do/doadd_invoice.php` - حفظ بيانات الدفع في قاعدة البيانات عند التعديل
6. ✅ تحديث `ajax/load_order.php` - إرجاع بيانات الدفع مع الطلب عند تحميل طلب طاولة
7. ✅ إضافة old_payment_container إلى `includes/pos_content.php`

