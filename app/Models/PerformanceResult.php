<?php

namespace App\Models;

use App\Domain\Tenant\HasTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class PerformanceResult
 *
 * Hasil akumulasi skor kinerja komprehensif pegawai (KPI + Task + Target + Attendance) beserta predikat/grade.
 */
class PerformanceResult extends Model
{
    use HasUuids, HasTenant;

    public $timestamps = false;
    protected $keyType = 'string';
    public $incrementing = false;
    protected $table = 'performance_results';

    protected $fillable = [
        'tenant_id',
        'period_id',
        'employee_id',
        'kpi_score',
        'task_score',
        'target_score',
        'attendance_score',
        'final_score',
        'grade',
        'notes',
        'calculated_at',
        'created_at',
    ];

    protected $casts = [
        'kpi_score'        => 'decimal:2',
        'task_score'       => 'decimal:2',
        'target_score'     => 'decimal:2',
        'attendance_score' => 'decimal:2',
        'final_score'      => 'decimal:2',
        'calculated_at'    => 'datetime',
        'created_at'       => 'datetime',
    ];

    public function period(): BelongsTo
    {
        return $this->belongsTo(PerformancePeriod::class, 'period_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    /**
     * Hitung predikat grade berdasarkan final score.
     */
    public static function determineGrade(float $finalScore): string
    {
        if ($finalScore >= 90) return 'A';
        if ($finalScore >= 75) return 'B';
        if ($finalScore >= 60) return 'C';
        return 'D';
    }
}
