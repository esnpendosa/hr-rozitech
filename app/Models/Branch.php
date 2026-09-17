<?php

namespace App\Models;

use App\Domain\Tenant\HasTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use HasUuids, HasTenant, SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'tenant_id', 'company_id', 'name', 'code', 'address',
        'city', 'province', 'latitude', 'longitude', 'phone',
        'manager_user_id', 'is_active',
    ];
}
