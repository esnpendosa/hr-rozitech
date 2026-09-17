<?php

namespace App\Models;

use App\Domain\Tenant\HasTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OvertimeRequest extends Model
{
    use HasUuids, HasTenant;

    protected $keyType = 'string';
    public $incrementing = false;
    protected $table = 'overtime_requests';

    protected $fillable = [
        'tenant_id',
        'employee_id',
        'overtime_date',
        'start_time',
        'end_time',
        'total_minutes',
        'reason',
        'status',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
        'submitted_at',
    ];

    protected $casts = [
        'overtime_date' => 'date',
        'total_minutes' => 'integer',
        'reviewed_at'   => 'datetime',
        'submitted_at'  => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
