@extends('layouts.admin')

@section('title', __('Add Promo Code'))

@section('content')
<div class="mx-auto max-w-7xl">
    <!-- Page Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">{{ __('Add Promo Code') }}</h1>
            <p class="mt-1 text-sm text-slate-500">{{ __('Create a new discount code for customers') }}</p>
        </div>
        <div class="flex gap-3">
            <a
                href="{{ route('admin.promo-codes.index') }}"
                class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
            >
                {{ __('Back') }}
            </a>
        </div>
    </div>

    <!-- Create Form -->
    <div class="admin-card border-0 p-6 shadow-md shadow-slate-200/50">
        <form action="{{ route('admin.promo-codes.store') }}" method="POST">
            @csrf

            <div class="grid gap-6 lg:grid-cols-2">
                <!-- Code -->
                <div>
                    <label for="code" class="block text-sm font-medium text-slate-700">{{ __('Promo Code') }}</label>
                    <input
                        type="text"
                        id="code"
                        name="code"
                        value="{{ old('code') }}"
                        required
                        placeholder="{{ __('e.g., SAVE10') }}"
                        class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm font-mono uppercase focus:border-slate-500 focus:ring focus:ring-slate-200 transition-all"
                    >
                    @error('code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-slate-700">{{ __('Description') }}</label>
                    <input
                        type="text"
                        id="description"
                        name="description"
                        value="{{ old('description') }}"
                        placeholder="{{ __('e.g., 10% off for first order') }}"
                        class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:ring focus:ring-slate-200 transition-all"
                    >
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Discount Type -->
                <div>
                    <label for="discount_type" class="block text-sm font-medium text-slate-700">{{ __('Discount Type') }}</label>
                    <select
                        id="discount_type"
                        name="discount_type"
                        required
                        class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:ring focus:ring-slate-200 transition-all"
                    >
                        <option value="percentage" {{ old('discount_type') === 'percentage' ? 'selected' : '' }}>{{ __('Percentage') }}</option>
                        <option value="fixed" {{ old('discount_type') === 'fixed' ? 'selected' : '' }}>{{ __('Fixed Amount') }}</option>
                    </select>
                    @error('discount_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Discount Value -->
                <div>
                    <label for="discount_value" class="block text-sm font-medium text-slate-700">{{ __('Discount Value') }}</label>
                    <input
                        type="number"
                        id="discount_value"
                        name="discount_value"
                        value="{{ old('discount_value') }}"
                        required
                        min="0"
                        step="0.01"
                        placeholder="{{ __('e.g., 10') }}"
                        class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:ring focus:ring-slate-200 transition-all"
                    >
                    @error('discount_value')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Minimum Amount -->
                <div>
                    <label for="min_amount" class="block text-sm font-medium text-slate-700">{{ __('Minimum Order Amount (EGP)') }}</label>
                    <input
                        type="number"
                        id="min_amount"
                        name="min_amount"
                        value="{{ old('min_amount', 0) }}"
                        min="0"
                        step="0.01"
                        placeholder="{{ __('e.g., 500') }}"
                        class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:ring focus:ring-slate-200 transition-all"
                    >
                    @error('min_amount')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Maximum Discount Amount -->
                <div>
                    <label for="max_discount_amount" class="block text-sm font-medium text-slate-700">{{ __('Maximum Discount Amount (EGP)') }}</label>
                    <input
                        type="number"
                        id="max_discount_amount"
                        name="max_discount_amount"
                        value="{{ old('max_discount_amount') }}"
                        min="0"
                        step="0.01"
                        placeholder="{{ __('e.g., 200') }}"
                        class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:ring focus:ring-slate-200 transition-all"
                    >
                    @error('max_discount_amount')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Maximum Uses -->
                <div>
                    <label for="max_uses" class="block text-sm font-medium text-slate-700">{{ __('Maximum Uses') }}</label>
                    <input
                        type="number"
                        id="max_uses"
                        name="max_uses"
                        value="{{ old('max_uses') }}"
                        min="1"
                        placeholder="{{ __('e.g., 100') }}"
                        class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:ring focus:ring-slate-200 transition-all"
                    >
                    <p class="mt-1 text-xs text-slate-500">{{ __('Leave empty for unlimited') }}</p>
                    @error('max_uses')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Expires At -->
                <div>
                    <label for="expires_at" class="block text-sm font-medium text-slate-700">{{ __('Expires At') }}</label>
                    <input
                        type="datetime-local"
                        id="expires_at"
                        name="expires_at"
                        value="{{ old('expires_at') }}"
                        class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:ring focus:ring-slate-200 transition-all"
                    >
                    <p class="mt-1 text-xs text-slate-500">{{ __('Leave empty for no expiration') }}</p>
                    @error('expires_at')
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
                            {{ old('is_active', '1') ? 'checked' : '' }}
                            class="h-4 w-4 rounded border-slate-300 bg-slate-100 text-slate-900 focus:ring-slate-500"
                        >
                        <span class="ml-2 text-sm text-slate-700">{{ __('Enable this promo code') }}</span>
                    </label>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="mt-8 flex gap-3 border-t border-slate-200 pt-6">
                <button
                    type="submit"
                    class="rounded-lg bg-slate-900 px-6 py-2 text-sm font-semibold text-white transition hover:bg-slate-800"
                >
                    {{ __('Create Promo Code') }}
                </button>
                <a
                    href="{{ route('admin.promo-codes.index') }}"
                    class="rounded-lg border border-slate-300 px-6 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
                >
                    {{ __('Cancel') }}
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
