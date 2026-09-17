<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Employee;
use App\Models\FieldJob;
use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Class CustomerAndFieldJobTest
 *
 * Menguji fungsionalitas manajemen pelanggan dan pekerjaan lapangan:
 * - Registrasi data pelanggan (Customer)
 * - Penjadwalan pekerjaan lapangan (Field Job)
 * - Eksekusi GPS Check-in & verifikasi koordinat
 * - Penyelesaian pekerjaan lapangan beserta catatan
 */
class CustomerAndFieldJobTest extends TestCase
{
    use DatabaseTransactions;

    private Tenant $tenant;
    private User $adminUser;
    private Employee $technician;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);

        $this->tenant = Tenant::create([
            'name'   => 'Field Services Tenant',
            'slug'   => 'field-test-' . uniqid(),
            'status' => 'active',
        ]);

        $this->adminUser = User::create([
            'tenant_id' => $this->tenant->id,
            'name'      => 'Field Operations Manager',
            'email'     => 'fom-' . uniqid() . '@example.com',
            'password'  => bcrypt('password'),
        ]);

        $techUser = User::create([
            'tenant_id' => $this->tenant->id,
            'name'      => 'Field Technician',
            'email'     => 'tech-' . uniqid() . '@example.com',
            'password'  => bcrypt('password'),
        ]);

        $this->technician = Employee::create([
            'tenant_id'       => $this->tenant->id,
            'user_id'         => $techUser->id,
            'name'            => 'Field Technician',
            'employee_number' => 'TECH-' . rand(1000, 9999),
            'status'          => 'active',
            'join_date'       => now(),
            'employment_type' => 'permanent',
        ]);
    }

    /**
     * Uji pembuatan data pelanggan dan penjadwalan field job.
     */
    public function test_can_create_customer_and_schedule_field_job(): void
    {
        // 1. Buat Customer
        $custResp = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson('/api/v1/customers', [
                'name'         => 'PT Bank Sentral Mandiri',
                'company_name' => 'Bank Mandiri Cabang Sudirman',
                'email'        => 'contact@bsm.example.com',
                'phone'        => '021-5551234',
                'address'      => 'Jl. Jend Sudirman Kav 50, Jakarta',
                'city'         => 'Jakarta Selatan',
            ]);

        $custResp->assertStatus(201)
            ->assertJsonPath('data.name', 'PT Bank Sentral Mandiri');

        $customerId = $custResp->json('data.id');

        // 2. Jadwalkan Field Job
        $jobResp = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson('/api/v1/field-jobs', [
                'customer_id'   => $customerId,
                'assigned_to'   => $this->technician->id,
                'title'         => 'Maintenance ATM & CCTV Cabang',
                'description'   => 'Pengecekan berkala perangkat jaringan ATM',
                'site_name'     => 'ATM Lobby Utama',
                'scheduled_at'  => now()->addDay()->toDateTimeString(),
            ]);

        $jobResp->assertStatus(201)
            ->assertJsonPath('data.title', 'Maintenance ATM & CCTV Cabang')
            ->assertJsonPath('data.status', 'assigned');

        $this->assertDatabaseHas('field_jobs', [
            'customer_id' => $customerId,
            'assigned_to' => $this->technician->id,
        ]);
    }

    /**
     * Uji alur GPS Check-in dan penyelesaian field job.
     */
    public function test_can_checkin_and_complete_field_job(): void
    {
        $job = FieldJob::create([
            'tenant_id'     => $this->tenant->id,
            'assigned_to'   => $this->technician->id,
            'assigned_by'   => $this->adminUser->id,
            'title'         => 'Pemasangan Sensor Suhu Data Center',
            'site_name'     => 'DC Cibitung',
            'scheduled_at'  => now(),
            'status'        => 'assigned',
        ]);

        // 1. Check-in dengan koordinat GPS
        $checkInResp = $this->actingAs($this->adminUser, 'sanctum')
            ->putJson("/api/v1/field-jobs/{$job->id}/checkin", [
                'latitude'  => -6.2550,
                'longitude' => 106.9000,
            ]);

        $checkInResp->assertStatus(200)
            ->assertJsonPath('data.status', 'in_progress');

        // 2. Selesaikan job dengan work_notes
        $completeResp = $this->actingAs($this->adminUser, 'sanctum')
            ->putJson("/api/v1/field-jobs/{$job->id}/complete", [
                'work_notes' => 'Pemasangan 4 unit sensor selesai. Pengujian transmisi MQTT sukses.',
            ]);

        $completeResp->assertStatus(200)
            ->assertJsonPath('data.status', 'completed');

        $this->assertDatabaseHas('field_jobs', [
            'id'     => $job->id,
            'status' => 'completed',
        ]);
    }
}
