<?php

namespace Tests\Feature;

use App\Models\AttendanceRecord;
use App\Models\Employee;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AttendanceWebPagesTest extends TestCase
{
    use DatabaseTransactions;

    private Tenant $tenant;
    private User $user;
    private Employee $employee;
    private AttendanceRecord $record;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);

        $this->tenant = Tenant::create([
            'name'   => 'Web Test Tenant',
            'slug'   => 'web-test-' . uniqid(),
            'status' => 'active',
        ]);

        $this->user = User::create([
            'tenant_id' => $this->tenant->id,
            'name'      => 'Admin User',
            'email'     => 'admin-' . uniqid() . '@example.com',
            'password'  => bcrypt('password'),
        ]);
        $this->user->assignRole('hr_admin');

        $this->employee = Employee::create([
            'tenant_id'       => $this->tenant->id,
            'user_id'         => $this->user->id,
            'employee_number' => 'EMP-' . rand(1000, 9999),
            'name'            => 'Admin User',
            'status'          => 'active',
        ]);

        $this->record = AttendanceRecord::create([
            'tenant_id'       => $this->tenant->id,
            'employee_id'     => $this->employee->id,
            'attendance_date' => Carbon::today()->toDateString(),
            'check_in_at'     => Carbon::now()->subHours(4),
            'status'          => 'present',
        ]);
    }

    public function test_attendance_dashboard_page_loads(): void
    {
        $this->actingAs($this->user)
            ->get('/attendance')
            ->assertStatus(200)
            ->assertSee('Absensi')
            ->assertSee($this->employee->name);
    }

    public function test_attendance_calendar_page_loads(): void
    {
        $this->actingAs($this->user)
            ->get('/attendance/calendar?employee_id=' . $this->employee->id)
            ->assertStatus(200)
            ->assertSee('Kalender Absensi')
            ->assertSee($this->employee->name);
    }

    public function test_attendance_leaves_page_loads(): void
    {
        $this->actingAs($this->user)
            ->get('/attendance/leaves')
            ->assertStatus(200)
            ->assertSee('Manajemen Izin & Cuti');
    }

    public function test_attendance_corrections_page_loads(): void
    {
        $this->actingAs($this->user)
            ->get('/attendance/corrections')
            ->assertStatus(200)
            ->assertSee('Persetujuan Koreksi Absensi');
    }

    public function test_attendance_shifts_page_loads(): void
    {
        $this->actingAs($this->user)
            ->get('/attendance/shifts')
            ->assertStatus(200)
            ->assertSee('Shift & Jadwal Kerja');
    }

    public function test_attendance_detail_page_loads(): void
    {
        $this->actingAs($this->user)
            ->get('/attendance/' . $this->record->id)
            ->assertStatus(200)
            ->assertSee('Detail Catatan Absensi')
            ->assertSee($this->employee->name);
    }
}
