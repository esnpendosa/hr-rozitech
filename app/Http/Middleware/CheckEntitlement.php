<?php

namespace App\Http\Middleware;

use App\Domain\SaaS\EntitlementService;
use App\Domain\Tenant\TenantContext;
use Closure;
use Illuminate\Http\Request;

class CheckEntitlement
{
    public function __construct(
        private readonly EntitlementService $entitlementService
    ) {}

    public function handle(Request $request, Closure $next, string $feature): mixed
    {
        if (!TenantContext::has()) {
            return response()->json([
                'success' => false,
                'message' => __('auth.tenant_required'),
            ], 403);
        }

        if (!$this->entitlementService->canUseFeature($feature)) {
            return response()->json([
                'success' => false,
                'message' => __('saas.feature_not_available'),
            ], 403);
        }

        return $next($request);
    }
}
