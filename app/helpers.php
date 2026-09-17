<?php

use App\Domain\Tenant\TenantContext;
use App\Models\Tenant;

if (!function_exists('tenant')) {
    function tenant(): ?Tenant
    {
        return TenantContext::get();
    }
}

if (!function_exists('tenant_id')) {
    function tenant_id(): ?string
    {
        return TenantContext::id();
    }
}
