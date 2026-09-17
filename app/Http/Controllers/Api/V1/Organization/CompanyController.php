<?php

namespace App\Http\Controllers\Api\V1\Organization;

use App\Application\Services\ApiResponse;
use App\Domain\Tenant\TenantContext;
use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function show(): JsonResponse
    {
        $company = Company::where('tenant_id', TenantContext::id())->first();
        if (!$company) return ApiResponse::notFound(__('organization.company_not_found'));
        return ApiResponse::success($company);
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('organization.manage');
        $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'email'      => ['nullable', 'email'],
            'phone'      => ['nullable', 'string', 'max:50'],
            'address'    => ['nullable', 'string'],
            'city'       => ['nullable', 'string', 'max:100'],
            'province'   => ['nullable', 'string', 'max:100'],
            'tax_number' => ['nullable', 'string', 'max:50'],
            'industry'   => ['nullable', 'string', 'max:100'],
            'website'    => ['nullable', 'url'],
        ]);

        $company = Company::updateOrCreate(
            ['tenant_id' => TenantContext::id()],
            $request->only('name', 'email', 'phone', 'address', 'city', 'province', 'postal_code', 'country', 'tax_number', 'industry', 'website')
        );

        return ApiResponse::success($company, __('organization.company_saved'));
    }
}
