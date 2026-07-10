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
}
