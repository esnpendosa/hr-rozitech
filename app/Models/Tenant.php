<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name', 'slug', 'domain', 'status',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function subscription()
    {
        return $this->hasOne(\App\Models\Subscription::class)->latest();
    }

    /**
     * Cek apakah akun tenant terkunci karena masa uji coba/langganan telah berakhir
     */
    public function isSubscriptionLocked(): bool
    {
        $sub = $this->subscription;
        if (!$sub) {
            return false;
        }

        if (in_array($sub->status, ['expired', 'suspended', 'cancelled'])) {
            return true;
        }

        if ($sub->status === 'trial') {
            return $sub->trial_ends_at && $sub->trial_ends_at->isPast();
        }

        if ($sub->status === 'active') {
            return $sub->ends_at && $sub->ends_at->isPast();
        }

        return false;
    }

    /**
     * Cek apakah tenant sedang dalam masa uji coba aktif
     */
    public function isTrial(): bool
    {
        $sub = $this->subscription;
        return $sub && $sub->status === 'trial' && (!$sub->trial_ends_at || $sub->trial_ends_at->isFuture());
    }

    /**
     * Hitung sisa hari masa uji coba
     */
    public function trialDaysRemaining(): int
    {
        $sub = $this->subscription;
        if (!$sub || $sub->status !== 'trial' || !$sub->trial_ends_at) {
            return 0;
        }
        return max(0, (int) ceil(now()->diffInDays($sub->trial_ends_at, false)));
    }
}
