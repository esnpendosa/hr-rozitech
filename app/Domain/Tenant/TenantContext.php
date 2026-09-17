<?php

namespace App\Domain\Tenant;

use App\Models\Tenant;

class TenantContext
{
    private static ?Tenant $current = null;

    public static function set(Tenant $tenant): void
    {
        static::$current = $tenant;
    }

    public static function get(): ?Tenant
    {
        return static::$current;
    }

    public static function id(): ?string
    {
        return static::$current?->id;
    }

    public static function getTenantId(): ?string
    {
        return static::$current?->id;
    }

    public static function clear(): void
    {
        static::$current = null;
    }

    public static function has(): bool
    {
        return static::$current !== null;
    }
}
