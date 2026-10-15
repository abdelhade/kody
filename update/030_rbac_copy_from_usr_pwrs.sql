-- نقل الأدوار والصلاحيات من usr_pwrs للجداول الجديدة.
-- متولّد من includes/rbac_catalog.php بـ tools/gen_rbac_sql.php — متعدّلش يدوي.
-- الأدوار بتتنقل بنفس الـ id عشان users.userrole يفضل صح. الدور المحذوف بيتنقل لو لسه عليه مستخدمين
-- (النظام القديم كان بيحمّل صلاحياته عادي)، والاسم المكرر بياخد رقمه عشان UNIQUE (name, guard_name).

INSERT IGNORE INTO `roles` (`id`, `name`, `guard_name`, `description`, `is_active`, `created_at`, `updated_at`)
SELECT u.`id`,
       CASE
         WHEN NULLIF(TRIM(u.`rollname`), '') IS NULL THEN CONCAT('دور ', u.`id`)
         WHEN EXISTS (SELECT 1 FROM `usr_pwrs` u2
                      WHERE TRIM(u2.`rollname`) = TRIM(u.`rollname`) AND u2.`id` < u.`id`)
           THEN CONCAT(TRIM(u.`rollname`), ' #', u.`id`)
         ELSE TRIM(u.`rollname`)
       END,
       'web',
       u.`info`,
       COALESCE(u.`is_active`, 1),
       NOW(), NOW()
FROM `usr_pwrs` u
WHERE COALESCE(u.`isdeleted`, 0) = 0
   OR EXISTS (SELECT 1 FROM `users` us WHERE us.`userrole` = u.`id`);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'dashboard.cards' AND p.`guard_name` = 'web'
