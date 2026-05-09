<?php

namespace Modules\Orders\Services\Api;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Validation\ValidationException;
use Modules\Auth\Models\User;
use Modules\Orders\Models\Cart;
use Modules\Orders\Models\CartItem;
use Modules\Orders\Models\Governorate;
use Modules\Orders\Models\Order;
use Modules\Orders\Models\PromoCode;
use Modules\Product\Models\Product;
use Modules\Product\Models\Variant;
use Modules\Payments\Services\PaymentService;

class CartService
{
    public function getOrCreateActiveCart(User $user): Cart
    {
        return Cart::query()->firstOrCreate(
            ['user_id' => $user->id, 'status' => 'active']
        );
    }

    public function getOrCreateGuestCart(string $guestId): Cart
    {
        return Cart::query()->firstOrCreate(
            ['guest_id' => $guestId, 'status' => 'active']
        );
    }

    public function getCart(?User $user = null, ?string $guestId = null): Cart
    {
        if ($user) {
            return $this->getOrCreateActiveCart($user);
        }

        if ($guestId) {
            return $this->getOrCreateGuestCart($guestId);
        }

        throw new \InvalidArgumentException('Either user or guest_id must be provided');
    }

    public function getActiveCartWithRelations(?User $user = null, ?string $guestId = null): Cart
    {
        return $this->getCart($user, $guestId)->load([
            'items.product',
            'items.variant',
        ]);
    }

