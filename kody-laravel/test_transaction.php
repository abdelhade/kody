<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
try {
    DB::connection('landlord')->beginTransaction();
    DB::connection('landlord')->statement("CREATE DATABASE \	est_foo\");
    DB::connection('landlord')->rollBack();
    echo "Rollback successful\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
DB::connection('landlord')->statement("DROP DATABASE IF EXISTS \	est_foo\");
