<?php

/**
 * Invoice Processor Class
 * يجمع المنطق المشترك بين الإضافة والتعديل والحذف للفواتير
 */
class InvoiceProcessor {
    
    // تعريف ثوابت أنواع الفواتير
    const INVOICE_TYPES = [
        'PURCHASE' => 4,          // مشتريات
        'SALES' => 3,             // مبيعات  
        'POS' => 9,               // كاشير
        'PURCHASE_RETURN' => 10,  // مردود مشتريات
        'SALES_RETURN' => 11,     // مردود مبيعات
        'PURCHASE_ORDER' => 12,   // أمر شراء
        'SALES_ORDER' => 13,      // أمر بيع
        'OFFER' => 14             // عرض سعر
    ];

    // تعريف أنواع العمليات المحاسبية
    const ACCOUNTING_TYPES = [
        'RECEIPT' => 1,           // سند قبض
        'PAYMENT' => 2,           // سند دفع
        'SALES_DISC' => 7,        // خصم مبيعات
        'PURCHASE_DISC' => 6      // خصم مشتريات
    ];

    /**
     * دالة الحصول على إعدادات نوع الفاتورة
     */
    public static function getInvoiceConfig($pro_tybe) {
        $configs = [
            self::INVOICE_TYPES['PURCHASE'] => [
                'note' => 'فاتورة مشتريات',
                'paid_note' => 'سند دفع',
                'disc_type' => self::ACCOUNTING_TYPES['PURCHASE_DISC'],
                'paid_type' => self::ACCOUNTING_TYPES['PAYMENT'],
                'cost_account' => 97
            ],
            self::INVOICE_TYPES['SALES'] => [
                'note' => 'فاتورة مبيعات',
                'paid_note' => 'سند قبض',
                'disc_type' => self::ACCOUNTING_TYPES['SALES_DISC'],
                'paid_type' => self::ACCOUNTING_TYPES['RECEIPT'],
                'cost_account' => 91
            ],
            self::INVOICE_TYPES['POS'] => [
                'note' => 'فاتورة ريسيت',
                'paid_note' => 'سند قبض',
                'disc_type' => self::ACCOUNTING_TYPES['SALES_DISC'],
                'paid_type' => self::ACCOUNTING_TYPES['RECEIPT'],
                'cost_account' => 91
            ],
            self::INVOICE_TYPES['PURCHASE_RETURN'] => [
                'note' => 'مردود مشتريات',
                'paid_note' => 'سند قبض',
                'disc_type' => self::ACCOUNTING_TYPES['PURCHASE_DISC'],
                'paid_type' => self::ACCOUNTING_TYPES['RECEIPT'],
                'cost_account' => 97
            ],
            self::INVOICE_TYPES['SALES_RETURN'] => [
                'note' => 'مردود مبيعات',
                'paid_note' => 'سند دفع',
                'disc_type' => self::ACCOUNTING_TYPES['SALES_DISC'],
                'paid_type' => self::ACCOUNTING_TYPES['PAYMENT'],
                'cost_account' => 91
            ],
            self::INVOICE_TYPES['PURCHASE_ORDER'] => [
                'note' => 'أمر شراء',
                'paid_note' => 'سند دفع',
                'disc_type' => self::ACCOUNTING_TYPES['PURCHASE_DISC'],
                'paid_type' => self::ACCOUNTING_TYPES['PAYMENT'],
                'cost_account' => 97
            ],
            self::INVOICE_TYPES['SALES_ORDER'] => [
                'note' => 'أمر بيع',
                'paid_note' => 'سند قبض',
                'disc_type' => self::ACCOUNTING_TYPES['SALES_DISC'],
                'paid_type' => self::ACCOUNTING_TYPES['RECEIPT'],
                'cost_account' => 91
            ],
            self::INVOICE_TYPES['OFFER'] => [
                'note' => 'عرض سعر',
                'paid_note' => 'سند قبض',
                'disc_type' => self::ACCOUNTING_TYPES['SALES_DISC'],
                'paid_type' => self::ACCOUNTING_TYPES['RECEIPT'],
                'cost_account' => 91
            ]
        ];
        
        return isset($configs[$pro_tybe]) ? $configs[$pro_tybe] : null;
    }

    /**
     * دالة تحديد الحسابات المحاسبية
     */
    public static function getAccountingAccounts($pro_tybe, $store_id, $acc2_id, $fund_id) {
        switch($pro_tybe) {
            case self::INVOICE_TYPES['PURCHASE']:
                return [
                    'acc1' => $store_id,
                    'acc2' => $acc2_id,
                    'acc3' => $acc2_id,
                    'acc4' => 97,
                    'acc5' => $acc2_id,
                    'acc6' => $fund_id
                ];
                
            case self::INVOICE_TYPES['SALES']:
            case self::INVOICE_TYPES['POS']:
                return [
                    'acc1' => $fund_id,      // الصندوق (مدين)
                    'acc2' => $acc2_id,      // العميل (دائن)
                    'acc3' => 91,            // حساب المبيعات
                    'acc4' => $acc2_id,      // العميل
                    'acc5' => $fund_id,      // الصندوق (للدفع)
                    'acc6' => $acc2_id       // العميل (للدفع الآجل)
                ];
                
            case self::INVOICE_TYPES['PURCHASE_RETURN']:
                return [
                    'acc1' => $acc2_id,
                    'acc2' => $store_id,
                    'acc3' => $acc2_id,
                    'acc4' => 97,
                    'acc5' => $fund_id,
                    'acc6' => $acc2_id
                ];
                
            case self::INVOICE_TYPES['SALES_RETURN']:
                return [
                    'acc1' => $store_id,
                    'acc2' => $acc2_id,
                    'acc3' => 91,
                    'acc4' => $acc2_id,
                    'acc5' => $acc2_id,
                    'acc6' => $fund_id
                ];
                
            case self::INVOICE_TYPES['PURCHASE_ORDER']:
                return [
                    'acc1' => $store_id,
                    'acc2' => $acc2_id,
                    'acc3' => $acc2_id,
                    'acc4' => 97,
                    'acc5' => $acc2_id,
                    'acc6' => $fund_id
                ];
                
            case self::INVOICE_TYPES['SALES_ORDER']:
            case self::INVOICE_TYPES['OFFER']:
                return [
                    'acc1' => $acc2_id,
                    'acc2' => $store_id,
                    'acc3' => 91,
                    'acc4' => $acc2_id,
                    'acc5' => $fund_id,
                    'acc6' => $acc2_id
                ];
                
            default:
                throw new InvalidArgumentException('نوع فاتورة غير مدعوم');
        }
    }

    /**
     * الحصول على رقم الفاتورة التالي
     */
    public static function getNextInvoiceNumber($conn, $invoice_type) {
        $stmt = $conn->prepare("SELECT MAX(CAST(pro_id AS UNSIGNED)) as max_id FROM ot_head WHERE pro_tybe = ?");
        if (!$stmt) {
            throw new Exception('فشل في تحضير الاستعلام: ' . $conn->error);
        }
        
        $stmt->bind_param("i", $invoice_type);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        
        return $row && $row['max_id'] ? ($row['max_id'] + 1) : 1;
    }

