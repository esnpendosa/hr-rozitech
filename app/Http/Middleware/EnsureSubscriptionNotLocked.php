<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;

/**
 * Middleware EnsureSubscriptionNotLocked
 *
 * Memastikan akun tenant tidak sedang terkunci akibat masa uji coba (trial)
 * atau masa berlangganan yang telah kedaluwarsa.
 * Jika terkunci, pengguna wajib diarahkan ke halaman upgrade (/subscription/locked).
 */
class EnsureSubscriptionNotLocked
{
    public function handle(Request $request, Closure $next): mixed
    {
        $user = $request->user();

        // Jika belum login atau tidak terikat tenant (misal Super Admin sistem), lanjutkan
        if (!$user || !$user->tenant_id) {
            return $next($request);
        }

        // Rute yang dikecualikan dari penguncian (agar pengguna bisa upgrade, logout, dll)
        if ($request->routeIs([
            'subscription.locked',
            'subscription.upgrade',
            'subscription.upgrade.process',
            'subscription.simulate-expired',
            'subscription.simulate-active',
            'logout',
            'locale.*',
        ]) || $request->is('logout', 'locale/*', 'subscription/*')) {
            return $next($request);
        }

        $tenant = Tenant::find($user->tenant_id);

        if ($tenant && $tenant->isSubscriptionLocked()) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success'   => false,
                    'message'   => 'Masa uji coba gratis atau paket langganan Anda telah berakhir. Akun terkunci, silakan upgrade paket Anda.',
                    'is_locked' => true,
                ], 403);
            }

            return redirect()->route('subscription.locked');
        }

        return $next($request);
    }
}
