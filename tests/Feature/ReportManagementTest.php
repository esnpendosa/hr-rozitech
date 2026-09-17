<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Queue;
use App\Jobs\GenerateReportJob;
use Tests\TestCase;

/**
 * Class ReportManagementTest
 *
 * Menguji agregasi laporan dan pemicu antrian ekspor berkas.
 */
class ReportManagementTest extends TestCase
{
    use DatabaseTransactions;

    private Tenant $tenant;
    private User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);

        $this->tenant = Tenant::create([
            'name'   => 'Reports Tenant',
            'slug'   => 'rep-test-' . uniqid(),
            'status' => 'active',
        ]);

        $this->adminUser = User::create([
            'tenant_id' => $this->tenant->id,
            'name'      => 'Finance Executive',
            'email'     => 'fin-' . uniqid() . '@example.com',
            'password'  => bcrypt('password'),
        ]);
    }

    /**
     * Uji pemanggilan agregasi laporan dan pembuat antrian job ekspor.
     */
    public function test_can_fetch_reports_and_trigger_export(): void
    {
        Queue::fake();

        // 1. Ambil agregasi presensi
        $attResp = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson('/api/v1/reports/attendance');

        $attResp->assertStatus(200)
            ->assertJsonStructure(['data' => ['total_records', 'present_count', 'late_count']]);

        // 2. Ambil agregasi tugas
        $taskResp = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson('/api/v1/reports/tasks');

        $taskResp->assertStatus(200)
            ->assertJsonStructure(['data' => ['total_tasks', 'completion_rate']]);

        // 3. Trigger antrian ekspor
        $exportResp = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson('/api/v1/reports/attendance/export', [
                'format' => 'xlsx',
            ]);

        $exportResp->assertStatus(200)
            ->assertJsonPath('data.status', 'queued');

        Queue::assertPushed(GenerateReportJob::class);
    }
}
