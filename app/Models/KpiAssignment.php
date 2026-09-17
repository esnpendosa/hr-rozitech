<?php

namespace App\Models;

use App\Domain\Tenant\HasTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class KpiAssignment
 *
 * Penugasan template KPI ke pegawai tertentu untuk periode evaluasi spesifik.
 */
class KpiAssignment extends Model
{
    use HasUuids, HasTenant;

    protected $keyType = 'string';
    public $incrementing = false;
    protected $table = 'kpi_assignments';

    protected $fillable = [
        'tenant_id',
        'template_id',
        'employee_id',
        'period_start',
        'period_end',
        'status',
        'template_version',
        'created_by',
    ];

    protected $casts = [
        'period_start'     => 'date',
        'period_end'       => 'date',
        'template_version' => 'integer',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(KpiTemplate::class, 'template_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function results(): HasMany
    {
        return $this->hasMany(KpiResult::class, 'assignment_id');
    }

    /**
     * Hitung total skor akhir akumulatif dari seluruh metrik KPI.
     */
    public function getTotalScore(): float
    {
        return (float) $this->results()->sum('weighted_score');
    }
}
