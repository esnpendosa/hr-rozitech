<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Employee;
use App\Models\FingerprintDevice;
use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Class FingerprintIntegrationTest
 *
 * Verifikasi integrasi sistem absensi biometrik:
 * - Registrasi dan listing perangkat fingerprint
 * - Pengujian koneksi mesin (heartbeat/health check)
 * - Pemetaan PIN/Enroll ID mesin ke data pegawai
 * - Trigger sinkronisasi log presensi (idempotent)
 */
class FingerprintIntegrationTest extends TestCase
{
    use DatabaseTransactions;

    private Tenant $tenant;
    private User $user;
    private Branch $branch;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);

        // Buat tenant uji dengan slug unik
        $this->tenant = Tenant::create([
            'name'   => 'Biometric Test Tenant',
            'slug'   => 'bio-test-' . uniqid(),
            'status' => 'active',
        ]);

        $company = \App\Models\Company::create([
            'tenant_id' => $this->tenant->id,
            'name'      => 'Biometric Corp',
        ]);

        // Akun HR/Admin untuk pengujian
        $this->user = User::create([
            'tenant_id' => $this->tenant->id,
            'name'      => 'Biometric Manager',
            'email'     => 'bio-manager-' . uniqid() . '@example.com',
            'password'  => bcrypt('password'),
        ]);

        // Buat cabang untuk asosiasi perangkat
        $this->branch = Branch::create([
            'tenant_id'     => $this->tenant->id,
            'company_id'    => $company->id,
            'name'          => 'HQ Jakarta',
            'code'          => 'HQ-' . uniqid(),
            'latitude'      => -6.2088,
            'longitude'     => 106.8456,
            'radius_meters' => 100,
            'is_active'     => true,
        ]);
    }

    /**
     * Uji alur pendaftaran perangkat fingerprint baru dan pembacaan daftarnya.
     */
    public function test_can_register_and_list_fingerprint_devices(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/fingerprint/devices', [
                'branch_id'     => $this->branch->id,
                'device_name'   => 'ZKTeco Main Lobby',
                'device_sn'     => 'ZK-SN-998877',
                'ip_address'    => '192.168.1.201',
                'port'          => 4370,
                'protocol'      => 'tcp',
                'location_name' => 'Lantai 1 Gerbang Depan',
                'is_active'     => true,
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'ZKTeco Main Lobby');

        $list = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/fingerprint/devices');

        $list->assertStatus(200)
            ->assertJsonPath('data.0.serial_number', 'ZK-SN-998877');
    }

    /**
     * Uji pengecekan ping/koneksi status mesin fisik.
     */
    public function test_can_test_device_connection(): void
    {
        $device = FingerprintDevice::create([
            'tenant_id'     => $this->tenant->id,
            'branch_id'     => $this->branch->id,
            'name'          => 'Solution Fingerprint Pro',
            'serial_number' => 'SOL-' . uniqid(),
            'ip_address'    => '192.168.1.100',
            'port'          => 80,
            'provider'      => 'xsolutions',
            'status'        => 'active',
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/v1/fingerprint/devices/{$device->id}/test");

        $response->assertStatus(200)
            ->assertJsonPath('data.status', 'active');

        $this->assertDatabaseHas('fingerprint_devices', [
            'id'     => $device->id,
            'status' => 'active',
        ]);
    }

    /**
     * Uji mapping PIN mesin ke profil Employee.
     */
    public function test_can_map_employee_to_device_pin(): void
    {
        $device = FingerprintDevice::create([
            'tenant_id'     => $this->tenant->id,
            'branch_id'     => $this->branch->id,
            'name'          => 'Solution Fingerprint Pro',
            'serial_number' => 'SOL-' . uniqid(),
            'provider'      => 'xsolutions',
            'status'        => 'active',
        ]);

        $employeeUser = User::create([
            'tenant_id' => $this->tenant->id,
            'name'      => 'John Doe',
            'email'     => 'john-' . uniqid() . '@example.com',
            'password'  => bcrypt('password'),
        ]);

        $employee = Employee::create([
            'tenant_id'       => $this->tenant->id,
            'user_id'         => $employeeUser->id,
            'branch_id'       => $this->branch->id,
            'name'            => 'John Doe',
            'employee_number' => 'EMP-' . rand(1000, 9999),
            'status'          => 'active',
            'join_date'       => now(),
            'employment_type' => 'permanent',
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/fingerprint/mappings', [
                'employee_id'           => $employee->id,
                'fingerprint_device_id' => $device->id,
                'device_pin'            => '1001',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.external_id', '1001');

        $this->assertDatabaseHas('fingerprint_mappings', [
            'tenant_id'   => $this->tenant->id,
            'device_id'   => $device->id,
            'employee_id' => $employee->id,
            'external_id' => '1001',
        ]);
    }

    /**
     * Uji trigger sinkronisasi dan audit log rekaman sinkronisasi.
     */
    public function test_can_trigger_sync_and_view_logs(): void
    {
        $device = FingerprintDevice::create([
            'tenant_id'     => $this->tenant->id,
            'branch_id'     => $this->branch->id,
            'name'          => 'Solution Fingerprint Pro',
            'serial_number' => 'SOL-' . uniqid(),
            'provider'      => 'xsolutions',
            'status'        => 'active',
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/v1/fingerprint/devices/{$device->id}/sync", [
                'async' => false,
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.status', 'completed');

        $logs = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/v1/fingerprint/devices/{$device->id}/logs");

        $logs->assertStatus(200)
            ->assertJsonPath('data.0.device_id', $device->id);
    }
}
