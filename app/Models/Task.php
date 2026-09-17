<?php

namespace App\Models;

use App\Domain\Tenant\HasTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Task
 *
 * Entitas pekerjaan atau penugasan (Task) individual maupun kolaboratif,
 * dilengkapi pelacakan siklus hidup (lifecycle), target progres, evidence bukti kerja,
 * checklist, dan komentar interaktif.
 */
class Task extends Model
{
    use HasUuids, HasTenant, SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;
    protected $table = 'tasks';

    protected $fillable = [
        'tenant_id',
        'project_id',
        'customer_id',
        'assigned_by',
        'priority',
        'title',
        'description',
        'status',
        'start_at',
        'due_at',
        'completed_at',
        'target_value',
        'target_unit',
        'progress_value',
        'progress_percent',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'start_at'         => 'datetime',
        'due_at'           => 'datetime',
        'completed_at'     => 'datetime',
        'target_value'     => 'decimal:2',
        'progress_value'   => 'decimal:2',
        'progress_percent' => 'decimal:2',
    ];

    /**
     * Proyek induk dari tugas ini.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    /**
     * User yang memberikan penugasan tugas.
     */
    public function assigner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * Pembuat tugas.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Pegawai yang ditugaskan (penerima tugas).
     */
    public function assignees(): HasMany
    {
        return $this->hasMany(TaskAssignee::class, 'task_id');
    }

    /**
     * Daftar item periksa (checklist) di dalam tugas.
     */
    public function checklists(): HasMany
    {
        return $this->hasMany(TaskChecklist::class, 'task_id')->orderBy('sort_order');
    }

    /**
     * Bukti kerja (foto/dokumen/video/GPS) yang diunggah pelaksana.
     */
    public function evidences(): HasMany
    {
        return $this->hasMany(TaskEvidence::class, 'task_id');
    }

    /**
     * Jejak riwayat perubahan status pengerjaan tugas.
     */
    public function statusHistories(): HasMany
    {
        return $this->hasMany(TaskStatusHistory::class, 'task_id')->latest();
    }

    /**
     * Kolom komentar dan diskusi tim pada tugas.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(TaskComment::class, 'task_id')->oldest();
    }

    /**
     * Hitung ulang progres persentase tugas berdasarkan checklist selesai.
     */
    public function recalculateProgressFromChecklists(): void
    {
        $totalChecklists = $this->checklists()->count();
        if ($totalChecklists === 0) {
            return;
        }

        $completedCount = $this->checklists()->where('is_completed', true)->count();
        $percent = round(($completedCount / $totalChecklists) * 100, 2);

        $this->update([
            'progress_percent' => $percent,
            'status'           => ($percent >= 100 && $this->status === 'in_progress') ? 'review' : $this->status,
        ]);
    }
}
