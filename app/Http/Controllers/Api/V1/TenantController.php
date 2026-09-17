<?php

namespace App\Http\Controllers\Api\V1;

use App\Application\Services\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

class TenantController extends Controller
{
    /**
     * List all tenants (Super Admin only).
     */
    public function index(Request $request): JsonResponse
    {
        Gate::authorize('platform.tenants.manage');

        $tenants = Tenant::withTrashed()
            ->orderByDesc('created_at')
            ->paginate($request->input('per_page', 20));

        return ApiResponse::paginated($tenants);
    }

    /**
     * Create new tenant (Super Admin) or public registration.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'slug'           => ['required', 'string', 'max:100', 'unique:tenants,slug', 'regex:/^[a-z0-9\-]+$/'],
            'owner_name'     => ['required', 'string', 'max:255'],
            'owner_email'    => ['required', 'email', 'unique:users,email'],
            'owner_password' => ['required', 'string', 'min:8'],
            'plan_slug'      => ['nullable', 'string'],
        ]);

        return DB::transaction(function () use ($request) {
            $tenant = Tenant::create([
                'name'   => $request->name,
                'slug'   => $request->slug,
                'status' => 'trial',
            ]);

            $plan = SubscriptionPlan::where('slug', $request->input('plan_slug', 'starter'))->first();
            if ($plan) {
                Subscription::create([
                    'tenant_id'     => $tenant->id,
                    'plan_id'       => $plan->id,
                    'status'        => 'trial',
                    'trial_ends_at' => now()->addDays(14),
                ]);
            }

            $owner = User::create([
                'tenant_id' => $tenant->id,
                'name'      => $request->owner_name,
                'email'     => $request->owner_email,
                'password'  => Hash::make($request->owner_password),
                'locale'    => 'id',
            ]);

            if (class_exists(\Spatie\Permission\Models\Role::class)) {
                $ownerRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'owner', 'guard_name' => 'web']);
                $owner->assignRole($ownerRole);
            }

            return ApiResponse::created([
                'tenant' => ['id' => $tenant->id, 'name' => $tenant->name, 'slug' => $tenant->slug, 'status' => $tenant->status],
                'owner'  => ['id' => $owner->id, 'name' => $owner->name, 'email' => $owner->email],
            ], __('tenant.created'));
        });
    }

    /**
     * Show a tenant (Super Admin).
     */
    public function show(Request $request, string $id): JsonResponse
    {
        Gate::authorize('platform.tenants.manage');
        $tenant = Tenant::withTrashed()->findOrFail($id);
        return ApiResponse::success($tenant);
    }

    /**
     * Update tenant status or name (Super Admin).
     */
    public function update(Request $request, string $id): JsonResponse
    {
        Gate::authorize('platform.tenants.manage');
        $tenant = Tenant::withTrashed()->findOrFail($id);

        $request->validate([
            'status' => ['sometimes', 'in:active,suspended,trial'],
            'name'   => ['sometimes', 'string', 'max:255'],
        ]);

        $tenant->update($request->only('status', 'name'));

        return ApiResponse::success($tenant, __('tenant.updated'));
    }

    /**
     * Suspend a tenant (Super Admin).
     */
    public function suspend(string $id): JsonResponse
    {
        Gate::authorize('platform.tenants.manage');
        Tenant::findOrFail($id)->update(['status' => 'suspended']);
        return ApiResponse::success(null, __('tenant.suspended'));
    }

    /**
     * Activate a tenant (Super Admin).
     */
    public function activate(string $id): JsonResponse
    {
        Gate::authorize('platform.tenants.manage');
        $tenant = Tenant::withTrashed()->findOrFail($id);
        $tenant->restore();
        $tenant->update(['status' => 'active']);
        return ApiResponse::success(null, __('tenant.activated'));
    }
}
