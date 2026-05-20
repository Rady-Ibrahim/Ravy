<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label class="mb-1 block text-sm font-medium text-slate-700">{{ __('Name') }}</label>
        <input type="text" name="name" value="{{ old('name', $brand?->name) }}" required
            class="w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-400">
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium text-slate-700">{{ __('Slug (optional)') }}</label>
        <input type="text" name="slug" value="{{ old('slug', $brand?->slug) }}"
            class="w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-400">
    </div>
</div>
<div>
    <label class="mb-1 block text-sm font-medium text-slate-700">{{ __('Logo') }}</label>
    <input type="file" name="logo" accept="image/*"
        class="w-full rounded-xl border-slate-200 file:me-3 file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-2 file:text-sm">
    @if (!empty($brand?->logo))
        <img id="preview-logo" src="{{ asset('public/storage/' . $brand->logo) }}"
            class="mt-2 h-10 w-10 rounded-md object-cover ring-1 ring-slate-200" alt="">
    @else
        <img id="preview-logo" class="mt-2 hidden h-10 w-10 rounded-md object-cover ring-1 ring-slate-200"
            alt="">
    @endif
</div>
<label class="inline-flex items-center gap-2 text-sm text-slate-700">
    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $brand?->is_active ?? true))
        class="rounded border-slate-300 text-slate-900 focus:ring-slate-500">
    {{ __('Active') }}
</label>

@push('scripts')
    <script>
        (function() {
            var input = document.querySelector('input[name="logo"]');
            var preview = document.getElementById('preview-logo');
            if (!input || !preview) return;
            input.addEventListener('change', function() {
                var file = input.files && input.files[0];
                if (!file) return;
                var reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            });
        })();
    </script>
@endpush
