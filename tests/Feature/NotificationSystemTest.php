<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Class NotificationSystemTest
 *
 * Menguji sistem notifikasi aplikasi:
 * - Listing notifikasi dan unread count
 * - Menandai notifikasi telah dibaca (satuan & sekaligus)
 */
class NotificationSystemTest extends TestCase
{
    use DatabaseTransactions;

    private Tenant $tenant;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);

        $this->tenant = Tenant::create([
            'name'   => 'Notification Tenant',
            'slug'   => 'notif-test-' . uniqid(),
            'status' => 'active',
        ]);

        $this->user = User::create([
            'tenant_id' => $this->tenant->id,
            'name'      => 'Receiver User',
            'email'     => 'receiver-' . uniqid() . '@example.com',
            'password'  => bcrypt('password'),
        ]);
    }

    /**
     * Uji alur pembacaan notifikasi dan mark-as-read.
     */
    public function test_can_fetch_and_mark_notifications_as_read(): void
    {
        // 1. Buat 2 notifikasi
        $notif1 = Notification::create([
            'tenant_id' => $this->tenant->id,
            'user_id'   => $this->user->id,
            'type'      => 'task_assigned',
            'title'     => 'Tugas Baru #1',
            'body'      => 'Segera selesaikan sebelum tenggat',
        ]);

        $notif2 = Notification::create([
            'tenant_id' => $this->tenant->id,
            'user_id'   => $this->user->id,
            'type'      => 'target_risk',
            'title'     => 'Target Berisiko',
            'body'      => 'Laju harian perlu ditingkatkan',
        ]);

        // 2. Cek unread count -> harus 2
        $countResp = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/notifications/count');

        $countResp->assertStatus(200)
            ->assertJsonPath('data.unread_count', 2);

        // 3. Mark read 1 notifikasi
        $readResp = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/v1/notifications/{$notif1->id}/read");

        $readResp->assertStatus(200);
        $this->assertNotNull($notif1->fresh()->read_at);

        // 4. Mark all as read
        $readAllResp = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/notifications/read-all');

        $readAllResp->assertStatus(200);
        $this->assertNotNull($notif2->fresh()->read_at);

        // 5. Cek unread count -> harus 0
        $finalCount = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/notifications/count');

        $finalCount->assertStatus(200)
            ->assertJsonPath('data.unread_count', 0);
    }
}
