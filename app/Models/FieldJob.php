<?php

namespace App\Models;

use App\Domain\Tenant\HasTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class FieldJob
 *
 * Pekerjaan operasional lapangan / visitasi customer (Field Work)
 * yang dilengkapi verifikasi geolokasi GPS Check-in, catatan pengerjaan, dan tanda tangan klien.
 */
class FieldJob extends Model
{
    use HasUuids, HasTenant, SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;
    protected $table = 'field_jobs';

    protected $fillable = [
        'tenant_id',
        'customer_id',
        'project_id',
        'assigned_to',
        'assigned_by',
        'title',
        'description',
        'site_name',
        'site_address',
        'site_latitude',
        'site_longitude',
        'scheduled_at',
        'checked_in_at',
        'check_in_latitude',
        'check_in_longitude',
        'completed_at',
        'status',
        'work_notes',
        'customer_signature',
    ];

    protected $casts = [
        'scheduled_at'        => 'datetime',
        'checked_in_at'       => 'datetime',
        'completed_at'        => 'datetime',
        'site_latitude'       => 'float',
        'site_longitude'      => 'float',
        'check_in_latitude'   => 'float',
        'check_in_longitude'  => 'float',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'assigned_to');
    }

    public function assigner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
