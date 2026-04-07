<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label class="mb-1 block text-sm font-medium text-slate-700">{{ __('Value') }}</label>
        <input type="text" name="value" value="{{ old('value', $value?->value) }}" required class="w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-400">
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium text-slate-700">{{ __('Slug (optional)') }}</label>
        <input type="text" name="slug" value="{{ old('slug', $value?->slug) }}" class="w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-400">
    </div>
</div>
<div>
    @php
        $isColorAttribute = in_array(strtolower((string) $attribute->code), ['color', 'colour'], true)
            || str_contains(strtolower((string) $attribute->name), 'color')
            || str_contains(strtolower((string) $attribute->name), 'colour');
        $extraValue = old('extra', $value?->extra['value'] ?? null);
        $normalizedExtraValue = null;
        if (is_string($extraValue) && preg_match('/^#?[0-9a-fA-F]{6}$/', trim($extraValue))) {
            $normalizedExtraValue = '#'.ltrim(trim($extraValue), '#');
        }
    @endphp
    <label class="mb-1 block text-sm font-medium text-slate-700">
        {{ $isColorAttribute ? __('Color hex code') : __('Extra (hex / note)') }}
    </label>
    <div class="flex items-center gap-3">
        <input type="text" name="extra" value="{{ $extraValue }}" placeholder="{{ $isColorAttribute ? '#1f2937' : __('Optional extra value') }}" class="w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-400">
        @if ($isColorAttribute)
            <input id="extra-color-picker" type="color" value="{{ $normalizedExtraValue ?: '#1f2937' }}" class="h-9 w-11 cursor-pointer rounded-md border border-slate-200 bg-white p-1">
            <span id="extra-color-preview" class="inline-block h-9 w-9 rounded-lg ring-1 ring-slate-200" style="background-color: {{ $normalizedExtraValue ?: '#f1f5f9' }};"></span>
        @endif
    </div>
</div>

@if ($isColorAttribute)
    @push('scripts')
        <script>
            (function () {
                var input = document.querySelector('input[name="extra"]');
                var picker = document.getElementById('extra-color-picker');
                var swatch = document.getElementById('extra-color-preview');
                if (!input || !swatch) return;
            function normalizeHex(value) {
                var raw = (value || '').trim();
                if (!raw) return null;
                if (/^#?[0-9a-fA-F]{6}$/.test(raw)) {
                    return '#' + raw.replace(/^#/, '');
                }
                return null;
            }
                input.addEventListener('input', function () {
                var normalized = normalizeHex(input.value);
                swatch.style.backgroundColor = normalized || '#f1f5f9';
                    if (picker && normalized) {
                        picker.value = normalized;
                    }
                });
                if (picker) {
                    picker.addEventListener('input', function () {
                        input.value = picker.value;
                        swatch.style.backgroundColor = picker.value;
                    });
                }
            })();
        </script>
    @endpush
@endif
