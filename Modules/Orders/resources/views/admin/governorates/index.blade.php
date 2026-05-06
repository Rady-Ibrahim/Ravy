@extends('layouts.admin')

@section('title', __('Shipping Rates'))

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-brand-navy">{{ __('Shipping Rates') }}</h1>
            <p class="mt-1 text-sm text-brand-navy/60">{{ __('Manage shipping costs by governorate') }}</p>
        </div>
        <div class="flex gap-3">
            <a
                href="{{ route('admin.governorates.create') }}"
                class="rounded-lg bg-brand-navy px-4 py-2 text-sm font-semibold text-white transition hover:bg-brand-navy/90"
            >
                {{ __('Add Governorate') }}
            </a>
        </div>
    </div>

    <!-- Governorates Table -->
    <div class="overflow-hidden rounded-xl border border-brand-navy/10 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-brand-navy/10 bg-brand-navy/5">
                    <tr>
                        <th class="px-4 py-3 font-semibold text-brand-navy">{{ __('Governorate') }}</th>
                        <th class="px-4 py-3 font-semibold text-brand-navy text-center">{{ __('Shipping Cost') }}</th>
                        <th class="px-4 py-3 font-semibold text-brand-navy text-center">{{ __('Delivery Days') }}</th>
                        <th class="px-4 py-3 font-semibold text-brand-navy text-center">{{ __('Status') }}</th>
                        <th class="px-4 py-3 font-semibold text-brand-navy text-center">{{ __('Orders Count') }}</th>
                        <th class="px-4 py-3 font-semibold text-brand-navy text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-navy/10">
                    @forelse($governorates as $governorate)
                        <tr class="hover:bg-brand-navy/5">
                            <td class="px-4 py-3">
                                <div class="font-medium text-brand-navy">{{ $governorate->name }}</div>
                                <div class="text-xs text-brand-navy/60">{{ $governorate->name_en }}</div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="font-semibold text-brand-navy">{{ number_format($governorate->shipping_cost, 2) }} AED</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="text-sm text-brand-navy/80">{{ $governorate->delivery_days }} {{ __('days') }}</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <form action="{{ route('admin.governorates.toggle-status', $governorate) }}" method="POST" class="inline">
                                    @csrf
                                    <button
                                        type="submit"
                                        class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium transition {{ $governorate->is_active 
                                            ? 'bg-green-100 text-green-800 hover:bg-green-200' 
                                            : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                                    >
                                        {{ $governorate->is_active ? __('Active') : __('Inactive') }}
                                    </button>
                                </form>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-brand-navy/10 text-xs font-medium text-brand-navy">
                                    {{ $governorate->orders_count ?? $governorate->orders()->count() }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <a
                                        href="{{ route('admin.governorates.edit', $governorate) }}"
                                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-brand-navy hover:bg-brand-navy/10"
                                        title="{{ __('Edit') }}"
                                    >
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.governorates.destroy', $governorate) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this governorate?') }}')">
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
                            <td colspan="6" class="px-4 py-8 text-center text-brand-navy/60">
                                {{ __('No governorates found') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl border border-brand-navy/10 bg-white p-4 shadow-sm">
            <div class="text-sm text-brand-navy/60">{{ __('Total Governorates') }}</div>
            <div class="text-2xl font-bold text-brand-navy">{{ $governorates->count() }}</div>
        </div>
        <div class="rounded-xl border border-brand-navy/10 bg-white p-4 shadow-sm">
            <div class="text-sm text-brand-navy/60">{{ __('Active Governorates') }}</div>
            <div class="text-2xl font-bold text-green-600">{{ $governorates->where('is_active', true)->count() }}</div>
        </div>
        <div class="rounded-xl border border-brand-navy/10 bg-white p-4 shadow-sm">
            <div class="text-sm text-brand-navy/60">{{ __('Average Shipping Cost') }}</div>
            <div class="text-2xl font-bold text-brand-navy">
                {{ $governorates->avg('shipping_cost') ? number_format($governorates->avg('shipping_cost'), 2) : '0.00' }} AED
            </div>
        </div>
        <div class="rounded-xl border border-brand-navy/10 bg-white p-4 shadow-sm">
            <div class="text-sm text-brand-navy/60">{{ __('Total Orders') }}</div>
            <div class="text-2xl font-bold text-brand-navy">{{ $governorates->sum(function($g) { return $g->orders()->count(); }) }}</div>
        </div>
    </div>
</div>
@endsection
