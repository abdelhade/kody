<?php
/**
 * فحص تغطية الصلاحيات (CLI بس):
 *   php tests/check_authorize.php
 *
 * - ملف PHP مش موجود في includes/rbac_routes.php
 * - ملف مش بيوصل لـ connect.php ولا rbac_guard.php (يعني الحماية مش بتشتغل عليه)
 * - صلاحية في الـ routes مش موجودة في الكتالوج
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

$root = dirname(__DIR__);
require_once $root . '/classes/Can.php';
require_once $root . '/tools/RbacLegacy.php';

$routes = require $root . '/includes/rbac_routes.php';
$known  = array_flip(array_column(RbacLegacy::flatten(), 'name'));

$files = [];
foreach (['', 'do/', 'do/start/', 'do/dbase/', 'ajax/', 'get/', 'api/', 'print/'] as $dir) {
    foreach (glob($root . '/' . $dir . '*.php') as $path) {
        $files[] = strtolower($dir . basename($path));
    }
}

// ملفات بتوصل لـ connect.php بطريق غير مباشر
$guardIncludes = '#(include|require)(_once)?\s*\(?[^;]*(connect|header|head|rbac_guard)\.php#i';

$unmapped = [];
$unguarded = [];
foreach ($files as $rel) {
    if (!isset($routes[$rel])) {
        $unmapped[] = $rel;
    }
    $rule = $routes[$rel] ?? null;
    if ($rule === 'public') {
        continue;
    }
    $src = (string) file_get_contents($root . '/' . $rel);
    // فاضي / HTML ثابت / UTF-16 (مش بيتنفّذ) / تحويل بس
    $inert = !str_contains($src, '<?php')
        || preg_match('/^<\?php\s*header\([^;]+\);\s*exit;\s*$/', trim($src));
    if (!$inert && !preg_match($guardIncludes, $src)) {
        $unguarded[] = $rel;
    }
}

$unknown = [];
$collect = static function ($rule) use (&$collect, $known, &$unknown, &$file): void {
    if (is_string($rule)) {
        if (!in_array($rule, ['public', 'login', 'custom'], true) && !isset($known[$rule])) {
            $unknown[] = $file . ' → ' . $rule;
        }
        return;
    }
    if (isset($rule['param'])) {
        foreach ($rule['map'] as $v) {
            $collect($v);
        }
        if (isset($rule['default'])) {
            $collect($rule['default']);
        }
        return;
    }
    foreach ($rule as $v) {
        $collect($v);
    }
};
foreach ($routes as $file => $rule) {
    $collect($rule);
}

$stale = array_diff(array_keys($routes), $files);

$report = [
    'ملفات مش موجودة في rbac_routes.php' => $unmapped,
    'ملفات الحماية مش بتشتغل عليها (مفيش connect.php / rbac_guard.php)' => $unguarded,
    'صلاحيات مش موجودة في الكتالوج' => $unknown,
    'مسارات في rbac_routes.php لملفات مش موجودة' => array_values($stale),
];
$problems = 0;
foreach ($report as $title => $list) {
    printf("%s: %d\n", $title, count($list));
    foreach ($list as $item) {
        echo '  - ', $item, PHP_EOL;
    }
    $problems += count($list);
}
exit($problems > 0 ? 1 : 0);
