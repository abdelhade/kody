<?php
/**
 * Shared helpers for the POS tables panel (ajax/tables_panel.php).
 */

function tpanel_ensure_schema($conn) {
    static $done = false;
    if ($done) return;
    $done = true;

    $chk = $conn->query("SHOW COLUMNS FROM ot_head LIKE 'table_id'");
    if ($chk && $chk->num_rows == 0) {
        $conn->query("ALTER TABLE ot_head ADD COLUMN table_id INT DEFAULT NULL");
    }
    $chk = $conn->query("SHOW COLUMNS FROM tables LIKE 'parent_table_id'");
    if ($chk && $chk->num_rows == 0) {
        $conn->query("ALTER TABLE tables ADD COLUMN parent_table_id INT DEFAULT NULL");
    }
    $chk = $conn->query("SHOW COLUMNS FROM tables LIKE 'is_merged'");
    if ($chk && $chk->num_rows == 0) {
        $conn->query("ALTER TABLE tables ADD COLUMN is_merged TINYINT(1) DEFAULT 0");
    }
}

function tpanel_get_table($conn, $table_id) {
    $stmt = $conn->prepare("SELECT * FROM tables WHERE id = ? AND isdeleted = 0 LIMIT 1");
    $stmt->bind_param('i', $table_id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $row ?: null;
}

/** Primary table id for merged groups (child → parent, else self). */
function tpanel_resolve_primary_id($conn, $table_id) {
    $table = tpanel_get_table($conn, $table_id);
    if (!$table) return 0;
    $parent = intval($table['parent_table_id'] ?? 0);
    return $parent > 0 ? $parent : intval($table['id']);
}

/** أرقام طاولات المجموعة المدمجة: الرئيسية أولاً ثم التابعة لها. */
function tpanel_get_group_ids($conn, $primary_id) {
    $primary_id = intval($primary_id);
    $ids = [$primary_id];

    $stmt = $conn->prepare(
        "SELECT id FROM tables WHERE parent_table_id = ? AND isdeleted = 0"
    );
    $stmt->bind_param('i', $primary_id);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $ids[] = intval($row['id']);
    }
    $stmt->close();

    return array_values(array_unique($ids));
}

function tpanel_get_active_order($conn, $table_id) {
    $primary_id = tpanel_resolve_primary_id($conn, $table_id);
    if ($primary_id <= 0) return null;

    $table = tpanel_get_table($conn, $primary_id);
    $tname = $table['tname'] ?? '';

    /**
     * 1) الربط الرقمي (المفضل).
     * الطلب المفتوح = فاتورة كاشير (pro_tybe = 9) لم تُغلق بعد،
     * والإغلاق يُسجَّل في order_status لا في pro_tybe.
     */
    $stmt = $conn->prepare(
        "SELECT * FROM ot_head
         WHERE table_id = ? AND pro_tybe = 9 AND isdeleted = 0
           AND order_status <> 'completed'
         ORDER BY id DESC LIMIT 1"
    );
    $stmt->bind_param('i', $primary_id);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    /**
     * 1.b) طلب محمول على طاولة تابعة في مجموعة مدمجة.
     * الدمج يجعل الفاتورة على الطاولة الرئيسية، لكن بيانات دمج سابقة
     * قد تكون تركت الطلب على طاولة تابعة فيصبح غير قابل للوصول؛
     * فنبحث في المجموعة كلها ونعيد ربط الطلب بالرئيسية.
     */
    if (!$order) {
        $group_ids = tpanel_get_group_ids($conn, $primary_id);
        if (count($group_ids) > 1) {
            $in_list = implode(',', array_map('intval', $group_ids));
            $res = $conn->query(
                "SELECT * FROM ot_head
                 WHERE table_id IN ($in_list) AND pro_tybe = 9 AND isdeleted = 0
                   AND order_status <> 'completed'
                 ORDER BY id DESC LIMIT 1"
            );
            $order = $res ? $res->fetch_assoc() : null;

            if ($order) {
                $oid = intval($order['id']);
                $stmt = $conn->prepare("UPDATE ot_head SET table_id = ? WHERE id = ?");
                $stmt->bind_param('ii', $primary_id, $oid);
                $stmt->execute();
                $stmt->close();
                $order['table_id'] = $primary_id;
            }
        }
    }

    // 2) fallback للطلبات القديمة (اسم الطاولة في info — بدون LIKE واسع)
    if (!$order && $tname !== '') {
        $pat_full = '% - طاولة: ' . $tname;
        $pat_short = 'طاولة: ' . $tname;
        $pat_only = 'طاولة: ' . $tname . '%';
        $stmt = $conn->prepare(
            "SELECT * FROM ot_head
             WHERE pro_tybe = 9 AND isdeleted = 0
               AND order_status <> 'completed'
               AND (info LIKE ? OR info = ? OR info LIKE ?)
             ORDER BY id DESC LIMIT 1"
        );
        $stmt->bind_param('sss', $pat_full, $pat_short, $pat_only);
        $stmt->execute();
        $order = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        // ربط الطلب بالطاولة رقمياً للمرة القادمة
        if ($order) {
            $oid = intval($order['id']);
            $backfill = $conn->prepare("UPDATE ot_head SET table_id = ? WHERE id = ? AND (table_id IS NULL OR table_id = 0)");
            $backfill->bind_param('ii', $primary_id, $oid);
            $backfill->execute();
            $backfill->close();
        }
    }

    return $order ?: null;
}

