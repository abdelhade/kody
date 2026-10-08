# خطة تحسين الإعدادات والمستخدمين والواجهة (Kody)

مرجع تنفيذي للنظام الحالي (`kody` PHP) — production.
كل مرحلة تتسلّم لوحدها، وكل تعديل على الداتابيز يكون ملف `update/0NN_*.sql` ويتطبّق من `run_db_update.php` / زر التحديث في الإعدادات.
آخر ملف موجود حالياً: `021` ← أول ملف جديد في الخطة دي هو `022`.

---

## ترتيب التنفيذ

| # | المرحلة | الأولوية | الحجم |
| :---: | :--- | :---: | :---: |
| 0 | سد ثغرات الأمان في المستخدمين والإعدادات | عاجل | صغير |
| 1 | الإعدادات (Backend + UX) | عالي | متوسط |
| 2 | المستخدمين والأدوار | عالي | كبير |
| 3 | الواجهة (UI موحّد + أداء) | متوسط | كبير |
| 4 | الاختبار والتسليم | مستمر | — |

---

## المرحلة 0 — أمان عاجل

### ما تم رصده

| الملف | المشكلة |
| :--- | :--- |
| `do/*.php` (165 ملف) | ملفين بس بيتحققوا من `$role`. أي مستخدم مسجّل دخول يقدر يحذف/يعدّل أي حاجة بفتح اللينك مباشرة |
| `do/do_deluser.php` | حذف نهائي بـ GET، `$_GET['id']` داخل SQL مباشرة (SQL Injection)، بدون صلاحية، وممكن يحذف نفسه أو آخر أدمن |
| `edit_user.php` | `$id = $_GET['id']` داخل SQL مباشرة، و`uname` بيتطبع بدون escape (XSS) |
| `do/doedit_user.php` | بدون صلاحية؛ أي مستخدم يقدر يغيّر `userrole` لنفسه (رفع صلاحيات). الباسورد بيتخزن `md5` |
| `edit_role.php` | "الحماية" بـ `md5(id)` في اللينك مش حماية؛ مفيش فحص صلاحية |
| `setting.php` | كلمة سر ثابتة في الكود `hadi@1234` وبدون فحص صلاحية |
| `do/doedit_settings.php` | مفيش فحص صلاحية خالص (أي مستخدم يعمل POST)، وبيكتب كل الـ POST في `error_log` بما فيه `edit_pass` |
| `users.php` | بيعرض المستخدمين المحذوفين (`isdeleted`) |
| جذر المشروع | سكربتات debug/fix مكشوفة: `fix_passwords.php`, `debug_*.php`, `check_*.php`, `alter_db.php`, `delete_fix_file.php` |

### التنفيذ

1. ملف جديد `includes/auth.php`:
   - `kody_require_login()`
   - `kody_can(string $perm): bool` — يقرا من `$role`
   - `kody_require_perm(string $perm)` — يرجّع 403 (JSON لو ajax، صفحة لو عادي)
   - `kody_csrf_token()` / `kody_csrf_check()`
2. تطبيقه فوراً على: `do_deluser`, `doadd_user`, `doedit_user`, `doedit_userprev`, `dochange_password`, `doadd_role`, `doedit_role`, `dodel_role`, `doedit_settings`, و`ajax/` الخاصة بالداتابيز (`db_setup`, `run_migrations`, `period_ops`, `git_pull`, `save_license`).
3. حذف المستخدم: POST + CSRF + صلاحية `delete_users` + soft delete (`isdeleted = 1`) + منع حذف النفس وآخر مستخدم عنده `show_users`.
4. كل `$_GET['id']` في ملفات المستخدمين والأدوار → `(int)` أو prepared statement، وكل طباعة → `htmlspecialchars`.
5. تغيير `userrole` مسموح بس لمن عنده `edit_users`، والمستخدم ميقدرش يغيّر دوره بنفسه.
6. الإعدادات: صلاحية جديدة `manage_settings` بدل كلمة السر الثابتة (مع إعادة إدخال باسورد المستخدم نفسه اختيارياً للعمليات الخطرة).
7. شيل `error_log(print_r($_POST))` من `doedit_settings.php`.
8. منع سكربتات debug/fix من `.htaccess` أو نقلها خارج الـ webroot.

