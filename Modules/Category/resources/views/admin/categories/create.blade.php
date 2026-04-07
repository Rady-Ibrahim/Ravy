@extends('layouts.admin')

@section('title', __('Create category'))
@section('page_title', __('Create category'))
@section('page_subtitle', __('Add a new catalog category.'))

@section('content')
    <div class="mx-auto max-w-4xl">
        <form action="{{ route('admin.categories.store') }}" method="post" enctype="multipart/form-data" class="admin-card space-y-5">
            @csrf
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">{{ __('Name') }}</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-400">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">{{ __('Slug (optional)') }}</label>
                    <input type="text" name="slug" value="{{ old('slug') }}" class="w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-400">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">{{ __('Parent category') }}</label>
                    <select name="parent_id" class="w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-400">
                        <option value="">{{ __('None') }}</option>
                        @foreach ($parents as $parent)
                            <option value="{{ $parent->id }}" @selected((string) old('parent_id') === (string) $parent->id)>{{ $parent->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">{{ __('Sort order') }}</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" class="w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-400">
                </div>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">{{ __('Description') }}</label>
                <textarea name="description" rows="4" class="w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-400">{{ old('description') }}</textarea>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">{{ __('Image') }}</label>
                    <input type="file" name="image" accept="image/*" class="w-full rounded-xl border-slate-200 file:me-3 file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-2 file:text-sm">
                    <img id="preview-image" class="mt-2 hidden h-10 w-10 rounded-md object-cover ring-1 ring-slate-200" alt="">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">{{ __('Banner') }}</label>
                    <input type="file" name="banner" accept="image/*" class="w-full rounded-xl border-slate-200 file:me-3 file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-2 file:text-sm">
                    <img id="preview-banner" class="mt-2 hidden h-10 w-16 rounded-md object-cover ring-1 ring-slate-200" alt="">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">{{ __('Icon') }}</label>
                    <input type="file" name="icon" accept="image/*" class="w-full rounded-xl border-slate-200 file:me-3 file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-2 file:text-sm">
                    <img id="preview-icon" class="mt-2 hidden h-8 w-8 rounded-md object-cover ring-1 ring-slate-200" alt="">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">{{ __('Meta title') }}</label>
                    <input type="text" name="meta_title" value="{{ old('meta_title') }}" class="w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-400">
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-slate-700">{{ __('Meta description') }}</label>
                    <textarea name="meta_description" rows="3" class="w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-400">{{ old('meta_description') }}</textarea>
                </div>
            </div>
            <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', true)) class="rounded border-slate-300 text-slate-900 focus:ring-slate-500">
                {{ __('Active') }}
            </label>
            <div class="flex items-center gap-3">
                <button type="submit" class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">{{ __('Create') }}</button>
                <a href="{{ route('admin.categories.index') }}" class="text-sm font-medium text-slate-600 hover:text-slate-900">{{ __('Cancel') }}</a>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        function wireImagePreview(inputName, previewId) {
            var input = document.querySelector('input[name="' + inputName + '"]');
            var preview = document.getElementById(previewId);
            if (!input || !preview) return;
            input.addEventListener('change', function () {
                var file = input.files && input.files[0];
                if (!file) return;
                var reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            });
        }
        wireImagePreview('image', 'preview-image');
        wireImagePreview('banner', 'preview-banner');
        wireImagePreview('icon', 'preview-icon');
    </script>
@endpush
