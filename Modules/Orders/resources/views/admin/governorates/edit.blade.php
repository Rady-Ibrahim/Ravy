@extends('layouts.admin')

@section('title', __('Edit Governorate'))

@section('content')
<div class="mx-auto max-w-7xl">
    <!-- Page Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">{{ __('Edit Governorate') }}: {{ $governorate->name }}</h1>
            <p class="mt-1 text-sm text-slate-500">{{ __('Update governorate shipping settings') }}</p>
        </div>
        <div class="flex gap-3">
            <a
                href="{{ route('admin.governorates.index') }}"
                class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
            >
                {{ __('Back') }}
            </a>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="admin-card border-0 p-6 shadow-md shadow-slate-200/50">
        <form action="{{ route('admin.governorates.update', $governorate) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid gap-6 lg:grid-cols-2">
                <!-- Governorate Name (Arabic) -->
                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700">{{ __('Governorate Name (Arabic)') }}</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name', $governorate->name) }}"
                        required
                        placeholder="{{ __('e.g., أبوظبي') }}"
                        class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:ring focus:ring-slate-200 transition-all"
                    >
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Governorate Name (English) -->
                <div>
                    <label for="name_en" class="block text-sm font-medium text-slate-700">{{ __('Governorate Name (English)') }}</label>
                    <input
                        type="text"
                        id="name_en"
                        name="name_en"
                        value="{{ old('name_en', $governorate->name_en) }}"
                        required
                        placeholder="{{ __('e.g., Abu Dhabi') }}"
                        class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:ring focus:ring-slate-200 transition-all"
                    >
                    @error('name_en')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Shipping Cost -->
                <div>
                    <label for="shipping_cost" class="block text-sm font-medium text-slate-700">{{ __('Shipping Cost (AED)') }}</label>
                    <input
                        type="number"
                        id="shipping_cost"
                        name="shipping_cost"
                        value="{{ old('shipping_cost', $governorate->shipping_cost) }}"
                        required
                        min="0"
                        step="0.01"
                        placeholder="{{ __('e.g., 25.00') }}"
                        class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:ring focus:ring-slate-200 transition-all"
                    >
                    @error('shipping_cost')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Delivery Days -->
                <div>
                    <label for="delivery_days" class="block text-sm font-medium text-slate-700">{{ __('Expected Delivery Days') }}</label>
                    <input
                        type="number"
                        id="delivery_days"
                        name="delivery_days"
                        value="{{ old('delivery_days', $governorate->delivery_days) }}"
                        required
                        min="1"
                        max="30"
                        placeholder="{{ __('e.g., 2') }}"
                        class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:ring focus:ring-slate-200 transition-all"
                    >
                    @error('delivery_days')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Active Status -->
                <div class="lg:col-span-2">
                    <label class="flex items-center">
                        <input
                            type="checkbox"
                            name="is_active"
                            value="1"
                            {{ old('is_active', $governorate->is_active) ? 'checked' : '' }}
                            class="h-4 w-4 rounded border-slate-300 bg-slate-100 text-slate-900 focus:ring-slate-500"
                        >
                        <span class="ml-2 text-sm text-slate-700">{{ __('Enable shipping to this governorate') }}</span>
                    </label>
                </div>
            </div>

            <!-- Statistics -->
            <div class="mt-8 border-t border-slate-200 pt-6">
                <h3 class="mb-4 text-lg font-semibold text-slate-900">{{ __('Governorate Statistics') }}</h3>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="rounded-lg bg-slate-50 p-4">
                        <div class="text-sm text-slate-500">{{ __('Total Orders') }}</div>
                        <div class="text-lg font-semibold text-slate-900">{{ $governorate->orders()->count() }}</div>
                    </div>
                    <div class="rounded-lg bg-slate-50 p-4">
                        <div class="text-sm text-slate-500">{{ __('Total Revenue') }}</div>
                        <div class="text-lg font-semibold text-slate-900">
                            {{ number_format($governorate->orders()->sum('grand_total'), 2) }} AED
                        </div>
                    </div>
                    <div class="rounded-lg bg-slate-50 p-4">
                        <div class="text-sm text-slate-500">{{ __('Created At') }}</div>
                        <div class="text-lg font-semibold text-slate-900">{{ $governorate->created_at ? $governorate->created_at->format('d M Y') : '-' }}</div>
                    </div>
                    <div class="rounded-lg bg-slate-50 p-4">
                        <div class="text-sm text-slate-500">{{ __('Status') }}</div>
                        <div class="text-lg font-semibold">
                            <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium ring-1 ring-inset {{ $governorate->is_active 
                                ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20' 
                                : 'bg-slate-100 text-slate-600 ring-slate-500/10' }}">
                                {{ $governorate->is_active ? __('Active') : __('Inactive') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="mt-8 flex gap-3 border-t border-slate-200 pt-6">
                <button
                    type="submit"
                    class="rounded-lg bg-slate-900 px-6 py-2 text-sm font-semibold text-white transition hover:bg-slate-800"
                >
                    {{ __('Update Governorate') }}
                </button>
                <a
                    href="{{ route('admin.governorates.index') }}"
                    class="rounded-lg border border-slate-300 px-6 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
                >
                    {{ __('Cancel') }}
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
