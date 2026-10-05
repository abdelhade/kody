<?php

/**
 * مسار حفظ النسخ الاحتياطية من الإعدادات، أو مجلد BACKUP الافتراضي.
 */
function kody_default_backup_directory(): string
{
    return dirname(__DIR__) . DIRECTORY_SEPARATOR . 'BACKUP';
}

function kody_ensure_backup_path_column(mysqli $conn): void
{
    $col = $conn->query("SHOW COLUMNS FROM settings LIKE 'backup_path'");
    if ($col && $col->num_rows > 0) {
        return;
    }
    try {
        $conn->query("ALTER TABLE `settings` ADD COLUMN `backup_path` VARCHAR(500) NOT NULL DEFAULT ''");
    } catch (mysqli_sql_exception $e) {
        if (stripos($e->getMessage(), 'Duplicate column') === false) {
            throw $e;
        }
    }
}

function kody_normalize_backup_path(string $path): string
{
    $path = trim(str_replace(["\0", "\r", "\n"], '', $path));
    if ($path === '') {
        return '';
    }
    $path = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
    $path = rtrim($path, DIRECTORY_SEPARATOR);
    $isAbsolute = (bool) preg_match('/^[A-Za-z]:' . preg_quote(DIRECTORY_SEPARATOR, '/') . '/', $path)
        || str_starts_with($path, DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR)
        || str_starts_with($path, DIRECTORY_SEPARATOR);
    if (!$isAbsolute) {
        return '';
    }
    $parts = explode(DIRECTORY_SEPARATOR, $path);
    foreach ($parts as $part) {
        if ($part === '..') {
            return '';
        }
    }
    return $path;
}

function kody_backup_directory(mysqli $conn): string
{
    $configured = '';
    $col = $conn->query("SHOW COLUMNS FROM settings LIKE 'backup_path'");
    if ($col && $col->num_rows > 0) {
        $res = $conn->query('SELECT backup_path FROM settings LIMIT 1');
        if ($res && ($row = $res->fetch_assoc())) {
            $configured = kody_normalize_backup_path((string) ($row['backup_path'] ?? ''));
        }
    }

    $dir = $configured !== '' ? $configured : kody_default_backup_directory();
    if (!is_dir($dir) && !@mkdir($dir, 0755, true) && !is_dir($dir)) {
        throw new RuntimeException('تعذر إنشاء مجلد النسخة الاحتياطية: ' . $dir);
    }
    if (!is_writable($dir)) {
        throw new RuntimeException('مجلد النسخة الاحتياطية غير قابل للكتابة: ' . $dir);
    }

    return $dir;
}
