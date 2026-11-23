-- مواءمة مجموعات الصلاحيات مع أقسام السايد بار

-- إدارة المخزون (بيانات الأصناف)
UPDATE `permissions`
SET `group_name` = 'إدارة المخزون', `updated_at` = NOW()
WHERE `guard_name` = 'web' AND `group_key` = 'inventory'
  AND `name` NOT IN (
    'items.opening_balance', 'inventory.audit',
    'store_transfers.view', 'store_transfers.add', 'store_transfers.edit', 'store_transfers.delete',
    'manufacturing.view', 'manufacturing.add', 'manufacturing.edit', 'manufacturing.delete'
  );

-- عمليات المخزون
UPDATE `permissions`
SET `group_key` = 'stock_ops', `group_name` = 'عمليات المخزون', `updated_at` = NOW()
WHERE `guard_name` = 'web' AND `name` IN (
  'items.opening_balance',
  'inventory.audit',
  'store_transfers.view', 'store_transfers.add', 'store_transfers.edit', 'store_transfers.delete'
);

-- التصنيع
UPDATE `permissions`
SET `group_key` = 'manufacturing', `group_name` = 'التصنيع', `updated_at` = NOW()
WHERE `guard_name` = 'web' AND `name` LIKE 'manufacturing.%';

-- بيانات الوظائف (منفصلة عن الموارد البشرية في السايد بار)
UPDATE `permissions`
SET `group_key` = 'job_data', `group_name` = 'بيانات الوظائف', `updated_at` = NOW()
WHERE `guard_name` = 'web' AND (
  `name` LIKE 'employees.%'
  OR `name` LIKE 'jobs.%'
  OR `name` LIKE 'job_levels.%'
  OR `name` LIKE 'job_rules.%'
  OR `name` LIKE 'departments.%'
  OR `name` LIKE 'shifts.%'
  OR `name` LIKE 'hr_operations.%'
  OR `name` LIKE 'employee_operations.%'
);

-- الموارد البشرية (مهام + Pulse جوه قسم الموارد البشرية في السايد بار)
UPDATE `permissions`
SET `group_key` = 'tasks', `group_name` = 'الموارد البشرية', `updated_at` = NOW()
WHERE `guard_name` = 'web' AND (`name` LIKE 'pulse.%' OR `name` LIKE 'pulse_types.%');

UPDATE `permissions`
SET `group_name` = 'الموارد البشرية', `updated_at` = NOW()
WHERE `guard_name` = 'web' AND `group_key` = 'tasks';

-- الحضور والانصراف
UPDATE `permissions`
SET `group_name` = 'الحضور والانصراف', `updated_at` = NOW()
WHERE `guard_name` = 'web' AND `group_key` = 'payroll';

-- الإعدادات
UPDATE `permissions`
SET `group_name` = 'الإعدادات', `updated_at` = NOW()
WHERE `guard_name` = 'web' AND `group_key` = 'system';

-- نقاط البيع
UPDATE `permissions`
SET `group_name` = 'نقاط البيع', `updated_at` = NOW()
WHERE `guard_name` = 'web' AND `group_key` = 'pos';

-- العيادة
UPDATE `permissions`
SET `group_name` = 'العيادة', `updated_at` = NOW()
WHERE `guard_name` = 'web' AND `group_key` = 'clinics';
