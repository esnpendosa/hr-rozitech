<?php

namespace App\Models;

use App\Domain\Tenant\HasTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Project
 *
 * Entitas proyek yang menaungi sekelompok tugas (tasks), alokasi tim/anggota,
 * estimasi anggaran, dan timeline pelaksanaan proyek.
 */
class Project extends Model
{
    use HasUuids, HasTenant, SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;
    protected $table = 'projects';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'customer_id',
        'code',
        'name',
        'description',
        'manager_user_id',
        'start_date',
        'deadline',
        'status',
        'budget',
        'progress_percent',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'start_date'       => 'date',
        'deadline'         => 'date',
        'budget'           => 'decimal:2',
        'progress_percent' => 'decimal:2',
    ];

    /**
     * Manajer penanggung jawab proyek.
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_user_id');
    }

    /**
     * Pembuat entitas proyek.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Daftar anggota tim yang ditugaskan ke proyek ini.
     */
    public function members(): HasMany
    {
        return $this->hasMany(ProjectMember::class, 'project_id');
    }

    /**
     * Daftar tugas/pekerjaan yang berada di bawah proyek ini.
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'project_id');
    }

    /**
     * Menghitung dan menyinkronkan ulang persentase progres proyek berdasarkan tugas yang selesai.
     */
    public function recalculateProgress(): void
    {
        $totalTasks = $this->tasks()->count();
        if ($totalTasks === 0) {
            return;
        }

        $completedTasks = $this->tasks()->where('status', 'completed')->count();
        $this->update([
            'progress_percent' => round(($completedTasks / $totalTasks) * 100, 2),
        ]);
    }
}
