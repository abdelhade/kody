<?php
/**
 * RolesSeeder - أدوار جاهزة (محاسب، مدير مالي، موارد بشرية، كاشير ...) بصلاحيات مدروسة.
 *
 * صيغة الصلاحيات في كل preset:
 *   'sales.add'        صلاحية بالاسم
 *   'clients:vae'      أفعال CRUD للكيان: v=view a=add e=edit d=delete
 *   '@reports'         كل صلاحيات المجموعة (group_key)
 *   '*.view'           كل صلاحيات العرض
 * أي اسم مش موجود في جدول permissions بيرمي استثناء.
 */

require_once __DIR__ . '/../classes/Roles.php';

class RolesSeeder
{
    private const ACTION_LETTERS = ['v' => 'view', 'a' => 'add', 'e' => 'edit', 'd' => 'delete'];

    public static function getPresets(): array
    {
        $basicFinance = ['clients', 'suppliers', 'funds', 'banks', 'expenses', 'revenues', 'creditors', 'debtors'];

        return [
            'accountant' => [
                'key'         => 'accountant',
                'rollname'    => 'محاسب',
                'info'        => 'مسؤول الحسابات العامة، قيود اليومية، سندات القبض والصرف، كشوفات الحسابات والتقارير المالية وميزان المراجعة.',
                'icon'        => 'fa-calculator',
                'category'    => 'مالية ومحاسبة',
                'color'       => '#2563eb',
                'permissions' => array_merge(
                    array_map(static fn ($e) => $e . ':vae', $basicFinance),
                    [
                        'journals:vae', 'receipts:vae', 'payments:vae',
                        'accounts.chart', 'accounts.opening_balance',
                        'reports.accounting', 'reports.sales', 'reports.purchases',
                        'dashboard.cards', 'dashboard.shortcuts', 'dashboard.tables',
                    ]
                ),
            ],

            'cfo' => [
                'key'         => 'cfo',
                'rollname'    => 'مدير مالي',
                'info'        => 'الإشراف المالي الكامل، الحسابات الختامية، الميزانية وقائمة الدخل، الأصول الثابتة، الرواتب والتقارير الاستراتيجية.',
                'icon'        => 'fa-chart-line',
                'category'    => 'إدارة مالية عليا',
                'color'       => '#7c3aed',
                'permissions' => array_merge(
                    array_map(static fn ($e) => $e . ':vaed', $basicFinance),
                    [
                        'partners:vaed', 'assets:vaed', 'assets.operations',
                        'journals:vaed', 'receipts:vaed', 'payments:vaed',
                        'accounts.chart', 'accounts.opening_balance',
                        '@reports', 'payroll:v', 'tasks:v', 'tasks.view_all',
                        'dashboard.cards', 'dashboard.shortcuts', 'dashboard.tables',
                    ]
                ),
            ],

            'hr_manager' => [
                'key'         => 'hr_manager',
                'rollname'    => 'مسؤول موارد بشرية',
                'info'        => 'إدارة شؤون الموظفين، عقود العمل، الحضور والانصراف، الورديات، مسيرات الرواتب والأجور والتقارير الإدارية.',
                'icon'        => 'fa-user-tie',
                'category'    => 'الموارد البشرية HR',
                'color'       => '#059669',
                'permissions' => [
                    '@hr', '@tasks', '@payroll',
                    'reports.hr', 'reports.payroll',
                    'dashboard.hr', 'dashboard.cards',
                ],
            ],

            'storekeeper' => [
                'key'         => 'storekeeper',
                'rollname'    => 'أمين مستودع',
                'info'        => 'إدارة المخزون، الأصناف وفئات المنتجات، أذونات التوريد والإضافة، فواتير المشتريات ومردوداتها، وجرد المستودع.',
                'icon'        => 'fa-boxes',
                'category'    => 'المخازن والمستودعات',
                'color'       => '#d97706',
                'permissions' => [
                    'stores:vae', 'items:vae', 'item_groups:vae', 'units:vae',
                    'items.pricing', 'items.opening_balance', 'inventory.audit',
                    'purchases:vae', 'purchase_returns:vae', 'purchase_orders:vae',
                    'suppliers:v', 'reports.purchases', 'dashboard.tables',
                ],
            ],

            'cashier' => [
                'key'         => 'cashier',
                'rollname'    => 'كاشير مبيعات',
                'info'        => 'إجراء عمليات البيع وإصدار الفواتير عبر شاشة الكاشير وPOS، تسجيل مرتجع المبيعات، وسندات القبض، وخدمة التوصيل.',
                'icon'        => 'fa-cash-register',
                'category'    => 'نقاط البيع والكاشير',
                'color'       => '#0891b2',
                'permissions' => [
                    'pos.use', 'pos.close_shift', 'pos.delivery',
                    'sales:va', 'sales_returns:va', 'receipts:va', 'delivery:va', 'clients:v',
                    'dashboard.cards',
                ],
            ],

            'sales_manager' => [
                'key'         => 'sales_manager',
                'rollname'    => 'مدير مبيعات',
                'info'        => 'الإشراف على حركة المبيعات، عروض الأسعار، إدارة العملاء، إدارة علاقات العملاء CRM، ومتابعة تقارير المبيعات.',
                'icon'        => 'fa-funnel-dollar',
                'category'    => 'المبيعات و CRM',
                'color'       => '#ea580c',
                'permissions' => [
                    'sales:vae', 'sales_returns:vae', 'sales_orders:vae', 'quotations:vae',
                    'clients:vae', 'crm_clients:vae', 'client_profile:vae', 'chances:vae', 'calls:vae',
                    'delivery:vae', 'pos.delivery', 'pos.closed_sessions',
                    'reports.sales', 'reports.shift', 'dashboard.cards', 'dashboard.tables',
                ],
            ],

            'purchasing_manager' => [
                'key'         => 'purchasing_manager',
                'rollname'    => 'مدير مشتريات',
                'info'        => 'إدارة الموردين، طلبات وفواتير الشراء، مرتجعات المشتريات، متابعة أسعار وتكاليف الأصناف وسندات الصرف.',
                'icon'        => 'fa-truck-loading',
                'category'    => 'المشتريات والموردين',
                'color'       => '#4f46e5',
                'permissions' => [
                    'purchases:vae', 'purchase_returns:vae', 'purchase_orders:vae',
                    'suppliers:vae', 'items:v', 'stores:v', 'payments:vae',
                    'reports.purchases', 'dashboard.tables',
                ],
            ],

            'receptionist' => [
                'key'         => 'receptionist',
                'rollname'    => 'موظف استقبال',
                'info'        => 'تسجيل الحجوزات والمواعيد، استقبال العملاء والزوار، متابعة سجل الاتصالات والزيارات وخدمة العملاء.',
                'icon'        => 'fa-concierge-bell',
                'category'    => 'الاستقبال والعملاء',
                'color'       => '#db2777',
                'permissions' => [
                    'reservations:vae', 'reservations.view_total', 'reservations.view_ended',
                    'patients:v', 'clients:vae', 'client_profile:v', 'calls:va', 'visits:va',
                    'dashboard.cards',
                ],
            ],

            'auditor' => [
                'key'         => 'auditor',
                'rollname'    => 'مراجع داخلي',
                'info'        => 'صلاحية تدقيق ومراجعة شاملة لجميع السجلات والقيود والمخازن والتقارير المالية (عرض وقراءة فقط بدون أي إضافة أو تعديل أو حذف).',
                'icon'        => 'fa-search-dollar',
                'category'    => 'التدقيق والرقابة (عرض فقط)',
                'color'       => '#475569',
                'permissions' => [
                    '*.view', '@reports', 'accounts.chart',
                    'dashboard.cards', 'dashboard.tables',
                ],
            ],
        ];
    }

