<?php

namespace App\Models;

use App\Domain\Tenant\HasTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Position extends Model
{
    use HasUuids, HasTenant, SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['tenant_id', 'department_id', 'name', 'code', 'level', 'description', 'is_active'];

    protected $casts = ['is_active' => 'boolean', 'level' => 'integer'];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
