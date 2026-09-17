<?php

namespace App\Models;

use App\Domain\Tenant\HasTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class TargetProgress
 *
 * Catatan riwayat penambahan/pencapaian nilai aktual target secara berkala.
 */
class TargetProgress extends Model
{
    use HasUuids, HasTenant;

    public $timestamps = false;
    protected $keyType = 'string';
    public $incrementing = false;
    protected $table = 'target_progress';

    protected $fillable = [
        'tenant_id',
        'target_id',
        'recorded_by',
        'value',
        'notes',
        'recorded_at',
        'created_at',
    ];

    protected $casts = [
        'value'       => 'decimal:2',
        'recorded_at' => 'datetime',
        'created_at'  => 'datetime',
    ];

    public function target(): BelongsTo
    {
        return $this->belongsTo(Target::class, 'target_id');
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
