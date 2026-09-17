<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LeaveManagementTest extends TestCase
{
    use DatabaseTransactions;

    private Tenant $tenant;
    private User $adminUser;
    private User $employeeUser;
    private Employee $employee;
    private LeaveType $leaveType;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);

        $this->tenant = Tenant::create([
            'name'   => 'Test Org',
            'slug'   => 'test-org-' . uniqid(),
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
            'name'      => 'Jane Doe',
            'email'     => 'jane-' . uniqid() . '@example.com',
            'password'  => bcrypt('password'),
        ]);
        $this->employeeUser->assignRole('employee');

        $this->employee = Employee::create([
            'tenant_id'       => $this->tenant->id,
            'user_id'         => $this->employeeUser->id,
            'employee_number' => 'EMP-' . rand(1000, 9999),
            'name'            => 'Jane Doe',
            'status'          => 'active',
        ]);

        $this->leaveType = LeaveType::create([
            'tenant_id'         => $this->tenant->id,
            'name'              => 'Cuti Tahunan',
            'code'              => 'ANNUAL-' . uniqid(),
            'default_days'      => 12,
            'is_paid'           => true,
            'requires_document' => false,
            'is_active'         => true,
        ]);
    }

    public function test_admin_can_crud_leave_types(): void
    {
        Sanctum::actingAs($this->adminUser);

        // 1. Create
        $response = $this->postJson('/api/v1/leave-types', [
            'name'              => 'Cuti Sakit',
            'code'              => 'SICK-' . uniqid(),
            'default_days'      => 14,
            'is_paid'           => true,
            'requires_document' => true,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.name', 'Cuti Sakit');

        $typeId = $response->json('data.id');

        // 2. List
        $this->getJson('/api/v1/leave-types')
            ->assertStatus(200)
            ->assertJsonPath('success', true);

        // 3. Update
        $this->putJson("/api/v1/leave-types/{$typeId}", [
            'name' => 'Cuti Sakit Khusus',
        ])->assertStatus(200)
          ->assertJsonPath('data.name', 'Cuti Sakit Khusus');

        // 4. Delete
        $this->deleteJson("/api/v1/leave-types/{$typeId}")
            ->assertStatus(200)
            ->assertJsonPath('success', true);
    }

    public function test_employee_can_submit_leave_request(): void
    {
        Sanctum::actingAs($this->employeeUser);

        $nextMonday = Carbon::now()->next(Carbon::MONDAY)->toDateString();
        $nextWednesday = Carbon::now()->next(Carbon::WEDNESDAY)->toDateString();

        $response = $this->postJson('/api/v1/leaves', [
            'leave_type_id' => $this->leaveType->id,
            'start_date'    => $nextMonday,
            'end_date'      => $nextWednesday,
            'reason'        => 'Acara keluarga di kampung.',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', 'submitted')
            ->assertJsonPath('data.employee_id', $this->employee->id);

        $leaveId = $response->json('data.id');

        $this->assertDatabaseHas('leave_requests', [
            'id'          => $leaveId,
            'tenant_id'   => $this->tenant->id,
            'employee_id' => $this->employee->id,
            'status'      => 'submitted',
        ]);

        // Verify Audit Log
        $this->assertDatabaseHas('audit_logs', [
            'tenant_id'     => $this->tenant->id,
            'actor_user_id' => $this->employeeUser->id,
            'action'        => 'leave.submitted',
            'entity_type'   => 'LeaveRequest',
            'entity_id'     => $leaveId,
        ]);

        // Verify Notification to HR Admin
        $this->assertDatabaseHas('notifications', [
            'tenant_id' => $this->tenant->id,
            'user_id'   => $this->adminUser->id,
            'type'      => 'leave_request',
        ]);
    }

    public function test_cannot_submit_overlapping_leave_request(): void
    {
        Sanctum::actingAs($this->employeeUser);

        $nextMonday = Carbon::now()->next(Carbon::MONDAY)->toDateString();
        $nextWednesday = Carbon::now()->next(Carbon::WEDNESDAY)->toDateString();

        LeaveRequest::create([
            'tenant_id'     => $this->tenant->id,
            'employee_id'   => $this->employee->id,
            'leave_type_id' => $this->leaveType->id,
            'start_date'    => $nextMonday,
            'end_date'      => $nextWednesday,
            'total_days'    => 3,
            'status'        => 'submitted',
        ]);

        // Duplicate / overlap attempt
        $response = $this->postJson('/api/v1/leaves', [
            'leave_type_id' => $this->leaveType->id,
            'start_date'    => $nextMonday,
            'end_date'      => $nextWednesday,
            'reason'        => 'Overlap test',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('success', false);
    }

    public function test_hr_can_approve_leave_request_and_create_attendance_records(): void
    {
        $nextMonday = Carbon::now()->next(Carbon::MONDAY)->toDateString();
        $nextTuesday = Carbon::now()->next(Carbon::TUESDAY)->toDateString();

        $leaveRequest = LeaveRequest::create([
            'tenant_id'     => $this->tenant->id,
            'employee_id'   => $this->employee->id,
            'leave_type_id' => $this->leaveType->id,
            'start_date'    => $nextMonday,
            'end_date'      => $nextTuesday,
            'total_days'    => 2,
            'status'        => 'submitted',
        ]);

        Sanctum::actingAs($this->adminUser);

        $response = $this->putJson("/api/v1/leaves/{$leaveRequest->id}/approve", [
            'review_notes' => 'Selamat berlibur.',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', 'approved')
            ->assertJsonPath('data.reviewed_by', $this->adminUser->id);

        // Verify Attendance records created for the days
        $this->assertDatabaseHas('attendance_records', [
            'tenant_id'       => $this->tenant->id,
            'employee_id'     => $this->employee->id,
            'attendance_date' => $nextMonday,
            'status'          => 'leave',
        ]);

        // Verify Audit Log
        $this->assertDatabaseHas('audit_logs', [
            'tenant_id'     => $this->tenant->id,
            'actor_user_id' => $this->adminUser->id,
            'action'        => 'leave.approved',
            'entity_type'   => 'LeaveRequest',
            'entity_id'     => $leaveRequest->id,
        ]);

        // Verify Notification to Employee
        $this->assertDatabaseHas('notifications', [
            'tenant_id' => $this->tenant->id,
            'user_id'   => $this->employeeUser->id,
            'type'      => 'leave_approved',
        ]);
    }

    public function test_hr_can_reject_leave_request(): void
    {
        $nextMonday = Carbon::now()->next(Carbon::MONDAY)->toDateString();

        $leaveRequest = LeaveRequest::create([
            'tenant_id'     => $this->tenant->id,
            'employee_id'   => $this->employee->id,
            'leave_type_id' => $this->leaveType->id,
            'start_date'    => $nextMonday,
            'end_date'      => $nextMonday,
            'total_days'    => 1,
            'status'        => 'submitted',
        ]);

        Sanctum::actingAs($this->adminUser);

        $response = $this->putJson("/api/v1/leaves/{$leaveRequest->id}/reject", [
            'review_notes' => 'Sedang ada rilis penting pada hari itu.',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', 'rejected')
            ->assertJsonPath('data.review_notes', 'Sedang ada rilis penting pada hari itu.');

        // Verify Audit Log
        $this->assertDatabaseHas('audit_logs', [
            'tenant_id'     => $this->tenant->id,
            'actor_user_id' => $this->adminUser->id,
            'action'        => 'leave.rejected',
            'entity_type'   => 'LeaveRequest',
            'entity_id'     => $leaveRequest->id,
        ]);

        // Verify Notification to Employee
        $this->assertDatabaseHas('notifications', [
            'tenant_id' => $this->tenant->id,
            'user_id'   => $this->employeeUser->id,
            'type'      => 'leave_rejected',
        ]);
    }

    public function test_employee_cannot_approve_leave(): void
    {
        $nextMonday = Carbon::now()->next(Carbon::MONDAY)->toDateString();

        $leaveRequest = LeaveRequest::create([
            'tenant_id'     => $this->tenant->id,
            'employee_id'   => $this->employee->id,
            'leave_type_id' => $this->leaveType->id,
            'start_date'    => $nextMonday,
            'end_date'      => $nextMonday,
            'total_days'    => 1,
            'status'        => 'submitted',
        ]);

        Sanctum::actingAs($this->employeeUser);

        $this->putJson("/api/v1/leaves/{$leaveRequest->id}/approve", [
            'review_notes' => 'Approve sendiri',
        ])->assertStatus(403);
    }
}
