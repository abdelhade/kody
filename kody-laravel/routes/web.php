<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WorkspaceController;
use Illuminate\Support\Facades\Route;

Route::get('/', function (\Illuminate\Http\Request $request) {
    if (\App\Models\Tenant::current()) {
        if (\Illuminate\Support\Facades\Auth::guard('web')->check()) {
            return redirect()->route('dashboard');
        }
    } else {
        $landlordUser = \Illuminate\Support\Facades\Auth::guard('landlord')->user();
        if ($landlordUser) {
            $tenant = \App\Models\Tenant::where('settings->email', $landlordUser->email)->first();
            if ($tenant) {
                $port = $request->getPort();
                $portSuffix = ($port && $port != 80 && $port != 443) ? ':' . $port : '';
                $url = $request->getScheme() . '://' . $tenant->domain . $portSuffix . '/login';
                return Inertia\Inertia::render('Welcome', [
                    'isLoggedIn' => true,
                    'tenantUrl' => $url,
                ]);
            }
        }
    }
    return Inertia\Inertia::render('Welcome', ['isLoggedIn' => false]);
})->name('welcome');

// Removed guest:landlord middleware to avoid redirect loops. Handled in Controller.
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/api/check-subdomain', [AuthController::class, 'checkSubdomain']);

// Workspace Provisioning (Must be logged into Landlord)
Route::middleware('auth:landlord')->group(function () {
    Route::get('/workspaces/create', [WorkspaceController::class, 'create'])->name('workspaces.create');
    Route::post('/workspaces', [WorkspaceController::class, 'store'])->name('workspaces.store');
});

Route::any('/logout', [AuthController::class, 'logout'])->name('logout');

// Tenant Application (Must be logged into Web guard & Tenant middleware)
Route::middleware(['tenant', 'auth:web'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Accounting & Shifts
    Route::get('/shifts', [\App\Http\Controllers\ShiftController::class, 'index'])->name('shifts.index');
    Route::post('/shifts/close', [\App\Http\Controllers\ShiftController::class, 'close'])->name('shifts.close');
    Route::get('/vouchers', [\App\Http\Controllers\VoucherController::class, 'index'])->name('vouchers.index');
    Route::post('/vouchers', [\App\Http\Controllers\VoucherController::class, 'store'])->name('vouchers.store');
    
    // Reports
    Route::prefix('reports')->group(function () {
        Route::get('/account-statement', [\App\Http\Controllers\ReportController::class, 'accountStatement'])->name('reports.account-statement');
        Route::get('/trial-balance', [\App\Http\Controllers\ReportController::class, 'trialBalance'])->name('reports.trial-balance');
        Route::get('/profit-loss', [\App\Http\Controllers\ReportController::class, 'profitLoss'])->name('reports.profit-loss');
    });
    Route::get('/accounting/opening-balances', [\App\Http\Controllers\OpeningBalancesController::class, 'index'])->name('accounting.opening-balances');
    Route::post('/accounting/opening-balances', [\App\Http\Controllers\OpeningBalancesController::class, 'store']);

    // Master Data
    Route::prefix('master-data')->group(function () {
        Route::get('/customers', [\App\Http\Controllers\CustomerController::class, 'index'])->name('customers.index');
        Route::post('/customers', [\App\Http\Controllers\CustomerController::class, 'store'])->name('customers.store');
        Route::put('/customers/{id}', [\App\Http\Controllers\CustomerController::class, 'update'])->name('customers.update');
        Route::delete('/customers/{id}', [\App\Http\Controllers\CustomerController::class, 'destroy'])->name('customers.destroy');

        Route::get('/suppliers', [\App\Http\Controllers\SupplierController::class, 'index'])->name('suppliers.index');
        Route::post('/suppliers', [\App\Http\Controllers\SupplierController::class, 'store'])->name('suppliers.store');
        Route::put('/suppliers/{id}', [\App\Http\Controllers\SupplierController::class, 'update'])->name('suppliers.update');
        Route::delete('/suppliers/{id}', [\App\Http\Controllers\SupplierController::class, 'destroy'])->name('suppliers.destroy');

        Route::get('/warehouses', [\App\Http\Controllers\WarehouseController::class, 'index'])->name('warehouses.index');
        Route::post('/warehouses', [\App\Http\Controllers\WarehouseController::class, 'store'])->name('warehouses.store');
        Route::put('/warehouses/{id}', [\App\Http\Controllers\WarehouseController::class, 'update'])->name('warehouses.update');
        Route::delete('/warehouses/{id}', [\App\Http\Controllers\WarehouseController::class, 'destroy'])->name('warehouses.destroy');

        Route::get('/item-groups', [\App\Http\Controllers\ItemGroupController::class, 'index'])->name('item-groups.index');
        Route::post('/item-groups', [\App\Http\Controllers\ItemGroupController::class, 'store'])->name('item-groups.store');
        Route::put('/item-groups/{id}', [\App\Http\Controllers\ItemGroupController::class, 'update'])->name('item-groups.update');
        Route::delete('/item-groups/{id}', [\App\Http\Controllers\ItemGroupController::class, 'destroy'])->name('item-groups.destroy');

        Route::get('/items', [\App\Http\Controllers\ItemController::class, 'index'])->name('items.index');
        Route::get('/items/create', [\App\Http\Controllers\ItemController::class, 'create'])->name('items.create');
        Route::post('/items', [\App\Http\Controllers\ItemController::class, 'store'])->name('items.store');
        Route::get('/items/{id}/edit', [\App\Http\Controllers\ItemController::class, 'edit'])->name('items.edit');
        Route::put('/items/{id}', [\App\Http\Controllers\ItemController::class, 'update'])->name('items.update');
        Route::delete('/items/{id}', [\App\Http\Controllers\ItemController::class, 'destroy'])->name('items.destroy');
    });

    // Invoices
    Route::prefix('invoices')->group(function () {
        Route::get('/', [\App\Http\Controllers\InvoiceController::class, 'index'])->name('invoices.index');
        Route::get('/create', [\App\Http\Controllers\InvoiceController::class, 'create'])->name('invoices.create');
        Route::post('/', [\App\Http\Controllers\InvoiceController::class, 'store'])->name('invoices.store');
    });

    // POS
    Route::get('/pos', [\App\Http\Controllers\PosController::class, 'index'])->name('pos.index');
    Route::post('/pos/checkout', [\App\Http\Controllers\PosController::class, 'checkout'])->name('pos.checkout');
});
