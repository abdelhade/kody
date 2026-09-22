<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Force session array driver
$app->make('config')->set('session.driver', 'array');

// Mock request
$request = Illuminate\Http\Request::create('/register', 'POST', [
    'company_name' => 'Test Company',
    'company_code' => 'testcompany22',
    'email' => 'test2@test.com',
    'phone' => '01000000',
    'uname' => 'testadmin2',
    'password' => 'password123',
    'password_confirmation' => 'password123',
]);
$request->headers->set('X-CSRF-TOKEN', 'test');

// Disable CSRF by unbinding it from the pipeline? No, just clear the middleware array!
$router = $app->make('router');
$router->aliasMiddleware('VerifyCsrfToken', function($req, $next) { return $next($req); });

// Try hitting the kernel
$response = $kernel->handle($request);
echo "Status: " . $response->getStatusCode() . "\n";
if ($response->isRedirect()) {
    echo "Redirect: " . $response->headers->get('Location') . "\n";
    if ($request->session()->has('errors')) {
        print_r($request->session()->get('errors')->getBag('default')->getMessages());
    }
}
