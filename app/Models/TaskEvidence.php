<?php

namespace App\Models;

use App\Domain\Tenant\HasTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class TaskEvidence
 *
 * Bukti eksekusi tugas (foto lapangan, video, berkas PDF) beserta geotag GPS.
 */
class TaskEvidence extends Model
{
    use HasUuids, HasTenant;

    public $timestamps = false;
    protected $keyType = 'string';
    public $incrementing = false;
    protected $table = 'task_evidences';

    protected $fillable = [
        'tenant_id',
        'task_id',
        'uploaded_by',
        'type',
        'file_path',
        'file_size',
        'mime_type',
        'description',
        'captured_at',
        'latitude',
        'longitude',
        'created_at',
    ];

    protected $casts = [
        'captured_at' => 'datetime',
        'created_at'  => 'datetime',
        'file_size'   => 'integer',
        'latitude'    => 'float',
        'longitude'   => 'float',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
