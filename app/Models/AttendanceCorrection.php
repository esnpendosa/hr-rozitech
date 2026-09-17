<?php

namespace App\Models;

use App\Domain\Tenant\HasTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceCorrection extends Model
{
    use HasUuids, HasTenant;

    protected $keyType = 'string';
    public $incrementing = false;
    protected $table = 'attendance_corrections';

    protected $fillable = [
        'tenant_id',
        'employee_id',
        'attendance_record_id',
        'correction_date',
        'requested_check_in',
        'requested_check_out',
        'reason',
        'status',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
        'submitted_at',
        'created_by',
    ];

    protected $casts = [
        'correction_date'     => 'date',
        'requested_check_in'  => 'datetime',
        'requested_check_out' => 'datetime',
        'reviewed_at'         => 'datetime',
        'submitted_at'        => 'datetime',
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

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
