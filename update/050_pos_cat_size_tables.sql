-- حجم أزرار التصنيفات في نقاط البيع (مطعم باركود/طاولات + ملابس)
ALTER TABLE `settings`
  ADD COLUMN `pos_cat_size_tables` VARCHAR(10) NOT NULL DEFAULT 'medium' COMMENT 'حجم أزرار التصنيفات في POS المطعم والملابس';
