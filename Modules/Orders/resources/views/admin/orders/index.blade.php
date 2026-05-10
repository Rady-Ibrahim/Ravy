@extends('layouts.admin')

@section('title', __('Orders'))

@section('content')
<div class="mx-auto max-w-7xl">
    <!-- Page Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">{{ __('Orders') }}</h1>
            <p class="mt-1 text-sm text-slate-500">{{ __('Manage and track all orders') }}</p>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="admin-card mb-6 border-0 p-0 shadow-md shadow-slate-200/50">
        <div class="border-b border-slate-100 px-4 py-4 sm:px-6">
            <form method="GET" action="{{ route('admin.orders.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-6">
                    <!-- Search -->
                    <div class="lg:col-span-2">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="{{ __('Search by order number or customer email...') }}"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:ring focus:ring-slate-200 transition-all">
                    </div>

                    <!-- Order Status -->
                    <div>
                        <select name="status"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:ring focus:ring-slate-200 transition-all">
                            <option value="">{{ __('All statuses') }}</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                            <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>{{ __('Processing') }}</option>
                            <option value="shipped" {{ request('status') === 'shipped' ? 'selected' : '' }}>{{ __('Shipped') }}</option>
                            <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>{{ __('Delivered') }}</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
                        </select>
                    </div>

                    <!-- Payment Status -->
                    <div>
                        <select name="payment_status"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:ring focus:ring-slate-200 transition-all">
                            <option value="">{{ __('All payments') }}</option>
                            <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                            <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>{{ __('Paid') }}</option>
                            <option value="failed" {{ request('payment_status') === 'failed' ? 'selected' : '' }}>{{ __('Failed') }}</option>
                            <option value="refunded" {{ request('payment_status') === 'refunded' ? 'selected' : '' }}>{{ __('Refunded') }}</option>
                        </select>
                    </div>

                    <!-- Date From -->
                    <div>
                        <input type="date" name="date_from" value="{{ request('date_from') }}"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:ring focus:ring-slate-200 transition-all">
                    </div>

                    <!-- Date To -->
                    <div>
                        <input type="date" name="date_to" value="{{ request('date_to') }}"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:ring focus:ring-slate-200 transition-all">
                    </div>

                    <!-- Apply button -->
                    <div class="flex gap-2 lg:col-span-2">
                        <button type="submit"
                            class="flex-1 rounded-lg bg-slate-900 px-3 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
                            {{ __('Filter') }}
                        </button>
                        @if (request()->anyFilled(['search', 'status', 'payment_status', 'date_from', 'date_to']))
                            <a href="{{ route('admin.orders.index') }}"
                                class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                                {{ __('Clear') }}
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="admin-card overflow-hidden border-0 p-0 shadow-md shadow-slate-200/50">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1200px] text-left text-sm">
                <thead class="border-b border-slate-200 bg-slate-50/80 text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-3 text-center">#</th>
                        <th class="px-4 py-3">{{ __('Order Code') }}</th>
                        <th class="px-4 py-3">{{ __('Items') }}</th>
                        <th class="px-4 py-3">{{ __('Customer') }}</th>
                        <th class="px-4 py-3">{{ __('Phone') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('Total') }}</th>
                        <th class="px-4 py-3 text-center">{{ __('Payment') }}</th>
                        <th class="px-4 py-3 text-center">{{ __('Status') }}</th>
                        <th class="px-4 py-3 text-center">{{ __('Date') }}</th>
                        <th class="px-4 py-3 text-end">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($orders as $index => $order)
                        <tr class="bg-white hover:bg-slate-50/50">
                            <!-- # -->
                            <td class="px-4 py-3 text-center text-slate-400">
                                {{ $orders->firstItem() + $index }}
                            </td>
                            
                            <!-- Order Code -->
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.orders.show', $order->order_number) }}" class="font-mono font-medium text-slate-900 hover:text-slate-700">
                                    {{ $order->order_number }}
                                </a>
                                <div class="mt-1">
                                    @if($order->user)
                                        <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium ring-1 ring-inset bg-emerald-50 text-emerald-700 ring-emerald-600/20">{{ __('User') }}</span>
                                    @else
                                        <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium ring-1 ring-inset bg-slate-100 text-slate-600 ring-slate-500/10">{{ __('Guest') }}</span>
                                    @endif
                                </div>
                            </td>
                            
                            <!-- Items -->
                            <td class="px-4 py-3 text-slate-600">
                                {{ $order->items_count ?? $order->items->count() }}
                            </td>
                            
                            <!-- Customer -->
                            <td class="px-4 py-3 font-medium text-slate-900">
                                @if($order->user)
                                    {{ $order->user->name }}
                                    <div class="text-xs font-normal text-slate-500">{{ $order->user->email }}</div>
                                @elseif($order->shipping_address_snapshot)
                                    {{ ($order->shipping_address_snapshot['first_name'] ?? '') . ' ' . ($order->shipping_address_snapshot['last_name'] ?? '') }}
                                    <div class="text-xs font-normal text-slate-500">{{ $order->shipping_address_snapshot['email'] ?? '-' }}</div>
                                @else
                                    —
                                @endif
                            </td>
                            
                            <!-- Phone -->
                            <td class="px-4 py-3 text-slate-600 font-mono">
                                {{ $order->shipping_address_snapshot['phone'] ?? ($order->user?->phone ?? '—') }}
                            </td>
                            
                            <!-- Total -->
                            <td class="px-4 py-3 text-right">
                                <div class="font-semibold text-slate-900">{{ number_format($order->grand_total, 2) }} {{ $order->currency }}</div>
                                <div class="text-xs text-slate-500">{{ $order->payment_method === 'cod' ? __('COD') : ucfirst($order->payment_method) }}</div>
                            </td>
                            
                            <!-- Payment Status -->
                            <td class="px-4 py-3 text-center">
                                @php
                                    $paymentStatusColors = [
                                        'pending' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
                                        'paid' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
                                        'unpaid' => 'bg-red-50 text-red-700 ring-red-600/20',
                                        'failed' => 'bg-red-50 text-red-700 ring-red-600/20',
                                        'refunded' => 'bg-slate-100 text-slate-600 ring-slate-500/10',
                                    ];
                                    $paymentStatusColor = $paymentStatusColors[$order->payment_status] ?? 'bg-slate-100 text-slate-600 ring-slate-500/10';
                                    $paymentStatusLabels = [
                                        'pending' => __('Pending'),
                                        'paid' => __('Paid'),
                                        'unpaid' => __('Unpaid'),
                                        'failed' => __('Failed'),
                                        'refunded' => __('Refunded'),
                                    ];
                                @endphp
                                <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium ring-1 ring-inset {{ $paymentStatusColor }}">
                                    {{ $paymentStatusLabels[$order->payment_status] ?? ucfirst($order->payment_status) }}
                                </span>
                            </td>
                            
                            <!-- Status -->
                            <td class="px-4 py-3 text-center">
                                @php
                                    $statusColors = [
                                        'pending_payment' => 'bg-orange-50 text-orange-700 ring-orange-600/20',
                                        'pending' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
                                        'processing' => 'bg-blue-50 text-blue-700 ring-blue-600/20',
                                        'shipped' => 'bg-purple-50 text-purple-700 ring-purple-600/20',
                                        'delivered' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
                                        'cancelled' => 'bg-red-50 text-red-700 ring-red-600/20',
                                    ];
                                    $statusColor = $statusColors[$order->status] ?? 'bg-slate-100 text-slate-600 ring-slate-500/10';
                                    $statusLabels = [
                                        'pending_payment' => __('Pending Payment'),
                                        'pending' => __('Pending'),
                                        'processing' => __('Processing'),
                                        'shipped' => __('Shipped'),
                                        'delivered' => __('Delivered'),
                                        'cancelled' => __('Cancelled'),
                                    ];
                                @endphp
                                <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium ring-1 ring-inset {{ $statusColor }}">
                                    {{ $statusLabels[$order->status] ?? ucfirst($order->status) }}
                                </span>
                            </td>
                            
                            <!-- Date -->
                            <td class="px-4 py-3 text-center text-slate-600">
                                {{ $order->created_at->format('d M Y') }}
                                <div class="text-xs text-slate-400">{{ $order->created_at->format('H:i') }}</div>
                            </td>
                            
                            <!-- Actions -->
                            <td class="px-4 py-3 text-end">
                                <a href="{{ route('admin.orders.show', $order->order_number) }}"
                                    class="rounded-lg px-2 py-1 text-xs font-semibold text-indigo-700 hover:bg-indigo-50">{{ __('View') }}</a>
                                <a href="{{ route('admin.orders.edit', $order->order_number) }}"
                                    class="rounded-lg px-2 py-1 text-xs font-semibold text-amber-700 hover:bg-amber-50">{{ __('Edit') }}</a>
                                <form action="{{ route('admin.orders.destroy', $order->order_number) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this order? This action cannot be undone.') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="rounded-lg px-2 py-1 text-xs font-semibold text-red-600 hover:bg-red-50">{{ __('Delete') }}</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-4 py-8 text-center text-slate-500">
                                {{ __('No orders found') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($orders->hasPages())
            <div class="border-t border-slate-100 px-4 py-3">
                {{ $orders->appends(request()->except('page'))->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