    public function addItem(?User $user = null, array $payload = [], ?string $guestId = null): Cart
    {
        $cart = $this->getCart($user, $guestId);

        $product = Product::query()
            ->whereKey($payload['product_id'])
            ->where('is_active', true)
            ->first();

        if (! $product) {
            throw ValidationException::withMessages([
                'product_id' => ['Selected product is invalid or inactive.'],
            ]);
        }

        $variant = $this->resolveVariant($product, $payload['variant_id'] ?? null);
        $qty = (int) $payload['qty'];
        $unitPrice = (float) ($variant?->price ?? $product->min_price ?? 0);

        if ($unitPrice <= 0) {
            throw ValidationException::withMessages([
                'product_id' => ['Product price is not configured.'],
            ]);
        }

        if ($variant && $variant->stock < $qty) {
            throw ValidationException::withMessages([
                'qty' => ['Requested quantity exceeds available stock.'],
            ]);
        }

        $existing = CartItem::query()
            ->where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->where('variant_id', $variant?->id)
            ->first();

        if ($existing) {
            $newQty = $existing->qty + $qty;

            if ($variant && $variant->stock < $newQty) {
                throw ValidationException::withMessages([
                    'qty' => ['Requested quantity exceeds available stock.'],
                ]);
            }

            $existing->update([
                'qty' => $newQty,
                'unit_price' => $unitPrice,
                'line_total' => $newQty * $unitPrice,
            ]);
        } else {
            CartItem::query()->create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'variant_id' => $variant?->id,
                'qty' => $qty,
                'unit_price' => $unitPrice,
                'line_total' => $qty * $unitPrice,
            ]);
        }

        return $this->getActiveCartWithRelations($user, $guestId);
    }

    public function updateItemQty(?User $user = null, int $itemId, int $qty, ?string $guestId = null): Cart
    {
        $cart = $this->getCart($user, $guestId);
        $item = $cart->items()->whereKey($itemId)->firstOrFail();

        if ($item->variant && $item->variant->stock < $qty) {
            throw ValidationException::withMessages([
                'qty' => ['Requested quantity exceeds available stock.'],
            ]);
        }

        $item->update([
            'qty' => $qty,
            'line_total' => $qty * (float) $item->unit_price,
        ]);

        return $this->getActiveCartWithRelations($user, $guestId);
    }

    public function removeItem(?User $user = null, int $itemId, ?string $guestId = null): Cart
    {
        $cart = $this->getCart($user, $guestId);
        $item = $cart->items()->whereKey($itemId)->firstOrFail();
        $item->delete();

        return $this->getActiveCartWithRelations($user, $guestId);
    }

    public function clear(?User $user = null, ?string $guestId = null): Cart
    {
        $cart = $this->getCart($user, $guestId);
        $cart->items()->delete();

        return $this->getActiveCartWithRelations($user, $guestId);
    }

    public function totals(Cart $cart, ?int $governorateId = null, ?string $promoCode = null): array
    {
        $subtotal = (float) $cart->items->sum(fn ($item) => (float) $item->line_total);
        $shipping = 0.0;
        $discount = 0.0;

        // Calculate promo code discount
        if ($promoCode) {
            $promoCodeModel = PromoCode::query()
                ->where('code', strtoupper($promoCode))
                ->valid()
                ->notUsedUp()
                ->first();

            if ($promoCodeModel && $promoCodeModel->isValid()) {
                $discount = $promoCodeModel->calculateDiscount($subtotal);
            }
        }

        // Calculate shipping based on governorate
        if ($governorateId && $subtotal > 0) {
            $governorate = Governorate::find($governorateId);
            if ($governorate && $governorate->is_active) {
                $shipping = (float) $governorate->shipping_cost;
                
                // Check for free shipping threshold
                $freeShippingThreshold = config('orders.free_shipping_threshold', 0);
                if ($freeShippingThreshold > 0 && $subtotal >= $freeShippingThreshold) {
                    $shipping = 0.0;
                }
            }
        }

        return [
            'subtotal' => $subtotal,
            'shipping_amount' => $shipping,
            'discount_amount' => $discount,
            'grand_total' => max($subtotal + $shipping - $discount, 0),
        ];
    }

    public function checkout(?User $user = null, array $payload = [], ?string $guestId = null): Order
    {
        $cart = $this->getActiveCartWithRelations($user, $guestId);

        if ($cart->items->isEmpty()) {
            throw ValidationException::withMessages([
                'cart' => ['Cart is empty.'],
            ]);
        }

        return DB::transaction(function () use ($user, $cart, $payload, $guestId) {
            $cart->load(['items.product', 'items.variant']);
            $governorateId = $payload['governorate_id'] ?? null;
            $promoCode = $payload['promo_code'] ?? null;
            $totals = $this->totals($cart, $governorateId, $promoCode);

            // Handle promo code
            $promoCodeModel = null;
            if ($promoCode && $totals['discount_amount'] > 0) {
                $promoCodeModel = PromoCode::query()
                    ->where('code', strtoupper($promoCode))
                    ->valid()
                    ->notUsedUp()
                    ->first();

                if ($promoCodeModel && $promoCodeModel->isValid()) {
                    $promoCodeModel->incrementUsage();
                }
            }

            $order = Order::query()->create([
                'order_number' => $this->generateOrderNumber(),
                'user_id' => $user?->id,
                'governorate_id' => $governorateId,
                'status' => 'pending_payment',
                'payment_status' => 'unpaid',
                'subtotal' => $totals['subtotal'],
                'shipping_amount' => $totals['shipping_amount'],
                'shipping_calculated_cost' => $totals['shipping_amount'],
                'discount_amount' => $totals['discount_amount'],
                'grand_total' => $totals['grand_total'],
                'currency' => 'EGP',
                'payment_method' => $payload['payment_method'] ?? null,
                'source' => $payload['source'] ?? null,
                'shipping_address_snapshot' => $payload['shipping_address'],
                'packaging_option' => $payload['packaging_option'] ?? null,
                'notes' => $payload['notes'] ?? null,
                'promo_code_id' => $promoCodeModel?->id,
                'promo_code' => $promoCodeModel?->code,
            ]);

            foreach ($cart->items as $item) {
                $variant = $item->variant;

                if ($variant && $variant->stock < $item->qty) {
                    throw ValidationException::withMessages([
                        'cart' => ["Insufficient stock for {$item->product?->name}."],
                    ]);
                }

                $order->items()->create([
                    'product_id' => $item->product_id,
                    'variant_id' => $item->variant_id,
                    'name_snapshot' => $item->product?->name ?? 'Product',
                    'sku_snapshot' => $variant?->sku,
                    'qty' => $item->qty,
                    'unit_price' => $item->unit_price,
                    'line_total' => $item->line_total,
                ]);
            }

            $cart->update(['status' => 'converted']);
            if ($user) {
                $this->getOrCreateActiveCart($user);
            } elseif ($guestId) {
                $this->getOrCreateGuestCart($guestId);
            }

            // Initiate payment if payment method is provided
            if (isset($payload['payment_method']) && $payload['payment_method'] !== 'cod') {
                $paymentService = new PaymentService();
                $paymentResponse = $paymentService->initiatePayment(
                    $order,
                    $payload['payment_method'],
                    $payload['payment_context'] ?? []
                );

                if (!$paymentResponse->success) {
                    throw ValidationException::withMessages([
                        'payment' => ['Payment initiation failed: ' . $paymentResponse->message],
                    ]);
                }
            }

            return $order->load('items');
        });
    }

    private function resolveVariant(Product $product, ?int $variantId): ?Variant
    {
        $hasVariants = $product->variants()->exists();

        if (! $hasVariants) {
            return null;
        }

        if (! $variantId) {
            throw ValidationException::withMessages([
                'variant_id' => ['Variant is required for this product.'],
            ]);
        }

        $variant = Variant::query()
            ->whereKey($variantId)
            ->where('product_id', $product->id)
            ->where('is_active', true)
            ->first();

        if (! $variant) {
            throw ValidationException::withMessages([
                'variant_id' => ['Selected variant is invalid or inactive.'],
            ]);
        }

        return $variant;
    }

    private function generateOrderNumber(): string
    {
        do {
            $candidate = 'RAVY-'.now()->format('Ymd').'-'.str_pad((string) random_int(1, 99999), 5, '0', STR_PAD_LEFT);
        } while (Order::query()->where('order_number', $candidate)->exists());

        return $candidate;
    }
}
