<?php

namespace Tests\Feature;

use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Models\User;
use Tests\TestCase;

class SubscriptionLockoutAndAiTest extends TestCase
{
    public function test_registration_page_shows_trial_option(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertSee('Coba Gratis 14 Hari');
        $response->assertSee('terkunci otomatis');
        $response->assertSee('Starter');
        $response->assertSee('Operations');
        $response->assertSee('Enterprise');
    }

    public function test_registration_with_explicit_trial_mode(): void
    {
        $plan = SubscriptionPlan::where('slug', 'operations')->first();
        $this->assertNotNull($plan);

        $email = 'trial.user.' . uniqid() . '@example.com';

        $response = $this->post('/register', [
            'name'                  => 'Bambang Trial',
            'company_name'          => 'PT Trial Mandiri',
            'email'                 => $email,
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'plan_slug'             => 'operations',
            'billing_cycle'         => 'annual',
            'subscription_mode'     => 'trial',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();

        $user = User::where('email', $email)->first();
        $this->assertNotNull($user);

        $tenant = Tenant::find($user->tenant_id);
        $this->assertNotNull($tenant);

        $subscription = $tenant->subscription;
        $this->assertNotNull($subscription);
        $this->assertEquals('trial', $subscription->status);
        $this->assertNotNull($subscription->trial_ends_at);
        $this->assertTrue($tenant->isTrial());
        $this->assertFalse($tenant->isSubscriptionLocked());
    }

    public function test_locked_account_is_redirected_to_subscription_locked(): void
    {
        // 1. Buat tenant dengan langganan expired
        $tenant = Tenant::create([
            'name'   => 'PT Terkunci Abadi',
            'slug'   => 'pt-terkunci-' . uniqid(),
            'status' => 'active',
        ]);

        $plan = SubscriptionPlan::first();

        Subscription::create([
            'tenant_id'     => $tenant->id,
            'plan_id'       => $plan->id,
            'status'        => 'expired',
            'trial_ends_at' => now()->subDays(2),
            'starts_at'     => now()->subMonth(),
            'ends_at'       => now()->subDay(),
        ]);

        $user = User::create([
            'tenant_id' => $tenant->id,
            'name'      => 'Admin Terkunci',
            'email'     => 'locked.' . uniqid() . '@example.com',
            'password'  => bcrypt('password123'),
        ]);

        $this->assertTrue($tenant->isSubscriptionLocked());

        // 2. Coba akses dashboard
        $response = $this->actingAs($user)->get('/dashboard');

        // Harus dialihkan ke /subscription/locked
        $response->assertRedirect('/subscription/locked');
    }

    public function test_locked_page_displays_plans_and_upgrade_form(): void
    {
        $tenant = Tenant::create([
            'name'   => 'PT Coba Kunci',
            'slug'   => 'pt-coba-kunci-' . uniqid(),
            'status' => 'active',
        ]);

        $plan = SubscriptionPlan::first();

        Subscription::create([
            'tenant_id'     => $tenant->id,
            'plan_id'       => $plan->id,
            'status'        => 'trial',
            'trial_ends_at' => now()->subDay(),
            'starts_at'     => now()->subDays(15),
            'ends_at'       => now()->subDay(),
        ]);

        $user = User::create([
            'tenant_id' => $tenant->id,
            'name'      => 'Owner Uji Coba',
            'email'     => 'trial.lock.' . uniqid() . '@example.com',
            'password'  => bcrypt('password123'),
        ]);

        $response = $this->actingAs($user)->get('/subscription/locked');

        $response->assertStatus(200);
        $response->assertSee('Akses Organisasi Terkunci');
        $response->assertSee('Starter');
        $response->assertSee('Operations');
        $response->assertSee('Enterprise');
        $response->assertSee('Mesin Absensi X Solutions');
    }

    public function test_user_can_upgrade_and_unlock_account(): void
    {
        $tenant = Tenant::create([
            'name'   => 'PT Siap Upgrade',
            'slug'   => 'pt-upgrade-' . uniqid(),
            'status' => 'active',
        ]);

        $starterPlan = SubscriptionPlan::where('slug', 'starter')->first();
        $operationsPlan = SubscriptionPlan::where('slug', 'operations')->first();

        Subscription::create([
            'tenant_id'     => $tenant->id,
            'plan_id'       => $starterPlan->id,
            'status'        => 'expired',
            'trial_ends_at' => now()->subDays(1),
            'starts_at'     => now()->subMonths(2),
            'ends_at'       => now()->subDays(1),
        ]);

        $user = User::create([
            'tenant_id' => $tenant->id,
            'name'      => 'Owner Upgrade',
            'email'     => 'upgrade.' . uniqid() . '@example.com',
            'password'  => bcrypt('password123'),
        ]);

        // Kirim upgrade request
        $response = $this->actingAs($user)->post('/subscription/upgrade', [
            'plan_slug'     => 'operations',
            'billing_cycle' => 'annual',
        ]);

        $response->assertRedirect('/dashboard');

        // Verifikasi langganan aktif
        $tenant->refresh();
        $sub = $tenant->subscription;
        $this->assertEquals('active', $sub->status);
        $this->assertEquals($operationsPlan->id, $sub->plan_id);
        $this->assertFalse($tenant->isSubscriptionLocked());
    }

    public function test_ai_assistant_page_loads(): void
    {
        $tenant = Tenant::create([
            'name'   => 'PT AI Corpora',
            'slug'   => 'pt-ai-' . uniqid(),
            'status' => 'active',
        ]);

        $plan = SubscriptionPlan::first();
        Subscription::create([
            'tenant_id' => $tenant->id,
            'plan_id'   => $plan->id,
            'status'    => 'active',
            'starts_at' => now(),
            'ends_at'   => now()->addYear(),
        ]);

        $user = User::create([
            'tenant_id' => $tenant->id,
            'name'      => 'Direktur HR',
            'email'     => 'dir.hr.' . uniqid() . '@example.com',
            'password'  => bcrypt('password123'),
        ]);

        $response = $this->actingAs($user)->get('/ai-assistant');

        $response->assertStatus(200);
        $response->assertSee('RMIH Lanjutan');
        $response->assertSee('Asisten AI Enterprise');
        $response->assertSee('Konteks Live Workspace');
    }

    public function test_ai_assistant_chat_interaction_and_history(): void
    {
        $tenant = Tenant::create([
            'name'   => 'PT Solusi Bangsa',
            'slug'   => 'pt-solusi-' . uniqid(),
            'status' => 'active',
        ]);

        $plan = SubscriptionPlan::first();
        Subscription::create([
            'tenant_id' => $tenant->id,
            'plan_id'   => $plan->id,
            'status'    => 'active',
            'starts_at' => now(),
            'ends_at'   => now()->addYear(),
        ]);

        $user = User::create([
            'tenant_id' => $tenant->id,
            'name'      => 'HR Specialist',
            'email'     => 'hr.spec.' . uniqid() . '@example.com',
            'password'  => bcrypt('password123'),
        ]);

        // Chat tentang Mesin X Solutions
        $response = $this->actingAs($user)->post('/ai-assistant/chat', [
            'prompt' => 'Bagaimana status koneksi Mesin Absensi X Solutions dan cara ADMS bekerja?',
        ]);

        $response->assertRedirect('/ai-assistant');

        // Pastikan sesi percakapan terisi
        $history = session('ai_chat_history');
        $this->assertIsArray($history);
        $this->assertNotEmpty($history);
        $this->assertStringContainsString('Mesin Absensi X Solutions', $history[1]['content']);

        // Test bersihkan riwayat
        $clearResponse = $this->actingAs($user)->post('/ai-assistant/clear');
        $clearResponse->assertRedirect('/ai-assistant');
        $this->assertEmpty(session('ai_chat_history'));
    }
}