    /**
     * التحقق من توفر المخزون
     */
    public static function checkStockAvailability($conn, $item_id, $store_id, $required_qty) {
        $stmt = $conn->prepare(
            "SELECT COALESCE(SUM(qty_in) - SUM(qty_out), 0) AS available 
             FROM fat_details 
             WHERE item_id = ? AND det_store = ? AND isdeleted = 0"
        );
        $stmt->bind_param("ii", $item_id, $store_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $result['available'] >= $required_qty;
    }

    /**
     * الحصول على الرصيد الفعلي للصنف (عبر كل المخازن أو مخزن محدد)
     */
    public static function getRealStockQuantity($conn, $item_id, $store_id = null) {
        if ($store_id) {
            $stmt = $conn->prepare(
                "SELECT COALESCE(SUM(qty_in) - SUM(qty_out), 0) AS real_qty 
                 FROM fat_details 
                 WHERE item_id = ? AND det_store = ? AND isdeleted = 0"
            );
            $stmt->bind_param("ii", $item_id, $store_id);
        } else {
            $stmt = $conn->prepare(
                "SELECT COALESCE(SUM(qty_in) - SUM(qty_out), 0) AS real_qty 
                 FROM fat_details 
                 WHERE item_id = ? AND isdeleted = 0"
            );
            $stmt->bind_param("i", $item_id);
        }
        
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        return (float)($result['real_qty'] ?? 0);
    }

    /**
     * Soft-delete كامل لوحدة عملية على ot_head:
     * الرأس + البنود + القيود (op_id) + السندات المرتبطة (op2) وقيودها.
     *
     * @param bool $manageTransaction true = begin/commit داخل الدالة
     */
    public static function softDelete(mysqli $conn, int $id, bool $manageTransaction = true): void
    {
        if ($id <= 0) {
            throw new InvalidArgumentException('معرّف فاتورة غير صالح');
        }

        if ($manageTransaction) {
            $conn->begin_transaction();
        }

        try {
            $relatedIds = self::fetchIds(
                $conn,
                'SELECT id FROM ot_head WHERE op2 = ? AND id <> ? AND isdeleted = 0',
                'ii',
                [$id, $id]
            );
            $opIds = array_merge([$id], $relatedIds);
            $journalIds = self::fetchJournalHeadIds($conn, $opIds);
            $accountIds = self::fetchJournalAccountIds($conn, $journalIds, $opIds);

            self::softDeleteJournalEntries($conn, $journalIds, $opIds);
            self::softDeleteJournalHeadsByIds($conn, $journalIds);
            foreach ($relatedIds as $relatedId) {
                self::softDeleteHeader($conn, $relatedId);
            }
            self::softDeleteDetails($conn, $id);
            self::softDeleteHeader($conn, $id);
            foreach (array_unique($accountIds) as $accountId) {
                self::recalcAccountBalance($conn, (int) $accountId);
            }

            if ($manageTransaction) {
                $conn->commit();
            }
        } catch (Throwable $e) {
            if ($manageTransaction) {
                $conn->rollback();
            }
            throw $e;
        }
    }

    /**
     * حذف نهائي للفاتورة: القيود وتفاصيلها، البنود، والسندات المرتبطة، ثم إعادة الأرصدة والكميات.
     */
    public static function hardDelete(mysqli $conn, int $id, bool $manageTransaction = true): void
    {
        if ($id <= 0) {
            throw new InvalidArgumentException('معرّف فاتورة غير صالح');
        }

        if ($manageTransaction) {
            $conn->begin_transaction();
        }

        try {
            $relatedIds = self::fetchIds(
                $conn,
                'SELECT id FROM ot_head WHERE op2 = ? AND id <> ?',
                'ii',
                [$id, $id]
            );
            $opIds = array_values(array_unique(array_merge([$id], $relatedIds)));
            $journalIds = self::fetchLinkedJournalHeadIds($conn, $opIds);
            $accountIds = self::fetchLinkedJournalAccountIds($conn, $journalIds, $opIds);
            $itemIds = self::fetchLinkedItemIds($conn, $opIds);

            self::deleteLinkedJournalEntries($conn, $journalIds, $opIds);
            self::deleteLinkedJournalHeads($conn, $journalIds, $opIds);
            self::deleteLinkedDetails($conn, $opIds);
            self::deleteLinkedHeaders($conn, $opIds);

            foreach (array_unique($accountIds) as $accountId) {
                self::recalcAccountBalance($conn, (int) $accountId);
            }
            self::syncItemQuantitiesByIds($conn, $itemIds);

            if ($manageTransaction) {
                $conn->commit();
            }
        } catch (Throwable $e) {
            if ($manageTransaction) {
                $conn->rollback();
            }
            throw $e;
        }
    }

    /** Soft-delete رأس ot_head */
    public static function softDeleteHeader(mysqli $conn, int $id): void
    {
        $stmt = $conn->prepare('UPDATE ot_head SET isdeleted = 1, crtime = crtime WHERE id = ?');
        if (!$stmt) {
            throw new Exception('فشل تحضير حذف الرأس: ' . $conn->error);
        }
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();
    }

    /** Soft-delete بنود fat_details المرتبطة بالفاتورة (pro_id = ot_head.id) */
    public static function softDeleteDetails(mysqli $conn, int $invoiceId): void
    {
        $stmt = $conn->prepare('UPDATE fat_details SET isdeleted = 1 WHERE pro_id = ?');
        if (!$stmt) {
            throw new Exception('فشل تحضير حذف البنود: ' . $conn->error);
        }
        $stmt->bind_param('i', $invoiceId);
        $stmt->execute();
        $stmt->close();

        // بعض المسارات القديمة تربط عبر fatid
        $stmt = $conn->prepare('UPDATE fat_details SET isdeleted = 1 WHERE fatid = ? AND isdeleted = 0');
        if ($stmt) {
            $stmt->bind_param('i', $invoiceId);
            $stmt->execute();
            $stmt->close();
        }
    }

    /** Soft-delete سندات القبض/الدفع المرتبطة (ot_head.op2) */
    public static function softDeleteLinkedPayments(mysqli $conn, int $invoiceId, ?int $proTybe = null): void
    {
        if ($proTybe !== null) {
            $stmt = $conn->prepare(
                'UPDATE ot_head SET isdeleted = 1, crtime = crtime WHERE op2 = ? AND pro_tybe = ?'
            );
            if (!$stmt) {
                throw new Exception('فشل تحضير حذف السندات: ' . $conn->error);
            }
            $stmt->bind_param('ii', $invoiceId, $proTybe);
        } else {
            $stmt = $conn->prepare(
                'UPDATE ot_head SET isdeleted = 1, crtime = crtime WHERE op2 = ?'
            );
            if (!$stmt) {
                throw new Exception('فشل تحضير حذف السندات: ' . $conn->error);
            }
            $stmt->bind_param('i', $invoiceId);
        }
        $stmt->execute();
        $stmt->close();
    }

    /** Soft-delete قيود مرتبطة بـ op_id */
    public static function softDeleteJournalsByOpId(mysqli $conn, int $opId): void
    {
        $stmt = $conn->prepare('UPDATE journal_entries SET isdeleted = 1 WHERE op_id = ?');
        if ($stmt) {
            $stmt->bind_param('i', $opId);
            $stmt->execute();
            $stmt->close();
        }
        $stmt = $conn->prepare('UPDATE journal_heads SET isdeleted = 1 WHERE op_id = ?');
        if ($stmt) {
            $stmt->bind_param('i', $opId);
            $stmt->execute();
            $stmt->close();
        }
    }

    /** Soft-delete قيود مرتبطة بـ op2 (سندات) */
    public static function softDeleteJournalsByOp2(mysqli $conn, int $op2): void
    {
        $stmt = $conn->prepare('UPDATE journal_entries SET isdeleted = 1 WHERE op2 = ?');
        if ($stmt) {
            $stmt->bind_param('i', $op2);
            $stmt->execute();
            $stmt->close();
        }
        $stmt = $conn->prepare('UPDATE journal_heads SET isdeleted = 1 WHERE op2 = ?');
        if ($stmt) {
            $stmt->bind_param('i', $op2);
            $stmt->execute();
            $stmt->close();
        }

        // بعض السجلات تربط journal_entries عبر journal_id فقط
        $stmt = $conn->prepare('SELECT id FROM journal_heads WHERE op2 = ?');
        if ($stmt) {
            $stmt->bind_param('i', $op2);
            $stmt->execute();
            $res = $stmt->get_result();
            while ($row = $res->fetch_assoc()) {
                $jid = (int) $row['id'];
                $s2 = $conn->prepare('UPDATE journal_entries SET isdeleted = 1 WHERE journal_id = ?');
                if ($s2) {
                    $s2->bind_param('i', $jid);
                    $s2->execute();
                    $s2->close();
                }
            }
            $stmt->close();
        }
    }

    /** @param array<int,int|string> $params */
    private static function fetchIds(mysqli $conn, string $sql, string $types, array $params): array
    {
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception('فشل تحضير استعلام الحذف: ' . $conn->error);
        }
        if ($types !== '') {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $res = $stmt->get_result();
        $ids = [];
        while ($row = $res->fetch_assoc()) {
            $ids[] = (int) reset($row);
        }
        $stmt->close();
        return $ids;
    }

    /** @param int[] $opIds */
    private static function fetchJournalHeadIds(mysqli $conn, array $opIds): array
    {
        $opIds = array_values(array_filter(array_map('intval', $opIds)));
        if (empty($opIds)) {
            return [];
        }
        $placeholders = implode(',', array_fill(0, count($opIds), '?'));
        $types = str_repeat('i', count($opIds) * 2);
        $sql = "SELECT id FROM journal_heads
                WHERE isdeleted = 0 AND (op_id IN ($placeholders) OR op2 IN ($placeholders))";
        return self::fetchIds($conn, $sql, $types, array_merge($opIds, $opIds));
    }

    /**
     * @param int[] $journalIds
     * @param int[] $opIds
     * @return int[]
     */
    private static function fetchJournalAccountIds(mysqli $conn, array $journalIds, array $opIds): array
    {
        $journalIds = array_values(array_filter(array_map('intval', $journalIds)));
        $opIds = array_values(array_filter(array_map('intval', $opIds)));
        $parts = [];
        $params = [];
        if (!empty($journalIds)) {
            $parts[] = 'journal_id IN (' . implode(',', array_fill(0, count($journalIds), '?')) . ')';
            $params = array_merge($params, $journalIds);
        }
        if (!empty($opIds)) {
            $ph = implode(',', array_fill(0, count($opIds), '?'));
            $parts[] = "op_id IN ($ph)";
            $parts[] = "op2 IN ($ph)";
            $params = array_merge($params, $opIds, $opIds);
        }
        if (empty($parts)) {
            return [];
        }
        $sql = 'SELECT DISTINCT account_id FROM journal_entries WHERE isdeleted = 0 AND (' . implode(' OR ', $parts) . ')';
        return self::fetchIds($conn, $sql, str_repeat('i', count($params)), $params);
    }

    /** @param int[] $journalIds @param int[] $opIds */
    private static function softDeleteJournalEntries(mysqli $conn, array $journalIds, array $opIds): void
    {
        $journalIds = array_values(array_filter(array_map('intval', $journalIds)));
        $opIds = array_values(array_filter(array_map('intval', $opIds)));
        $parts = [];
        $params = [];
        if (!empty($journalIds)) {
            $parts[] = 'journal_id IN (' . implode(',', array_fill(0, count($journalIds), '?')) . ')';
            $params = array_merge($params, $journalIds);
        }
        if (!empty($opIds)) {
            $ph = implode(',', array_fill(0, count($opIds), '?'));
            $parts[] = "op_id IN ($ph)";
            $parts[] = "op2 IN ($ph)";
            $params = array_merge($params, $opIds, $opIds);
        }
        if (empty($parts)) {
            return;
        }
        $sql = 'UPDATE journal_entries SET isdeleted = 1 WHERE isdeleted = 0 AND (' . implode(' OR ', $parts) . ')';
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception('فشل تحضير حذف تفاصيل القيود: ' . $conn->error);
        }
        $stmt->bind_param(str_repeat('i', count($params)), ...$params);
        $stmt->execute();
        $stmt->close();
    }

