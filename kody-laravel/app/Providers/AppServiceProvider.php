<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Inertia\Inertia::share([
            'auth' => function () {
                $user = \Illuminate\Support\Facades\Auth::user();
                return [
                    'user' => $user ? [
                        'id' => $user->id,
                        'uname' => $user->uname,
                    ] : null,
                    'permissions' => $user && method_exists($user, 'getAllPermissions')
                        ? $user->getAllPermissions()->pluck('name')
                        : [],
                ];
            },
            'flash' => function () {
                return [
                    'success' => session('success'),
                    'error' => session('error'),
                ];
            },
        ]);
    }
}
