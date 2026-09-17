<?php

namespace App\Models;

use App\Domain\Tenant\HasTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FingerprintSyncError extends Model
{
    use HasUuids, HasTenant;

    public $timestamps = false;
    protected $keyType = 'string';
    public $incrementing = false;
    protected $table = 'fingerprint_sync_errors';

    protected $fillable = [
        'tenant_id',
        'sync_run_id',
        'device_id',
        'employee_id',
        'raw_data',
        'error_message',
    ];

    protected $casts = [
        'raw_data' => 'array',
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(FingerprintDevice::class, 'device_id');
    }

    public function syncRun(): BelongsTo
    {
        return $this->belongsTo(FingerprintSyncRun::class, 'sync_run_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
