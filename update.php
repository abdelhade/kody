<?php
require_once __DIR__ . '/includes/rbac_guard.php';
/**
 * @deprecated مسار التحديث اليدوي أُوقف.
 * استخدم: الإعدادات → قاعدة البيانات → «تحديث قاعدة البيانات»
 */
header('Location: setting.php');
exit;
