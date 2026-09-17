<?php

namespace App\Http\Controllers\Api\V1\Employee;

use App\Application\Services\ApiResponse;
use App\Domain\Tenant\TenantContext;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    /**
     * Display a paginated list of employees.
     * Gate: employees.view
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('employees.view');

        $query = Employee::with([
            'branch:id,name',
            'department:id,name',
            'position:id,name',
        ]);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', '%' . $search . '%')
                  ->orWhere('nik', 'ilike', '%' . $search . '%')
                  ->orWhere('email', 'ilike', '%' . $search . '%')
                  ->orWhere('employee_number', 'ilike', '%' . $search . '%');
            });
        }

        // Filters
        $query->when($request->filled('branch_id'), fn($q) => $q->where('branch_id', $request->branch_id))
              ->when($request->filled('department_id'), fn($q) => $q->where('department_id', $request->department_id))
              ->when($request->filled('position_id'), fn($q) => $q->where('position_id', $request->position_id))
              ->when($request->filled('employment_type'), fn($q) => $q->where('employment_type', $request->employment_type))
              ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
              ->when($request->filled('join_date_from'), fn($q) => $q->whereDate('join_date', '>=', $request->join_date_from))
              ->when($request->filled('join_date_to'), fn($q) => $q->whereDate('join_date', '<=', $request->join_date_to));

        $employees = $query->orderBy('name')->paginate(15);

        return ApiResponse::paginated($employees);
    }

    /**
     * Store a newly created employee.
     * Gate: employees.manage
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize('employees.manage');

        $tenantId = tenant_id();

        $validated = $request->validate([
            'nik'             => [
                'required',
                'string',
                'max:20',
                Rule::unique('employees', 'nik')
                    ->where('tenant_id', $tenantId),
            ],
            'name'            => ['required', 'string', 'max:255'],
            'gender'          => ['required', Rule::in(['male', 'female'])],
            'employment_type' => ['required', Rule::in(['permanent', 'contract', 'probation', 'part_time', 'freelance'])],
            'join_date'       => ['required', 'date'],
            'email'           => [
                'nullable',
                'email',
                Rule::unique('employees', 'email')
                    ->where('tenant_id', $tenantId),
            ],
            'user_id'         => ['nullable', 'uuid', 'exists:users,id'],
            'branch_id'       => ['nullable', 'uuid', 'exists:branches,id'],
            'department_id'   => ['nullable', 'uuid', 'exists:departments,id'],
            'division_id'     => ['nullable', 'uuid', 'exists:divisions,id'],
            'team_id'         => ['nullable', 'uuid', 'exists:teams,id'],
            'position_id'     => ['nullable', 'uuid', 'exists:positions,id'],
            'birth_date'      => ['nullable', 'date'],
            'phone'           => ['nullable', 'string', 'max:20'],
            'status'          => ['nullable', Rule::in(['active', 'inactive', 'resigned', 'terminated'])],
            'employee_number' => ['nullable', 'string', 'max:50'],
            'photo'           => ['nullable', 'string', 'max:255'],
            'religion'        => ['nullable', 'string', 'max:50'],
            'marital_status'  => ['nullable', Rule::in(['single', 'married', 'divorced', 'widowed'])],
            'birth_place'     => ['nullable', 'string', 'max:100'],
            'address'         => ['nullable', 'string'],
            'city'            => ['nullable', 'string', 'max:100'],
            'province'        => ['nullable', 'string', 'max:100'],
            'postal_code'     => ['nullable', 'string', 'max:20'],
        ]);

        // Auto-generate employee_number if not provided
        if (empty($validated['employee_number'])) {
            $validated['employee_number'] = $this->generateEmployeeNumber($tenantId);
        }

        // Default status
        if (!isset($validated['status'])) {
            $validated['status'] = 'active';
        }

        $validated['created_by'] = auth()->id();
        $validated['updated_by'] = auth()->id();

        $employee = Employee::create($validated);
        $employee->load(['branch:id,name', 'department:id,name', 'position:id,name']);

        return ApiResponse::created($employee, __('employee.created'));
    }

    /**
     * Display the specified employee.
     * Gate: employees.view
     */
    public function show(string $id): JsonResponse
    {
        $this->authorize('employees.view');

        $employee = Employee::with([
            'tenant:id,name',
            'user:id,name,email',
            'branch:id,name',
            'department:id,name',
            'division:id,name',
            'team:id,name',
            'position:id,name',
        ])->find($id);

        if (!$employee) {
            return ApiResponse::notFound(__('employee.not_found'));
        }

        return ApiResponse::success($employee);
    }

    /**
     * Update the specified employee.
     * Gate: employees.manage
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $this->authorize('employees.manage');

        $employee = Employee::find($id);
        if (!$employee) {
            return ApiResponse::notFound(__('employee.not_found'));
        }

        $tenantId = tenant_id();

        $validated = $request->validate([
            'nik'             => [
                'sometimes',
                'required',
                'string',
                'max:20',
                Rule::unique('employees', 'nik')
                    ->where('tenant_id', $tenantId)
                    ->ignore($employee->id),
            ],
            'name'            => ['sometimes', 'required', 'string', 'max:255'],
            'gender'          => ['sometimes', 'required', Rule::in(['male', 'female'])],
            'employment_type' => ['sometimes', 'required', Rule::in(['permanent', 'contract', 'probation', 'part_time', 'freelance'])],
            'join_date'       => ['sometimes', 'required', 'date'],
            'email'           => [
                'nullable',
                'email',
                Rule::unique('employees', 'email')
                    ->where('tenant_id', $tenantId)
                    ->ignore($employee->id),
            ],
            'user_id'         => ['nullable', 'uuid', 'exists:users,id'],
            'branch_id'       => ['nullable', 'uuid', 'exists:branches,id'],
            'department_id'   => ['nullable', 'uuid', 'exists:departments,id'],
            'division_id'     => ['nullable', 'uuid', 'exists:divisions,id'],
            'team_id'         => ['nullable', 'uuid', 'exists:teams,id'],
            'position_id'     => ['nullable', 'uuid', 'exists:positions,id'],
            'birth_date'      => ['nullable', 'date'],
            'phone'           => ['nullable', 'string', 'max:20'],
            'status'          => ['nullable', Rule::in(['active', 'inactive', 'resigned', 'terminated'])],
            'employee_number' => ['nullable', 'string', 'max:50'],
            'photo'           => ['nullable', 'string', 'max:255'],
            'religion'        => ['nullable', 'string', 'max:50'],
            'marital_status'  => ['nullable', Rule::in(['single', 'married', 'divorced', 'widowed'])],
            'birth_place'     => ['nullable', 'string', 'max:100'],
            'address'         => ['nullable', 'string'],
            'city'            => ['nullable', 'string', 'max:100'],
            'province'        => ['nullable', 'string', 'max:100'],
            'postal_code'     => ['nullable', 'string', 'max:20'],
        ]);

        $validated['updated_by'] = auth()->id();

        $employee->update($validated);
        $employee->load(['branch:id,name', 'department:id,name', 'position:id,name']);

        return ApiResponse::success($employee, __('employee.updated'));
    }

    /**
     * Soft-delete the specified employee.
     * Gate: employees.manage
     */
    public function destroy(string $id): JsonResponse
    {
        $this->authorize('employees.manage');

        $employee = Employee::find($id);
        if (!$employee) {
            return ApiResponse::notFound(__('employee.not_found'));
        }

        $employee->delete();

        return ApiResponse::success(null, __('employee.deleted'));
    }

    /**
     * Generate a unique employee number for the given tenant.
     * Format: EMP-{YEAR}-{3-digit-padded-count}
     * e.g. EMP-2025-001, EMP-2025-002
     */
    private function generateEmployeeNumber(string $tenantId): string
    {
        $year = now()->year;
        $prefix = "EMP-{$year}-";

        // Count existing employees for this tenant in the current year
        $count = Employee::withoutGlobalScopes()
            ->where('tenant_id', $tenantId)
            ->where('employee_number', 'like', $prefix . '%')
            ->count();

        $next = $count + 1;

        // Ensure uniqueness in case of concurrent inserts
        $candidate = $prefix . str_pad($next, 3, '0', STR_PAD_LEFT);
        while (
            Employee::withoutGlobalScopes()
                ->where('tenant_id', $tenantId)
                ->where('employee_number', $candidate)
                ->exists()
        ) {
            $next++;
            $candidate = $prefix . str_pad($next, 3, '0', STR_PAD_LEFT);
        }

        return $candidate;
    }
}
