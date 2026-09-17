<?php

namespace App\Models;

use App\Domain\Tenant\HasTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FingerprintSyncRun extends Model
{
    use HasUuids, HasTenant;

    public $timestamps = false;
    protected $keyType = 'string';
    public $incrementing = false;
    protected $table = 'fingerprint_sync_runs';

    protected $fillable = [
        'tenant_id',
        'device_id',
        'status',
        'started_at',
        'completed_at',
        'records_fetched',
        'records_processed',
        'records_failed',
        'sync_from',
        'sync_to',
        'error_message',
    ];

    protected $casts = [
        'started_at'        => 'datetime',
        'completed_at'      => 'datetime',
        'sync_from'         => 'datetime',
        'sync_to'           => 'datetime',
        'records_fetched'   => 'integer',
        'records_processed' => 'integer',
        'records_failed'    => 'integer',
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(FingerprintDevice::class, 'device_id');
    }

    public function errors(): HasMany
    {
        return $this->hasMany(FingerprintSyncError::class, 'sync_run_id');
    }
}
