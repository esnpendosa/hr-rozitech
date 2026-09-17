<?php

namespace App\Models;

use App\Domain\Tenant\HasTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class InventoryTransaction
 *
 * Catatan mutasi stok barang (Masuk, Keluar, Penyesuaian, Transfer).
 */
class InventoryTransaction extends Model
{
    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;
    protected $table = 'inventory_transactions';

    protected $fillable = [
        'tenant_id',
        'item_id',
        'type',
        'quantity',
        'reference',
        'notes',
        'employee_id',
        'created_by',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class, 'item_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
