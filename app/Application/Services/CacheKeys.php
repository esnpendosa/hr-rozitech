<?php

namespace App\Application\Services;

class CacheKeys
{
    public static function tenantDashboard(string $tenantId, string $period): string
    {
        return "tenant:{$tenantId}:dashboard:{$period}";
    }

    public static function tenantPermissions(string $userId): string
    {
        return "user:{$userId}:permissions";
    }

    public static function tenantLimits(string $tenantId, string $feature): string
    {
        return "tenant:{$tenantId}:limit:{$feature}";
    }

    public static function tenantConfig(string $tenantId): string
    {
        return "tenant:{$tenantId}:config";
    }
}
