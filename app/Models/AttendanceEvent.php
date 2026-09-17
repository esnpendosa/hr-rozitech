<?php

namespace App\Models;

use App\Domain\Tenant\HasTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceEvent extends Model
{
    use HasUuids, HasTenant;

    // No SoftDeletes, no updated_at — events are immutable records
    public $timestamps = false;

    protected $keyType = 'string';
    public $incrementing = false;
    protected $table = 'attendance_events';

    protected $fillable = [
        'tenant_id',
        'employee_id',
        'device_id',
        'event_type',
        'event_time',
        'latitude',
        'longitude',
        'source',
        'source_device_id',
        'source_event_id',
        'raw_data',
        'processed',
        'attendance_record_id',
    ];

    protected $casts = [
        'event_time' => 'datetime',
        'latitude'   => 'float',
        'longitude'  => 'float',
        'raw_data'   => 'array',
        'processed'  => 'boolean',
        'created_at' => 'datetime',
    ];

    // ---------------------
    // Relations
    // ---------------------

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function attendanceRecord(): BelongsTo
    {
        return $this->belongsTo(AttendanceRecord::class);
    }
}
