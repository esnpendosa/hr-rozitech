<?php

namespace App\Domain\Fingerprint\Services;

use App\Domain\Attendance\Services\AttendanceService;
use App\Integrations\Fingerprint\Contracts\FingerprintProvider;
use App\Models\AttendanceEvent;
use App\Models\AttendanceRecord;
use App\Models\FingerprintDevice;
use App\Models\FingerprintDeviceUser;
use App\Models\FingerprintMapping;
use App\Models\FingerprintSyncError;
use App\Models\FingerprintSyncRun;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FingerprintSyncService
{
    public function __construct(
        private readonly FingerprintProvider $provider,
        private readonly AttendanceService $attendanceService,
    ) {}

    /**
     * Run sync transaction pull for a specific device.
     */
    public function syncDevice(FingerprintDevice $device): FingerprintSyncRun
    {
        $syncRun = FingerprintSyncRun::create([
            'tenant_id'         => $device->tenant_id,
            'device_id'         => $device->id,
            'status'            => 'running',
            'started_at'        => now(),
            'sync_from'         => $device->last_sync_at,
            'records_fetched'   => 0,
            'records_processed' => 0,
            'records_failed'    => 0,
        ]);

        try {
            $logs = $this->provider->fetchAttendance($device, $device->last_sync_at);
            $fetchedCount = count($logs);
            $processedCount = 0;
            $failedCount = 0;

            foreach ($logs as $log) {
                $pin = $log['device_user_id'];
                $eventTime = Carbon::parse($log['timestamp']);
                $sourceEventId = $log['source_event_id'];

                // 1. Resolve employee
                $mapping = FingerprintMapping::where('device_id', $device->id)
                    ->where('external_id', $pin)
                    ->where('is_active', true)
                    ->first();

                $employeeId = $mapping?->employee_id;

                if (!$employeeId) {
                    $deviceUser = FingerprintDeviceUser::where('device_id', $device->id)
                        ->where('device_user_id', $pin)
                        ->first();
                    $employeeId = $deviceUser?->employee_id;
                }

                if (!$employeeId) {
                    $failedCount++;
                    FingerprintSyncError::create([
                        'tenant_id'     => $device->tenant_id,
                        'sync_run_id'   => $syncRun->id,
                        'device_id'     => $device->id,
                        'employee_id'   => null,
                        'raw_data'      => $log,
                        'error_message' => "Employee not found for device PIN: {$pin}",
                    ]);
                    continue;
                }

                // 2. Protect from duplicate events (Idempotency)
                $existingEvent = AttendanceEvent::where('tenant_id', $device->tenant_id)
                    ->where('employee_id', $employeeId)
                    ->where('source_event_id', $sourceEventId)
                    ->first();

                if ($existingEvent) {
                    continue; // Already processed
                }

                DB::transaction(function () use (
                    $device,
                    $employeeId,
                    $sourceEventId,
                    $eventTime,
                    &$processedCount
                ) {
                    $dateStr = $eventTime->toDateString();

                    // Create AttendanceEvent
                    AttendanceEvent::create([
                        'tenant_id'        => $device->tenant_id,
                        'employee_id'      => $employeeId,
                        'event_time'       => $eventTime,
                        'event_type'       => 'punch',
                        'source'           => 'fingerprint',
                        'source_device_id' => $device->id,
                        'source_event_id'  => $sourceEventId,
                    ]);

                    // Update or create AttendanceRecord
                    $record = AttendanceRecord::where('tenant_id', $device->tenant_id)
                        ->where('employee_id', $employeeId)
                        ->whereDate('attendance_date', $dateStr)
                        ->first();

                    if (!$record) {
                        $record = AttendanceRecord::create([
                            'tenant_id'        => $device->tenant_id,
                            'employee_id'      => $employeeId,
                            'attendance_date'  => $dateStr,
                            'check_in_at'      => $eventTime,
                            'status'           => 'present',
                            'source'           => 'fingerprint',
                            'source_device_id' => $device->id,
                        ]);
                    } else {
                        // If record exists and this punch is later than check-in, set as check-out
                        if ($record->check_in_at && $eventTime->gt(Carbon::parse($record->check_in_at))) {
                            $record->check_out_at = $eventTime;
                            $record->save();
                        }
                    }

                    $processedCount++;
                });
            }

            $status = ($failedCount > 0 && $processedCount > 0)
                ? 'partial'
                : ($failedCount > 0 && $processedCount === 0 && $fetchedCount > 0 ? 'failed' : 'completed');

            $syncRun->update([
                'status'            => $status,
                'completed_at'      => now(),
                'sync_to'           => now(),
                'records_fetched'   => $fetchedCount,
                'records_processed' => $processedCount,
                'records_failed'    => $failedCount,
            ]);

            $device->update([
                'last_sync_at' => now(),
                'last_seen_at' => now(),
            ]);

        } catch (\Throwable $e) {
            Log::error("Fingerprint sync failed: {$e->getMessage()}");
            $syncRun->update([
                'status'        => 'failed',
                'completed_at'  => now(),
                'error_message' => $e->getMessage(),
            ]);
        }

        return $syncRun;
    }
}
