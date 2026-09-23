# خطة Refactoring — Launch / DB / ot_head (Kody)

مرجع تنفيذي للنظام الحالي (`kody` PHP)، وليس مسار Laravel.

## الأهداف

1. **Launch على السيرفر:** `.env` → `pre_start.php` → إنشاء/استعادة قاعدة → migrations → دخول.
2. **قاعدة جديدة + تحديث:** زر إنشاء من `pre_start`، وزر تحديث من الإعدادات عبر `MigrationRunner`.
3. **توحيد الحفظ/الحذف على `ot_head`:** منطق مركزي في `InvoiceProcessor` (soft delete + helpers).
4. **تعدد المدد:** قواعد منفصلة + قفل مدة + أرصدة افتتاحية.

## حالة المراحل

| # | المرحلة | الحالة |
| :---: | :--- | :---: |
| 1 | MigrationRunner + ملفات 013–017 | مكتمل |
| 2 | ربط launch والإعدادات | مكتمل |
| 3 | `softDelete` + ربط `dodel_invoice` | مكتمل |
| 4 | تعدد المدد (`PeriodManager`) | مكتمل |
| 5 | استخراج helpers الحفظ (header/journal/payments/details) | مكتمل |
| 6 | إيقاف `update.php` SQL الحر + تليين `ensure_*` | مكتمل |
| 7 | توحيد `doadd` عند `edit_id` + شارة المدة في navbar | مكتمل |

## المكونات

| ملف | دور |
| :--- | :--- |
| `includes/MigrationRunner.php` | تشغيل ملفات `update/NNN_*.sql` مرة واحدة + جدول `schema_migrations` |
| `ajax/db_setup.php` | إنشاء/استعادة ثم تشغيل migrations الناقصة |
| `ajax/run_migrations.php` | API لتحديث القاعدة من الإعدادات |
| `pre_start.php` | بوابة first-run + زر قاعدة جديدة |
| `setting.php` (تبويب قاعدة البيانات) | تحديث + مدد + قفل مدة |
| `classes/InvoiceProcessor.php` | softDelete + insert/updateHeader + journals + payments + replaceDetails |
| `do/dodel_invoice.php` | `InvoiceProcessor::softDelete` |
| `do/doadd_invoice.php` / `do/doedit_invoice.php` | controllers رفيعة تستدعي الـ Processor |
| `includes/PeriodManager.php` | قفل مدة / إنشاء / تبديل |

## مبدأ عملية ot_head

```
ot_head (الرأس)
  ├── fat_details
  ├── journal_heads / journal_entries (op_id)
  └── سندات مرتبطة (ot_head.op2 + قيودها)
```

- الحذف النهائي = soft delete داخل transaction.
- إعادة الكتابة عند التعديل (مسار POS edit داخل doadd) = `purgeRelatedForRewrite` بسبب triggers الأرصدة.
- الحفظ عبر: `insertHeader` / `updateHeader` / `createMainJournal` / `createSplitPaymentVouchers` / `replaceDetails`.

## تعدد المدد

كل مدة = قاعدة MySQL مستقلة عبر `config/db_registry.json`.

**قفل المدة:** نسخ الهيكل + البيانات الأساسية، تخطي جداول الحركة، أرصدة افتتاحية (حسابات 15 + مخازن 14)، ثم التبديل للقاعدة الجديدة.

## ملاحظات نشر السيرفر

1. ارفع الملفات وانسخ `.env` (`DB_HOST`, `DB_USER`, `DB_PASS`, `DB_NAME`).
2. افتح `/pre_start.php` وأنشئ قاعدة أو استعد نسخة.
3. من الإعدادات → قاعدة البيانات → «تحديث قاعدة البيانات» إن وُجدت migrations ناقصة.
4. الملف المرجعي للهيكل: `db/DB.sql` (حساس لحالة الأحرف على Linux).

## مرحلة لاحقة (اختياري)

- اختبارات انحدار لحفظ POS (طاولة / كاش / بنك).
- توحيد مسار تعديل رأس `doadd` عند `edit_id` عبر `updateHeaderForRewrite` — **تم**.
- إظهار المدة النشطة في الـ navbar — **تم**.
