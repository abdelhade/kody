<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$request = Illuminate\Http\Request::create('/register', 'POST', [
    'company_name' => 'Test',
    'email' => 'test@test.com',
    'phone' => '123',
    'uname' => 'testadmin',
    'password' => '1234',
    'password_confirmation' => '1234'
]);

$controller = new App\Http\Controllers\AuthController();
try {
    $response = $controller->register($request);
    echo get_class($response) . "\n";
    if ($response instanceof \Illuminate\Http\RedirectResponse) {
        $errors = session()->get('errors');
        if ($errors) {
            print_r($errors->toArray());
        } else {
            echo "Redirect without errors. Target: " . $response->getTargetUrl();
        }
    }
} catch (\Exception $e) {
    echo "Exception:\n" . $e->getMessage();
}
