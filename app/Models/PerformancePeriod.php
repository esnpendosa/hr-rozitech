<?php

namespace App\Models;

use App\Domain\Tenant\HasTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class PerformancePeriod
 *
 * Periode penilaian kinerja terpadu dengan snapshot konfigurasi bobot pilar:
 * - KPI (default 40%)
 * - Tasks (default 20%)
 * - Targets (default 20%)
 * - Attendance (default 20%)
 */
class PerformancePeriod extends Model
{
    use HasUuids, HasTenant;

    protected $keyType = 'string';
    public $incrementing = false;
    protected $table = 'performance_periods';

    protected $fillable = [
        'tenant_id',
        'name',
        'start_date',
        'end_date',
        'config_snapshot',
        'kpi_weight',
        'task_weight',
        'target_weight',
        'attendance_weight',
        'status',
        'created_by',
    ];

    protected $casts = [
        'start_date'        => 'date',
        'end_date'          => 'date',
        'config_snapshot'   => 'array',
        'kpi_weight'        => 'decimal:2',
        'task_weight'       => 'decimal:2',
        'target_weight'     => 'decimal:2',
        'attendance_weight' => 'decimal:2',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function results(): HasMany
    {
        return $this->hasMany(PerformanceResult::class, 'period_id');
    }
}
