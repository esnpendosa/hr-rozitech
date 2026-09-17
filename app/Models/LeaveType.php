<?php

namespace App\Models;

use App\Domain\Tenant\HasTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeaveType extends Model
{
    use HasUuids, HasTenant;

    protected $keyType = 'string';
    public $incrementing = false;
    protected $table = 'leave_types';

    protected $fillable = [
        'tenant_id',
        'name',
        'code',
        'default_days',
        'is_paid',
        'requires_document',
        'is_active',
    ];

    protected $casts = [
        'default_days'      => 'integer',
        'is_paid'           => 'boolean',
        'requires_document' => 'boolean',
        'is_active'         => 'boolean',
    ];

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }
}
