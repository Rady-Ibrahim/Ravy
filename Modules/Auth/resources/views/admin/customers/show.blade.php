@extends('layouts.admin')

@section('title', __('Customer Details') . ' - ' . $customer->name)

@section('page_title', __('Customer Details'))
@section('page_subtitle', $customer->first_name . ' ' . $customer->last_name)

@section('content')
    <div class="mx-auto max-w-7xl">
        <!-- Customer Info Card -->
        <div class="mb-6 admin-card">
            <div class="p-6">
                <div class="flex items-start justify-between">
                    <div class="flex items-start gap-4">
                        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-blue-100 text-2xl font-bold text-blue-600">
                            {{ strtoupper(substr($customer->first_name, 0, 1) . substr($customer->last_name, 0, 1)) }}
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-slate-900">
                                {{ $customer->first_name }} {{ $customer->last_name }}
                            </h3>
                            <p class="text-sm text-slate-600">{{ $customer->email }}</p>
                            <div class="mt-2 flex items-center gap-4 text-sm">
                                <span class="text-slate-500">
                                    <strong>{{ __('Phone') }}:</strong> {{ $customer->phone ?: '—' }}
                                </span>
                                <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium ring-1 ring-inset {{ $customer->status === 'active' ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20' : 'bg-slate-100 text-slate-600 ring-slate-500/10' }}">
                                    {{ $customer->status }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        @can('admin.customers.edit')
                            <a href="{{ route('admin.customers.edit', $customer) }}" class="inline-flex items-center rounded-lg bg-amber-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-amber-500">
                                {{ __('Edit Customer') }}
                            </a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>

        <!-- Orders History -->
        <div class="admin-card">
            <div class="border-b border-slate-200 px-6 py-4">
                <h4 class="text-lg font-semibold text-slate-900">{{ __('Order History') }}</h4>
                <p class="text-sm text-slate-600">{{ $customer->orders->count() }} {{ __('total orders') }}</p>
            </div>
            <div class="overflow-x-auto">
                @if ($customer->orders->count() > 0)
                    <table class="w-full text-left text-sm">
                        <thead class="border-b border-slate-200 bg-slate-50/80 text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-6 py-3">{{ __('Order ID') }}</th>
                                <th class="px-6 py-3">{{ __('Date') }}</th>
                                <th class="px-6 py-3">{{ __('Total') }}</th>
                                <th class="px-6 py-3">{{ __('Status') }}</th>
                                <th class="px-6 py-3 text-end">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($customer->orders as $order)
                                <tr class="bg-white hover:bg-slate-50/50">
                                    <td class="px-6 py-3 font-medium text-slate-900">#{{ $order->id }}</td>
                                    <td class="px-6 py-3 text-slate-600">{{ $order->created_at->format('M d, Y') }}</td>
                                    <td class="px-6 py-3 font-medium text-slate-900">${{ number_format($order->grand_total, 2) }}</td>
                                    <td class="px-6 py-3">
                                        <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium ring-1 ring-inset 
                                            {{ $order->status === 'completed' ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20' : 
                                               ($order->status === 'pending' ? 'bg-amber-50 text-amber-700 ring-amber-600/20' : 
                                               'bg-slate-100 text-slate-600 ring-slate-500/10') }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3 text-end">
                                        <a href="{{ route('admin.orders.show', $order) }}" class="rounded-lg px-2 py-1 text-xs font-semibold text-blue-700 hover:bg-blue-50">
                                            {{ __('View') }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="px-6 py-8 text-center text-slate-500">
                        {{ __('No orders found for this customer.') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
