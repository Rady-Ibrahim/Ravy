@php
    $selectedValues = collect(old('attribute_value_ids', $variant?->attributeValues?->pluck('id')->all() ?? []))
        ->map(fn($id) => (int) $id)
        ->all();
@endphp

<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label class="mb-1 block text-sm font-medium text-slate-700">{{ __('SKU') }}</label>
        <input type="text" name="sku" value="{{ old('sku', $variant?->sku) }}" required
            class="w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-400">
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium text-slate-700">{{ __('Stock') }}</label>
        <input type="number" name="stock" value="{{ old('stock', $variant?->stock ?? 0) }}" min="0" required
            class="w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-400">
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium text-slate-700">{{ __('Price') }}</label>
        <input type="number" step="0.01" name="price" value="{{ old('price', $variant?->price) }}" min="0"
            required class="w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-400">
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium text-slate-700">{{ __('Compare at price') }}</label>
        <input type="number" step="0.01" name="compare_at_price"
            value="{{ old('compare_at_price', $variant?->compare_at_price) }}" min="0"
            class="w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-400">
    </div>
</div>

<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label class="mb-1 block text-sm font-medium text-slate-700">{{ __('Variant Image') }}</label>
        <input type="file" name="image" accept="image/*"
            class="w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-400">
        @error('image')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
        @if ($variant?->image)
            <div class="mt-2">
                <img src="{{ asset('public/storage/' . $variant->image) }}" alt="{{ __('Variant Image') }}"
                    class="h-20 w-20 rounded-lg object-cover">
                <p class="mt-1 text-xs text-slate-500">{{ __('Current image') }}</p>
            </div>
        @endif
    </div>
</div>

<div>
    <label
        class="mb-2 block text-sm font-medium text-slate-700">{{ __('Variant attributes (e.g. color, size)') }}</label>
    <div class="max-h-72 space-y-3 overflow-y-auto rounded-xl border border-slate-200 p-3">
        @php
            $grouped = $attributeValues->groupBy(fn($item) => $item->attribute?->name ?? 'Attribute');
        @endphp
        @foreach ($grouped as $groupName => $values)
            <div>
                <p class="mb-1 text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $groupName }}</p>
                <div class="flex flex-wrap gap-2">
                    @foreach ($values as $value)
                        @php
                            $extra = is_array($value->extra)
                                ? $value->extra
                                : (is_string($value->extra)
                                    ? json_decode($value->extra, true)
                                    : []);
                        @endphp
                        <label
                            class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-2 py-1 text-sm text-slate-700">
                            <input type="checkbox" name="attribute_value_ids[]" value="{{ $value->id }}"
                                @checked(in_array((int) $value->id, $selectedValues, true))
                                class="rounded border-slate-300 text-slate-900 focus:ring-slate-500">
                            <span>{{ $value->value }}</span>
                            @if (!empty($extra['code']) || !empty($extra['code_from']) || !empty($extra['code_to']))
                                <span class="ml-2 text-xs text-slate-500">Code: {{ $extra['code'] ?? '-' }}</span>
                                @if (!empty($extra['code_from']) || !empty($extra['code_to']))
                                    <span class="ml-1 text-xs text-slate-400">({{ $extra['code_from'] ?? '-' }} -
                                        {{ $extra['code_to'] ?? '-' }})</span>
                                @endif
                            @endif
                            @if ($extra['hex'] ?? false)
                                <span class="ml-2 inline-flex items-center">
                                    <span class="inline-block h-4 w-4 rounded"
                                        style="background-color: {{ $extra['hex'] }}; border:1px solid #e2e8f0"></span>
                                    <span class="ml-2 text-xs text-slate-500">{{ $extra['hex'] }}</span>
                                </span>
                            @endif
                        </label>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
    @error('attribute_value_ids')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

<label class="inline-flex items-center gap-2 text-sm text-slate-700">
    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $variant?->is_active ?? true))
        class="rounded border-slate-300 text-slate-900 focus:ring-slate-500">
    {{ __('Active') }}
</label>
