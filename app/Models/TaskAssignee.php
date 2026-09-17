<?php

namespace App\Models;

use App\Domain\Tenant\HasTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class TaskAssignee
 *
 * Menghubungkan pegawai (Employee) yang menerima tanggung jawab pengerjaan suatu tugas.
 */
class TaskAssignee extends Model
{
    use HasUuids, HasTenant;

    public $timestamps = false;
    protected $keyType = 'string';
    public $incrementing = false;
    protected $table = 'task_assignees';

    protected $fillable = [
        'tenant_id',
        'task_id',
        'employee_id',
        'assigned_at',
        'created_at',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'created_at'  => 'datetime',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