function tpanel_get_order_items($conn, $order_id) {
    $items = [];
    // fatid و pro_id قد يحملان ot_head.id حسب إصدار البيانات
    $stmt = $conn->prepare(
        "SELECT fd.id, fd.item_id, fd.price, fd.det_value,
                (fd.qty_out - fd.qty_in) AS qty,
                COALESCE(i.iname, 'صنف') AS iname
         FROM fat_details fd
         LEFT JOIN myitems i ON fd.item_id = i.id
         WHERE (fd.fatid = ? OR fd.pro_id = ?) AND fd.isdeleted = 0
         ORDER BY fd.id ASC"
    );
    $stmt->bind_param('ii', $order_id, $order_id);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $qty = floatval($row['qty']);
        if ($qty <= 0) continue;
        $price = floatval($row['price']);
        $line = $qty * $price;
        $items[] = [
            'id' => intval($row['id']),
            'item_id' => intval($row['item_id']),
            'iname' => $row['iname'],
            'qty' => $qty,
            'price' => $price,
            'line_total' => round($line, 2),
        ];
    }
    $stmt->close();
    return $items;
}

function tpanel_calc_totals($order, $items) {
    $total = 0;
    foreach ($items as $it) {
        $total += $it['line_total'];
    }
    $discount = floatval($order['fat_disc'] ?? 0);
    $net = floatval($order['fat_net'] ?? 0);
    if ($net <= 0 && $total > 0) {
        $net = max(0, $total - $discount);
    }
    $paid = floatval($order['paid_amount'] ?? 0);
    return [
        'total' => round($total, 2),
        'discount' => round($discount, 2),
        'net' => round($net, 2),
        'paid' => round($paid, 2),
        'remaining' => round(max(0, $net - $paid), 2),
    ];
}

function tpanel_table_status($conn, $table) {
    $tid = intval($table['id']);
    $is_merged = intval($table['is_merged'] ?? 0) === 1;
    $parent_id = intval($table['parent_table_id'] ?? 0);
    $primary_id = $parent_id > 0 ? $parent_id : $tid;

    $order = tpanel_get_active_order($conn, $primary_id);
    $has_order = !empty($order) && (floatval($order['fat_net'] ?? 0) > 0 || intval($table['table_case']) !== 0);

    $status = 'available';
    if ($is_merged && $has_order) {
        $status = 'merged_occupied';
    } elseif ($is_merged) {
        $status = 'merged';
    } elseif ($has_order || intval($table['table_case']) !== 0) {
        $status = 'occupied';
    }

    $order_total = 0;
    $order_id = null;
    if ($order) {
        $order_id = intval($order['id']);
        $order_total = floatval($order['fat_net'] ?? 0);
    }

    return [
        'id' => $tid,
        'name' => $table['tname'],
        'status' => $status,
        'is_merged' => $is_merged,
        'parent_table_id' => $parent_id > 0 ? $parent_id : null,
        'primary_table_id' => $primary_id,
        'order_id' => $order_id,
        'order_total' => round($order_total, 2),
    ];
}

function tpanel_get_merged_siblings($conn, $primary_id) {
    $siblings = [];
    $stmt = $conn->prepare(
        "SELECT id, tname FROM tables
         WHERE (id = ? OR parent_table_id = ?) AND isdeleted = 0 AND is_merged = 1"
    );
    $stmt->bind_param('ii', $primary_id, $primary_id);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        if (intval($row['id']) !== $primary_id) {
            $siblings[] = ['id' => intval($row['id']), 'name' => $row['tname']];
        }
    }
    $stmt->close();
    return $siblings;
}

function tpanel_seed_tables_if_empty($conn) {
    $r = $conn->query("SELECT COUNT(*) AS c FROM tables WHERE isdeleted = 0");
    $count = $r ? intval($r->fetch_assoc()['c']) : 0;
    if ($count > 0) return;

    $stmt = $conn->prepare("INSERT INTO tables (tname, table_case) VALUES (?, 0)");
    for ($i = 1; $i <= 12; $i++) {
        $name = "طاولة $i";
        $stmt->bind_param('s', $name);
        $stmt->execute();
    }
    $stmt->close();
}

