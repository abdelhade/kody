-- =====================================================
-- تحديثات نظام الطاولات - Tables System Updates
-- =====================================================

-- 1. إضافة حقل table_id في جدول الفواتير
ALTER TABLE `ot_head` 
ADD COLUMN `table_id` INT(11) NULL AFTER `id`,
ADD INDEX `idx_table_id` (`table_id`);

-- 2. إضافة حقل order_status في جدول الفواتير
ALTER TABLE `ot_head` 
ADD COLUMN `order_status` ENUM('draft','active','completed','cancelled') DEFAULT 'active' AFTER `table_id`;

-- 3. إضافة فهرس على order_status للاستعلامات السريعة
ALTER TABLE `ot_head` 
ADD INDEX `idx_order_status` (`order_status`);

-- 4. إضافة فهرس مركب لتسريع البحث عن الطلبات النشطة للطاولات
ALTER TABLE `ot_head` 
ADD INDEX `idx_table_status` (`table_id`, `order_status`);

-- =====================================================
-- ملاحظات:
-- - table_id: معرف الطاولة (NULL = بدون طاولة، مثل طلبات التيك أواي)
-- - order_status:
--   * draft: مسودة (لم يتم حفظها بعد)
--   * active: نشط (قيد التنفيذ، الطاولة مشغولة)
--   * completed: مكتمل (تم الدفع والإغلاق)
--   * cancelled: ملغي
-- =====================================================

