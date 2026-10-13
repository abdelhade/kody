<?php
/**
 * RolesSeeder - نظام توليد وتثبيت الأدوار القياسية الجاهزة
 * يتيح إنشاء أدوار المحاسب، المدير المالي، الموارد البشرية HR، الكاشير، أمين المستودع وغيرها بصلاحيات مدروسة.
 */

class RolesSeeder
{
    /**
     * قائمة الأدوار الافتراضية المتاحة في النظام
     */
    public static function getPresets(): array
    {
        return [
            'accountant' => [
                'key'         => 'accountant',
                'rollname'    => 'محاسب',
                'info'        => 'مسؤول الحسابات العامة، قيود اليومية، سندات القبض والصرف، كشوفات الحسابات والتقارير المالية وميزان المراجعة.',
                'icon'        => 'fa-calculator',
                'category'    => 'مالية ومحاسبة',
                'color'       => '#2563eb', // Blue
                'permissions' => [
                    // الأقسام الجانبية
                    'sid_entry'            => 1,
                    'sid_vouchers'         => 1,
                    'sid_accounts'         => 1,
                    'sid_reports'          => 1,
                    'sid_cards'            => 1,
                    // الحسابات والقيود
                    'show_general_entrys'  => 1,
                    'add_general_entrys'   => 1,
                    'edit_general_entrys'  => 1,
                    'delete_general_entrys'=> 0,
                    'show_journals'        => 1,
                    'add_journals'         => 1,
                    'edit_journals'        => 1,
                    'delete_journals'      => 0,
                    // السندات
                    'show_recive'          => 1,
                    'add_recive'           => 1,
                    'edit_recive'          => 1,
                    'delete_recive'        => 0,
                    'show_payment'         => 1,
                    'add_payment'          => 1,
                    'edit_payment'         => 1,
                    'delete_payment'       => 0,
                    // البيانات الأساسية المالية
                    'show_clients'         => 1,
                    'add_clients'          => 1,
                    'edit_clients'         => 1,
                    'delete_clients'       => 0,
                    'show_suppliers'       => 1,
                    'add_suppliers'        => 1,
                    'edit_suppliers'       => 1,
                    'delete_suppliers'     => 0,
                    'show_funds'           => 1,
                    'add_funds'            => 1,
                    'edit_funds'           => 1,
                    'delete_funds'         => 0,
                    'show_banks'           => 1,
                    'add_banks'            => 1,
                    'edit_banks'           => 1,
                    'delete_banks'         => 0,
                    'show_expenses'        => 1,
                    'add_expenses'         => 1,
                    'edit_expenses'        => 1,
                    'delete_expenses'      => 0,
                    'show_revenuses'       => 1,
                    'add_revenuses'        => 1,
                    'edit_revenuses'       => 1,
                    'delete_revenuses'     => 0,
                    'show_credits'         => 1,
                    'add_credits'          => 1,
                    'edit_credits'         => 1,
                    'delete_credits'       => 0,
                    'show_depits'          => 1,
                    'add_depits'           => 1,
                    'edit_depits'          => 1,
                    'delete_depits'        => 0,
                    // التقارير والواجهة
                    'show_gl_reports'      => 1,
                    'show_main_cards'      => 1,
                    'show_main_elements'   => 1,
                    'show_main_tables'     => 1,
                ]
            ],

            'cfo' => [
                'key'         => 'cfo',
                'rollname'    => 'مدير مالي',
                'info'        => 'الإشراف المالي الكامل، الحسابات الختامية، الميزانية وقائمة الدخل، الأصول الثابتة، الرواتب والتقارير الاستراتيجية.',
                'icon'        => 'fa-chart-line',
                'category'    => 'إدارة مالية عليا',
                'color'       => '#7c3aed', // Purple
                'permissions' => [
                    // الأقسام الجانبية
                    'sid_entry'            => 1,
                    'sid_vouchers'         => 1,
                    'sid_accounts'         => 1,
                    'sid_reports'          => 1,
                    'sid_assets'           => 1,
                    'sid_payroll'          => 1,
                    'sid_cards'            => 1,
                    // القيود والحسابات (صلاحيات كاملة تشمل الحذف المالي)
                    'show_general_entrys'  => 1,
                    'add_general_entrys'   => 1,
                    'edit_general_entrys'  => 1,
                    'delete_general_entrys'=> 1,
                    'show_journals'        => 1,
                    'add_journals'         => 1,
                    'edit_journals'        => 1,
                    'delete_journals'      => 1,
                    // السندات
                    'show_recive'          => 1,
                    'add_recive'           => 1,
                    'edit_recive'          => 1,
                    'delete_recive'        => 1,
                    'show_payment'         => 1,
                    'add_payment'          => 1,
                    'edit_payment'         => 1,
                    'delete_payment'       => 1,
                    // الكيانات المالية والأصول
                    'show_clients'         => 1,
                    'add_clients'          => 1,
                    'edit_clients'         => 1,
                    'delete_clients'       => 1,
                    'show_suppliers'       => 1,
                    'add_suppliers'        => 1,
                    'edit_suppliers'       => 1,
                    'delete_suppliers'     => 1,
                    'show_funds'           => 1,
                    'add_funds'            => 1,
                    'edit_funds'           => 1,
                    'delete_funds'         => 1,
                    'show_banks'           => 1,
                    'add_banks'            => 1,
                    'edit_banks'           => 1,
                    'delete_banks'         => 1,
                    'show_expenses'        => 1,
                    'add_expenses'         => 1,
                    'edit_expenses'        => 1,
                    'delete_expenses'      => 1,
                    'show_revenuses'       => 1,
                    'add_revenuses'        => 1,
                    'edit_revenuses'       => 1,
                    'delete_revenuses'     => 1,
                    'show_credits'         => 1,
                    'add_credits'          => 1,
                    'edit_credits'         => 1,
                    'delete_credits'       => 1,
                    'show_depits'          => 1,
                    'add_depits'           => 1,
                    'edit_depits'          => 1,
                    'delete_depits'        => 1,
                    'show_partners'        => 1,
                    'add_partners'         => 1,
                    'edit_partners'        => 1,
                    'delete_partners'      => 1,
                    'show_assets'          => 1,
                    'add_assets'           => 1,
                    'edit_assets'          => 1,
                    'delete_assets'        => 1,
                    // التقارير
                    'show_gl_reports'      => 1,
                    'show_payroll_report'  => 1,
                    'show_all_tasks'       => 1,
                    'show_main_cards'      => 1,
                    'show_main_elements'   => 1,
                    'show_main_tables'     => 1,
                ]
            ],

            'hr_manager' => [
                'key'         => 'hr_manager',
                'rollname'    => 'مسؤول موارد بشرية',
                'info'        => 'إدارة شؤون الموظفين، عقود العمل، الحضور والانصراف، الورديات، مسيرات الرواتب والأجور والتقارير الإدارية.',
                'icon'        => 'fa-user-tie',
                'category'    => 'الموارد البشرية HR',
                'color'       => '#059669', // Emerald
                'permissions' => [
                    // الأقسام الجانبية
                    'sid_hr'               => 1,
                    'sid_payroll'          => 1,
                    'sid_reports'          => 1,
                    // الموظفين
                    'show_employees'       => 1,
                    'add_employees'        => 1,
                    'edit_employees'       => 1,
                    'delete_employees'     => 1,
                    // الحضور والانصراف
                    'show_attandance'      => 1,
                    'add_attandance'       => 1,
                    'edit_attandance'      => 1,
                    'delete_attandance'    => 1,
                    // التقارير والمهام
                    'show_hr_report'       => 1,
                    'show_payroll_report'  => 1,
                    'show_all_tasks'       => 1,
                    'show_main_hr'         => 1,
                    'show_main_cards'      => 1,
                ]
            ],

            'storekeeper' => [
                'key'         => 'storekeeper',
                'rollname'    => 'أمين مستودع',
                'info'        => 'إدارة المخزون، الأصناف وفئات المنتجات، أذونات التوريد والإضافة، فواتير المشتريات ومردوداتها، وجرد المستودع.',
                'icon'        => 'fa-boxes',
                'category'    => 'المخازن والمستودعات',
                'color'       => '#d97706', // Amber
                'permissions' => [
                    // الأقسام الجانبية
                    'sid_stock'            => 1,
                    'sid_purchases'        => 1,
                    'sid_cards'            => 1,
                    'sid_reports'          => 1,
                    // المخزون والأصناف
                    'show_stock'           => 1,
                    'add_stock'            => 1,
                    'edit_stock'           => 1,
                    'delete_stock'         => 0,
                    'show_items'           => 1,
                    'add_items'            => 1,
                    'edit_items'           => 1,
                    'delete_items'         => 0,
                    'show_item_groups'     => 1,
                    'add_item_groups'      => 1,
                    'edit_item_groups'     => 1,
                    'delete_item_groups'   => 0,
                    'prevent_negative_stock'=> 1,
                    // المشتريات
                    'show_purchases'       => 1,
                    'add_purchases'        => 1,
                    'edit_purchases'       => 1,
                    'delete_purchases'     => 0,
                    'show_repurchases'     => 1,
                    'add_repurchases'      => 1,
                    'edit_repurchases'     => 1,
                    'delete_repurchases'   => 0,
                    'show_suppliers'       => 1,
                    'show_main_tables'     => 1,
                ]
            ],

            'cashier' => [
                'key'         => 'cashier',
                'rollname'    => 'كاشير مبيعات',
                'info'        => 'إجراء عمليات البيع وإصدار الفواتير عبر شاشة الكاشير وPOS، تسجيل مرتجع المبيعات، وسندات القبض، وخدمة التوصيل.',
                'icon'        => 'fa-cash-register',
                'category'    => 'نقاط البيع والكاشير',
                'color'       => '#0891b2', // Cyan
                'permissions' => [
                    // الأقسام الجانبية
                    'sid_sales'            => 1,
                    // المبيعات ونقاط البيع
                    'show_sales'           => 1,
                    'add_sales'            => 1,
                    'edit_sales'           => 0,
                    'delete_sales'         => 0,
                    'show_resale'          => 1,
                    'add_resale'           => 1,
                    'edit_resale'          => 0,
                    'delete_resale'        => 0,
                    'show_recive'          => 1,
                    'add_recive'           => 1,
                    'show_delivery'        => 1,
                    'add_delivery'         => 1,
                    'allow_pos_delivery'   => 1,
                    'prevent_negative_stock'=> 1,
                    'show_main_cards'      => 1,
                ]
            ],

            'sales_manager' => [
                'key'         => 'sales_manager',
                'rollname'    => 'مدير مبيعات',
                'info'        => 'الإشراف على حركة المبيعات، عروض الأسعار، إدارة العملاء، إدارة علاقات العملاء CRM، ومتابعة تقارير المبيعات.',
                'icon'        => 'fa-funnel-dollar',
                'category'    => 'المبيعات و CRM',
                'color'       => '#ea580c', // Orange
                'permissions' => [
                    // الأقسام الجانبية
                    'sid_sales'            => 1,
                    'sid_crm'              => 1,
                    'sid_reports'          => 1,
                    'sid_entry'            => 1,
                    // المبيعات والمرتجعات
                    'show_sales'           => 1,
                    'add_sales'            => 1,
                    'edit_sales'           => 1,
                    'delete_sales'         => 0,
                    'show_resale'          => 1,
                    'add_resale'           => 1,
                    'edit_resale'          => 1,
                    'delete_resale'        => 0,
                    // العملاء و CRM
                    'show_clients'         => 1,
                    'add_clients'          => 1,
                    'edit_clients'         => 1,
                    'delete_clients'       => 0,
                    'show_client_profile'  => 1,
                    'add_client_profile'   => 1,
                    'edit_client_profile'  => 1,
                    'show_advanced_clients'=> 1,
                    'add_advanced_clients' => 1,
                    'edit_advanced_clients'=> 1,
                    'show_chances'         => 1,
                    'add_chances'          => 1,
                    'edit_chances'         => 1,
                    'delete_chances'       => 0,
                    'show_calls'           => 1,
                    'add_calls'            => 1,
                    'edit_calls'           => 1,
                    // التوصيل واللوحة
                    'show_delivery'        => 1,
                    'add_delivery'         => 1,
                    'edit_delivery'        => 1,
                    'allow_pos_delivery'   => 1,
                    'show_main_cards'      => 1,
                    'show_main_tables'     => 1,
                ]
            ],

            'purchasing_manager' => [
                'key'         => 'purchasing_manager',
                'rollname'    => 'مدير مشتريات',
                'info'        => 'إدارة الموردين، طلبات وفواتير الشراء، مرتجعات المشتريات، متابعة أسعار وتكاليف الأصناف وسندات الصرف.',
                'icon'        => 'fa-truck-loading',
                'category'    => 'المشتريات والموردين',
                'color'       => '#4f46e5', // Indigo
                'permissions' => [
                    'sid_purchases'        => 1,
                    'sid_stock'            => 1,
                    'sid_vouchers'         => 1,
                    'sid_entry'            => 1,
                    'sid_reports'          => 1,
                    'show_purchases'       => 1,
                    'add_purchases'        => 1,
                    'edit_purchases'       => 1,
                    'delete_purchases'     => 0,
                    'show_repurchases'     => 1,
                    'add_repurchases'      => 1,
                    'edit_repurchases'     => 1,
                    'delete_repurchases'   => 0,
                    'show_suppliers'       => 1,
                    'add_suppliers'        => 1,
                    'edit_suppliers'       => 1,
                    'delete_suppliers'     => 0,
                    'show_items'           => 1,
                    'show_stock'           => 1,
                    'show_payment'         => 1,
                    'add_payment'          => 1,
                    'edit_payment'         => 1,
                    'show_main_tables'     => 1,
                ]
            ],

            'receptionist' => [
                'key'         => 'receptionist',
                'rollname'    => 'موظف استقبال',
                'info'        => 'تسجيل الحجوزات والمواعيد، استقبال العملاء والزوار، متابعة سجل الاتصالات والزيارات وخدمة العملاء.',
                'icon'        => 'fa-concierge-bell',
                'category'    => 'الاستقبال والعملاء',
                'color'       => '#db2777', // Pink
                'permissions' => [
                    'sid_crm'              => 1,
                    'sid_clinics'          => 1,
                    'sid_visits'           => 1,
                    'show_reservations'    => 1,
                    'add_reservations'     => 1,
                    'edit_reservations'    => 1,
                    'delete_reservations'  => 0,
                    'show_total_reservation'=> 1,
                    'show_ended_reservation'=> 1,
                    'show_clients'         => 1,
                    'add_clients'          => 1,
                    'edit_clients'         => 1,
                    'show_client_profile'  => 1,
                    'show_calls'           => 1,
                    'add_calls'            => 1,
                    'show_main_cards'      => 1,
                ]
            ],

            'auditor' => [
                'key'         => 'auditor',
                'rollname'    => 'مراجع داخلي',
                'info'        => 'صلاحية تدقيق ومراجعة شاملة لجميع السجلات والقيود والمخازن والتقارير المالية (عرض وقراءة فقط بدون أي إضافة أو تعديل أو حذف).',
                'icon'        => 'fa-search-dollar',
                'category'    => 'التدقيق والرقابة (عرض فقط)',
                'color'       => '#475569', // Slate
                'permissions' => [
                    // جميع الأقسام الجانبية
                    'sid_entry'            => 1,
                    'sid_stock'            => 1,
                    'sid_sales'            => 1,
                    'sid_purchases'        => 1,
                    'sid_vouchers'         => 1,
                    'sid_accounts'         => 1,
                    'sid_reports'          => 1,
                    'sid_assets'           => 1,
                    'sid_payroll'          => 1,
                    'sid_hr'               => 1,
                    // استعراض القيود والسندات والبيانات فقط
                    'show_general_entrys'  => 1,
                    'show_journals'        => 1,
                    'show_recive'          => 1,
                    'show_payment'         => 1,
                    'show_clients'         => 1,
                    'show_suppliers'       => 1,
                    'show_funds'           => 1,
                    'show_banks'           => 1,
                    'show_expenses'        => 1,
                    'show_revenuses'       => 1,
                    'show_credits'         => 1,
                    'show_depits'          => 1,
                    'show_items'           => 1,
                    'show_stock'           => 1,
                    'show_sales'           => 1,
                    'show_purchases'       => 1,
                    'show_employees'       => 1,
                    'show_attandance'      => 1,
                    'show_gl_reports'      => 1,
                    'show_payroll_report'  => 1,
                    'show_hr_report'       => 1,
                    'show_main_cards'      => 1,
                    'show_main_tables'     => 1,
                    // منع الإضافة والتعديل والحذف تماماً
                    'add_general_entrys'   => 0,
                    'edit_general_entrys'  => 0,
                    'delete_general_entrys'=> 0,
                    'add_journals'         => 0,
                    'edit_journals'        => 0,
                    'delete_journals'      => 0,
                    'add_recive'           => 0,
                    'edit_recive'          => 0,
                    'delete_recive'        => 0,
                    'add_payment'          => 0,
                    'edit_payment'         => 0,
                    'delete_payment'       => 0,
                    'add_clients'          => 0,
                    'edit_clients'         => 0,
                    'delete_clients'       => 0,
                ]
            ],
        ];
    }

