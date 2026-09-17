<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Project;
use App\Models\Task;
use App\Support\TenantContext;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Class TaskWebController
 *
 * Menampilkan halaman antarmuka web untuk manajemen tugas (Tasks) dan proyek (Projects):
 * - Tampilan Tabel Tugas dengan Filter & Search
 * - Tampilan Kanban Board (berdasarkan status pengerjaan)
 * - Tampilan Detail Tugas (Checklist, Komentar, Evidence, Histori)
 * - Tampilan Manajemen Proyek
 */
class TaskWebController extends Controller
{
    public function index(Request $request): View
    {
        $tenantId = TenantContext::getTenantId() ?? $request->user()?->tenant_id;

        $tasks = Task::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->with(['project', 'assignees.employee.user'])
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->when($request->filled('priority'), fn($q) => $q->where('priority', $request->priority))
            ->when($request->filled('project_id'), fn($q) => $q->where('project_id', $request->project_id))
            ->latest()
            ->paginate(15);

        $projects = Project::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))->get();

        return view('tasks.index', compact('tasks', 'projects'));
    }

    public function kanban(Request $request): View
    {
        $tenantId = TenantContext::getTenantId() ?? $request->user()?->tenant_id;

        $columns = [
            'assigned'    => 'Ditugaskan',
            'in_progress' => 'Sedang Dikerjakan',
            'waiting'     => 'Menunggu Review',
            'review'      => 'Dalam Review',
            'completed'   => 'Selesai',
        ];

        $tasks = Task::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->with(['project', 'assignees.employee.user'])
            ->get()
            ->groupBy('status');

        return view('tasks.kanban', compact('columns', 'tasks'));
    }

    public function show(Request $request, string $id): View
    {
        $tenantId = TenantContext::getTenantId() ?? $request->user()?->tenant_id;

        $task = Task::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->with([
                'project',
                'assignees.employee.user',
                'checklists',
                'evidences.uploader',
                'comments.user',
                'statusHistories.changer',
            ])
            ->findOrFail($id);

        return view('tasks.show', compact('task'));
    }

    public function projects(Request $request): View
    {
        $tenantId = TenantContext::getTenantId() ?? $request->user()?->tenant_id;

        $projects = Project::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->with(['manager', 'creator'])
            ->withCount(['tasks', 'members'])
            ->latest()
            ->paginate(12);

        return view('projects.index', compact('projects'));
    }

    public function showProject(Request $request, string $id): View
    {
        $tenantId = TenantContext::getTenantId() ?? $request->user()?->tenant_id;

        $project = Project::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->with([
                'manager',
                'members.employee.user',
                'tasks' => fn($q) => $q->latest(),
            ])
            ->findOrFail($id);

        return view('projects.show', compact('project'));
    }
}
