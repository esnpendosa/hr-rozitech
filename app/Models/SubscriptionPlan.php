<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;
    protected $table = 'subscription_plans';

    protected $fillable = ['name', 'slug', 'description', 'price', 'annual_price', 'currency', 'billing_cycle', 'badge', 'is_active'];

    protected $casts = [
        'price'        => 'decimal:2',
        'annual_price' => 'decimal:2',
        'is_active'    => 'boolean',
    ];

    public function features()
    {
        return $this->hasMany(PlanFeature::class, 'plan_id');
    }
}
