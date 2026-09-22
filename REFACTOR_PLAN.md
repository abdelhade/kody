# خطة Refactoring — Launch / DB / ot_head (Kody)

مرجع تنفيذي للنظام الحالي (`kody` PHP)، وليس مسار Laravel.

## الأهداف

1. **Launch على السيرفر:** `.env` → `pre_start.php` → إنشاء/استعادة قاعدة → migrations → دخول.
2. **قاعدة جديدة + تحديث:** زر إنشاء من `pre_start`، وزر تحديث من الإعدادات عبر `MigrationRunner`.
3. **توحيد الحفظ/الحذف على `ot_head`:** منطق مركزي في `InvoiceProcessor` (soft delete + helpers).

## المكونات

| ملف | دور |
| :--- | :--- |
| `includes/MigrationRunner.php` | تشغيل ملفات `update/NNN_*.sql` مرة واحدة + جدول `schema_migrations` |
| `ajax/db_setup.php` | إنشاء/استعادة ثم تشغيل migrations الناقصة |
| `ajax/run_migrations.php` | API لتحديث القاعدة من الإعدادات |
| `pre_start.php` | بوابة first-run + زر قاعدة جديدة |
| `setting.php` (تبويب قاعدة البيانات) | زر «تحديث قاعدة البيانات» + حالة الإصدارات |
| `classes/InvoiceProcessor.php` | `softDelete()` + helpers مشتركة للحفظ/الحذف |
| `do/dodel_invoice.php` | يستدعي `InvoiceProcessor::softDelete` فقط |

## مبدأ عملية ot_head

```
ot_head (الرأس)
  ├── fat_details
  ├── journal_heads / journal_entries (op_id)
  └── سندات مرتبطة (ot_head.op2 + قيودها)
```

- الحذف = soft delete (`isdeleted = 1`) داخل transaction.
- الحفظ/التعديل يمر عبر نفس الـ helpers قدر الإمكان (تدريجياً من `doadd` / `doedit`).

## ترتيب التنفيذ

1. MigrationRunner + ملفات 013–016
2. ربط launch والإعدادات
3. `softDelete` ثم استبدال منطق الحذف المكرر
4. توسيع helpers الحفظ تدريجياً

## تعدد المدد (Multi-Period Databases)

كل مدة مالية = قاعدة MySQL مستقلة، مربوطة عبر `config/db_registry.json`.

| عنصر | الوظيفة |
| :--- | :--- |
| `includes/PeriodManager.php` | قفل مدة / إنشاء قاعدة / تبديل / سجل |
| `ajax/period_ops.php` | API: `list` `switch` `create` `close` |
| الإعدادات → قاعدة البيانات | Select للمدد + زر قفل + زر قاعدة جديدة |
| `update/017_period_meta.sql` | جدول `period_meta` داخل كل قاعدة |

**قفل المدة:** ينسخ الهيكل + البيانات الأساسية، يتخطى جداول الحركة (`ot_head`, `fat_details`, `journal_*`…)، يكتب أرصدة افتتاحية حسابات (`pro_tybe=15`) ومخازن (`pro_tybe=14`)، ثم يبدّل للعمل على القاعدة الجديدة.

1. ارفع الملفات وانسخ `.env` (`DB_HOST`, `DB_USER`, `DB_PASS`, `DB_NAME`).
2. افتح `/pre_start.php` وأنشئ قاعدة أو استعد نسخة.
3. من الإعدادات → قاعدة البيانات → «تحديث قاعدة البيانات» إن وُجدت migrations ناقصة.
4. الملف المرجعي للهيكل: `db/DB.sql` (حساس لحالة الأحرف على Linux).
