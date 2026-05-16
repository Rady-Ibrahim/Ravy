<?php

namespace Tests\Feature\Notification;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Modules\Auth\Models\User;
use Modules\Category\Models\Category;
use Modules\Notification\Mail\NewOrderAdminMail;
use Modules\Notification\Models\NotificationRecipient;
use Modules\Notification\Support\NotificationEvents;
use Modules\Orders\Models\Governorate;
use Modules\Product\Models\Product;
use Modules\Product\Models\Variant;
use Tests\TestCase;

class OrderPlacedNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_queues_admin_email_to_configured_recipients(): void
    {
        Mail::fake();

        NotificationRecipient::query()->create([
            'channel' => 'email',
            'event' => NotificationEvents::ORDER_PLACED,
            'address' => 'orders@ravy.test',
            'label' => 'Sales',
            'is_active' => true,
            'filters' => ['source' => 'website'],
        ]);

        $governorate = Governorate::query()->firstOrFail();
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $category = Category::query()->create(['name' => 'Shoes', 'slug' => 'shoes']);
        $product = Product::query()->create([
            'name' => 'Red Heel',
            'slug' => 'red-heel',
            'primary_category_id' => $category->id,
            'is_active' => true,
        ]);
        $product->categories()->attach($category->id);

        $variant = Variant::query()->create([
            'product_id' => $product->id,
            'sku' => 'RED-HEEL-39',
            'price' => 345,
            'stock' => 3,
            'is_active' => true,
            'attributes_hash' => sha1('red-39'),
        ]);

        $this->withToken($token)->postJson('/api/v1/cart/items', [
            'product_id' => $product->id,
            'variant_id' => $variant->id,
            'qty' => 1,
        ])->assertCreated();

        $this->withToken($token)->postJson('/api/v1/checkout/place-order', [
            'governorate_id' => $governorate->id,
            'source' => 'website',
            'shipping_address' => [
                'first_name' => 'Ravy',
                'last_name' => 'Customer',
                'email' => 'customer@example.com',
                'phone' => '01000000000',
                'country' => 'Egypt',
                'city' => 'Cairo',
                'address_line_1' => 'Nasr City',
            ],
            'payment_method' => 'cod',
        ])->assertCreated();

        Mail::assertQueued(NewOrderAdminMail::class, function (NewOrderAdminMail $mail) {
            return $mail->hasTo('orders@ravy.test');
        });
    }

    public function test_recipient_with_source_filter_does_not_receive_mismatched_orders(): void
    {
        Mail::fake();

        NotificationRecipient::query()->create([
            'channel' => 'email',
            'event' => NotificationEvents::ORDER_PLACED,
            'address' => 'orders@ravy.test',
            'is_active' => true,
            'filters' => ['source' => 'website'],
        ]);

        $governorate = Governorate::query()->firstOrFail();
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $category = Category::query()->create(['name' => 'Bags', 'slug' => 'bags']);
        $product = Product::query()->create([
            'name' => 'Tote',
            'slug' => 'tote',
            'primary_category_id' => $category->id,
            'is_active' => true,
        ]);
        $product->categories()->attach($category->id);

        $variant = Variant::query()->create([
            'product_id' => $product->id,
            'sku' => 'TOTE-1',
            'price' => 100,
            'stock' => 5,
            'is_active' => true,
            'attributes_hash' => sha1('tote'),
        ]);

        $this->withToken($token)->postJson('/api/v1/cart/items', [
            'product_id' => $product->id,
            'variant_id' => $variant->id,
            'qty' => 1,
        ])->assertCreated();

        $this->withToken($token)->postJson('/api/v1/checkout/place-order', [
            'governorate_id' => $governorate->id,
            'source' => 'pos',
            'shipping_address' => [
                'first_name' => 'POS',
                'last_name' => 'Sale',
                'email' => 'pos@example.com',
                'phone' => '01000000001',
                'country' => 'Egypt',
                'city' => 'Cairo',
                'address_line_1' => 'Store',
            ],
            'payment_method' => 'cod',
        ])->assertCreated();

        Mail::assertNothingQueued();
    }
}
