<?php
include('includes/connect.php');

$sql = file_get_contents('db/pulse_setup.sql');
$queries = explode(';', $sql);

$skipErrors = [
    'Duplicate column',
    'already exists',
    'Duplicate entry',
];

foreach ($queries as $query) {
    $lines = array_filter(array_map('trim', explode("\n", $query)), function ($line) {
        return $line !== '' && !str_starts_with($line, '--');
    });
    $query = trim(implode("\n", $lines));
    if (empty($query)) {
        continue;
    }

    if (stripos($query, 'INSERT INTO pulse_types') !== false) {
        $countRes = $conn->query('SELECT COUNT(*) AS cnt FROM pulse_types');
        if ($countRes && (int)$countRes->fetch_assoc()['cnt'] > 0) {
            echo "SKIP (data exists): pulse_types default rows\n";
            continue;
        }
    }

    $summary = substr(preg_replace('/\s+/', ' ', $query), 0, 60);

    try {
        $conn->query($query);
        echo "OK: {$summary}...\n";
    } catch (mysqli_sql_exception $e) {
        $msg = $e->getMessage();
        $skip = false;
        foreach ($skipErrors as $needle) {
            if (str_contains($msg, $needle)) {
                $skip = true;
                break;
            }
        }
        if ($skip) {
            echo "SKIP (already exists): {$summary}...\n";
        } else {
            echo "Error: {$msg}\n  Query: " . substr($query, 0, 80) . "...\n";
        }
    }
}

echo "Done.\n";
$conn->close();
