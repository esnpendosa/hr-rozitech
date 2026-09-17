<?php

namespace App\Models;

use App\Domain\Tenant\HasTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AttendanceRecord extends Model
{
    use HasUuids, HasTenant;

    // No SoftDeletes — attendance records are append-only

    protected $keyType = 'string';
    public $incrementing = false;
    protected $table = 'attendance_records';

    protected $fillable = [
        'tenant_id',
        'employee_id',
        'work_schedule_id',
        'attendance_date',
        'check_in_at',
        'check_out_at',
        'check_in_lat',
        'check_in_lng',
        'check_out_lat',
        'check_out_lng',
        'check_in_photo',
        'status',
        'late_minutes',
        'early_leave_minutes',
        'overtime_minutes',
        'source',
        'source_device_id',
        'source_event_id',
        'work_location_id',
        'is_outside_geofence',
        'notes',
    ];

    protected $casts = [
        'attendance_date'     => 'date',
        'check_in_at'         => 'datetime',
        'check_out_at'        => 'datetime',
        'check_in_lat'        => 'float',
        'check_in_lng'        => 'float',
        'check_out_lat'       => 'float',
        'check_out_lng'       => 'float',
        'is_outside_geofence' => 'boolean',
        'late_minutes'        => 'integer',
        'early_leave_minutes' => 'integer',
        'overtime_minutes'    => 'integer',
    ];

    // ---------------------
    // Relations
    // ---------------------

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function workSchedule(): BelongsTo
    {
        return $this->belongsTo(WorkSchedule::class);
    }

    public function workLocation(): BelongsTo
    {
        return $this->belongsTo(WorkLocation::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(AttendanceEvent::class);
    }

    public function corrections(): HasMany
    {
        return $this->hasMany(AttendanceCorrection::class);
    }
}
