<?php

namespace App\Http\Controllers\Api\V1\Report;

use App\Application\Services\ApiResponse;
use App\Http\Controllers\Controller;
use App\Jobs\GenerateReportJob;
use App\Models\AttendanceRecord;
use App\Models\PerformanceResult;
use App\Models\Target;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class ReportController
 *
 * Mengelola agregasi data laporan (Presensi, Tugas, Target, dan Kinerja)
 * serta pemicu eksekusi antrian ekspor berkas (Excel / PDF).
 */
class ReportController extends Controller
{
    /**
     * Agregasi ringkasan data presensi bulanan/rentang tanggal.
     */
    public function attendance(Request $request): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;

        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());

        $records = AttendanceRecord::where('tenant_id', $tenantId)
            ->whereBetween('attendance_date', [$startDate, $endDate])
            ->get();

        $summary = [
            'total_records'   => $records->count(),
            'present_count'   => $records->where('status', 'present')->count(),
            'late_count'      => $records->where('status', 'late')->count(),
            'leave_count'     => $records->where('status', 'on_leave')->count(),
            'absent_count'    => $records->where('status', 'absent')->count(),
            'total_late_mins' => $records->sum('late_minutes'),
        ];

        return ApiResponse::success($summary, 'Laporan presensi berhasil diagregasikan');
    }

    /**
     * Agregasi ringkasan penyelesaian tugas kerja (Task Completion Rate).
     */
    public function tasks(Request $request): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;

        $totalTasks = Task::where('tenant_id', $tenantId)->count();
        $completedTasks = Task::where('tenant_id', $tenantId)->where('status', 'completed')->count();
        $overdueTasks = Task::where('tenant_id', $tenantId)->where('status', 'overdue')->count();
        $inProgressTasks = Task::where('tenant_id', $tenantId)->where('status', 'in_progress')->count();

        $summary = [
            'total_tasks'     => $totalTasks,
            'completed'       => $completedTasks,
            'overdue'         => $overdueTasks,
            'in_progress'     => $inProgressTasks,
            'completion_rate' => $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 2) : 0,
        ];

        return ApiResponse::success($summary, 'Laporan penugasan tugas berhasil diagregasikan');
    }

    /**
     * Agregasi pencapaian target dan distribusi risiko.
     */
    public function targets(Request $request): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;

        $targets = Target::where('tenant_id', $tenantId)->get();

        $summary = [
            'total_targets'     => $targets->count(),
            'on_track'          => $targets->where('risk_level', 'on_track')->count(),
            'at_risk'           => $targets->where('risk_level', 'at_risk')->count(),
            'critical'          => $targets->where('risk_level', 'critical')->count(),
            'completed'         => $targets->where('risk_level', 'completed')->count(),
            'avg_achievement'   => $targets->isNotEmpty() ? round($targets->avg('progress_percent'), 2) : 0,
        ];

        return ApiResponse::success($summary, 'Laporan target kinerja berhasil diagregasikan');
    }

    /**
     * Agregasi matriks performa dan distribusi grade pegawai.
     */
    public function performance(Request $request): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;

        $results = PerformanceResult::where('tenant_id', $tenantId)->get();

        $summary = [
            'total_evaluated' => $results->count(),
            'grade_a_count'   => $results->where('grade', 'A')->count(),
            'grade_b_count'   => $results->where('grade', 'B')->count(),
            'grade_c_count'   => $results->where('grade', 'C')->count(),
            'grade_d_count'   => $results->where('grade', 'D')->count(),
            'average_score'   => $results->isNotEmpty() ? round($results->avg('final_score'), 2) : 0,
        ];

        return ApiResponse::success($summary, 'Laporan nilai kinerja berhasil diagregasikan');
    }

    /**
     * Memicu pembuatan file ekspor laporan secara asinkron melalui antrian background.
     */
    public function triggerExport(Request $request, string $type): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;
        $userId = $request->user()->id;

        $validated = $request->validate([
            'format'     => 'nullable|in:xlsx,pdf,csv',
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date',
        ]);

        $format = $validated['format'] ?? 'xlsx';

        GenerateReportJob::dispatch(
            reportType: $type,
            tenantId: $tenantId,
            userId: $userId,
            filters: $validated,
            format: $format
        );

        return ApiResponse::success([
            'report_type' => $type,
            'format'      => $format,
            'status'      => 'queued',
        ], 'Antrian ekspor laporan telah berhasil dijadwalkan');
    }
}
