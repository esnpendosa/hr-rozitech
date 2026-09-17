<?php

namespace App\Http\Controllers\Api\V1\Notification;

use App\Application\Services\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class NotificationController
 *
 * Mengelola inbox notifikasi in-app dan counter pesan belum terbaca (unread count).
 */
class NotificationController extends Controller
{
    /**
     * Menampilkan daftar notifikasi pengguna (yang belum dibaca diprioritaskan).
     */
    public function index(Request $request): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;
        $userId = $request->user()->id;

        $notifications = Notification::where('tenant_id', $tenantId)
            ->where('user_id', $userId)
            ->orderByRaw('read_at IS NULL DESC')
            ->latest('created_at')
            ->paginate($request->integer('per_page', 20));

        return ApiResponse::paginated($notifications);
    }

    /**
     * Menghitung total notifikasi yang belum dibaca pengguna aktif.
     */
    public function unreadCount(Request $request): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;
        $userId = $request->user()->id;

        $count = Notification::where('tenant_id', $tenantId)
            ->where('user_id', $userId)
            ->whereNull('read_at')
            ->count();

        return ApiResponse::success(['unread_count' => $count]);
    }

    /**
     * Menandai satu notifikasi sebagai telah dibaca.
     */
    public function markAsRead(Request $request, string $id): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;
        $userId = $request->user()->id;

        $notification = Notification::where('tenant_id', $tenantId)
            ->where('user_id', $userId)
            ->findOrFail($id);

        if (!$notification->read_at) {
            $notification->update(['read_at' => now()]);
        }

        return ApiResponse::success($notification, 'Notifikasi telah ditandai dibaca');
    }

    /**
     * Menandai seluruh notifikasi yang belum dibaca menjadi terbaca.
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;
        $userId = $request->user()->id;

        Notification::where('tenant_id', $tenantId)
            ->where('user_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return ApiResponse::success(null, 'Seluruh notifikasi telah ditandai dibaca');
    }
}
