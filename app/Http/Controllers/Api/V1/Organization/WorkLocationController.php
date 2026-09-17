<?php

namespace App\Http\Controllers\Api\V1\Organization;

use App\Application\Services\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\WorkLocation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WorkLocationController extends Controller
{
    /**
     * Display a listing of work locations.
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('work-locations.view');

        $query = WorkLocation::query();

        // Search by name, address, or city
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', '%' . $search . '%')
                    ->orWhere('address', 'ilike', '%' . $search . '%')
                    ->orWhere('city', 'ilike', '%' . $search . '%');
            });
        }

        // Filter by branch
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        // Filter by active status
        if ($request->filled('is_active')) {
            $query->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN));
        }

        $items = $query->with('branch:id,name')
            ->orderBy('name')
            ->paginate($request->input('per_page', 15));

        return ApiResponse::paginated($items);
    }

    /**
     * Store a newly created work location.
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize('work-locations.manage');

        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:100'],
            'code'          => ['nullable', 'string', 'max:20'],
            'branch_id'     => ['nullable', 'uuid', 'exists:branches,id'],
            'address'       => ['required', 'string'],
            'city'          => ['nullable', 'string', 'max:100'],
            'province'      => ['nullable', 'string', 'max:100'],
            'latitude'      => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'     => ['nullable', 'numeric', 'between:-180,180'],
            'radius_meters' => ['nullable', 'integer', 'min:1', 'max:50000'],
            'is_active'     => ['nullable', 'boolean'],
        ]);

        $workLocation = WorkLocation::create($validated);

        return ApiResponse::created($workLocation, __('organization.work_location_created'));
    }

    /**
     * Display the specified work location.
     */
    public function show(string $id): JsonResponse
    {
        $this->authorize('work-locations.view');

        $workLocation = WorkLocation::with('branch:id,name')->findOrFail($id);

        return ApiResponse::success($workLocation);
    }

    /**
     * Update the specified work location.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $this->authorize('work-locations.manage');

        $workLocation = WorkLocation::findOrFail($id);

        $validated = $request->validate([
            'name'          => ['sometimes', 'required', 'string', 'max:100'],
            'code'          => ['nullable', 'string', 'max:20'],
            'branch_id'     => ['nullable', 'uuid', 'exists:branches,id'],
            'address'       => ['sometimes', 'required', 'string'],
            'city'          => ['nullable', 'string', 'max:100'],
            'province'      => ['nullable', 'string', 'max:100'],
            'latitude'      => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'     => ['nullable', 'numeric', 'between:-180,180'],
            'radius_meters' => ['nullable', 'integer', 'min:1', 'max:50000'],
            'is_active'     => ['nullable', 'boolean'],
        ]);

        $workLocation->update($validated);

        return ApiResponse::success($workLocation, __('organization.work_location_updated'));
    }

    /**
     * Remove the specified work location (soft delete).
     */
    public function destroy(string $id): JsonResponse
    {
        $this->authorize('work-locations.manage');

        WorkLocation::findOrFail($id)->delete();

        return ApiResponse::success(null, __('organization.work_location_deleted'));
    }
}
