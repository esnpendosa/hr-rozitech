<?php

namespace App\Http\Controllers\Api\V1\Employee;

use App\Application\Services\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeEmergencyContact;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmployeeEmergencyContactController extends Controller
{
    /**
     * List emergency contacts for an employee.
     */
    public function index(string $employeeId): JsonResponse
    {
        $this->authorize('employees.view');

        $employee = Employee::findOrFail($employeeId);

        $contacts = EmployeeEmergencyContact::where('employee_id', $employee->id)
            ->orderByDesc('is_primary')
            ->get();

        return ApiResponse::success($contacts);
    }

    /**
     * Add an emergency contact.
     */
    public function store(Request $request, string $employeeId): JsonResponse
    {
        $this->authorize('employees.manage');

        $employee = Employee::findOrFail($employeeId);

        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'relationship' => ['required', 'string', 'max:100'],
            'phone'        => ['required', 'string', 'max:50'],
            'address'      => ['nullable', 'string'],
            'is_primary'   => ['nullable', 'boolean'],
        ]);

        $validated['employee_id'] = $employee->id;

        // If setting as primary, unset others
        if (!empty($validated['is_primary'])) {
            EmployeeEmergencyContact::where('employee_id', $employee->id)
                ->where('is_primary', true)
                ->update(['is_primary' => false]);
        }

        $contact = EmployeeEmergencyContact::create($validated);

        return ApiResponse::created($contact, __('employee.emergency_contact_created'));
    }

    /**
     * Update an emergency contact.
     */
    public function update(Request $request, string $employeeId, string $contactId): JsonResponse
    {
        $this->authorize('employees.manage');

        $contact = EmployeeEmergencyContact::where('employee_id', $employeeId)
            ->findOrFail($contactId);

        $validated = $request->validate([
            'name'         => ['sometimes', 'required', 'string', 'max:255'],
            'relationship' => ['sometimes', 'required', 'string', 'max:100'],
            'phone'        => ['sometimes', 'required', 'string', 'max:50'],
            'address'      => ['nullable', 'string'],
            'is_primary'   => ['nullable', 'boolean'],
        ]);

        if (!empty($validated['is_primary'])) {
            EmployeeEmergencyContact::where('employee_id', $employeeId)
                ->where('id', '!=', $contactId)
                ->where('is_primary', true)
                ->update(['is_primary' => false]);
        }

        $contact->update($validated);

        return ApiResponse::success($contact, __('employee.emergency_contact_updated'));
    }

    /**
     * Delete an emergency contact.
     */
    public function destroy(string $employeeId, string $contactId): JsonResponse
    {
        $this->authorize('employees.manage');

        $contact = EmployeeEmergencyContact::where('employee_id', $employeeId)
            ->findOrFail($contactId);

        $contact->delete();

        return ApiResponse::success(null, __('employee.emergency_contact_deleted'));
    }
}
