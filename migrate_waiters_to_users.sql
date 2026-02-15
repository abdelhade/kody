-- =====================================================
-- Migrate Waiter System to Users Table
-- تاريخ: 2026-02-12
-- الوصف: ترحيل نظام الويترز من جدول منفصل إلى جدول المستخدمين
-- =====================================================

-- الخطوة 1: إضافة عمود is_waiter لجدول users
-- =====================================================
ALTER TABLE `users` 
ADD COLUMN `is_waiter` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 = ويتر، 0 = مستخدم عادي' AFTER `userrole`;

-- إضافة فهرس للأداء
ALTER TABLE `users` 
ADD KEY `is_waiter` (`is_waiter`);

-- =====================================================
-- الخطوة 2 (اختياري): ترحيل بيانات الويترز الحاليين
-- قم بتشغيل هذا فقط إذا كنت تريد الاحتفاظ بالويترز الحاليين
-- تحذير: تأكد من وجود جدول waiters قبل تنفيذ هذا القسم
-- =====================================================

-- UNCOMMENT THE FOLLOWING IF YOU WANT TO MIGRATE EXISTING WAITERS:
/*
-- التحقق من وجود جدول waiters أولاً
INSERT INTO `users` (`uname`, `password`, `usertype`, `userrole`, `is_waiter`, `img`, `isdeleted`)
SELECT 
    `name` as `uname`,
    MD5(`barcode`) as `password`,  -- تحويل الباركود إلى باسورد مشفر
    1 as `usertype`,  -- نوع المستخدم (عدّل حسب نظامك)
    1 as `userrole`,  -- دور المستخدم (عدّل حسب نظامك)
    1 as `is_waiter`,
    'default.png' as `img`,
    `isdeleted`
FROM `waiters`
WHERE `isdeleted` = 0
AND NOT EXISTS (
    SELECT 1 FROM `users` 
    WHERE `users`.`password` = MD5(`waiters`.`barcode`)
);
*/

-- =====================================================
-- الخطوة 3: حذف جدول waiters (اختياري)
-- تحذير: سيتم حذف جميع البيانات في جدول waiters نهائياً
-- قم بعمل نسخة احتياطية أولاً!
-- =====================================================

-- UNCOMMENT THE FOLLOWING TO DROP THE OLD WAITERS TABLE:
-- DROP TABLE IF EXISTS `waiters`;

-- =====================================================
-- ملاحظات مهمة:
-- =====================================================
-- 1. الباسورد الآن يُستخدم كباركود (مشفر MD5)
-- 2. عند مسح الباركود، سيتم البحث عن المستخدم بـ MD5(barcode)
-- 3. يجب أن يكون is_waiter = 1 للويترز
-- 4. عمود waiter_id في جدول ot_head سيشير الآن إلى users.id
-- 5. يمكن إدارة الويترز من خلال نظام إدارة المستخدمين العادي
-- =====================================================

-- =====================================================
-- مثال: إضافة ويتر جديد
-- =====================================================
-- INSERT INTO `users` (`uname`, `password`, `usertype`, `userrole`, `is_waiter`, `img`) 
-- VALUES ('waiter1', MD5('1234'), 1, 1, 1, 'default.png');
-- 
-- الباركود للدخول سيكون: 1234
-- =====================================================


-- =====================================================
-- خطوات ما بعد الترحيل
-- =====================================================

-- 1. اختبار النظام
-- افتح: test_waiter_system.php للتحقق من نجاح الترحيل

-- 2. إضافة ويتر تجريبي (اختياري)
-- INSERT INTO `users` (`uname`, `password`, `is_waiter`, `userrole`) 
-- VALUES ('ويتر تجريبي', MD5('1234'), 1, 1);

-- 3. تسجيل دخول الويتر
-- افتح: waiter_login.php
-- الباركود: 1234

-- =====================================================
-- استعلامات مفيدة بعد الترحيل
-- =====================================================

-- عرض جميع الويترز
-- SELECT id, uname, is_waiter FROM users WHERE is_waiter = 1 AND isdeleted = 0;

-- تحويل مستخدم إلى ويتر
-- UPDATE users SET is_waiter = 1 WHERE id = ?;

-- إلغاء صفة الويتر
-- UPDATE users SET is_waiter = 0 WHERE id = ?;

-- عرض طلبات ويتر معين
-- SELECT oh.*, u.uname FROM ot_head oh 
-- JOIN users u ON oh.waiter_id = u.id 
-- WHERE oh.waiter_id = ? AND u.is_waiter = 1;

-- إحصائيات المبيعات لكل ويتر
-- SELECT u.uname, COUNT(oh.id) as orders, SUM(oh.net) as sales
-- FROM users u
-- LEFT JOIN ot_head oh ON u.id = oh.waiter_id
-- WHERE u.is_waiter = 1
-- GROUP BY u.id
-- ORDER BY sales DESC;

-- =====================================================
-- انتهى ملف الترحيل
-- للمزيد من المعلومات، راجع:
-- - WAITER_MIGRATION_GUIDE_AR.md (دليل شامل)
-- - WAITER_SYSTEM_README_AR.md (دليل سريع)
-- - README_WAITER_INTEGRATION.md (ملخص)
-- =====================================================
