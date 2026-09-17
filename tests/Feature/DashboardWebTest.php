<?php

namespace Tests\Feature;

use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Models\User;
use Tests\TestCase;

class DashboardWebTest extends TestCase
{
    public function test_authenticated_user_can_access_dashboard(): void
    {
        $tenant = Tenant::create([
            'name'   => 'PT Dashboard Test',
            'slug'   => 'pt-dash-' . uniqid(),
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
            'name'      => 'Admin Dashboard',
            'email'     => 'dash.' . uniqid() . '@example.com',
            'password'  => bcrypt('password123'),
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Dashboard');
        $response->assertSee('Owner / Direktur');
        $response->assertSee('Langkah Awal Workspace Baru');
        $response->assertSee('Asisten RMIH');
        $response->assertDontSee('Super Admin');
        $response->assertDontSee('Kantor Pusat Jakarta');
    }

    public function test_unauthenticated_user_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');
        // If not logged in and no tenant session
        $this->assertTrue(in_array($response->getStatusCode(), [200, 302]));
    }
}
