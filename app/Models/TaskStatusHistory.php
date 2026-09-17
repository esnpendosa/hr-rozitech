<?php

namespace App\Models;

use App\Domain\Tenant\HasTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class TaskStatusHistory
 *
 * Catatan audit perubahan status tugas (contoh: draft -> assigned -> in_progress -> review -> completed).
 */
class TaskStatusHistory extends Model
{
    use HasUuids, HasTenant;

    public $timestamps = false;
    protected $keyType = 'string';
    public $incrementing = false;
    protected $table = 'task_status_histories';

    protected $fillable = [
        'tenant_id',
        'task_id',
        'from_status',
        'to_status',
        'changed_by',
        'notes',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function changer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
