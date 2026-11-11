-- session_time.id من غير AUTO_INCREMENT فتسجيل الدخول يدرج 0 ويتكرر المفتاح الأساسي
ALTER TABLE `session_time`
  MODIFY `id` INT NOT NULL AUTO_INCREMENT;
