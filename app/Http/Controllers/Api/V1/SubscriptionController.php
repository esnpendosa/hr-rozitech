<?php

namespace App\Http\Controllers\Api\V1;

use App\Application\Services\ApiResponse;
use App\Domain\SaaS\EntitlementService;
use App\Domain\Tenant\TenantContext;
use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use Illuminate\Http\JsonResponse;

class SubscriptionController extends Controller
{
    public function __construct(
        private readonly EntitlementService $entitlement
    ) {}

    /**
     * Get current tenant subscription & plan details.
     */
    public function show(): JsonResponse
    {
        $tenantId = TenantContext::id();
        $subscription = Subscription::where('tenant_id', $tenantId)
            ->whereIn('status', ['active', 'trial'])
            ->with('plan')
            ->latest()
            ->first();

        if (!$subscription) {
            return ApiResponse::error(__('saas.no_active_subscription'), status: 404);
        }

        return ApiResponse::success([
            'subscription' => $subscription,
            'usage' => [
                'employees' => [
                    'used'      => $this->entitlement->getUsage('max_employees'),
                    'limit'     => $this->entitlement->limit('max_employees'),
                    'remaining' => $this->entitlement->remaining('max_employees'),
                ],
                'branches' => [
                    'used'      => $this->entitlement->getUsage('max_branches'),
                    'limit'     => $this->entitlement->limit('max_branches'),
                    'remaining' => $this->entitlement->remaining('max_branches'),
                ],
            ],
        ]);
    }

    /**
     * List all available subscription plans.
     */
    public function plans(): JsonResponse
    {
        $plans = SubscriptionPlan::where('is_active', true)
            ->with('features')
            ->orderBy('price')
            ->get();

        return ApiResponse::success($plans);
    }
}
