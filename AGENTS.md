# Kody — قواعد المشروع

ده المصدر الوحيد لقواعد الشغل في المشروع. أي قاعدة جديدة تتضاف هنا، مش في ملف تاني.

## البيئة
- PHP إجرائي + mysqli على XAMPP (Windows)، الواجهة jQuery + Bootstrap/AdminLTE، عربي RTL.
- الاتصال بقاعدة البيانات من `includes/connect.php` بس (`$conn`). فيه قواعد بيانات كتير (tenants)، فمتفترضش اسم قاعدة بيانات معيّن.
- رسائل الخطأ والتعليقات بالعربي زي باقي الكود.

## تعديلات قاعدة البيانات
- أي تعديل على الـ schema (عمود/جدول/index/بيانات أساسية) يتعمل في ملف migration جديد بس:
  `update/NNN_وصف_قصير.sql` بالرقم اللي بعد آخر ملف.
- الـ migrations بيشغّلها `includes/MigrationRunner.php` (من الإعدادات ← قاعدة البيانات) مرة واحدة لكل قاعدة بيانات.
- ممنوع `ALTER TABLE` / `SHOW COLUMNS ... ADD COLUMN` جوه كود PHP (connect.php أو صفحات do/ أو كلاسات).
- متعدّلش `db/DB.sql` ولا ملفات `backup/` ولا `logs/`.

```sql
-- update/024_acc_head_cash_only.sql
ALTER TABLE `acc_head`
  ADD COLUMN `cash_only` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'تعامل نقدي فقط';
```

## الكود
- استعلامات جديدة بـ prepared statements، مش تجميع strings من `$_POST`.
- أي قاعدة بيزنس تتطبّق في السيرفر (`do/`) حتى لو موجودة في الواجهة؛ الواجهة للراحة بس.
- منطق الفواتير المشترك مكانه `classes/InvoiceProcessor.php`؛ أنواع الفواتير من `InvoiceProcessor::INVOICE_TYPES` مش أرقام سحرية جديدة.
- خصائص الحساب (زي `cash_only`, `price_list`, `is_fund`) تتقري من `acc_head`، مش تتخمّن من الاسم أو الكود.
- افتراضيات المستخدم (`users.def_store/def_fund/def_client/def_supplier/def_emp/def_price_list`) من `classes/UserDefaults.php` بس: أي قائمة مخزن/خزنة/عميل/مورد/موظف/أسعار في مستند جديد تستخدم `UserDefaults::pick()`، وأي حفظ مستند جديد يعمل `UserDefaults::enforceInvoice()` (أو `enforce()`). اللي معندوش `defaults.change` مقفول على افتراضيه.

## الصلاحيات (RBAC على طريقة Spatie)
- الجداول: `roles` و `permissions` و `role_has_permissions`. `usr_pwrs` و `users.usertype` ملغيين، متستخدمهمش. مفيش super admin ولا bypass.
- الفحص من `classes/Can.php` بس: `Can::check('x.y')`، `Can::any('a','b')`، `Can::authorize('x.y')` (بيرفض 403)، `Can::canVisit('page.php?q=..')` (بيطبّق قاعدة المسار).
- أسماء الصلاحيات `entity.action` بالإنجليزي (`view/add/edit/delete`) أو فعل خاص (`items.pricing`, `pos.close_shift`).
- صلاحيات الدور بتتحمّل في السيشن وقت الدخول؛ أي تغيير يسري بعد ما المستخدم يخرج ويدخل.
- `settings.*` و `system.*` محتاجين كمان باسورد فتح الإعدادات (`Can::settingsUnlocked()`)، بيتعمل أوتوماتيك في `guardRoute`.

### لما تضيف feature جديدة
1. **صلاحية جديدة** ← migration جديدة `update/NNN_*.sql` فيها `INSERT IGNORE INTO permissions (name, guard_name, label, group_key, group_name, entity_label, action, sort, created_at, updated_at)` بنفس شكل `029_rbac_tables.sql` (`guard_name = 'web'`, `label` و `group_name` بالعربي، `group_key` = قسم السايد بار). ولو مناسب تتدّي للدور 1 بنفس الـ migration.
2. **كل ملف جديد** (صفحة / `do/` / `ajax/` / `get/` / `api/` / `print/`) لازم يتسجّل في `includes/rbac_routes.php` بالصلاحية بتاعته. ملف do/ajax مش متسجّل = مرفوض. شغّل `tests/check_authorize.php` يطلع 0 مشاكل.
3. **لينك السايد بار** يتحط عادي في `includes/sidebar.php` من غير أي if؛ `Can::filterMenu` بيشيله لو المستخدم مش هيقدر يفتح المسار.
4. **أي زرار أو لينك أو مودال أو فورم لأكشن جوه الصفحة** (إضافة/تعديل/حذف/...) يتلف في PHP من السيرفر بنفس صلاحية الملف اللي بيفتحه — مش إخفاء بالـ JS:
   ```php
   <?php $canEdit = Can::check('jobs.edit'); ?>
   <?php if ($canEdit) { ?> <a href="edit_jop.php?id=<?= $id ?>">...</a> <?php } ?>
   ```
   لو الصلاحية بتتحدد من البارامترات استخدم `Can::canVisit($href)`.
5. **ملف `do/` نفسه** محمي بالمسار، ولو فيه منطق بيختلف حسب النوع (زي الفواتير/الحسابات) يعمل `Can::authorize(...)` جوه.
6. محدش يتعمل له check بالاسم أو برقم الدور أو المستخدم (`userrole == 1` وكده) — دايماً بالصلاحية.

## مرجع سريع
- أنواع الفواتير: 3 مبيعات، 4 مشتريات، 9 كاشير، 10 مردود مشتريات، 11 مردود مبيعات، 12 أمر شراء، 13 أمر بيع، 14 عرض سعر.
- أكواد الحسابات: `122` عملاء، `211` موردين، `121` خزائن، `124` بنوك، `123` مخازن، `213` موظفين.
- `is_fund` معناها خزنة، مش عميل نقدي. العميل/المورد النقدي هو `cash_only = 1`.
