@extends('layouts.admin')

@section('title', __('Orders'))

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-brand-navy">{{ __('Orders') }}</h1>
            <p class="mt-1 text-sm text-brand-navy/60">{{ __('Manage and track all orders') }}</p>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="rounded-xl border border-brand-navy/10 bg-white p-4 shadow-sm">
        <form method="GET" action="{{ route('admin.orders.index') }}" class="space-y-4">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <!-- Search -->
                <div class="sm:col-span-2">
                    <label for="search" class="block text-sm font-medium text-brand-navy">{{ __('Search') }}</label>
                    <input
                        type="text"
                        name="search"
                        id="search"
                        value="{{ request('search') }}"
                        placeholder="{{ __('Order number or customer email') }}"
                        class="mt-1 block w-full rounded-lg border border-brand-navy/20 px-3 py-2 text-sm focus:border-brand-navy focus:outline-none focus:ring-1 focus:ring-brand-navy"
                    >
                </div>

                <!-- Order Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-brand-navy">{{ __('Order Status') }}</label>
                    <select
                        name="status"
                        id="status"
                        class="mt-1 block w-full rounded-lg border border-brand-navy/20 px-3 py-2 text-sm focus:border-brand-navy focus:outline-none focus:ring-1 focus:ring-brand-navy"
                    >
                        <option value="">{{ __('All') }}</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                        <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>{{ __('Processing') }}</option>
                        <option value="shipped" {{ request('status') === 'shipped' ? 'selected' : '' }}>{{ __('Shipped') }}</option>
                        <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>{{ __('Delivered') }}</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
                    </select>
                </div>

                <!-- Payment Status -->
                <div>
                    <label for="payment_status" class="block text-sm font-medium text-brand-navy">{{ __('Payment Status') }}</label>
                    <select
                        name="payment_status"
                        id="payment_status"
                        class="mt-1 block w-full rounded-lg border border-brand-navy/20 px-3 py-2 text-sm focus:border-brand-navy focus:outline-none focus:ring-1 focus:ring-brand-navy"
                    >
                        <option value="">{{ __('All') }}</option>
                        <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                        <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>{{ __('Paid') }}</option>
                        <option value="failed" {{ request('payment_status') === 'failed' ? 'selected' : '' }}>{{ __('Failed') }}</option>
                        <option value="refunded" {{ request('payment_status') === 'refunded' ? 'selected' : '' }}>{{ __('Refunded') }}</option>
                    </select>
                </div>

                <!-- Date From -->
                <div>
                    <label for="date_from" class="block text-sm font-medium text-brand-navy">{{ __('From Date') }}</label>
                    <input
                        type="date"
                        name="date_from"
                        id="date_from"
                        value="{{ request('date_from') }}"
                        class="mt-1 block w-full rounded-lg border border-brand-navy/20 px-3 py-2 text-sm focus:border-brand-navy focus:outline-none focus:ring-1 focus:ring-brand-navy"
                    >
                </div>

                <!-- Date To -->
                <div>
                    <label for="date_to" class="block text-sm font-medium text-brand-navy">{{ __('To Date') }}</label>
                    <input
                        type="date"
                        name="date_to"
                        id="date_to"
                        value="{{ request('date_to') }}"
                        class="mt-1 block w-full rounded-lg border border-brand-navy/20 px-3 py-2 text-sm focus:border-brand-navy focus:outline-none focus:ring-1 focus:ring-brand-navy"
                    >
                </div>
            </div>

            <div class="flex gap-3">
                <button
                    type="submit"
                    class="rounded-lg bg-brand-navy px-4 py-2 text-sm font-semibold text-white transition hover:bg-brand-navy/90"
                >
                    {{ __('Apply Filters') }}
                </button>
                <a
                    href="{{ route('admin.orders.index') }}"
                    class="rounded-lg border border-brand-navy/20 px-4 py-2 text-sm font-semibold text-brand-navy transition hover:bg-brand-navy/5"
                >
                    {{ __('Reset') }}
                </a>
            </div>
        </form>
    </div>

    <!-- Orders Table -->
    <div class="overflow-hidden rounded-xl border border-brand-navy/10 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-brand-navy/10 bg-brand-navy/5">
                    <tr>
                        <th class="px-2 py-3 font-semibold text-brand-navy text-center">#</th>
                        <th class="px-4 py-3 font-semibold text-brand-navy">{{ __('Order Code:') }}</th>
                        <th class="px-4 py-3 font-semibold text-brand-navy">{{ __('Items') }}</th>
                        <th class="px-4 py-3 font-semibold text-brand-navy">{{ __('Customer Name') }}</th>
                        <th class="px-4 py-3 font-semibold text-brand-navy">{{ __('Customer Phone') }}</th>
                        <th class="px-4 py-3 font-semibold text-brand-navy">{{ __('Total Price') }}</th>
                        <th class="px-4 py-3 font-semibold text-brand-navy">{{ __('Source') }}</th>
                        <th class="px-4 py-3 font-semibold text-brand-navy">{{ __('Payment Status') }}</th>
                        <th class="px-4 py-3 font-semibold text-brand-navy">{{ __('Delivery Status') }}</th>
                        <th class="px-4 py-3 font-semibold text-brand-navy">{{ __('Payment Type') }}</th>
                        <th class="px-4 py-3 font-semibold text-brand-navy">{{ __('User Type') }}</th>
                        <th class="px-4 py-3 font-semibold text-brand-navy">{{ __('Shipping address') }}</th>
                        <th class="px-4 py-3 font-semibold text-brand-navy">{{ __('Tracking Number') }}</th>
                        <th class="px-4 py-3 font-semibold text-brand-navy">{{ __('Order date') }}</th>
                        <th class="px-4 py-3 font-semibold text-brand-navy">{{ __('History') }}</th>
                        <th class="px-4 py-3 font-semibold text-brand-navy text-center">{{ __('Action') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-navy/10">
                    @forelse($orders as $index => $order)
                        <tr class="hover:bg-brand-navy/5">
                            <!-- # -->
                            <td class="px-2 py-3 text-center text-brand-navy/60">
                                {{ $orders->firstItem() + $index }}
                            </td>
                            
                            <!-- Order Code: -->
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.orders.show', $order->order_number) }}" class="font-semibold text-brand-navy hover:text-brand-navy/70">
                                    {{ $order->order_number }}
                                </a>
                            </td>
                            
                            <!-- Items -->
                            <td class="px-4 py-3">
                                <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-brand-navy/10 text-xs font-medium text-brand-navy">
                                    {{ $order->items_count ?? $order->items->count() }}
                                </span>
                            </td>
                            
                            <!-- Customer Name -->
                            <td class="px-4 py-3">
                                @if($order->user)
                                    <div class="font-medium text-brand-navy">{{ $order->user->name }}</div>
                                @elseif($order->shipping_address_snapshot)
                                    <div class="font-medium text-brand-navy">
                                        {{ ($order->shipping_address_snapshot['first_name'] ?? '') . ' ' . ($order->shipping_address_snapshot['last_name'] ?? '') }}
                                    </div>
                                @else
                                    <span class="text-brand-navy/60">-</span>
                                @endif
                            </td>
                            
                            <!-- Customer Phone -->
                            <td class="px-4 py-3 text-brand-navy">
                                {{ $order->shipping_address_snapshot['phone'] ?? ($order->user?->phone ?? '-') }}
                            </td>
                            
                            <!-- Total Price -->
                            <td class="px-4 py-3 font-semibold text-brand-navy">
                                {{ number_format($order->grand_total, 2) }} {{ $order->currency }}
                            </td>
                            
                            <!-- Source -->
                            <td class="px-4 py-3">
                                @if($order->source)
                                    <span class="inline-flex items-center rounded-md bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-700">
                                        {{ $order->source }}
                                    </span>
                                @else
                                    <span class="text-xs text-brand-navy/40">-</span>
                                @endif
                            </td>
                            
                            <!-- Payment Status -->
                            <td class="px-4 py-3">
                                @php
                                    $paymentStatusColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'paid' => 'bg-green-100 text-green-800',
                                        'unpaid' => 'bg-red-100 text-red-800',
                                        'failed' => 'bg-red-100 text-red-800',
                                        'refunded' => 'bg-gray-100 text-gray-800',
                                    ];
                                    $paymentStatusColor = $paymentStatusColors[$order->payment_status] ?? 'bg-gray-100 text-gray-800';
                                    $paymentStatusLabels = [
                                        'pending' => __('Pending'),
                                        'paid' => __('Paid'),
                                        'unpaid' => __('Unpaid'),
                                        'failed' => __('Failed'),
                                        'refunded' => __('Refunded'),
                                    ];
                                @endphp
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {{ $paymentStatusColor }}">
                                    {{ $paymentStatusLabels[$order->payment_status] ?? ucfirst($order->payment_status) }}
                                </span>
                            </td>
                            
                            <!-- Delivery Status -->
                            <td class="px-4 py-3">
                                @php
                                    $statusColors = [
                                        'pending_payment' => 'bg-orange-100 text-orange-800',
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'processing' => 'bg-blue-100 text-blue-800',
                                        'shipped' => 'bg-purple-100 text-purple-800',
                                        'delivered' => 'bg-green-100 text-green-800',
                                        'cancelled' => 'bg-red-100 text-red-800',
                                    ];
                                    $statusColor = $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800';
                                    $statusLabels = [
                                        'pending_payment' => __('Pending Payment'),
                                        'pending' => __('Pending'),
                                        'processing' => __('Processing'),
                                        'shipped' => __('Shipped'),
                                        'delivered' => __('Delivered'),
                                        'cancelled' => __('Cancelled'),
                                    ];
                                @endphp
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {{ $statusColor }}">
                                    {{ $statusLabels[$order->status] ?? ucfirst($order->status) }}
                                </span>
                            </td>
                            
                            <!-- Payment Type -->
                            <td class="px-4 py-3">
                                @php
                                    $paymentMethodLabels = [
                                        'cod' => __('Cash Payment'),
                                        'paymob' => __('Paymob'),
                                        'stripe' => __('Stripe'),
                                    ];
                                @endphp
                                <span class="text-xs text-brand-navy/80">
                                    {{ $paymentMethodLabels[$order->payment_method] ?? ucfirst($order->payment_method) }}
                                </span>
                            </td>
                            
                            <!-- User Type -->
                            <td class="px-4 py-3">
                                @if($order->user)
                                    <span class="inline-flex items-center rounded-md bg-green-100 px-2 py-0.5 text-xs font-medium text-green-700">
                                        {{ __('User') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600">
                                        {{ __('Guest') }}
                                    </span>
                                @endif
                            </td>
                            
                            <!-- Shipping address -->
                            <td class="px-4 py-3">
                                @if($order->shipping_address_snapshot)
                                    <div class="max-w-[150px] text-xs text-brand-navy/80 truncate" title="{{ $order->shipping_address_snapshot['address_line_1'] ?? '' }}">
                                        {{ ($order->shipping_address_snapshot['city'] ?? '') }}
                                        @if(isset($order->shipping_address_snapshot['country']))
                                            , {{ $order->shipping_address_snapshot['country'] }}
                                        @endif
                                    </div>
                                @else
                                    <span class="text-xs text-brand-navy/40">-</span>
                                @endif
                            </td>
                            
                            <!-- Tracking Number -->
                            <td class="px-4 py-3">
                                @if($order->tracking_number)
                                    <span class="text-xs font-mono text-brand-navy">{{ $order->tracking_number }}</span>
                                @else
                                    <span class="text-xs text-brand-navy/40">-</span>
                                @endif
                            </td>
                            
                            <!-- Order date -->
                            <td class="px-4 py-3 text-brand-navy/80">
                                {{ $order->created_at->format('d-m-Y H:i') }}
                            </td>
                            
                            <!-- History -->
                            <td class="px-4 py-3">
                                <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-blue-100 text-xs font-medium text-blue-700">
                                    0
                                </span>
                            </td>
                            
                            <!-- Action -->
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-1">
                                    <a
                                        href="{{ route('admin.orders.show', $order->order_number) }}"
                                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-brand-navy hover:bg-brand-navy/10"
                                        title="{{ __('View') }}"
                                    >
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </a>
                                    <a
                                        href="{{ route('admin.orders.edit', $order->order_number) }}"
                                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-brand-navy hover:bg-brand-navy/10"
                                        title="{{ __('Edit') }}"
                                    >
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.orders.destroy', $order->order_number) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this order? This action cannot be undone.') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-red-600 hover:bg-red-50"
                                            title="{{ __('Delete') }}"
                                        >
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12.562 3.034a48.108 48.108 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="16" class="px-4 py-8 text-center text-brand-navy/60">
                                {{ __('No orders found') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($orders->hasPages())
            <div class="border-t border-brand-navy/10 px-4 py-3">
                {{ $orders->appends(request()->except('page'))->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
