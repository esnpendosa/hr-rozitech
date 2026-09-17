<?php

namespace App\Models;

use App\Domain\Tenant\HasTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkShift extends Model
{
    use HasUuids, HasTenant, SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'tenant_id',
        'name',
        'code',
        'start_time',
        'end_time',
        'tolerance_late_minutes',
        'tolerance_early_leave_minutes',
        'overtime_threshold_minutes',
        'is_overnight',
        'is_active',
    ];

    protected $casts = [
        'is_overnight'                  => 'boolean',
        'is_active'                     => 'boolean',
        'tolerance_late_minutes'        => 'integer',
        'tolerance_early_leave_minutes' => 'integer',
        'overtime_threshold_minutes'    => 'integer',
    ];

    public function schedules(): HasMany
    {
        return $this->hasMany(WorkSchedule::class, 'shift_id');
    }
}