    /**
     * فك صيغة الـ preset لأسماء صلاحيات.
     *
     * @param list<string> $specs
     * @return list<string>
     */
    public static function expand(mysqli $conn, array $specs): array
    {
        $all = Roles::allPermissions($conn);
        $names = [];
        foreach ($specs as $spec) {
            if ($spec === '*.view') {
                foreach ($all as $p) {
                    if ($p['action'] === 'view') {
                        $names[] = $p['name'];
                    }
                }
            } elseif (str_starts_with($spec, '@')) {
                $group = substr($spec, 1);
                $found = false;
                foreach ($all as $p) {
                    if ($p['group_key'] === $group) {
                        $names[] = $p['name'];
                        $found = true;
                    }
                }
                if (!$found) {
                    throw new InvalidArgumentException('مجموعة صلاحيات غير معروفة: ' . $group);
                }
            } elseif (str_contains($spec, ':')) {
                [$entity, $letters] = explode(':', $spec, 2);
                foreach (str_split($letters) as $l) {
                    if (!isset(self::ACTION_LETTERS[$l])) {
                        throw new InvalidArgumentException('فعل غير معروف في ' . $spec);
                    }
                    $names[] = $entity . '.' . self::ACTION_LETTERS[$l];
                }
            } else {
                $names[] = $spec;
            }
        }
        return array_values(array_unique($names));
    }

