<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\Company;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Position;
use App\Models\WorkLocation;
use App\Models\Employee;
use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use App\Models\Target;
use App\Models\AttendanceRecord;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Class DemoDataSeeder
 *
 * Mengisi data komprehensif untuk demonstrasi dan pengujian end-to-end:
 * - 1 Demo Tenant & Perusahaan
 * - Struktur organisasi (Cabang, Departemen, Jabatan, Lokasi Kerja)
 * - Akun pengguna & 10+ data karyawan
 * - Proyek, Tugas, Target, serta Catatan Presensi
 */
class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Tenant Demo
        $tenant = Tenant::firstOrCreate(
            ['slug' => 'demo-corp'],
            [
                'name' => 'PT Solusi Terpadu Nusantara',
                'status' => 'active',
            ]
        );

        // 2. Buat Perusahaan
        $company = Company::firstOrCreate(
            ['tenant_id' => $tenant->id],
            [
                'name' => 'PT Solusi Terpadu Nusantara',
                'email' => 'contact@solusiterpadu.co.id',
                'phone' => '021-5558899',
                'address' => 'Jl. Jenderal Sudirman Kav. 52-53',
                'city' => 'Jakarta Selatan',
                'province' => 'DKI Jakarta',
                'postal_code' => '12190',
                'country' => 'Indonesia',
                'industry' => 'Information Technology',
                'website' => 'https://solusiterpadu.co.id',
            ]
        );

        // 3. Cabang Utama & Lokasi Kerja
        $branch = Branch::firstOrCreate(
            ['tenant_id' => $tenant->id, 'code' => 'HQ-JKT'],
            [
                'company_id' => $company->id,
                'name' => 'Kantor Pusat Jakarta',
                'city' => 'Jakarta Selatan',
            ]
        );

        $workLocation = WorkLocation::firstOrCreate(
            ['tenant_id' => $tenant->id, 'name' => 'Head Office Sudirman'],
            [
                'branch_id' => $branch->id,
                'address' => 'Gedung Bursa Efek Tower 2 Lt. 15',
                'latitude' => -6.225588,
                'longitude' => 106.808544,
                'radius_meters' => 150,
            ]
        );


        // 4. Departemen & Posisi
        $deptTech = Department::firstOrCreate(
            ['tenant_id' => $tenant->id, 'name' => 'Teknologi Informasi'],
            ['branch_id' => $branch->id, 'code' => 'IT-DEPT']
        );

        $deptOps = Department::firstOrCreate(
            ['tenant_id' => $tenant->id, 'name' => 'Operasional & Lapangan'],
            ['branch_id' => $branch->id, 'code' => 'OPS-DEPT']
        );

        $posManager = Position::firstOrCreate(
            ['tenant_id' => $tenant->id, 'name' => 'Engineering Manager'],
            ['department_id' => $deptTech->id, 'code' => 'ENG-MGR', 'level' => 3]
        );

        $posDev = Position::firstOrCreate(
            ['tenant_id' => $tenant->id, 'name' => 'Software Engineer'],
            ['department_id' => $deptTech->id, 'code' => 'SWE', 'level' => 2]
        );

        $posField = Position::firstOrCreate(
            ['tenant_id' => $tenant->id, 'name' => 'Field Technician'],
            ['department_id' => $deptOps->id, 'code' => 'TECH', 'level' => 1]
        );

        // 5. Akun Pengguna Owner & Staf
        $ownerUser = User::firstOrCreate(
            ['email' => 'owner@solusiterpadu.co.id'],
            [
                'tenant_id' => $tenant->id,
                'name' => 'Budi Pratama (Owner)',
                'password' => Hash::make('password123'),
                'locale' => 'id',
            ]
        );
        $ownerUser->assignRole('owner');

        // Buat Karyawan Demo
        $employeesData = [
            ['name' => 'Ahmad Fauzi', 'email' => 'ahmad@solusiterpadu.co.id', 'role' => 'manager', 'pos' => $posManager->id, 'dept' => $deptTech->id],
            ['name' => 'Siti Rahma', 'email' => 'siti@solusiterpadu.co.id', 'role' => 'employee', 'pos' => $posDev->id, 'dept' => $deptTech->id],
            ['name' => 'Bambang Irawan', 'email' => 'bambang@solusiterpadu.co.id', 'role' => 'employee', 'pos' => $posDev->id, 'dept' => $deptTech->id],
            ['name' => 'Dewi Lestari', 'email' => 'dewi@solusiterpadu.co.id', 'role' => 'employee', 'pos' => $posDev->id, 'dept' => $deptTech->id],
            ['name' => 'Eko Prasetyo', 'email' => 'eko@solusiterpadu.co.id', 'role' => 'field_worker', 'pos' => $posField->id, 'dept' => $deptOps->id],
            ['name' => 'Fajar Santoso', 'email' => 'fajar@solusiterpadu.co.id', 'role' => 'field_worker', 'pos' => $posField->id, 'dept' => $deptOps->id],
            ['name' => 'Gita Anggraeni', 'email' => 'gita@solusiterpadu.co.id', 'role' => 'employee', 'pos' => $posDev->id, 'dept' => $deptTech->id],
            ['name' => 'Hendra Wijaya', 'email' => 'hendra@solusiterpadu.co.id', 'role' => 'employee', 'pos' => $posDev->id, 'dept' => $deptTech->id],
            ['name' => 'Indah Permata', 'email' => 'indah@solusiterpadu.co.id', 'role' => 'employee', 'pos' => $posDev->id, 'dept' => $deptTech->id],
            ['name' => 'Joko Widodo', 'email' => 'joko@solusiterpadu.co.id', 'role' => 'field_worker', 'pos' => $posField->id, 'dept' => $deptOps->id],
        ];

        $createdEmployees = [];

        foreach ($employeesData as $idx => $emp) {
            $user = User::firstOrCreate(
                ['email' => $emp['email']],
                [
                    'tenant_id' => $tenant->id,
                    'name' => $emp['name'],
                    'password' => Hash::make('password123'),
                    'locale' => 'id',
                ]
            );
            $user->assignRole($emp['role']);

            $employee = Employee::firstOrCreate(
                ['tenant_id' => $tenant->id, 'employee_number' => 'EMP-' . str_pad($idx + 1, 4, '0', STR_PAD_LEFT)],
                [
                    'name' => $emp['name'],
                    'user_id' => $user->id,
                    'branch_id' => $branch->id,
                    'department_id' => $emp['dept'],
                    'position_id' => $emp['pos'],
                    'status' => 'active',
                    'join_date' => Carbon::now()->subMonths(12)->toDateString(),
                ]
            );

            $createdEmployees[] = $employee;
        }

        // 6. Proyek & Tugas Demo
        $project = Project::firstOrCreate(
            ['tenant_id' => $tenant->id, 'name' => 'Rollout Sistem RMIH Enterprise'],
            [
                'company_id' => $company->id,
                'code' => 'PRJ-RMIH-01',
                'description' => 'Implementasi platform manajemen SDM dan presensi cerdas',
                'status' => 'active',
                'start_date' => Carbon::now()->startOfMonth()->toDateString(),
                'deadline' => Carbon::now()->endOfMonth()->addMonths(2)->toDateString(),
            ]
        );



        Task::firstOrCreate(
            ['tenant_id' => $tenant->id, 'title' => 'Konfigurasi Mesin Presensi Kantor'],
            [
                'project_id' => $project->id,
                'created_by' => $ownerUser->id,
                'priority' => 'high',
                'status' => 'in_progress',
                'due_at' => Carbon::now()->addDays(5),
            ]
        );

        Task::firstOrCreate(
            ['tenant_id' => $tenant->id, 'title' => 'Verifikasi Lokasi & Geofence Cabang'],
            [
                'project_id' => $project->id,
                'created_by' => $ownerUser->id,
                'priority' => 'medium',
                'status' => 'assigned',
                'due_at' => Carbon::now()->addDays(10),
            ]
        );


        // 7. Target & Presensi Demo
        Target::firstOrCreate(
            ['tenant_id' => $tenant->id, 'name' => 'Tingkat Kepatuhan Presensi 98%'],
            [
                'created_by' => $ownerUser->id,
                'metric' => 'Persentase Kehadiran Tepat Waktu',
                'period' => 'monthly',
                'start_date' => Carbon::now()->startOfMonth()->toDateString(),
                'end_date' => Carbon::now()->endOfMonth()->toDateString(),
                'target_value' => 98.0,
                'actual_value' => 95.0,
                'risk_level' => 'on_track',
                'status' => 'active',
            ]
        );


        // Record Kehadiran Hari Ini
        foreach ($createdEmployees as $idx => $emp) {
            AttendanceRecord::firstOrCreate(
                [
                    'tenant_id' => $tenant->id,
                    'employee_id' => $emp->id,
                    'attendance_date' => Carbon::today()->toDateString(),
                ],
                [
                    'check_in_at' => Carbon::today()->setHour(8)->setMinute(15 + $idx)->toDateTimeString(),
                    'status' => $idx % 5 === 0 ? 'late' : 'present',
                    'source' => 'mobile',
                    'work_location_id' => $workLocation->id,

                ]
            );
        }
    }
}
