-- صنف مجمع + وصفة تصنيع + أمر تصنيع (بدون قيد محاسبي)

ALTER TABLE `myitems`
  ADD COLUMN `is_assembly` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'صنف مجمع له وصفة تصنيع';

CREATE TABLE IF NOT EXISTS `item_bom` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL COMMENT 'الصنف المجمع',
  `component_id` int(11) NOT NULL COMMENT 'الصنف الخام',
  `qty` double NOT NULL DEFAULT 0 COMMENT 'الكمية لكل وحدة واحدة من المنتج',
  `isdeleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_item_bom_parent` (`parent_id`),
  KEY `idx_item_bom_component` (`component_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `pro_tybes` (`id`, `pname`, `ptext`, `ptybe`, `info`)
SELECT 20, 'أمر تصنيع', '', 20, NULL
WHERE NOT EXISTS (SELECT 1 FROM `pro_tybes` WHERE `id` = 20);

INSERT IGNORE INTO `permissions` (`name`, `guard_name`, `label`, `group_key`, `group_name`, `entity_label`, `action`, `sort`, `created_at`, `updated_at`) VALUES
('manufacturing.view', 'web', 'عرض أوامر التصنيع', 'inventory', 'المخزون', 'التصنيع', 'view', 760, NOW(), NOW()),
('manufacturing.add', 'web', 'إضافة أوامر التصنيع', 'inventory', 'المخزون', 'التصنيع', 'add', 761, NOW(), NOW()),
('manufacturing.edit', 'web', 'تعديل أوامر التصنيع', 'inventory', 'المخزون', 'التصنيع', 'edit', 762, NOW(), NOW()),
('manufacturing.delete', 'web', 'حذف أوامر التصنيع', 'inventory', 'المخزون', 'التصنيع', 'delete', 763, NOW(), NOW());

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, 1
FROM `permissions` p
WHERE p.`name` IN (
    'manufacturing.view',
    'manufacturing.add',
    'manufacturing.edit',
    'manufacturing.delete'
)
AND p.`guard_name` = 'web'
AND EXISTS (SELECT 1 FROM `roles` WHERE `id` = 1);
