<?php

namespace App\Domain\SaaS;

use App\Domain\Tenant\TenantContext;
use App\Models\PlanFeature;
use App\Models\UsageCounter;
use Illuminate\Support\Facades\Cache;

class EntitlementService
{
    public function canUseFeature(string $feature): bool
    {
        $limit = $this->limit($feature);
        if ($limit === null) {
            return true; // unlimited
        }
        if ($limit === 0) {
            return false; // not available
        }
        return $this->getUsage($feature) < $limit;
    }

    public function remaining(string $feature): ?int
    {
        $limit = $this->limit($feature);
        if ($limit === null) return null;
        return max(0, $limit - $this->getUsage($feature));
    }

    public function limit(string $feature): ?int
    {
        $tenantId = TenantContext::id();
        if (!$tenantId) return null;

        return Cache::remember(
            "tenant:{$tenantId}:limit:{$feature}",
            now()->addMinutes(30),
            function () use ($tenantId, $feature) {
                // Get active plan via subscription
                $subscription = \App\Models\Subscription::where('tenant_id', $tenantId)
                    ->whereIn('status', ['active', 'trial'])
                    ->latest()
                    ->first();

                if (!$subscription) return 0;

                $planFeature = PlanFeature::where('plan_id', $subscription->plan_id)
                    ->where('feature_key', $feature)
                    ->first();

                if (!$planFeature) return null; // not defined = unlimited
                $val = $planFeature->feature_value;
                if ($val === 'true') return PHP_INT_MAX; // unlimited
                if ($val === 'false') return 0; // not available
                return (int) $val;
            }
        );
    }

    public function getUsage(string $feature): int
    {
        $tenantId = TenantContext::id();
        if (!$tenantId) return 0;

        $period = now()->format('Y-m');
        return UsageCounter::where('tenant_id', $tenantId)
            ->where('feature_key', $feature)
            ->where('period', $period)
            ->value('count') ?? 0;
    }

    public function consume(string $feature, int $amount = 1): void
    {
        $tenantId = TenantContext::id();
        if (!$tenantId) return;

        $period = now()->format('Y-m');
        UsageCounter::updateOrCreate(
            ['tenant_id' => $tenantId, 'feature_key' => $feature, 'period' => $period],
            ['count' => \DB::raw("count + {$amount}")]
        );

        // Invalidate cache
        Cache::forget("tenant:{$tenantId}:limit:{$feature}");
    }
}
