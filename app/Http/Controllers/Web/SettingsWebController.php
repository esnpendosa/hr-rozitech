<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Support\TenantContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Class SettingsWebController
 *
 * Mengelola antarmuka pengaturan perusahaan, langganan & kuota SaaS,
 * manajemen pengguna (User Management), serta hak akses (Roles & Permissions).
 */
class SettingsWebController extends Controller
{
    /**
     * Tampilkan pengaturan umum perusahaan
     */
    public function company(Request $request): View
    {
        $tenantId = TenantContext::getTenantId() ?? $request->user()?->tenant_id;
        $tenant = $request->user()?->tenant_id ? \App\Models\Tenant::find($tenantId) : null;
        $company = Company::where('tenant_id', $tenantId)->first();

        if (!$company && $tenant) {
            $company = Company::create([
                'tenant_id' => $tenant->id,
                'name'      => $tenant->name,
                'email'     => $request->user()?->email,
            ]);
        }

        return view('settings.company', compact('company'));
    }

    /**
     * Perbarui data profil perusahaan
     */
    public function updateCompany(Request $request): RedirectResponse
    {
        $tenantId = TenantContext::getTenantId() ?? $request->user()?->tenant_id;

        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'nullable|email|max:255',
            'phone'   => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'city'    => 'nullable|string|max:100',
            'website' => 'nullable|string|max:255',
        ]);

        Company::updateOrCreate(
            ['tenant_id' => $tenantId],
            $validated
        );

        return redirect()->route('settings.company')->with('success', 'Pengaturan profil perusahaan berhasil diperbarui.');
    }

    /**
     * Halaman manajemen langganan & pemakaian kuota tenant
     */
    public function subscription(Request $request): View
    {
        $plans = SubscriptionPlan::with('features')->where('is_active', true)->get();
        return view('settings.subscription', compact('plans'));
    }

    /**
     * Tampilkan daftar akun pengguna tenant
     */
    public function users(Request $request): View
    {
        $tenantId = TenantContext::getTenantId() ?? $request->user()?->tenant_id;
        $users = User::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->with('roles')
            ->latest()
            ->paginate(15);

        $roles = Role::where('guard_name', 'web')->get();

        return view('settings.users', compact('users', 'roles'));
    }

    /**
     * Simpan pengguna baru untuk tenant
     */
    public function storeUser(Request $request): RedirectResponse
    {
        $tenantId = TenantContext::getTenantId() ?? $request->user()?->tenant_id;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|string|exists:roles,name',
        ]);

        $user = User::create([
            'tenant_id' => $tenantId,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->assignRole($validated['role']);

        return redirect()->route('settings.users')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    /**
     * Tampilkan daftar peran (Roles) & izin (Permissions)
     */
    public function roles(): View
    {
        $roles = Role::with('permissions')->where('guard_name', 'web')->get();
        $permissions = Permission::where('guard_name', 'web')->get();

        return view('settings.roles', compact('roles', 'permissions'));
    }
}
