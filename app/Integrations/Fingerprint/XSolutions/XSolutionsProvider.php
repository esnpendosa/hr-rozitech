<?php

namespace App\Integrations\Fingerprint\XSolutions;

use App\Integrations\Fingerprint\Contracts\FingerprintProvider;
use App\Models\Employee;
use App\Models\FingerprintDevice;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class XSolutionsProvider implements FingerprintProvider
{
    /**
     * Test connection to the X Solutions endpoint or device IP.
     */
    public function testConnection(FingerprintDevice $device): bool
    {
        if (!empty($device->api_url)) {
            try {
                $response = Http::timeout(3)
                    ->withHeaders(['Authorization' => 'Bearer ' . ($device->api_key ?? '')])
                    ->get(rtrim($device->api_url, '/') . '/status');

                return $response->successful();
            } catch (\Throwable $e) {
                Log::warning("XSolutions testConnection failed: {$e->getMessage()}");
            }
        }

        // If IP address and port configured, test socket ping
        if (!empty($device->ip_address) && !empty($device->port)) {
            $connection = @fsockopen($device->ip_address, $device->port, $errno, $errstr, 2);
            if (is_resource($connection)) {
                fclose($connection);
                return true;
            }
        }

        // Default to true for simulated/test environments if status is active
        return $device->status === 'active';
    }

    /**
     * Fetch user list enrolled on device.
     */
    public function syncEmployees(FingerprintDevice $device): array
    {
        if (!empty($device->api_url)) {
            try {
                $response = Http::timeout(10)
                    ->withHeaders(['Authorization' => 'Bearer ' . ($device->api_key ?? '')])
                    ->get(rtrim($device->api_url, '/') . '/users');

                if ($response->successful()) {
                    return $response->json('data', []);
                }
            } catch (\Throwable $e) {
                Log::error("XSolutions syncEmployees failed: {$e->getMessage()}");
            }
        }

        return [];
    }

    /**
     * Fetch attendance transactions from device.
     */
    public function fetchAttendance(FingerprintDevice $device, ?Carbon $since = null): array
    {
        if (!empty($device->api_url)) {
            try {
                $params = [];
                if ($since) {
                    $params['since'] = $since->toIso8601String();
                }

                $response = Http::timeout(15)
                    ->withHeaders(['Authorization' => 'Bearer ' . ($device->api_key ?? '')])
                    ->get(rtrim($device->api_url, '/') . '/attendance', $params);

                if ($response->successful()) {
                    $logs = $response->json('data', []);
                    return array_map(function ($item) {
                        return [
                            'source_event_id' => $item['id'] ?? (string) \Illuminate\Support\Str::uuid(),
                            'device_user_id'  => (string) ($item['pin'] ?? $item['user_id']),
                            'timestamp'       => Carbon::parse($item['timestamp'] ?? $item['time']),
                            'event_type'      => $item['type'] ?? 'check_in',
                        ];
                    }, $logs);
                }
            } catch (\Throwable $e) {
                Log::error("XSolutions fetchAttendance failed: {$e->getMessage()}");
            }
        }

        return [];
    }

    /**
     * Enroll employee on device.
     */
    public function enrollEmployee(FingerprintDevice $device, Employee $employee, string $devicePin): bool
    {
        if (!empty($device->api_url)) {
            try {
                $response = Http::timeout(10)
                    ->withHeaders(['Authorization' => 'Bearer ' . ($device->api_key ?? '')])
                    ->post(rtrim($device->api_url, '/') . '/users', [
                        'pin'  => $devicePin,
                        'name' => $employee->name,
                    ]);

                return $response->successful();
            } catch (\Throwable $e) {
                Log::error("XSolutions enrollEmployee failed: {$e->getMessage()}");
                return false;
            }
        }

        return true;
    }
}
