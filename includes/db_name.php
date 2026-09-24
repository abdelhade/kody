<?php
/**
 * Resolve which MySQL database the app should use.
 * Order: session → db_registry.json current → .env DB_NAME
 * If $conn is provided, picks the first candidate that actually exists.
 */
function kody_db_candidates(): array
{
    $candidates = [];

    if (session_status() === PHP_SESSION_ACTIVE
        && !empty($_SESSION['active_dbname'])
        && preg_match('/^[A-Za-z0-9_]{2,64}$/', (string) $_SESSION['active_dbname'])
    ) {
        $candidates[] = (string) $_SESSION['active_dbname'];
    }

    $registryFile = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'db_registry.json';
    if (is_readable($registryFile)) {
        $reg = json_decode((string) file_get_contents($registryFile), true);
        if (is_array($reg) && !empty($reg['current'])
            && preg_match('/^[A-Za-z0-9_]{2,64}$/', (string) $reg['current'])
        ) {
            $candidates[] = (string) $reg['current'];
        }
    }

    $envName = function_exists('env') ? (string) env('DB_NAME', 'kody2') : 'kody2';
    if (preg_match('/^[A-Za-z0-9_]{2,64}$/', $envName)) {
        $candidates[] = $envName;
    }

    return array_values(array_unique($candidates));
}

/**
 * @return string|null selected db name, or null if none exist
 */
function kody_select_existing_db(mysqli $conn): ?string
{
    foreach (kody_db_candidates() as $name) {
        if (@$conn->select_db($name)) {
            if (session_status() === PHP_SESSION_ACTIVE) {
                $_SESSION['active_dbname'] = $name;
            }
            return $name;
        }
    }

    // Stale session name pointing at a missing DB — clear it
    if (session_status() === PHP_SESSION_ACTIVE && isset($_SESSION['active_dbname'])) {
        unset($_SESSION['active_dbname']);
    }

    return null;
}

function kody_preferred_dbname(): string
{
    $candidates = kody_db_candidates();
    return $candidates[0] ?? 'kody2';
}

/**
 * Validate MySQL database name: letters, digits, underscore — length 2–64.
 *
 * @return string validated name
 * @throws InvalidArgumentException
 */
function kody_validate_dbname(string $name): string
{
    $name = trim($name);
    if ($name === '') {
        throw new InvalidArgumentException('أدخل اسم قاعدة البيانات');
    }
    if (!preg_match('/^[A-Za-z0-9_]{2,64}$/', $name)) {
        throw new InvalidArgumentException('اسم القاعدة غير صالح (حروف إنجليزية/أرقام/_ فقط، من 2 إلى 64 حرفاً)');
    }
    // Avoid accidental system schemas
    $reserved = ['mysql', 'information_schema', 'performance_schema', 'sys'];
    if (in_array(strtolower($name), $reserved, true)) {
        throw new InvalidArgumentException('لا يمكن استخدام اسم محجوز للنظام');
    }
    return $name;
}

/**
 * Ensure restored/created DB appears in period registry and becomes current.
 */
function kody_register_dbname(string $dbName, string $label = ''): void
{
    $dbName = kody_validate_dbname($dbName);
    $label = $label !== '' ? $label : $dbName;
    $registryFile = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'db_registry.json';
    $dir = dirname($registryFile);
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }

    $reg = [
        'group_id' => 'default',
        'current' => $dbName,
        'databases' => [],
    ];
    if (is_readable($registryFile)) {
        $decoded = json_decode((string) file_get_contents($registryFile), true);
        if (is_array($decoded)) {
            $reg = array_merge($reg, $decoded);
            if (empty($reg['databases']) || !is_array($reg['databases'])) {
                $reg['databases'] = [];
            }
        }
    }

    $names = array_column($reg['databases'], 'name');
    if (!in_array($dbName, $names, true)) {
        $reg['databases'][] = [
            'name' => $dbName,
            'label' => $label,
            'created_at' => date('Y-m-d H:i:s'),
            'closed_at' => null,
            'parent' => null,
        ];
    }
    $reg['current'] = $dbName;

    @file_put_contents(
        $registryFile,
        json_encode($reg, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
    );

    if (session_status() === PHP_SESSION_ACTIVE) {
        $_SESSION['active_dbname'] = $dbName;
    }
}
