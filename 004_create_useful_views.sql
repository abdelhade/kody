-- ==========================================
-- Migration: 004 - Create Useful Views
-- Purpose: إنشاء Views للتقارير والاستعلامات المتكررة
-- Date: 2025-10-17
-- Safe to run: YES (views فقط)
-- ==========================================

USE hrmsnat;

-- ==========================================
-- 1. Products with Images View
-- ==========================================

CREATE OR REPLACE VIEW `vw_products_with_images` AS
SELECT 
    m.id,
    m.iname,
    m.barcode,
    m.price1,
    m.price2,
    m.price3,
    m.cost_price,
    m.itmqty as stock_quantity,
    m.group1,
    m.group2,
    m.group3,
    g1.gname as category_name,
    g2.gname as category2_name,
    g3.gname as category3_name,
    i.iname as image_filename,
    m.info,
    m.isdeleted,
    m.crtime,
    m.mdtime
FROM myitems m
LEFT JOIN item_group g1 ON m.group1 = g1.id
LEFT JOIN item_group2 g2 ON m.group2 = g2.id
LEFT JOIN item_group3 g3 ON m.group3 = g3.id
LEFT JOIN (
    SELECT itemid, iname, MIN(id) as img_id
    FROM imgs
    GROUP BY itemid
) i ON m.id = i.itemid
WHERE m.isdeleted = 0;

-- ==========================================
-- 2. Sales Summary View
-- ==========================================

CREATE OR REPLACE VIEW `vw_sales_summary` AS
SELECT 
    o.id as invoice_id,
    o.pro_num as invoice_number,
    o.pro_date as invoice_date,
    o.pro_tybe as invoice_type,
    c.aname as customer_name,
    c.phone as customer_phone,
    e.aname as employee_name,
    s.aname as store_name,
    o.fat_total as total,
    o.fat_disc as discount,
    o.fat_tax as tax,
    (o.fat_total - IFNULL(o.fat_disc, 0) + IFNULL(o.fat_tax, 0)) as net_total,
    o.info as notes,
    o.crtime,
    o.mdtime
FROM ot_head o
LEFT JOIN acc_head c ON o.acc1 = c.id
LEFT JOIN acc_head e ON o.emp_id = e.id
LEFT JOIN acc_head s ON o.store_id = s.id
ORDER BY o.pro_date DESC, o.id DESC;

-- ==========================================
-- 3. Invoice Items Detail View
-- ==========================================

CREATE OR REPLACE VIEW `vw_invoice_items` AS
SELECT 
    fd.id as detail_id,
    fd.pro_id as invoice_id,
    o.pro_num as invoice_number,
    o.pro_date as invoice_date,
    m.id as item_id,
    m.iname as item_name,
    m.barcode,
    fd.qty_out as quantity,
    fd.price as unit_price,
    fd.discount,
    fd.det_value as line_total,
    fd.cost_price,
    (fd.det_value - (fd.qty_out * IFNULL(fd.cost_price, m.cost_price))) as profit,
    fd.crtime
FROM fat_details fd
INNER JOIN myitems m ON fd.item_id = m.id
LEFT JOIN ot_head o ON fd.pro_id = o.id
WHERE IFNULL(fd.isdeleted, 0) = 0
ORDER BY fd.crtime DESC;

-- ==========================================
-- 4. Stock Status View
-- ==========================================

CREATE OR REPLACE VIEW `vw_stock_status` AS
SELECT 
    m.id,
    m.iname,
    m.barcode,
    m.itmqty as current_stock,
    m.reorder_level,
    CASE 
        WHEN m.itmqty <= 0 THEN 'out_of_stock'
        WHEN m.itmqty <= IFNULL(m.reorder_level, 0) THEN 'low_stock'
        ELSE 'in_stock'
    END as stock_status,
    m.cost_price,
    m.price1,
    (m.itmqty * m.cost_price) as stock_value,
    g.gname as category_name,
    m.mdtime as last_updated
FROM myitems m
LEFT JOIN item_group g ON m.group1 = g.id
WHERE m.isdeleted = 0
ORDER BY 
    CASE 
        WHEN m.itmqty <= 0 THEN 1
        WHEN m.itmqty <= IFNULL(m.reorder_level, 0) THEN 2
        ELSE 3
    END,
    m.iname;

-- ==========================================
-- 5. Top Selling Products View
-- ==========================================

CREATE OR REPLACE VIEW `vw_top_selling_products` AS
SELECT 
    m.id,
    m.iname,
    m.barcode,
    COUNT(fd.id) as times_sold,
    SUM(fd.qty_out) as total_quantity_sold,
    SUM(fd.det_value) as total_revenue,
    SUM(fd.qty_out * IFNULL(fd.cost_price, m.cost_price)) as total_cost,
    SUM(fd.det_value - (fd.qty_out * IFNULL(fd.cost_price, m.cost_price))) as total_profit,
    AVG(fd.price) as avg_selling_price,
    MAX(fd.crtime) as last_sold_date
