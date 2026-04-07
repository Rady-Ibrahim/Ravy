<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label class="mb-1 block text-sm font-medium text-slate-700">{{ __('Category') }}</label>
        <select name="category_id" class="w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-400" required>
            <option value="">{{ __('Select category') }}</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected((string) old('category_id', $attribute?->category_id) === (string) $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium text-slate-700">{{ __('Name') }}</label>
        <input type="text" name="name" value="{{ old('name', $attribute?->name) }}" required class="w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-400">
    </div>
    <div>
        @php $initialCode = old('code', $attribute?->code); @endphp
        <label class="mb-1 block text-sm font-medium text-slate-700">{{ __('Code (auto)') }}</label>
        <input type="hidden" name="code" value="{{ $initialCode }}">
        <input type="text" id="code-preview" value="{{ $initialCode }}" readonly class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-600 focus:border-slate-300 focus:ring-slate-300">
        <p class="mt-1 text-xs text-slate-500">{{ __('Generated automatically from name.') }}</p>
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium text-slate-700">{{ __('Type') }}</label>
        <input type="text" name="type" value="{{ old('type', $attribute?->type ?? 'select') }}" class="w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-400">
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium text-slate-700">{{ __('Sort order') }}</label>
        <input type="number" name="sort_order" value="{{ old('sort_order', $attribute?->sort_order ?? 0) }}" class="w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-400">
    </div>
</div>
<label class="inline-flex items-center gap-2 text-sm text-slate-700">
    <input type="checkbox" name="is_filterable" value="1" @checked(old('is_filterable', $attribute?->is_filterable ?? true)) class="rounded border-slate-300 text-slate-900 focus:ring-slate-500">
    {{ __('Filterable') }}
</label>

@push('scripts')
    <script>
        (function () {
            var nameInput = document.querySelector('input[name="name"]');
            var hiddenCodeInput = document.querySelector('input[name="code"]');
            var codePreview = document.getElementById('code-preview');
            if (!nameInput || !hiddenCodeInput || !codePreview) return;
            function slugify(value) {
                return (value || '')
                    .toString()
                    .trim()
                    .toLowerCase()
                    .replace(/[^a-z0-9]+/g, '_')
                    .replace(/^_+|_+$/g, '');
            }
            function updateCode() {
                var code = slugify(nameInput.value);
                hiddenCodeInput.value = code;
                codePreview.value = code;
            }
            nameInput.addEventListener('input', updateCode);
            if (!hiddenCodeInput.value) updateCode();
        })();
    </script>
@endpush
