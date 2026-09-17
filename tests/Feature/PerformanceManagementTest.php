<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\PerformancePeriod;
use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Class PerformanceManagementTest
 *
 * Menguji mesin evaluasi performa komprehensif (Performance Engine):
 * - Konfigurasi 4 pilar bobot (KPI, Task, Target, Attendance)
 * - Eksekusi agregasi skor dan kalkulasi otomatis
 * - Penentuan predikat grade (A, B, C, D)
 */
class PerformanceManagementTest extends TestCase
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
            'name'   => 'Performance Tenant',
            'slug'   => 'perf-test-' . uniqid(),
            'status' => 'active',
        ]);

        $this->adminUser = User::create([
            'tenant_id' => $this->tenant->id,
            'name'      => 'HR Specialist',
            'email'     => 'perf-hr-' . uniqid() . '@example.com',
            'password'  => bcrypt('password'),
        ]);

        $employeeUser = User::create([
            'tenant_id' => $this->tenant->id,
            'name'      => 'Employee Star',
            'email'     => 'star-' . uniqid() . '@example.com',
            'password'  => bcrypt('password'),
        ]);

        $this->employee = Employee::create([
            'tenant_id'       => $this->tenant->id,
            'user_id'         => $employeeUser->id,
            'name'            => 'Employee Star',
            'employee_number' => 'STAR-' . rand(1000, 9999),
            'status'          => 'active',
            'join_date'       => now(),
            'employment_type' => 'permanent',
        ]);
    }

    /**
     * Uji alur pembobotan periode dan kalkulasi skor akhir.
     */
    public function test_can_configure_period_and_calculate_performance(): void
    {
        // 1. Buat konfigurasi periode (40% KPI, 20% Task, 20% Target, 20% Attendance)
        $periodResp = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson('/api/v1/performance/periods', [
                'name'              => 'Evaluasi Kinerja Q3 2026',
                'start_date'        => now()->startOfMonth()->toDateString(),
                'end_date'          => now()->endOfMonth()->toDateString(),
                'kpi_weight'        => 40,
                'task_weight'       => 20,
                'target_weight'     => 20,
                'attendance_weight' => 20,
            ]);

        $periodResp->assertStatus(201)
            ->assertJsonPath('data.name', 'Evaluasi Kinerja Q3 2026');

        $periodId = $periodResp->json('data.id');

        // 2. Jalankan kalkulasi performa
        $calcResp = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson('/api/v1/performance/calculate', [
                'period_id' => $periodId,
            ]);

        $calcResp->assertStatus(200)
            ->assertJsonPath('data.total_calculated', 1);

        // 3. Verifikasi hasil tersimpan di database
        $this->assertDatabaseHas('performance_results', [
            'tenant_id'   => $this->tenant->id,
            'period_id'   => $periodId,
            'employee_id' => $this->employee->id,
        ]);
    }
}
