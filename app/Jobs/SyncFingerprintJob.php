<?php

namespace App\Jobs;

use App\Domain\Fingerprint\Services\FingerprintSyncService;
use App\Models\FingerprintDevice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncFingerprintJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 300;

    public function __construct(
        public ?string $deviceId = null,
        public ?string $tenantId = null
    ) {}

    public function handle(FingerprintSyncService $syncService): void
    {
        Log::info('Starting SyncFingerprintJob', [
            'device_id' => $this->deviceId,
            'tenant_id' => $this->tenantId,
        ]);

        if ($this->deviceId) {
            $device = FingerprintDevice::find($this->deviceId);
            if ($device) {
                $syncService->syncDevice($device);
            } else {
                Log::warning("Fingerprint device [{$this->deviceId}] not found.");
            }
            return;
        }

        $query = FingerprintDevice::where('is_active', true);
        if ($this->tenantId) {
            $query->where('tenant_id', $this->tenantId);
        }

        $devices = $query->get();
        foreach ($devices as $device) {
            try {
                $syncService->syncDevice($device);
            } catch (\Throwable $e) {
                Log::error("Failed to sync device {$device->id}: " . $e->getMessage());
            }
        }
    }
}
