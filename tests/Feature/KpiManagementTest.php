<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\KpiTemplate;
use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Class KpiManagementTest
 *
 * Menguji fungsionalitas mesin KPI:
 * - Pembuatan KPI Template dengan validasi bobot metrik 100%
 * - Penugasan KPI ke pegawai
 * - Penginputan nilai capaian aktual & pembobotan skor otomatis
 */
class KpiManagementTest extends TestCase
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
            'name'   => 'KPI Test Tenant',
            'slug'   => 'kpi-test-' . uniqid(),
            'status' => 'active',
        ]);

        $this->adminUser = User::create([
            'tenant_id' => $this->tenant->id,
            'name'      => 'HR Director',
            'email'     => 'hr-dir-' . uniqid() . '@example.com',
            'password'  => bcrypt('password'),
        ]);

        $employeeUser = User::create([
            'tenant_id' => $this->tenant->id,
            'name'      => 'Staff Operation',
            'email'     => 'staff-' . uniqid() . '@example.com',
            'password'  => bcrypt('password'),
        ]);

        $this->employee = Employee::create([
            'tenant_id'       => $this->tenant->id,
            'user_id'         => $employeeUser->id,
            'name'            => 'Staff Operation',
            'employee_number' => 'STF-' . rand(1000, 9999),
            'status'          => 'active',
            'join_date'       => now(),
            'employment_type' => 'permanent',
        ]);
    }

    /**
     * Uji pembuatan template KPI dan validasi total bobot wajib 100%.
     */
    public function test_can_create_kpi_template_with_strict_weights(): void
    {
        // 1. Bobot tidak 100% (40 + 40 = 80%) -> Harus ditolak 422
        $invalidResp = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson('/api/v1/kpi/templates', [
                'name'    => 'KPI Divisi Operasional',
                'period'  => 'monthly',
                'metrics' => [
                    [
                        'name'           => 'Tingkat Kehadiran',
                        'weight'         => 40,
                        'target_value'   => 95,
                        'scoring_method' => 'higher_is_better',
                    ],
                    [
                        'name'           => 'Penyelesaian Tiket',
                        'weight'         => 40,
                        'target_value'   => 50,
                        'scoring_method' => 'higher_is_better',
                    ],
                ],
            ]);

        $invalidResp->assertStatus(422);

        // 2. Bobot pas 100% (60 + 40 = 100%) -> Harus sukses 201
        $validResp = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson('/api/v1/kpi/templates', [
                'name'    => 'KPI Divisi Operasional',
                'period'  => 'monthly',
                'metrics' => [
                    [
                        'name'           => 'Tingkat Kehadiran',
                        'weight'         => 60,
                        'target_value'   => 100,
                        'scoring_method' => 'higher_is_better',
                    ],
                    [
                        'name'           => 'Penyelesaian Tiket',
                        'weight'         => 40,
                        'target_value'   => 50,
                        'scoring_method' => 'higher_is_better',
                    ],
                ],
            ]);

        $validResp->assertStatus(201)
            ->assertJsonPath('data.name', 'KPI Divisi Operasional');
    }

    /**
     * Uji penugasan template ke pegawai dan penginputan nilai aktual beserta skor akhir.
     */
    public function test_can_assign_kpi_and_submit_actuals(): void
    {
        $templateResp = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson('/api/v1/kpi/templates', [
                'name'    => 'KPI Customer Service',
                'period'  => 'monthly',
                'metrics' => [
                    [
                        'name'           => 'Jumlah Panggilan Terlayani',
                        'weight'         => 50,
                        'target_value'   => 200,
                        'scoring_method' => 'higher_is_better',
                    ],
                    [
                        'name'           => 'Skor CSAT',
                        'weight'         => 50,
                        'target_value'   => 5,
                        'scoring_method' => 'higher_is_better',
                    ],
                ],
            ]);

        $templateId = $templateResp->json('data.id');
        $metric1Id = $templateResp->json('data.metrics.0.id');
        $metric2Id = $templateResp->json('data.metrics.1.id');

        // Tugaskan ke pegawai
        $assignResp = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson('/api/v1/kpi/assignments', [
                'template_id'  => $templateId,
                'employee_id'  => $this->employee->id,
                'period_start' => now()->startOfMonth()->toDateString(),
                'period_end'   => now()->endOfMonth()->toDateString(),
            ]);

        $assignResp->assertStatus(201);
        $assignmentId = $assignResp->json('data.id');

        // Input capaian aktual: 200 panggilan (100% skor -> 50 bobot), 4 CSAT (80% skor -> 40 bobot) -> Total 90
        $actualsResp = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson("/api/v1/kpi/assignments/{$assignmentId}/actuals", [
                'actuals' => [
                    [
                        'metric_id'    => $metric1Id,
                        'actual_value' => 200,
                    ],
                    [
                        'metric_id'    => $metric2Id,
                        'actual_value' => 4,
                    ],
                ],
            ]);

        $actualsResp->assertStatus(200)
            ->assertJsonPath('data.total_score', 90);
    }

    public function test_can_create_kpi_template_from_web_interface(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->post('/kpi/templates', [
                'name'        => 'Template Web KPI',
                'description' => 'Deskripsi template web',
                'period'      => 'monthly',
                'metrics'     => [
                    [
                        'name'           => 'Target Sales',
                        'weight'         => 60,
                        'target_value'   => 100,
                        'unit'           => '%',
                        'scoring_method' => 'higher_is_better',
                    ],
                    [
                        'name'           => 'Kehadiran',
                        'weight'         => 40,
                        'target_value'   => 100,
                        'unit'           => '%',
                        'scoring_method' => 'higher_is_better',
                    ],
                ],
            ]);

        $response->assertRedirect('/kpi/templates');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('kpi_templates', [
            'tenant_id' => $this->tenant->id,
            'name'      => 'Template Web KPI',
        ]);
    }
}
