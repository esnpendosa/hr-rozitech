<?php

namespace App\Jobs;

use App\Domain\Notification\Services\NotificationService;
use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Class CheckOverdueTasksJob
 *
 * Job terjadwal (hourly) untuk:
 * 1. Mendeteksi tugas yang melewati tenggat waktu (due_at < now) dan belum selesai
 * 2. Mengubah statusnya menjadi 'overdue'
 * 3. Mengirimkan notifikasi peringatan keterlambatan tugas ke assignees dan atasan
 */
class CheckOverdueTasksJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function handle(NotificationService $notificationService): void
    {
        Log::info('Menjalankan CheckOverdueTasksJob...');

        // Ambil tugas yang melewati batas waktu namun belum 'completed', 'cancelled', atau 'overdue'
        $overdueTasks = Task::whereNotIn('status', ['completed', 'cancelled', 'overdue'])
            ->whereNotNull('due_at')
            ->where('due_at', '<', now())
            ->with(['assignees.employee.user', 'creator'])
            ->get();

        $count = 0;

        foreach ($overdueTasks as $task) {
            $task->update([
                'status' => 'overdue',
            ]);

            // Catat history
            $task->statusHistories()->create([
                'tenant_id'   => $task->tenant_id,
                'from_status' => $task->getOriginal('status'),
                'to_status'   => 'overdue',
                'notes'       => 'Otomatis diubah menjadi terlambat (overdue) oleh sistem.',
                'created_at'  => now(),
            ]);

            // Kirim notifikasi ke pelaksana
            foreach ($task->assignees as $assignee) {
                if ($assignee->employee?->user) {
                    $notificationService->sendNotification(
                        user: $assignee->employee->user,
                        title: 'Tugas Melewati Batas Waktu (Overdue)',
                        message: "Tugas '{$task->title}' telah melewati batas waktu yang ditentukan.",
                        type: 'task_overdue',
                        data: ['task_id' => $task->id]
                    );
                }
            }

            $count++;
        }

        Log::info("CheckOverdueTasksJob selesai. Total {$count} tugas diperbarui ke status overdue.");
    }
}