FROM myitems m
INNER JOIN fat_details fd ON m.id = fd.item_id
WHERE m.isdeleted = 0 
  AND IFNULL(fd.isdeleted, 0) = 0
  AND fd.qty_out > 0
GROUP BY m.id
ORDER BY total_quantity_sold DESC;

-- ==========================================
-- 6. Daily Sales Report View
-- ==========================================

CREATE OR REPLACE VIEW `vw_daily_sales_report` AS
SELECT 
    DATE(o.pro_date) as sale_date,
    COUNT(DISTINCT o.id) as total_invoices,
    SUM(o.fat_total) as total_sales,
    SUM(IFNULL(o.fat_disc, 0)) as total_discounts,
    SUM(IFNULL(o.fat_tax, 0)) as total_tax,
    SUM(o.fat_total - IFNULL(o.fat_disc, 0) + IFNULL(o.fat_tax, 0)) as net_sales,
    COUNT(DISTINCT o.acc1) as unique_customers
FROM ot_head o
WHERE o.pro_tybe = 9  -- نوع فاتورة المبيعات
GROUP BY DATE(o.pro_date)
ORDER BY sale_date DESC;

-- ==========================================
-- 7. Customer Purchase History
-- ==========================================

CREATE OR REPLACE VIEW `vw_customer_purchase_history` AS
SELECT 
    c.id as customer_id,
    c.aname as customer_name,
    c.phone,
    COUNT(o.id) as total_orders,
    SUM(o.fat_total) as total_spent,
    AVG(o.fat_total) as avg_order_value,
    MAX(o.pro_date) as last_purchase_date,
    MIN(o.pro_date) as first_purchase_date,
    DATEDIFF(CURDATE(), MAX(o.pro_date)) as days_since_last_purchase
FROM acc_head c
LEFT JOIN ot_head o ON c.id = o.acc1
WHERE c.code LIKE '122%'  -- العملاء
  AND c.isdeleted = 0
GROUP BY c.id
ORDER BY total_spent DESC;

-- ==========================================
-- 8. Low Stock Alert View
-- ==========================================

CREATE OR REPLACE VIEW `vw_low_stock_alert` AS
SELECT 
    m.id,
    m.iname,
    m.barcode,
    m.itmqty as current_stock,
    m.reorder_level,
    (m.reorder_level - m.itmqty) as shortage,
    g.gname as category,
    m.price1,
    m.cost_price,
    m.mdtime as last_updated
FROM myitems m
LEFT JOIN item_group g ON m.group1 = g.id
WHERE m.isdeleted = 0
  AND m.itmqty <= IFNULL(m.reorder_level, 0)
  AND m.itmqty >= 0
ORDER BY (m.reorder_level - m.itmqty) DESC;

-- ==========================================
-- 9. Employee Sales Performance
-- ==========================================

CREATE OR REPLACE VIEW `vw_employee_sales_performance` AS
SELECT 
    e.id as employee_id,
    e.aname as employee_name,
    COUNT(o.id) as total_sales,
    SUM(o.fat_total) as total_revenue,
    AVG(o.fat_total) as avg_sale_value,
    DATE(MAX(o.pro_date)) as last_sale_date
FROM acc_head e
LEFT JOIN ot_head o ON e.id = o.emp_id
WHERE e.parent_id = 35  -- الموظفين
  AND e.isdeleted = 0
GROUP BY e.id
ORDER BY total_revenue DESC;

-- ==========================================
-- 10. Profit Analysis View
-- ==========================================

CREATE OR REPLACE VIEW `vw_profit_analysis` AS
SELECT 
    DATE(o.pro_date) as sale_date,
    SUM(fd.det_value) as revenue,
    SUM(fd.qty_out * IFNULL(fd.cost_price, m.cost_price)) as cost,
    SUM(fd.det_value - (fd.qty_out * IFNULL(fd.cost_price, m.cost_price))) as gross_profit,
    (SUM(fd.det_value - (fd.qty_out * IFNULL(fd.cost_price, m.cost_price))) / NULLIF(SUM(fd.det_value), 0) * 100) as profit_margin_percentage
FROM ot_head o
INNER JOIN fat_details fd ON o.id = fd.pro_id
INNER JOIN myitems m ON fd.item_id = m.id
WHERE o.pro_tybe = 9
  AND IFNULL(fd.isdeleted, 0) = 0
GROUP BY DATE(o.pro_date)
ORDER BY sale_date DESC;

SELECT 'Useful views created successfully!' as status;

