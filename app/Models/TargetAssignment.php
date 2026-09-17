<?php

namespace App\Models;

use App\Domain\Tenant\HasTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class TargetAssignment
 *
 * Menghubungkan target ke pegawai (Employee) atau tim kerja (Team).
 */
class TargetAssignment extends Model
{
    use HasUuids, HasTenant;

    public $timestamps = false;
    protected $keyType = 'string';
    public $incrementing = false;
    protected $table = 'target_assignments';

    protected $fillable = [
        'tenant_id',
        'target_id',
        'assignee_type',
        'employee_id',
        'team_id',
        'created_at',
    ];

    public function target(): BelongsTo
    {
        return $this->belongsTo(Target::class, 'target_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_id');
    }
}
