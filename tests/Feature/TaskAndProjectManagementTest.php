<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Employee;
use App\Models\Project;
use App\Models\Task;
use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Class TaskAndProjectManagementTest
 *
 * Menguji fungsionalitas manajemen tugas dan proyek:
 * - Siklus CRUD Proyek & Anggota Tim
 * - Siklus CRUD Tugas & Penugasan multi-pegawai
 * - Transisi status tugas & histori audit
 * - Manajemen item checklist & perhitungan persentase progres
 * - Pengunggahan bukti fisik / evidence
 */
class TaskAndProjectManagementTest extends TestCase
{
    use DatabaseTransactions;

    private Tenant $tenant;
    private User $adminUser;
    private User $employeeUser;
    private Employee $employee;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);

        $this->tenant = Tenant::create([
            'name'   => 'Project Test Tenant',
            'slug'   => 'proj-test-' . uniqid(),
            'status' => 'active',
        ]);

        $this->adminUser = User::create([
            'tenant_id' => $this->tenant->id,
            'name'      => 'Project Manager',
            'email'     => 'pm-' . uniqid() . '@example.com',
            'password'  => bcrypt('password'),
        ]);

        $this->employeeUser = User::create([
            'tenant_id' => $this->tenant->id,
            'name'      => 'Developer Lead',
            'email'     => 'dev-' . uniqid() . '@example.com',
            'password'  => bcrypt('password'),
        ]);

        $this->employee = Employee::create([
            'tenant_id'       => $this->tenant->id,
            'user_id'         => $this->employeeUser->id,
            'name'            => 'Developer Lead',
            'employee_number' => 'EMP-' . rand(1000, 9999),
            'status'          => 'active',
            'join_date'       => now(),
            'employment_type' => 'permanent',
        ]);
    }

    /**
     * Uji alur pembuatan proyek dan penambahan anggota proyek.
     */
    public function test_can_create_project_and_add_member(): void
    {
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson('/api/v1/projects', [
                'name'        => 'Aplikasi HRIS Mobile 2.0',
                'code'        => 'HRIS-MOB',
                'description' => 'Pembangunan aplikasi absensi mobile Flutter',
                'status'      => 'active',
                'budget'      => 50000000,
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'Aplikasi HRIS Mobile 2.0');

        $projectId = $response->json('data.id');

        // Tambah anggota ke proyek
        $memberResp = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson("/api/v1/projects/{$projectId}/members", [
                'employee_id' => $this->employee->id,
                'role'        => 'tech_lead',
            ]);

        $memberResp->assertStatus(201)
            ->assertJsonPath('data.role', 'tech_lead');

        $this->assertDatabaseHas('project_members', [
            'project_id'  => $projectId,
            'employee_id' => $this->employee->id,
            'role'        => 'tech_lead',
        ]);
    }

    /**
     * Uji pembuatan tugas kerja (Task) dengan multi-assignee.
     */
    public function test_can_create_and_assign_task(): void
    {
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson('/api/v1/tasks', [
                'title'        => 'Implementasi API Presensi Sidik Jari',
                'description'  => 'Hubungkan endpoint X Solutions ke sistem presensi',
                'priority'     => 'high',
                'start_at'     => now()->toDateTimeString(),
                'due_at'       => now()->addDays(3)->toDateTimeString(),
                'assignee_ids' => [$this->employee->id],
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.title', 'Implementasi API Presensi Sidik Jari')
            ->assertJsonPath('data.status', 'assigned');

        $taskId = $response->json('data.id');

        $this->assertDatabaseHas('task_assignees', [
            'task_id'     => $taskId,
            'employee_id' => $this->employee->id,
        ]);
    }

    /**
     * Uji pembaruan status tugas dan pencatatan audit histori.
     */
    public function test_can_update_task_status_lifecycle(): void
    {
        $task = Task::create([
            'tenant_id'   => $this->tenant->id,
            'title'       => 'Refactoring Database Schema',
            'priority'    => 'medium',
            'status'      => 'assigned',
            'created_by'  => $this->adminUser->id,
            'assigned_by' => $this->adminUser->id,
        ]);

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->putJson("/api/v1/tasks/{$task->id}/status", [
                'status' => 'in_progress',
                'notes'  => 'Mulai pengerjaan arsitektur schema.',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.status', 'in_progress');

        $this->assertDatabaseHas('task_status_histories', [
            'task_id'     => $task->id,
            'from_status' => 'assigned',
            'to_status'   => 'in_progress',
        ]);
    }

    /**
     * Uji penambahan checklist tugas dan auto-kalkulasi progres kerja.
     */
    public function test_can_manage_checklists_and_recalculate_progress(): void
    {
        $task = Task::create([
            'tenant_id'   => $this->tenant->id,
            'title'       => 'Pembuatan Modul Gaji',
            'priority'    => 'high',
            'status'      => 'in_progress',
            'created_by'  => $this->adminUser->id,
        ]);

        // Tambah 2 item checklist
        $item1 = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson("/api/v1/tasks/{$task->id}/checklists", [
                'title' => 'Buat kalkulator PPh 21',
            ])->json('data');

        $item2 = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson("/api/v1/tasks/{$task->id}/checklists", [
                'title' => 'Kompilasi slip gaji PDF',
            ])->json('data');

        // Toggle selesai item 1 -> Progres harus menjadi 50%
        $toggleResp = $this->actingAs($this->adminUser, 'sanctum')
            ->putJson("/api/v1/tasks/{$task->id}/checklists/{$item1['id']}");

        $toggleResp->assertStatus(200)
            ->assertJsonPath('data.progress_percent', 50);

        $this->assertEquals(50.0, (float) $task->fresh()->progress_percent);
    }

    /**
     * Uji pengunggahan bukti fisik (Task Evidence).
     */
    public function test_can_upload_task_evidence(): void
    {
        Storage::fake('local');

        $task = Task::create([
            'tenant_id'  => $this->tenant->id,
            'title'      => 'Pemasangan Router Cabang',
            'priority'   => 'medium',
            'status'     => 'in_progress',
            'created_by' => $this->adminUser->id,
        ]);

        $file = UploadedFile::fake()->image('bukti_router.jpg', 800, 600);

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson("/api/v1/tasks/{$task->id}/evidence", [
                'file'        => $file,
                'type'        => 'photo',
                'description' => 'Foto bukti router berhasil dipasang di rack server.',
                'latitude'    => -6.2000,
                'longitude'   => 106.8166,
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.type', 'photo');

        $this->assertDatabaseHas('task_evidences', [
            'task_id' => $task->id,
            'type'    => 'photo',
        ]);
    }
}