function tpanel_build_state($conn, $selected_table_id = 0) {
    tpanel_ensure_schema($conn);
    tpanel_seed_tables_if_empty($conn);

    // إصلاح ذاتي لمجموعة الطاولة المختارة: فاتورة واحدة للمجموعة المدمجة
    if ($selected_table_id > 0) {
        $primary_for_fix = tpanel_resolve_primary_id($conn, $selected_table_id);
        if ($primary_for_fix > 0) {
            tpanel_consolidate_group_orders($conn, $primary_for_fix);
        }
    }

    tpanel_release_stale_tables($conn);

    $tables = [];
    $res = $conn->query(
        "SELECT * FROM tables WHERE isdeleted = 0
         ORDER BY CAST(SUBSTRING_INDEX(tname, ' ', -1) AS UNSIGNED), tname"
    );
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $tables[] = tpanel_table_status($conn, $row);
        }
    }

    $selected = null;
    if ($selected_table_id > 0) {
        $table = tpanel_get_table($conn, $selected_table_id);
        if ($table) {
            $primary_id = tpanel_resolve_primary_id($conn, $selected_table_id);
            $order = tpanel_get_active_order($conn, $selected_table_id);
            $items = [];
            $totals = ['total' => 0, 'discount' => 0, 'net' => 0, 'paid' => 0, 'remaining' => 0];
            if ($order) {
                $items = tpanel_get_order_items($conn, intval($order['id']));
                $totals = tpanel_calc_totals($order, $items);
            }
            // فاتورة المجموعة المدمجة تُحمَّل دائماً على الطاولة الرئيسية
            $primary_table = ($primary_id === intval($table['id']))
                ? $table
                : tpanel_get_table($conn, $primary_id);

            $selected = [
                'table_id' => intval($table['id']),
                'table_name' => $table['tname'],
                'primary_table_id' => $primary_id,
                'primary_table_name' => $primary_table['tname'] ?? $table['tname'],
                'is_merged' => intval($table['is_merged']) === 1,
                'parent_table_id' => intval($table['parent_table_id']) > 0 ? intval($table['parent_table_id']) : null,
                'merged_with' => tpanel_get_merged_siblings($conn, $primary_id),
                'order_id' => $order ? intval($order['id']) : null,
                'items' => $items,
                'totals' => $totals,
            ];
        }
    }

    return ['tables' => $tables, 'selected' => $selected];
}

function tpanel_sync_table_case($conn, $table_id, $case) {
    $stmt = $conn->prepare("UPDATE tables SET table_case = ? WHERE id = ?");
    $stmt->bind_param('ii', $case, $table_id);
    $stmt->execute();
    $stmt->close();
}

/** إعادة حساب إجماليات الطلب من أصنافه. */
function tpanel_recalc_order_totals($conn, $order_id) {
    $order_id = intval($order_id);

    $stmt = $conn->prepare(
        "SELECT COALESCE(SUM((qty_out - qty_in) * price), 0) AS total
         FROM fat_details WHERE (fatid = ? OR pro_id = ?) AND isdeleted = 0"
    );
    $stmt->bind_param('ii', $order_id, $order_id);
    $stmt->execute();
    $total = floatval($stmt->get_result()->fetch_assoc()['total'] ?? 0);
    $stmt->close();

    $stmt = $conn->prepare(
        "UPDATE ot_head
         SET fat_total = ?, pro_value = ?, fat_net = GREATEST(0, ? - COALESCE(fat_disc, 0))
         WHERE id = ?"
    );
    $stmt->bind_param('dddi', $total, $total, $total, $order_id);
    $stmt->execute();
    $stmt->close();

    return round($total, 2);
}

/**
 * توحيد فاتورة المجموعة المدمجة: تُنقل أصناف طلبات الطاولات التابعة
 * إلى طلب واحد على الطاولة الرئيسية، لأن الدمج يعني فاتورة واحدة.
 * بدون هذا التوحيد يبقى طلب الطاولة التابعة غير مرئي ولا يُدفع،
 * وتظل الطاولة مشغولة إلى الأبد.
 *
 * الطلبات التي سُدِّد منها شيء لا تُلمس: دمج قيودها المحاسبية غير آمن،
 * فتُغلق أولاً من مسار الدفع العادي.
 *
 * @return int عدد الطلبات التي أُدمجت في فاتورة المجموعة
 */
