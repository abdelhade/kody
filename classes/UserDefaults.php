<?php
/**
 * افتراضيات المستخدم (users.def_*) في الفواتير والكاشير والسندات.
 * الافتراضي بيتختار أوتوماتيك في المستند الجديد، واللي معندوش defaults.change
 * مش بيقدر يغيّره: القوائم بتعرض الافتراضي بس، والسيرفر بيفرضه وقت الحفظ.
 * التعديل على مستند قديم مش بيتقفل (بيفضل على قيمه الأصلية).
 */
final class UserDefaults
{
    public const COLUMNS = [
        'store'      => 'def_store',
        'fund'       => 'def_fund',
        'client'     => 'def_client',
        'supplier'   => 'def_supplier',
        'emp'        => 'def_emp',
        'price_list' => 'def_price_list',
    ];

    public const LABELS = [
        'store'      => 'المخزن',
        'fund'       => 'الخزنة',
        'client'     => 'العميل',
        'supplier'   => 'المورد',
        'emp'        => 'الموظف / المندوب',
        'price_list' => 'قائمة الأسعار',
    ];

    public const CHANGE_PERMISSION = 'defaults.change';

    // أنواع الفواتير اللي طرفها مورد، وأنواع البيع اللي ليها قائمة أسعار
    private const SUPPLIER_TYPES = [4, 10, 12];
    private const PRICE_LIST_TYPES = [3, 9, 11, 13, 14];

    private static ?array $cache = null;

    /** @return array<string,int> افتراضيات المستخدم الحالي (0 = مفيش) */
    public static function all(mysqli $conn): array
    {
        if (self::$cache !== null) {
            return self::$cache;
        }
        self::$cache = array_fill_keys(array_keys(self::COLUMNS), 0);
        $uid = Can::userId();
        if ($uid < 1) {
            return self::$cache;
        }
        $stmt = $conn->prepare('SELECT ' . implode(', ', self::COLUMNS) . ' FROM users WHERE id = ? LIMIT 1');
        $stmt->bind_param('i', $uid);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc() ?: [];
        $stmt->close();
        foreach (self::COLUMNS as $key => $col) {
            self::$cache[$key] = (int) ($row[$col] ?? 0);
        }
        return self::$cache;
    }

    public static function get(mysqli $conn, string $key): int
    {
        return self::all($conn)[$key] ?? 0;
    }

    public static function isLocked(mysqli $conn, string $key): bool
    {
        return self::get($conn, $key) > 0 && !Can::check(self::CHANGE_PERMISSION);
    }

    /** true = الخيار ده يتشال من القائمة لأن المستخدم مقفول على افتراضي تاني */
    public static function hides(mysqli $conn, string $key, int|string|null $id): bool
    {
        return self::isLocked($conn, $key) && (int) $id !== self::get($conn, $key);
    }

    /** ' selected' لو ده افتراضي المستخدم */
    public static function selected(mysqli $conn, string $key, int|string|null $id): string
    {
        $def = self::get($conn, $key);
        return $def > 0 && (int) $id === $def ? ' selected' : '';
    }

    /**
     * لخيار في قائمة: null = يتشال (المستخدم مقفول على افتراضي تاني)، غير كده قيمة selected.
     * في المستند الجديد افتراضي المستخدم أولى من الافتراضي العام.
     */
    public static function pick(mysqli $conn, string $key, int|string|null $id, string $selected, bool $isNew = true): ?string
    {
        if (!$isNew) {
            return $selected;
        }
        if (self::hides($conn, $key, $id)) {
            return null;
        }
        return self::get($conn, $key) > 0 ? trim(self::selected($conn, $key, $id)) : $selected;
    }

    /** طرف الفاتورة حسب نوعها: مورد أو عميل */
    public static function partyKey(int $proType): string
    {
        return in_array($proType, self::SUPPLIER_TYPES, true) ? 'supplier' : 'client';
    }

    public static function hasPriceList(int $proType): bool
    {
        return in_array($proType, self::PRICE_LIST_TYPES, true);
    }

