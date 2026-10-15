<?php

require_once __DIR__ . '/InvoiceProcessor.php';

/**
 * تقفيل شيفت الكاشير.
 *
 * الشيفت المفتوح = حركات المستخدم في ot_head اللي shift_id = 0.
 * عند التقفيل بيتعمل سجل في closed_orders والحركات دي تاخد رقمه،
 * والمتوقع في كل خزنة/بنك بيتحسب من السندات (قبض/دفع/تحويل) مش من الفورم.
 */
class ShiftCloser
{
    public const VOUCHER_RECEIPT = 1;
    public const VOUCHER_PAYMENT = 2;
    public const VOUCHER_TRANSFER = 16;

    private mysqli $conn;

    /** @var array<int, array{id:int, name:string, is_fund:bool}>|null */
    private ?array $moneyAccounts = null;

    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }

    /** أنواع الحركات اللي بتدخل في الشيفت */
    public static function shiftTypes(): array
    {
        $t = InvoiceProcessor::INVOICE_TYPES;
        return [
            $t['SALES'], $t['POS'], $t['SALES_RETURN'], $t['PURCHASE'], $t['PURCHASE_RETURN'],
            self::VOUCHER_RECEIPT, self::VOUCHER_PAYMENT, self::VOUCHER_TRANSFER,
        ];
    }

    /**
     * الكاشير يشوف المتوقع قبل الجرد؟ (لو الجرد الأعمى مقفول أو عنده صلاحية)
     */
    public static function canSeeExpected(?array $settings): bool
    {
        if (empty($settings['pos_blind_close'])) {
            return true;
        }
        return Can::check('pos.view_shift_expected');
    }

    // ─── Preview ────────────────────────────────────────────────

    /**
     * ملخص الشيفت المفتوح للمستخدم.
     */
    public function preview(int $userId): array
    {
        $summary = $this->summarize('oh.`user` = ? AND oh.shift_id = 0', 'i', [$userId]);
        $startCash = $this->previousLeftInDrawer($userId);
        $primary = $this->primaryFund($userId, $summary['movements']);
        $summary['start_cash'] = $startCash;
        $summary['primary_fund'] = $primary;
        $summary['accounts'] = $this->buildAccountRows($summary['movements'], $primary, $startCash);
        $summary['cashier_name'] = $this->username($userId);
        return $summary;
    }

    // ─── Close ──────────────────────────────────────────────────

    /**
     * @param array<int|string, mixed> $counted account_id => المبلغ المعدود
     * @return array{shift_id:int, total_diff:float}
     */
    public function close(int $userId, array $counted, float $leftInDrawer, string $notes, string $businessDate): array
    {
        if ($userId <= 0) {
            throw new RuntimeException('الرجاء تسجيل الدخول أولاً');
        }

        $money = $this->moneyAccounts();
        $countedClean = [];
        foreach ($counted as $accId => $amount) {
            $accId = (int) $accId;
            $amount = round((float) $amount, 2);
            if ($accId <= 0 || !isset($money[$accId])) {
                continue;
            }
            if ($amount < 0) {
                throw new RuntimeException('المبلغ المعدود لا يمكن أن يكون بالسالب');
            }
            $countedClean[$accId] = $amount;
        }
        $leftInDrawer = round(max(0.0, $leftInDrawer), 2);
        $notes = mb_substr(trim($notes), 0, 255);

        $lockName = 'kody_shift_close_' . $userId;
        $lockRes = $this->conn->query("SELECT GET_LOCK('" . $this->conn->real_escape_string($lockName) . "', 5) AS l");
        $locked = $lockRes && (int) ($lockRes->fetch_assoc()['l'] ?? 0) === 1;
        if (!$locked) {
            throw new RuntimeException('يوجد تقفيل آخر جاري لنفس المستخدم، حاول مرة أخرى');
        }

        $this->conn->begin_transaction();
        try {
            $startCash = $this->previousLeftInDrawer($userId);
            $username = $this->username($userId);
            $now = date('Y-m-d H:i:s');
            $endTime = date('H:i:s');

            $stmt = $this->conn->prepare(
                'INSERT INTO closed_orders (shift, `user`, user_id, `date`, endtime, closed_at, fund_before, start_cash, info, status)
                 VALUES (\'\', ?, ?, ?, ?, ?, ?, ?, ?, 1)'
            );
            $stmt->bind_param('sisssdds', $username, $userId, $businessDate, $endTime, $now, $startCash, $startCash, $notes);
            $stmt->execute();
            $shiftId = (int) $this->conn->insert_id;
            $stmt->close();

            $types = self::shiftTypes();
            $in = implode(',', array_fill(0, count($types), '?'));
            $stmt = $this->conn->prepare(
                "UPDATE ot_head SET shift_id = ?, mdtime = mdtime
                 WHERE `user` = ? AND shift_id = 0 AND isdeleted = 0 AND pro_tybe IN ($in)"
            );
            $params = array_merge([$shiftId, $userId], $types);
            $stmt->bind_param(str_repeat('i', count($params)), ...$params);
            $stmt->execute();
            $claimed = $stmt->affected_rows;
            $stmt->close();

            if ($claimed <= 0 && $startCash == 0.0 && array_sum($countedClean) == 0.0) {
                throw new RuntimeException('لا توجد حركات في الشيفت الحالي لتقفيلها');
            }

            $summary = $this->summarize('oh.shift_id = ?', 'i', [$shiftId]);
            $primary = $this->primaryFund($userId, $summary['movements']);
            $rows = $this->buildAccountRows($summary['movements'], $primary, $startCash, array_keys($countedClean));

            if ($primary > 0 && $leftInDrawer > ($countedClean[$primary] ?? 0.0) + 0.001) {
                throw new RuntimeException('المتبقي في الدرج أكبر من النقدية المعدودة');
            }

            $expectedTotal = 0.0;
            $countedTotal = 0.0;
            $expFunds = $expBanks = $cntFunds = $cntBanks = 0.0;
            $stmt = $this->conn->prepare(
                'INSERT INTO shift_payments (shift_id, account_id, start_amount, movement, expected, counted, diff)
                 VALUES (?, ?, ?, ?, ?, ?, ?)'
            );
            foreach ($rows as $row) {
                $accId = $row['account_id'];
                $cnt = $countedClean[$accId] ?? 0.0;
                $diff = round($cnt - $row['expected'], 2);
                $stmt->bind_param('iiddddd', $shiftId, $accId, $row['start_amount'], $row['movement'], $row['expected'], $cnt, $diff);
                $stmt->execute();
                $expectedTotal += $row['expected'];
                $countedTotal += $cnt;
                if ($row['is_fund']) {
                    $expFunds += $row['expected'];
                    $cntFunds += $cnt;
                } else {
                    $expBanks += $row['expected'];
                    $cntBanks += $cnt;
                }
            }
            $stmt->close();

            $deficit = round($countedTotal - $expectedTotal, 2);
            $handedOver = $primary > 0 ? round(($countedClean[$primary] ?? 0.0) - $leftInDrawer, 2) : 0.0;
            $shiftNo = (string) $shiftId;
            $openedAt = $summary['opened_at'] ?? $now;

            $stmt = $this->conn->prepare(
                'UPDATE closed_orders SET
                    shift = ?, strttime = ?, opened_at = ?,
                    orders_count = ?, total_sales = ?, total_discount = ?, total_returns = ?,
                    expenses = ?, total_receipts = ?,
                    total_cash = ?, total_visa = ?, actual_cash = ?, actual_visa = ?,
                    expected_total = ?, counted_total = ?, deficit = ?,
                    cash = ?, fund_after = ?
                 WHERE id = ?'
            );
            $stmt->bind_param(
                'sssiddddddddddddddi',
                $shiftNo, $openedAt, $openedAt,
                $summary['orders_count'], $summary['sales_net'], $summary['sales_discount'], $summary['returns_total'],
                $summary['expenses'], $summary['receipts'],
                $expFunds, $expBanks, $cntFunds, $cntBanks,
                $expectedTotal, $countedTotal, $deficit,
                $handedOver, $leftInDrawer,
                $shiftId
            );
            $stmt->execute();
            $stmt->close();

            $this->conn->commit();
        } catch (Throwable $e) {
            $this->conn->rollback();
            $this->conn->query("SELECT RELEASE_LOCK('" . $this->conn->real_escape_string($lockName) . "')");
            throw $e;
        }
        $this->conn->query("SELECT RELEASE_LOCK('" . $this->conn->real_escape_string($lockName) . "')");

        return ['shift_id' => $shiftId, 'total_diff' => $deficit];
    }

    // ─── Closed shift ───────────────────────────────────────────

    /**
     * بيانات شيفت مقفول + مقارنة بالأرقام الحالية (لو فواتير اتعدلت بعد التقفيل).
     */
    public function getShift(int $shiftId): ?array
    {
        $stmt = $this->conn->prepare('SELECT * FROM closed_orders WHERE id = ?');
        $stmt->bind_param('i', $shiftId);
        $stmt->execute();
        $shift = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if (!$shift) {
            return null;
        }

        $payments = [];
        $stmt = $this->conn->prepare(
            'SELECT sp.*, ah.aname FROM shift_payments sp
             LEFT JOIN acc_head ah ON ah.id = sp.account_id
             WHERE sp.shift_id = ? ORDER BY sp.id'
        );
        $stmt->bind_param('i', $shiftId);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            $payments[] = $row;
        }
        $stmt->close();

        $changes = [];
        $live = null;
        if ((int) $shift['user_id'] > 0) {
            $live = $this->summarize('oh.shift_id = ?', 'i', [$shiftId]);
            if (abs($live['sales_net'] - (float) $shift['total_sales']) >= 0.01) {
                $changes[] = 'صافي المبيعات أصبح ' . number_format($live['sales_net'], 2);
            }
            if (abs($live['returns_total'] - (float) $shift['total_returns']) >= 0.01) {
                $changes[] = 'المرتجعات أصبحت ' . number_format($live['returns_total'], 2);
            }
            $seen = [];
            foreach ($payments as $p) {
                $accId = (int) $p['account_id'];
                $seen[$accId] = true;
                $now = $live['movements'][$accId] ?? 0.0;
                if (abs($now - (float) $p['movement']) >= 0.01) {
                    $changes[] = 'حركة ' . ($p['aname'] ?? $accId) . ' أصبحت ' . number_format($now, 2);
                }
            }
            foreach ($live['movements'] as $accId => $mv) {
                if (!isset($seen[$accId]) && abs($mv) >= 0.01) {
                    $changes[] = 'حركة جديدة على ' . ($this->moneyAccounts()[$accId]['name'] ?? $accId) . ': ' . number_format($mv, 2);
                }
            }
        }

        return ['shift' => $shift, 'payments' => $payments, 'changes' => $changes, 'summary' => $live];
    }

    // ─── Internals ──────────────────────────────────────────────

    /**
     * @return array{orders_count:int, sales_gross:float, sales_discount:float, sales_net:float,
     *   cash_sales:float, credit_sales:float,
     *   returns_count:int, returns_total:float, net_sales:float, expenses:float, receipts:float,
     *   movements:array<int,float>, opened_at:?string}
     */
    private function summarize(string $cond, string $types, array $params): array
    {
        $t = InvoiceProcessor::INVOICE_TYPES;
        $out = [
            'orders_count' => 0, 'sales_gross' => 0.0, 'sales_discount' => 0.0, 'sales_net' => 0.0,
            'cash_sales' => 0.0, 'credit_sales' => 0.0,
            'returns_count' => 0, 'returns_total' => 0.0, 'net_sales' => 0.0,
            'expenses' => 0.0, 'receipts' => 0.0, 'movements' => [], 'opened_at' => null,
        ];

        $stmt = $this->conn->prepare(
            "SELECT oh.pro_tybe, COUNT(*) AS cnt,
                    COALESCE(SUM(oh.fat_total), 0) AS gross,
                    COALESCE(SUM(oh.fat_disc), 0) AS disc,
                    COALESCE(SUM(oh.fat_net), 0) AS net,
                    COALESCE(SUM(LEAST(GREATEST(COALESCE(oh.paid_amount, 0), 0), GREATEST(COALESCE(oh.fat_net, 0), 0))), 0) AS collected
             FROM ot_head oh
             WHERE $cond AND oh.isdeleted = 0 AND oh.pro_tybe IN (?, ?, ?)
             GROUP BY oh.pro_tybe"
        );
        $p = array_merge($params, [$t['SALES'], $t['POS'], $t['SALES_RETURN']]);
        $stmt->bind_param($types . 'iii', ...$p);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            if ((int) $row['pro_tybe'] === $t['SALES_RETURN']) {
                $out['returns_count'] += (int) $row['cnt'];
                $out['returns_total'] += (float) $row['net'];
            } else {
                $out['orders_count'] += (int) $row['cnt'];
                $out['sales_gross'] += (float) $row['gross'];
                $out['sales_discount'] += (float) $row['disc'];
                $out['sales_net'] += (float) $row['net'];
                $collected = (float) $row['collected'];
                $out['cash_sales'] += $collected;
                $out['credit_sales'] += (float) $row['net'] - $collected;
            }
        }
        $stmt->close();

        $money = $this->moneyAccounts();
        $stmt = $this->conn->prepare(
            "SELECT oh.pro_tybe, oh.acc1, oh.acc2, oh.op2, COALESCE(oh.pro_value, 0) AS val
             FROM ot_head oh
             WHERE $cond AND oh.isdeleted = 0 AND oh.pro_tybe IN (?, ?, ?)"
        );
        $p = array_merge($params, [self::VOUCHER_RECEIPT, self::VOUCHER_PAYMENT, self::VOUCHER_TRANSFER]);
        $stmt->bind_param($types . 'iii', ...$p);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            $val = (float) $row['val'];
            $debit = (int) $row['acc1'];
            $credit = (int) $row['acc2'];
            if (isset($money[$debit])) {
                $out['movements'][$debit] = ($out['movements'][$debit] ?? 0.0) + $val;
            }
            if (isset($money[$credit])) {
                $out['movements'][$credit] = ($out['movements'][$credit] ?? 0.0) - $val;
            }
            if ((int) $row['op2'] === 0) {
                if ((int) $row['pro_tybe'] === self::VOUCHER_PAYMENT) {
                    $out['expenses'] += $val;
                } elseif ((int) $row['pro_tybe'] === self::VOUCHER_RECEIPT) {
                    $out['receipts'] += $val;
                }
            }
        }
        $stmt->close();

        $stmt = $this->conn->prepare("SELECT MIN(oh.crtime) AS opened FROM ot_head oh WHERE $cond AND oh.isdeleted = 0");
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $out['opened_at'] = $stmt->get_result()->fetch_assoc()['opened'] ?? null;
        $stmt->close();

        foreach ($out['movements'] as $k => $v) {
            $out['movements'][$k] = round($v, 2);
        }
        foreach (['sales_gross', 'sales_discount', 'sales_net', 'cash_sales', 'credit_sales', 'returns_total', 'expenses', 'receipts'] as $k) {
            $out[$k] = round($out[$k], 2);
        }
        $out['net_sales'] = round($out['sales_net'] - $out['returns_total'], 2);
        return $out;
    }

    /**
     * @param array<int,float> $movements
     * @param list<int> $extraAccounts
     * @return list<array{account_id:int, name:string, is_fund:bool, start_amount:float, movement:float, expected:float}>
     */
    private function buildAccountRows(array $movements, int $primary, float $startCash, array $extraAccounts = []): array
    {
        $money = $this->moneyAccounts();
        $ids = [];
        foreach ($money as $id => $acc) {
            if ($acc['is_fund']) {
                $ids[] = (int) $id;
            }
        }
        foreach ($movements as $id => $v) {
            if (abs($v) >= 0.005) {
                $ids[] = (int) $id;
            }
        }
        if ($primary > 0) {
            array_unshift($ids, $primary);
        }
        $ids = array_values(array_unique(array_merge($ids, $extraAccounts)));

        $rows = [];
        foreach ($ids as $accId) {
            if (!isset($money[$accId])) {
                continue;
            }
            $start = $accId === $primary ? $startCash : 0.0;
            $mv = $movements[$accId] ?? 0.0;
            $rows[] = [
                'account_id' => $accId,
                'name' => $money[$accId]['name'],
                'is_fund' => $money[$accId]['is_fund'],
                'start_amount' => round($start, 2),
                'movement' => round($mv, 2),
                'expected' => round($start + $mv, 2),
            ];
        }
        return $rows;
    }

    /** المتبقي في الدرج من آخر شيفت مقفول للمستخدم */
    private function previousLeftInDrawer(int $userId): float
    {
        $stmt = $this->conn->prepare('SELECT fund_after FROM closed_orders WHERE user_id = ? ORDER BY id DESC LIMIT 1');
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row ? round((float) $row['fund_after'], 2) : 0.0;
    }

    /**
     * خزنة الكاشير الأساسية: خزنة المستخدم الافتراضية ← خزنة POS من الإعدادات ← أكبر خزنة اتحركت.
     *
     * @param array<int,float> $movements
     */
    private function primaryFund(int $userId, array $movements): int
    {
        $money = $this->moneyAccounts();
        $candidates = [];

        $stmt = $this->conn->prepare('SELECT def_fund FROM users WHERE id = ?');
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        $candidates[] = (int) ($row['def_fund'] ?? 0);

        $res = $this->conn->query('SELECT def_pos_fund FROM settings LIMIT 1');
        $candidates[] = (int) (($res ? $res->fetch_assoc() : [])['def_pos_fund'] ?? 0);

        foreach ($candidates as $id) {
            if ($id > 0 && isset($money[$id]) && $money[$id]['is_fund']) {
                return $id;
            }
        }

        $best = 0;
        $bestVal = 0.0;
        foreach ($movements as $id => $mv) {
            if (isset($money[$id]) && $money[$id]['is_fund'] && abs($mv) > $bestVal) {
                $best = (int) $id;
                $bestVal = abs($mv);
            }
        }
        return $best;
    }

    /** @return array<int, array{id:int, name:string, is_fund:bool}> */
    private function moneyAccounts(): array
    {
        if ($this->moneyAccounts !== null) {
            return $this->moneyAccounts;
        }
        $this->moneyAccounts = [];
        $res = $this->conn->query(
            "SELECT id, aname, is_fund FROM acc_head
             WHERE isdeleted = 0 AND is_basic = 0 AND (is_fund = 1 OR code LIKE '124%')"
        );
        while ($res && ($row = $res->fetch_assoc())) {
            $id = (int) $row['id'];
            $this->moneyAccounts[$id] = [
                'id' => $id,
                'name' => (string) $row['aname'],
                'is_fund' => (int) $row['is_fund'] === 1,
            ];
        }
        return $this->moneyAccounts;
    }

    private function username(int $userId): string
    {
        $stmt = $this->conn->prepare('SELECT uname FROM users WHERE id = ?');
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return (string) ($row['uname'] ?? '');
    }
}