    /** @param int[] $journalIds */
    private static function softDeleteJournalHeadsByIds(mysqli $conn, array $journalIds): void
    {
        $journalIds = array_values(array_filter(array_map('intval', $journalIds)));
        if (empty($journalIds)) {
            return;
        }
        $placeholders = implode(',', array_fill(0, count($journalIds), '?'));
        $stmt = $conn->prepare("UPDATE journal_heads SET isdeleted = 1 WHERE id IN ($placeholders)");
        if (!$stmt) {
            throw new Exception('فشل تحضير حذف القيود: ' . $conn->error);
        }
        $stmt->bind_param(str_repeat('i', count($journalIds)), ...$journalIds);
        $stmt->execute();
        $stmt->close();
    }

    /** @param int[] $opIds @return int[] */
    private static function fetchLinkedJournalHeadIds(mysqli $conn, array $opIds): array
    {
        $opIds = array_values(array_filter(array_map('intval', $opIds)));
        if (empty($opIds)) {
            return [];
        }
        $placeholders = implode(',', array_fill(0, count($opIds), '?'));
        $types = str_repeat('i', count($opIds) * 2);
        $sql = "SELECT id FROM journal_heads WHERE op_id IN ($placeholders) OR op2 IN ($placeholders)";
        return self::fetchIds($conn, $sql, $types, array_merge($opIds, $opIds));
    }

    /**
     * @param int[] $journalIds
     * @param int[] $opIds
     * @return int[]
     */
    private static function fetchLinkedJournalAccountIds(mysqli $conn, array $journalIds, array $opIds): array
    {
        $journalIds = array_values(array_filter(array_map('intval', $journalIds)));
        $opIds = array_values(array_filter(array_map('intval', $opIds)));
        $parts = [];
        $params = [];
        if (!empty($journalIds)) {
            $parts[] = 'journal_id IN (' . implode(',', array_fill(0, count($journalIds), '?')) . ')';
            $params = array_merge($params, $journalIds);
        }
        if (!empty($opIds)) {
            $ph = implode(',', array_fill(0, count($opIds), '?'));
            $parts[] = "op_id IN ($ph)";
            $parts[] = "op2 IN ($ph)";
            $params = array_merge($params, $opIds, $opIds);
        }
        if (empty($parts)) {
            return [];
        }
        $sql = 'SELECT DISTINCT account_id FROM journal_entries WHERE ' . implode(' OR ', $parts);
        return self::fetchIds($conn, $sql, str_repeat('i', count($params)), $params);
    }

    /** @param int[] $opIds @return int[] */
    private static function fetchLinkedItemIds(mysqli $conn, array $opIds): array
    {
        $opIds = array_values(array_filter(array_map('intval', $opIds)));
        if (empty($opIds)) {
            return [];
        }
        $placeholders = implode(',', array_fill(0, count($opIds), '?'));
        $types = str_repeat('i', count($opIds) * 2);
        $sql = "SELECT DISTINCT item_id FROM fat_details WHERE fatid IN ($placeholders) OR pro_id IN ($placeholders)";
        return self::fetchIds($conn, $sql, $types, array_merge($opIds, $opIds));
    }

