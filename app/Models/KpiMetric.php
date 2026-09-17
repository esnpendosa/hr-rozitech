<?php

namespace App\Models;

use App\Domain\Tenant\HasTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class KpiMetric
 *
 * Butir metrik indikator kinerja dalam template KPI dengan bobot, target, dan metode skoring.
 */
class KpiMetric extends Model
{
    use HasUuids, HasTenant;

    protected $keyType = 'string';
    public $incrementing = false;
    protected $table = 'kpi_metrics';

    protected $fillable = [
        'tenant_id',
        'template_id',
        'name',
        'description',
        'metric_key',
        'unit',
        'weight',
        'target_value',
        'minimum_value',
        'maximum_value',
        'scoring_method',
        'sort_order',
    ];

    protected $casts = [
        'weight'        => 'decimal:2',
        'target_value'  => 'decimal:2',
        'minimum_value' => 'decimal:2',
        'maximum_value' => 'decimal:2',
        'sort_order'    => 'integer',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(KpiTemplate::class, 'template_id');
    }

    /**
     * Hitung skor (0-100) dan skor terbobot berdasarkan nilai aktual yang dicapai.
     */
    public function calculateScore(float $actualValue): array
    {
        $target = (float) $this->target_value;
        $score = 0;

        if ($this->scoring_method === 'lower_is_better') {
            // Makin rendah makin bagus (misal: tingkat komplain, error rate)
            if ($actualValue <= $target && $target > 0) {
                $score = 100;
            } elseif ($actualValue > 0) {
                $score = max(0, round(($target / $actualValue) * 100, 2));
            }
        } else {
            // higher_is_better / target_based
            if ($target > 0) {
                $score = min(120, round(($actualValue / $target) * 100, 2)); // cap 120% max bonus
            }
        }

        $weightedScore = round(($score * ((float) $this->weight / 100)), 2);

        return [
            'score'          => $score,
            'weighted_score' => $weightedScore,
        ];
    }
}
