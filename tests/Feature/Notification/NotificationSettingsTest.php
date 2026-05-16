<?php

namespace Tests\Feature\Notification;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Database\Seeders\AuthDatabaseSeeder;
use Modules\Auth\Database\Seeders\RolePermissionSeeder;
use Modules\Auth\Models\User;
use Modules\Notification\Models\NotificationRecipient;
use Modules\Notification\Support\NotificationEvents;
use Tests\TestCase;

class NotificationSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
        $this->seed(AuthDatabaseSeeder::class);
    }

    public function test_admin_can_save_order_notification_email_from_settings(): void
    {
        $admin = User::query()->where('email', env('AUTH_SEED_ADMIN_EMAIL', 'admin@ravy.test'))->firstOrFail();

        $response = $this->actingAs($admin)->put(route('admin.settings.notifications.update'), [
            'order_notification_email' => 'sales@ravy.test',
        ]);

        $response->assertRedirect(route('admin.settings.notifications'));

        $this->assertDatabaseHas('notification_recipients', [
            'channel' => 'email',
            'event' => NotificationEvents::ORDER_PLACED,
            'address' => 'sales@ravy.test',
            'is_active' => true,
        ]);
    }

    public function test_settings_page_shows_saved_email(): void
    {
        $admin = User::query()->where('email', env('AUTH_SEED_ADMIN_EMAIL', 'admin@ravy.test'))->firstOrFail();

        NotificationRecipient::query()->create([
            'channel' => 'email',
            'event' => NotificationEvents::ORDER_PLACED,
            'address' => 'orders@ravy.test',
            'is_active' => true,
            'filters' => ['source' => 'website'],
        ]);

        $this->actingAs($admin)
            ->get(route('admin.settings.notifications'))
            ->assertOk()
            ->assertSee('orders@ravy.test');
    }
}
