DELIMITER //

DROP PROCEDURE IF EXISTS sp_recalculate_costs //

CREATE PROCEDURE sp_recalculate_costs()
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE v_id INT;
    DECLARE v_pro_tybe INT;
    DECLARE v_pro_id INT;
    DECLARE v_fatid INT;
    DECLARE v_item_id INT;
    DECLARE v_qty_in DECIMAL(14, 4);
    DECLARE v_qty_out DECIMAL(14, 4);
    DECLARE v_price DECIMAL(14, 4);
    DECLARE v_discount DECIMAL(14, 4);
    
    DECLARE v_old_qty DECIMAL(14, 4) DEFAULT 0.0;
    DECLARE v_old_cost DECIMAL(14, 4) DEFAULT 0.0;
    DECLARE v_new_cost DECIMAL(14, 4) DEFAULT 0.0;
    DECLARE v_total_qty DECIMAL(14, 4) DEFAULT 0.0;
    DECLARE v_unit_profit DECIMAL(14, 4) DEFAULT 0.0;
    DECLARE v_line_profit DECIMAL(14, 4) DEFAULT 0.0;

    -- مؤشر cursor لقراءة حركات الفواتير غير المحذوفة (السطر + رأس الفاتورة + الصنف)
    -- بالتاريخ الفعلي للعملية مع تقديم الرصيد الافتتاحي ثم الوارد في نفس اليوم
    DECLARE cur_details CURSOR FOR
        SELECT d.id, d.pro_tybe, d.pro_id, d.fatid, d.item_id, 
               COALESCE(d.qty_in, 0), COALESCE(d.qty_out, 0), 
               COALESCE(d.price, 0), COALESCE(d.discount, 0)
        FROM fat_details d
        JOIN myitems m ON d.item_id = m.id
        LEFT JOIN ot_head o ON o.id = COALESCE(NULLIF(d.pro_id, 0), d.fatid)
        WHERE COALESCE(d.isdeleted, 0) = 0
          AND COALESCE(m.isdeleted, 0) = 0
          AND COALESCE(o.isdeleted, 0) = 0
          AND (m.manual_price_edit = 0 OR m.manual_price_edit IS NULL)
        ORDER BY 
            COALESCE(NULLIF(o.pro_date, '0000-00-00'), DATE(d.crtime)) ASC,
            (CASE WHEN d.pro_tybe = 14 THEN 0
                  WHEN d.pro_tybe IN (4, 12) THEN 1
                  ELSE 2 END) ASC,
            d.crtime ASC, 
            d.id ASC;

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    -- معالج الأخطاء لضمان التراجع في حالة حدوث استثناء
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        DROP TEMPORARY TABLE IF EXISTS tmp_item_state;
        RESIGNAL;
    END;

    START TRANSACTION;

    -- 1. إنشاء جدول مؤقت في الذاكرة لتتبع حالة كل صنف (الرصيد التراكمي ومتوسط التكلفة)
    DROP TEMPORARY TABLE IF EXISTS tmp_item_state;
    CREATE TEMPORARY TABLE tmp_item_state (
        item_id INT PRIMARY KEY,
        running_qty DECIMAL(14, 4) DEFAULT 0.0,
        running_cost DECIMAL(14, 4) DEFAULT 0.0
    ) ENGINE = MEMORY;

    -- تهيئة الجدول المؤقت لجميع الأصناف النشطة
    INSERT INTO tmp_item_state (item_id, running_qty, running_cost)
    SELECT id, 0.0, 0.0
    FROM myitems
    WHERE COALESCE(isdeleted, 0) = 0 AND (manual_price_edit = 0 OR manual_price_edit IS NULL);

    -- 2. فتح الـ Cursor والتكرار عبر جميع حركات الفواتير بالترتيب المحاسبي الزمني
    OPEN cur_details;

    read_loop: LOOP
        FETCH cur_details INTO v_id, v_pro_tybe, v_pro_id, v_fatid, v_item_id, 
                               v_qty_in, v_qty_out, v_price, v_discount;
        IF done THEN
            LEAVE read_loop;
        END IF;

        -- قراءة الرصيد والتكلفة السابقة للصنف
        -- (استعلام فرعي بدل SELECT INTO حتى لا يُطلق NOT FOUND فينهي الحلقة مبكراً)
        SET v_old_qty  = COALESCE((SELECT running_qty  FROM tmp_item_state WHERE item_id = v_item_id), 0);
        SET v_old_cost = COALESCE((SELECT running_cost FROM tmp_item_state WHERE item_id = v_item_id), 0);

        -- أ) رصيد افتتاحي مخازن / مشتريات / أمر شراء (pro_tybe = 14 أو 4 أو 12)
        IF v_pro_tybe IN (14, 4, 12) THEN
            SET v_new_cost = v_old_cost;
            IF v_qty_in > 0 THEN
                IF v_old_qty > 0 THEN
                    SET v_total_qty = v_old_qty + v_qty_in;
                    SET v_new_cost = ((v_old_cost * v_old_qty) + (v_price * v_qty_in)) / v_total_qty;
                ELSE
                    -- الرصيد صفر أو سالب: التكلفة = سعر الشراء الحالي
                    SET v_new_cost = v_price;
                END IF;
            END IF;

            UPDATE fat_details 
            SET cost_price = v_new_cost 
            WHERE id = v_id;

            UPDATE tmp_item_state 
            SET running_qty = running_qty + v_qty_in - v_qty_out,
                running_cost = v_new_cost 
            WHERE item_id = v_item_id;

        -- ب) حركة مبيعات أو صرف (منصرف: pro_tybe = 3 أو 9 أو 13)
        ELSEIF v_pro_tybe IN (3, 9, 13) THEN
            SET v_unit_profit = v_price - v_old_cost;
            SET v_line_profit = (v_qty_out * v_unit_profit) - v_discount;

            UPDATE fat_details 
            SET cost_price = v_old_cost,
                profit = v_line_profit 
            WHERE id = v_id;

            UPDATE tmp_item_state 
            SET running_qty = running_qty + v_qty_in - v_qty_out 
            WHERE item_id = v_item_id;

        -- ج) المردودات (pro_tybe = 10 أو 11): الاتجاه يُحدَّد من الكمية
        ELSEIF v_pro_tybe IN (10, 11) THEN
            IF v_qty_in > 0 THEN
                -- مردود مبيعات: يرجع للمخزن بالتكلفة الحالية ويعكس الربح
                SET v_unit_profit = v_price - v_old_cost;
                SET v_line_profit = - ((v_qty_in * v_unit_profit) - v_discount);

                UPDATE fat_details 
                SET cost_price = v_old_cost,
                    profit = v_line_profit 
                WHERE id = v_id;
            END IF;

            UPDATE tmp_item_state 
            SET running_qty = running_qty + v_qty_in - v_qty_out 
            WHERE item_id = v_item_id;

        -- د) أي حركة مخزنية أخرى: تؤثر على الرصيد فقط
        ELSE
            UPDATE tmp_item_state 
            SET running_qty = running_qty + v_qty_in - v_qty_out 
            WHERE item_id = v_item_id;
        END IF;

    END LOOP;

    CLOSE cur_details;

    -- 3. تحديث جدول الأصناف الرئيسي (myitems) بآخر متوسط تكلفة وصل إليه كل صنف
    UPDATE myitems m
    JOIN tmp_item_state s ON m.id = s.item_id
    SET m.cost_price = s.running_cost
    WHERE s.running_cost > 0 
      AND COALESCE(m.isdeleted, 0) = 0
      AND (m.manual_price_edit = 0 OR m.manual_price_edit IS NULL);

    -- 3.1 إعادة حساب رصيد الصنف (itmqty) من الحركات غير المحذوفة فقط
    -- للأصناف التي لها أي حركة (ولو محذوفة)؛ الأصناف بلا حركات تحتفظ برصيدها اليدوي
    UPDATE myitems m
    JOIN (
        SELECT d.item_id,
               SUM(CASE WHEN COALESCE(d.isdeleted, 0) = 0 AND COALESCE(o.isdeleted, 0) = 0
                        THEN COALESCE(d.qty_in, 0) - COALESCE(d.qty_out, 0)
                        ELSE 0 END) AS bal
        FROM fat_details d
        LEFT JOIN ot_head o ON o.id = COALESCE(NULLIF(d.pro_id, 0), d.fatid)
        GROUP BY d.item_id
    ) b ON b.item_id = m.id
    SET m.itmqty = b.bal
    WHERE COALESCE(m.isdeleted, 0) = 0;

    -- 4. تحديث أسعار وحدات الصنف (item_units)
    UPDATE item_units u
    JOIN tmp_item_state s ON u.item_id = s.item_id
    SET u.cost_price = s.running_cost * u.u_val
    WHERE s.running_cost > 0;

    -- 5. تحديث إجمالي الأرباح في رأس الفاتورة (ot_head.profit) بأثر رجعي لجميع الفواتير غير المحذوفة
    UPDATE ot_head o
    JOIN (
        SELECT COALESCE(NULLIF(pro_id, 0), fatid) AS invoice_id, 
               SUM(profit) AS total_profit
        FROM fat_details
        WHERE COALESCE(isdeleted, 0) = 0
        GROUP BY invoice_id
    ) d ON o.id = d.invoice_id
    SET o.profit = d.total_profit,
        o.crtime = o.crtime
    WHERE COALESCE(o.isdeleted, 0) = 0;

    -- تنظيف وحفظ التغييرات
    DROP TEMPORARY TABLE IF EXISTS tmp_item_state;
    COMMIT;
END //

DELIMITER ;
