-- الطيار (مندوب التوصيل) على رأس الفاتورة
ALTER TABLE `ot_head`
  ADD COLUMN `delivery_person_id` INT(11) DEFAULT NULL COMMENT 'معرف الطيار/مندوب التوصيل' AFTER `emp2_id`;
