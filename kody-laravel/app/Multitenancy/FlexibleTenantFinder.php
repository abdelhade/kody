<?php

namespace App\Multitenancy;

use Illuminate\Http\Request;
use Spatie\Multitenancy\Models\Tenant;
use Spatie\Multitenancy\TenantFinder\TenantFinder;

class FlexibleTenantFinder extends TenantFinder
{
    public function findForRequest(Request $request): ?Tenant
    {
        $host = $request->getHost();

        // 1. Check by Domain/Subdomain
        // If the host is not the main application domain (e.g., localhost or your production domain), try to find by domain
        $mainDomain = env('APP_DOMAIN', 'localhost');
        if ($host !== $mainDomain && $host !== '127.0.0.1') {
            $tenantModel = config('multitenancy.tenant_model', Tenant::class);
            $tenant = $tenantModel::where('domain', $host)->first();
            if ($tenant) {
                return $tenant;
            }
        }

        // 2. Fallback to Session (if still needed for any reason)
        if ($request->hasSession() && $request->session()->has('tenant_id')) {
            $tenantModel = config('multitenancy.tenant_model', Tenant::class);
            return $tenantModel::find($request->session()->get('tenant_id'));
        }

        return null;
    }
}
