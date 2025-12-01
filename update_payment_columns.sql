-- إضافة أعمدة جديدة لجدول ot_head لتحسين نظام السداد
ALTER TABLE ot_head 
ADD COLUMN IF NOT EXISTS payment_method VARCHAR(20) DEFAULT 'cash',
ADD COLUMN IF NOT EXISTS payment_notes TEXT,
ADD COLUMN IF NOT EXISTS payment_date DATETIME;

-- إنشاء جدول سجلات المدفوعات إذا لم يكن موجوداً
CREATE TABLE IF NOT EXISTS payment_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    table_id INT,
    amount DECIMAL(10,2) NOT NULL,
    payment_method VARCHAR(20) DEFAULT 'cash',
    notes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_order_id (order_id),
    INDEX idx_table_id (table_id)
);

-- إنشاء جدول سجلات الطلبات إذا لم يكن موجوداً
CREATE TABLE IF NOT EXISTS order_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    action VARCHAR(50) NOT NULL,
    notes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_order_id (order_id),
    INDEX idx_action (action)
);

-- تحديث البيانات الموجودة
UPDATE ot_head SET payment_method = 'cash' WHERE payment_method IS NULL;