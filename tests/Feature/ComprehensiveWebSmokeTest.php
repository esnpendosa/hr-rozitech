<?php

namespace Tests\Feature;

use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Class ComprehensiveWebSmokeTest
 *
 * Memverifikasi seluruh halaman web dapat diakses dengan respons HTTP 200 tanpa error 500.
 */
class ComprehensiveWebSmokeTest extends TestCase
{
    use DatabaseTransactions;

    private User $user;
    private Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::create([
            'name'   => 'Smoke Test Workspace',
            'slug'   => 'smoke-test-' . uniqid(),
            'status' => 'active',
        ]);

        $plan = SubscriptionPlan::first();
        Subscription::create([
            'tenant_id' => $this->tenant->id,
            'plan_id'   => $plan?->id,
            'status'    => 'active',
            'starts_at' => now(),
            'ends_at'   => now()->addYear(),
        ]);

        $this->user = User::create([
            'tenant_id' => $this->tenant->id,
            'name'      => 'Smoke Test Owner',
            'email'     => 'smoke.' . uniqid() . '@example.com',
            'password'  => bcrypt('password123'),
        ]);
        $this->user->assignRole('owner');
    }

    public function test_all_major_web_pages_load_successfully(): void
    {
        $urls = [
            '/dashboard',
            '/employees',
            '/employees/create',
            '/organization',
            '/branches',
            '/departments',
            '/teams',
            '/positions',
            '/attendance',
            '/attendance/calendar',
            '/attendance/leaves',
            '/attendance/shifts',
            '/attendance/corrections',
            '/fingerprint',
            '/fingerprint/mappings',
            '/fingerprint/sync-logs',
            '/tasks',
            '/tasks/kanban',
            '/projects',
            '/targets',
            '/kpi',
            '/kpi/templates',
            '/performance',
            '/customers',
            '/customers/jobs',
            '/accounting',
            '/inventory',
            '/reports',
            '/notifications',
            '/audit-logs',
            '/settings/company',
            '/settings/users',
            '/settings/roles',
            '/settings/subscription',
            '/ai-assistant',
        ];

        foreach ($urls as $url) {
            $response = $this->actingAs($this->user)->get($url);
            $this->assertEquals(
                200,
                $response->getStatusCode(),
                "Halaman {$url} gagal dimuat (Status: {$response->getStatusCode()})"
            );
        }
    }
}
