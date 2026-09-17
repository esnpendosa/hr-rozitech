<?php

namespace App\Support;

use App\Domain\Tenant\TenantContext as DomainTenantContext;

/**
 * Class TenantContext
 *
 * Bridge helper untuk mengakses ID dan instance Tenant aktif di konteks web & API.
 */
class TenantContext
{
    /**
     * Dapatkan ID Tenant aktif
     */
    public static function getTenantId(): ?string
    {
        return DomainTenantContext::id();
    }

    /**
     * Alias method id()
     */
    public static function id(): ?string
    {
        return DomainTenantContext::id();
    }

    /**
     * Dapatkan model Tenant aktif
     */
    public static function get(): mixed
    {
        return DomainTenantContext::get();
    }
}
