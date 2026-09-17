<?php

namespace App\Http\Middleware;

use App\Domain\Tenant\TenantContext;
use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;

class ResolveTenant
{
    public function handle(Request $request, Closure $next): mixed
    {
        // Only resolve when user is authenticated
        if ($request->user() && $request->user()->tenant_id) {
            $tenant = Tenant::find($request->user()->tenant_id);
            if ($tenant && $tenant->status === 'active') {
                TenantContext::set($tenant);
            }
        }

        $response = $next($request);

        TenantContext::clear();

        return $response;
    }
}
