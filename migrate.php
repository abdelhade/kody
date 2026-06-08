<?php
/**
 * CLI Database Migrations Runner
 * 
 * Run from command line:
 * - php migrate.php (runs pending migrations)
 * - php migrate.php rollback (rolls back the latest batch)
 * - php migrate.php status (displays all migrations and status)
 * - php migrate.php make [name] [php|sql] (creates a new migration stub)
 * 
 * Date: 2026-06-09
 */

if (php_sapi_name() !== 'cli') {
    header("HTTP/1.1 403 Forbidden");
    echo "<h1>403 Forbidden</h1>";
    echo "<p>This script can only be run from the command line. If you want a web-based migration runner, please open <a href='migrations_ui.php'>migrations_ui.php</a> instead.</p>";
    exit(1);
}

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Locate and require database connection
$connectFile = __DIR__ . '/includes/connect.php';
if (!file_exists($connectFile)) {
    echo "❌ Database connection file not found at: $connectFile\n";
    exit(1);
}

// In case connect.php uses redirects (e.g. to pre_start.php) if connection fails,
// let's define a mock $_SERVER structure to avoid notices, and let connect.php do its job.
if (!isset($_SERVER['PHP_SELF'])) {
    $_SERVER['PHP_SELF'] = 'migrate.php';
}

require_once $connectFile;
require_once __DIR__ . '/classes/MigrationManager.php';

// Verify the connection variable exists
if (!isset($conn) || !$conn instanceof mysqli) {
    echo "❌ Error: Connection variable (\$conn) is not properly initialized.\n";
    exit(1);
}

try {
    $manager = new MigrationManager($conn);
} catch (Exception $e) {
    echo "❌ Error initializing MigrationManager: " . $e->getMessage() . "\n";
    exit(1);
}

$action = $argv[1] ?? 'up';

switch (strtolower($action)) {
    case 'up':
    case 'migrate':
        echo "\n🚀 [Focus Migration] Running pending migrations...\n";
        echo str_repeat('=', 60) . "\n";
        
        try {
            $result = $manager->runPending();
            
            foreach ($result['logs'] as $log) {
                echo "  $log\n";
            }
            
            echo str_repeat('=', 60) . "\n";
            if ($result['success']) {
                if ($result['count'] > 0) {
                    echo "🎉 Success! Applied {$result['count']} migration(s) in batch #{$result['batch']}.\n\n";
                } else {
                    echo "✨ Database is already up to date. No pending migrations.\n\n";
                }
            } else {
                echo "❌ Migration failed: " . ($result['error'] ?? 'Unknown Error') . "\n";
                echo "⚠️ Exited with errors. Check logs for details.\n\n";
                exit(1);
            }
        } catch (Exception $e) {
            echo "❌ Fatal Exception: " . $e->getMessage() . "\n\n";
            exit(1);
        }
        break;

    case 'rollback':
        echo "\n🔄 [Focus Migration] Rolling back last batch of migrations...\n";
        echo str_repeat('=', 60) . "\n";
        
        try {
            $result = $manager->rollback();
            
            foreach ($result['logs'] as $log) {
                echo "  $log\n";
            }
            
            echo str_repeat('=', 60) . "\n";
            if ($result['success']) {
                if ($result['count'] > 0) {
                    echo "🎉 Success! Rolled back {$result['count']} migration(s) from batch #{$result['batch']}.\n\n";
                } else {
                    echo "✨ Nothing to rollback. No migrations have been registered.\n\n";
                }
            } else {
                echo "❌ Rollback failed: " . ($result['error'] ?? 'Unknown Error') . "\n";
                echo "⚠️ Exited with errors. Check logs for details.\n\n";
                exit(1);
            }
        } catch (Exception $e) {
            echo "❌ Fatal Exception: " . $e->getMessage() . "\n\n";
            exit(1);
        }
        break;

    case 'status':
        echo "\n📋 [Focus Migration] Database Migrations Status:\n";
        echo str_repeat('-', 80) . "\n";
        echo sprintf(" %-45s | %-12s | %-6s | %-19s\n", "Migration File", "Status", "Batch", "Applied At");
        echo str_repeat('-', 80) . "\n";
        
        try {
            $status = $manager->getMigrationStatus();
            if (empty($status)) {
                echo "  No migrations found. Use 'php migrate.php make [name]' to create one.\n";
            } else {
                foreach ($status as $m) {
                    $statusStr = $m['applied'] ? "✅ Applied" : "⏳ Pending";
                    $batchStr = $m['batch'] !== null ? $m['batch'] : "-";
                    $appliedAtStr = $m['applied_at'] !== null ? $m['applied_at'] : "-";
                    
                    echo sprintf(
                        " %-45s | %-12s | %-6s | %-19s\n",
                        $m['file'],
                        $statusStr,
                        $batchStr,
                        $appliedAtStr
                    );
                }
            }
            echo str_repeat('-', 80) . "\n\n";
        } catch (Exception $e) {
            echo "❌ Error reading status: " . $e->getMessage() . "\n\n";
            exit(1);
        }
        break;

    case 'make':
    case 'create':
        $name = $argv[2] ?? null;
        $type = $argv[3] ?? 'php';
        
        if (!$name) {
            echo "❌ Error: Migration name/description is required.\n";
            echo "Usage: php migrate.php make [migration_name] [php|sql]\n";
            exit(1);
        }
        
        try {
            $fileCreated = $manager->create($name, $type);
            echo "\n🆕 Created new migration files:\n";
            echo "  └─ 📁 database/migrations/$fileCreated\n";
            if (strtolower($type) === 'sql') {
                $downFile = str_replace('.sql', '.down.sql', $fileCreated);
                echo "  └─ 📁 database/migrations/$downFile\n";
            }
            echo "✨ Complete! Open and edit these files to write your schema changes.\n\n";
        } catch (Exception $e) {
            echo "❌ Error creating migration: " . $e->getMessage() . "\n\n";
            exit(1);
        }
        break;

    default:
        echo "\n❓ Unknown command: '$action'\n";
        echo "Usage: php migrate.php [command] [options]\n";
        echo "Commands:\n";
        echo "  up / migrate             Run all pending migrations (default)\n";
        echo "  rollback                 Roll back the last batch of migrations\n";
        echo "  status                   Show the list of all migrations and their status\n";
        echo "  make [name] [php|sql]    Create a new migration file stub\n\n";
        exit(1);
}

if (isset($conn) && $conn instanceof mysqli) {
    $conn->close();
}
