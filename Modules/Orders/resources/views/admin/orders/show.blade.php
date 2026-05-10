@extends('layouts.admin')

@section('title', __('Order Details') . ' #' . $order->order_number)

@section('content')
<div class="mx-auto max-w-7xl">
    <!-- Page Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">{{ __('Order Details') }}</h1>
            <p class="mt-1 text-sm text-slate-500">#{{ $order->order_number }}</p>
        </div>
        <div class="flex gap-3">
            <form method="POST" action="{{ route('admin.orders.update-status', $order->order_number) }}" class="inline-flex">
                @csrf
                <div class="flex gap-2">
                    <select
                        name="status"
                        class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:ring focus:ring-slate-200 transition-all"
                    >
                        <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                        <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>{{ __('Processing') }}</option>
                        <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>{{ __('Shipped') }}</option>
                        <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>{{ __('Delivered') }}</option>
                        <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
                    </select>
                    <button
                        type="submit"
                        class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800"
                    >
                        {{ __('Update Status') }}
                    </button>
                </div>
            </form>
            <a
                href="{{ route('admin.orders.index') }}"
                class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
            >
                {{ __('Back to Orders') }}
            </a>
        </div>
    </div>

    <!-- Order Summary & Customer Info -->
    <div class="grid gap-6 lg:grid-cols-2">
        <!-- Order Summary -->
        <div class="admin-card border-0 p-6 shadow-md shadow-slate-200/50">
            <h2 class="mb-4 text-lg font-semibold text-slate-900">{{ __('Order Summary') }}</h2>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm text-slate-500">{{ __('Order Number') }}</span>
                    <span class="text-sm font-semibold text-slate-900">#{{ $order->order_number }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-slate-500">{{ __('Date') }}</span>
                    <span class="text-sm text-slate-600">{{ $order->created_at->format('M d, Y - g:i A') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-slate-500">{{ __('Status') }}</span>
                    @php
                        $statusColors = [
                            'pending' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
                            'processing' => 'bg-blue-50 text-blue-700 ring-blue-600/20',
                            'shipped' => 'bg-purple-50 text-purple-700 ring-purple-600/20',
                            'delivered' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
                            'cancelled' => 'bg-red-50 text-red-700 ring-red-600/20',
                        ];
                        $statusColor = $statusColors[$order->status] ?? 'bg-slate-100 text-slate-600 ring-slate-500/10';
                    @endphp
                    <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium ring-1 ring-inset {{ $statusColor }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-slate-500">{{ __('Payment Status') }}</span>
                    @php
                        $paymentStatusColors = [
                            'pending' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
                            'paid' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
                            'failed' => 'bg-red-50 text-red-700 ring-red-600/20',
                            'refunded' => 'bg-slate-100 text-slate-600 ring-slate-500/10',
                        ];
                        $paymentStatusColor = $paymentStatusColors[$order->payment_status] ?? 'bg-slate-100 text-slate-600 ring-slate-500/10';
                    @endphp
                    <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium ring-1 ring-inset {{ $paymentStatusColor }}">
                        {{ ucfirst($order->payment_status) }}
                    </span>
                </div>
                @if($order->packaging_option)
                <div class="flex justify-between">
                    <span class="text-sm text-slate-500">{{ __('Packaging') }}</span>
                    <span class="text-sm text-slate-600">{{ ucfirst($order->packaging_option) }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Customer Info -->
        <div class="admin-card border-0 p-6 shadow-md shadow-slate-200/50">
            <h2 class="mb-4 text-lg font-semibold text-slate-900">{{ __('Customer Information') }}</h2>
            @if($order->user)
                <div class="space-y-3">
                    <div>
                        <span class="text-sm text-slate-500">{{ __('Name') }}</span>
                        <p class="text-sm font-semibold text-slate-900">{{ $order->user->name }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-slate-500">{{ __('Email') }}</span>
                        <p class="text-sm text-slate-600">{{ $order->user->email }}</p>
                    </div>
                    @if($order->user->phone)
                    <div>
                        <span class="text-sm text-slate-500">{{ __('Phone') }}</span>
                        <p class="text-sm text-slate-600">{{ $order->user->phone }}</p>
                    </div>
                    @endif
                    <div class="pt-2">
                        <a
                            href="{{ route('admin.users.edit', $order->user->id) }}"
                            class="inline-flex items-center gap-1 text-sm font-semibold text-indigo-700 hover:text-indigo-600"
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
                        <span class="text-sm text-slate-500">{{ __('Name') }}</span>
                        <p class="text-sm font-semibold text-slate-900">
                            {{ ($order->shipping_address_snapshot['first_name'] ?? '') . ' ' . ($order->shipping_address_snapshot['last_name'] ?? '') }}
                        </p>
                    </div>
                    <div>
                        <span class="text-sm text-slate-500">{{ __('Email') }}</span>
                        <p class="text-sm text-slate-600">{{ $order->shipping_address_snapshot['email'] ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-slate-500">{{ __('Phone') }}</span>
                        <p class="text-sm text-slate-600">{{ $order->shipping_address_snapshot['phone'] ?? '-' }}</p>
                    </div>
                </div>
            @else
                <p class="text-sm text-slate-500">{{ __('Guest order') }}</p>
            @endif
        </div>
    </div>

    <!-- Shipping Address -->
    @if($order->shipping_address_snapshot)
    <div class="admin-card border-0 p-6 shadow-md shadow-slate-200/50">
        <h2 class="mb-4 text-lg font-semibold text-slate-900">{{ __('Shipping Address') }}</h2>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div>
                <span class="text-sm text-slate-500">{{ __('Full Name') }}</span>
                <p class="text-sm text-slate-600">
                    {{ ($order->shipping_address_snapshot['first_name'] ?? '') . ' ' . ($order->shipping_address_snapshot['last_name'] ?? '') }}
                </p>
            </div>
            <div>
                <span class="text-sm text-slate-500">{{ __('Phone') }}</span>
                <p class="text-sm text-slate-600">{{ $order->shipping_address_snapshot['phone'] ?? '-' }}</p>
            </div>
            <div class="sm:col-span-2 lg:col-span-1">
                <span class="text-sm text-slate-500">{{ __('Country') }}</span>
                <p class="text-sm text-slate-600">{{ $order->shipping_address_snapshot['country'] ?? '-' }}</p>
            </div>
            <div class="sm:col-span-2 lg:col-span-1">
                <span class="text-sm text-slate-500">{{ __('City') }}</span>
                <p class="text-sm text-slate-600">{{ $order->shipping_address_snapshot['city'] ?? '-' }}</p>
            </div>
            <div class="sm:col-span-2">
                <span class="text-sm text-slate-500">{{ __('Address') }}</span>
                <p class="text-sm text-slate-600">
                    {{ $order->shipping_address_snapshot['address_line_1'] ?? '-' }}
                    @if(isset($order->shipping_address_snapshot['address_line_2']) && $order->shipping_address_snapshot['address_line_2'])
                        <br>{{ $order->shipping_address_snapshot['address_line_2'] }}
                    @endif
                </p>
            </div>
            @if(isset($order->shipping_address_snapshot['postal_code']))
            <div>
                <span class="text-sm text-slate-500">{{ __('Postal Code') }}</span>
                <p class="text-sm text-slate-600">{{ $order->shipping_address_snapshot['postal_code'] }}</p>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Order Items -->
    <div class="admin-card overflow-hidden border-0 shadow-md shadow-slate-200/50">
        <div class="border-b border-slate-200 p-6">
            <h2 class="text-lg font-semibold text-slate-900">{{ __('Order Items') }}</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-slate-200 bg-slate-50/80 text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-6 py-3">{{ __('Product') }}</th>
                        <th class="px-6 py-3">{{ __('Variant') }}</th>
                        <th class="px-6 py-3 text-right">{{ __('Price') }}</th>
                        <th class="px-6 py-3 text-right">{{ __('Quantity') }}</th>
                        <th class="px-6 py-3 text-right">{{ __('Total') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($order->items as $item)
                        <tr class="bg-white hover:bg-slate-50/50">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if($item->product && $item->product->main_image)
                                        <img src="{{ asset($item->product->main_image) }}" alt="" class="h-12 w-12 rounded-lg object-cover ring-1 ring-slate-200">
                                    @else
                                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-slate-100 text-slate-400">
                                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 11-.75 0 .375.375 0 0 1.75 0Z" />
                                            </svg>
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-medium text-slate-900">{{ $item->name_snapshot }}</p>
                                        <p class="text-xs text-slate-500">{{ $item->sku_snapshot ?? '-' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-600">
                                @if($item->variant)
                                    @if($item->variant->attributeValues && $item->variant->attributeValues->count() > 0)
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($item->variant->attributeValues as $attributeValue)
                                                @if($attributeValue->attribute)
                                                    <span class="inline-flex items-center rounded-md bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-700">
                                                        {{ $attributeValue->attribute->name }}: {{ $attributeValue->value }}
                                                    </span>
                                                @endif
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-xs text-slate-500">{{ $item->variant->sku ?? '-' }}</span>
                                    @endif
                                @else
                                    <span class="text-xs text-slate-500">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right text-slate-600">
                                {{ $order->currency }} {{ number_format($item->unit_price, 2) }}
                            </td>
                            <td class="px-6 py-4 text-right text-slate-600">
                                {{ $item->qty }}
                            </td>
                            <td class="px-6 py-4 text-right font-semibold text-slate-900">
                                {{ $order->currency }} {{ number_format($item->line_total, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="border-t border-slate-200 bg-slate-50/80">
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-right text-sm font-semibold text-slate-900">
                            {{ __('Subtotal') }}
                        </td>
                        <td class="px-6 py-4 text-right text-sm font-semibold text-slate-900">
                            {{ $order->currency }} {{ number_format($order->subtotal, 2) }}
                        </td>
                    </tr>
                    @if($order->discount_amount > 0)
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-right text-sm text-slate-600">
                            {{ __('Discount') }}
                        </td>
                        <td class="px-6 py-4 text-right text-sm text-green-600">
                            -{{ $order->currency }} {{ number_format($order->discount_amount, 2) }}
                        </td>
                    </tr>
                    @endif
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-right text-sm text-slate-600">
                            {{ __('Shipping') }}
                        </td>
                        <td class="px-6 py-4 text-right text-sm text-slate-600">
                            {{ $order->currency }} {{ number_format($order->shipping_amount, 2) }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-right text-base font-bold text-slate-900">
                            {{ __('Total') }}
                        </td>
                        <td class="px-6 py-4 text-right text-base font-bold text-slate-900">
                            {{ $order->currency }} {{ number_format($order->grand_total, 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Payment Details -->
    @if($order->paymentTransactions && $order->paymentTransactions->count() > 0)
    <div class="admin-card border-0 p-6 shadow-md shadow-slate-200/50">
        <h2 class="mb-4 text-lg font-semibold text-slate-900">{{ __('Payment Transactions') }}</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-slate-200 bg-slate-50/80 text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-3">{{ __('Provider') }}</th>
                        <th class="px-4 py-3">{{ __('Method') }}</th>
                        <th class="px-4 py-3">{{ __('Status') }}</th>
                        <th class="px-4 py-3">{{ __('Amount') }}</th>
                        <th class="px-4 py-3">{{ __('Transaction ID') }}</th>
                        <th class="px-4 py-3">{{ __('Date') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($order->paymentTransactions as $transaction)
                        <tr class="bg-white hover:bg-slate-50/50">
                            <td class="px-4 py-3 text-slate-600">{{ $transaction->getHumanProviderAttribute() }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ ucfirst($transaction->method) }}</td>
                            <td class="px-4 py-3">
                                @php
                                    $txStatusColors = [
                                        'pending' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
                                        'completed' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
                                        'failed' => 'bg-red-50 text-red-700 ring-red-600/20',
                                        'refunded' => 'bg-slate-100 text-slate-600 ring-slate-500/10',
                                    ];
                                    $txStatusColor = $txStatusColors[$transaction->status] ?? 'bg-slate-100 text-slate-600 ring-slate-500/10';
                                @endphp
                                <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium ring-1 ring-inset {{ $txStatusColor }}">
                                    {{ $transaction->getHumanStatusAttribute() }}
                                </span>
                            </td>
                            <td class="px-4 py-3 font-semibold text-slate-900">
                                {{ $transaction->currency }} {{ number_format($transaction->amount, 2) }}
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-500 font-mono">
                                {{ $transaction->provider_transaction_id ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-slate-600">
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
    <div class="admin-card border-0 p-6 shadow-md shadow-slate-200/50">
        <h2 class="mb-4 text-lg font-semibold text-slate-900">{{ __('Order History') }}</h2>
        <div class="space-y-4">
            <div class="flex gap-4">
                <div class="flex flex-col items-center">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-100">
                        <svg class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                    </div>
                    @if($order->status !== 'delivered' && $order->status !== 'cancelled')
                    <div class="h-full w-0.5 bg-slate-200"></div>
                    @endif
                </div>
                <div class="@if($order->status !== 'delivered' && $order->status !== 'cancelled') pb-6 @endif">
                    <p class="text-sm font-semibold text-slate-900">{{ __('Order Created') }}</p>
                    <p class="text-xs text-slate-500">{{ $order->created_at->format('M d, Y - g:i A') }}</p>
                </div>
            </div>

            @if($order->payment_status === 'paid')
            <div class="flex gap-4">
                <div class="flex flex-col items-center">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-100">
                        <svg class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                    </div>
                    @if($order->status !== 'delivered' && $order->status !== 'cancelled')
                    <div class="h-full w-0.5 bg-slate-200"></div>
                    @endif
                </div>
                <div class="@if($order->status !== 'delivered' && $order->status !== 'cancelled') pb-6 @endif">
                    <p class="text-sm font-semibold text-slate-900">{{ __('Payment Completed') }}</p>
                    <p class="text-xs text-slate-500">{{ __('Order has been paid successfully') }}</p>
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
                    @if($order->status !== 'delivered' && $order->status !== 'cancelled')
                    <div class="h-full w-0.5 bg-slate-200"></div>
                    @endif
                </div>
                <div class="@if($order->status !== 'delivered' && $order->status !== 'cancelled') pb-6 @endif">
                    <p class="text-sm font-semibold text-slate-900">{{ __('Order Processing') }}</p>
                    <p class="text-xs text-slate-500">{{ __('Order is being prepared') }}</p>
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
                    @if($order->status !== 'delivered' && $order->status !== 'cancelled')
                    <div class="h-full w-0.5 bg-slate-200"></div>
                    @endif
                </div>
                <div class="@if($order->status !== 'delivered' && $order->status !== 'cancelled') pb-6 @endif">
                    <p class="text-sm font-semibold text-slate-900">{{ __('Order Shipped') }}</p>
                    <p class="text-xs text-slate-500">{{ __('Order has been shipped') }}</p>
                </div>
            </div>
            @endif

            @if($order->status === 'delivered')
            <div class="flex gap-4">
                <div class="flex flex-col items-center">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-100">
                        <svg class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-semibold text-slate-900">{{ __('Order Delivered') }}</p>
                    <p class="text-xs text-slate-500">{{ __('Order has been delivered successfully') }}</p>
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
                    <p class="text-sm font-semibold text-slate-900">{{ __('Order Cancelled') }}</p>
                    <p class="text-xs text-slate-500">{{ __('Order has been cancelled') }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Notes -->
    @if($order->notes)
    <div class="admin-card border-0 p-6 shadow-md shadow-slate-200/50">
        <h2 class="mb-4 text-lg font-semibold text-slate-900">{{ __('Order Notes') }}</h2>
        <p class="text-sm text-slate-600">{{ $order->notes }}</p>
    </div>
    @endif

    <!-- Print Invoice Button -->
    <div class="flex justify-end">
        <button
            onclick="window.print()"
            class="inline-flex items-center gap-2 rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
        >
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
            </svg>
            {{ __('Print Invoice') }}
        </button>
    </div>
</div>
@endsection
