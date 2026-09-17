<?php

namespace Tests\Feature;

use App\Models\AttendanceRecord;
use App\Models\Employee;
use App\Models\OvertimeRequest;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OvertimeManagementTest extends TestCase
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
            'name'   => 'Overtime Org',
            'slug'   => 'overtime-org-' . uniqid(),
            'status' => 'active',
        ]);

        $this->adminUser = User::create([
            'tenant_id' => $this->tenant->id,
            'name'      => 'HR Admin',
            'email'     => 'hr-' . uniqid() . '@example.com',
            'password'  => bcrypt('password'),
        ]);
        $this->adminUser->assignRole('hr_admin');

        $this->employeeUser = User::create([
            'tenant_id' => $this->tenant->id,
            'name'      => 'Bob Smith',
            'email'     => 'bob-' . uniqid() . '@example.com',
            'password'  => bcrypt('password'),
        ]);
        $this->employeeUser->assignRole('employee');

        $this->employee = Employee::create([
            'tenant_id'       => $this->tenant->id,
            'user_id'         => $this->employeeUser->id,
            'employee_number' => 'EMP-' . rand(1000, 9999),
            'name'            => 'Bob Smith',
            'status'          => 'active',
        ]);
    }

    public function test_employee_can_submit_overtime_request(): void
    {
        Sanctum::actingAs($this->employeeUser);

        $yesterday = Carbon::yesterday()->toDateString();

        $response = $this->postJson('/api/v1/overtime', [
            'overtime_date' => $yesterday,
            'start_time'    => '17:00',
            'end_time'      => '20:00',
            'reason'        => 'Menyelesaikan deploy modul absensi.',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', 'submitted')
            ->assertJsonPath('data.total_minutes', 180)
            ->assertJsonPath('data.employee_id', $this->employee->id);

        $otId = $response->json('data.id');

        $this->assertDatabaseHas('overtime_requests', [
            'id'            => $otId,
            'tenant_id'     => $this->tenant->id,
            'employee_id'   => $this->employee->id,
            'total_minutes' => 180,
            'status'        => 'submitted',
        ]);

        // Verify Audit Log
        $this->assertDatabaseHas('audit_logs', [
            'tenant_id'     => $this->tenant->id,
            'actor_user_id' => $this->employeeUser->id,
            'action'        => 'overtime.submitted',
            'entity_type'   => 'OvertimeRequest',
            'entity_id'     => $otId,
        ]);

        // Verify Notification to HR Admin
        $this->assertDatabaseHas('notifications', [
            'tenant_id' => $this->tenant->id,
            'user_id'   => $this->adminUser->id,
            'type'      => 'overtime_request',
        ]);
    }

    public function test_hr_can_view_overtime_list(): void
    {
        $yesterday = Carbon::yesterday()->toDateString();

        OvertimeRequest::create([
            'tenant_id'     => $this->tenant->id,
            'employee_id'   => $this->employee->id,
            'overtime_date' => $yesterday,
            'start_time'    => '17:00',
            'end_time'      => '19:00',
            'total_minutes' => 120,
            'reason'        => 'Test list',
            'status'        => 'submitted',
        ]);

        Sanctum::actingAs($this->adminUser);

        $this->getJson('/api/v1/overtime?status=submitted')
            ->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'overtime_date', 'total_minutes', 'status', 'employee'],
                ],
            ]);
    }

    public function test_hr_can_approve_overtime_and_update_attendance_record(): void
    {
        $yesterday = Carbon::yesterday()->toDateString();

        // Create an attendance record for yesterday
        $attendance = AttendanceRecord::create([
            'tenant_id'       => $this->tenant->id,
            'employee_id'     => $this->employee->id,
            'attendance_date' => $yesterday,
            'status'          => 'present',
            'overtime_minutes'=> 0,
        ]);

        $overtime = OvertimeRequest::create([
            'tenant_id'     => $this->tenant->id,
            'employee_id'   => $this->employee->id,
            'overtime_date' => $yesterday,
            'start_time'    => '18:00',
            'end_time'      => '21:00',
            'total_minutes' => 180,
            'reason'        => 'Lembur valid',
            'status'        => 'submitted',
        ]);

        Sanctum::actingAs($this->adminUser);

        $response = $this->putJson("/api/v1/overtime/{$overtime->id}/approve", [
            'review_notes' => 'Disetujui manajer.',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', 'approved')
            ->assertJsonPath('data.reviewed_by', $this->adminUser->id);

        // Verify Attendance record updated with overtime minutes
        $this->assertDatabaseHas('attendance_records', [
            'id'               => $attendance->id,
            'overtime_minutes' => 180,
        ]);

        // Verify Audit Log
        $this->assertDatabaseHas('audit_logs', [
            'tenant_id'     => $this->tenant->id,
            'actor_user_id' => $this->adminUser->id,
            'action'        => 'overtime.approved',
            'entity_type'   => 'OvertimeRequest',
            'entity_id'     => $overtime->id,
        ]);

        // Verify Notification to Employee
        $this->assertDatabaseHas('notifications', [
            'tenant_id' => $this->tenant->id,
            'user_id'   => $this->employeeUser->id,
            'type'      => 'overtime_approved',
        ]);
    }

    public function test_hr_can_reject_overtime(): void
    {
        $yesterday = Carbon::yesterday()->toDateString();

        $overtime = OvertimeRequest::create([
            'tenant_id'     => $this->tenant->id,
            'employee_id'   => $this->employee->id,
            'overtime_date' => $yesterday,
            'start_time'    => '18:00',
            'end_time'      => '21:00',
            'total_minutes' => 180,
            'reason'        => 'Lembur ditolak',
            'status'        => 'submitted',
        ]);

        Sanctum::actingAs($this->adminUser);

        $response = $this->putJson("/api/v1/overtime/{$overtime->id}/reject", [
            'review_notes' => 'Tidak ada SPK lembur sebelumnya.',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', 'rejected')
            ->assertJsonPath('data.review_notes', 'Tidak ada SPK lembur sebelumnya.');

        // Verify Audit Log
        $this->assertDatabaseHas('audit_logs', [
            'tenant_id'     => $this->tenant->id,
            'actor_user_id' => $this->adminUser->id,
            'action'        => 'overtime.rejected',
            'entity_type'   => 'OvertimeRequest',
            'entity_id'     => $overtime->id,
        ]);

        // Verify Notification to Employee
        $this->assertDatabaseHas('notifications', [
            'tenant_id' => $this->tenant->id,
            'user_id'   => $this->employeeUser->id,
            'type'      => 'overtime_rejected',
        ]);
    }

    public function test_employee_cannot_approve_overtime(): void
    {
        $yesterday = Carbon::yesterday()->toDateString();

        $overtime = OvertimeRequest::create([
            'tenant_id'     => $this->tenant->id,
            'employee_id'   => $this->employee->id,
            'overtime_date' => $yesterday,
            'start_time'    => '18:00',
            'end_time'      => '21:00',
            'total_minutes' => 180,
            'reason'        => 'Lembur sendiri',
            'status'        => 'submitted',
        ]);

        Sanctum::actingAs($this->employeeUser);

        $this->putJson("/api/v1/overtime/{$overtime->id}/approve", [
            'review_notes' => 'Approve sendiri',
        ])->assertStatus(403);
    }
}
