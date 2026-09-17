<?php

namespace App\Integrations\Fingerprint\Contracts;

use App\Models\Employee;
use App\Models\FingerprintDevice;
use Carbon\Carbon;

interface FingerprintProvider
{
    /**
     * Test connection to the fingerprint hardware or API.
     */
    public function testConnection(FingerprintDevice $device): bool;

    /**
     * Fetch user list / employees enrolled on the device.
     * Returns array of ['device_user_id' => string, 'name' => string]
     */
    public function syncEmployees(FingerprintDevice $device): array;

    /**
     * Fetch attendance transaction logs from device.
     * Returns array of:
     * [
     *   [
     *      'source_event_id' => string,
     *      'device_user_id'  => string,
     *      'timestamp'       => Carbon,
     *      'event_type'      => 'check_in'|'check_out'|'unknown',
     *   ]
     * ]
     */
    public function fetchAttendance(FingerprintDevice $device, ?Carbon $since = null): array;

    /**
     * Register / enroll employee on the device.
     */
    public function enrollEmployee(FingerprintDevice $device, Employee $employee, string $devicePin): bool;
}
