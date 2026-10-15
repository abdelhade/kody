<?php
/**
 * كتالوج الصلاحيات — المصدر اللي اتولّد منه update/029 و update/030.
 *
 * كل مجموعة = قسم في القائمة الجانبية.
 * crud   => الكيان بياخد الأربع صلاحيات: .view / .add / .edit / .delete
 * single => صلاحية فعل واحدة بالاسم الكامل.
 *
 * legacy = مصدر الصلاحية في usr_pwrs وقت النقل (مؤقت لحد حذف usr_pwrs):
 *   cols:X         ← show_X / add_X / edit_X / delete_X (لكل فعل العمود بتاعه)
 *   cols:X|Y       ← نفس اللي فوق، ويكفي أي واحد من X أو Y
 *   col:C          ← العمود C = 1 (لكل الأفعال لو crud)
 *   any:C1|C2      ← أي عمود منهم = 1
 *   not:C          ← العمود C = 0 أو NULL (صلاحية معكوسة)
 *   all            ← كل الأدوار (الشاشة كانت مفتوحة لأي مستخدم)
 *   role1          ← الدور رقم 1 بس
 */

return [
    'dashboard' => [
        'label' => 'لوحة التحكم',
        'sid'   => [],
        'items' => [
            ['single' => 'dashboard.cards',     'label' => 'كروت الملخص في الشاشة الرئيسية', 'legacy' => 'col:show_main_cards'],
            ['single' => 'dashboard.shortcuts', 'label' => 'الاختصارات في الشاشة الرئيسية',  'legacy' => 'col:show_main_elements'],
            ['single' => 'dashboard.tables',    'label' => 'الجداول في الشاشة الرئيسية',      'legacy' => 'col:show_main_tables'],
            ['single' => 'dashboard.hr',        'label' => 'ملخص الموارد البشرية في الشاشة الرئيسية', 'legacy' => 'col:show_main_hr'],
        ],
    ],

    'basic' => [
        'label' => 'البيانات الأساسية',
        'sid'   => ['sid_entry'],
        'items' => [
            ['crud' => 'clients',    'label' => 'العملاء',              'legacy' => 'cols:clients'],
            ['crud' => 'suppliers',  'label' => 'الموردين',             'legacy' => 'cols:suppliers'],
            ['crud' => 'funds',      'label' => 'الخزائن',              'legacy' => 'cols:funds'],
            ['crud' => 'banks',      'label' => 'البنوك',               'legacy' => 'cols:banks'],
            ['crud' => 'stores',     'label' => 'المخازن',              'legacy' => 'cols:stock'],
            ['crud' => 'expenses',   'label' => 'المصروفات',            'legacy' => 'cols:expenses'],
            ['crud' => 'revenues',   'label' => 'الإيرادات',            'legacy' => 'cols:revenuses'],
            ['crud' => 'creditors',  'label' => 'دائنين آخرين',         'legacy' => 'cols:credits'],
            ['crud' => 'debtors',    'label' => 'مدينين آخرين',         'legacy' => 'cols:depits'],
            ['crud' => 'partners',   'label' => 'الشركاء',              'legacy' => 'cols:partners'],
            ['crud' => 'assets',     'label' => 'الأصول',               'legacy' => 'cols:assets'],
            ['crud' => 'rentables',  'label' => 'الأصول القابلة للتأجير', 'legacy' => 'cols:rentables'],
            ['crud' => 'delivery',   'label' => 'الديليفري',            'legacy' => 'cols:delivery'],
            ['crud' => 'towns',      'label' => 'المدن والمناطق',       'legacy' => 'col:sid_entry'],
        ],
    ],

    'inventory' => [
        'label' => 'المخزون',
        'sid'   => ['sid_stock'],
        'items' => [
            ['crud'   => 'items',                 'label' => 'الأصناف',                    'legacy' => 'cols:items'],
            ['crud'   => 'item_groups',           'label' => 'مجموعات وتصنيفات الأصناف',   'legacy' => 'cols:item_groups'],
            ['crud'   => 'units',                 'label' => 'الوحدات',                    'legacy' => 'col:edit_items'],
            ['single' => 'items.pricing',         'label' => 'التسعير والباركود وإعادة احتساب التكلفة', 'legacy' => 'col:edit_items'],
            ['single' => 'items.opening_balance', 'label' => 'أرصدة أول المدة للأصناف',    'legacy' => 'col:edit_items'],
            ['single' => 'inventory.audit',       'label' => 'جرد المخزن',                 'legacy' => 'col:sid_stock'],
            ['single' => 'stock.sell_negative',   'label' => 'البيع بأكثر من رصيد المخزن', 'legacy' => 'not:prevent_negative_stock'],
        ],
    ],

    'sales' => [
        'label' => 'المبيعات',
        'sid'   => ['sid_sales'],
        'items' => [
            ['crud' => 'sales',         'label' => 'فواتير المبيعات', 'legacy' => 'cols:sales'],
            ['crud' => 'sales_returns', 'label' => 'مردود المبيعات',  'legacy' => 'cols:resale'],
            ['crud' => 'sales_orders',  'label' => 'أوامر البيع',     'legacy' => 'cols:sales'],
            ['crud' => 'quotations',    'label' => 'عروض الأسعار',    'legacy' => 'cols:sales'],
        ],
    ],

    'pos' => [
        'label' => 'نقاط البيع',
        'sid'   => ['sid_sales'],
        'items' => [
            ['single' => 'pos.use',                'label' => 'استخدام نقطة البيع',              'legacy' => 'col:add_sales'],
            ['single' => 'pos.cancel_order',       'label' => 'إلغاء طلب أو حذف صنف من طلب',     'legacy' => 'col:delete_sales'],
            ['single' => 'pos.delivery',           'label' => 'طلبات الديليفري من الكاشير',      'legacy' => 'col:allow_pos_delivery'],
            ['crud'   => 'tables',                 'label' => 'الطاولات',                        'legacy' => 'col:sid_sales'],
            ['single' => 'pos.closed_sessions',    'label' => 'الجلسات المقفولة',                'legacy' => 'col:show_sales'],
            ['single' => 'pos.close_shift',        'label' => 'تقفيل الشيفت',                    'legacy' => 'col:add_sales'],
            ['single' => 'pos.view_shift_expected','label' => 'عرض المتوقع في تقفيل الشيفت (حتى مع الجرد الأعمى)', 'legacy' => 'col:view_shift_expected'],
        ],
    ],

    'visits' => [
        'label' => 'الزيارات',
        'sid'   => ['sid_visits'],
        'items' => [
            ['crud' => 'visits',      'label' => 'الزيارات',      'legacy' => 'col:sid_visits'],
            ['crud' => 'visit_types', 'label' => 'أنواع الزيارات', 'legacy' => 'col:sid_visits'],
        ],
    ],

    'cards' => [
        'label' => 'إدارة الكروت',
        'sid'   => ['sid_cards'],
        'items' => [
            ['crud'   => 'cards',      'label' => 'الكروت',      'legacy' => 'col:sid_cards'],
            ['single' => 'cards.pass', 'label' => 'تمرير كارت', 'legacy' => 'col:sid_cards'],
        ],
    ],

    'purchases' => [
        'label' => 'المشتريات',
        'sid'   => ['sid_purchases'],
        'items' => [
            ['crud' => 'purchases',        'label' => 'فواتير المشتريات', 'legacy' => 'cols:purchases'],
            ['crud' => 'purchase_returns', 'label' => 'مردود المشتريات',  'legacy' => 'cols:repurchases'],
            ['crud' => 'purchase_orders',  'label' => 'أوامر الشراء',     'legacy' => 'cols:purchases'],
        ],
    ],

    'vouchers' => [
        'label' => 'السندات',
        'sid'   => ['sid_vouchers'],
        'items' => [
            ['crud' => 'receipts', 'label' => 'سندات القبض', 'legacy' => 'cols:recive'],
            ['crud' => 'payments', 'label' => 'سندات الصرف', 'legacy' => 'cols:payment'],
        ],
    ],

    'accounting' => [
        'label' => 'الحسابات العامة',
        'sid'   => ['sid_accounts'],
        'items' => [
            ['crud'   => 'journals',                 'label' => 'قيود اليومية',               'legacy' => 'cols:journals|general_entrys'],
            ['single' => 'accounts.chart',           'label' => 'دليل الحسابات وقائمة الحسابات', 'legacy' => 'col:sid_accounts'],
            ['single' => 'accounts.opening_balance', 'label' => 'أرصدة أول المدة للحسابات',    'legacy' => 'col:sid_accounts'],
        ],
    ],

    'assets_ops' => [
        'label' => 'عمليات الأصول',
        'sid'   => ['sid_assets'],
        'items' => [
            ['single' => 'assets.operations', 'label' => 'شراء / بيع / إهلاك أصل', 'legacy' => 'col:sid_assets'],
        ],
    ],

    'rents' => [
        'label' => 'التأجير',
        'sid'   => ['sid_rents'],
        'items' => [
            ['crud'   => 'rent_contracts',            'label' => 'عقود الإيجار',   'legacy' => 'col:sid_rents'],
            ['single' => 'rent_installments.collect', 'label' => 'تحصيل الأقساط', 'legacy' => 'col:sid_rents'],
        ],
    ],

    'clinics' => [
        'label' => 'العيادات',
        'sid'   => ['sid_clinics'],
        'items' => [
            ['crud'   => 'reservations',             'label' => 'الحجوزات',               'legacy' => 'cols:reservations'],
            ['single' => 'reservations.view_ended',  'label' => 'عرض الحجوزات المنتهية',  'legacy' => 'col:show_ended_reservation'],
            ['single' => 'reservations.view_total',  'label' => 'عرض إجمالي الحجوزات',    'legacy' => 'col:show_total_reservation'],
            ['crud'   => 'patients',                 'label' => 'بيانات المرضى والروشتات', 'legacy' => 'cols:clinic_clients'],
            ['crud'   => 'drugs',                    'label' => 'الأدوية',                 'legacy' => 'cols:drugs'],
        ],
    ],

    'hr' => [
        'label' => 'الموارد البشرية',
        'sid'   => ['sid_hr'],
        'items' => [
            ['crud' => 'employees',           'label' => 'الموظفين',            'legacy' => 'cols:employees'],
            ['crud' => 'jobs',                'label' => 'الوظائف',             'legacy' => 'col:sid_hr'],
            ['crud' => 'job_levels',          'label' => 'المستويات الوظيفية',  'legacy' => 'col:sid_hr'],
            ['crud' => 'job_rules',           'label' => 'قواعد الوظائف',       'legacy' => 'col:sid_hr'],
            ['crud' => 'departments',         'label' => 'الأقسام',             'legacy' => 'col:sid_hr'],
            ['crud' => 'shifts',              'label' => 'الورديات',            'legacy' => 'any:sid_hr|sid_payroll'],
            ['crud' => 'hr_operations',       'label' => 'تعريف العمليات وخطواتها', 'legacy' => 'col:sid_hr'],
            ['crud' => 'employee_operations', 'label' => 'عمليات الموظفين',     'legacy' => 'col:sid_hr'],
            ['crud' => 'pulse',               'label' => 'تقييمات Pulse',       'legacy' => 'col:sid_hr'],
            ['crud' => 'pulse_types',         'label' => 'أنواع التقييم',       'legacy' => 'col:sid_hr'],
        ],
    ],

    'tasks' => [
        'label' => 'المهام والأداء والتعاقدات',
        'sid'   => ['sid_payroll'],
        'items' => [
            ['crud'   => 'tasks',          'label' => 'المهام',                         'legacy' => 'col:sid_payroll'],
            ['single' => 'tasks.view_all', 'label' => 'عرض مهام كل الموظفين',           'legacy' => 'col:show_all_tasks'],
            ['crud'   => 'kpis',           'label' => 'معدلات الأداء',                  'legacy' => 'col:sid_payroll'],
            ['crud'   => 'employee_kpis',  'label' => 'أوزان الأداء للموظفين',          'legacy' => 'col:sid_payroll'],
            ['crud'   => 'contracts',      'label' => 'عقود التدريب والتعيين والتعهيد', 'legacy' => 'col:sid_payroll'],
            ['crud'   => 'cvs',            'label' => 'السير الذاتية',                  'legacy' => 'col:sid_payroll'],
            ['crud'   => 'requests',       'label' => 'الطلبات (إجازات وغيرها)',        'legacy' => 'all'],
        ],
    ],

    'payroll' => [
        'label' => 'الحضور والمرتبات',
        'sid'   => ['sid_payroll'],
        'items' => [
            ['crud'   => 'attendance',         'label' => 'سجل الحضور والبصمات والأذونات', 'legacy' => 'cols:attandance'],
            ['crud'   => 'allowances',         'label' => 'البدلات والاستقطاعات',          'legacy' => 'cols:attandance'],
            ['single' => 'attendance.process', 'label' => 'معالجة الحضور',                 'legacy' => 'col:sid_payroll'],
            ['crud'   => 'production',         'label' => 'الإنتاجية اليومية',             'legacy' => 'col:sid_payroll'],
            ['crud'   => 'payroll',            'label' => 'احتسابات الرواتب',              'legacy' => 'col:sid_payroll'],
        ],
    ],

    'crm' => [
        'label' => 'إدارة علاقات العملاء',
        'sid'   => ['sid_crm'],
        'items' => [
            ['crud' => 'crm_clients',    'label' => 'العملاء (متقدم)',          'legacy' => 'cols:advanced_clients'],
            ['crud' => 'client_profile', 'label' => 'بروفايل العميل ومستنداته', 'legacy' => 'cols:client_profile'],
            ['crud' => 'chances',        'label' => 'الفرص',                    'legacy' => 'cols:chances'],
            ['crud' => 'calls',          'label' => 'المكالمات',                'legacy' => 'cols:calls'],
        ],
    ],

    'reports' => [
        'label' => 'التقارير',
        'sid'   => ['sid_reports'],
        'items' => [
            ['single' => 'reports.sales',      'label' => 'تقارير المبيعات',                 'legacy' => 'col:sid_reports'],
            ['single' => 'reports.shift',      'label' => 'تقارير الشيفت',                   'legacy' => 'col:sid_reports'],
            ['single' => 'reports.purchases',  'label' => 'تقارير المشتريات',                'legacy' => 'col:sid_reports'],
            ['single' => 'reports.accounting', 'label' => 'كشوف الحسابات والقوائم المالية',  'legacy' => 'any:sid_reports|show_gl_reports'],
            ['single' => 'reports.rents',      'label' => 'تقارير التأجير',                  'legacy' => 'any:sid_reports|show_rent_reports'],
            ['single' => 'reports.clinics',    'label' => 'تقارير العيادات',                 'legacy' => 'any:sid_clinics|show_clinic_reports'],
            ['single' => 'reports.visits',     'label' => 'إحصائيات الزيارات',               'legacy' => 'col:sid_visits'],
            ['single' => 'reports.payroll',    'label' => 'تقارير المرتبات والحضور',         'legacy' => 'any:sid_payroll|show_payroll_report'],
            ['single' => 'reports.hr',         'label' => 'تقرير الموظفين',                  'legacy' => 'any:sid_hr|show_hr_report'],
        ],
    ],

    'users' => [
        'label' => 'المستخدمين والأدوار',
        'sid'   => [],
        'items' => [
            ['crud'   => 'users',                 'label' => 'المستخدمين',                   'legacy' => 'cols:users'],
            ['single' => 'users.change_password', 'label' => 'تغيير كلمات مرور المستخدمين', 'legacy' => 'col:edit_user_passwords'],
            ['single' => 'users.waiter_codes',    'label' => 'باركود الويترز',               'legacy' => 'col:edit_users'],
            ['crud'   => 'roles',                 'label' => 'الأدوار والصلاحيات',          'legacy' => 'cols:users'],
        ],
    ],

    'system' => [
        'label' => 'الإعدادات والنظام',
        'sid'   => [],
        'items' => [
            ['single' => 'settings.manage', 'label' => 'الإعدادات العامة',                 'legacy' => 'role1'],
            ['single' => 'system.manage',   'label' => 'النظام وقاعدة البيانات والترخيص', 'legacy' => 'role1'],
            ['crud'   => 'news',            'label' => 'الأخبار والإعلانات',               'legacy' => 'role1'],
        ],
    ],
];