    /** @return array<string, int> اسم الدور => id */
    public static function getExistingRoles(mysqli $conn): array
    {
        $existing = [];
        foreach (Roles::options($conn, false) as $r) {
            $existing[trim($r['name'])] = $r['id'];
        }
        return $existing;
    }

    public static function seedSingle(mysqli $conn, string $presetKey, bool $overwriteIfExists = false): array
    {
        $presets = self::getPresets();
        if (!isset($presets[$presetKey])) {
            return ['success' => false, 'status' => 'error', 'message' => "الدور المحدد غير معروف ($presetKey)"];
        }

        $preset   = $presets[$presetKey];
        $roleName = $preset['rollname'];
        $existing = self::getExistingRoles($conn);

        if (isset($existing[$roleName]) && !$overwriteIfExists) {
            return [
                'success' => true, 'status' => 'skipped', 'id' => $existing[$roleName], 'name' => $roleName,
                'message' => "الدور \"$roleName\" موجود مسبقاً (تم تخطيه)",
            ];
        }

        try {
            $ids = Roles::idsFor($conn, self::expand($conn, $preset['permissions']));

            $conn->begin_transaction();
            if (isset($existing[$roleName])) {
                $roleId = $existing[$roleName];
                Roles::update($conn, $roleId, $roleName, $preset['info'], true);
                $status = 'updated';
            } else {
                $roleId = Roles::create($conn, $roleName, $preset['info']);
                $status = 'created';
            }
            Roles::syncPermissions($conn, $roleId, $ids);
            $conn->commit();
        } catch (Throwable $e) {
            try {
                $conn->rollback();
            } catch (Throwable $ignored) {
            }
            return ['success' => false, 'status' => 'error', 'name' => $roleName, 'message' => "فشل حفظ الدور \"$roleName\": " . $e->getMessage()];
        }

        return [
            'success' => true, 'status' => $status, 'id' => $roleId, 'name' => $roleName,
            'message' => $status === 'created' ? "تم إنشاء الدور \"$roleName\" بنجاح" : "تم تحديث صلاحيات الدور \"$roleName\" بنجاح",
        ];
    }

    public static function seedMany(mysqli $conn, array $keys = [], bool $overwriteIfExists = false): array
    {
        $presets = self::getPresets();
        if (empty($keys)) {
            $keys = array_keys($presets);
        }

        $results = ['created' => [], 'updated' => [], 'skipped' => [], 'errors' => [], 'details' => []];

        foreach ($keys as $key) {
            if (!isset($presets[$key])) {
                continue;
            }
            $res = self::seedSingle($conn, $key, $overwriteIfExists);
            $results['details'][$key] = $res;

            if ($res['success']) {
                $results[$res['status']][] = $res['name'];
            } else {
                $results['errors'][] = $res['message'] ?? $key;
            }
        }

        if ($results['created'] || $results['updated']) {
            Roles::log($conn, 'seed roles: created ' . implode(', ', $results['created']) . ' | updated ' . implode(', ', $results['updated']));
        }

        return $results;
    }
}
