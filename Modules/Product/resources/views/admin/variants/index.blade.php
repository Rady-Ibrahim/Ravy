@extends('layouts.admin')

@section('title', __('Variants'))
@section('page_title', __('Variants'))
@section('page_subtitle', $product->name)

@section('content')
    <div class="mx-auto max-w-7xl space-y-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.products.index') }}"
                class="text-sm font-medium text-slate-600 hover:text-slate-900">{{ __('Back to products') }}</a>
            <a href="{{ route('admin.products.variants.create', $product) }}"
                class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800">{{ __('Add variant') }}</a>
        </div>

        <div class="admin-card overflow-hidden border-0 p-0 shadow-md shadow-slate-200/50">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[720px] text-left text-sm">
                    <thead
                        class="border-b border-slate-200 bg-slate-50/80 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-4 py-3">{{ __('SKU') }}</th>
                            <th class="px-4 py-3">{{ __('Image') }}</th>
                            <th class="px-4 py-3">{{ __('Attributes') }}</th>
                            <th class="px-4 py-3">{{ __('Price') }}</th>
                            <th class="px-4 py-3">{{ __('Compare at Price') }}</th>
                            <th class="px-4 py-3">{{ __('Stock') }}</th>
                            <th class="px-4 py-3">{{ __('Status') }}</th>
                            <th class="px-4 py-3 text-end">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($variants as $variant)
                            <tr class="bg-white hover:bg-slate-50/50">
                                <td class="px-4 py-3 font-medium text-slate-900">{{ $variant->sku }}</td>
                                <td class="px-4 py-3">
                                    @if ($variant->image)
                                        <img src="{{ asset('public/storage/' . $variant->image) }}"
                                            alt="{{ __('Variant Image') }}"
                                            class="h-10 w-10 rounded-lg object-cover ring-1 ring-slate-200"
                                            title="{{ __('Variant Image') }}">
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="space-y-1">
                                        @foreach ($variant->attributeValues as $attributeValue)
                                            <div class="flex items-center gap-2">
                                                @if ($attributeValue->attribute?->code === 'color' && $attributeValue->attribute?->image)
                                                    <img src="{{ asset('public/storage/' . $attributeValue->attribute->image) }}"
                                                        alt="{{ $attributeValue->value }}"
                                                        class="h-6 w-6 rounded object-cover ring-1 ring-slate-200"
                                                        title="{{ $attributeValue->value }}">
                                                @endif
                                                <div>
                                                    <span
                                                        class="text-xs text-slate-500">{{ $attributeValue->attribute?->name }}:</span>
                                                    <span
                                                        class="text-sm text-slate-700 ml-1">{{ $attributeValue->value }}</span>
                                                    @php $extra = is_array($attributeValue->extra) ? $attributeValue->extra : (is_string($attributeValue->extra) ? json_decode($attributeValue->extra, true) : []); @endphp
                                                    @if (!empty($extra['code']) || !empty($extra['code_from']) || !empty($extra['code_to']))
                                                        <div class="text-xs text-slate-400">Code:
                                                            {{ $extra['code'] ?? '-' }} @if (!empty($extra['code_from']) || !empty($extra['code_to']))
                                                                — Range: {{ $extra['code_from'] ?? '-' }} -
                                                                {{ $extra['code_to'] ?? '-' }}
                                                            @endif
                                                        </div>
                                                    @endif
                                                    @if ($extra['hex'] ?? false)
                                                        <div class="mt-1">
                                                            <span class="inline-block h-4 w-4 rounded"
                                                                style="background-color: {{ $extra['hex'] }}; border:1px solid #e2e8f0"></span>
                                                            <span
                                                                class="text-xs text-slate-500 ml-2">{{ $extra['hex'] }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-slate-600">{{ $variant->price }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $variant->compare_at_price ?? '-' }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $variant->stock }}</td>
                                <td class="px-4 py-3">
                                    <span
                                        class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium ring-1 ring-inset {{ $variant->is_active ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20' : 'bg-slate-100 text-slate-600 ring-slate-500/10' }}">
                                        {{ $variant->is_active ? __('Active') : __('Inactive') }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-end">
                                    <a href="{{ route('admin.products.variants.edit', [$product, $variant]) }}"
                                        class="rounded-lg px-2 py-1 text-xs font-semibold text-amber-700 hover:bg-amber-50">{{ __('Edit') }}</a>
                                    <form action="{{ route('admin.products.variants.destroy', [$product, $variant]) }}"
                                        method="post" class="inline"
                                        onsubmit="return confirm(@json(__('Delete this variant?')));">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="rounded-lg px-2 py-1 text-xs font-semibold text-red-600 hover:bg-red-50">{{ __('Delete') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-slate-500">
                                    {{ __('No variants found.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($variants->hasPages())
                <div class="border-t border-slate-100 px-4 py-3">{{ $variants->withQueryString()->links() }}</div>
            @endif
        </div>
    </div>
@endsection
