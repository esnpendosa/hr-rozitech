<?php

namespace App\Models;

use App\Domain\Tenant\HasTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class KpiResult
 *
 * Realisasi capaian aktual dan skor berbobot per metrik KPI yang tersimpan permanen/terkunci.
 */
class KpiResult extends Model
{
    use HasUuids, HasTenant;

    protected $keyType = 'string';
    public $incrementing = false;
    protected $table = 'kpi_results';

    protected $fillable = [
        'tenant_id',
        'assignment_id',
        'metric_id',
        'actual_value',
        'score',
        'weighted_score',
        'notes',
        'recorded_by',
    ];

    protected $casts = [
        'actual_value'   => 'decimal:2',
        'score'          => 'decimal:2',
        'weighted_score' => 'decimal:2',
    ];

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(KpiAssignment::class, 'assignment_id');
    }

    public function metric(): BelongsTo
    {
        return $this->belongsTo(KpiMetric::class, 'metric_id');
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
