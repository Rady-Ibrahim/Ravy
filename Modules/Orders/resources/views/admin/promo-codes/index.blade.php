@extends('layouts.admin')

@section('title', __('Promo Codes'))

@section('content')
<div class="mx-auto max-w-7xl">
    <!-- Page Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">{{ __('Promo Codes') }}</h1>
            <p class="mt-1 text-sm text-slate-500">{{ __('Manage discount codes for customers') }}</p>
        </div>
        <div class="flex gap-3">
            <a
                href="{{ route('admin.promo-codes.create') }}"
                class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800"
            >
                {{ __('Add Promo Code') }}
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="admin-card border-0 p-4 shadow-md shadow-slate-200/50">
            <div class="text-sm text-slate-500">{{ __('Total Codes') }}</div>
            <div class="text-2xl font-bold text-slate-900">{{ $promoCodes->count() }}</div>
        </div>
        <div class="admin-card border-0 p-4 shadow-md shadow-slate-200/50">
            <div class="text-sm text-slate-500">{{ __('Active Codes') }}</div>
            <div class="text-2xl font-bold text-emerald-600">{{ $promoCodes->where('is_active', true)->count() }}</div>
        </div>
        <div class="admin-card border-0 p-4 shadow-md shadow-slate-200/50">
            <div class="text-sm text-slate-500">{{ __('Total Uses') }}</div>
            <div class="text-2xl font-bold text-slate-900">{{ $promoCodes->sum('used_count') }}</div>
        </div>
        <div class="admin-card border-0 p-4 shadow-md shadow-slate-200/50">
            <div class="text-sm text-slate-500">{{ __('Expired Codes') }}</div>
            <div class="text-2xl font-bold text-red-600">{{ $promoCodes->filter(fn($c) => $c->expires_at && $c->expires_at->isPast())->count() }}</div>
        </div>
    </div>

    <!-- Promo Codes Table -->
    <div class="admin-card overflow-hidden border-0 shadow-md shadow-slate-200/50">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[760px] text-left text-sm">
                <thead class="border-b border-slate-200 bg-slate-50/80 text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-3">{{ __('Code') }}</th>
                        <th class="px-4 py-3">{{ __('Description') }}</th>
                        <th class="px-4 py-3 text-center">{{ __('Discount') }}</th>
                        <th class="px-4 py-3 text-center">{{ __('Used / Max') }}</th>
                        <th class="px-4 py-3 text-center">{{ __('Expires At') }}</th>
                        <th class="px-4 py-3 text-center">{{ __('Status') }}</th>
                        <th class="px-4 py-3 text-end">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($promoCodes as $promoCode)
                        <tr class="bg-white hover:bg-slate-50/50">
                            <td class="px-4 py-3">
                                <div class="font-mono font-bold text-slate-900">{{ $promoCode->code }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-sm text-slate-600">{{ $promoCode->description ?? '—' }}</div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="font-semibold text-slate-900">
                                    {{ $promoCode->discount_type === 'percentage' ? $promoCode->discount_value . '%' : number_format($promoCode->discount_value, 2) . ' EGP' }}
                                </div>
                                @if($promoCode->min_amount > 0)
                                    <div class="text-xs text-slate-500">{{ __('Min:') }} {{ number_format($promoCode->min_amount, 2) }} EGP</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="text-sm text-slate-600">
                                    {{ $promoCode->used_count }} / {{ $promoCode->max_uses ?? '∞' }}
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($promoCode->expires_at)
                                    <div class="text-sm {{ $promoCode->expires_at->isPast() ? 'text-red-600' : 'text-slate-600' }}">
                                        {{ $promoCode->expires_at->format('M d, Y') }}
                                    </div>
                                @else
                                    <div class="text-sm text-slate-500">{{ __('Never') }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <form action="{{ route('admin.promo-codes.toggle-status', $promoCode) }}" method="POST" class="inline">
                                    @csrf
                                    <button
                                        type="submit"
                                        class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium ring-1 ring-inset transition {{ $promoCode->is_active 
                                            ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 hover:bg-emerald-100' 
                                            : 'bg-slate-100 text-slate-600 ring-slate-500/10 hover:bg-slate-200' }}"
                                    >
                                        {{ $promoCode->is_active ? __('Active') : __('Inactive') }}
                                    </button>
                                </form>
                            </td>
                            <td class="px-4 py-3 text-end">
                                <div class="flex items-center justify-end gap-1">
                                    <a
                                        href="{{ route('admin.promo-codes.edit', $promoCode) }}"
                                        class="rounded-lg px-2 py-1 text-xs font-semibold text-amber-700 hover:bg-amber-50"
                                        title="{{ __('Edit') }}"
                                    >
                                        {{ __('Edit') }}
                                    </a>
                                    <form action="{{ route('admin.promo-codes.destroy', $promoCode) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this promo code?') }}')">
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
                            <td colspan="7" class="px-4 py-8 text-center text-slate-500">
                                {{ __('No promo codes found') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($promoCodes->hasPages())
        <div class="mt-6 flex items-center justify-between">
            <div class="text-sm text-slate-500">
                {{ __('Showing') }} {{ $promoCodes->firstItem() }} {{ __('to') }} {{ $promoCodes->lastItem() }} {{ __('of') }} {{ $promoCodes->total() }} {{ __('results') }}
            </div>
            {{ $promoCodes->links() }}
        </div>
    @endif
</div>
@endsection