WHERE (u.`show_main_cards` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'dashboard.shortcuts' AND p.`guard_name` = 'web'
WHERE (u.`show_main_elements` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'dashboard.tables' AND p.`guard_name` = 'web'
WHERE (u.`show_main_tables` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'dashboard.hr' AND p.`guard_name` = 'web'
WHERE (u.`show_main_hr` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'clients.view' AND p.`guard_name` = 'web'
WHERE (u.`show_clients` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'clients.add' AND p.`guard_name` = 'web'
WHERE (u.`add_clients` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'clients.edit' AND p.`guard_name` = 'web'
WHERE (u.`edit_clients` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'clients.delete' AND p.`guard_name` = 'web'
WHERE (u.`delete_clients` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'suppliers.view' AND p.`guard_name` = 'web'
WHERE (u.`show_suppliers` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'suppliers.add' AND p.`guard_name` = 'web'
WHERE (u.`add_suppliers` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'suppliers.edit' AND p.`guard_name` = 'web'
WHERE (u.`edit_suppliers` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'suppliers.delete' AND p.`guard_name` = 'web'
WHERE (u.`delete_suppliers` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'funds.view' AND p.`guard_name` = 'web'
WHERE (u.`show_funds` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'funds.add' AND p.`guard_name` = 'web'
WHERE (u.`add_funds` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'funds.edit' AND p.`guard_name` = 'web'
WHERE (u.`edit_funds` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'funds.delete' AND p.`guard_name` = 'web'
WHERE (u.`delete_funds` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'banks.view' AND p.`guard_name` = 'web'
WHERE (u.`show_banks` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'banks.add' AND p.`guard_name` = 'web'
WHERE (u.`add_banks` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'banks.edit' AND p.`guard_name` = 'web'
WHERE (u.`edit_banks` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'banks.delete' AND p.`guard_name` = 'web'
WHERE (u.`delete_banks` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'stores.view' AND p.`guard_name` = 'web'
WHERE (u.`show_stock` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'stores.add' AND p.`guard_name` = 'web'
WHERE (u.`add_stock` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'stores.edit' AND p.`guard_name` = 'web'
WHERE (u.`edit_stock` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'stores.delete' AND p.`guard_name` = 'web'
WHERE (u.`delete_stock` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'expenses.view' AND p.`guard_name` = 'web'
WHERE (u.`show_expenses` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'expenses.add' AND p.`guard_name` = 'web'
WHERE (u.`add_expenses` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'expenses.edit' AND p.`guard_name` = 'web'
WHERE (u.`edit_expenses` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'expenses.delete' AND p.`guard_name` = 'web'
WHERE (u.`delete_expenses` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'revenues.view' AND p.`guard_name` = 'web'
WHERE (u.`show_revenuses` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'revenues.add' AND p.`guard_name` = 'web'
WHERE (u.`add_revenuses` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'revenues.edit' AND p.`guard_name` = 'web'
WHERE (u.`edit_revenuses` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'revenues.delete' AND p.`guard_name` = 'web'
WHERE (u.`delete_revenuses` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'creditors.view' AND p.`guard_name` = 'web'
WHERE (u.`show_credits` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'creditors.add' AND p.`guard_name` = 'web'
WHERE (u.`add_credits` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'creditors.edit' AND p.`guard_name` = 'web'
WHERE (u.`edit_credits` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'creditors.delete' AND p.`guard_name` = 'web'
WHERE (u.`delete_credits` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'debtors.view' AND p.`guard_name` = 'web'
WHERE (u.`show_depits` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'debtors.add' AND p.`guard_name` = 'web'
WHERE (u.`add_depits` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'debtors.edit' AND p.`guard_name` = 'web'
WHERE (u.`edit_depits` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'debtors.delete' AND p.`guard_name` = 'web'
WHERE (u.`delete_depits` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'partners.view' AND p.`guard_name` = 'web'
WHERE (u.`show_partners` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'partners.add' AND p.`guard_name` = 'web'
WHERE (u.`add_partners` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'partners.edit' AND p.`guard_name` = 'web'
WHERE (u.`edit_partners` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'partners.delete' AND p.`guard_name` = 'web'
WHERE (u.`delete_partners` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'assets.view' AND p.`guard_name` = 'web'
WHERE (u.`show_assets` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'assets.add' AND p.`guard_name` = 'web'
WHERE (u.`add_assets` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'assets.edit' AND p.`guard_name` = 'web'
WHERE (u.`edit_assets` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'assets.delete' AND p.`guard_name` = 'web'
WHERE (u.`delete_assets` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'rentables.view' AND p.`guard_name` = 'web'
WHERE (u.`show_rentables` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'rentables.add' AND p.`guard_name` = 'web'
WHERE (u.`add_rentables` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'rentables.edit' AND p.`guard_name` = 'web'
WHERE (u.`edit_rentables` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'rentables.delete' AND p.`guard_name` = 'web'
WHERE (u.`delete_rentables` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'delivery.view' AND p.`guard_name` = 'web'
WHERE (u.`show_delivery` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'delivery.add' AND p.`guard_name` = 'web'
WHERE (u.`add_delivery` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'delivery.edit' AND p.`guard_name` = 'web'
WHERE (u.`edit_delivery` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'delivery.delete' AND p.`guard_name` = 'web'
WHERE (u.`delete_delivery` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'towns.view' AND p.`guard_name` = 'web'
WHERE (u.`sid_entry` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'towns.add' AND p.`guard_name` = 'web'
WHERE (u.`sid_entry` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'towns.edit' AND p.`guard_name` = 'web'
WHERE (u.`sid_entry` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'towns.delete' AND p.`guard_name` = 'web'
WHERE (u.`sid_entry` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'items.view' AND p.`guard_name` = 'web'
WHERE (u.`show_items` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'items.add' AND p.`guard_name` = 'web'
WHERE (u.`add_items` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'items.edit' AND p.`guard_name` = 'web'
WHERE (u.`edit_items` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'items.delete' AND p.`guard_name` = 'web'
WHERE (u.`delete_items` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'item_groups.view' AND p.`guard_name` = 'web'
WHERE (u.`show_item_groups` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'item_groups.add' AND p.`guard_name` = 'web'
WHERE (u.`add_item_groups` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'item_groups.edit' AND p.`guard_name` = 'web'
WHERE (u.`edit_item_groups` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'item_groups.delete' AND p.`guard_name` = 'web'
WHERE (u.`delete_item_groups` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'units.view' AND p.`guard_name` = 'web'
WHERE (u.`edit_items` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'units.add' AND p.`guard_name` = 'web'
WHERE (u.`edit_items` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'units.edit' AND p.`guard_name` = 'web'
WHERE (u.`edit_items` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'units.delete' AND p.`guard_name` = 'web'
WHERE (u.`edit_items` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'items.pricing' AND p.`guard_name` = 'web'
WHERE (u.`edit_items` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'items.opening_balance' AND p.`guard_name` = 'web'
WHERE (u.`edit_items` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'inventory.audit' AND p.`guard_name` = 'web'
WHERE (u.`sid_stock` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'stock.sell_negative' AND p.`guard_name` = 'web'
WHERE COALESCE(u.`prevent_negative_stock`, 0) = 0;

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'sales.view' AND p.`guard_name` = 'web'
WHERE (u.`show_sales` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'sales.add' AND p.`guard_name` = 'web'
WHERE (u.`add_sales` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'sales.edit' AND p.`guard_name` = 'web'
WHERE (u.`edit_sales` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'sales.delete' AND p.`guard_name` = 'web'
WHERE (u.`delete_sales` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'sales_returns.view' AND p.`guard_name` = 'web'
WHERE (u.`show_resale` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'sales_returns.add' AND p.`guard_name` = 'web'
WHERE (u.`add_resale` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'sales_returns.edit' AND p.`guard_name` = 'web'
WHERE (u.`edit_resale` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'sales_returns.delete' AND p.`guard_name` = 'web'
WHERE (u.`delete_resale` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'sales_orders.view' AND p.`guard_name` = 'web'
WHERE (u.`show_sales` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'sales_orders.add' AND p.`guard_name` = 'web'
WHERE (u.`add_sales` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'sales_orders.edit' AND p.`guard_name` = 'web'
WHERE (u.`edit_sales` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'sales_orders.delete' AND p.`guard_name` = 'web'
WHERE (u.`delete_sales` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'quotations.view' AND p.`guard_name` = 'web'
WHERE (u.`show_sales` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'quotations.add' AND p.`guard_name` = 'web'
WHERE (u.`add_sales` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'quotations.edit' AND p.`guard_name` = 'web'
WHERE (u.`edit_sales` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'quotations.delete' AND p.`guard_name` = 'web'
WHERE (u.`delete_sales` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'pos.use' AND p.`guard_name` = 'web'
WHERE (u.`add_sales` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'pos.cancel_order' AND p.`guard_name` = 'web'
WHERE (u.`delete_sales` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'pos.delivery' AND p.`guard_name` = 'web'
WHERE (u.`allow_pos_delivery` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'tables.view' AND p.`guard_name` = 'web'
WHERE (u.`sid_sales` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'tables.add' AND p.`guard_name` = 'web'
WHERE (u.`sid_sales` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'tables.edit' AND p.`guard_name` = 'web'
WHERE (u.`sid_sales` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'tables.delete' AND p.`guard_name` = 'web'
WHERE (u.`sid_sales` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'pos.closed_sessions' AND p.`guard_name` = 'web'
WHERE (u.`show_sales` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'pos.close_shift' AND p.`guard_name` = 'web'
WHERE (u.`add_sales` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'pos.view_shift_expected' AND p.`guard_name` = 'web'
WHERE (u.`view_shift_expected` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'visits.view' AND p.`guard_name` = 'web'
WHERE (u.`sid_visits` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'visits.add' AND p.`guard_name` = 'web'
WHERE (u.`sid_visits` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'visits.edit' AND p.`guard_name` = 'web'
WHERE (u.`sid_visits` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'visits.delete' AND p.`guard_name` = 'web'
WHERE (u.`sid_visits` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'visit_types.view' AND p.`guard_name` = 'web'
WHERE (u.`sid_visits` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'visit_types.add' AND p.`guard_name` = 'web'
WHERE (u.`sid_visits` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'visit_types.edit' AND p.`guard_name` = 'web'
WHERE (u.`sid_visits` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'visit_types.delete' AND p.`guard_name` = 'web'
WHERE (u.`sid_visits` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'cards.view' AND p.`guard_name` = 'web'
WHERE (u.`sid_cards` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'cards.add' AND p.`guard_name` = 'web'
WHERE (u.`sid_cards` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'cards.edit' AND p.`guard_name` = 'web'
WHERE (u.`sid_cards` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'cards.delete' AND p.`guard_name` = 'web'
WHERE (u.`sid_cards` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'cards.pass' AND p.`guard_name` = 'web'
WHERE (u.`sid_cards` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'purchases.view' AND p.`guard_name` = 'web'
WHERE (u.`show_purchases` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'purchases.add' AND p.`guard_name` = 'web'
WHERE (u.`add_purchases` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'purchases.edit' AND p.`guard_name` = 'web'
WHERE (u.`edit_purchases` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'purchases.delete' AND p.`guard_name` = 'web'
WHERE (u.`delete_purchases` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'purchase_returns.view' AND p.`guard_name` = 'web'
WHERE (u.`show_repurchases` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'purchase_returns.add' AND p.`guard_name` = 'web'
WHERE (u.`add_repurchases` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'purchase_returns.edit' AND p.`guard_name` = 'web'
WHERE (u.`edit_repurchases` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'purchase_returns.delete' AND p.`guard_name` = 'web'
WHERE (u.`delete_repurchases` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'purchase_orders.view' AND p.`guard_name` = 'web'
WHERE (u.`show_purchases` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'purchase_orders.add' AND p.`guard_name` = 'web'
WHERE (u.`add_purchases` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'purchase_orders.edit' AND p.`guard_name` = 'web'
WHERE (u.`edit_purchases` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'purchase_orders.delete' AND p.`guard_name` = 'web'
WHERE (u.`delete_purchases` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'receipts.view' AND p.`guard_name` = 'web'
WHERE (u.`show_recive` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'receipts.add' AND p.`guard_name` = 'web'
WHERE (u.`add_recive` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'receipts.edit' AND p.`guard_name` = 'web'
WHERE (u.`edit_recive` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'receipts.delete' AND p.`guard_name` = 'web'
WHERE (u.`delete_recive` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'payments.view' AND p.`guard_name` = 'web'
WHERE (u.`show_payment` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'payments.add' AND p.`guard_name` = 'web'
WHERE (u.`add_payment` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'payments.edit' AND p.`guard_name` = 'web'
WHERE (u.`edit_payment` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'payments.delete' AND p.`guard_name` = 'web'
WHERE (u.`delete_payment` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'journals.view' AND p.`guard_name` = 'web'
WHERE (u.`show_journals` = 1 OR u.`show_general_entrys` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'journals.add' AND p.`guard_name` = 'web'
WHERE (u.`add_journals` = 1 OR u.`add_general_entrys` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'journals.edit' AND p.`guard_name` = 'web'
WHERE (u.`edit_journals` = 1 OR u.`edit_general_entrys` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'journals.delete' AND p.`guard_name` = 'web'
WHERE (u.`delete_journals` = 1 OR u.`delete_general_entrys` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'accounts.chart' AND p.`guard_name` = 'web'
WHERE (u.`sid_accounts` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'accounts.opening_balance' AND p.`guard_name` = 'web'
WHERE (u.`sid_accounts` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'assets.operations' AND p.`guard_name` = 'web'
WHERE (u.`sid_assets` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'rent_contracts.view' AND p.`guard_name` = 'web'
WHERE (u.`sid_rents` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'rent_contracts.add' AND p.`guard_name` = 'web'
WHERE (u.`sid_rents` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'rent_contracts.edit' AND p.`guard_name` = 'web'
WHERE (u.`sid_rents` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'rent_contracts.delete' AND p.`guard_name` = 'web'
WHERE (u.`sid_rents` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'rent_installments.collect' AND p.`guard_name` = 'web'
WHERE (u.`sid_rents` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'reservations.view' AND p.`guard_name` = 'web'
WHERE (u.`show_reservations` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'reservations.add' AND p.`guard_name` = 'web'
WHERE (u.`add_reservations` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'reservations.edit' AND p.`guard_name` = 'web'
WHERE (u.`edit_reservations` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'reservations.delete' AND p.`guard_name` = 'web'
WHERE (u.`delete_reservations` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'reservations.view_ended' AND p.`guard_name` = 'web'
WHERE (u.`show_ended_reservation` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'reservations.view_total' AND p.`guard_name` = 'web'
WHERE (u.`show_total_reservation` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'patients.view' AND p.`guard_name` = 'web'
WHERE (u.`show_clinic_clients` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'patients.add' AND p.`guard_name` = 'web'
WHERE (u.`add_clinic_clients` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'patients.edit' AND p.`guard_name` = 'web'
WHERE (u.`edit_clinic_clients` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'patients.delete' AND p.`guard_name` = 'web'
WHERE (u.`delete_clinic_clients` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'drugs.view' AND p.`guard_name` = 'web'
WHERE (u.`show_drugs` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'drugs.add' AND p.`guard_name` = 'web'
WHERE (u.`add_drugs` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'drugs.edit' AND p.`guard_name` = 'web'
WHERE (u.`edit_drugs` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'drugs.delete' AND p.`guard_name` = 'web'
WHERE (u.`delete_drugs` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'employees.view' AND p.`guard_name` = 'web'
WHERE (u.`show_employees` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'employees.add' AND p.`guard_name` = 'web'
WHERE (u.`add_employees` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'employees.edit' AND p.`guard_name` = 'web'
WHERE (u.`edit_employees` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'employees.delete' AND p.`guard_name` = 'web'
WHERE (u.`delete_employees` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'jobs.view' AND p.`guard_name` = 'web'
WHERE (u.`sid_hr` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'jobs.add' AND p.`guard_name` = 'web'
WHERE (u.`sid_hr` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'jobs.edit' AND p.`guard_name` = 'web'
WHERE (u.`sid_hr` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'jobs.delete' AND p.`guard_name` = 'web'
WHERE (u.`sid_hr` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'job_levels.view' AND p.`guard_name` = 'web'
WHERE (u.`sid_hr` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'job_levels.add' AND p.`guard_name` = 'web'
WHERE (u.`sid_hr` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'job_levels.edit' AND p.`guard_name` = 'web'
WHERE (u.`sid_hr` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'job_levels.delete' AND p.`guard_name` = 'web'
WHERE (u.`sid_hr` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'job_rules.view' AND p.`guard_name` = 'web'
WHERE (u.`sid_hr` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'job_rules.add' AND p.`guard_name` = 'web'
WHERE (u.`sid_hr` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'job_rules.edit' AND p.`guard_name` = 'web'
WHERE (u.`sid_hr` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'job_rules.delete' AND p.`guard_name` = 'web'
WHERE (u.`sid_hr` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'departments.view' AND p.`guard_name` = 'web'
WHERE (u.`sid_hr` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'departments.add' AND p.`guard_name` = 'web'
WHERE (u.`sid_hr` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'departments.edit' AND p.`guard_name` = 'web'
WHERE (u.`sid_hr` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'departments.delete' AND p.`guard_name` = 'web'
WHERE (u.`sid_hr` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'shifts.view' AND p.`guard_name` = 'web'
WHERE (u.`sid_hr` = 1 OR u.`sid_payroll` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'shifts.add' AND p.`guard_name` = 'web'
WHERE (u.`sid_hr` = 1 OR u.`sid_payroll` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'shifts.edit' AND p.`guard_name` = 'web'
WHERE (u.`sid_hr` = 1 OR u.`sid_payroll` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'shifts.delete' AND p.`guard_name` = 'web'
WHERE (u.`sid_hr` = 1 OR u.`sid_payroll` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'hr_operations.view' AND p.`guard_name` = 'web'
WHERE (u.`sid_hr` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'hr_operations.add' AND p.`guard_name` = 'web'
WHERE (u.`sid_hr` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'hr_operations.edit' AND p.`guard_name` = 'web'
WHERE (u.`sid_hr` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'hr_operations.delete' AND p.`guard_name` = 'web'
WHERE (u.`sid_hr` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'employee_operations.view' AND p.`guard_name` = 'web'
WHERE (u.`sid_hr` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'employee_operations.add' AND p.`guard_name` = 'web'
WHERE (u.`sid_hr` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'employee_operations.edit' AND p.`guard_name` = 'web'
WHERE (u.`sid_hr` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'employee_operations.delete' AND p.`guard_name` = 'web'
WHERE (u.`sid_hr` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'pulse.view' AND p.`guard_name` = 'web'
WHERE (u.`sid_hr` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'pulse.add' AND p.`guard_name` = 'web'
WHERE (u.`sid_hr` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'pulse.edit' AND p.`guard_name` = 'web'
WHERE (u.`sid_hr` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'pulse.delete' AND p.`guard_name` = 'web'
WHERE (u.`sid_hr` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'pulse_types.view' AND p.`guard_name` = 'web'
WHERE (u.`sid_hr` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'pulse_types.add' AND p.`guard_name` = 'web'
WHERE (u.`sid_hr` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'pulse_types.edit' AND p.`guard_name` = 'web'
WHERE (u.`sid_hr` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'pulse_types.delete' AND p.`guard_name` = 'web'
WHERE (u.`sid_hr` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'tasks.view' AND p.`guard_name` = 'web'
WHERE (u.`sid_payroll` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'tasks.add' AND p.`guard_name` = 'web'
WHERE (u.`sid_payroll` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'tasks.edit' AND p.`guard_name` = 'web'
WHERE (u.`sid_payroll` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'tasks.delete' AND p.`guard_name` = 'web'
WHERE (u.`sid_payroll` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'tasks.view_all' AND p.`guard_name` = 'web'
WHERE (u.`show_all_tasks` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'kpis.view' AND p.`guard_name` = 'web'
WHERE (u.`sid_payroll` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'kpis.add' AND p.`guard_name` = 'web'
WHERE (u.`sid_payroll` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'kpis.edit' AND p.`guard_name` = 'web'
WHERE (u.`sid_payroll` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'kpis.delete' AND p.`guard_name` = 'web'
WHERE (u.`sid_payroll` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'employee_kpis.view' AND p.`guard_name` = 'web'
WHERE (u.`sid_payroll` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'employee_kpis.add' AND p.`guard_name` = 'web'
WHERE (u.`sid_payroll` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'employee_kpis.edit' AND p.`guard_name` = 'web'
WHERE (u.`sid_payroll` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'employee_kpis.delete' AND p.`guard_name` = 'web'
WHERE (u.`sid_payroll` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'contracts.view' AND p.`guard_name` = 'web'
WHERE (u.`sid_payroll` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'contracts.add' AND p.`guard_name` = 'web'
WHERE (u.`sid_payroll` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'contracts.edit' AND p.`guard_name` = 'web'
WHERE (u.`sid_payroll` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'contracts.delete' AND p.`guard_name` = 'web'
WHERE (u.`sid_payroll` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'cvs.view' AND p.`guard_name` = 'web'
WHERE (u.`sid_payroll` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'cvs.add' AND p.`guard_name` = 'web'
WHERE (u.`sid_payroll` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'cvs.edit' AND p.`guard_name` = 'web'
WHERE (u.`sid_payroll` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'cvs.delete' AND p.`guard_name` = 'web'
WHERE (u.`sid_payroll` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, r.`id` FROM `roles` r JOIN `permissions` p ON p.`name` = 'requests.view' AND p.`guard_name` = 'web';

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, r.`id` FROM `roles` r JOIN `permissions` p ON p.`name` = 'requests.add' AND p.`guard_name` = 'web';

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, r.`id` FROM `roles` r JOIN `permissions` p ON p.`name` = 'requests.edit' AND p.`guard_name` = 'web';

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, r.`id` FROM `roles` r JOIN `permissions` p ON p.`name` = 'requests.delete' AND p.`guard_name` = 'web';

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'attendance.view' AND p.`guard_name` = 'web'
WHERE (u.`show_attandance` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'attendance.add' AND p.`guard_name` = 'web'
WHERE (u.`add_attandance` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'attendance.edit' AND p.`guard_name` = 'web'
WHERE (u.`edit_attandance` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'attendance.delete' AND p.`guard_name` = 'web'
WHERE (u.`delete_attandance` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'allowances.view' AND p.`guard_name` = 'web'
WHERE (u.`show_attandance` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'allowances.add' AND p.`guard_name` = 'web'
WHERE (u.`add_attandance` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'allowances.edit' AND p.`guard_name` = 'web'
WHERE (u.`edit_attandance` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'allowances.delete' AND p.`guard_name` = 'web'
WHERE (u.`delete_attandance` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'attendance.process' AND p.`guard_name` = 'web'
WHERE (u.`sid_payroll` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'production.view' AND p.`guard_name` = 'web'
WHERE (u.`sid_payroll` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'production.add' AND p.`guard_name` = 'web'
WHERE (u.`sid_payroll` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'production.edit' AND p.`guard_name` = 'web'
WHERE (u.`sid_payroll` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'production.delete' AND p.`guard_name` = 'web'
WHERE (u.`sid_payroll` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'payroll.view' AND p.`guard_name` = 'web'
WHERE (u.`sid_payroll` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'payroll.add' AND p.`guard_name` = 'web'
WHERE (u.`sid_payroll` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'payroll.edit' AND p.`guard_name` = 'web'
WHERE (u.`sid_payroll` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'payroll.delete' AND p.`guard_name` = 'web'
WHERE (u.`sid_payroll` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'crm_clients.view' AND p.`guard_name` = 'web'
WHERE (u.`show_advanced_clients` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'crm_clients.add' AND p.`guard_name` = 'web'
WHERE (u.`add_advanced_clients` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'crm_clients.edit' AND p.`guard_name` = 'web'
WHERE (u.`edit_advanced_clients` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'crm_clients.delete' AND p.`guard_name` = 'web'
WHERE (u.`delete_advanced_clients` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'client_profile.view' AND p.`guard_name` = 'web'
WHERE (u.`show_client_profile` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'client_profile.add' AND p.`guard_name` = 'web'
WHERE (u.`add_client_profile` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'client_profile.edit' AND p.`guard_name` = 'web'
WHERE (u.`edit_client_profile` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'client_profile.delete' AND p.`guard_name` = 'web'
WHERE (u.`delete_client_profile` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'chances.view' AND p.`guard_name` = 'web'
WHERE (u.`show_chances` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'chances.add' AND p.`guard_name` = 'web'
WHERE (u.`add_chances` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'chances.edit' AND p.`guard_name` = 'web'
WHERE (u.`edit_chances` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'chances.delete' AND p.`guard_name` = 'web'
WHERE (u.`delete_chances` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'calls.view' AND p.`guard_name` = 'web'
WHERE (u.`show_calls` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'calls.add' AND p.`guard_name` = 'web'
WHERE (u.`add_calls` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'calls.edit' AND p.`guard_name` = 'web'
WHERE (u.`edit_calls` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'calls.delete' AND p.`guard_name` = 'web'
WHERE (u.`delete_calls` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'reports.sales' AND p.`guard_name` = 'web'
WHERE (u.`sid_reports` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'reports.shift' AND p.`guard_name` = 'web'
WHERE (u.`sid_reports` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'reports.purchases' AND p.`guard_name` = 'web'
WHERE (u.`sid_reports` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'reports.accounting' AND p.`guard_name` = 'web'
WHERE (u.`sid_reports` = 1 OR u.`show_gl_reports` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'reports.rents' AND p.`guard_name` = 'web'
WHERE (u.`sid_reports` = 1 OR u.`show_rent_reports` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'reports.clinics' AND p.`guard_name` = 'web'
WHERE (u.`sid_clinics` = 1 OR u.`show_clinic_reports` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'reports.visits' AND p.`guard_name` = 'web'
WHERE (u.`sid_visits` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'reports.payroll' AND p.`guard_name` = 'web'
WHERE (u.`sid_payroll` = 1 OR u.`show_payroll_report` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'reports.hr' AND p.`guard_name` = 'web'
WHERE (u.`sid_hr` = 1 OR u.`show_hr_report` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'users.view' AND p.`guard_name` = 'web'
WHERE (u.`show_users` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'users.add' AND p.`guard_name` = 'web'
WHERE (u.`add_users` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'users.edit' AND p.`guard_name` = 'web'
WHERE (u.`edit_users` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'users.delete' AND p.`guard_name` = 'web'
WHERE (u.`delete_users` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'users.change_password' AND p.`guard_name` = 'web'
WHERE (u.`edit_user_passwords` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'users.waiter_codes' AND p.`guard_name` = 'web'
WHERE (u.`edit_users` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'roles.view' AND p.`guard_name` = 'web'
WHERE (u.`show_users` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'roles.add' AND p.`guard_name` = 'web'
WHERE (u.`add_users` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'roles.edit' AND p.`guard_name` = 'web'
WHERE (u.`edit_users` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, u.`id` FROM `usr_pwrs` u
JOIN `roles` r ON r.`id` = u.`id`
JOIN `permissions` p ON p.`name` = 'roles.delete' AND p.`guard_name` = 'web'
WHERE (u.`delete_users` = 1);

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, r.`id` FROM `roles` r JOIN `permissions` p ON p.`name` = 'settings.manage' AND p.`guard_name` = 'web' WHERE r.`id` = 1;

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, r.`id` FROM `roles` r JOIN `permissions` p ON p.`name` = 'system.manage' AND p.`guard_name` = 'web' WHERE r.`id` = 1;

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, r.`id` FROM `roles` r JOIN `permissions` p ON p.`name` = 'news.view' AND p.`guard_name` = 'web' WHERE r.`id` = 1;

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, r.`id` FROM `roles` r JOIN `permissions` p ON p.`name` = 'news.add' AND p.`guard_name` = 'web' WHERE r.`id` = 1;

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, r.`id` FROM `roles` r JOIN `permissions` p ON p.`name` = 'news.edit' AND p.`guard_name` = 'web' WHERE r.`id` = 1;

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, r.`id` FROM `roles` r JOIN `permissions` p ON p.`name` = 'news.delete' AND p.`guard_name` = 'web' WHERE r.`id` = 1;

-- قاعدة CRUD: أي دور معاه add / edit / delete لكيان ياخد view بتاعه
INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT v.`id`, x.`role_id`
FROM (
    SELECT DISTINCT rhp.`role_id`, SUBSTRING_INDEX(p.`name`, '.', 1) AS entity
    FROM `role_has_permissions` rhp
    JOIN `permissions` p ON p.`id` = rhp.`permission_id`
    WHERE p.`action` IN ('add', 'edit', 'delete')
) x
JOIN `permissions` v ON v.`name` = CONCAT(x.entity, '.view') AND v.`guard_name` = 'web';
