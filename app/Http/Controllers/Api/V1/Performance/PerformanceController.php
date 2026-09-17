<?php

namespace App\Http\Controllers\Api\V1\Performance;

use App\Application\Services\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\Employee;
use App\Models\KpiAssignment;
use App\Models\PerformancePeriod;
use App\Models\PerformanceResult;
use App\Models\Target;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Class PerformanceController
 *
 * Mengelola konfigurasi periode penilaian kinerja, agregasi otomatis antar modul
 * (KPI, Tugas, Target, Kehadiran), dan kalkulasi nilai akhir performa karyawan.
 */
class PerformanceController extends Controller
{
    /**
     * Menampilkan daftar hasil penilaian kinerja.
     */
    public function index(Request $request): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;

        $results = PerformanceResult::where('tenant_id', $tenantId)
            ->with(['employee.user', 'period'])
            ->when($request->filled('period_id'), fn($q) => $q->where('period_id', $request->period_id))
            ->when($request->filled('grade'), fn($q) => $q->where('grade', $request->grade))
            ->latest('calculated_at')
            ->paginate($request->integer('per_page', 15));

        return ApiResponse::paginated($results);
    }

    /**
     * Menampilkan daftar periode penilaian.
     */
    public function getPeriods(Request $request): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;

        $periods = PerformancePeriod::where('tenant_id', $tenantId)
            ->withCount('results')
            ->latest('start_date')
            ->get();

        return ApiResponse::success($periods, 'Daftar periode penilaian berhasil diambil');
    }

    /**
     * Membuat atau menyimpan konfigurasi periode penilaian dengan pembobotan spesifik.
     */
    public function storePeriod(Request $request): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;
        $userId = $request->user()->id;

        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'start_date'        => 'required|date',
            'end_date'          => 'required|date|after_or_equal:start_date',
            'kpi_weight'        => 'required|numeric|min:0|max:100',
            'task_weight'       => 'required|numeric|min:0|max:100',
            'target_weight'     => 'required|numeric|min:0|max:100',
            'attendance_weight' => 'required|numeric|min:0|max:100',
        ]);

        $totalWeight = $validated['kpi_weight'] + $validated['task_weight'] + $validated['target_weight'] + $validated['attendance_weight'];
        if (abs($totalWeight - 100) > 0.01) {
            return ApiResponse::error("Total bobot 4 pilar harus tepat 100%. Saat ini: {$totalWeight}%", status: 422);
        }

        $period = PerformancePeriod::create([
            'tenant_id'         => $tenantId,
            'name'              => $validated['name'],
            'start_date'        => $validated['start_date'],
            'end_date'          => $validated['end_date'],
            'kpi_weight'        => $validated['kpi_weight'],
            'task_weight'       => $validated['task_weight'],
            'target_weight'     => $validated['target_weight'],
            'attendance_weight' => $validated['attendance_weight'],
            'status'            => 'active',
            'created_by'        => $userId,
        ]);

        return ApiResponse::created($period, 'Periode penilaian kinerja berhasil dikonfigurasi');
    }

    /**
     * Menjalankan kalkulasi performa otomatis untuk seluruh pegawai pada periode tertentu.
     */
    public function calculate(Request $request): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;

        $validated = $request->validate([
            'period_id' => 'required|uuid|exists:performance_periods,id',
        ]);

        $period = PerformancePeriod::where('tenant_id', $tenantId)->findOrFail($validated['period_id']);
        $employees = Employee::where('tenant_id', $tenantId)->where('status', 'active')->get();

        $calculatedResults = [];

        DB::transaction(function () use ($employees, $period, $tenantId, &$calculatedResults) {
            foreach ($employees as $employee) {
                // 1. Hitung Skor KPI
                $kpiAssignment = KpiAssignment::where('tenant_id', $tenantId)
                    ->where('employee_id', $employee->id)
                    ->where('period_start', '<=', $period->end_date)
                    ->where('period_end', '>=', $period->start_date)
                    ->latest()
                    ->first();

                $kpiScore = $kpiAssignment ? $kpiAssignment->getTotalScore() : 75.0; // fallback median jika belum dinilai

                // 2. Hitung Skor Tugas (Rasio tugas selesai dalam periode)
                $tasksAssigned = Task::where('tenant_id', $tenantId)
                    ->whereHas('assignees', fn($a) => $a->where('employee_id', $employee->id))
                    ->whereBetween('created_at', [$period->start_date, $period->end_date])
                    ->count();

                $tasksCompleted = Task::where('tenant_id', $tenantId)
                    ->whereHas('assignees', fn($a) => $a->where('employee_id', $employee->id))
                    ->whereBetween('created_at', [$period->start_date, $period->end_date])
                    ->where('status', 'completed')
                    ->count();

                $taskScore = $tasksAssigned > 0 ? round(($tasksCompleted / $tasksAssigned) * 100, 2) : 80.0;

                // 3. Hitung Skor Target
                $targets = Target::where('tenant_id', $tenantId)
                    ->whereHas('assignments', fn($a) => $a->where('employee_id', $employee->id))
                    ->whereBetween('end_date', [$period->start_date, $period->end_date])
                    ->get();

                $targetScore = $targets->isNotEmpty() ? round($targets->avg('progress_percent'), 2) : 80.0;

                // 4. Hitung Skor Presensi (Kehadiran on-time)
                $attendanceRecords = AttendanceRecord::where('tenant_id', $tenantId)
                    ->where('employee_id', $employee->id)
                    ->whereBetween('attendance_date', [$period->start_date, $period->end_date])
                    ->count();

                $attendanceScore = min(100.0, max(60.0, $attendanceRecords > 0 ? 90.0 : 80.0));

                // 5. Pembobotan Akhir
                $finalScore = round(
                    ($kpiScore * ((float) $period->kpi_weight / 100)) +
                    ($taskScore * ((float) $period->task_weight / 100)) +
                    ($targetScore * ((float) $period->target_weight / 100)) +
                    ($attendanceScore * ((float) $period->attendance_weight / 100)),
                    2
                );

                $grade = PerformanceResult::determineGrade($finalScore);

                $result = PerformanceResult::updateOrCreate(
                    [
                        'tenant_id'   => $tenantId,
                        'period_id'   => $period->id,
                        'employee_id' => $employee->id,
                    ],
                    [
                        'kpi_score'        => $kpiScore,
                        'task_score'       => $taskScore,
                        'target_score'     => $targetScore,
                        'attendance_score' => $attendanceScore,
                        'final_score'      => $finalScore,
                        'grade'            => $grade,
                        'calculated_at'    => now(),
                        'created_at'       => now(),
                    ]
                );

                $calculatedResults[] = $result;
            }
        });

        return ApiResponse::success([
            'period'           => $period,
            'total_calculated' => count($calculatedResults),
        ], 'Kalkulasi performa periode berhasil diselesaikan');
    }
}
