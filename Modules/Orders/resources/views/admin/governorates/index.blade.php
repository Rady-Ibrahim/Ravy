@extends('layouts.admin')

@section('title', __('Shipping Rates'))

@section('content')
<div class="mx-auto max-w-7xl">
    <!-- Page Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">{{ __('Shipping Rates') }}</h1>
            <p class="mt-1 text-sm text-slate-500">{{ __('Manage shipping costs by governorate') }}</p>
        </div>
        <div class="flex gap-3">
            <a
                href="{{ route('admin.governorates.create') }}"
                class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800"
            >
                {{ __('Add Governorate') }}
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="admin-card border-0 p-4 shadow-md shadow-slate-200/50">
            <div class="text-sm text-slate-500">{{ __('Total Governorates') }}</div>
            <div class="text-2xl font-bold text-slate-900">{{ $governorates->count() }}</div>
        </div>
        <div class="admin-card border-0 p-4 shadow-md shadow-slate-200/50">
            <div class="text-sm text-slate-500">{{ __('Active Governorates') }}</div>
            <div class="text-2xl font-bold text-emerald-600">{{ $governorates->where('is_active', true)->count() }}</div>
        </div>
        <div class="admin-card border-0 p-4 shadow-md shadow-slate-200/50">
            <div class="text-sm text-slate-500">{{ __('Average Shipping Cost') }}</div>
            <div class="text-2xl font-bold text-slate-900">
                {{ $governorates->avg('shipping_cost') ? number_format($governorates->avg('shipping_cost'), 2) : '0.00' }} AED
            </div>
        </div>
        <div class="admin-card border-0 p-4 shadow-md shadow-slate-200/50">
            <div class="text-sm text-slate-500">{{ __('Total Orders') }}</div>
            <div class="text-2xl font-bold text-slate-900">{{ $governorates->sum(function($g) { return $g->orders()->count(); }) }}</div>
        </div>
    </div>

    <!-- Governorates Table -->
    <div class="admin-card overflow-hidden border-0 shadow-md shadow-slate-200/50">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[760px] text-left text-sm">
                <thead class="border-b border-slate-200 bg-slate-50/80 text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-3">{{ __('Governorate') }}</th>
                        <th class="px-4 py-3 text-center">{{ __('Shipping Cost') }}</th>
                        <th class="px-4 py-3 text-center">{{ __('Delivery Days') }}</th>
                        <th class="px-4 py-3 text-center">{{ __('Status') }}</th>
                        <th class="px-4 py-3 text-center">{{ __('Orders Count') }}</th>
                        <th class="px-4 py-3 text-end">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($governorates as $governorate)
                        <tr class="bg-white hover:bg-slate-50/50">
                            <td class="px-4 py-3">
                                <div class="font-medium text-slate-900">{{ $governorate->name }}</div>
                                <div class="text-xs text-slate-500">{{ $governorate->name_en }}</div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="font-semibold text-slate-900">{{ number_format($governorate->shipping_cost, 2) }} AED</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="text-sm text-slate-600">{{ $governorate->delivery_days }} {{ __('days') }}</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <form action="{{ route('admin.governorates.toggle-status', $governorate) }}" method="POST" class="inline">
                                    @csrf
                                    <button
                                        type="submit"
                                        class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium ring-1 ring-inset transition {{ $governorate->is_active 
                                            ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 hover:bg-emerald-100' 
                                            : 'bg-slate-100 text-slate-600 ring-slate-500/10 hover:bg-slate-200' }}"
                                    >
                                        {{ $governorate->is_active ? __('Active') : __('Inactive') }}
                                    </button>
                                </form>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-slate-100 text-xs font-medium text-slate-600 ring-1 ring-inset ring-slate-500/10">
                                    {{ $governorate->orders_count ?? $governorate->orders()->count() }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-end">
                                <div class="flex items-center justify-end gap-1">
                                    <a
                                        href="{{ route('admin.governorates.edit', $governorate) }}"
                                        class="rounded-lg px-2 py-1 text-xs font-semibold text-amber-700 hover:bg-amber-50"
                                        title="{{ __('Edit') }}"
                                    >
                                        {{ __('Edit') }}
                                    </a>
                                    <form action="{{ route('admin.governorates.destroy', $governorate) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this governorate?') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="rounded-lg px-2 py-1 text-xs font-semibold text-red-600 hover:bg-red-50"
                                            title="{{ __('Delete') }}"
                                        >
                                            {{ __('Delete') }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-slate-500">
                                {{ __('No governorates found') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
