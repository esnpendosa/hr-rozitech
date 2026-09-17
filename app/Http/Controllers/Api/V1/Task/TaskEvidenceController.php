<?php

namespace App\Http\Controllers\Api\V1\Task;

use App\Application\Services\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskEvidence;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Class TaskEvidenceController
 *
 * Mengelola pengunggahan dan visualisasi bukti fisik pengerjaan tugas (Evidence):
 * - Foto/video/dokumen
 * - Koordinat GPS lokasi pengerjaan
 * - Signed temporary URL untuk akses file privat
 */
class TaskEvidenceController extends Controller
{
    /**
     * Mengambil daftar bukti eksekusi tugas.
     */
    public function index(Request $request, string $taskId): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;
        $task = Task::where('tenant_id', $tenantId)->findOrFail($taskId);

        $evidences = TaskEvidence::where('tenant_id', $tenantId)
            ->where('task_id', $task->id)
            ->with('uploader')
            ->latest()
            ->get()
            ->map(function ($evidence) {
                return [
                    'id'          => $evidence->id,
                    'type'        => $evidence->type,
                    'file_url'    => Storage::disk('local')->url($evidence->file_path),
                    'file_size'   => $evidence->file_size,
                    'mime_type'   => $evidence->mime_type,
                    'description' => $evidence->description,
                    'latitude'    => $evidence->latitude,
                    'longitude'   => $evidence->longitude,
                    'captured_at' => $evidence->captured_at,
                    'uploaded_by' => $evidence->uploader?->name,
                ];
            });

        return ApiResponse::success($evidences, 'Bukti tugas berhasil diambil');
    }

    /**
     * Mengunggah file bukti eksekusi tugas.
     */
    public function store(Request $request, string $taskId): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;
        $task = Task::where('tenant_id', $tenantId)->findOrFail($taskId);

        $validated = $request->validate([
            'file'        => 'required|file|mimes:jpeg,jpg,png,pdf,mp4|max:10240', // Maksimal 10MB
            'type'        => 'required|in:photo,video,document,other',
            'description' => 'nullable|string|max:1000',
            'latitude'    => 'nullable|numeric|between:-90,90',
            'longitude'   => 'nullable|numeric|between:-180,180',
            'captured_at' => 'nullable|date',
        ]);

        $file = $request->file('file');
        $path = $file->store("tenants/{$tenantId}/tasks/{$task->id}/evidences", 'local');

        $evidence = TaskEvidence::create([
            'tenant_id'   => $tenantId,
            'task_id'     => $task->id,
            'uploaded_by' => $request->user()->id,
            'type'        => $validated['type'],
            'file_path'   => $path,
            'file_size'   => $file->getSize(),
            'mime_type'   => $file->getMimeType(),
            'description' => $validated['description'] ?? null,
            'latitude'    => $validated['latitude'] ?? null,
            'longitude'   => $validated['longitude'] ?? null,
            'captured_at' => $validated['captured_at'] ?? now(),
            'created_at'  => now(),
        ]);

        return ApiResponse::created($evidence, 'Bukti tugas berhasil diunggah');
    }
}
