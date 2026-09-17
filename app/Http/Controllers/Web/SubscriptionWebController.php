<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Controller SubscriptionWebController
 *
 * Mengelola antarmuka akun terkunci (lockout) akibat masa uji coba
 * atau langganan kedaluwarsa, serta proses upgrade/pembayaran paket.
 */
class SubscriptionWebController extends Controller
{
    /**
     * Tampilan akun terkunci ketika masa uji coba / langganan habis
     */
    public function locked(Request $request): View|RedirectResponse
    {
        $user = Auth::user();
        if (!$user || !$user->tenant_id) {
            return redirect()->route('dashboard');
        }

        $tenant = Tenant::with('subscription.plan')->find($user->tenant_id);

        // Jika akun tidak terkunci, arahkan ke dashboard
        if ($tenant && !$tenant->isSubscriptionLocked()) {
            return redirect()->route('dashboard')->with('info', 'Akun organisasi Anda dalam status aktif.');
        }

        $subscription = $tenant?->subscription;
        $plans = SubscriptionPlan::where('is_active', true)
            ->with('features')
            ->orderBy('price', 'asc')
            ->get();

        return view('subscription.locked', compact('tenant', 'subscription', 'plans'));
    }

    /**
     * Proses upgrade paket langganan untuk membuka kunci akun
     */
    public function upgrade(Request $request): RedirectResponse
    {
        $request->validate([
            'plan_slug'     => 'required|string|exists:subscription_plans,slug',
            'billing_cycle' => 'required|string|in:monthly,annual',
        ]);

        $user = Auth::user();
        if (!$user || !$user->tenant_id) {
            return redirect()->route('login');
        }

        $tenant = Tenant::findOrFail($user->tenant_id);
        $plan = SubscriptionPlan::where('slug', $request->input('plan_slug'))->firstOrFail();
        $isAnnual = $request->input('billing_cycle') === 'annual';

        $subscription = $tenant->subscription;
        $now = now();
        $endsAt = $isAnnual ? $now->copy()->addYear() : $now->copy()->addMonth();

        if ($subscription) {
            $subscription->update([
                'plan_id'       => $plan->id,
                'status'        => 'active',
                'trial_ends_at' => null,
                'starts_at'     => $now,
                'ends_at'       => $endsAt,
            ]);
        } else {
            Subscription::create([
                'tenant_id'     => $tenant->id,
                'plan_id'       => $plan->id,
                'status'        => 'active',
                'trial_ends_at' => null,
                'starts_at'     => $now,
                'ends_at'       => $endsAt,
            ]);
        }

        return redirect()->route('dashboard')->with(
            'success',
            "Akun berhasil dibuka! Paket {$plan->name} (" . ($isAnnual ? 'Tahunan' : 'Bulanan') . ") kini aktif penuh hingga " . $endsAt->translatedFormat('d F Y') . "."
        );
    }

    /**
     * Simulasi Akun Terkunci (untuk demonstrasi / pengujian cepat)
     */
    public function simulateExpired(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if (!$user || !$user->tenant_id) {
            return redirect()->route('login');
        }

        $tenant = Tenant::findOrFail($user->tenant_id);
        $sub = $tenant->subscription;

        if ($sub) {
            $sub->update([
                'status'        => 'expired',
                'trial_ends_at' => now()->subDay(),
                'ends_at'       => now()->subDay(),
            ]);
        }

        return redirect()->route('dashboard')->with('warning', 'Simulasi: Akun disetel ke status Expired (Terkunci).');
    }

    /**
     * Simulasi Akun Aktif Kembali (untuk demonstrasi / pengujian cepat)
     */
    public function simulateActive(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if (!$user || !$user->tenant_id) {
            return redirect()->route('login');
        }

        $tenant = Tenant::findOrFail($user->tenant_id);
        $sub = $tenant->subscription;

        if ($sub) {
            $sub->update([
                'status'        => 'active',
                'trial_ends_at' => null,
                'ends_at'       => now()->addMonths(1),
            ]);
        }

        return redirect()->route('dashboard')->with('success', 'Simulasi: Akun diaktifkan kembali.');
    }
}
