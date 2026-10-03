<?php
// ajax/git_pull.php
header('Content-Type: application/json');

$envPath = __DIR__ . '/../.env';
$gitUrl = '';

if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        $parts = explode('=', $line, 2);
        if (count($parts) === 2 && trim($parts[0]) === 'GIT_PULL_URL') {
            $gitUrl = trim($parts[1]);
            break;
        }
    }
}

$command = "git pull";
if ($gitUrl !== '') {
    $command .= ' ' . escapeshellarg($gitUrl);
}
$command .= ' 2>&1'; // to capture output

exec($command, $output, $return_var);

echo json_encode([
    'success' => $return_var === 0,
    'output' => implode("\n", $output),
    'url_used' => $gitUrl,
    'command' => $command
]);
