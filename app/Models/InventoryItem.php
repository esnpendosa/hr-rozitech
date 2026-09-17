<?php

namespace App\Models;

use App\Domain\Tenant\HasTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class InventoryItem
 *
 * Entitas barang stok dan aset operasional.
 */
class InventoryItem extends Model
{
    use HasUuids, HasTenant, SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;
    protected $table = 'inventory_items';

    protected $fillable = [
        'tenant_id',
        'category_id',
        'sku',
        'name',
        'unit',
        'current_stock',
        'min_stock',
        'unit_price',
        'location',
        'status',
    ];

    protected $casts = [
        'current_stock' => 'integer',
        'min_stock'     => 'integer',
        'unit_price'    => 'decimal:2',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(InventoryCategory::class, 'category_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class, 'item_id');
    }
}
