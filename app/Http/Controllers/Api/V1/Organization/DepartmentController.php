<?php

namespace App\Http\Controllers\Api\V1\Organization;

use App\Application\Services\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('departments.view');
        $items = Department::when($request->search, fn($q) => $q->where('name', 'ilike', '%'.$request->search.'%'))
            ->when($request->branch_id, fn($q) => $q->where('branch_id', $request->branch_id))
            ->when($request->division_id, fn($q) => $q->where('division_id', $request->division_id))
            ->orderBy('name')
            ->paginate($request->input('per_page', 20));
        return ApiResponse::paginated($items);
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('departments.manage');
        $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'branch_id'   => ['nullable', 'exists:branches,id'],
            'division_id' => ['nullable', 'exists:divisions,id'],
            'code'        => ['nullable', 'string', 'max:50'],
        ]);
        return ApiResponse::created(Department::create($request->all()), __('organization.department_created'));
    }

    public function show(string $id): JsonResponse
    {
        $this->authorize('departments.view');
        return ApiResponse::success(Department::findOrFail($id));
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $this->authorize('departments.manage');
        $dept = Department::findOrFail($id);
        $dept->update($request->all());
        return ApiResponse::success($dept, __('organization.department_updated'));
    }

    public function destroy(string $id): JsonResponse
    {
        $this->authorize('departments.manage');
        Department::findOrFail($id)->delete();
        return ApiResponse::success(null, __('organization.department_deleted'));
    }
}
