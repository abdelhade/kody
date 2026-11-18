-- حجم أيقونات الأصناف في نقطة البيع (ملابس / باركود المطعم)
ALTER TABLE `settings`
  ADD COLUMN `pos_icon_size_barcode` VARCHAR(10) NOT NULL DEFAULT 'medium' COMMENT 'حجم أيقونات POS باركود المطعم',
  ADD COLUMN `pos_icon_size_clothes` VARCHAR(10) NOT NULL DEFAULT 'medium' COMMENT 'حجم أيقونات POS الملابس';
