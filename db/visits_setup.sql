-- صلاحية عرض الزيارات في السايد بار (مستقلة عن نقاط البيع / sid_sales)
ALTER TABLE usr_pwrs ADD COLUMN sid_visits INT DEFAULT 1;
