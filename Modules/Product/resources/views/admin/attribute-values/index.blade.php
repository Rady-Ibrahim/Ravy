@extends('layouts.admin')

@section('title', __('Attribute values'))
@section('page_title', __('Attribute values'))
@section('page_subtitle', $attribute->name)

@section('content')
    <div class="mx-auto max-w-5xl">
        <div class="mb-4 flex items-center gap-3">
            <a href="{{ route('admin.attributes.index') }}" class="text-sm font-medium text-slate-600 hover:text-slate-900">{{ __('Back to attributes') }}</a>
            <a href="{{ route('admin.attributes.values.create', $attribute) }}" class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800">{{ __('Add value') }}</a>
        </div>
        <div class="admin-card overflow-hidden border-0 p-0 shadow-md shadow-slate-200/50">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[560px] text-left text-sm">
                    <thead class="border-b border-slate-200 bg-slate-50/80 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr><th class="px-4 py-3">{{ __('Value') }}</th><th class="px-4 py-3">{{ __('Slug') }}</th><th class="px-4 py-3">{{ __('Extra') }}</th><th class="px-4 py-3 text-end">{{ __('Actions') }}</th></tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($values as $value)
                            <tr class="bg-white hover:bg-slate-50/50">
                                <td class="px-4 py-3">{{ $value->value }}</td>
                                <td class="px-4 py-3">{{ $value->slug }}</td>
                                <td class="px-4 py-3">
                                    @php
                                        $extra = $value->extra['value'] ?? null;
                                        $normalizedExtra = null;
                                        if (is_string($extra) && preg_match('/^#?[0-9a-fA-F]{6}$/', trim($extra))) {
                                            $normalizedExtra = '#'.ltrim(trim($extra), '#');
                                        }
                                        $isColorAttribute = in_array(strtolower((string) $attribute->code), ['color', 'colour'], true)
                                            || str_contains(strtolower((string) $attribute->name), 'color')
                                            || str_contains(strtolower((string) $attribute->name), 'colour');
                                        $colorNameFallbackMap = [
                                            'black' => '#000000',
                                            'white' => '#ffffff',
                                            'beige' => '#f5f5dc',
                                            'red' => '#ff0000',
                                            'green' => '#008000',
                                            'blue' => '#0000ff',
                                            'gray' => '#808080',
                                            'grey' => '#808080',
                                            'yellow' => '#ffff00',
                                            'orange' => '#ffa500',
                                            'pink' => '#ffc0cb',
                                            'purple' => '#800080',
                                            'brown' => '#a52a2a',
                                        ];
                                        $valueColorFallback = $colorNameFallbackMap[strtolower(trim((string) $value->value))] ?? null;
                                        $displayColor = $normalizedExtra ?: $valueColorFallback;
                                    @endphp
                                    @if ($isColorAttribute && $displayColor)
                                        <span class="inline-flex items-center gap-2">
                                            <span class="inline-block h-5 w-5 rounded-md ring-1 ring-slate-200" style="background-color: {{ $displayColor }}"></span>
                                            <span>{{ $displayColor }}</span>
                                        </span>
                                    @else
                                        {{ $extra ?? '—' }}
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-end">
                                    <a href="{{ route('admin.attributes.values.edit', [$attribute, $value]) }}" class="rounded-lg px-2 py-1 text-xs font-semibold text-amber-700 hover:bg-amber-50">{{ __('Edit') }}</a>
                                    <form action="{{ route('admin.attributes.values.destroy', [$attribute, $value]) }}" method="post" class="inline" onsubmit="return confirm(@json(__('Delete this value?')));">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="rounded-lg px-2 py-1 text-xs font-semibold text-red-600 hover:bg-red-50">{{ __('Delete') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-4 py-8 text-center text-slate-500">{{ __('No values found.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($values->hasPages())<div class="border-t border-slate-100 px-4 py-3">{{ $values->withQueryString()->links() }}</div>@endif
        </div>
    </div>
@endsection
