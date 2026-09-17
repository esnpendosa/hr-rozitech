<?php

namespace App\Http\Controllers\Api\V1\Task;

use App\Application\Services\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskChecklist;
use App\Models\TaskComment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class TaskChecklistAndCommentController
 *
 * Mengelola interaksi checklist dan thread percakapan/komentar di dalam tugas.
 */
class TaskChecklistAndCommentController extends Controller
{
    /**
     * Menambahkan item checklist baru pada tugas.
     */
    public function storeChecklist(Request $request, string $taskId): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;
        $task = Task::where('tenant_id', $tenantId)->findOrFail($taskId);

        $validated = $request->validate([
            'title'      => 'required|string|max:500',
            'sort_order' => 'nullable|integer',
        ]);

        $checklist = TaskChecklist::create([
            'tenant_id'    => $tenantId,
            'task_id'      => $task->id,
            'title'        => $validated['title'],
            'sort_order'   => $validated['sort_order'] ?? 0,
            'is_completed' => false,
        ]);

        $task->recalculateProgressFromChecklists();

        return ApiResponse::created($checklist, 'Item checklist berhasil ditambahkan');
    }

    /**
     * Memperbarui status selesai/belum suatu item checklist.
     */
    public function toggleChecklist(Request $request, string $taskId, string $checklistId): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;
        $task = Task::where('tenant_id', $tenantId)->findOrFail($taskId);
        $checklist = TaskChecklist::where('tenant_id', $tenantId)
            ->where('task_id', $task->id)
            ->findOrFail($checklistId);

        $isCompleted = !$checklist->is_completed;
        $checklist->update([
            'is_completed' => $isCompleted,
            'completed_by' => $isCompleted ? $request->user()->id : null,
            'completed_at' => $isCompleted ? now() : null,
        ]);

        // Perbarui kalkulasi persentase progres tugas
        $task->recalculateProgressFromChecklists();

        return ApiResponse::success([
            'checklist'        => $checklist,
            'progress_percent' => (float) $task->fresh()->progress_percent,
        ], 'Status checklist berhasil diperbarui');
    }

    /**
     * Menghapus item checklist.
     */
    public function destroyChecklist(Request $request, string $taskId, string $checklistId): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;
        $task = Task::where('tenant_id', $tenantId)->findOrFail($taskId);
        $checklist = TaskChecklist::where('tenant_id', $tenantId)
            ->where('task_id', $task->id)
            ->findOrFail($checklistId);

        $checklist->delete();
        $task->recalculateProgressFromChecklists();

        return ApiResponse::success(null, 'Item checklist berhasil dihapus');
    }

    /**
     * Mengambil seluruh komentar tugas.
     */
    public function getComments(Request $request, string $taskId): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;
        $task = Task::where('tenant_id', $tenantId)->findOrFail($taskId);

        $comments = TaskComment::where('task_id', $task->id)
            ->with('user')
            ->latest()
            ->paginate($request->integer('per_page', 20));

        return ApiResponse::paginated($comments);
    }

    /**
     * Menambahkan komentar pada tugas.
     */
    public function storeComment(Request $request, string $taskId): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;
        $task = Task::where('tenant_id', $tenantId)->findOrFail($taskId);

        $validated = $request->validate([
            'body' => 'required|string|max:2000',
        ]);

        $comment = TaskComment::create([
            'tenant_id' => $tenantId,
            'task_id'   => $task->id,
            'user_id'   => $request->user()->id,
            'body'      => $validated['body'],
        ]);

        $comment->load('user');

        return ApiResponse::created($comment, 'Komentar berhasil dipublikasikan');
    }
}
