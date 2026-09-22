<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::create('/register', 'POST', [
    'company_name' => 'Test Company',
    'company_code' => 'testcompany123',
    'email' => 'test@test.com',
    'phone' => '01000000',
    'uname' => 'testadmin',
    'password' => 'password123',
    'password_confirmation' => 'password123',
]);
$response = $kernel->handle($request);
echo "Status: " . $response->getStatusCode() . "\n";
if ($response->getStatusCode() == 500) {
    echo $response->getContent();
}
