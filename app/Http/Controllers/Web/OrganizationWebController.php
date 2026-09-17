<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Company;
use App\Models\Department;
use App\Models\Division;
use App\Models\Employee;
use App\Models\Position;
use App\Models\Team;
use App\Models\WorkLocation;
use App\Support\TenantContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrganizationWebController extends Controller
{
    protected function getTenantId(Request $request): ?string
    {
        return TenantContext::getTenantId() ?? $request->user()?->tenant_id;
    }

    protected function getCompanyId(string $tenantId, Request $request): string
    {
        $company = Company::where('tenant_id', $tenantId)->first();
        if (!$company) {
            $tenant = $request->user()?->tenant_id ? \App\Models\Tenant::find($tenantId) : null;
            $company = Company::create([
                'tenant_id' => $tenantId,
                'name'      => $tenant?->name ?? 'Perusahaan Utama',
                'email'     => $request->user()?->email,
            ]);
        }
        return $company->id;
    }

    // ==========================================
    // 1. KANTOR CABANG (BRANCHES)
    // ==========================================
    public function branches(Request $request): View
    {
        $tenantId = $this->getTenantId($request);
        $search = $request->input('search');

        $branches = Branch::where('tenant_id', $tenantId)
            ->when($search, fn($q) => $q->where('name', 'ilike', "%{$search}%")->orWhere('city', 'ilike', "%{$search}%"))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('organization.branches.index', compact('branches', 'search'));
    }

    public function storeBranch(Request $request): RedirectResponse
    {
        $tenantId = $this->getTenantId($request);
        $companyId = $this->getCompanyId($tenantId, $request);

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'code'     => 'nullable|string|max:50',
            'city'     => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'address'  => 'nullable|string',
            'phone'    => 'nullable|string|max:50',
        ]);

        Branch::create(array_merge($validated, [
            'tenant_id'  => $tenantId,
            'company_id' => $companyId,
            'is_active'  => true,
        ]));

        return redirect()->route('branches.index')->with('success', 'Kantor cabang berhasil ditambahkan.');
    }

    public function destroyBranch(Request $request, string $id): RedirectResponse
    {
        $tenantId = $this->getTenantId($request);
        Branch::where('tenant_id', $tenantId)->where('id', $id)->delete();
        return redirect()->route('branches.index')->with('success', 'Kantor cabang berhasil dihapus.');
    }

    // ==========================================
    // 2. DEPARTEMEN / DIVISI
    // ==========================================
    public function departments(Request $request): View
    {
        $tenantId = $this->getTenantId($request);
        $search = $request->input('search');
        $branchId = $request->input('branch_id');

        $branches = Branch::where('tenant_id', $tenantId)->orderBy('name')->get();

        $departments = Department::where('tenant_id', $tenantId)
            ->when($search, fn($q) => $q->where('name', 'ilike', "%{$search}%")->orWhere('code', 'ilike', "%{$search}%"))
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('organization.departments.index', compact('departments', 'branches', 'search', 'branchId'));
    }

    public function storeDepartment(Request $request): RedirectResponse
    {
        $tenantId = $this->getTenantId($request);

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'code'        => 'nullable|string|max:50',
            'branch_id'   => 'nullable|exists:branches,id',
            'description' => 'nullable|string',
        ]);

        Department::create(array_merge($validated, [
            'tenant_id' => $tenantId,
            'is_active' => true,
        ]));

        return redirect()->route('departments.index')->with('success', 'Departemen / divisi berhasil ditambahkan.');
    }

    public function destroyDepartment(Request $request, string $id): RedirectResponse
    {
        $tenantId = $this->getTenantId($request);
        Department::where('tenant_id', $tenantId)->where('id', $id)->delete();
        return redirect()->route('departments.index')->with('success', 'Departemen berhasil dihapus.');
    }

    // ==========================================
    // 3. JABATAN & POSISI
    // ==========================================
    public function positions(Request $request): View
    {
        $tenantId = $this->getTenantId($request);
        $search = $request->input('search');
        $departmentId = $request->input('department_id');

        $departments = Department::where('tenant_id', $tenantId)->orderBy('name')->get();

        $positions = Position::where('tenant_id', $tenantId)
            ->when($search, fn($q) => $q->where('name', 'ilike', "%{$search}%")->orWhere('code', 'ilike', "%{$search}%"))
            ->when($departmentId, fn($q) => $q->where('department_id', $departmentId))
            ->with('department')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('organization.positions.index', compact('positions', 'departments', 'search', 'departmentId'));
    }

    public function storePosition(Request $request): RedirectResponse
    {
        $tenantId = $this->getTenantId($request);

        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'code'          => 'nullable|string|max:50',
            'department_id' => 'nullable|exists:departments,id',
            'level'         => 'nullable|integer',
            'description'   => 'nullable|string',
        ]);

        Position::create(array_merge($validated, [
            'tenant_id' => $tenantId,
            'is_active' => true,
        ]));

        return redirect()->route('positions.index')->with('success', 'Jabatan / posisi berhasil ditambahkan.');
    }

    public function destroyPosition(Request $request, string $id): RedirectResponse
    {
        $tenantId = $this->getTenantId($request);
        Position::where('tenant_id', $tenantId)->where('id', $id)->delete();
        return redirect()->route('positions.index')->with('success', 'Jabatan berhasil dihapus.');
    }

    // ==========================================
    // 4. TIM KERJA (TEAMS)
    // ==========================================
    public function teams(Request $request): View
    {
        $tenantId = $this->getTenantId($request);
        $search = $request->input('search');
        $departmentId = $request->input('department_id');

        $departments = Department::where('tenant_id', $tenantId)->orderBy('name')->get();

        $teams = Team::where('tenant_id', $tenantId)
            ->when($search, fn($q) => $q->where('name', 'ilike', "%{$search}%")->orWhere('code', 'ilike', "%{$search}%"))
            ->when($departmentId, fn($q) => $q->where('department_id', $departmentId))
            ->with(['department', 'leader'])
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('organization.teams.index', compact('teams', 'departments', 'search', 'departmentId'));
    }

    public function storeTeam(Request $request): RedirectResponse
    {
        $tenantId = $this->getTenantId($request);

        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'code'          => 'nullable|string|max:50',
            'department_id' => 'nullable|exists:departments,id',
            'description'   => 'nullable|string',
        ]);

        Team::create(array_merge($validated, [
            'tenant_id' => $tenantId,
            'is_active' => true,
        ]));

        return redirect()->route('teams.index')->with('success', 'Tim kerja berhasil ditambahkan.');
    }

    public function destroyTeam(Request $request, string $id): RedirectResponse
    {
        $tenantId = $this->getTenantId($request);
        Team::where('tenant_id', $tenantId)->where('id', $id)->delete();
        return redirect()->route('teams.index')->with('success', 'Tim kerja berhasil dihapus.');
    }

    // ==========================================
    // 5. WORK LOCATIONS (LOKASI PRESENSI / KERJA)
    // ==========================================
    public function workLocations(Request $request): View
    {
        $tenantId = $this->getTenantId($request);
        $search = $request->input('search');
        $branchId = $request->input('branch_id');

        $branches = Branch::where('tenant_id', $tenantId)->orderBy('name')->get();

        $locations = WorkLocation::where('tenant_id', $tenantId)
            ->when($search, fn($q) => $q->where('name', 'ilike', "%{$search}%")->orWhere('city', 'ilike', "%{$search}%"))
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->with('branch')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('organization.work-locations.index', compact('locations', 'branches', 'search', 'branchId'));
    }

    public function storeWorkLocation(Request $request): RedirectResponse
    {
        $tenantId = $this->getTenantId($request);

        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'code'          => 'nullable|string|max:50',
            'branch_id'     => 'nullable|exists:branches,id',
            'city'          => 'nullable|string|max:100',
            'address'       => 'nullable|string',
            'latitude'      => 'nullable|numeric',
            'longitude'     => 'nullable|numeric',
            'radius_meters' => 'nullable|integer|min:1',
        ]);

        WorkLocation::create(array_merge($validated, [
            'tenant_id'     => $tenantId,
            'radius_meters' => $validated['radius_meters'] ?? 100,
            'is_active'     => true,
        ]));

        return redirect()->route('work-locations.index')->with('success', 'Lokasi kerja/presensi berhasil ditambahkan.');
    }

    public function destroyWorkLocation(Request $request, string $id): RedirectResponse
    {
        $tenantId = $this->getTenantId($request);
        WorkLocation::where('tenant_id', $tenantId)->where('id', $id)->delete();
        return redirect()->route('work-locations.index')->with('success', 'Lokasi kerja berhasil dihapus.');
    }
}
