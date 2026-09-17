<?php

namespace App\Models;

use App\Domain\Tenant\HasTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ProjectMember
 *
 * Menghubungkan pegawai (Employee) sebagai anggota tim proyek dengan peran spesifik.
 */
class ProjectMember extends Model
{
    use HasUuids, HasTenant;

    protected $keyType = 'string';
    public $incrementing = false;
    protected $table = 'project_members';

    protected $fillable = [
        'tenant_id',
        'project_id',
        'employee_id',
        'role',
        'joined_at',
    ];

    protected $casts = [
        'joined_at' => 'date',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
