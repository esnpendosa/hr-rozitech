<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\Target;
use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Class TargetManagementTest
 *
 * Menguji fungsionalitas manajemen target kinerja:
 * - CRUD Target
 * - Input progres berkala & penambahan actual_value
 * - Kalkulasi dinamis persentase progres, sisa nilai, dan laju harian
 * - Deteksi status risiko ketercapaian (risk_level)
 */
class TargetManagementTest extends TestCase
{
    use DatabaseTransactions;

    private Tenant $tenant;
    private User $adminUser;
    private Employee $employee;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);

        $this->tenant = Tenant::create([
            'name'   => 'Target Test Tenant',
            'slug'   => 'target-test-' . uniqid(),
            'status' => 'active',
        ]);

        $this->adminUser = User::create([
            'tenant_id' => $this->tenant->id,
            'name'      => 'Sales Manager',
            'email'     => 'sales-mgr-' . uniqid() . '@example.com',
            'password'  => bcrypt('password'),
        ]);

        $employeeUser = User::create([
            'tenant_id' => $this->tenant->id,
            'name'      => 'Account Executive',
            'email'     => 'ae-' . uniqid() . '@example.com',
            'password'  => bcrypt('password'),
        ]);

        $this->employee = Employee::create([
            'tenant_id'       => $this->tenant->id,
            'user_id'         => $employeeUser->id,
            'name'            => 'Account Executive',
            'employee_number' => 'AE-' . rand(1000, 9999),
            'status'          => 'active',
            'join_date'       => now(),
            'employment_type' => 'permanent',
        ]);
    }

    /**
     * Uji alur pembuatan target kuantitatif dan penetapan pegawai.
     */
    public function test_can_create_and_assign_target(): void
    {
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson('/api/v1/targets', [
                'name'         => 'Akuisisi 50 Mitra Korporat Q3',
                'metric'       => 'Jumlah Klien Baru',
                'target_value' => 50,
                'unit'         => 'klien',
                'period'       => 'quarterly',
                'start_date'   => now()->toDateString(),
                'end_date'     => now()->addMonths(3)->toDateString(),
                'assignee_ids' => [$this->employee->id],
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'Akuisisi 50 Mitra Korporat Q3')
            ->assertJsonPath('data.target_value', '50.00')
            ->assertJsonPath('data.risk_level', 'on_track');

        $targetId = $response->json('data.id');

        $this->assertDatabaseHas('target_assignments', [
            'target_id'   => $targetId,
            'employee_id' => $this->employee->id,
        ]);
    }

    /**
     * Uji pencatatan progres bertahap dan auto-update persentase ketercapaian.
     */
    public function test_can_record_progress_and_update_metrics(): void
    {
        $target = Target::create([
            'tenant_id'    => $this->tenant->id,
            'name'         => 'Penjualan Paket Software',
            'metric'       => 'Revenue',
            'target_value' => 100000000,
            'unit'         => 'IDR',
            'period'       => 'monthly',
            'start_date'   => now()->startOfMonth()->toDateString(),
            'end_date'     => now()->endOfMonth()->toDateString(),
            'created_by'   => $this->adminUser->id,
        ]);

        $target->recalculateMetrics();

        // Input progres capaian: 40.000.000 (40%)
        $progressResp = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson("/api/v1/targets/{$target->id}/progress", [
                'value' => 40000000,
                'notes' => 'Closing deal PT Sumber Makmur',
            ]);

        $progressResp->assertStatus(200)
            ->assertJsonPath('data.actual_value', '40000000.00')
            ->assertJsonPath('data.progress_percent', '40.00')
            ->assertJsonPath('data.remaining_value', '60000000.00');

        $this->assertDatabaseHas('target_progress', [
            'target_id' => $target->id,
            'value'     => 40000000,
        ]);
    }

    public function test_can_create_target_from_web_interface(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->post('/targets', [
                'name'         => 'Target Web Test',
                'metric'       => 'Pelanggan Baru',
                'target_value' => 50,
                'unit'         => 'orang',
                'period'       => 'monthly',
                'start_date'   => now()->startOfMonth()->toDateString(),
                'end_date'     => now()->endOfMonth()->toDateString(),
                'notes'        => 'Catatan target web',
            ]);

        $response->assertRedirect('/targets');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('targets', [
            'tenant_id' => $this->tenant->id,
            'name'      => 'Target Web Test',
        ]);
    }
}
