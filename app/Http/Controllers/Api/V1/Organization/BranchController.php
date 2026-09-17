<?php

namespace App\Http\Controllers\Api\V1\Organization;

use App\Application\Services\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('branches.view');
        $branches = Branch::when($request->search, fn($q) => $q->where('name', 'ilike', '%' . $request->search . '%'))
            ->when($request->active_only, fn($q) => $q->where('is_active', true))
            ->orderBy('name')
            ->paginate($request->input('per_page', 20));
        return ApiResponse::paginated($branches);
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('branches.manage');
        $request->validate([
            'company_id' => ['required', 'exists:companies,id'],
            'name'       => ['required', 'string', 'max:255'],
            'code'       => ['nullable', 'string', 'max:50'],
            'address'    => ['nullable', 'string'],
            'city'       => ['nullable', 'string', 'max:100'],
            'province'   => ['nullable', 'string', 'max:100'],
            'latitude'   => ['nullable', 'numeric'],
            'longitude'  => ['nullable', 'numeric'],
            'phone'      => ['nullable', 'string', 'max:50'],
        ]);
        $branch = Branch::create($request->all());
        return ApiResponse::created($branch, __('organization.branch_created'));
    }

    public function show(string $id): JsonResponse
    {
        $this->authorize('branches.view');
        $branch = Branch::findOrFail($id);
        return ApiResponse::success($branch);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $this->authorize('branches.manage');
        $branch = Branch::findOrFail($id);
        $request->validate([
            'name'      => ['sometimes', 'string', 'max:255'],
            'code'      => ['nullable', 'string', 'max:50'],
            'address'   => ['nullable', 'string'],
            'city'      => ['nullable', 'string', 'max:100'],
            'province'  => ['nullable', 'string', 'max:100'],
            'latitude'  => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'is_active' => ['sometimes', 'boolean'],
        ]);
        $branch->update($request->all());
        return ApiResponse::success($branch, __('organization.branch_updated'));
    }

    public function destroy(string $id): JsonResponse
    {
        $this->authorize('branches.manage');
        Branch::findOrFail($id)->delete();
        return ApiResponse::success(null, __('organization.branch_deleted'));
    }
}
