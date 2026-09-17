<?php

namespace Tests\Feature;

use App\Models\AttendanceCorrection;
use App\Models\AttendanceRecord;
use App\Models\AuditLog;
use App\Models\Employee;
use App\Models\Notification;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AttendanceCorrectionTest extends TestCase
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
            'name'   => 'Test Company',
            'slug'   => 'test-company-' . uniqid(),
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
            'name'      => 'John Doe',
            'email'     => 'john-' . uniqid() . '@example.com',
            'password'  => bcrypt('password'),
        ]);
        $this->employeeUser->assignRole('employee');

        $this->employee = Employee::create([
            'tenant_id'       => $this->tenant->id,
            'user_id'         => $this->employeeUser->id,
            'employee_number' => 'EMP-' . rand(1000, 9999),
            'name'            => 'John Doe',
            'status'          => 'active',
        ]);
    }

    public function test_employee_can_submit_attendance_correction(): void
    {
        Sanctum::actingAs($this->employeeUser);

        $yesterday = Carbon::yesterday()->toDateString();

        $response = $this->postJson('/api/v1/attendance/corrections', [
            'correction_date'    => $yesterday,
            'requested_check_in' => "{$yesterday} 08:30:00",
            'requested_check_out'=> "{$yesterday} 17:05:00",
            'reason'             => 'Lupa bawa HP saat absen pagi.',
            'status'             => 'submitted',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', 'submitted')
            ->assertJsonPath('data.employee_id', $this->employee->id);

        $correctionId = $response->json('data.id');

        $this->assertDatabaseHas('attendance_corrections', [
            'id'          => $correctionId,
            'tenant_id'   => $this->tenant->id,
            'employee_id' => $this->employee->id,
            'status'      => 'submitted',
        ]);

        // Verify audit log
        $this->assertDatabaseHas('audit_logs', [
            'tenant_id'     => $this->tenant->id,
            'actor_user_id' => $this->employeeUser->id,
            'action'        => 'attendance.correction.submitted',
            'entity_type'   => 'AttendanceCorrection',
            'entity_id'     => $correctionId,
        ]);

        // Verify notification sent to HR Admin
        $this->assertDatabaseHas('notifications', [
            'tenant_id' => $this->tenant->id,
            'user_id'   => $this->adminUser->id,
            'type'      => 'attendance_correction',
        ]);
    }

    public function test_cannot_submit_duplicate_pending_correction(): void
    {
        Sanctum::actingAs($this->employeeUser);

        $yesterday = Carbon::yesterday()->toDateString();

        AttendanceCorrection::create([
            'tenant_id'          => $this->tenant->id,
            'employee_id'        => $this->employee->id,
            'correction_date'    => $yesterday,
            'requested_check_in' => "{$yesterday} 08:30:00",
            'reason'             => 'Pertama',
            'status'             => 'submitted',
            'created_by'         => $this->employeeUser->id,
        ]);

        $response = $this->postJson('/api/v1/attendance/corrections', [
            'correction_date'    => $yesterday,
            'requested_check_in' => "{$yesterday} 08:30:00",
            'reason'             => 'Kedua',
            'status'             => 'submitted',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('success', false);
    }

    public function test_hr_can_view_corrections_list_and_filter_by_status(): void
    {
        $yesterday = Carbon::yesterday()->toDateString();

        AttendanceCorrection::create([
            'tenant_id'          => $this->tenant->id,
            'employee_id'        => $this->employee->id,
            'correction_date'    => $yesterday,
            'requested_check_in' => "{$yesterday} 08:30:00",
            'reason'             => 'Testing filter',
            'status'             => 'submitted',
            'created_by'         => $this->employeeUser->id,
        ]);

        Sanctum::actingAs($this->adminUser);

        $response = $this->getJson('/api/v1/attendance/corrections?status=submitted');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'correction_date', 'status', 'employee'],
                ],
            ]);
    }

    public function test_hr_can_approve_attendance_correction_and_create_or_update_record(): void
    {
        $yesterday = Carbon::yesterday()->toDateString();

        $correction = AttendanceCorrection::create([
            'tenant_id'          => $this->tenant->id,
            'employee_id'        => $this->employee->id,
            'correction_date'    => $yesterday,
            'requested_check_in' => "{$yesterday} 08:00:00",
            'requested_check_out'=> "{$yesterday} 17:00:00",
            'reason'             => 'Koreksi waktu',
            'status'             => 'submitted',
            'created_by'         => $this->employeeUser->id,
        ]);

        Sanctum::actingAs($this->adminUser);

        $response = $this->putJson("/api/v1/attendance/corrections/{$correction->id}/approve", [
            'review_notes' => 'Disetujui setelah konfirmasi dengan atasan.',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', 'approved')
            ->assertJsonPath('data.reviewed_by', $this->adminUser->id);

        // Verify AttendanceRecord created
        $this->assertDatabaseHas('attendance_records', [
            'tenant_id'       => $this->tenant->id,
            'employee_id'     => $this->employee->id,
            'attendance_date' => $yesterday,
            'status'          => 'present',
        ]);

        // Verify AuditLog
        $this->assertDatabaseHas('audit_logs', [
            'tenant_id'     => $this->tenant->id,
            'actor_user_id' => $this->adminUser->id,
            'action'        => 'attendance.correction.approved',
            'entity_type'   => 'AttendanceCorrection',
            'entity_id'     => $correction->id,
        ]);

        // Verify Notification to Employee
        $this->assertDatabaseHas('notifications', [
            'tenant_id' => $this->tenant->id,
            'user_id'   => $this->employeeUser->id,
            'type'      => 'attendance_correction_approved',
        ]);
    }

    public function test_hr_can_reject_attendance_correction(): void
    {
        $yesterday = Carbon::yesterday()->toDateString();

        $correction = AttendanceCorrection::create([
            'tenant_id'          => $this->tenant->id,
            'employee_id'        => $this->employee->id,
            'correction_date'    => $yesterday,
            'requested_check_in' => "{$yesterday} 08:00:00",
            'reason'             => 'Koreksi palsu',
            'status'             => 'submitted',
            'created_by'         => $this->employeeUser->id,
        ]);

        Sanctum::actingAs($this->adminUser);

        $response = $this->putJson("/api/v1/attendance/corrections/{$correction->id}/reject", [
            'review_notes' => 'Tidak sesuai dengan log CCTV kantor.',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', 'rejected')
            ->assertJsonPath('data.review_notes', 'Tidak sesuai dengan log CCTV kantor.');

        // Verify AuditLog
        $this->assertDatabaseHas('audit_logs', [
            'tenant_id'     => $this->tenant->id,
            'actor_user_id' => $this->adminUser->id,
            'action'        => 'attendance.correction.rejected',
            'entity_type'   => 'AttendanceCorrection',
            'entity_id'     => $correction->id,
        ]);

        // Verify Notification to Employee
        $this->assertDatabaseHas('notifications', [
            'tenant_id' => $this->tenant->id,
            'user_id'   => $this->employeeUser->id,
            'type'      => 'attendance_correction_rejected',
        ]);
    }

    public function test_employee_cannot_approve_or_reject(): void
    {
        $yesterday = Carbon::yesterday()->toDateString();

        $correction = AttendanceCorrection::create([
            'tenant_id'          => $this->tenant->id,
            'employee_id'        => $this->employee->id,
            'correction_date'    => $yesterday,
            'requested_check_in' => "{$yesterday} 08:00:00",
            'reason'             => 'Koreksi sendiri',
            'status'             => 'submitted',
            'created_by'         => $this->employeeUser->id,
        ]);

        Sanctum::actingAs($this->employeeUser);

        $response = $this->putJson("/api/v1/attendance/corrections/{$correction->id}/approve", [
            'review_notes' => 'Mencoba approve sendiri',
        ]);

        $response->assertStatus(403);
    }
}