    /**
     * بيفرض الافتراضيات المقفولة على بيانات طلب جديد (الحقول الموجودة في الطلب بس).
     * @param array<string,string> $fields اسم الحقل => مفتاح الافتراضي
     */
    public static function enforce(mysqli $conn, array &$data, array $fields): void
    {
        foreach ($fields as $field => $key) {
            if (array_key_exists($field, $data) && self::isLocked($conn, $key)) {
                $data[$field] = (string) self::get($conn, $key);
            }
        }
    }

    /** @return array<int,string> الاختيارات المتاحة لكل افتراضي (شاشة المستخدم) */
    public static function options(mysqli $conn, string $key): array
    {
        if ($key === 'price_list') {
            require_once __DIR__ . '/InvoiceProcessor.php';
            return array_column(InvoiceProcessor::priceLists($conn), 'pname', 'id');
        }
        $where = match ($key) {
            'store'    => 'is_stock = 1',
            'fund'     => 'is_fund = 1',
            'client'   => "code LIKE '122%' AND is_basic = 0",
            'supplier' => "code LIKE '211%' AND is_basic = 0",
            'emp'      => "code LIKE '213%' AND is_basic = 0",
        };
        $res = $conn->query("SELECT id, aname FROM acc_head WHERE $where AND isdeleted = 0 ORDER BY aname");
        return array_column($res->fetch_all(MYSQLI_ASSOC), 'aname', 'id');
    }

    /** @return array<string,?int> عمود => قيمة من فورم المستخدم (فاضي = NULL) */
    public static function fromPost(array $post): array
    {
        $out = [];
        foreach (self::COLUMNS as $col) {
            $v = (int) ($post[$col] ?? 0);
            $out[$col] = $v > 0 ? $v : null;
        }
        return $out;
    }

    /** حفظ افتراضيات مستخدم من الفورم */
    public static function save(mysqli $conn, int $userId, array $post): void
    {
        $values = self::fromPost($post);
        $set = implode(', ', array_map(static fn ($c) => "$c = ?", array_keys($values)));
        $stmt = $conn->prepare("UPDATE users SET $set WHERE id = ?");
        $params = array_values($values);
        $params[] = $userId;
        $stmt->bind_param(str_repeat('i', count($params)), ...$params);
        $stmt->execute();
        $stmt->close();
    }

    /** قسم الافتراضيات في شاشة إضافة/تعديل مستخدم */
    public static function renderUserFields(mysqli $conn, array $user = []): void
    {
        $e = static fn ($v): string => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
        echo '<div class="row">';
        foreach (self::COLUMNS as $key => $col) {
            $current = (int) ($user[$col] ?? 0);
            echo '<div class="col-md-4"><div class="k-form-group">';
            echo '<label class="k-label" for="' . $col . '">' . self::LABELS[$key] . '</label>';
            echo '<select name="' . $col . '" id="' . $col . '" class="k-select form-control">';
            echo '<option value="">— بدون —</option>';
            foreach (self::options($conn, $key) as $id => $name) {
                echo '<option value="' . (int) $id . '"' . ((int) $id === $current ? ' selected' : '') . '>' . $e($name) . '</option>';
            }
            echo '</select></div></div>';
        }
        echo '</div>';
        echo '<small class="text-muted d-block"><i class="fas fa-info-circle ml-1"></i> بتتختار أوتوماتيك في الفواتير والكاشير والسندات الجديدة. اللي معندوش صلاحية "تغيير الافتراضيات" مش هيقدر يغيّرها.</small>';
    }

    /** فرض افتراضيات فاتورة/كاشير جديدة (store_id, fund_id, emp_id, acc2_id, price_list) */
    public static function enforceInvoice(mysqli $conn, array &$data, int $proType): void
    {
        $fields = [
            'store_id' => 'store',
            'fund_id'  => 'fund',
            'payment_fund_id' => 'fund',
            'emp_id'   => 'emp',
        ];
        // التحويل المخزني: acc2_id = مخزن هدف، مش عميل/مورد
        if ($proType !== 18) {
            $fields['acc2_id'] = self::partyKey($proType);
        }
        if (self::hasPriceList($proType)) {
            $fields['price_list'] = 'price_list';
        }
        // الدفع بالبنك في كاشير الملابس بيبعت البنك في fund_id
        if (($data['payment_method'] ?? '') === 'bank') {
            unset($fields['fund_id']);
        }
        self::enforce($conn, $data, $fields);
    }
}
