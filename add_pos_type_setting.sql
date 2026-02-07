-- إضافة حقل نوع نظام POS في جدول الإعدادات
ALTER TABLE settings ADD COLUMN pos_type VARCHAR(20) DEFAULT 'barcode' COMMENT 'نوع نظام POS: barcode أو clothes';

-- تحديث القيمة الافتراضية
UPDATE settings SET pos_type = 'barcode' WHERE pos_type IS NULL OR pos_type = '';
