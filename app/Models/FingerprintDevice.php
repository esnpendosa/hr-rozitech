<?php

namespace App\Models;

use App\Domain\Tenant\HasTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FingerprintDevice extends Model
{
    use HasUuids, HasTenant, SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;
    protected $table = 'fingerprint_devices';

    protected $fillable = [
        'tenant_id',
        'branch_id',
        'name',
        'model',
        'serial_number',
        'ip_address',
        'port',
        'api_url',
        'api_key',
        'provider',
        'status',
        'last_sync_at',
        'last_seen_at',
        'extra_config',
    ];

    protected $casts = [
        'port'         => 'integer',
        'last_sync_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'extra_config' => 'array',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function deviceUsers(): HasMany
    {
        return $this->hasMany(FingerprintDeviceUser::class, 'device_id');
    }

    public function mappings(): HasMany
    {
        return $this->hasMany(FingerprintMapping::class, 'device_id');
    }

    public function syncRuns(): HasMany
    {
        return $this->hasMany(FingerprintSyncRun::class, 'device_id');
    }
}
