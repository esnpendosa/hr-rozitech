<?php

namespace App\Http\Controllers\Api\V1\Organization;

use App\Application\Services\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Position;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('positions.view');
        $items = Position::when($request->department_id, fn($q) => $q->where('department_id', $request->department_id))
            ->when($request->search, fn($q) => $q->where('name', 'ilike', '%'.$request->search.'%'))
            ->orderBy('level')->orderBy('name')
            ->paginate($request->input('per_page', 20));
        return ApiResponse::paginated($items);
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('positions.manage');
        $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'code'          => ['nullable', 'string', 'max:50'],
            'level'         => ['nullable', 'integer', 'min:1'],
        ]);
        return ApiResponse::created(Position::create($request->all()), __('organization.position_created'));
    }

    public function show(string $id): JsonResponse
    {
        $this->authorize('positions.view');
        return ApiResponse::success(Position::findOrFail($id));
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $this->authorize('positions.manage');
        $pos = Position::findOrFail($id);
        $pos->update($request->all());
        return ApiResponse::success($pos, __('organization.position_updated'));
    }

    public function destroy(string $id): JsonResponse
    {
        $this->authorize('positions.manage');
        Position::findOrFail($id)->delete();
        return ApiResponse::success(null, __('organization.position_deleted'));
    }
}