function tpanel_consolidate_group_orders($conn, $primary_id) {
    $primary_id = intval($primary_id);
    $group_ids = tpanel_get_group_ids($conn, $primary_id);
    if (count($group_ids) < 2) return 0;

    $in_list = implode(',', array_map('intval', $group_ids));
    $res = $conn->query(
        "SELECT id, table_id, paid_amount FROM ot_head
         WHERE table_id IN ($in_list) AND pro_tybe = 9 AND isdeleted = 0
           AND order_status <> 'completed'
         ORDER BY (table_id = $primary_id) DESC, id ASC"
    );
    if (!$res || $res->num_rows === 0) return 0;

    $orders = [];
    while ($row = $res->fetch_assoc()) {
        $orders[] = $row;
    }

    // الفاتورة الباقية: طلب الطاولة الرئيسية إن وُجد، وإلا أقدم طلب في المجموعة
    $keep = array_shift($orders);
    $keep_id = intval($keep['id']);

    if (intval($keep['table_id']) !== $primary_id) {
        $stmt = $conn->prepare("UPDATE ot_head SET table_id = ? WHERE id = ?");
        $stmt->bind_param('ii', $primary_id, $keep_id);
        $stmt->execute();
        $stmt->close();
    }

    $merged = 0;
    foreach ($orders as $order) {
        if (floatval($order['paid_amount'] ?? 0) > 0) continue;
        $oid = intval($order['id']);

        $stmt = $conn->prepare(
            "UPDATE fat_details SET fatid = ?, pro_id = ?
             WHERE (fatid = ? OR pro_id = ?) AND isdeleted = 0"
        );
        $stmt->bind_param('iiii', $keep_id, $keep_id, $oid, $oid);
        $stmt->execute();
        $stmt->close();

        $stmt = $conn->prepare(
            "UPDATE ot_head SET isdeleted = 1, fat_total = 0, fat_net = 0 WHERE id = ?"
        );
        $stmt->bind_param('i', $oid);
        $stmt->execute();
        $stmt->close();

        $merged++;
    }

    if ($merged > 0) {
        tpanel_recalc_order_totals($conn, $keep_id);
    }

    return $merged;
}

/**
 * تحرير الطاولات المشغولة بلا فاتورة (بقايا طلبات مُغلقة أو محذوفة).
 * بدون ذلك تبقى الطاولة مشغولة للأبد فلا يمكن فتح طلب جديد عليها.
 * الدمج لا يُفكّ هنا: قد تُدمج طاولات قبل الطلب استعداداً لمجموعة كبيرة،
 * وفكّه يقتصر على الإلغاء الصريح أو الإغلاق بعد السداد.
 *
 * @return int عدد المجموعات التي حُرِّرت
 */
function tpanel_release_stale_tables($conn) {
    $res = $conn->query(
        "SELECT id, parent_table_id FROM tables
         WHERE isdeleted = 0 AND (table_case <> 0 OR is_merged = 1)"
    );
    if (!$res) return 0;

    $primaries = [];
    while ($row = $res->fetch_assoc()) {
        $parent = intval($row['parent_table_id'] ?? 0);
        $primaries[$parent > 0 ? $parent : intval($row['id'])] = true;
    }

    $freed = 0;
    foreach (array_keys($primaries) as $primary_id) {
        if (tpanel_get_active_order($conn, $primary_id)) continue;

        $stmt = $conn->prepare(
            "UPDATE tables SET table_case = 0
             WHERE (id = ? OR parent_table_id = ?) AND table_case <> 0"
        );
        $stmt->bind_param('ii', $primary_id, $primary_id);
        $stmt->execute();
        if ($stmt->affected_rows > 0) $freed++;
        $stmt->close();
    }

    return $freed;
}

/** توحيد حالة الإشغال على مستوى المجموعة المدمجة. */
function tpanel_sync_group_case($conn, $primary_id) {
    $primary_id = intval($primary_id);
    $order = tpanel_get_active_order($conn, $primary_id);
    $case = $order ? 1 : 0;

    $stmt = $conn->prepare(
        "UPDATE tables SET table_case = ? WHERE id = ? OR parent_table_id = ?"
    );
    $stmt->bind_param('iii', $case, $primary_id, $primary_id);
    $stmt->execute();
    $stmt->close();
}

function tpanel_free_merged_group($conn, $primary_id) {
    $stmt = $conn->prepare(
        "UPDATE tables SET is_merged = 0, parent_table_id = NULL, table_case = 0
         WHERE id = ? OR parent_table_id = ?"
    );
    $stmt->bind_param('ii', $primary_id, $primary_id);
    $stmt->execute();
    $stmt->close();
}

/**
 * ملاحظة: لا تُنشأ أي قيود محاسبية هنا.
 * قيود الدفع تُنشأ في do/doadd_invoice.php فقط (مسار الدفع الوحيد).
 */

function tpanel_json_response($data, $code = 200) {
    if (ob_get_length()) ob_clean();
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}
