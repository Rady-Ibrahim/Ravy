@extends('layouts.admin')

@section('title', __('Edit Order') . ' #' . $order->order_number)

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-brand-navy">{{ __('Edit Order') }} #{{ $order->order_number }}</h1>
            <p class="mt-1 text-sm text-brand-navy/60">{{ __('Update order information') }}</p>
        </div>
        <div class="flex gap-3">
            <a
                href="{{ route('admin.orders.show', $order->order_number) }}"
                class="rounded-lg border border-brand-navy/20 px-4 py-2 text-sm font-semibold text-brand-navy transition hover:bg-brand-navy/5"
            >
                {{ __('Cancel') }}
            </a>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="rounded-xl border border-brand-navy/10 bg-white p-6 shadow-sm">
        <form action="{{ route('admin.orders.update', $order->order_number) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid gap-6 lg:grid-cols-2">
                <!-- Order Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-brand-navy">{{ __('Order Status') }}</label>
                    <select
                        id="status"
                        name="status"
                        class="mt-1 block w-full rounded-lg border border-brand-navy/20 px-3 py-2 text-sm focus:border-brand-navy focus:outline-none focus:ring-1 focus:ring-brand-navy"
                        required
                    >
                        <option value="pending_payment" {{ $order->status === 'pending_payment' ? 'selected' : '' }}>{{ __('Pending Payment') }}</option>
                        <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                        <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>{{ __('Processing') }}</option>
                        <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>{{ __('Shipped') }}</option>
                        <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>{{ __('Delivered') }}</option>
                        <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
                    </select>
                </div>

                <!-- Payment Status -->
                <div>
                    <label for="payment_status" class="block text-sm font-medium text-brand-navy">{{ __('Payment Status') }}</label>
                    <select
                        id="payment_status"
                        name="payment_status"
                        class="mt-1 block w-full rounded-lg border border-brand-navy/20 px-3 py-2 text-sm focus:border-brand-navy focus:outline-none focus:ring-1 focus:ring-brand-navy"
                        required
                    >
                        <option value="pending" {{ $order->payment_status === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                        <option value="paid" {{ $order->payment_status === 'paid' ? 'selected' : '' }}>{{ __('Paid') }}</option>
                        <option value="unpaid" {{ $order->payment_status === 'unpaid' ? 'selected' : '' }}>{{ __('Unpaid') }}</option>
                        <option value="failed" {{ $order->payment_status === 'failed' ? 'selected' : '' }}>{{ __('Failed') }}</option>
                        <option value="refunded" {{ $order->payment_status === 'refunded' ? 'selected' : '' }}>{{ __('Refunded') }}</option>
                    </select>
                </div>

                <!-- Payment Method -->
                <div>
                    <label for="payment_method" class="block text-sm font-medium text-brand-navy">{{ __('Payment Method') }}</label>
                    <select
                        id="payment_method"
                        name="payment_method"
                        class="mt-1 block w-full rounded-lg border border-brand-navy/20 px-3 py-2 text-sm focus:border-brand-navy focus:outline-none focus:ring-1 focus:ring-brand-navy"
                    >
                        <option value="">{{ __('Select Payment Method') }}</option>
                        <option value="cod" {{ $order->payment_method === 'cod' ? 'selected' : '' }}>{{ __('Cash on Delivery') }}</option>
                        <option value="paymob" {{ $order->payment_method === 'paymob' ? 'selected' : '' }}>{{ __('Paymob') }}</option>
                        <option value="stripe" {{ $order->payment_method === 'stripe' ? 'selected' : '' }}>{{ __('Stripe') }}</option>
                    </select>
                </div>

                <!-- Source -->
                <div>
                    <label for="source" class="block text-sm font-medium text-brand-navy">{{ __('Source') }}</label>
                    <input
                        type="text"
                        id="source"
                        name="source"
                        value="{{ $order->source }}"
                        placeholder="{{ __('e.g., Facebook, Website, Mobile App') }}"
                        class="mt-1 block w-full rounded-lg border border-brand-navy/20 px-3 py-2 text-sm focus:border-brand-navy focus:outline-none focus:ring-1 focus:ring-brand-navy"
                    >
                </div>

                <!-- Tracking Number -->
                <div>
                    <label for="tracking_number" class="block text-sm font-medium text-brand-navy">{{ __('Tracking Number') }}</label>
                    <input
                        type="text"
                        id="tracking_number"
                        name="tracking_number"
                        value="{{ $order->tracking_number }}"
                        placeholder="{{ __('Enter tracking number') }}"
                        class="mt-1 block w-full rounded-lg border border-brand-navy/20 px-3 py-2 text-sm focus:border-brand-navy focus:outline-none focus:ring-1 focus:ring-brand-navy"
                    >
                </div>

                <!-- Notes -->
                <div class="lg:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-brand-navy">{{ __('Notes') }}</label>
                    <textarea
                        id="notes"
                        name="notes"
                        rows="4"
                        placeholder="{{ __('Add any additional notes about this order') }}"
                        class="mt-1 block w-full rounded-lg border border-brand-navy/20 px-3 py-2 text-sm focus:border-brand-navy focus:outline-none focus:ring-1 focus:ring-brand-navy"
                    >{{ $order->notes }}</textarea>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="mt-8 border-t border-brand-navy/10 pt-6">
                <h3 class="mb-4 text-lg font-semibold text-brand-navy">{{ __('Order Summary') }}</h3>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="rounded-lg bg-brand-navy/5 p-4">
                        <div class="text-sm text-brand-navy/60">{{ __('Subtotal') }}</div>
                        <div class="text-lg font-semibold text-brand-navy">{{ $order->currency }} {{ number_format($order->subtotal, 2) }}</div>
                    </div>
                    <div class="rounded-lg bg-brand-navy/5 p-4">
                        <div class="text-sm text-brand-navy/60">{{ __('Shipping') }}</div>
                        <div class="text-lg font-semibold text-brand-navy">{{ $order->currency }} {{ number_format($order->shipping_amount, 2) }}</div>
                    </div>
                    <div class="rounded-lg bg-brand-navy/5 p-4">
                        <div class="text-sm text-brand-navy/60">{{ __('Discount') }}</div>
                        <div class="text-lg font-semibold text-brand-navy">{{ $order->currency }} {{ number_format($order->discount_amount, 2) }}</div>
                    </div>
                    <div class="rounded-lg bg-brand-navy/10 p-4">
                        <div class="text-sm text-brand-navy/60">{{ __('Total') }}</div>
                        <div class="text-xl font-bold text-brand-navy">{{ $order->currency }} {{ number_format($order->grand_total, 2) }}</div>
                    </div>
                </div>
            </div>

            <!-- Customer Info Preview -->
            <div class="mt-8 border-t border-brand-navy/10 pt-6">
                <h3 class="mb-4 text-lg font-semibold text-brand-navy">{{ __('Customer Information') }}</h3>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <span class="text-sm text-brand-navy/60">{{ __('Name') }}</span>
                        <p class="text-sm font-semibold text-brand-navy">
                            @if($order->user)
                                {{ $order->user->name }}
                            @elseif($order->shipping_address_snapshot)
                                {{ ($order->shipping_address_snapshot['first_name'] ?? '') . ' ' . ($order->shipping_address_snapshot['last_name'] ?? '') }}
                            @else
                                -
                            @endif
                        </p>
                    </div>
                    <div>
                        <span class="text-sm text-brand-navy/60">{{ __('Email') }}</span>
                        <p class="text-sm text-brand-navy">
                            {{ $order->user?->email ?? ($order->shipping_address_snapshot['email'] ?? '-') }}
                        </p>
                    </div>
                    <div>
                        <span class="text-sm text-brand-navy/60">{{ __('Phone') }}</span>
                        <p class="text-sm text-brand-navy">
                            {{ $order->shipping_address_snapshot['phone'] ?? ($order->user?->phone ?? '-') }}
                        </p>
                    </div>
                    <div>
                        <span class="text-sm text-brand-navy/60">{{ __('User Type') }}</span>
                        <p class="text-sm text-brand-navy">
                            @if($order->user)
                                <span class="inline-flex items-center rounded-md bg-green-100 px-2 py-0.5 text-xs font-medium text-green-700">
                                    {{ __('User') }}
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600">
                                    {{ __('Guest') }}
                                </span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="mt-8 flex gap-3 border-t border-brand-navy/10 pt-6">
                <button
                    type="submit"
                    class="rounded-lg bg-brand-navy px-6 py-2 text-sm font-semibold text-white transition hover:bg-brand-navy/90"
                >
                    {{ __('Update Order') }}
                </button>
                <a
                    href="{{ route('admin.orders.show', $order->order_number) }}"
                    class="rounded-lg border border-brand-navy/20 px-6 py-2 text-sm font-semibold text-brand-navy transition hover:bg-brand-navy/5"
                >
                    {{ __('Cancel') }}
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
