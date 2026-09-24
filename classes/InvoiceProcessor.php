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

        if ($calc['actual_cash'] > 0 && $paymentFundId > 0) {
            $created['cash'] = self::createPaymentVoucher($conn, [
                'paid_type' => $paidType,
                'amount' => $calc['actual_cash'],
                'debit_account' => $paymentFundId,
                'credit_account' => $partyAccId,
                'invoice_id' => $invoiceId,
                'pro_date' => $proDate,
                'emp_id' => $empId,
                'user_id' => $userId,
                'info' => $info . ' - دفع كاش',
                'details' => $paidNote . ' كاش _ ' . $display,
            ]);
        }

        if ($calc['actual_bank'] > 0 && $paymentBankId > 0) {
            $created['bank'] = self::createPaymentVoucher($conn, [
                'paid_type' => $paidType,
                'amount' => $calc['actual_bank'],
                'debit_account' => $paymentBankId,
                'credit_account' => $partyAccId,
                'invoice_id' => $invoiceId,
                'pro_date' => $proDate,
                'emp_id' => $empId,
                'user_id' => $userId,
                'info' => $info . ' - دفع صرافة',
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
        $paidType = ($proTybe == self::INVOICE_TYPES['SALES'] || $proTybe == self::INVOICE_TYPES['POS'])
            ? self::ACCOUNTING_TYPES['RECEIPT']
            : (int) $config['paid_type'];

        $stmt = $conn->prepare('SELECT * FROM ot_head WHERE op2 = ? AND pro_tybe IN (1, 2) AND isdeleted = 0 LIMIT 1');
        $stmt->bind_param('i', $invoiceId);
        $stmt->execute();
        $rowPaid = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($paid > 0 && $rowPaid === null) {
            self::createPaymentVoucher($conn, [
                'paid_type' => $paidType,
                'amount' => $paid,
                'debit_account' => (int) $accounts['acc5'],
                'credit_account' => (int) $accounts['acc6'],
                'invoice_id' => $invoiceId,
                'pro_date' => $proDate,
                'emp_id' => $empId,
                'user_id' => $userId,
                'info' => $info,
                'details' => ($config['paid_note'] ?? 'سند') . ' _ ' . $invoiceId,
            ]);
            return;
        }

        if ($paid > 0 && $rowPaid !== null) {
            $stmt = $conn->prepare(
                'UPDATE ot_head SET info = ?, pro_date = ?, emp_id = ?, acc1 = ?, acc2 = ?, pro_value = ?, crtime = crtime
                 WHERE op2 = ? AND pro_tybe = ? AND isdeleted = 0'
            );
            $acc5 = (int) $accounts['acc5'];
            $acc6 = (int) $accounts['acc6'];
            $stmt->bind_param('ssiiiiii', $info, $proDate, $empId, $acc5, $acc6, $paid, $invoiceId, $paidType);
            $stmt->execute();
            $stmt->close();

            $stmt = $conn->prepare(
                'UPDATE journal_heads SET total = ?, jdate = ? WHERE op2 = ? AND isdeleted = 0'
            );
            $stmt->bind_param('dsi', $paid, $proDate, $invoiceId);
            $stmt->execute();
            $stmt->close();

            $stmt = $conn->prepare('SELECT id FROM journal_heads WHERE op2 = ? AND isdeleted = 0 LIMIT 1');
            $stmt->bind_param('i', $invoiceId);
            $stmt->execute();
            $row = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            if ($row) {
                $jr = (int) $row['id'];
                $stmt = $conn->prepare(
                    'UPDATE journal_entries SET account_id = ?, debit = ?, credit = 0 WHERE journal_id = ? AND tybe = 0'
                );
                $stmt->bind_param('idi', $acc5, $paid, $jr);
                $stmt->execute();
                $stmt->close();
                $stmt = $conn->prepare(
                    'UPDATE journal_entries SET account_id = ?, debit = 0, credit = ? WHERE journal_id = ? AND tybe = 1'
                );
                $stmt->bind_param('idi', $acc6, $paid, $jr);
                $stmt->execute();
                $stmt->close();
            }
            return;
        }

        if ($paid == 0 && $rowPaid !== null) {
            self::softDeleteLinkedPayments($conn, $invoiceId, $paidType);
            self::softDeleteJournalsByOp2($conn, $invoiceId);
        }
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
                ?, ?, 1, 1, ?, ?, ?, ?, 1, ?, 1, ?, ?, ?, ?, ?, ?, 0, 1, 0,
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
            'sssssssssssssssssssssss',
            $proId,
            $proTybe,
            $proTybe,
            $info,
            $proDate,
            $accural,
            $serial,
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
                info = ?, pro_date = ?, accural_date = ?, pro_serial = ?, store_id = ?,
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
            'ssssiiiidddddddii',
            $info,
            $proDate,
            $accural,
            $serial,
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
                pro_serial = ?, store_id = ?, emp_id = ?, emp2_id = ?,
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
            'sssssssssssssssssssss',
            $proTybe,
            $info,
            $accural,
            $serial,
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
