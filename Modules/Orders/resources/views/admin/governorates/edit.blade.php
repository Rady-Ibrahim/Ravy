@extends('layouts.admin')

@section('title', __('Edit Governorate'))

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-brand-navy">{{ __('Edit Governorate') }}: {{ $governorate->name }}</h1>
            <p class="mt-1 text-sm text-brand-navy/60">{{ __('Update governorate shipping settings') }}</p>
        </div>
        <div class="flex gap-3">
            <a
                href="{{ route('admin.governorates.index') }}"
                class="rounded-lg border border-brand-navy/20 px-4 py-2 text-sm font-semibold text-brand-navy transition hover:bg-brand-navy/5"
            >
                {{ __('Back') }}
            </a>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="rounded-xl border border-brand-navy/10 bg-white p-6 shadow-sm">
        <form action="{{ route('admin.governorates.update', $governorate) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid gap-6 lg:grid-cols-2">
                <!-- Governorate Name (Arabic) -->
                <div>
                    <label for="name" class="block text-sm font-medium text-brand-navy">{{ __('Governorate Name (Arabic)') }}</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name', $governorate->name) }}"
                        required
                        placeholder="{{ __('e.g., أبوظبي') }}"
                        class="mt-1 block w-full rounded-lg border border-brand-navy/20 px-3 py-2 text-sm focus:border-brand-navy focus:outline-none focus:ring-1 focus:ring-brand-navy"
                    >
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Governorate Name (English) -->
                <div>
                    <label for="name_en" class="block text-sm font-medium text-brand-navy">{{ __('Governorate Name (English)') }}</label>
                    <input
                        type="text"
                        id="name_en"
                        name="name_en"
                        value="{{ old('name_en', $governorate->name_en) }}"
                        required
                        placeholder="{{ __('e.g., Abu Dhabi') }}"
                        class="mt-1 block w-full rounded-lg border border-brand-navy/20 px-3 py-2 text-sm focus:border-brand-navy focus:outline-none focus:ring-1 focus:ring-brand-navy"
                    >
                    @error('name_en')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Shipping Cost -->
                <div>
                    <label for="shipping_cost" class="block text-sm font-medium text-brand-navy">{{ __('Shipping Cost (AED)') }}</label>
                    <input
                        type="number"
                        id="shipping_cost"
                        name="shipping_cost"
                        value="{{ old('shipping_cost', $governorate->shipping_cost) }}"
                        required
                        min="0"
                        step="0.01"
                        placeholder="{{ __('e.g., 25.00') }}"
                        class="mt-1 block w-full rounded-lg border border-brand-navy/20 px-3 py-2 text-sm focus:border-brand-navy focus:outline-none focus:ring-1 focus:ring-brand-navy"
                    >
                    @error('shipping_cost')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Delivery Days -->
                <div>
                    <label for="delivery_days" class="block text-sm font-medium text-brand-navy">{{ __('Expected Delivery Days') }}</label>
                    <input
                        type="number"
                        id="delivery_days"
                        name="delivery_days"
                        value="{{ old('delivery_days', $governorate->delivery_days) }}"
                        required
                        min="1"
                        max="30"
                        placeholder="{{ __('e.g., 2') }}"
                        class="mt-1 block w-full rounded-lg border border-brand-navy/20 px-3 py-2 text-sm focus:border-brand-navy focus:outline-none focus:ring-1 focus:ring-brand-navy"
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
                            class="h-4 w-4 rounded border-brand-navy/20 bg-brand-navy/10 text-brand-navy focus:ring-brand-navy"
                        >
                        <span class="ml-2 text-sm text-brand-navy">{{ __('Enable shipping to this governorate') }}</span>
                    </label>
                </div>
            </div>

            <!-- Statistics -->
            <div class="mt-8 border-t border-brand-navy/10 pt-6">
                <h3 class="mb-4 text-lg font-semibold text-brand-navy">{{ __('Governorate Statistics') }}</h3>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="rounded-lg bg-brand-navy/5 p-4">
                        <div class="text-sm text-brand-navy/60">{{ __('Total Orders') }}</div>
                        <div class="text-lg font-semibold text-brand-navy">{{ $governorate->orders()->count() }}</div>
                    </div>
                    <div class="rounded-lg bg-brand-navy/5 p-4">
                        <div class="text-sm text-brand-navy/60">{{ __('Total Revenue') }}</div>
                        <div class="text-lg font-semibold text-brand-navy">
                            {{ number_format($governorate->orders()->sum('grand_total'), 2) }} AED
                        </div>
                    </div>
                    <div class="rounded-lg bg-brand-navy/5 p-4">
                        <div class="text-sm text-brand-navy/60">{{ __('Created At') }}</div>
                        <div class="text-lg font-semibold text-brand-navy">{{ $governorate->created_at->format('d M Y') }}</div>
                    </div>
                    <div class="rounded-lg bg-brand-navy/5 p-4">
                        <div class="text-sm text-brand-navy/60">{{ __('Status') }}</div>
                        <div class="text-lg font-semibold">
                            <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {{ $governorate->is_active 
                                ? 'bg-green-100 text-green-800' 
                                : 'bg-gray-100 text-gray-600' }}">
                                {{ $governorate->is_active ? __('Active') : __('Inactive') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="mt-8 flex gap-3 border-t border-brand-navy/10 pt-6">
                <button
                    type="submit"
                    class="rounded-lg bg-brand-navy px-6 py-2 text-sm font-semibold text-white transition hover:bg-brand-navy/90"
                >
                    {{ __('Update Governorate') }}
                </button>
                <a
                    href="{{ route('admin.governorates.index') }}"
                    class="rounded-lg border border-brand-navy/20 px-6 py-2 text-sm font-semibold text-brand-navy transition hover:bg-brand-navy/5"
                >
                    {{ __('Cancel') }}
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
