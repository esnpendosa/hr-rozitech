<?php

namespace App\Jobs;

use App\Domain\Notification\Services\NotificationService;
use App\Models\Target;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Class CheckTargetRiskJob
 *
 * Menjalankan inspeksi berkala pada seluruh target kinerja aktif:
 * - Menghitung ulang metrik kecepatan harian (required daily rate)
 * - Mendeteksi level risiko ketercapaian ('at_risk', 'critical')
 * - Mengirimkan notifikasi peringatan dini kepada pelaksana dan supervisor
 */
class CheckTargetRiskJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function handle(NotificationService $notificationService): void
    {
        Log::info('Menjalankan CheckTargetRiskJob...');

        $activeTargets = Target::whereIn('status', ['created', 'assigned', 'active'])
            ->with(['assignments.employee.user', 'creator'])
            ->get();

        $warnedCount = 0;

        foreach ($activeTargets as $target) {
            $target->recalculateMetrics();

            if (in_array($target->risk_level, ['at_risk', 'critical'])) {
                $urgency = $target->risk_level === 'critical' ? 'KRITIS' : 'PERHATIAN';

                foreach ($target->assignments as $assignment) {
                    if ($assignment->employee?->user) {
                        $notificationService->send(
                            recipient: $assignment->employee->user,
                            type: 'target_risk',
                            title: "Peringatan Target {$urgency}: {$target->name}",
                            body: "Capaian target Anda saat ini ({$target->progress_percent}%) berisiko tidak selesai tepat waktu. Laju harian yang diperlukan: {$target->required_daily_rate} {$target->unit}/hari.",
                            data: ['target_id' => $target->id, 'risk_level' => $target->risk_level]
                        );
                    }
                }

                $warnedCount++;
            }
        }

        Log::info("CheckTargetRiskJob selesai. Total {$warnedCount} target berisiko terdeteksi dan dikirimi notifikasi.");
    }
}
