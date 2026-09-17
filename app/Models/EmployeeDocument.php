<?php

namespace App\Models;

use App\Domain\Tenant\HasTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class EmployeeDocument extends Model
{
    use HasUuids, HasTenant;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'tenant_id',
        'employee_id',
        'document_type',
        'title',
        'file_path',
        'file_size',
        'mime_type',
        'notes',
        'uploaded_by',
    ];

    protected $hidden = ['file_path']; // exposed via signed URL only

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Generate a temporary signed URL for file download.
     */
    public function getDownloadUrl(int $minutes = 30): string
    {
        return Storage::temporaryUrl($this->file_path, now()->addMinutes($minutes));
    }
}
