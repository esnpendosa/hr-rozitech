<?php

namespace App\Models;

use App\Domain\Tenant\HasTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasUuids, HasTenant, SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'tenant_id', 'name', 'logo', 'email', 'phone', 'address',
        'city', 'province', 'postal_code', 'country', 'tax_number',
        'industry', 'website',
    ];
}
