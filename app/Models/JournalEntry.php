<?php

namespace App\Models;

use App\Domain\Tenant\HasTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class JournalEntry
 *
 * Jurnal Transaksi Keuangan (Debit / Kredit).
 */
class JournalEntry extends Model
{
    use HasUuids, HasTenant, SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;
    protected $table = 'journal_entries';

    protected $fillable = [
        'tenant_id',
        'entry_number',
        'transaction_date',
        'description',
        'reference',
        'total_amount',
        'status',
        'created_by',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'total_amount'     => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(JournalItem::class, 'journal_entry_id');
    }
}
