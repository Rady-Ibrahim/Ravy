@extends('layouts.admin')

@section('title', __('Create product'))
@section('page_title', __('Create product'))
@section('page_subtitle', __('Add a new catalog product.'))

@section('content')
    <div class="mx-auto max-w-5xl">
        <form action="{{ route('admin.products.store') }}" method="post" enctype="multipart/form-data" class="admin-card space-y-5">
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
                    <label class="mb-1 block text-sm font-medium text-slate-700">{{ __('Brand') }}</label>
                    <select name="brand_id" class="w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-400">
                        <option value="">{{ __('None') }}</option>
                        @foreach ($brands as $brand)
                            <option value="{{ $brand->id }}" @selected((string) old('brand_id') === (string) $brand->id)>{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">{{ __('Primary category') }}</label>
                    <select name="primary_category_id" class="w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-400">
                        <option value="">{{ __('None') }}</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected((string) old('primary_category_id') === (string) $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-slate-700">{{ __('Categories') }}</label>
                    <select name="category_ids[]" multiple class="w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-400">
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(collect(old('category_ids', []))->contains($category->id))>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">{{ __('Description') }}</label>
                <textarea name="description" rows="4" class="w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-400">{{ old('description') }}</textarea>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">{{ __('Cover image') }}</label>
                <input type="file" name="cover_image" accept="image/*" class="w-full rounded-xl border-slate-200 file:me-3 file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-2 file:text-sm">
                <img id="preview-cover-image" class="mt-2 hidden h-10 w-10 rounded-md object-cover ring-1 ring-slate-200" alt="">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">{{ __('Short description') }}</label>
                <textarea name="short_description" rows="3" class="w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-400">{{ old('short_description') }}</textarea>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">{{ __('Featured until') }}</label>
                    <input type="datetime-local" name="featured_until" value="{{ old('featured_until') }}" class="w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-400">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">{{ __('Sort order') }}</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" class="w-full rounded-xl border-slate-200 focus:border-slate-400 focus:ring-slate-400">
                </div>
            </div>
            <div class="grid gap-4 sm:grid-cols-3">
                <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', true)) class="rounded border-slate-300 text-slate-900 focus:ring-slate-500">
                    {{ __('Active') }}
                </label>
                <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                    <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', false)) class="rounded border-slate-300 text-slate-900 focus:ring-slate-500">
                    {{ __('Featured') }}
                </label>
                <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                    <input type="checkbox" name="is_new" value="1" @checked(old('is_new', true)) class="rounded border-slate-300 text-slate-900 focus:ring-slate-500">
                    {{ __('New') }}
                </label>
            </div>
            <div class="flex items-center gap-3">
                <button type="submit" class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">{{ __('Create') }}</button>
                <a href="{{ route('admin.products.index') }}" class="text-sm font-medium text-slate-600 hover:text-slate-900">{{ __('Cancel') }}</a>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            var input = document.querySelector('input[name="cover_image"]');
            var preview = document.getElementById('preview-cover-image');
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
        })();
    </script>
@endpush
