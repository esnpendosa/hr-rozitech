<?php

namespace App\Http\Controllers\Api\V1\Employee;

use App\Application\Services\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmployeeHistoryController extends Controller
{
    /**
     * List change history for an employee.
     * Append-only — no create/update/delete endpoints.
     */
    public function index(Request $request, string $employeeId): JsonResponse
    {
        $this->authorize('employees.view');

        $employee = Employee::findOrFail($employeeId);

        $query = EmployeeHistory::where('employee_id', $employee->id)
            ->with('changer:id,name,email');

        if ($request->filled('field_name')) {
            $query->where('field_name', $request->field_name);
        }

        $history = $query->orderByDesc('changed_at')
            ->paginate($request->input('per_page', 20));

        return ApiResponse::paginated($history);
    }
}