**DB:** `022_usr_pwrs_manage_settings.sql` — إضافة `manage_settings` و`manage_system` لـ `usr_pwrs` (default 0، و1 للدور رقم 1).

---

## المرحلة 1 — الإعدادات

### مشاكل موجودة

- **السمة (Theme) مش شغالة:** `setting.php` بيحفظ `solarized_white / monokai / tokyo_night`، لكن `includes/header.php` بيتوقع لون hex فبيرجع للافتراضي دايماً، و`dist/css/themes.css` مش متضمّن في أي صفحة.
- **`ALTER TABLE` وقت التشغيل:** `includes/connect.php` بيعمل ~7 استعلامات `SHOW COLUMNS` مع **كل طلب** لكل صفحة، و`doedit_settings.php` بيعمل `ALTER` وقت الحفظ. ده مخالف لقاعدة "مصدر واحد للتحديث" وبيبطّأ النظام.
- الحفظ كله في `UPDATE` واحد بـ 31 باراميتر و`bind_param` نصّي طويل — أي عمود جديد بيكسره بسهولة.
- بعد الحفظ بيحوّل للـ dashboard بدل ما يرجع لنفس التبويب. الأخطاء بتطلع بـ `die()` صفحة بيضا.
- ظهور القوائم بيتكتب كأرقام 0/1 بدل مفاتيح تشغيل.
- حسابات POS الافتراضية (عميل/مخزن/موظف/صندوق) بتتكتب ID رقمي يدوي.
- رفع اللوجو بيقبل SVG (ممكن يحمل JavaScript) والامتداد بيتاخد من اسم الملف.
- لغات الواجهة 9 في القائمة، والشغل الفعلي عربي بس.

### التنفيذ

1. **نقل كل الـ ALTER** من `connect.php` و`doedit_settings.php` لملف migration، وحذف الفحوصات من وقت التشغيل.
   **DB:** `023_settings_usr_pwrs_runtime_columns.sql` (بـ `ADD COLUMN IF NOT EXISTS` لنفس الأعمدة: `showpulse`, العمولات، الطباعة، `company_logo`, `receipt_header_text/notes`, `sid_visits`, `show_main_hr`, التوصيل، `sid_cards`, `edit_user_passwords`, `prevent_negative_stock`, `acc_head.price_list`).
2. **`includes/Settings.php`:** `Settings::get($key)` / `Settings::save(array $data)` بقائمة أعمدة مسموحة + نوع كل عمود + validation، والـ UPDATE يتبني ديناميكياً.
3. **حفظ لكل تبويب** (الشركة / POS / الطباعة / ...) بدل فورم واحد ضخم، والرجوع لـ `setting.php#tab-xxx` مع رسالة نجاح/خطأ (Toast).
4. **إصلاح السمة:** `body` ياخد class `theme-{bodycolor}` وضم `themes.css` في `header.php`، وتحويل ألوان الواجهة لـ CSS variables عشان الثيمات الداكنة تشتغل فعلاً.
5. ظهور القوائم → `custom-switch`.
6. حسابات POS الافتراضية → `select2` من `acc_head` / المخازن / الموظفين مع البحث.
7. اللوجو: منع SVG، التحقق بالـ MIME، حد أقصى 2MB، وحذف اللوجو القديم.
8. **سجل تغييرات الإعدادات:** مين غيّر إيه وإمتى، يظهر في تبويب "السجل".
   **DB:** `024_settings_audit.sql` — جدول `settings_audit (id, user_id, field, old_value, new_value, created_at)`.
9. فصل العمليات الخطرة (قاعدة البيانات / المدد / Git / الترخيص) في صفحة **"النظام"** بصلاحية `manage_system`، والإعدادات العادية تفضل لـ `manage_settings`.
10. استبدال `alert/confirm/prompt` في الصفحة بـ SweetAlert2 (موجود بالفعل في المشروع).
11. بحث سريع داخل الإعدادات (فلترة الحقول بالاسم).

---

## المرحلة 2 — المستخدمين والأدوار

### مشاكل موجودة

