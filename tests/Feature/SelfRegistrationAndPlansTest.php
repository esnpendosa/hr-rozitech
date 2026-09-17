<?php

namespace Tests\Feature;

use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SelfRegistrationAndPlansTest extends TestCase
{
    public function test_registration_page_loads_with_active_plans(): void
    {
        $response = $this->get('/register?plan=operations&cycle=annual');

        $response->assertStatus(200);
        $response->assertSee('Daftar Mandiri');
        $response->assertSee('Starter');
        $response->assertSee('Operations');
        $response->assertSee('Enterprise');
        $response->assertSee('Mesin Absensi X Solutions');
    }

    public function test_user_can_self_register_and_activate_plan(): void
    {
        $plan = SubscriptionPlan::where('slug', 'operations')->first();
        $this->assertNotNull($plan);

        $email = 'ahmad.' . uniqid() . '@example.com';

        $response = $this->post('/register', [
            'name'                  => 'Ahmad Testing',
            'company_name'          => 'PT Inovasi Berdikari',
            'email'                 => $email,
            'phone'                 => '08123456789',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'plan_slug'             => 'operations',
            'billing_cycle'         => 'annual',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();

        // Verifikasi User dan Tenant dibuat
        $user = User::where('email', $email)->first();
        $this->assertNotNull($user);
        $this->assertEquals('Ahmad Testing', $user->name);

        $tenant = Tenant::find($user->tenant_id);
        $this->assertNotNull($tenant);
        $this->assertEquals('PT Inovasi Berdikari', $tenant->name);

        // Verifikasi langganan dibuat
        $subscription = $tenant->subscription;
        $this->assertNotNull($subscription);
        $this->assertEquals($plan->id, $subscription->plan_id);
        $this->assertEquals('active', $subscription->status);
    }
}
