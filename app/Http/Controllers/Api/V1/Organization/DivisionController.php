<?php

namespace App\Http\Controllers\Api\V1\Organization;

use App\Application\Services\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Division;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DivisionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('organization.view');
        $items = Division::when($request->branch_id, fn($q) => $q->where('branch_id', $request->branch_id))
            ->orderBy('name')
            ->paginate($request->input('per_page', 20));
        return ApiResponse::paginated($items);
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('organization.manage');
        $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'branch_id' => ['nullable', 'exists:branches,id'],
            'code'      => ['nullable', 'string', 'max:50'],
        ]);
        return ApiResponse::created(Division::create($request->all()), __('organization.division_created'));
    }

    public function show(string $id): JsonResponse
    {
        $this->authorize('organization.view');
        return ApiResponse::success(Division::findOrFail($id));
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $this->authorize('organization.manage');
        $div = Division::findOrFail($id);
        $div->update($request->all());
        return ApiResponse::success($div, __('organization.division_updated'));
    }

    public function destroy(string $id): JsonResponse
    {
        $this->authorize('organization.manage');
        Division::findOrFail($id)->delete();
        return ApiResponse::success(null, __('organization.division_deleted'));
    }
}
