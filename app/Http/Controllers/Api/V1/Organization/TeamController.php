<?php

namespace App\Http\Controllers\Api\V1\Organization;

use App\Application\Services\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('teams.view');
        $items = Team::when($request->department_id, fn($q) => $q->where('department_id', $request->department_id))
            ->when($request->search, fn($q) => $q->where('name', 'ilike', '%'.$request->search.'%'))
            ->orderBy('name')
            ->paginate($request->input('per_page', 20));
        return ApiResponse::paginated($items);
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('teams.manage');
        $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'code'          => ['nullable', 'string', 'max:50'],
        ]);
        return ApiResponse::created(Team::create($request->all()), __('organization.team_created'));
    }

    public function show(string $id): JsonResponse
    {
        $this->authorize('teams.view');
        return ApiResponse::success(Team::findOrFail($id));
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $this->authorize('teams.manage');
        $team = Team::findOrFail($id);
        $team->update($request->all());
        return ApiResponse::success($team, __('organization.team_updated'));
    }

    public function destroy(string $id): JsonResponse
    {
        $this->authorize('teams.manage');
        Team::findOrFail($id)->delete();
        return ApiResponse::success(null, __('organization.team_deleted'));
    }
}
