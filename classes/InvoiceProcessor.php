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
            self::softDeleteDetails($conn, $id);
            self::softDeleteHeader($conn, $id);
            self::softDeleteJournalsByOpId($conn, $id);
            self::softDeleteLinkedPayments($conn, $id);
            self::softDeleteJournalsByOp2($conn, $id);

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
     * مسار الحذف النهائي للمستخدم يبقى softDelete().
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
