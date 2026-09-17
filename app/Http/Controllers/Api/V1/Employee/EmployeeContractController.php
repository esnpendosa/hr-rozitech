<?php

namespace App\Http\Controllers\Api\V1\Employee;

use App\Application\Services\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EmployeeContractController extends Controller
{
    /**
     * List contracts for an employee.
     */
    public function index(string $employeeId): JsonResponse
    {
        $this->authorize('employees.view');

        $employee = Employee::findOrFail($employeeId);

        $contracts = EmployeeContract::where('employee_id', $employee->id)
            ->orderByDesc('start_date')
            ->get();

        return ApiResponse::success($contracts);
    }

    /**
     * Create a new contract for an employee.
     * When is_active = true, deactivate previous active contract.
     */
    public function store(Request $request, string $employeeId): JsonResponse
    {
        $this->authorize('employees.manage');

        $employee = Employee::findOrFail($employeeId);

        $validated = $request->validate([
            'contract_type' => ['required', Rule::in(['permanent', 'contract', 'probation', 'freelance'])],
            'start_date'    => ['required', 'date'],
            'end_date'      => ['nullable', 'date', 'after_or_equal:start_date'],
            'salary'        => ['nullable', 'numeric', 'min:0'],
            'notes'         => ['nullable', 'string'],
            'is_active'     => ['nullable', 'boolean'],
        ]);

        $validated['employee_id'] = $employee->id;
        $validated['created_by']  = auth()->id();

        // Auto-set active if not specified
        if (!isset($validated['is_active'])) {
            $validated['is_active'] = true;
        }

        // Deactivate previous active contract
        if ($validated['is_active']) {
            EmployeeContract::where('employee_id', $employee->id)
                ->where('is_active', true)
                ->update(['is_active' => false]);
        }

        $contract = EmployeeContract::create($validated);

        return ApiResponse::created($contract, __('employee.contract_created'));
    }

    /**
     * Show a specific contract.
     */
    public function show(string $employeeId, string $contractId): JsonResponse
    {
        $this->authorize('employees.view');

        $contract = EmployeeContract::where('employee_id', $employeeId)
            ->findOrFail($contractId);

        return ApiResponse::success($contract);
    }

    /**
     * Update a contract.
     */
    public function update(Request $request, string $employeeId, string $contractId): JsonResponse
    {
        $this->authorize('employees.manage');

        $contract = EmployeeContract::where('employee_id', $employeeId)
            ->findOrFail($contractId);

        $validated = $request->validate([
            'contract_type' => ['sometimes', 'required', Rule::in(['permanent', 'contract', 'probation', 'freelance'])],
            'start_date'    => ['sometimes', 'required', 'date'],
            'end_date'      => ['nullable', 'date'],
            'salary'        => ['nullable', 'numeric', 'min:0'],
            'notes'         => ['nullable', 'string'],
            'is_active'     => ['nullable', 'boolean'],
        ]);

        $contract->update($validated);

        return ApiResponse::success($contract, __('employee.contract_updated'));
    }

    /**
     * Delete a contract.
     */
    public function destroy(string $employeeId, string $contractId): JsonResponse
    {
        $this->authorize('employees.manage');

        $contract = EmployeeContract::where('employee_id', $employeeId)
            ->findOrFail($contractId);

        $contract->delete();

        return ApiResponse::success(null, __('employee.contract_deleted'));
    }
}
