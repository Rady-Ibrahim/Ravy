@extends('layouts.admin')

@section('title', __('Order Details') . ' #' . $order->order_number)

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-brand-navy">{{ __('Order Details') }}</h1>
            <p class="mt-1 text-sm text-brand-navy/60">#{{ $order->order_number }}</p>
        </div>
        <div class="flex gap-3">
            <form method="POST" action="{{ route('admin.orders.update-status', $order->order_number) }}" class="inline-flex">
                @csrf
                <div class="flex gap-2">
                    <select
                        name="status"
                        class="rounded-lg border border-brand-navy/20 px-3 py-2 text-sm focus:border-brand-navy focus:outline-none focus:ring-1 focus:ring-brand-navy"
                    >
                        <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                        <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>{{ __('Processing') }}</option>
                        <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>{{ __('Shipped') }}</option>
                        <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>{{ __('Delivered') }}</option>
                        <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
                    </select>
                    <button
                        type="submit"
                        class="rounded-lg bg-brand-navy px-4 py-2 text-sm font-semibold text-white transition hover:bg-brand-navy/90"
                    >
                        {{ __('Update Status') }}
                    </button>
                </div>
            </form>
            <a
                href="{{ route('admin.orders.index') }}"
                class="rounded-lg border border-brand-navy/20 px-4 py-2 text-sm font-semibold text-brand-navy transition hover:bg-brand-navy/5"
            >
                {{ __('Back to Orders') }}
            </a>
        </div>
    </div>

    <!-- Order Summary & Customer Info -->
    <div class="grid gap-6 lg:grid-cols-2">
        <!-- Order Summary -->
        <div class="rounded-xl border border-brand-navy/10 bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-lg font-semibold text-brand-navy">{{ __('Order Summary') }}</h2>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm text-brand-navy/60">{{ __('Order Number') }}</span>
                    <span class="text-sm font-semibold text-brand-navy">#{{ $order->order_number }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-brand-navy/60">{{ __('Date') }}</span>
                    <span class="text-sm text-brand-navy">{{ $order->created_at->format('M d, Y - g:i A') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-brand-navy/60">{{ __('Status') }}</span>
                    @php
                        $statusColors = [
                            'pending' => 'bg-yellow-100 text-yellow-800',
                            'processing' => 'bg-blue-100 text-blue-800',
                            'shipped' => 'bg-purple-100 text-purple-800',
                            'delivered' => 'bg-green-100 text-green-800',
                            'cancelled' => 'bg-red-100 text-red-800',
                        ];
                        $statusColor = $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800';
                    @endphp
                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {{ $statusColor }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-brand-navy/60">{{ __('Payment Status') }}</span>
                    @php
                        $paymentStatusColors = [
                            'pending' => 'bg-yellow-100 text-yellow-800',
                            'paid' => 'bg-green-100 text-green-800',
                            'failed' => 'bg-red-100 text-red-800',
                            'refunded' => 'bg-gray-100 text-gray-800',
                        ];
                        $paymentStatusColor = $paymentStatusColors[$order->payment_status] ?? 'bg-gray-100 text-gray-800';
                    @endphp
                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {{ $paymentStatusColor }}">
                        {{ ucfirst($order->payment_status) }}
                    </span>
                </div>
                @if($order->packaging_option)
                <div class="flex justify-between">
                    <span class="text-sm text-brand-navy/60">{{ __('Packaging') }}</span>
                    <span class="text-sm text-brand-navy">{{ ucfirst($order->packaging_option) }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Customer Info -->
        <div class="rounded-xl border border-brand-navy/10 bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-lg font-semibold text-brand-navy">{{ __('Customer Information') }}</h2>
            @if($order->user)
                <div class="space-y-3">
                    <div>
                        <span class="text-sm text-brand-navy/60">{{ __('Name') }}</span>
                        <p class="text-sm font-semibold text-brand-navy">{{ $order->user->name }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-brand-navy/60">{{ __('Email') }}</span>
                        <p class="text-sm text-brand-navy">{{ $order->user->email }}</p>
                    </div>
                    @if($order->user->phone)
                    <div>
                        <span class="text-sm text-brand-navy/60">{{ __('Phone') }}</span>
                        <p class="text-sm text-brand-navy">{{ $order->user->phone }}</p>
                    </div>
                    @endif
                    <div class="pt-2">
                        <a
                            href="{{ route('admin.users.edit', $order->user->id) }}"
                            class="inline-flex items-center gap-1 text-sm font-semibold text-brand-navy hover:text-brand-navy/70"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                            {{ __('View Profile') }}
                        </a>
                    </div>
                </div>
            @elseif($order->shipping_address_snapshot)
                <div class="space-y-3">
                    <div>
                        <span class="text-sm text-brand-navy/60">{{ __('Name') }}</span>
                        <p class="text-sm font-semibold text-brand-navy">
                            {{ ($order->shipping_address_snapshot['first_name'] ?? '') . ' ' . ($order->shipping_address_snapshot['last_name'] ?? '') }}
                        </p>
                    </div>
                    <div>
                        <span class="text-sm text-brand-navy/60">{{ __('Email') }}</span>
                        <p class="text-sm text-brand-navy">{{ $order->shipping_address_snapshot['email'] ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-brand-navy/60">{{ __('Phone') }}</span>
                        <p class="text-sm text-brand-navy">{{ $order->shipping_address_snapshot['phone'] ?? '-' }}</p>
                    </div>
                </div>
            @else
                <p class="text-sm text-brand-navy/60">{{ __('Guest order') }}</p>
            @endif
        </div>
    </div>

    <!-- Shipping Address -->
    @if($order->shipping_address_snapshot)
    <div class="rounded-xl border border-brand-navy/10 bg-white p-6 shadow-sm">
        <h2 class="mb-4 text-lg font-semibold text-brand-navy">{{ __('Shipping Address') }}</h2>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div>
                <span class="text-sm text-brand-navy/60">{{ __('Full Name') }}</span>
                <p class="text-sm text-brand-navy">
                    {{ ($order->shipping_address_snapshot['first_name'] ?? '') . ' ' . ($order->shipping_address_snapshot['last_name'] ?? '') }}
                </p>
            </div>
            <div>
                <span class="text-sm text-brand-navy/60">{{ __('Phone') }}</span>
                <p class="text-sm text-brand-navy">{{ $order->shipping_address_snapshot['phone'] ?? '-' }}</p>
            </div>
            <div class="sm:col-span-2 lg:col-span-1">
                <span class="text-sm text-brand-navy/60">{{ __('Country') }}</span>
                <p class="text-sm text-brand-navy">{{ $order->shipping_address_snapshot['country'] ?? '-' }}</p>
            </div>
            <div class="sm:col-span-2 lg:col-span-1">
                <span class="text-sm text-brand-navy/60">{{ __('City') }}</span>
                <p class="text-sm text-brand-navy">{{ $order->shipping_address_snapshot['city'] ?? '-' }}</p>
            </div>
            <div class="sm:col-span-2">
                <span class="text-sm text-brand-navy/60">{{ __('Address') }}</span>
                <p class="text-sm text-brand-navy">
                    {{ $order->shipping_address_snapshot['address_line_1'] ?? '-' }}
                    @if(isset($order->shipping_address_snapshot['address_line_2']) && $order->shipping_address_snapshot['address_line_2'])
                        <br>{{ $order->shipping_address_snapshot['address_line_2'] }}
                    @endif
                </p>
            </div>
            @if(isset($order->shipping_address_snapshot['postal_code']))
            <div>
                <span class="text-sm text-brand-navy/60">{{ __('Postal Code') }}</span>
                <p class="text-sm text-brand-navy">{{ $order->shipping_address_snapshot['postal_code'] }}</p>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Order Items -->
    <div class="rounded-xl border border-brand-navy/10 bg-white shadow-sm">
        <div class="border-b border-brand-navy/10 p-6">
            <h2 class="text-lg font-semibold text-brand-navy">{{ __('Order Items') }}</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-brand-navy/10 bg-brand-navy/5">
                    <tr>
                        <th class="px-6 py-3 font-semibold text-brand-navy">{{ __('Product') }}</th>
                        <th class="px-6 py-3 font-semibold text-brand-navy">{{ __('Variant') }}</th>
                        <th class="px-6 py-3 font-semibold text-brand-navy text-right">{{ __('Price') }}</th>
                        <th class="px-6 py-3 font-semibold text-brand-navy text-right">{{ __('Quantity') }}</th>
                        <th class="px-6 py-3 font-semibold text-brand-navy text-right">{{ __('Total') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-navy/10">
                    @foreach($order->items as $item)
                        <tr>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if($item->product && $item->product->main_image)
                                        <img src="{{ asset($item->product->main_image) }}" alt="" class="h-12 w-12 rounded-lg object-cover">
                                    @else
                                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-brand-navy/10 text-brand-navy/40">
                                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 11-.75 0 .375.375 0 0 1 .75 0Z" />
                                            </svg>
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-medium text-brand-navy">{{ $item->name_snapshot }}</p>
                                        <p class="text-xs text-brand-navy/60">{{ $item->sku_snapshot ?? '-' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-brand-navy/80">
                                @if($item->variant)
                                    @if($item->variant->attributeValues && $item->variant->attributeValues->count() > 0)
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($item->variant->attributeValues as $attributeValue)
                                                @if($attributeValue->attribute)
                                                    <span class="inline-flex items-center rounded-md bg-brand-navy/10 px-2 py-0.5 text-xs font-medium text-brand-navy">
                                                        {{ $attributeValue->attribute->name }}: {{ $attributeValue->value }}
                                                    </span>
                                                @endif
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-xs text-brand-navy/60">{{ $item->variant->sku ?? '-' }}</span>
                                    @endif
                                @else
                                    <span class="text-xs text-brand-navy/60">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right text-brand-navy">
                                {{ $order->currency }} {{ number_format($item->unit_price, 2) }}
                            </td>
                            <td class="px-6 py-4 text-right text-brand-navy">
                                {{ $item->qty }}
                            </td>
                            <td class="px-6 py-4 text-right font-semibold text-brand-navy">
                                {{ $order->currency }} {{ number_format($item->line_total, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="border-t border-brand-navy/10 bg-brand-navy/5">
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-right text-sm font-semibold text-brand-navy">
                            {{ __('Subtotal') }}
                        </td>
                        <td class="px-6 py-4 text-right text-sm font-semibold text-brand-navy">
                            {{ $order->currency }} {{ number_format($order->subtotal, 2) }}
                        </td>
                    </tr>
                    @if($order->discount_amount > 0)
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-right text-sm text-brand-navy">
                            {{ __('Discount') }}
                        </td>
                        <td class="px-6 py-4 text-right text-sm text-green-600">
                            -{{ $order->currency }} {{ number_format($order->discount_amount, 2) }}
                        </td>
                    </tr>
                    @endif
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-right text-sm text-brand-navy">
                            {{ __('Shipping') }}
                        </td>
                        <td class="px-6 py-4 text-right text-sm text-brand-navy">
                            {{ $order->currency }} {{ number_format($order->shipping_amount, 2) }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-right text-base font-bold text-brand-navy">
                            {{ __('Total') }}
                        </td>
                        <td class="px-6 py-4 text-right text-base font-bold text-brand-navy">
                            {{ $order->currency }} {{ number_format($order->grand_total, 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Payment Details -->
    @if($order->paymentTransactions && $order->paymentTransactions->count() > 0)
    <div class="rounded-xl border border-brand-navy/10 bg-white p-6 shadow-sm">
        <h2 class="mb-4 text-lg font-semibold text-brand-navy">{{ __('Payment Transactions') }}</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-brand-navy/10 bg-brand-navy/5">
                    <tr>
                        <th class="px-4 py-3 font-semibold text-brand-navy">{{ __('Provider') }}</th>
                        <th class="px-4 py-3 font-semibold text-brand-navy">{{ __('Method') }}</th>
                        <th class="px-4 py-3 font-semibold text-brand-navy">{{ __('Status') }}</th>
                        <th class="px-4 py-3 font-semibold text-brand-navy">{{ __('Amount') }}</th>
                        <th class="px-4 py-3 font-semibold text-brand-navy">{{ __('Transaction ID') }}</th>
                        <th class="px-4 py-3 font-semibold text-brand-navy">{{ __('Date') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-navy/10">
                    @foreach($order->paymentTransactions as $transaction)
                        <tr>
                            <td class="px-4 py-3 text-brand-navy">{{ $transaction->getHumanProviderAttribute() }}</td>
                            <td class="px-4 py-3 text-brand-navy/80">{{ ucfirst($transaction->method) }}</td>
                            <td class="px-4 py-3">
                                @php
                                    $txStatusColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'completed' => 'bg-green-100 text-green-800',
                                        'failed' => 'bg-red-100 text-red-800',
                                        'refunded' => 'bg-gray-100 text-gray-800',
                                    ];
                                    $txStatusColor = $txStatusColors[$transaction->status] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {{ $txStatusColor }}">
                                    {{ $transaction->getHumanStatusAttribute() }}
                                </span>
                            </td>
                            <td class="px-4 py-3 font-semibold text-brand-navy">
                                {{ $transaction->currency }} {{ number_format($transaction->amount, 2) }}
                            </td>
                            <td class="px-4 py-3 text-xs text-brand-navy/60 font-mono">
                                {{ $transaction->provider_transaction_id ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-brand-navy/80">
                                {{ $transaction->created_at->format('M d, Y - g:i A') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Timeline / Order History -->
    <div class="rounded-xl border border-brand-navy/10 bg-white p-6 shadow-sm">
        <h2 class="mb-4 text-lg font-semibold text-brand-navy">{{ __('Order History') }}</h2>
        <div class="space-y-4">
            <div class="flex gap-4">
                <div class="flex flex-col items-center">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-green-100">
                        <svg class="h-4 w-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                    </div>
                    <div class="h-full w-0.5 bg-brand-navy/10"></div>
                </div>
                <div class="pb-6">
                    <p class="text-sm font-semibold text-brand-navy">{{ __('Order Created') }}</p>
                    <p class="text-xs text-brand-navy/60">{{ $order->created_at->format('M d, Y - g:i A') }}</p>
                </div>
            </div>

            @if($order->payment_status === 'paid')
            <div class="flex gap-4">
                <div class="flex flex-col items-center">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-green-100">
                        <svg class="h-4 w-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                    </div>
                    @if($order->status !== 'delivered')
                    <div class="h-full w-0.5 bg-brand-navy/10"></div>
                    @endif
                </div>
                <div class="@if($order->status !== 'delivered') pb-6 @endif">
                    <p class="text-sm font-semibold text-brand-navy">{{ __('Payment Completed') }}</p>
                    <p class="text-xs text-brand-navy/60">{{ __('Order has been paid successfully') }}</p>
                </div>
            </div>
            @endif

            @if($order->status === 'processing' || $order->status === 'shipped' || $order->status === 'delivered')
            <div class="flex gap-4">
                <div class="flex flex-col items-center">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-100">
                        <svg class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                        </svg>
                    </div>
                    @if($order->status !== 'delivered')
                    <div class="h-full w-0.5 bg-brand-navy/10"></div>
                    @endif
                </div>
                <div class="@if($order->status !== 'delivered') pb-6 @endif">
                    <p class="text-sm font-semibold text-brand-navy">{{ __('Order Processing') }}</p>
                    <p class="text-xs text-brand-navy/60">{{ __('Order is being prepared') }}</p>
                </div>
            </div>
            @endif

            @if($order->status === 'shipped' || $order->status === 'delivered')
            <div class="flex gap-4">
                <div class="flex flex-col items-center">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-purple-100">
                        <svg class="h-4 w-4 text-purple-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                        </svg>
                    </div>
                    @if($order->status !== 'delivered')
                    <div class="h-full w-0.5 bg-brand-navy/10"></div>
                    @endif
                </div>
                <div class="@if($order->status !== 'delivered') pb-6 @endif">
                    <p class="text-sm font-semibold text-brand-navy">{{ __('Order Shipped') }}</p>
                    <p class="text-xs text-brand-navy/60">{{ __('Order has been shipped') }}</p>
                </div>
            </div>
            @endif

            @if($order->status === 'delivered')
            <div class="flex gap-4">
                <div class="flex flex-col items-center">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-green-100">
                        <svg class="h-4 w-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-semibold text-brand-navy">{{ __('Order Delivered') }}</p>
                    <p class="text-xs text-brand-navy/60">{{ __('Order has been delivered successfully') }}</p>
                </div>
            </div>
            @endif

            @if($order->status === 'cancelled')
            <div class="flex gap-4">
                <div class="flex flex-col items-center">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-red-100">
                        <svg class="h-4 w-4 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-semibold text-brand-navy">{{ __('Order Cancelled') }}</p>
                    <p class="text-xs text-brand-navy/60">{{ __('Order has been cancelled') }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Notes -->
    @if($order->notes)
    <div class="rounded-xl border border-brand-navy/10 bg-white p-6 shadow-sm">
        <h2 class="mb-4 text-lg font-semibold text-brand-navy">{{ __('Order Notes') }}</h2>
        <p class="text-sm text-brand-navy/80">{{ $order->notes }}</p>
    </div>
    @endif

    <!-- Print Invoice Button -->
    <div class="flex justify-end">
        <button
            onclick="window.print()"
            class="inline-flex items-center gap-2 rounded-lg border border-brand-navy/20 px-4 py-2 text-sm font-semibold text-brand-navy transition hover:bg-brand-navy/5"
        >
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
            </svg>
            {{ __('Print Invoice') }}
        </button>
    </div>
</div>
@endsection
