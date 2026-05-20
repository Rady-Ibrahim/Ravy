@extends('layouts.admin')

@section('title', __('Edit Product'))
@section('page_title', __('Edit Product'))
@section('page_subtitle', __('Update catalog product details.'))

@section('content')
    <div class="mx-auto max-w-6xl">
        <form action="{{ route('admin.products.update', $product) }}" method="post" enctype="multipart/form-data"
            class="space-y-8">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">

                <!-- Left Side - Main Content -->
                <div class="lg:col-span-2 space-y-10">

                    <!-- General Information -->
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                        <div class="px-6 py-5 border-b border-slate-100 bg-slate-50 text-center">
                            <h3 class="font-semibold text-xl text-slate-800">{{ __('General Information') }}</h3>
                        </div>
                        <div class="p-6 space-y-6">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div>
                                    <label class="block mb-1.5 text-sm font-semibold text-slate-700">{{ __('Name') }}
                                        <span class="text-red-500">*</span></label>
                                    <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                                        class="w-full rounded-xl border border-slate-300 focus:border-slate-500 focus:ring focus:ring-slate-200 px-4 py-3 transition-all">
                                </div>
                                <div>
                                    <label class="block mb-1.5 text-sm font-semibold text-slate-700">{{ __('Slug') }}
                                        <span class="text-slate-400">(optional)</span></label>
                                    <input type="text" name="slug" value="{{ old('slug', $product->slug) }}"
                                        class="w-full rounded-xl border border-slate-300 focus:border-slate-500 focus:ring focus:ring-slate-200 px-4 py-3 transition-all">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div>
                                    <label
                                        class="block mb-1.5 text-sm font-semibold text-slate-700">{{ __('Brand') }}</label>
                                    <select name="brand_id"
                                        class="w-full rounded-xl border border-slate-300 focus:border-slate-500 focus:ring focus:ring-slate-200 px-4 py-3 transition-all">
                                        <option value="">{{ __('None') }}</option>
                                        @foreach ($brands as $brand)
                                            <option value="{{ $brand->id }}" @selected(old('brand_id', $product->brand_id) == $brand->id)>
                                                {{ $brand->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label
                                        class="block mb-1.5 text-sm font-semibold text-slate-700">{{ __('Primary Category') }}</label>
                                    <select name="primary_category_id"
                                        class="w-full rounded-xl border border-slate-300 focus:border-slate-500 focus:ring focus:ring-slate-200 px-4 py-3 transition-all">
                                        <option value="">{{ __('None') }}</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}" @selected(old('primary_category_id', $product->primary_category_id) == $category->id)>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label
                                    class="block mb-1.5 text-sm font-semibold text-slate-700">{{ __('Additional Categories') }}</label>
                                <select name="category_ids[]" multiple
                                    class="w-full rounded-xl border border-slate-300 focus:border-slate-500 focus:ring focus:ring-slate-200 px-4 py-3 transition-all h-32">
                                    @php $selected = collect(old('category_ids', $product->categories->pluck('id')->all())); @endphp
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" @selected($selected->contains($category->id))>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label
                                    class="block mb-1.5 text-sm font-semibold text-slate-700">{{ __('Description') }}</label>
                                <textarea name="description" rows="5"
                                    class="w-full rounded-xl border border-slate-300 focus:border-slate-500 focus:ring focus:ring-slate-200 px-4 py-3 transition-all">{{ old('description', $product->description) }}</textarea>
                            </div>

                            <div>
                                <label
                                    class="block mb-1.5 text-sm font-semibold text-slate-700">{{ __('Short Description') }}</label>
                                <textarea name="short_description" rows="3"
                                    class="w-full rounded-xl border border-slate-300 focus:border-slate-500 focus:ring focus:ring-slate-200 px-4 py-3 transition-all">{{ old('short_description', $product->short_description) }}</textarea>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Right Sidebar -->
                <div class="lg:col-span-1 space-y-8">

                    <!-- Media -->
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                        <div class="px-6 py-5 border-b border-slate-100 bg-slate-50 text-center">
                            <h3 class="font-semibold text-xl text-slate-800">{{ __('Media') }}</h3>
                        </div>
                        <div class="p-6">
                            <div>
                                <label
                                    class="block mb-2 text-sm font-medium text-slate-700">{{ __('Cover Image') }}</label>
                                <input type="file" name="cover_image" accept="image/*" id="cover-image-input"
                                    class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-slate-100 file:text-slate-700 cursor-pointer">

                                @php
                                    $coverImage =
                                        $product->images->firstWhere('is_primary', true) ?? $product->images->first();
                                @endphp
                                <img id="preview-cover-image"
                                    @if ($coverImage) src="{{ asset('public/storage/' . $coverImage->path) }}"
                                 @else
                                    class="hidden" @endif
                                    class="mt-3 h-12 w-12 rounded-xl object-cover border border-slate-200 shadow-sm"
                                    alt="Preview">
                            </div>
                        </div>
                    </div>

                    <!-- Status & Settings -->
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                        <div class="px-6 py-5 border-b border-slate-100 bg-slate-50 text-center">
                            <h3 class="font-semibold text-xl text-slate-800">{{ __('Status & Settings') }}</h3>
                        </div>
                        <div class="p-6 space-y-6">
                            <div class="space-y-5">
                                <label class="inline-flex items-center gap-3 cursor-pointer">
                                    <input type="hidden" name="is_active" value="0">
                                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active))
                                        class="w-5 h-5 rounded border-slate-300 text-slate-900 focus:ring-slate-500">
                                    <span class="font-semibold text-slate-700">{{ __('Active') }}</span>
                                </label>

                                <label class="inline-flex items-center gap-3 cursor-pointer">
                                    <input type="hidden" name="is_featured" value="0">
                                    <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $product->is_featured))
                                        class="w-5 h-5 rounded border-slate-300 text-slate-900 focus:ring-slate-500">
                                    <span class="font-semibold text-slate-700">{{ __('Featured') }}</span>
                                </label>

                                <label class="inline-flex items-center gap-3 cursor-pointer">
                                    <input type="hidden" name="is_new" value="0">
                                    <input type="checkbox" name="is_new" value="1" @checked(old('is_new', $product->is_new))
                                        class="w-5 h-5 rounded border-slate-300 text-slate-900 focus:ring-slate-500">
                                    <span class="font-semibold text-slate-700">{{ __('New') }}</span>
                                </label>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div>
                                    <label
                                        class="block mb-1.5 text-sm font-semibold text-slate-700">{{ __('Sort Order') }}</label>
                                    <input type="number" name="sort_order"
                                        value="{{ old('sort_order', $product->sort_order) }}"
                                        class="w-full rounded-xl border border-slate-300 focus:border-slate-500 focus:ring focus:ring-slate-200 px-4 py-3 transition-all">
                                </div>
                                <div>
                                    <label
                                        class="block mb-1.5 text-sm font-semibold text-slate-700">{{ __('Featured Until') }}</label>
                                    <input type="datetime-local" name="featured_until"
                                        value="{{ old('featured_until', optional($product->featured_until)->format('Y-m-d\TH:i')) }}"
                                        class="w-full rounded-xl border border-slate-300 focus:border-slate-500 focus:ring focus:ring-slate-200 px-4 py-3 transition-all">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="space-y-3 pt-2">
                        <button type="submit"
                            class="w-full bg-slate-900 hover:bg-slate-800 transition-all text-white font-semibold py-4 text-lg rounded-2xl shadow-sm">
                            {{ __('Save Changes') }}
                        </button>

                        <a href="{{ route('admin.products.index') }}"
                            class="block w-full text-center py-3 text-sm font-medium text-slate-500 hover:text-slate-700 transition-colors">
                            {{ __('Cancel') }}
                        </a>
                    </div>

                </div>

            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        function wireImagePreview(inputId, previewId) {
            const input = document.getElementById(inputId);
            const preview = document.getElementById(previewId);

            if (!input || !preview) return;

            input.addEventListener('change', function() {
                const file = this.files[0];
                if (!file) return;

                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            });
        }

        wireImagePreview('cover-image-input', 'preview-cover-image');
    </script>
@endpush
