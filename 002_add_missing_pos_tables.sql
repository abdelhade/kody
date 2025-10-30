-- ==========================================
-- Migration: 002 - Add Missing POS Tables
-- Purpose: إضافة الجداول المفقودة لنظام POS متكامل
-- Date: 2025-10-17
-- Safe to run: YES (جداول جديدة فقط)
-- ==========================================

USE hrmsnat;

-- ==========================================
-- 1. Tables Management (تمت إضافته بالفعل في الكود)
-- ==========================================

CREATE TABLE IF NOT EXISTS `tables` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `tname` VARCHAR(255) NOT NULL COMMENT 'اسم الطاولة',
    `table_case` INT NOT NULL DEFAULT 0 COMMENT '0=متاحة, 1+=محجوزة',
    `current_order_id` INT DEFAULT NULL,
    `crtime` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `mdtime` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `isdeleted` TINYINT(1) NOT NULL DEFAULT 0,
    `branch` VARCHAR(255) DEFAULT NULL,
    `tatnet` VARCHAR(255) DEFAULT NULL,
    INDEX `idx_table_case` (`table_case`),
    INDEX `idx_isdeleted` (`isdeleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='جداول المطعم/الكافيه';

-- ==========================================
-- 2. Payment Methods
-- ==========================================

CREATE TABLE IF NOT EXISTS `payment_methods` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `method_name` VARCHAR(50) NOT NULL COMMENT 'اسم طريقة الدفع',
    `method_name_ar` VARCHAR(50) NOT NULL,
    `method_type` ENUM('cash', 'card', 'mobile_wallet', 'bank_transfer', 'credit') NOT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `crtime` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `mdtime` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='طرق الدفع';

-- Insert default payment methods
INSERT IGNORE INTO `payment_methods` (`id`, `method_name`, `method_name_ar`, `method_type`) VALUES
(1, 'Cash', 'نقدي', 'cash'),
(2, 'Visa/Mastercard', 'فيزا/ماستركارد', 'card'),
(3, 'Vodafone Cash', 'فودافون كاش', 'mobile_wallet'),
(4, 'Bank Transfer', 'تحويل بنكي', 'bank_transfer'),
(5, 'Credit/Deferred', 'آجل', 'credit');

-- ==========================================
-- 3. Invoice Payments (Multiple payments per invoice)
-- ==========================================

CREATE TABLE IF NOT EXISTS `invoice_payments` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `invoice_id` INT NOT NULL COMMENT 'ot_head.id',
    `payment_method_id` INT NOT NULL,
    `amount` DECIMAL(15,2) NOT NULL,
    `reference_number` VARCHAR(100) COMMENT 'رقم المعاملة/الشيك',
    `payment_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `notes` TEXT,
    `user_id` INT,
    `crtime` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_invoice` (`invoice_id`),
    INDEX `idx_method` (`payment_method_id`),
    INDEX `idx_date` (`payment_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='سجل المدفوعات لكل فاتورة';

-- ==========================================
-- 4. Stock Movements Detailed Log
-- ==========================================

CREATE TABLE IF NOT EXISTS `stock_movements_log` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `item_id` INT NOT NULL,
    `movement_type` ENUM('in', 'out', 'transfer', 'adjustment', 'return', 'damaged') NOT NULL,
    `quantity` DECIMAL(10,2) NOT NULL,
    `quantity_before` DECIMAL(10,2),
    `quantity_after` DECIMAL(10,2),
    `reference_type` VARCHAR(50) COMMENT 'sale, purchase, return, adjustment',
    `reference_id` INT COMMENT 'fat_details.id أو أي مرجع آخر',
    `store_id` INT,
    `notes` TEXT,
    `user_id` INT,
    `crtime` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_item` (`item_id`),
    INDEX `idx_type` (`movement_type`),
    INDEX `idx_reference` (`reference_type`, `reference_id`),
    INDEX `idx_date` (`crtime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='سجل حركات المخزون التفصيلي';

-- ==========================================
-- 5. Offers & Promotions
-- ==========================================

CREATE TABLE IF NOT EXISTS `offers` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `offer_name` VARCHAR(100) NOT NULL,
    `offer_name_ar` VARCHAR(100),
    `offer_type` ENUM('fixed_discount', 'percentage_discount', 'buy_x_get_y', 'bundle') NOT NULL,
    `discount_value` DECIMAL(10,2) DEFAULT 0,
    `item_id` INT COMMENT 'الصنف المطبق عليه العرض',
    `min_quantity` INT DEFAULT 1,
    `start_date` DATE,
    `end_date` DATE,
    `is_active` TINYINT(1) DEFAULT 1,
    `crtime` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `mdtime` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `isdeleted` TINYINT(1) DEFAULT 0,
    INDEX `idx_item` (`item_id`),
    INDEX `idx_dates` (`start_date`, `end_date`),
    INDEX `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='العروض والخصومات';

-- ==========================================
-- 6. Cash Register Sessions
-- ==========================================

CREATE TABLE IF NOT EXISTS `cash_register_sessions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `branch_id` INT,
    `fund_id` INT COMMENT 'الصندوق - acc_head.id',
    `opening_balance` DECIMAL(15,2) DEFAULT 0,
    `expected_cash` DECIMAL(15,2) DEFAULT 0,
    `actual_cash` DECIMAL(15,2) DEFAULT 0,
    `difference` DECIMAL(15,2) DEFAULT 0,
    `total_sales` DECIMAL(15,2) DEFAULT 0,
    `total_expenses` DECIMAL(15,2) DEFAULT 0,
    `opened_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `closed_at` TIMESTAMP NULL,
    `status` ENUM('open', 'closed') DEFAULT 'open',
    `notes` TEXT,
    INDEX `idx_user` (`user_id`),
    INDEX `idx_status` (`status`),
    INDEX `idx_opened_at` (`opened_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='جلسات فتح وإغلاق الصندوق';

-- ==========================================
-- 7. Audit Trail / Activity Log
-- ==========================================

CREATE TABLE IF NOT EXISTS `audit_logs` (
    `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT,
    `action` VARCHAR(50) NOT NULL COMMENT 'create, update, delete, login, logout',
    `table_name` VARCHAR(50),
    `record_id` INT,
    `old_values` JSON,
    `new_values` JSON,
    `ip_address` VARCHAR(45),
    `user_agent` TEXT,
    `crtime` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_user` (`user_id`),
    INDEX `idx_action` (`action`),
    INDEX `idx_table` (`table_name`, `record_id`),
    INDEX `idx_date` (`crtime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='سجل التدقيق ونشاطات المستخدمين';

-- ==========================================
-- 8. Return Invoices
-- ==========================================

CREATE TABLE IF NOT EXISTS `return_invoices` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `original_invoice_id` INT NOT NULL COMMENT 'ot_head.id',
    `return_number` VARCHAR(50) UNIQUE,
    `return_date` DATE NOT NULL,
    `return_reason` TEXT,
    `total_amount` DECIMAL(15,2) NOT NULL,
    `refund_method` VARCHAR(50),
    `user_id` INT,
    `crtime` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `mdtime` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `isdeleted` TINYINT(1) DEFAULT 0,
    INDEX `idx_original_invoice` (`original_invoice_id`),
    INDEX `idx_return_date` (`return_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='فواتير المرتجعات';

-- ==========================================
-- 9. Return Items Details
-- ==========================================

CREATE TABLE IF NOT EXISTS `return_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `return_invoice_id` INT NOT NULL,
    `original_sale_item_id` INT COMMENT 'fat_details.id',
    `item_id` INT NOT NULL,
    `quantity` DECIMAL(10,2) NOT NULL,
    `unit_price` DECIMAL(15,3) NOT NULL,
    `line_total` DECIMAL(15,2) NOT NULL,
    `return_reason` VARCHAR(200),
    `crtime` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_return_invoice` (`return_invoice_id`),
    INDEX `idx_item` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='تفاصيل أصناف المرتجعات';

-- ==========================================
-- 10. Product Variants (للمقاسات والألوان)
-- ==========================================

CREATE TABLE IF NOT EXISTS `product_variants` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `product_id` INT NOT NULL COMMENT 'myitems.id',
    `variant_name` VARCHAR(100) NOT NULL COMMENT 'مثل: كبير، وسط، صغير',
    `variant_name_ar` VARCHAR(100),
    `barcode` VARCHAR(100) UNIQUE,
    `price_modifier` DECIMAL(10,2) DEFAULT 0 COMMENT 'فرق السعر عن المنتج الأصلي',
    `is_active` TINYINT(1) DEFAULT 1,
    `crtime` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `mdtime` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `isdeleted` TINYINT(1) DEFAULT 0,
    INDEX `idx_product` (`product_id`),
    INDEX `idx_barcode` (`barcode`),
    INDEX `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='أشكال المنتج (مقاسات، ألوان، إلخ)';

-- ==========================================
-- 11. Customer Loyalty Points Log
-- ==========================================

CREATE TABLE IF NOT EXISTS `loyalty_transactions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `customer_id` INT NOT NULL COMMENT 'clients.id أو acc_head.id',
    `invoice_id` INT COMMENT 'ot_head.id',
    `points_earned` INT DEFAULT 0,
    `points_redeemed` INT DEFAULT 0,
    `balance_before` INT DEFAULT 0,
    `balance_after` INT DEFAULT 0,
    `transaction_type` ENUM('earn', 'redeem', 'adjustment', 'expire') NOT NULL,
    `notes` TEXT,
    `crtime` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_customer` (`customer_id`),
    INDEX `idx_invoice` (`invoice_id`),
    INDEX `idx_date` (`crtime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='سجل نقاط الولاء';

-- ==========================================
-- 12. Kitchen Display System (للمطاعم)
-- ==========================================

CREATE TABLE IF NOT EXISTS `kitchen_orders` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `invoice_id` INT NOT NULL,
    `table_id` INT,
    `order_items` JSON COMMENT 'الأصناف المطلوبة',
    `status` ENUM('pending', 'preparing', 'ready', 'served') DEFAULT 'pending',
    `priority` INT DEFAULT 0,
    `estimated_time` INT COMMENT 'بالدقائق',
    `notes` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_invoice` (`invoice_id`),
    INDEX `idx_status` (`status`),
    INDEX `idx_priority` (`priority`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='نظام عرض المطبخ';

-- ==========================================
-- 13. Daily Sales Summary (للتقارير السريعة)
-- ==========================================

CREATE TABLE IF NOT EXISTS `daily_sales_summary` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `summary_date` DATE NOT NULL,
    `branch_id` INT,
    `total_invoices` INT DEFAULT 0,
    `total_sales` DECIMAL(15,2) DEFAULT 0,
    `total_cost` DECIMAL(15,2) DEFAULT 0,
    `total_profit` DECIMAL(15,2) DEFAULT 0,
    `total_discount` DECIMAL(15,2) DEFAULT 0,
    `total_tax` DECIMAL(15,2) DEFAULT 0,
    `cash_sales` DECIMAL(15,2) DEFAULT 0,
    `card_sales` DECIMAL(15,2) DEFAULT 0,
    `credit_sales` DECIMAL(15,2) DEFAULT 0,
    `crtime` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `mdtime` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `unique_daily_summary` (`summary_date`, `branch_id`),
    INDEX `idx_date` (`summary_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='ملخص المبيعات اليومي';

-- ==========================================
-- 14. Price History (تتبع تغييرات الأسعار)
-- ==========================================

CREATE TABLE IF NOT EXISTS `price_history` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `item_id` INT NOT NULL,
    `price_type` VARCHAR(20) COMMENT 'price1, price2, price3, cost_price',
    `old_price` DECIMAL(15,3),
    `new_price` DECIMAL(15,3),
    `changed_by` INT,
    `change_reason` TEXT,
    `crtime` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_item` (`item_id`),
    INDEX `idx_date` (`crtime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='سجل تغييرات الأسعار';

-- ==========================================
-- 15. Expenses Categories
-- ==========================================

CREATE TABLE IF NOT EXISTS `expense_categories` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `category_name` VARCHAR(100) NOT NULL,
    `category_name_ar` VARCHAR(100),
    `parent_id` INT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `crtime` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `isdeleted` TINYINT(1) DEFAULT 0,
    INDEX `idx_parent` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='تصنيفات المصروفات';

-- ==========================================
-- 16. Expenses
-- ==========================================

CREATE TABLE IF NOT EXISTS `expenses` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `expense_number` VARCHAR(50) UNIQUE,
    `expense_date` DATE NOT NULL,
    `category_id` INT,
    `amount` DECIMAL(15,2) NOT NULL,
    `payment_method_id` INT,
    `fund_id` INT COMMENT 'الصندوق المدفوع منه',
    `description` TEXT,
    `receipt_image` VARCHAR(255),
    `approved_by` INT,
    `user_id` INT,
    `branch_id` INT,
    `crtime` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `mdtime` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `isdeleted` TINYINT(1) DEFAULT 0,
    INDEX `idx_date` (`expense_date`),
    INDEX `idx_category` (`category_id`),
    INDEX `idx_branch` (`branch_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='المصروفات';

-- ==========================================
-- 17. Inventory Transfers
-- ==========================================

CREATE TABLE IF NOT EXISTS `inventory_transfers` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `transfer_number` VARCHAR(50) UNIQUE,
    `transfer_date` DATE NOT NULL,
    `from_store_id` INT NOT NULL,
    `to_store_id` INT NOT NULL,
    `status` ENUM('pending', 'in_transit', 'received', 'cancelled') DEFAULT 'pending',
    `notes` TEXT,
    `created_by` INT,
    `received_by` INT,
    `crtime` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `mdtime` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `isdeleted` TINYINT(1) DEFAULT 0,
    INDEX `idx_from_store` (`from_store_id`),
    INDEX `idx_to_store` (`to_store_id`),
    INDEX `idx_status` (`status`),
    INDEX `idx_date` (`transfer_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='نقل المخزون بين الفروع/المخازن';

-- ==========================================
-- 18. Inventory Transfer Items
-- ==========================================

CREATE TABLE IF NOT EXISTS `inventory_transfer_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `transfer_id` INT NOT NULL,
    `item_id` INT NOT NULL,
    `quantity_sent` DECIMAL(10,2) NOT NULL,
    `quantity_received` DECIMAL(10,2) DEFAULT 0,
    `notes` VARCHAR(255),
    `crtime` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_transfer` (`transfer_id`),
    INDEX `idx_item` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='تفاصيل أصناف النقل';

-- ==========================================
-- 19. Customer Addresses (للتوصيل)
-- ==========================================

CREATE TABLE IF NOT EXISTS `customer_addresses` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `customer_id` INT NOT NULL,
    `address_label` VARCHAR(50) COMMENT 'المنزل، العمل، إلخ',
    `address_line1` VARCHAR(255),
    `address_line2` VARCHAR(255),
    `city` VARCHAR(50),
    `district` VARCHAR(50),
    `building_number` VARCHAR(20),
    `floor_number` VARCHAR(10),
    `apartment_number` VARCHAR(10),
    `phone` VARCHAR(20),
    `is_default` TINYINT(1) DEFAULT 0,
    `latitude` DECIMAL(10,8),
    `longitude` DECIMAL(11,8),
    `crtime` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `mdtime` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `isdeleted` TINYINT(1) DEFAULT 0,
    INDEX `idx_customer` (`customer_id`),
    INDEX `idx_default` (`is_default`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='عناوين العملاء للتوصيل';

-- ==========================================
-- 20. Delivery Orders
-- ==========================================

CREATE TABLE IF NOT EXISTS `delivery_orders` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `invoice_id` INT NOT NULL,
    `customer_address_id` INT,
    `driver_id` INT COMMENT 'الموظف المسؤول عن التوصيل',
    `delivery_status` ENUM('pending', 'assigned', 'picked_up', 'on_the_way', 'delivered', 'cancelled') DEFAULT 'pending',
    `delivery_fee` DECIMAL(10,2) DEFAULT 0,
    `estimated_time` INT COMMENT 'بالدقائق',
    `picked_up_at` TIMESTAMP NULL,
    `delivered_at` TIMESTAMP NULL,
    `notes` TEXT,
    `crtime` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `mdtime` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_invoice` (`invoice_id`),
    INDEX `idx_driver` (`driver_id`),
    INDEX `idx_status` (`delivery_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='طلبات التوصيل';

SELECT 'Missing POS tables created successfully!' as status;

