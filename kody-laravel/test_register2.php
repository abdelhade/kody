<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$app->make('config')->set('session.driver', 'array');
$request = Illuminate\Http\Request::create('/register', 'POST', [
    'company_name' => 'Test Company',
    'company_code' => 'testcompany22',
    'email' => 'test2@test.com',
    'phone' => '01000000',
    'uname' => 'testadmin2',
    'password' => 'password123',
    'password_confirmation' => 'password123',
]);
$request->setLaravelSession($app->make('session')->driver());

// Disable VerifyCsrfToken middleware
$app->instance(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class, new class {
    public function handle($request, $next) { return $next($request); }
});

$response = $kernel->handle($request);
if ($response->isRedirect()) {
    echo "Redirect: " . $response->headers->get('Location') . "\n";
    if ($request->session()->has('errors')) {
        print_r($request->session()->get('errors')->getBag('default')->getMessages());
    }
} else {
    echo "Status: " . $response->getStatusCode() . "\n";
    if ($response->getStatusCode() == 500) {
        echo $response->getContent();
    }
}