- `usr_pwrs` جدول عريض (~190 عمود)، و`edit_role.php` (579 سطر) مكتوب صف صف يدوي، وأي صلاحية جديدة محتاجة تعديل في 3 أماكن على الأقل.
- الدور بيتقرا من `$_SESSION['usrole']` اللي بيتحط وقت الدخول بس → تغيير دور مستخدم أو تعطيله مش بيسري غير لما يعمل logout.
- `doadd_user` / `doedit_user` / `update_waiter_barcode` بيخزنوا `md5`، و`index.php` بيحوّلها لـ `password_hash` عند الدخول بس.
- الويتر بيستخدم حقل الباسورد كباركود.
- "تذكرني" في صفحة الدخول مش بتعمل حاجة، ومفيش حد لمحاولات الدخول الفاشلة.
- القيم الافتراضية للمستخدم (`def_client/def_fund/def_store/def_emp`) موجودة في الجدول وبيستخدمها `getUserDefault()`، ومحتاجة واجهة واضحة.
- `add_user.php` و`edit_user.php` فورمين منفصلين بنفس الحقول تقريباً.

### التنفيذ

1. **سجل الصلاحيات** `includes/permissions_map.php`: مصفوفة واحدة فيها كل موديول (اسم عربي، أيقونة، أعمدة show/add/edit/delete/fav)، وتبقى المصدر الوحيد لـ:
   - توليد شاشة تعديل الدور تلقائياً (`edit_role.php` من 579 سطر لحوالي 100).
   - حفظ الدور في `doedit_role.php` بلوب واحدة على الأعمدة المسموحة.
   - فلترة الشريط الجانبي.
   *نفضل على الجدول العريض نفسه (نفس الجدول اللي Laravel بيقرا منه) — بدون إعادة تصميم للسكيمة.*
2. شاشة الأدوار: عدد المستخدمين لكل دور، **نسخ دور**، منع حذف دور عليه مستخدمين، "تحديد الكل" لكل موديول ولكل عمود.
3. **تحميل الدور من `users.userrole` في كل طلب** (داخل `header.php` اللي بيجيب المستخدم أصلاً) + logout تلقائي لو المستخدم اتعطّل.
4. **الباسورد:** `password_hash` في كل مسارات الحفظ، وحد أدنى للطول، وزر "إعادة تعيين" للأدمن.
5. **باركود الويتر في عمود مستقل** وفريد بدل حقل الباسورد.
   **DB:** `025_users_waiter_code_status.sql` — `waiter_code VARCHAR(64) NULL UNIQUE`, `is_active TINYINT(1) DEFAULT 1`, `last_login_at DATETIME NULL`, `failed_logins INT DEFAULT 0`, `locked_until DATETIME NULL`.
   (نقل الباركودات الحالية محتاج سكربت تحويل لمرة واحدة، لأن القيم المخزنة حالياً hash مش نص صريح — يتقرر بعد مراجعة `includes/waiter_auth.php`.)
6. **قائمة المستخدمين:** DataTables (بحث/فرز)، عمود الدور بالاسم، الحالة (نشط/معطّل)، آخر دخول، فلتر حسب الدور، وأزرار: تعديل / تعطيل / تفعيل / إعادة تعيين باسورد / باركود الويتر.
7. **فورم موحّد** `user_form.php` للإضافة والتعديل، فيه تبويب "الافتراضيات" (العميل/الصندوق/المخزن/الموظف بـ select2).
8. **صفحة الدخول:** قفل مؤقت بعد 5 محاولات فاشلة، تفعيل "تذكرني" بتوكن آمن أو إزالته، وخيار في الإعدادات لإخفاء قائمة أسماء المستخدمين.
9. **سجل نشاط المستخدمين:** دخول/خروج/تغيير دور/تغيير باسورد.
   **DB:** `026_user_activity_log.sql`.

---

## المرحلة 3 — الواجهة (UI)

### مشاكل موجودة