    /**
     * إرجاع قائمة الأعمدة الفعلية الموجودة في جدول usr_pwrs
     */
    public static function getTableColumns(mysqli $conn): array
    {
        static $columns = null;
        if ($columns !== null) {
            return $columns;
        }

        $columns = [];
        $res = $conn->query("SHOW COLUMNS FROM `usr_pwrs`");
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $columns[] = $row['Field'];
            }
        }
        return $columns;
    }

    /**
     * فحص الأدوار المسجلة حالياً في قاعدة البيانات
     * ترجع مصفوفة مرتبطة: [اسم_الدور => id]
     */
    public static function getExistingRoles(mysqli $conn): array
    {
        $existing = [];
        $res = $conn->query("SELECT `id`, `rollname` FROM `usr_pwrs` WHERE COALESCE(`isdeleted`, 0) != 1");
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $name = trim((string)$row['rollname']);
                if ($name !== '') {
                    $existing[$name] = (int)$row['id'];
                }
            }
        }
        return $existing;
    }

    /**
     * زراعة دور محدد
     */
    public static function seedSingle(mysqli $conn, string $presetKey, bool $overwriteIfExists = false): array
    {
        $presets = self::getPresets();
        if (!isset($presets[$presetKey])) {
            return [
                'success' => false,
                'status'  => 'error',
                'message' => "الدور المحدد غير معروف ($presetKey)"
            ];
        }

        $preset = $presets[$presetKey];
        $roleName = $preset['rollname'];
        $existing = self::getExistingRoles($conn);
        $columns = self::getTableColumns($conn);

        // إذا كان الدور موجوداً مسبقاً
        if (isset($existing[$roleName])) {
            $existingId = $existing[$roleName];

            if (!$overwriteIfExists) {
                return [
                    'success' => true,
                    'status'  => 'skipped',
                    'id'      => $existingId,
                    'name'    => $roleName,
                    'message' => "الدور \"$roleName\" موجود مسبقاً (تم تخطيه)"
                ];
            }

            // تحديث الدور القائم
            $updates = [];
            $updates[] = "`info` = '" . $conn->real_escape_string($preset['info']) . "'";
            $updates[] = "`is_active` = 1";

            foreach ($columns as $col) {
                if (in_array($col, ['id', 'rollname', 'info', 'crtime', 'mdtime', 'isdeleted', 'tenant', 'branch'])) {
                    continue;
                }
                $val = isset($preset['permissions'][$col]) ? (int)$preset['permissions'][$col] : 0;
                $updates[] = "`$col` = $val";
            }

            $updateSql = "UPDATE `usr_pwrs` SET " . implode(', ', $updates) . " WHERE `id` = $existingId";
            if ($conn->query($updateSql)) {
                return [
                    'success' => true,
                    'status'  => 'updated',
                    'id'      => $existingId,
                    'name'    => $roleName,
                    'message' => "تم تحديث صلاحيات الدور \"$roleName\" بنجاح"
                ];
            } else {
                return [
                    'success' => false,
                    'status'  => 'error',
                    'name'    => $roleName,
                    'message' => "فشل في تحديث الدور \"$roleName\": " . $conn->error
                ];
            }
        }

        // إضافة دور جديد
        $insertData = [];
        $insertData['rollname'] = "'" . $conn->real_escape_string($roleName) . "'";
        $insertData['info'] = "'" . $conn->real_escape_string($preset['info']) . "'";

        if (in_array('is_active', $columns)) {
            $insertData['is_active'] = 1;
        }
        if (in_array('isdeleted', $columns)) {
            $insertData['isdeleted'] = 0;
        }

        foreach ($columns as $col) {
            if (isset($insertData[$col])) {
                continue;
            }
            if (in_array($col, ['id', 'crtime', 'mdtime', 'tenant', 'branch'])) {
                continue;
            }
            $val = isset($preset['permissions'][$col]) ? (int)$preset['permissions'][$col] : 0;
            $insertData[$col] = $val;
        }

        $fields = array_keys($insertData);
        $values = array_values($insertData);

        $insertSql = "INSERT INTO `usr_pwrs` (`" . implode('`, `', $fields) . "`) VALUES (" . implode(', ', $values) . ")";
        if ($conn->query($insertSql)) {
            $newId = $conn->insert_id;
            return [
                'success' => true,
                'status'  => 'created',
                'id'      => $newId,
                'name'    => $roleName,
                'message' => "تم إنشاء الدور \"$roleName\" بنجاح"
            ];
        } else {
            return [
                'success' => false,
                'status'  => 'error',
                'name'    => $roleName,
                'message' => "فشل إنشاء الدور \"$roleName\": " . $conn->error
            ];
        }
    }

    /**
     * زراعة مجموعة من الأدوار أو جميعها
     */
    public static function seedMany(mysqli $conn, array $keys = [], bool $overwriteIfExists = false): array
    {
        $presets = self::getPresets();
        if (empty($keys)) {
            $keys = array_keys($presets);
        }

        $results = [
            'created' => [],
            'updated' => [],
            'skipped' => [],
            'errors'  => [],
            'details' => []
        ];

        foreach ($keys as $key) {
            if (!isset($presets[$key])) {
                continue;
            }
            $res = self::seedSingle($conn, $key, $overwriteIfExists);
            $results['details'][$key] = $res;

            if ($res['success']) {
                if ($res['status'] === 'created') {
                    $results['created'][] = $res['name'];
                } elseif ($res['status'] === 'updated') {
                    $results['updated'][] = $res['name'];
                } elseif ($res['status'] === 'skipped') {
                    $results['skipped'][] = $res['name'];
                }
            } else {
                $results['errors'][] = $res['message'] ?? $key;
            }
        }

        // تسجيل العملية في جدول process إن وجد
        $totalChanged = count($results['created']) + count($results['updated']);
        if ($totalChanged > 0) {
            $desc = "seed roles: created " . implode(', ', $results['created']) . " | updated " . implode(', ', $results['updated']);
            @$conn->query("INSERT INTO `process` (`type`) VALUES ('" . $conn->real_escape_string($desc) . "')");
        }

        return $results;
    }
}
