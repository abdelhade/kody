-- ==========================================
-- Migration: 005 - Optimize Data Types
-- Purpose: تحسين أنواع البيانات للأداء والدقة
-- Date: 2025-10-17
-- ⚠️ CAUTION: قد يستغرق وقتاً على الجداول الكبيرة
-- ⚠️ يُنصح بعمل Backup أولاً
-- ==========================================

USE hrmsnat;

-- ==========================================
-- تحذير: هذه التعديلات قد تؤثر على الأداء مؤقتاً
-- يُنصح بتشغيلها في وقت الصيانة
-- ==========================================

-- ==========================================
-- 1. Optimize ot_head (Invoices)
-- ==========================================

-- تحسين أنواع البيانات المالية
ALTER TABLE `ot_head`
  MODIFY COLUMN `fat_total` DECIMAL(15,2) DEFAULT 0,
  MODIFY COLUMN `fat_disc` DECIMAL(15,2) DEFAULT 0,
  MODIFY COLUMN `fat_tax` DECIMAL(15,2) DEFAULT 0,
  MODIFY COLUMN `fat_plus` DECIMAL(15,2) DEFAULT 0,
  MODIFY COLUMN `pro_value` DECIMAL(15,2) DEFAULT 0,
  MODIFY COLUMN `fat_cost` DECIMAL(15,2) DEFAULT 0,
  MODIFY COLUMN `profit` DECIMAL(15,2) DEFAULT 0;

-- ==========================================
-- 2. Optimize fat_details (Invoice Items)
-- ==========================================

ALTER TABLE `fat_details`
  MODIFY COLUMN `qty_in` DECIMAL(10,2) DEFAULT 0,
  MODIFY COLUMN `qty_out` DECIMAL(10,2) DEFAULT 0,
  MODIFY COLUMN `price` DECIMAL(15,3) NOT NULL,
  MODIFY COLUMN `discount` DECIMAL(15,2) DEFAULT 0,
  MODIFY COLUMN `det_value` DECIMAL(15,2) NOT NULL;

-- ==========================================
-- 3. Optimize myitems (Products)
-- ==========================================

ALTER TABLE `myitems`
  MODIFY COLUMN `itmqty` DECIMAL(10,2) NOT NULL DEFAULT 0,
  MODIFY COLUMN `salesqty` DECIMAL(10,2) DEFAULT 1,
  MODIFY COLUMN `market_price` DECIMAL(15,3) NOT NULL DEFAULT 0,
  MODIFY COLUMN `cost_price` DECIMAL(15,3) NOT NULL DEFAULT 0,
  MODIFY COLUMN `price1` DECIMAL(15,3) NOT NULL DEFAULT 0,
  MODIFY COLUMN `price2` DECIMAL(15,3) NOT NULL DEFAULT 0,
  MODIFY COLUMN `price3` DECIMAL(15,3) NOT NULL DEFAULT 0,
  MODIFY COLUMN `last_price` DECIMAL(15,3) NOT NULL DEFAULT 0;

-- ==========================================
-- 4. Optimize acc_head (Accounts)
-- ==========================================

ALTER TABLE `acc_head`
  MODIFY COLUMN `start_balance` DECIMAL(15,2) NOT NULL DEFAULT 0,
  MODIFY COLUMN `credit` DECIMAL(15,2) NOT NULL DEFAULT 0,
  MODIFY COLUMN `debit` DECIMAL(15,2) NOT NULL DEFAULT 0,
  MODIFY COLUMN `balance` DECIMAL(15,2) NOT NULL DEFAULT 0;

-- ==========================================
-- 5. Optimize journal_entries
-- ==========================================

ALTER TABLE `journal_entries`
  MODIFY COLUMN `debit` DECIMAL(15,2) DEFAULT 0,
  MODIFY COLUMN `credit` DECIMAL(15,2) DEFAULT 0;

SELECT 'Data types optimized successfully!' as status;
SELECT 'تنبيه: تأكد من اختبار النظام بعد هذه التعديلات' as warning;