- `includes/header.php` بيحمّل في كل صفحة: AdminLTE + Bootstrap 4.2 + `tailwind.js` (365KB بيشتغل في المتصفح وقت التشغيل) + animate.css + ~15 plugin، حتى لو الصفحة مش محتاجاهم.
- ملفات مكررة: `bootstrap4.2.min.css` و`bootstrap-rtl.min.css` نفس الحجم بالظبط، و`bootstrap.min.css` كمان.
- كل صفحة فيها `<style>` خاص بيها (`users.php` حوالي 280 سطر CSS، `edit_user.php` حوالي 180) بأسماء classes مختلفة لنفس المكونات.
- `!important` عام في `header.php` (`.nav-link { color:#000 !important }`، override كامل لـ FontAwesome) بيكسر الثيمات الداكنة.
- `includes/sidebar.php` حوالي 1489 سطر HTML يدوي.
- ترتيب الـ includes مش ثابت (`edit_role.php` بيضم sidebar قبل navbar، و`edit_user.php` بيضم `connect.php` مرتين).
- الرسائل خليط من `alert()` و`die()` وSweetAlert.

### التنفيذ

1. **Design tokens:** ملف `assets/styles/kody-ui.css` فيه CSS variables (ألوان، مسافات، radius، ظلال، خط) + المكونات المشتركة: `page-header`, `k-card`, `k-table`, `k-form`, `k-btn`, `empty-state`, `badge`. الثيمات تبدّل الـ variables بس.
2. **Helpers للصفحة:** `kody_page_header($title, $icon, $breadcrumbs, $actions)` و`kody_flash()` (Toast موحّد بـ SweetAlert2) و`kody_confirm()` في JS بدل `confirm()`.
3. **تنظيف `header.php`:** شيل `tailwind.js` (أو استبداله بملف CSS مبني مسبقاً للكلاسات المستخدمة فعلاً)، شيل ملفات Bootstrap المكررة، وتحميل plugins التقارير (datatables/daterangepicker/summernote/jqvmap) بس في الصفحات اللي محتاجاها عن طريق متغير `$pagePlugins`.
4. **شيل `!important` العام** واستبداله بقواعد محددة.
5. **الشريط الجانبي data-driven:** مصفوفة قوائم (عنوان، أيقونة، لينك، مفتاح صلاحية، مفتاح ظهور من الإعدادات) + بحث في القائمة + المفضلة من أعمدة `is_fav_*`.
6. **تطبيق التصميم الجديد بالتدريج:** الإعدادات ← المستخدمين ← الأدوار ← تغيير الباسورد ← الداشبورد، وبعدها باقي الشاشات صفحة صفحة.
7. **Responsive:** الجداول تتحول لكروت على الموبايل، والأزرار الكبيرة في `users.php` (padding 14px 28px) تتظبط.
8. ثبات ترتيب الـ includes: `header → navbar → sidebar → content → footer` في كل الصفحات.

---

## المرحلة 4 — الاختبار والتسليم

- قبل كل مرحلة: backup من تبويب قاعدة البيانات + branch منفصل.
- checklist يدوي لكل مرحلة:
  - مستخدم بدور محدود يحاول يفتح `do/do_deluser.php`, `do/doedit_settings.php`, `edit_role.php` مباشرة ← لازم 403.
  - تغيير دور مستخدم وهو مسجّل دخول ← يسري من الطلب اللي بعده.
  - دخول بباسورد md5 قديم ← يشتغل ويتحوّل لـ hash.
  - دخول الويتر بالباركود في POS.
  - حفظ كل تبويب إعدادات على حدة + الثيمات الثلاثة.
  - POS (باركود / ملابس / سوبرماركت) بعد تنظيف `header.php`.
- تشغيل `run_db_update.php?confirm=yes` على نسخة من قاعدة production قبل السيرفر الحقيقي.

---

## ملفات DB الجديدة (ملخص)

| ملف | المحتوى |
| :--- | :--- |
| `022_usr_pwrs_manage_settings.sql` | صلاحيات `manage_settings` / `manage_system` |
| `023_settings_usr_pwrs_runtime_columns.sql` | نقل كل الـ ALTER من وقت التشغيل |
| `024_settings_audit.sql` | سجل تغييرات الإعدادات |
| `025_users_waiter_code_status.sql` | `waiter_code`, `is_active`, `last_login_at`, قفل المحاولات |
| `026_user_activity_log.sql` | سجل نشاط المستخدمين |
