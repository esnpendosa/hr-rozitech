<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Class AuthWebController
 *
 * Mengelola otentikasi antarmuka web RMIH:
 * - Registrasi mandiri (Self-Registration) dengan pemilihan paket langganan langsung
 * - Masuk akun (Login)
 * - Keluar akun (Logout)
 */
class AuthWebController extends Controller
{
    /**
     * Tampilkan formulir pendaftaran mandiri beserta pilihan paket
     */
    public function showRegister(Request $request): View
    {
        $selectedPlanSlug = $request->query('plan', 'operations');
        $selectedCycle = $request->query('cycle', 'annual');

        $plans = SubscriptionPlan::where('is_active', true)
            ->with('features')
            ->orderBy('price', 'asc')
            ->get();

        $selectedPlan = $plans->firstWhere('slug', $selectedPlanSlug) ?? $plans->first();

        return view('auth.register', compact('plans', 'selectedPlan', 'selectedPlanSlug', 'selectedCycle'));
    }

    /**
     * Proses registrasi akun, perusahaan (tenant), dan paket langganan
     */
    public function processRegister(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'company_name'      => 'required|string|max:255',
            'email'             => 'required|email|max:255|unique:users,email',
            'phone'             => 'nullable|string|max:25',
            'password'          => 'required|string|min:8|confirmed',
            'plan_slug'         => 'required|string',
            'billing_cycle'     => 'required|string|in:monthly,annual',
            'subscription_mode' => 'nullable|string|in:trial,paid',
        ], [
            'email.unique'       => 'Alamat email ini sudah terdaftar di sistem.',
            'password.min'       => 'Kata sandi minimal terdiri dari 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $isTrialMode = ($request->input('subscription_mode') === 'trial') || ($validated['plan_slug'] === 'trial');
        $planSlug = $validated['plan_slug'] === 'trial' ? 'operations' : $validated['plan_slug'];

        $plan = SubscriptionPlan::where('slug', $planSlug)->first()
            ?? SubscriptionPlan::where('slug', 'operations')->firstOrFail();

        DB::beginTransaction();
        try {
            // 1. Buat Tenant / Organisasi Baru
            $baseSlug = Str::slug($validated['company_name']);
            $uniqueSlug = $baseSlug . '-' . strtolower(Str::random(5));

            $tenant = Tenant::create([
                'name'   => $validated['company_name'],
                'slug'   => $uniqueSlug,
                'status' => 'active',
            ]);

            // 2. Buat Pengguna Utama (Admin/Owner)
            $user = User::create([
                'tenant_id' => $tenant->id,
                'name'      => $validated['name'],
                'email'     => $validated['email'],
                'password'  => Hash::make($validated['password']),
                'locale'    => 'id',
            ]);

            // Berikan peran 'owner'
            if (method_exists($user, 'assignRole')) {
                $user->assignRole('owner');
            }

            // 3. Buat Data Langganan (Subscription)
            $isAnnual = $validated['billing_cycle'] === 'annual';

            if ($isTrialMode) {
                // Mode Uji Coba Gratis 14 Hari (Setelah 14 hari akun akan terkunci dan wajib upgrade)
                $trialDays = 14;
                Subscription::create([
                    'tenant_id'     => $tenant->id,
                    'plan_id'       => $plan->id,
                    'status'        => 'trial',
                    'trial_ends_at' => now()->addDays($trialDays),
                    'starts_at'     => now(),
                    'ends_at'       => now()->addDays($trialDays),
                ]);

                $successMessage = "Selamat datang di RMIH Platform! Akun Anda aktif dalam masa Uji Coba Gratis {$trialDays} hari. Setelah masa uji coba berakhir, akun akan terkunci otomatis dan perlu upgrade paket.";
            } else {
                // Mode Langganan Berbayar Langsung
                Subscription::create([
                    'tenant_id'     => $tenant->id,
                    'plan_id'       => $plan->id,
                    'status'        => 'active',
                    'trial_ends_at' => null,
                    'starts_at'     => now(),
                    'ends_at'       => $isAnnual ? now()->addYear() : now()->addMonth(),
                ]);

                $successMessage = "Selamat datang di RMIH Platform! Paket langganan {$plan->name} berhasil diaktifkan untuk organisasi {$tenant->name}.";
            }

            DB::commit();

            // 4. Login Otomatis & Alihkan ke Dashboard
            Auth::login($user);
            $request->session()->regenerate();

            return redirect()->route('dashboard')->with('success', $successMessage);

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->withErrors([
                'general' => 'Terjadi kendala saat mendaftarkan akun: ' . $e->getMessage(),
            ]);
        }
    }
}
