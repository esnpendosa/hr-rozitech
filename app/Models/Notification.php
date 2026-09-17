<?php

namespace App\Models;

use App\Domain\Tenant\HasTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasUuids, HasTenant;

    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'tenant_id', 'user_id', 'type', 'title', 'body', 'data',
        'read_at', 'sent_via_fcm', 'fcm_message_id',
    ];

    protected $casts = [
        'data'         => 'array',
        'read_at'      => 'datetime',
        'sent_via_fcm' => 'boolean',
    ];
}
