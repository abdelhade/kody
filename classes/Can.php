<?php
/**
 * الصلاحيات: كل مستخدم له دور (users.userrole)، وكل دور له صلاحيات
 * (roles / permissions / role_has_permissions).
 *
 * صلاحيات الدور بتتحمّل مرة واحدة وقت الدخول في $_SESSION['auth']، فتعديل صلاحيات دور
 * بيسري على المستخدم بعد ما يسجّل دخول تاني. لو قاعدة البيانات اتغيّرت في نفس الـ session
 * (تبديل المدة المالية) الصلاحيات بتتحمّل تاني من القاعدة الجديدة.
 */
final class Can
{
    private const KEY = 'auth';

    /** المجموعات اللي الرفض فيها بيتطبّق دايماً حتى لو AUTHZ_ENFORCE = 0 */
    private const ALWAYS_ENFORCED = ['users.', 'roles.', 'settings.', 'system.', 'news.'];

    /** الإعدادات والنظام بيطلبوا باسورد ثابت، وبيفضلوا مفتوحين المدة دي (ثواني) */
    private const SETTINGS_LOCK_PREFIXES = ['settings.', 'system.'];
    private const SETTINGS_UNLOCK_PASSWORD = 'hadi@1234';
    private const SETTINGS_UNLOCK_TTL = 1800;
    /** setting.php هي اللي بتعرض فورم الباسورد، والنسخة الاحتياطية من الناف بار */
    private const SETTINGS_LOCK_EXEMPT = ['setting.php', 'do/dobackup.php'];

    /** باسورد المستخدم الحالي (password_hash أو md5 القديم) */
    public static function verifyPassword(mysqli $conn, string $password): bool
    {
        $uid = self::userId();
        if ($uid < 1 || $password === '') {
            return false;
        }
        $stmt = $conn->prepare('SELECT `password` FROM `users` WHERE `id` = ? LIMIT 1');
        $stmt->bind_param('i', $uid);
        $stmt->execute();
        $hash = (string) ($stmt->get_result()->fetch_row()[0] ?? '');
        $stmt->close();
        return $hash !== '' && (password_verify($password, $hash) || (strlen($hash) === 32 && hash_equals($hash, md5($password))));
    }

    public static function settingsUnlocked(): bool
    {
        $u = $_SESSION['settings_unlock'] ?? null;
        return is_array($u) && ($u['user_id'] ?? 0) === self::userId() && ($u['until'] ?? 0) > time();
    }

    public static function unlockSettings(string $password): bool
    {
        if ($password === '' || !hash_equals(self::SETTINGS_UNLOCK_PASSWORD, $password)) {
            self::log(sprintf('settings unlock failed user=%d', self::userId()));
            return false;
        }
        $_SESSION['settings_unlock'] = ['user_id' => self::userId(), 'until' => time() + self::SETTINGS_UNLOCK_TTL];
        return true;
    }

    public static function login(mysqli $conn, int $userId, string $db): bool
    {
        self::forget();

        $stmt = $conn->prepare('SELECT `userrole`, `isdeleted` FROM `users` WHERE `id` = ? LIMIT 1');
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$user || (int) $user['isdeleted'] === 1) {
            return false;
        }

        $roleId = (int) $user['userrole'];

