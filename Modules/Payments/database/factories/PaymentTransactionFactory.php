<?php

namespace Modules\Payments\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Payments\Models\PaymentTransaction;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Payments\Models\PaymentTransaction>
 */
class PaymentTransactionFactory extends Factory
{
    protected $model = PaymentTransaction::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => \App\Models\Order::factory(),
            'provider' => $this->faker->randomElement(['cod', 'paymob']),
            'method' => $this->faker->randomElement(['cod', 'card', 'wallet']),
            'status' => $this->faker->randomElement(['pending', 'completed', 'failed']),
            'amount' => $this->faker->randomFloat(2, 10, 1000),
            'currency' => 'EGP',
            'provider_transaction_id' => $this->faker->optional()->uuid(),
            'provider_order_id' => $this->faker->optional()->uuid(),
            'request_payload' => $this->faker->optional()->array(),
            'response_payload' => $this->faker->optional()->array(),
            'idempotency_key' => $this->faker->uuid(),
        ];
    }

    /**
     * Indicate that the transaction is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    /**
     * Indicate that the transaction is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'provider_transaction_id' => $this->faker->uuid(),
        ]);
    }

    /**
     * Indicate that the transaction is failed.
     */
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'failed',
        ]);
    }

    /**
     * Create a COD transaction.
     */
    public function cod(): static
    {
        return $this->state(fn (array $attributes) => [
            'provider' => 'cod',
            'method' => 'cod',
        ]);
    }

    /**
     * Create a Paymob transaction.
     */
    public function paymob(): static
    {
        return $this->state(fn (array $attributes) => [
            'provider' => 'paymob',
            'method' => $this->faker->randomElement(['card', 'wallet']),
            'provider_transaction_id' => $this->faker->uuid(),
            'provider_order_id' => $this->faker->uuid(),
        ]);
    }

    /**
     * Create a transaction with a specific amount.
     */
    public function amount(float $amount): static
    {
        return $this->state(fn (array $attributes) => [
            'amount' => $amount,
        ]);
    }

    /**
     * Create a transaction for a specific order.
     */
    public function forOrder(int $orderId): static
    {
        return $this->state(fn (array $attributes) => [
            'order_id' => $orderId,
        ]);
    }
}
