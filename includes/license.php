<?php
/**
 * ترخيص النسخة مرتبط بعنوان MAC للجهاز.
 * المفتاح = ناتج معادلة ثابتة على الـ MAC، وتُفحص محلياً في الإعدادات.
 */

function kody_license_store_path(): string
{
    return dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'license.json';
}

function kody_normalize_mac(string $mac): ?string
{
    $hex = strtoupper(preg_replace('/[^0-9A-Fa-f]/', '', $mac) ?? '');
    if (strlen($hex) !== 12 || !ctype_xdigit($hex)) {
        return null;
    }
    if ($hex === '000000000000' || $hex === 'FFFFFFFFFFFF') {
        return null;
    }
    return $hex;
}

function kody_format_mac(string $hex12): string
{
    return implode('-', str_split($hex12, 2));
}

/**
 * معادلة المفتاح: HMAC-SHA256(MAC) ثم 16 خانة تُقسَّم أربع مجموعات.
 */
function kody_license_key_from_mac(string $mac): ?string
{
    $hex = kody_normalize_mac($mac);
    if ($hex === null) {
        return null;
    }
    $raw = strtoupper(substr(hash_hmac('sha256', $hex, 'KODY2-LIC-EQ-7F3A'), 0, 16));
    return implode('-', str_split($raw, 4));
}

function kody_normalize_license_key(string $key): string
{
    $key = strtoupper(trim($key));
    $key = preg_replace('/[^0-9A-F]/', '', $key) ?? '';
    if (strlen($key) !== 16) {
        return '';
    }
    return implode('-', str_split($key, 4));
}

/**
 * @return list<array{mac:string,name:string,adapter:string}>
 */
function kody_mac_candidates(): array
{
    $rows = [];
    if (DIRECTORY_SEPARATOR === '\\') {
        $out = function_exists('shell_exec') ? (string) @shell_exec('getmac /V /FO CSV /NH') : '';
        if ($out !== '') {
            foreach (preg_split('/\R/', $out) as $line) {
                $line = trim($line);
                if ($line === '' || stripos($line, 'Physical Address') !== false) {
                    continue;
                }
                $cols = str_getcsv($line);
                if (count($cols) < 3) {
                    continue;
                }
                $rows[] = [
                    'name' => trim((string) $cols[0]),
                    'adapter' => trim((string) $cols[1]),
                    'mac' => trim((string) $cols[2]),
                ];
            }
        }
    } else {
        foreach (glob('/sys/class/net/*', GLOB_ONLYDIR) ?: [] as $dir) {
            $name = basename($dir);
            if ($name === 'lo') {
                continue;
            }
            $addr = @file_get_contents($dir . '/address');
            if ($addr === false) {
                continue;
            }
            $rows[] = [
                'name' => $name,
                'adapter' => $name,
                'mac' => trim($addr),
            ];
        }
    }
    return $rows;
}

function kody_machine_mac(): ?string
{
    $skip = '/virtual|vmware|hyper-v|vethernet|vbox|virtualbox|bluetooth|loopback|miniport|vpn|wsl|docker|npcap|pseudo/i';
    $preferred = [];
    $rest = [];
    foreach (kody_mac_candidates() as $row) {
        $hex = kody_normalize_mac($row['mac']);
        if ($hex === null) {
            continue;
        }
        $label = $row['name'] . ' ' . $row['adapter'];
        if (preg_match($skip, $label)) {
            continue;
        }
        $shown = kody_format_mac($hex);
        if (preg_match('/ethernet|wi-?fi|wlan|local area|enp|eth|wlan/i', $row['name'])) {
            $preferred[] = $shown;
        } else {
            $rest[] = $shown;
        }
    }
    return $preferred[0] ?? $rest[0] ?? null;
}

function kody_stored_license_key(): string
{
    $path = kody_license_store_path();
    if (!is_readable($path)) {
        return '';
    }
    $data = json_decode((string) file_get_contents($path), true);
    if (!is_array($data)) {
        return '';
    }
    return kody_normalize_license_key((string) ($data['key'] ?? ''));
}

/**
 * @return array{licensed:bool,mac:?string,key:string}
 */
function kody_license_status(): array
{
    $mac = kody_machine_mac();
    $stored = kody_stored_license_key();
    $expected = $mac !== null ? kody_license_key_from_mac($mac) : null;
    $licensed = $expected !== null && $stored !== '' && hash_equals($expected, $stored);
    return [
        'licensed' => $licensed,
        'mac' => $mac,
        'key' => $licensed ? $stored : '',
    ];
}

/**
 * @return array{success:bool,message:string,licensed:bool}
 */
function kody_license_activate(string $key): array
{
    $mac = kody_machine_mac();
    if ($mac === null) {
        return ['success' => false, 'message' => 'تعذر قراءة عنوان MAC لهذا الجهاز', 'licensed' => false];
    }
    $normalized = kody_normalize_license_key($key);
    $expected = kody_license_key_from_mac($mac);
    if ($normalized === '' || $expected === null || !hash_equals($expected, $normalized)) {
        return ['success' => false, 'message' => 'مفتاح الترخيص لا يطابق هذا الجهاز', 'licensed' => false];
    }

    $path = kody_license_store_path();
    $dir = dirname($path);
    if (!is_dir($dir) && !@mkdir($dir, 0755, true) && !is_dir($dir)) {
        return ['success' => false, 'message' => 'تعذر حفظ ملف الترخيص', 'licensed' => false];
    }
    $written = @file_put_contents($path, json_encode([
        'key' => $normalized,
        'mac' => $mac,
        'licensed_at' => date('Y-m-d H:i:s'),
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    if ($written === false) {
        return ['success' => false, 'message' => 'تعذر حفظ ملف الترخيص', 'licensed' => false];
    }
    return ['success' => true, 'message' => 'النسخة مرخصة', 'licensed' => true];
}
