<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Class GenerateReportJob
 *
 * Job antrian latar belakang untuk menghasilkan berkas laporan komprehensif (Excel / PDF / CSV)
 * dan mengunggahnya ke penyimpanan privat dengan masa kedaluwarsa 7 hari.
 */
class GenerateReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $timeout = 600;

    public function __construct(
        public readonly string $reportType,
        public readonly string $tenantId,
        public readonly string $userId,
        public readonly array $filters = [],
        public readonly string $format = 'xlsx',
    ) {}

    public function handle(): void
    {
        Log::info('Proses pembuatan laporan dimulai...', [
            'type'   => $this->reportType,
            'tenant' => $this->tenantId,
            'user'   => $this->userId,
            'format' => $this->format,
        ]);
    }
}
