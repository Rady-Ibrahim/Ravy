<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\Category\Models\Category;
use Modules\Product\Models\Product;
use Modules\Product\Models\Variant;
use Tests\TestCase;

class OrderApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_manage_cart_and_checkout_order(): void
    {
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

        $addResponse = $this->withToken($token)->postJson('/api/v1/cart/items', [
            'product_id' => $product->id,
            'variant_id' => $variant->id,
            'qty' => 1,
        ]);

        $addResponse->assertCreated()
            ->assertJsonPath('data.items.0.qty', 1)
            ->assertJsonPath('data.totals.subtotal', 345);

        $itemId = $addResponse->json('data.items.0.id');
        $updateResponse = $this->withToken($token)->patchJson("/api/v1/cart/items/{$itemId}", [
            'qty' => 2,
        ]);

        $updateResponse->assertOk()
            ->assertJsonPath('data.items.0.qty', 2)
            ->assertJsonPath('data.totals.grand_total', 690);

        $checkoutResponse = $this->withToken($token)->postJson('/api/v1/orders/checkout', [
            'shipping_address' => [
                'first_name' => 'Ravy',
                'last_name' => 'Customer',
                'email' => 'customer@example.com',
                'phone' => '01000000000',
                'country' => 'Egypt',
                'city' => 'Cairo',
                'address_line_1' => 'Nasr City',
                'postal_code' => '11765',
            ],
            'packaging_option' => 'eco',
            'payment_method' => 'online_placeholder',
        ]);

        $checkoutResponse->assertCreated()
            ->assertJsonPath('data.status', 'pending_payment')
            ->assertJsonPath('data.payment_status', 'unpaid')
            ->assertJsonPath('data.grand_total', 690);

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'status' => 'pending_payment',
            'payment_status' => 'unpaid',
        ]);
    }

    public function test_checkout_fails_for_empty_cart(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withToken($token)->postJson('/api/v1/orders/checkout', [
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
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['cart']);
    }
}
