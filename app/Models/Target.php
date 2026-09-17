<?php

namespace App\Models;

use App\Domain\Tenant\HasTenant;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Target
 *
 * Target kinerja kuantitatif (contoh: penjualan, jumlah deal, kunjungan nasabah)
 * dengan kalkulasi otomatis risiko ketercapaian (risk_level: on_track, at_risk, critical, completed, expired).
 */
class Target extends Model
{
    use HasUuids, HasTenant, SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;
    protected $table = 'targets';

    protected $fillable = [
        'tenant_id',
        'project_id',
        'name',
        'metric',
        'target_value',
        'unit',
        'period',
        'start_date',
        'end_date',
        'actual_value',
        'progress_percent',
        'remaining_value',
        'required_daily_rate',
        'risk_level',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'start_date'          => 'date',
        'end_date'            => 'date',
        'target_value'        => 'decimal:2',
        'actual_value'        => 'decimal:2',
        'progress_percent'    => 'decimal:2',
        'remaining_value'     => 'decimal:2',
        'required_daily_rate' => 'decimal:2',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(TargetAssignment::class, 'target_id');
    }

    public function progressRecords(): HasMany
    {
        return $this->hasMany(TargetProgress::class, 'target_id')->latest('recorded_at');
    }

    /**
     * Hitung ulang metrik target: progress_percent, remaining_value, required_daily_rate, dan risk_level.
     */
    public function recalculateMetrics(): void
    {
        $target = (float) $this->target_value;
        $actual = (float) $this->actual_value;

        $progressPercent = $target > 0 ? round(($actual / $target) * 100, 2) : 0;
        $remaining = max(0, $target - $actual);

        $today = Carbon::today();
        $end = Carbon::parse($this->end_date);
        $daysRemaining = max(1, $today->diffInDays($end, false) + 1);

        $dailyRate = $remaining > 0 ? round($remaining / $daysRemaining, 2) : 0;

        // Tentukan level risiko
        $risk = 'on_track';
        if ($progressPercent >= 100) {
            $risk = 'completed';
        } elseif ($today->gt($end)) {
            $risk = 'expired';
        } else {
            $totalDays = max(1, Carbon::parse($this->start_date)->diffInDays($end));
            $daysPassed = max(0, Carbon::parse($this->start_date)->diffInDays($today));
            $expectedPercent = ($daysPassed / $totalDays) * 100;

            if ($progressPercent < ($expectedPercent * 0.5)) {
                $risk = 'critical';
            } elseif ($progressPercent < ($expectedPercent * 0.8)) {
                $risk = 'at_risk';
            }
        }

        $this->update([
            'progress_percent'    => $progressPercent,
            'remaining_value'     => $remaining,
            'required_daily_rate' => $dailyRate,
            'risk_level'          => $risk,
            'status'              => $progressPercent >= 100 ? 'completed' : ($today->gt($end) ? 'expired' : 'active'),
        ]);
    }
}