    /** @param int[] $journalIds @param int[] $opIds */
    private static function deleteLinkedJournalEntries(mysqli $conn, array $journalIds, array $opIds): void
    {
        $journalIds = array_values(array_filter(array_map('intval', $journalIds)));
        $opIds = array_values(array_filter(array_map('intval', $opIds)));
        $parts = [];
        $params = [];
        if (!empty($journalIds)) {
            $parts[] = 'journal_id IN (' . implode(',', array_fill(0, count($journalIds), '?')) . ')';
            $params = array_merge($params, $journalIds);
        }
        if (!empty($opIds)) {
            $ph = implode(',', array_fill(0, count($opIds), '?'));
            $parts[] = "op_id IN ($ph)";
            $parts[] = "op2 IN ($ph)";
            $params = array_merge($params, $opIds, $opIds);
        }
        if (empty($parts)) {
            return;
        }
        $sql = 'DELETE FROM journal_entries WHERE ' . implode(' OR ', $parts);
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception('فشل تحضير حذف تفاصيل القيود: ' . $conn->error);
        }
        $stmt->bind_param(str_repeat('i', count($params)), ...$params);
        $stmt->execute();
        $stmt->close();
    }

    /** @param int[] $journalIds @param int[] $opIds */
    private static function deleteLinkedJournalHeads(mysqli $conn, array $journalIds, array $opIds): void
    {
        $journalIds = array_values(array_filter(array_map('intval', $journalIds)));
        $opIds = array_values(array_filter(array_map('intval', $opIds)));
        $parts = [];
        $params = [];
        if (!empty($journalIds)) {
            $parts[] = 'id IN (' . implode(',', array_fill(0, count($journalIds), '?')) . ')';
            $params = array_merge($params, $journalIds);
        }
        if (!empty($opIds)) {
            $ph = implode(',', array_fill(0, count($opIds), '?'));
            $parts[] = "op_id IN ($ph)";
            $parts[] = "op2 IN ($ph)";
            $params = array_merge($params, $opIds, $opIds);
        }
        if (empty($parts)) {
            return;
        }
        $sql = 'DELETE FROM journal_heads WHERE ' . implode(' OR ', $parts);
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception('فشل تحضير حذف القيود: ' . $conn->error);
        }
        $stmt->bind_param(str_repeat('i', count($params)), ...$params);
        $stmt->execute();
        $stmt->close();
    }

    /** @param int[] $opIds */
    private static function deleteLinkedDetails(mysqli $conn, array $opIds): void
    {
        $opIds = array_values(array_filter(array_map('intval', $opIds)));
        if (empty($opIds)) {
            return;
        }
        $placeholders = implode(',', array_fill(0, count($opIds), '?'));
        $sql = "DELETE FROM fat_details WHERE fatid IN ($placeholders) OR pro_id IN ($placeholders)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception('فشل تحضير حذف البنود: ' . $conn->error);
        }
        $params = array_merge($opIds, $opIds);
        $stmt->bind_param(str_repeat('i', count($params)), ...$params);
        $stmt->execute();
        $stmt->close();
    }

    /** @param int[] $opIds */
    private static function deleteLinkedHeaders(mysqli $conn, array $opIds): void
    {
        $opIds = array_values(array_filter(array_map('intval', $opIds)));
        if (empty($opIds)) {
            return;
        }
        $placeholders = implode(',', array_fill(0, count($opIds), '?'));
        $sql = "DELETE FROM ot_head WHERE id IN ($placeholders)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception('فشل تحضير حذف الفاتورة: ' . $conn->error);
        }
        $stmt->bind_param(str_repeat('i', count($opIds)), ...$opIds);
        $stmt->execute();
        $stmt->close();
    }

    /** @param int[] $itemIds */
    private static function syncItemQuantitiesByIds(mysqli $conn, array $itemIds): void
    {
        $itemIds = array_values(array_unique(array_filter(array_map('intval', $itemIds))));
        if (empty($itemIds)) {
            return;
        }
        $placeholders = implode(',', array_fill(0, count($itemIds), '?'));
        $sql = "
            UPDATE myitems mi
            SET itmqty = (
                SELECT COALESCE(SUM(qty_in) - SUM(qty_out), 0)
                FROM fat_details fd
                WHERE fd.item_id = mi.id AND fd.isdeleted = 0
            )
            WHERE mi.id IN ($placeholders)
        ";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception('فشل تحضير تحديث الكميات: ' . $conn->error);
        }
        $stmt->bind_param(str_repeat('i', count($itemIds)), ...$itemIds);
        $stmt->execute();
        $stmt->close();
    }

    /**
     * إعادة حساب ربح الفاتورة من البنود وتحديث ot_head.profit
     */
    public static function recalcProfit(mysqli $conn, int $invoiceId): float
    {
        $stmt = $conn->prepare(
            'SELECT COALESCE(SUM(profit), 0) AS tprofit
             FROM fat_details
             WHERE (fatid = ? OR pro_id = ?) AND isdeleted = 0'
        );
        if (!$stmt) {
            throw new Exception('فشل تحضير حساب الربح: ' . $conn->error);
        }
        $stmt->bind_param('ii', $invoiceId, $invoiceId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        $profit = (float) ($row['tprofit'] ?? 0);
        $stmt = $conn->prepare('UPDATE ot_head SET profit = ?, crtime = crtime WHERE id = ?');
        if ($stmt) {
            $stmt->bind_param('di', $profit, $invoiceId);
            $stmt->execute();
            $stmt->close();
        }
        return $profit;
    }

    /**
     * Hard-purge للقيود والسندات والبنود قبل إعادة كتابة فاتورة عند التعديل.
     * Soft-delete لا يُستخدم هنا لأن triggers على journal_entries تحدّث acc_head.balance عند DELETE.
     * مسار مسح الفاتورة من المستخدم هو hardDelete().
     */
    public static function purgeRelatedForRewrite(mysqli $conn, int $invoiceId): void
    {
        $invoiceId = (int) $invoiceId;
        if ($invoiceId <= 0) {
            throw new InvalidArgumentException('معرّف فاتورة غير صالح');
        }

        $stmt = $conn->prepare('DELETE FROM fat_details WHERE fatid = ? OR pro_id = ?');
        if ($stmt) {
            $stmt->bind_param('ii', $invoiceId, $invoiceId);
            $stmt->execute();
            $stmt->close();
        }

        $journalIds = [];
        $stmt = $conn->prepare('SELECT id FROM journal_heads WHERE op_id = ? OR op2 = ?');
        if ($stmt) {
            $stmt->bind_param('ii', $invoiceId, $invoiceId);
            $stmt->execute();
            $res = $stmt->get_result();
            while ($row = $res->fetch_assoc()) {
                $journalIds[] = (int) $row['id'];
            }
            $stmt->close();
        }

        if (!empty($journalIds)) {
            $placeholders = implode(',', array_fill(0, count($journalIds), '?'));
            $types = str_repeat('i', count($journalIds));
            $stmt = $conn->prepare("DELETE FROM journal_entries WHERE journal_id IN ($placeholders)");
            if ($stmt) {
                $stmt->bind_param($types, ...$journalIds);
                $stmt->execute();
                $stmt->close();
            }
            $stmt = $conn->prepare("DELETE FROM journal_heads WHERE id IN ($placeholders)");
            if ($stmt) {
                $stmt->bind_param($types, ...$journalIds);
                $stmt->execute();
                $stmt->close();
            }
        }

        $stmt = $conn->prepare('DELETE FROM ot_head WHERE op2 = ?');
        if ($stmt) {
            $stmt->bind_param('i', $invoiceId);
            $stmt->execute();
            $stmt->close();
        }
    }

    // ─── Save helpers (header / journal / payments / details) ───

    public static function nextJournalNumber(mysqli $conn): int
    {
        $stmt = $conn->prepare('SELECT MAX(journal_id) as max_id FROM journal_heads');
        if (!$stmt) {
            throw new Exception('فشل تحضير رقم القيد: ' . $conn->error);
        }
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row && $row['max_id'] ? ((int) $row['max_id'] + 1) : 1;
    }

    public static function resolveSalesAccountId(mysqli $conn): int
    {
        $salesAccount = 99;
        $stmt = $conn->prepare(
            "SELECT ah.id FROM acc_head ah
             INNER JOIN acc_head parent ON ah.parent_id = parent.id
             WHERE parent.code LIKE '32%' AND ah.is_basic = 0 AND ah.isdeleted = 0
             ORDER BY ah.code LIMIT 1"
        );
        if ($stmt) {
            $stmt->execute();
            $row = $stmt->get_result()->fetch_assoc();
            if ($row) {
                $salesAccount = (int) $row['id'];
            }
            $stmt->close();
        }
        return $salesAccount;
    }

    /**
     * تحديث حقول الدفع على رأس الفاتورة (paid_amount / status / notes).
     */
    public static function updatePaymentFields(
        mysqli $conn,
        int $invoiceId,
        float $paidCash,
        float $paidBank,
        int $paymentFundId,
        int $paymentBankId,
        float $headnet
    ): array {
        $totalPaid = $paidCash + $paidBank;
        $change = max(0, $totalPaid - $headnet);
        // ملاحظة: الحقل remaining_amount في المسار الأصلي كان يُخزَّن فيه الباقي (change) وليس المتبقي على العميل
        $status = ($totalPaid >= $headnet) ? 'paid' : (($totalPaid > 0) ? 'partial' : 'unpaid');
        $notes = json_encode([
            'paid_cash' => $paidCash,
            'paid_bank' => $paidBank,
            'payment_fund_id' => $paymentFundId,
            'payment_bank_id' => $paymentBankId,
            'change_amount' => $change,
        ], JSON_UNESCAPED_UNICODE);

        $stmt = $conn->prepare(
            'UPDATE ot_head SET paid_amount = ?, remaining_amount = ?, payment_status = ?, payment_notes = ? WHERE id = ?'
        );
        if (!$stmt) {
            throw new Exception('فشل تحضير تحديث الدفع: ' . $conn->error);
        }
        $stmt->bind_param('ddssi', $totalPaid, $change, $status, $notes, $invoiceId);
        if (!$stmt->execute()) {
            throw new Exception('فشل تحديث حقول الدفع: ' . $stmt->error);
        }
        $stmt->close();

        return [
            'total_paid' => $totalPaid,
            'change' => $change,
            'status' => $status,
        ];
    }

    /**
     * حساب المبلغ الفعلي للكاش/البنك بعد خصم الباقي (الرجوع من الكاش أولاً).
     */
    public static function calcSplitPayment(float $paidCash, float $paidBank, float $headnet): array
    {
        $totalPaid = $paidCash + $paidBank;
        $change = max(0, $totalPaid - $headnet);
        $actualCash = max(0, $paidCash - $change);
        $actualBank = $paidBank;
        if ($change > $paidCash) {
            $remainingChange = $change - $paidCash;
            $actualCash = 0;
            $actualBank = max(0, $paidBank - $remainingChange);
        }
        return [
            'total_paid' => $totalPaid,
            'change' => $change,
            'actual_cash' => $actualCash,
            'actual_bank' => $actualBank,
        ];
    }

    /**
     * طرفا سند القبض/الدفع.
     * سند دفع: مدين الطرف (مورد/عميل) ودائن الصندوق أو البنك.
     * سند قبض: مدين الصندوق أو البنك ودائن الطرف.
     *
     * @return array{debit:int,credit:int}
     */
    public static function voucherSides(int $paidType, int $partyAccId, int $cashAccId): array
    {
        if ($paidType === self::ACCOUNTING_TYPES['PAYMENT']) {
            return ['debit' => $partyAccId, 'credit' => $cashAccId];
        }
        return ['debit' => $cashAccId, 'credit' => $partyAccId];
    }

    /**
     * إنشاء سند قبض/دفع مرتبط بفاتورة (ot_head.op2) + قيوده.
     *
     * @return int insert_id للسند
     */
    public static function createPaymentVoucher(mysqli $conn, array $opts): int
    {
        $paidType = (int) $opts['paid_type'];
        $amount = (float) $opts['amount'];
        $debitAcc = (int) $opts['debit_account'];
        $creditAcc = (int) $opts['credit_account'];
        $invoiceId = (int) $opts['invoice_id'];
        $proDate = (string) $opts['pro_date'];
        $empId = (int) ($opts['emp_id'] ?? 0);
        $userId = (int) ($opts['user_id'] ?? 0);
        $info = (string) ($opts['info'] ?? '');
        $details = (string) ($opts['details'] ?? '');

        if ($amount <= 0 || $debitAcc <= 0) {
            return 0;
        }

        $proId = self::getNextInvoiceNumber($conn, $paidType);
        $stmt = $conn->prepare(
            "INSERT INTO ot_head (
                pro_id, pro_tybe, is_journal, journal_tybe, info, pro_date,
                emp_id, acc1, acc2, pro_value, cost_center, profit, user, op2
            ) VALUES (?, ?, 1, ?, ?, ?, ?, ?, ?, ?, 1, 0, ?, ?)"
        );
        if (!$stmt) {
            throw new Exception('فشل تحضير سند الدفع: ' . $conn->error);
        }
        $stmt->bind_param(
            'iiissiiidii',
            $proId,
            $paidType,
            $paidType,
            $info,
            $proDate,
            $empId,
            $debitAcc,
            $creditAcc,
            $amount,
            $userId,
            $invoiceId
        );
        if (!$stmt->execute()) {
            throw new Exception('فشل إدخال سند الدفع: ' . $stmt->error);
        }
        $voucherId = (int) $conn->insert_id;
        $stmt->close();

        $journalNum = self::nextJournalNumber($conn);
        $stmt = $conn->prepare(
            'INSERT INTO journal_heads (journal_id, op_id, total, jdate, details, user, op2)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        if (!$stmt) {
            throw new Exception('فشل تحضير رأس قيد السند: ' . $conn->error);
        }
        $stmt->bind_param(
            'iidssii',
            $journalNum,
            $voucherId,
            $amount,
            $proDate,
            $details,
            $userId,
            $invoiceId
        );
        if (!$stmt->execute()) {
            throw new Exception('فشل إدخال رأس قيد السند: ' . $stmt->error);
        }
        $journalLastId = (int) $conn->insert_id;
        $stmt->close();

        $stmt = $conn->prepare(
            'INSERT INTO journal_entries (journal_id, account_id, debit, credit, tybe, op2)
             VALUES (?, ?, ?, 0, 0, ?)'
        );
        $stmt->bind_param('iidi', $journalLastId, $debitAcc, $amount, $invoiceId);
        $stmt->execute();
        $stmt->close();

        $stmt = $conn->prepare(
            'INSERT INTO journal_entries (journal_id, account_id, debit, credit, tybe, op2)
             VALUES (?, ?, 0, ?, 1, ?)'
        );
        $stmt->bind_param('iidi', $journalLastId, $creditAcc, $amount, $invoiceId);
        $stmt->execute();
        $stmt->close();

        return $voucherId;
    }

    /**
     * إنشاء سندات كاش + بنك لفاتورة جديدة (مسار doadd).
     */
    public static function createSplitPaymentVouchers(
        mysqli $conn,
        int $invoiceId,
        array $config,
        string $info,
        string $proDate,
        int $empId,
        int $userId,
        int $partyAccId,
        float $paidCash,
        float $paidBank,
        int $paymentFundId,
        int $paymentBankId,
        float $headnet,
        $proDisplayId = null
    ): array {
        $calc = self::calcSplitPayment($paidCash, $paidBank, $headnet);
        $created = ['cash' => 0, 'bank' => 0];
        $display = $proDisplayId ?? $invoiceId;
        $paidType = (int) $config['paid_type'];
        $paidNote = (string) ($config['paid_note'] ?? 'سند');
        $cashSides = self::voucherSides($paidType, $partyAccId, $paymentFundId);
        $cashMove = ($paidType === self::ACCOUNTING_TYPES['PAYMENT']) ? 'دفع كاش' : 'قبض كاش';
        $bankMove = ($paidType === self::ACCOUNTING_TYPES['PAYMENT']) ? 'دفع صرافة' : 'قبض صرافة';

        if ($calc['actual_cash'] > 0 && $paymentFundId > 0) {
            $created['cash'] = self::createPaymentVoucher($conn, [
                'paid_type' => $paidType,
                'amount' => $calc['actual_cash'],
                'debit_account' => $cashSides['debit'],
                'credit_account' => $cashSides['credit'],
                'invoice_id' => $invoiceId,
                'pro_date' => $proDate,
                'emp_id' => $empId,
                'user_id' => $userId,
                'info' => $info . ' - ' . $cashMove,
                'details' => $paidNote . ' كاش _ ' . $display,
            ]);
        }

        if ($calc['actual_bank'] > 0 && $paymentBankId > 0) {
            $bankSides = self::voucherSides($paidType, $partyAccId, $paymentBankId);
            $created['bank'] = self::createPaymentVoucher($conn, [
                'paid_type' => $paidType,
                'amount' => $calc['actual_bank'],
                'debit_account' => $bankSides['debit'],
                'credit_account' => $bankSides['credit'],
                'invoice_id' => $invoiceId,
                'pro_date' => $proDate,
                'emp_id' => $empId,
                'user_id' => $userId,
                'info' => $info . ' - ' . $bankMove,
                'details' => $paidNote . ' صرافة _ ' . $display,
            ]);
        }

        return $created + $calc;
    }

    /**
     * مزامنة دفعة واحدة بسيطة (مسار doedit التقليدي: paid واحد).
     */
    public static function syncSimplePayment(
        mysqli $conn,
        int $invoiceId,
        int $proTybe,
        array $config,
        array $accounts,
        string $info,
        string $proDate,
        int $empId,
        int $userId,
        float $paid
    ): void {
        $paidType = (int) $config['paid_type'];
        $partyAccId = (int) ($paidType === self::ACCOUNTING_TYPES['PAYMENT'] ? $accounts['acc5'] : $accounts['acc6']);
        $cashAccId = (int) ($paidType === self::ACCOUNTING_TYPES['PAYMENT'] ? $accounts['acc6'] : $accounts['acc5']);
        $sides = self::voucherSides($paidType, $partyAccId, $cashAccId);
        $debitAcc = $sides['debit'];
        $creditAcc = $sides['credit'];

        $stmt = $conn->prepare(
            'SELECT id FROM ot_head WHERE op2 = ? AND pro_tybe IN (1, 2) AND isdeleted = 0 ORDER BY id ASC'
        );
        $stmt->bind_param('i', $invoiceId);
        $stmt->execute();
        $res = $stmt->get_result();
        $voucherIds = [];
        while ($row = $res->fetch_assoc()) {
            $voucherIds[] = (int) $row['id'];
        }
        $stmt->close();

        if ($paid <= 0) {
            if (!empty($voucherIds)) {
                self::softDeleteLinkedPayments($conn, $invoiceId, self::ACCOUNTING_TYPES['RECEIPT']);
                self::softDeleteLinkedPayments($conn, $invoiceId, self::ACCOUNTING_TYPES['PAYMENT']);
                self::softDeleteJournalsByOp2($conn, $invoiceId);
            }
            return;
        }

        if (empty($voucherIds)) {
            self::createPaymentVoucher($conn, [
                'paid_type' => $paidType,
                'amount' => $paid,
                'debit_account' => $debitAcc,
                'credit_account' => $creditAcc,
                'invoice_id' => $invoiceId,
                'pro_date' => $proDate,
                'emp_id' => $empId,
                'user_id' => $userId,
                'info' => $info,
                'details' => ($config['paid_note'] ?? 'سند') . ' _ ' . $invoiceId,
            ]);
            return;
        }

        $keepId = $voucherIds[0];
        $stmt = $conn->prepare(
            'UPDATE ot_head SET info = ?, pro_date = ?, emp_id = ?, acc1 = ?, acc2 = ?, pro_value = ?, pro_tybe = ?, journal_tybe = ?, crtime = crtime
             WHERE id = ?'
        );
        $stmt->bind_param('ssiiidiii', $info, $proDate, $empId, $debitAcc, $creditAcc, $paid, $paidType, $paidType, $keepId);
        if (!$stmt->execute()) {
            throw new Exception('فشل تحديث سند الدفع: ' . $stmt->error);
        }
        $stmt->close();

        $touched = [$debitAcc, $creditAcc];
        $stmt = $conn->prepare('SELECT id FROM journal_heads WHERE op_id = ? AND isdeleted = 0');
        $stmt->bind_param('i', $keepId);
        $stmt->execute();
        $jres = $stmt->get_result();
        $journalIds = [];
        while ($row = $jres->fetch_assoc()) {
            $journalIds[] = (int) $row['id'];
        }
        $stmt->close();

        if (empty($journalIds)) {
            $stmt = $conn->prepare('SELECT id FROM journal_heads WHERE op2 = ? AND op_id <> ? AND isdeleted = 0');
            $stmt->bind_param('ii', $invoiceId, $invoiceId);
            $stmt->execute();
            $jres = $stmt->get_result();
            while ($row = $jres->fetch_assoc()) {
                $journalIds[] = (int) $row['id'];
            }
            $stmt->close();
        }

        foreach ($journalIds as $jr) {
            $stmt = $conn->prepare('SELECT account_id FROM journal_entries WHERE journal_id = ? AND isdeleted = 0');
            $stmt->bind_param('i', $jr);
            $stmt->execute();
            $eres = $stmt->get_result();
            while ($erow = $eres->fetch_assoc()) {
                $touched[] = (int) $erow['account_id'];
            }
            $stmt->close();

            $stmt = $conn->prepare('UPDATE journal_heads SET total = ?, jdate = ? WHERE id = ?');
            $stmt->bind_param('dsi', $paid, $proDate, $jr);
            $stmt->execute();
            $stmt->close();

            $stmt = $conn->prepare(
                'UPDATE journal_entries SET account_id = ?, debit = ?, credit = 0 WHERE journal_id = ? AND tybe = 0'
            );
            $stmt->bind_param('idi', $debitAcc, $paid, $jr);
            $stmt->execute();
            $stmt->close();

            $stmt = $conn->prepare(
                'UPDATE journal_entries SET account_id = ?, debit = 0, credit = ? WHERE journal_id = ? AND tybe = 1'
            );
            $stmt->bind_param('idi', $creditAcc, $paid, $jr);
            $stmt->execute();
            $stmt->close();
        }

        foreach (array_slice($voucherIds, 1) as $extraId) {
            $stmt = $conn->prepare('UPDATE ot_head SET isdeleted = 1, crtime = crtime WHERE id = ?');
            $stmt->bind_param('i', $extraId);
            $stmt->execute();
            $stmt->close();

            $stmt = $conn->prepare('SELECT id FROM journal_heads WHERE op_id = ? AND isdeleted = 0');
            $stmt->bind_param('i', $extraId);
            $stmt->execute();
            $xres = $stmt->get_result();
            while ($xrow = $xres->fetch_assoc()) {
                $xid = (int) $xrow['id'];
                $s2 = $conn->prepare('SELECT account_id FROM journal_entries WHERE journal_id = ? AND isdeleted = 0');
                $s2->bind_param('i', $xid);
                $s2->execute();
                $ar = $s2->get_result();
                while ($arow = $ar->fetch_assoc()) {
                    $touched[] = (int) $arow['account_id'];
                }
                $s2->close();
                $s3 = $conn->prepare('UPDATE journal_entries SET isdeleted = 1 WHERE journal_id = ?');
                $s3->bind_param('i', $xid);
                $s3->execute();
                $s3->close();
                $s4 = $conn->prepare('UPDATE journal_heads SET isdeleted = 1 WHERE id = ?');
                $s4->bind_param('i', $xid);
                $s4->execute();
                $s4->close();
            }
            $stmt->close();
        }

        foreach (array_unique($touched) as $accId) {
            self::recalcAccountBalance($conn, (int) $accId);
        }
    }

    /** إعادة حساب رصيد حساب من قيوده غير المحذوفة. */
    public static function recalcAccountBalance(mysqli $conn, int $accountId): void
    {
        if ($accountId <= 0) {
            return;
        }
        $stmt = $conn->prepare(
            'UPDATE acc_head SET balance = (
                SELECT COALESCE(SUM(debit), 0) - COALESCE(SUM(credit), 0)
                FROM journal_entries
                WHERE account_id = ? AND isdeleted = 0
             ) WHERE id = ?'
        );
        if (!$stmt) {
            return;
        }
        $stmt->bind_param('ii', $accountId, $accountId);
        $stmt->execute();
        $stmt->close();
    }

    /**
     * إنشاء قيد الفاتورة الرئيسي (op_id) — مسار الإضافة.
     */
    public static function createMainJournal(
        mysqli $conn,
        int $invoiceId,
        int $proTybe,
        array $config,
        array $accounts,
        int $partyAccId,
        float $headnet,
        string $proDate,
        int $userId
    ): int {
        $journalNum = self::nextJournalNumber($conn);
        $details = ($config['note'] ?? 'فاتورة') . ' _ ' . $invoiceId;
        $stmt = $conn->prepare(
            'INSERT INTO journal_heads (journal_id, total, jdate, details, user, op_id)
             VALUES (?, ?, ?, ?, ?, ?)'
        );
        if (!$stmt) {
            throw new Exception('فشل تحضير رأس القيد: ' . $conn->error);
        }
        $stmt->bind_param('idssii', $journalNum, $headnet, $proDate, $details, $userId, $invoiceId);
        if (!$stmt->execute()) {
            throw new Exception('فشل إدخال رأس القيد: ' . $stmt->error);
        }
        $journalLastId = (int) $conn->insert_id;
        $stmt->close();

        if (in_array($proTybe, [self::INVOICE_TYPES['SALES'], self::INVOICE_TYPES['POS']], true)) {
            $debitAcc = $partyAccId;
            $creditAcc = self::resolveSalesAccountId($conn);
        } else {
            $debitAcc = (int) $accounts['acc1'];
            $creditAcc = (int) $accounts['acc2'];
        }

        $stmt = $conn->prepare(
            'INSERT INTO journal_entries (journal_id, account_id, debit, credit, tybe, op_id)
             VALUES (?, ?, ?, 0, 0, ?)'
        );
        $stmt->bind_param('iidi', $journalLastId, $debitAcc, $headnet, $invoiceId);
        if (!$stmt->execute()) {
            throw new Exception('فشل إدخال القيد المدين: ' . $stmt->error);
        }
        $stmt->close();

        $stmt = $conn->prepare(
            'INSERT INTO journal_entries (journal_id, account_id, debit, credit, tybe, op_id)
             VALUES (?, ?, 0, ?, 1, ?)'
        );
        $stmt->bind_param('iidi', $journalLastId, $creditAcc, $headnet, $invoiceId);
        if (!$stmt->execute()) {
            throw new Exception('فشل إدخال القيد الدائن: ' . $stmt->error);
        }
        $stmt->close();

        return $journalLastId;
    }

    /**
     * تحديث قيد الفاتورة الرئيسي عند التعديل.
     */
    public static function updateMainJournal(
        mysqli $conn,
        int $invoiceId,
        int $proTybe,
        array $config,
        array $accounts,
        int $partyAccId,
        float $headnet,
        string $proDate
    ): void {
        $details = ($config['note'] ?? 'فاتورة') . ' _ ' . $invoiceId;
        $stmt = $conn->prepare(
            'UPDATE journal_heads SET total = ?, jdate = ?, details = ? WHERE op_id = ? AND isdeleted = 0'
        );
        $stmt->bind_param('dssi', $headnet, $proDate, $details, $invoiceId);
        if (!$stmt->execute()) {
            throw new Exception('فشل تحديث رأس القيد: ' . $stmt->error);
        }
        $stmt->close();

        $stmt = $conn->prepare('SELECT id FROM journal_heads WHERE op_id = ? AND isdeleted = 0 LIMIT 1');
        $stmt->bind_param('i', $invoiceId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if (!$row) {
            return;
        }
        $journalId = (int) $row['id'];

        if (in_array($proTybe, [self::INVOICE_TYPES['SALES'], self::INVOICE_TYPES['POS']], true)) {
            $debitAcc = $partyAccId;
            $creditAcc = self::resolveSalesAccountId($conn);
        } else {
            $debitAcc = (int) $accounts['acc1'];
            $creditAcc = (int) $accounts['acc2'];
        }

        $stmt = $conn->prepare(
            'UPDATE journal_entries SET account_id = ?, debit = ?, credit = 0 WHERE journal_id = ? AND tybe = 0'
        );
        $stmt->bind_param('idi', $debitAcc, $headnet, $journalId);
        $stmt->execute();
        $stmt->close();

        $stmt = $conn->prepare(
            'UPDATE journal_entries SET account_id = ?, debit = 0, credit = ? WHERE journal_id = ? AND tybe = 1'
        );
        $stmt->bind_param('idi', $creditAcc, $headnet, $journalId);
        $stmt->execute();
        $stmt->close();
    }

    /**
     * فئات السعر: قطاعي / جملة / السوق.
     * يعيد تسمية القيم الافتراضية القديمة (سعر 1 / سعر 2) ويضيف الناقص.
     */
    public static function priceLists(mysqli $conn): array
    {
        $defaults = [1 => 'قطاعي', 2 => 'جملة', 3 => 'السوق'];
        $legacy = [1 => 'سعر 1', 2 => 'سعر 2'];
        $fallback = [
            ['id' => 1, 'pname' => 'قطاعي'],
            ['id' => 2, 'pname' => 'جملة'],
            ['id' => 3, 'pname' => 'السوق'],
        ];

        try {
            foreach ($defaults as $id => $name) {
                $stmt = $conn->prepare('SELECT pname FROM price_lists WHERE id = ? LIMIT 1');
                if (!$stmt) {
                    return $fallback;
                }
                $stmt->bind_param('i', $id);
                $stmt->execute();
                $row = $stmt->get_result()->fetch_assoc();
                $stmt->close();

                if (!$row) {
                    $ins = $conn->prepare('INSERT INTO price_lists (id, pname, isdeleted) VALUES (?, ?, 0)');
                    if ($ins) {
                        $ins->bind_param('is', $id, $name);
                        $ins->execute();
                        $ins->close();
                    }
                } elseif (isset($legacy[$id]) && trim((string) $row['pname']) === $legacy[$id]) {
                    $upd = $conn->prepare('UPDATE price_lists SET pname = ? WHERE id = ?');
                    if ($upd) {
                        $upd->bind_param('si', $name, $id);
                        $upd->execute();
                        $upd->close();
                    }
                }
            }

            $res = $conn->query('SELECT id, pname FROM price_lists WHERE isdeleted = 0 ORDER BY id');
            if (!$res) {
                return $fallback;
            }
            $rows = $res->fetch_all(MYSQLI_ASSOC);
            return $rows ?: $fallback;
        } catch (Throwable $e) {
            return $fallback;
        }
    }

    public static function echoPriceListOptions(mysqli $conn, int $selected = 1): void
    {
        foreach (self::priceLists($conn) as $plist) {
            $id = (int) ($plist['id'] ?? 0);
            if ($id < 1 || $id > 3) {
                continue;
            }
            $sel = $id === $selected ? ' selected' : '';
            echo '<option value="' . $id . '"' . $sel . '>' . htmlspecialchars((string) $plist['pname'], ENT_QUOTES, 'UTF-8') . '</option>';
        }
    }

    public static function echoPriceListSelect(mysqli $conn, int $selected = 1, string $class = 'form-control form-control-sm', string $style = ''): void
    {
        $styleAttr = $style !== '' ? ' style="' . htmlspecialchars($style, ENT_QUOTES, 'UTF-8') . '"' : '';
        echo '<select name="price_list" id="invoicePriceList" class="' . htmlspecialchars($class, ENT_QUOTES, 'UTF-8') . '"' . $styleAttr . '>';
        self::echoPriceListOptions($conn, $selected);
        echo '</select>';
    }

    public static function tierPrice(array $row, int $listId): float
    {
        $p1 = (float) ($row['price1'] ?? 0);
        $p2 = (float) ($row['price2'] ?? 0);
        $p3 = (float) ($row['price3'] ?? 0);
        if ($p3 <= 0) {
            $p3 = (float) ($row['market_price'] ?? 0);
        }
        $picked = $p1;
        if ($listId === 2) {
            $picked = $p2;
        } elseif ($listId >= 3) {
            $picked = $p3;
        }
        if ($picked <= 0) {
            $picked = $p1;
        }
        return $picked;
    }

    /**
     * إدخال رأس فاتورة جديدة — يُرجع insert_id.
     */
    public static function insertHeader(mysqli $conn, array $d): int
    {
        $stmt = $conn->prepare(
            "INSERT INTO ot_head (
                pro_id, pro_tybe, is_stock, is_journal, journal_tybe, info, pro_date,
                accural_date, pro_pattren, pro_serial, price_list, store_id, emp_id,
                emp2_id, acc1, acc2, pro_value, fat_cost, cost_center, profit,
                fat_total, fat_disc, fat_disc_per, fat_plus, fat_plus_per,
                fat_tax, fat_tax_per, fat_net, user, jal_name, jal_notes, jal_amount
            ) VALUES (
                ?, ?, 1, 1, ?, ?, ?, ?, 1, ?, ?, ?, ?, ?, ?, ?, ?, 0, 1, 0,
                ?, ?, ?, ?, ?, 0, 0, ?, ?, ?, ?, ?
            )"
        );
        if (!$stmt) {
            throw new Exception('فشل تحضير إدخال الفاتورة: ' . $conn->error);
        }

        $proId = $d['pro_id'];
        $proTybe = $d['pro_tybe'];
        $info = $d['info'] ?? '';
        $proDate = $d['pro_date'];
        $accural = $d['accural_date'] ?? '';
        $serial = $d['pro_serial'] ?? '';
        $priceList = (string) max(1, (int) ($d['price_list'] ?? 1));
        $storeId = $d['store_id'];
        $empId = $d['emp_id'];
        $emp2Id = $d['emp2_id'] ?? $empId;
        $acc1 = $d['acc1'];
        $acc2 = $d['acc2'];
        $headtotal = $d['headtotal'];
        $headdisc = $d['headdisc'];
        $discPer = $d['fat_disc_per'];
        $headplus = $d['headplus'];
        $plusPer = $d['fat_plus_per'];
        $headnet = $d['headnet'];
        $userId = $d['user_id'];
        $jalName = (string) ($d['jal_name'] ?? '');
        $jalNotes = (string) ($d['jal_notes'] ?? '');
        $jalAmount = $d['jal_amount'] ?? 0;

        // مطابق لـ doadd الأصلي (كل المعاملات كـ strings في bind)
        $stmt->bind_param(
            'ssssssssssssssssssssssss',
            $proId,
            $proTybe,
            $proTybe,
            $info,
            $proDate,
            $accural,
            $serial,
            $priceList,
            $storeId,
            $empId,
            $emp2Id,
            $acc1,
            $acc2,
            $headtotal,
            $headtotal,
            $headdisc,
            $discPer,
            $headplus,
            $plusPer,
            $headnet,
            $userId,
            $jalName,
            $jalNotes,
            $jalAmount
        );
        if (!$stmt->execute()) {
            throw new Exception('فشل إدخال الفاتورة: ' . $stmt->error);
        }
        $id = (int) $conn->insert_id;
        $stmt->close();
        return $id;
    }

    public static function updateHeader(mysqli $conn, int $invoiceId, array $d): void
    {
        $stmt = $conn->prepare(
            "UPDATE ot_head SET
                info = ?, pro_date = ?, accural_date = ?, pro_serial = ?, price_list = ?, store_id = ?,
                emp_id = ?, acc1 = ?, acc2 = ?, pro_value = ?, fat_cost = 0,
                fat_total = ?, fat_disc = ?, fat_disc_per = ?, fat_plus = ?, fat_plus_per = ?,
                fat_net = ?, acc_fund = ?, crtime = crtime
             WHERE id = ?"
        );
        if (!$stmt) {
            throw new Exception('فشل تحضير تحديث الفاتورة: ' . $conn->error);
        }
        $info = $d['info'] ?? '';
        $proDate = $d['pro_date'];
        $accural = $d['accural_date'] ?? '';
        $serial = $d['pro_serial'] ?? '';
        $priceList = max(1, (int) ($d['price_list'] ?? 1));
        $storeId = (int) $d['store_id'];
        $empId = (int) $d['emp_id'];
        $acc1 = (int) $d['acc1'];
        $acc2 = (int) $d['acc2'];
        $headtotal = (float) $d['headtotal'];
        $headdisc = (float) $d['headdisc'];
        $discPer = (float) $d['fat_disc_per'];
        $headplus = (float) $d['headplus'];
        $plusPer = (float) $d['fat_plus_per'];
        $headnet = (float) $d['headnet'];
        $fundId = (int) ($d['fund_id'] ?? 0);

        $stmt->bind_param(
            'ssssiiiiidddddddii',
            $info,
            $proDate,
            $accural,
            $serial,
            $priceList,
            $storeId,
            $empId,
            $acc1,
            $acc2,
            $headtotal,
            $headtotal,
            $headdisc,
            $discPer,
            $headplus,
            $plusPer,
            $headnet,
            $fundId,
            $invoiceId
        );
        if (!$stmt->execute()) {
            throw new Exception('فشل تحديث الفاتورة: ' . $stmt->error);
        }
        $stmt->close();
    }

    /**
     * تحديث رأس فاتورة عند إعادة الكتابة من doadd (edit_id / POS).
     * يحافظ على pro_date الأصلي ويحدّث pro_tybe + jal_* + user.
     */
    public static function updateHeaderForRewrite(mysqli $conn, int $invoiceId, array $d): void
    {
        $stmt = $conn->prepare(
            "UPDATE ot_head SET
                pro_tybe = ?, info = ?, accural_date = ?,
                pro_serial = ?, price_list = ?, store_id = ?, emp_id = ?, emp2_id = ?,
                acc1 = ?, acc2 = ?, pro_value = ?, fat_total = ?,
                fat_disc = ?, fat_disc_per = ?, fat_plus = ?, fat_plus_per = ?,
                fat_net = ?, user = ?, jal_name = ?, jal_notes = ?, jal_amount = ?
             WHERE id = ?"
        );
        if (!$stmt) {
            throw new Exception('فشل تحضير تحديث رأس الفاتورة: ' . $conn->error);
        }

        $proTybe = $d['pro_tybe'];
        $info = $d['info'] ?? '';
        $accural = $d['accural_date'] ?? '';
        $serial = $d['pro_serial'] ?? '';
        $priceList = (string) max(1, (int) ($d['price_list'] ?? 1));
        $storeId = $d['store_id'];
        $empId = $d['emp_id'];
        $emp2Id = $d['emp2_id'] ?? $empId;
        $acc1 = $d['acc1'];
        $acc2 = $d['acc2'];
        $headtotal = $d['headtotal'];
        $headdisc = $d['headdisc'];
        $discPer = $d['fat_disc_per'];
        $headplus = $d['headplus'];
        $plusPer = $d['fat_plus_per'];
        $headnet = $d['headnet'];
        $userId = $d['user_id'];
        $jalName = $d['jal_name'] ?? '';
        $jalNotes = $d['jal_notes'] ?? '';
        $jalAmount = $d['jal_amount'] ?? 0;

        $stmt->bind_param(
            'ssssssssssssssssssssss',
            $proTybe,
            $info,
            $accural,
            $serial,
            $priceList,
            $storeId,
            $empId,
            $emp2Id,
            $acc1,
            $acc2,
            $headtotal,
            $headtotal,
            $headdisc,
            $discPer,
            $headplus,
            $plusPer,
            $headnet,
            $userId,
            $jalName,
            $jalNotes,
            $jalAmount,
            $invoiceId
        );
        if (!$stmt->execute()) {
            throw new Exception('فشل تحديث رأس الفاتورة: ' . $stmt->error);
        }
        $stmt->close();
    }

    public static function setTableId(mysqli $conn, int $invoiceId, int $tableId): void
    {
        if ($tableId <= 0 || $invoiceId <= 0) {
            return;
        }
        $stmt = $conn->prepare('UPDATE ot_head SET table_id = ? WHERE id = ?');
        if ($stmt) {
            $stmt->bind_param('ii', $tableId, $invoiceId);
            $stmt->execute();
            $stmt->close();
        }
    }

    /**
     * Soft-delete البنود ثم إعادة إدراجها من مصفوفة أسطر موحّدة.
     *
     * @param list<array{item_id:int,qty:float,price:float,disc:float,disc_pct:float,sell_price?:float,u_val:float,crtime?:string}> $lines
     */
    public static function replaceDetails(
        mysqli $conn,
        int $invoiceId,
        int $proTybe,
        int $storeId,
        array $lines,
        bool $softDeleteFirst = true,
        bool $withCrtime = false
    ): void {
        if ($softDeleteFirst) {
            self::softDeleteDetails($conn, $invoiceId);
        }

        if ($withCrtime) {
            $stmtDetails = $conn->prepare(
                "INSERT INTO fat_details (
                    pro_tybe, pro_id, item_id, u_val, qty_in, qty_out, price,
                    discount, disc_pct, det_value, fatid, fat_tybe, det_store, cost_price, profit, crtime
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );
        } else {
            $stmtDetails = $conn->prepare(
                "INSERT INTO fat_details (
                    pro_tybe, pro_id, item_id, u_val, qty_in, qty_out, price,
                    discount, disc_pct, det_value, fatid, fat_tybe, det_store, cost_price, profit
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );
        }
        if (!$stmtDetails) {
            throw new Exception('فشل تحضير تفاصيل الفاتورة: ' . $conn->error);
        }

        $stmtItem = $conn->prepare('SELECT cost_price, itmqty, price1 FROM myitems WHERE id = ?');
        $stmtUpdate = $conn->prepare('UPDATE myitems SET last_price = ?, cost_price = ?, price1 = ? WHERE id = ?');
        if (!$stmtItem || !$stmtUpdate) {
            throw new Exception('فشل تحضير استعلامات الصنف: ' . $conn->error);
        }

        foreach ($lines as $line) {
            $itemId = (int) ($line['item_id'] ?? 0);
            if ($itemId <= 0) {
                continue;
            }
            $qty = (float) ($line['qty'] ?? 1);
            $price = (float) ($line['price'] ?? 0);
            $disc = (float) ($line['disc'] ?? 0);
            $discPct = (float) ($line['disc_pct'] ?? 0);
            $sellPrice = (float) ($line['sell_price'] ?? 0);
            $uVal = (float) ($line['u_val'] ?? 1);
            if ($uVal <= 0) {
                $uVal = 1;
            }

            [$qtyIn, $qtyOut] = self::resolveLineQty($proTybe, $qty, $uVal);
            $detValue = $qty * ($price - $disc);

            $stmtItem->bind_param('i', $itemId);
            $stmtItem->execute();
            $rowbl = $stmtItem->get_result()->fetch_assoc();
            if (!$rowbl) {
                throw new Exception('صنف غير موجود: ' . $itemId);
            }

            $oldPrice = (float) $rowbl['cost_price'];
            // doadd: رصيد حقيقي؛ doedit (withCrtime): itmqty كما كان
            $oldQty = $withCrtime
                ? (float) ($rowbl['itmqty'] ?? 0)
                : self::getRealStockQuantity($conn, $itemId);
            $existingPrice1 = (float) $rowbl['price1'];
            $costPrice = $oldPrice;
            $itmProfit = 0.0;

            $costUpdateTypes = $withCrtime
                ? [self::INVOICE_TYPES['PURCHASE']]
                : [self::INVOICE_TYPES['PURCHASE'], self::INVOICE_TYPES['PURCHASE_ORDER']];

            if (in_array($proTybe, $costUpdateTypes, true)) {
                $unitPrice = $price / $uVal;
                $oldBalance = $oldPrice * $oldQty;
                $newBalance = $qtyIn * $unitPrice;
                $totalBalance = $oldBalance + $newBalance;
                $totalQty = $oldQty + $qtyIn;
                if ($totalQty > 0) {
                    $costPrice = $totalBalance / $totalQty;
                }
                $sellUnit = ($sellPrice > 0) ? ($sellPrice / $uVal) : $existingPrice1;
                $stmtUpdate->bind_param('dddi', $unitPrice, $costPrice, $sellUnit, $itemId);
                if (!$stmtUpdate->execute()) {
                    throw new Exception('فشل تحديث الصنف ' . $itemId);
                }
                $price = $unitPrice;
            } elseif (in_array($proTybe, [self::INVOICE_TYPES['SALES'], self::INVOICE_TYPES['POS'], self::INVOICE_TYPES['OFFER']], true)) {
                $unitPrice = $price / $uVal;
                $itmProfit = $qty * $uVal * ($unitPrice - $oldPrice);
                $price = $unitPrice;
            }

            if ($withCrtime) {
                $crtime = !empty($line['crtime']) ? (string) $line['crtime'] : date('Y-m-d H:i:s');
                $stmtDetails->bind_param(
                    'iiiidddddiiiidds',
                    $proTybe,
                    $invoiceId,
                    $itemId,
                    $uVal,
                    $qtyIn,
                    $qtyOut,
                    $price,
                    $disc,
                    $discPct,
                    $detValue,
                    $invoiceId,
                    $proTybe,
                    $storeId,
                    $costPrice,
                    $itmProfit,
                    $crtime
                );
            } else {
                $stmtDetails->bind_param(
                    'sssssssssssssss',
                    $proTybe,
                    $invoiceId,
                    $itemId,
                    $uVal,
                    $qtyIn,
                    $qtyOut,
                    $price,
                    $disc,
                    $discPct,
                    $detValue,
                    $invoiceId,
                    $proTybe,
                    $storeId,
                    $costPrice,
                    $itmProfit
                );
            }
            if (!$stmtDetails->execute()) {
                throw new Exception('فشل إدخال تفاصيل الصنف ' . $itemId);
            }
        }

        $stmtDetails->close();
        $stmtItem->close();
        $stmtUpdate->close();
    }

    /** @return array{0:float,1:float} [qty_in, qty_out] */
    public static function resolveLineQty(int $proTybe, float $qty, float $uVal): array
    {
        if (in_array($proTybe, [self::INVOICE_TYPES['PURCHASE_ORDER'], self::INVOICE_TYPES['SALES_ORDER'], self::INVOICE_TYPES['OFFER']], true)) {
            return [0.0, 0.0];
        }
        if (in_array($proTybe, [self::INVOICE_TYPES['PURCHASE'], self::INVOICE_TYPES['SALES_RETURN']], true)) {
            return [$qty * $uVal, 0.0];
        }
        if ($proTybe === self::INVOICE_TYPES['PURCHASE_RETURN']) {
            return [0.0, $qty * $uVal];
        }
        if (in_array($proTybe, [self::INVOICE_TYPES['SALES'], self::INVOICE_TYPES['POS']], true)) {
            return [0.0, $qty * $uVal];
        }
        return [0.0, 0.0];
    }

    public static function syncItemQuantities(mysqli $conn, int $invoiceId): void
    {
        $sql = "
            UPDATE myitems mi
            SET itmqty = (
                SELECT COALESCE(SUM(qty_in) - SUM(qty_out), 0)
                FROM fat_details fd
                WHERE fd.item_id = mi.id AND fd.isdeleted = 0
            )
            WHERE mi.id IN (
                SELECT DISTINCT item_id FROM fat_details WHERE fatid = ? AND isdeleted = 0
            )
        ";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param('i', $invoiceId);
            $stmt->execute();
            $stmt->close();
        }
    }

    /**
     * يمنع الكمية السالبة وسعر البيع صفر.
     *
     * @return list<string>
     */
    public static function validateInvoiceLines(int $proTybe, array $post, $conn = null, $store_id = null, $prevent_negative_stock = false, $editing_invoice_id = null): array
    {
        $errors = [];
        $names = $post['itmname'] ?? null;
        if (!is_array($names)) {
            return $errors;
        }

        $purchase = $proTybe === self::INVOICE_TYPES['PURCHASE'];
        $salePriceOnLine = in_array($proTybe, [
            self::INVOICE_TYPES['SALES'],
            self::INVOICE_TYPES['POS'],
            self::INVOICE_TYPES['SALES_RETURN'],
            self::INVOICE_TYPES['SALES_ORDER'],
            self::INVOICE_TYPES['OFFER'],
        ], true);

        $rowNo = 0;
        foreach ($names as $index => $itemId) {
            if ($itemId === '' || $itemId === null) {
                continue;
            }
            $rowNo++;
            $qty = (float) ($post['itmqty'][$index] ?? 0);
            if ($qty < 0) {
                $errors[] = 'سطر ' . $rowNo . ': الكمية سالبة';
            }

            if ($purchase) {
                if (!isset($post['itmsellprice'][$index])) {
                    continue;
                }
                $sale = (float) $post['itmsellprice'][$index];
            } elseif ($salePriceOnLine) {
                $sale = (float) ($post['itmprice'][$index] ?? 0);
            } else {
                continue;
            }

            if (abs($sale) < 0.0000001) {
                $errors[] = 'سطر ' . $rowNo . ': البيع صفر';
            }

            if ($prevent_negative_stock && $conn && $store_id && $salePriceOnLine) {
                $available = self::getRealStockQuantity($conn, $itemId, $store_id);
                if ($editing_invoice_id) {
                    $stmtOld = $conn->prepare("SELECT COALESCE(SUM(qty_out), 0) AS old_qty FROM fat_details WHERE item_id = ? AND det_store = ? AND fatid = ? AND isdeleted = 0");
                    if ($stmtOld) {
                        $stmtOld->bind_param("iii", $itemId, $store_id, $editing_invoice_id);
                        $stmtOld->execute();
                        $resOld = $stmtOld->get_result()->fetch_assoc();
                        $available += (float)($resOld['old_qty'] ?? 0);
                        $stmtOld->close();
                    }
                }
                
                if ($available < $qty) {
                    $errors[] = 'سطر ' . $rowNo . ': الكمية المباعة (' . $qty . ') أكبر من رصيد المخزن (' . $available . ')';
                }
            }
        }

        return $errors;
    }

    /**
     * بناء مصفوفة أسطر من $_POST القياسي للفواتير.
     */
    public static function linesFromPost(array $post): array
    {
        $lines = [];
        if (!isset($post['itmname'], $post['itmqty'], $post['itmprice'], $post['itmdisc'])) {
            return $lines;
        }
        foreach ($post['itmname'] as $index => $itmname) {
            if ($itmname === '' || $itmname === null) {
                continue;
            }
            $lines[] = [
                'item_id' => (int) $itmname,
                'qty' => (float) ($post['itmqty'][$index] ?? 1),
                'price' => (float) ($post['itmprice'][$index] ?? 0),
                'disc' => (float) ($post['itmdisc'][$index] ?? 0),
                'disc_pct' => (float) ($post['itmdisc_pct'][$index] ?? 0),
                'sell_price' => isset($post['itmsellprice'][$index]) ? (float) $post['itmsellprice'][$index] : 0,
                'u_val' => (float) ($post['u_val'][$index] ?? 1),
                'crtime' => $post['detcrtime'][$index] ?? null,
            ];
        }
        return $lines;
    }

    /**
     * جلب فاتورة نشطة أو رمي استثناء
     */
    public static function getActiveInvoice(mysqli $conn, int $id): array
    {
        $stmt = $conn->prepare('SELECT * FROM ot_head WHERE id = ? AND isdeleted = 0');
        if (!$stmt) {
            throw new Exception('فشل تحضير استعلام الفاتورة: ' . $conn->error);
        }
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $invoice = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if (!$invoice) {
            throw new RuntimeException('invoice_not_found');
        }
        return $invoice;
    }
}