        $_SESSION[self::KEY] = [
            'user_id' => $userId,
            'role_id' => $roleId,
            'db'      => $db,
            'perms'   => self::loadRolePerms($conn, $roleId),
        ];
        return true;
    }

    /**
     * صلاحية لمستخدم تاني غير اللي مسجّل (زي الويتر اللي شغّال على الكاشير بـ pos_user_id).
     * يكفي أي صلاحية من اللي اتبعتت.
     */
    public static function userCan(mysqli $conn, int $userId, string ...$permissions): bool
    {
        if ($userId === self::userId()) {
            return self::any(...$permissions);
        }
        $stmt = $conn->prepare('SELECT `userrole`, `isdeleted` FROM `users` WHERE `id` = ? LIMIT 1');
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if (!$user || (int) $user['isdeleted'] === 1) {
            return false;
        }
        $perms = self::loadRolePerms($conn, (int) $user['userrole']);
        foreach ($permissions as $p) {
            if (isset($perms[$p])) {
                return true;
            }
        }
        return false;
    }

    /**
     * صلاحيات الدور من roles / role_has_permissions. دور مش موجود أو متعطّل = مفيش صلاحيات.
     * لو الجداول مش موجودة (القاعدة ما خدتش التحديث): الدور 1 بياخد الإعدادات والنظام بس عشان يشغّل التحديث، والباقي مفيش.
     *
     * @return array<string, string>
     */
    private static function loadRolePerms(mysqli $conn, int $roleId): array
    {
        if ($roleId < 1) {
            return [];
        }
        try {
            $stmt = $conn->prepare('SELECT `is_active` FROM `roles` WHERE `id` = ? LIMIT 1');
            $stmt->bind_param('i', $roleId);
            $stmt->execute();
            $role = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            if (!$role) {
                self::log(sprintf('role %d not found in roles', $roleId));
                return [];
            }
            if ((int) $role['is_active'] !== 1) {
                return [];
            }

            $stmt = $conn->prepare(
                'SELECT p.`name`, p.`group_key`
                 FROM `role_has_permissions` rhp
                 JOIN `permissions` p ON p.`id` = rhp.`permission_id`
                 WHERE rhp.`role_id` = ?'
            );
            $stmt->bind_param('i', $roleId);
            $stmt->execute();
            $res = $stmt->get_result();
            $perms = [];
            while ($row = $res->fetch_assoc()) {
                $perms[$row['name']] = (string) $row['group_key'];
            }
            $stmt->close();
            return $perms;
        } catch (mysqli_sql_exception $e) {
            self::log('rbac tables missing — run database update: ' . $e->getMessage());
            return $roleId === 1 ? ['settings.manage' => 'system', 'system.manage' => 'system'] : [];
        }
    }

    /**
     * بيتنادى مع كل request من connect.php: يتأكد إن صلاحيات الـ session تخص المستخدم وقاعدة البيانات الحالية.
     */
    public static function boot(mysqli $conn, string $db): void
    {
        $userId = (int) ($_SESSION['userid'] ?? 0);
        if ($userId < 1) {
            self::forget();
            return;
        }
        $auth = $_SESSION[self::KEY] ?? null;
        if (is_array($auth) && $auth['user_id'] === $userId && $auth['db'] === $db) {
            return;
        }
        if (!self::login($conn, $userId, $db)) {
            self::logout();
        }
    }

    public static function check(string $permission): bool
    {
        $auth = $_SESSION[self::KEY] ?? null;
        if (!is_array($auth)) {
            return false;
        }
        return isset($auth['perms'][$permission]);
    }

    public static function any(string ...$permissions): bool
    {
        foreach ($permissions as $p) {
            if (self::check($p)) {
                return true;
            }
        }
        return false;
    }

    /** أي صلاحية جوه مجموعة (قسم في القائمة الجانبية) */
    public static function anyIn(string ...$groupKeys): bool
    {
        $auth = $_SESSION[self::KEY] ?? null;
        if (!is_array($auth)) {
            return false;
        }
        foreach ($auth['perms'] as $group) {
            if (in_array($group, $groupKeys, true)) {
                return true;
            }
        }
        return false;
    }

    public static function isLoggedIn(): bool
    {
        return isset($_SESSION[self::KEY]);
    }

    public static function userId(): int
    {
        return (int) ($_SESSION[self::KEY]['user_id'] ?? 0);
    }

    public static function roleId(): int
    {
        return (int) ($_SESSION[self::KEY]['role_id'] ?? 0);
    }

    /** يكفي أي صلاحية من اللي اتبعتت */
    public static function authorize(string ...$permissions): void
    {
        if (!self::isLoggedIn()) {
            self::respond(401, 'يجب تسجيل الدخول أولاً');
        }
        if (self::any(...$permissions)) {
            return;
        }
        self::deny($permissions);
    }

    public static function requireLogin(): void
    {
        if (!self::isLoggedIn()) {
            self::respond(401, 'يجب تسجيل الدخول أولاً');
        }
    }

    /**
     * حماية الصفحة الحالية حسب includes/rbac_routes.php. بيتنادى مرة واحدة لكل request من connect.php.
     */
    public static function guardRoute(): void
    {
        static $done = false;
        if ($done || PHP_SAPI === 'cli') {
            return;
        }
        $done = true;

        $root   = str_replace('\\', '/', strtolower(dirname(__DIR__))) . '/';
        $file   = str_replace('\\', '/', strtolower((string) realpath($_SERVER['SCRIPT_FILENAME'] ?? '')));
        if ($file === '' || !str_starts_with($file, $root)) {
            return;
        }
        $script = substr($file, strlen($root));

        $routes = require dirname(__DIR__) . '/includes/rbac_routes.php';
        $rule   = $routes[$script] ?? null;

        if ($rule === null) {
            if (preg_match('#^(do|ajax|get|api|print)/#', $script)) {
                self::log('unmapped endpoint ' . $script);
                self::requireLogin();
                self::deny(['unmapped:' . $script]);
                return;
            }
            self::log('unmapped page ' . $script);
            self::requireLogin();
            return;
        }

        $needed = self::resolveRule($rule);
        if ($needed === 'public') {
            return;
        }
        self::requireLogin();
        // custom: الملف نفسه بيحدد الصلاحية بعد ما يعرف نوع العملية
        if ($needed === 'login' || $needed === 'custom') {
            return;
        }
        self::authorize(...(array) $needed);

        if (!in_array($script, self::SETTINGS_LOCK_EXEMPT, true) && self::isSettingsRule((array) $needed) && !self::settingsUnlocked()) {
            self::respond(403, 'الإعدادات مقفولة — افتح صفحة الإعدادات واكتب كلمة المرور الأول');
        }
    }

    /** كل الصلاحيات المطلوبة من الإعدادات / النظام */
    private static function isSettingsRule(array $needed): bool
    {
        foreach ($needed as $p) {
            $hit = false;
            foreach (self::SETTINGS_LOCK_PREFIXES as $prefix) {
                $hit = $hit || str_starts_with($p, $prefix);
            }
            if (!$hit) {
                return false;
            }
        }
        return $needed !== [];
    }

    /**
     * @param string|list<string>|array{param:string, from?:string, map:array<string, string|list<string>>, default?:string|list<string>} $rule
     * @return string|list<string>
     */
    private static function resolveRule(string|array $rule, ?array $get = null, ?array $post = null): string|array
    {
        if (!isset($rule['param'])) {
            return $rule;
        }
        $get  ??= $_GET;
        $post ??= $_POST;
        $source = match ($rule['from'] ?? 'get') {
            'post'    => $post,
            'request' => $get + $post,
            default   => $get,
        };
        $value = (string) ($source[$rule['param']] ?? '');
        foreach ($rule['map'] as $key => $needed) {
            if ((string) $key === $value) {
                return $needed;
            }
        }
        if ($value !== '' && isset($rule['prefix'])) {
            foreach ($rule['map'] as $key => $needed) {
                if (str_starts_with($value, (string) $key)) {
                    return $needed;
                }
            }
        }
        return $rule['default'] ?? 'login';
    }

    /**
     * هل المستخدم يقدر يفتح الرابط ده؟ (لإخفاء روابط القائمة) — نفس قواعد guardRoute.
     * الرابط نسبي من جذر المشروع، زي acc_report.php?acc=clients
     */
    public static function canVisit(string $href): bool
    {
        static $routes = null;
        $routes ??= require dirname(__DIR__) . '/includes/rbac_routes.php';

        $path = strtolower(ltrim((string) parse_url($href, PHP_URL_PATH), '/'));
        if ($path === '' || !str_ends_with($path, '.php')) {
            return true;
        }
        $rule = $routes[$path] ?? null;
        if ($rule === null) {
            return self::isLoggedIn();
        }
        parse_str((string) parse_url($href, PHP_URL_QUERY), $query);
        $needed = self::resolveRule($rule, $query, []);
        return match ($needed) {
            'public' => true,
            'login', 'custom' => self::isLoggedIn(),
            default => self::any(...(array) $needed),
        };
    }

    /**
     * يشيل من HTML القائمة أي li.nav-item رابطه مش مسموح، وبعدين أي قسم فضي.
     * لو الـ HTML ما اتقراش يرجع زي ما هو (الصفحات نفسها محمية بـ guardRoute).
     */
    public static function filterMenu(string $html): string
    {
        if (trim($html) === '') {
            return $html;
        }
        $doc = new DOMDocument();
        $prev = libxml_use_internal_errors(true);
        $ok = $doc->loadHTML('<?xml encoding="utf-8"?><div id="__menu">' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($prev);
        $root = $doc->getElementById('__menu');
        if (!$ok || $root === null) {
            return $html;
        }
        $xp = new DOMXPath($doc);
        $hasClass = "contains(concat(' ', normalize-space(@class), ' '), ' nav-item ')";

        foreach (iterator_to_array($xp->query("//li[$hasClass]/a[@href]")) as $a) {
            $href = trim($a->getAttribute('href'));
            if ($href !== '' && ($href[0] === '#' || str_starts_with($href, 'javascript:') || preg_match('~^[a-z]+://~i', $href))) {
                continue;
            }
            // رابط فاضي = صفحة مش موجودة
            if ($href === '' || !self::canVisit($href)) {
                $li = $a->parentNode;
                $li->parentNode?->removeChild($li);
            }
        }
        // الأقسام اللي كل روابطها اتشالت (من الأعمق للأعلى)
        do {
            $removed = 0;
            foreach (iterator_to_array($xp->query("//li[$hasClass][ul]")) as $li) {
                if ($xp->query(".//li[$hasClass]", $li)->length === 0) {
                    $li->parentNode?->removeChild($li);
                    $removed++;
                }
            }
        } while ($removed > 0);

        $out = '';
        foreach ($root->childNodes as $child) {
            $out .= $doc->saveHTML($child);
        }
        return $out;
    }

    /**
     * كيان الحساب من كوده (الأطول الأول): 122… عملاء، 211… موردين ...
     * الحسابات اللي مش تحت كيان معروف تابعة لدليل الحسابات.
     */
    public const ACCOUNT_ENTITIES = [
        '122' => 'clients',
        '211' => 'suppliers',
        '121' => 'funds',
        '124' => 'banks',
        '123' => 'stores',
        '125' => 'debtors',
        '126' => 'delivery',
        '212' => 'creditors',
        '213' => 'employees',
        '221' => 'partners',
        '112' => 'rentables',
        '44'  => 'expenses',
        '32'  => 'revenues',
        '11'  => 'assets',
    ];

    /** الصلاحية المطلوبة لفعل (view/add/edit/delete) على حساب بالكود ده */
    public static function accountPermission(string $code, string $action): string
    {
        foreach (self::ACCOUNT_ENTITIES as $prefix => $entity) {
            if (str_starts_with($code, (string) $prefix)) {
                return $entity . '.' . $action;
            }
        }
        return 'accounts.chart';
    }

    /** نفس accountPermission بس بـ id الحساب من acc_head */
    public static function authorizeAccountId(mysqli $conn, int $accountId, string $action): void
    {
        $stmt = $conn->prepare('SELECT `code` FROM `acc_head` WHERE `id` = ? LIMIT 1');
        $stmt->bind_param('i', $accountId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        self::authorize(self::accountPermission((string) ($row['code'] ?? ''), $action));
    }

    /**
     * رفض الوصول. لو AUTHZ_ENFORCE = 0 الرفض بيتسجّل بس (فترة انتقالية)،
     * ما عدا المستخدمين والأدوار والإعدادات والنظام: دول بيترفضوا دايماً.
     *
     * @param list<string> $permissions
     */
    public static function deny(array $permissions): void
    {
        $script = $_SERVER['SCRIPT_NAME'] ?? '';
        self::log(sprintf(
            'deny user=%d role=%d need=[%s] script=%s query=%s',
            self::userId(), self::roleId(), implode(',', $permissions), $script, $_SERVER['QUERY_STRING'] ?? ''
        ));

        if (!self::enforcing($permissions)) {
            return;
        }
        self::respond(403, 'ليس لديك صلاحية لتنفيذ هذا الإجراء');
    }

    public static function logout(): void
    {
        $_SESSION = [];
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }

    private static function forget(): void
    {
        unset($_SESSION[self::KEY]);
    }

    /** @param list<string> $permissions */
    private static function enforcing(array $permissions): bool
    {
        $flag = function_exists('env') ? env('AUTHZ_ENFORCE', '0') : '0';
        if ((string) $flag === '1') {
            return true;
        }
        foreach ($permissions as $p) {
            foreach (self::ALWAYS_ENFORCED as $prefix) {
                if (str_starts_with($p, $prefix)) {
                    return true;
                }
            }
        }
        return false;
    }

    private static function respond(int $code, string $message): never
    {
        $script = $_SERVER['SCRIPT_NAME'] ?? '';
        $wantsJson = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
            || str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json')
            || preg_match('#/(ajax|api|get)/#', $script);

        if (!headers_sent()) {
            http_response_code($code);
        }

        if ($wantsJson) {
            if (!headers_sent()) {
                header('Content-Type: application/json; charset=utf-8');
            }
            echo json_encode(['success' => false, 'error' => $code === 401 ? 'unauthorized' : 'forbidden', 'message' => $message], JSON_UNESCAPED_UNICODE);
            exit;
        }

        if ($code === 401 && !headers_sent()) {
            header('Location: ' . self::baseUrl() . 'index.php');
            exit;
        }

        $home = htmlspecialchars(self::baseUrl() . 'dashboard.php', ENT_QUOTES, 'UTF-8');
        $msg  = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
        echo '<!DOCTYPE html><html lang="ar" dir="rtl"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">'
            . '<title>غير مسموح</title><style>body{font-family:Tahoma,sans-serif;background:#f6f7fb;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0}'
            . '.box{background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:32px 28px;text-align:center;max-width:420px;margin:16px}'
            . 'h1{color:#dc2626;font-size:22px;margin:0 0 10px}a{display:inline-block;margin-top:16px;color:#fff;background:#2563eb;padding:8px 18px;border-radius:8px;text-decoration:none}</style></head>'
            . '<body><div class="box"><h1>غير مسموح</h1><p>' . $msg . '</p><a href="' . $home . '">الرجوع للرئيسية</a></div></body></html>';
        exit;
    }

    private static function baseUrl(): string
    {
        $script = $_SERVER['SCRIPT_NAME'] ?? '';
        return preg_match('#/(do|ajax|api|get|print)(/[^/]+)?/[^/]+$#', $script, $m)
            ? str_repeat('../', isset($m[2]) && $m[2] !== '' ? 2 : 1)
            : '';
    }

    private static function log(string $line): void
    {
        $dir = dirname(__DIR__) . '/logs';
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        @file_put_contents($dir . '/authz.log', '[' . date('Y-m-d H:i:s') . '] ' . $line . PHP_EOL, FILE_APPEND);
    }
}
