<?php

namespace App\Models;

use App\Domain\Tenant\HasTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class KpiTemplate
 *
 * Kerangka kerja penilaian KPI (Key Performance Indicator) terstandarisasi
 * yang menampung sekumpulan metrik beserta bobot persentase evaluasi (total 100%).
 */
class KpiTemplate extends Model
{
    use HasUuids, HasTenant, SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;
    protected $table = 'kpi_templates';

    protected $fillable = [
        'tenant_id',
        'name',
        'description',
        'period',
        'is_active',
        'version',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'version'   => 'integer',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function metrics(): HasMany
    {
        return $this->hasMany(KpiMetric::class, 'template_id')->orderBy('sort_order');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(KpiAssignment::class, 'template_id');
    }
}
