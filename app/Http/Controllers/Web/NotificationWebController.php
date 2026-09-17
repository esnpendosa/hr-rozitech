<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Support\TenantContext;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Class NotificationWebController
 *
 * Antarmuka web pusat pemberitahuan (Notification Center).
 */
class NotificationWebController extends Controller
{
    public function index(Request $request): View
    {
        $tenantId = TenantContext::getTenantId() ?? $request->user()?->tenant_id;
        $userId = $request->user()?->id;

        $notifications = Notification::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->when($userId, fn($q) => $q->where('user_id', $userId))
            ->orderByRaw('read_at IS NULL DESC')
            ->latest('created_at')
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }
}
