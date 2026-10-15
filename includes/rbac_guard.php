<?php
/**
 * حماية الملفات اللي مش بتستخدم connect.php (ليها اتصال خاص أو مش محتاجة قاعدة بيانات).
 * أول سطر في الملف: require_once __DIR__ . '/../includes/rbac_guard.php';
 *
 * لو صلاحيات الـ session لسه ما اتحمّلتش (دخول قديم) بيحمّل connect.php اللي بيعمل boot + guard.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/load_env.php';
require_once __DIR__ . '/../classes/Can.php';

if (!Can::isLoggedIn() && !empty($_SESSION['userid'])) {
    require_once __DIR__ . '/connect.php';
} else {
    Can::guardRoute();
}
