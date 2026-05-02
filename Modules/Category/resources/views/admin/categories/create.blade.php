@extends('layouts.admin')

@section('title', __('Create Category'))
@section('page_title', __('Create Category'))
@section('page_subtitle', __('Add a new catalog category.'))

@section('content')
<div class="mx-auto max-w-6xl">
    <form action="{{ route('admin.categories.store') }}" method="post" enctype="multipart/form-data" class="space-y-8">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10"> <!-- زيادة المسافة بين الأقسام -->

            <!-- Left Side - Main Content -->
            <div class="lg:col-span-2 space-y-10"> <!-- زيادة المسافة بين المربعات -->

                <!-- General Information -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 bg-slate-50 text-center"> <!-- محاذاة في المنتصف -->
                        <h3 class="font-semibold text-xl text-slate-800">{{ __('General Information') }}</h3>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label class="block mb-1.5 text-sm font-semibold text-slate-700">{{ __('Name') }} <span class="text-red-500">*</span></label>
                                <input type="text" name="name" value="{{ old('name') }}" required 
                                    class="w-full rounded-xl border border-slate-300 focus:border-slate-500 focus:ring focus:ring-slate-200 px-4 py-3 transition-all">
                            </div>
                            <div>
                                <label class="block mb-1.5 text-sm font-semibold text-slate-700">{{ __('Slug') }} <span class="text-slate-400">(optional)</span></label>
                                <input type="text" name="slug" value="{{ old('slug') }}" 
                                    class="w-full rounded-xl border border-slate-300 focus:border-slate-500 focus:ring focus:ring-slate-200 px-4 py-3 transition-all">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label class="block mb-1.5 text-sm font-semibold text-slate-700">{{ __('Parent Category') }}</label>
                                <select name="parent_id" 
                                    class="w-full rounded-xl border border-slate-300 focus:border-slate-500 focus:ring focus:ring-slate-200 px-4 py-3 transition-all">
                                    <option value="">{{ __('None') }}</option>
                                    @foreach ($parents as $parent)
                                        <option value="{{ $parent->id }}" @selected(old('parent_id') == $parent->id)>
                                            {{ $parent->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block mb-1.5 text-sm font-semibold text-slate-700">{{ __('Sort Order') }}</label>
                                <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" 
                                    class="w-full rounded-xl border border-slate-300 focus:border-slate-500 focus:ring focus:ring-slate-200 px-4 py-3 transition-all">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label class="block mb-1.5 text-sm font-semibold text-slate-700">{{ __('Menu order') }}</label>
                                <input type="number" name="menu_order" value="{{ old('menu_order', 0) }}"
                                    class="w-full rounded-xl border border-slate-300 focus:border-slate-500 focus:ring focus:ring-slate-200 px-4 py-3 transition-all">
                                <p class="mt-1 text-xs text-slate-500">{{ __('Lower numbers appear first in the storefront sidebar.') }}</p>
                            </div>
                            <div class="flex items-end pb-1">
                                <label class="inline-flex items-center gap-3 cursor-pointer">
                                    <input type="hidden" name="show_in_sidebar" value="0">
                                    <input type="checkbox" name="show_in_sidebar" value="1" @checked(old('show_in_sidebar', true))
                                        class="w-5 h-5 rounded border-slate-300 text-slate-900 focus:ring-slate-500">
                                    <span class="font-semibold text-slate-700">{{ __('Show in sidebar') }}</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block mb-1.5 text-sm font-semibold text-slate-700">{{ __('Description') }}</label>
                            <textarea name="description" rows="4" 
                                class="w-full rounded-xl border border-slate-300 focus:border-slate-500 focus:ring focus:ring-slate-200 px-4 py-3 transition-all">{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- SEO Section -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 bg-slate-50 text-center"> <!-- محاذاة في المنتصف -->
                        <h3 class="font-semibold text-xl text-slate-800">{{ __('Search Engine Optimization') }}</h3>
                    </div>
                    <div class="p-6 space-y-6">
                        <div>
                            <label class="block mb-1.5 text-sm font-semibold text-slate-700">{{ __('Meta Title') }}</label>
                            <input type="text" name="meta_title" value="{{ old('meta_title') }}" 
                                class="w-full rounded-xl border border-slate-300 focus:border-slate-500 focus:ring focus:ring-slate-200 px-4 py-3 transition-all">
                        </div>
                        <div>
                            <label class="block mb-1.5 text-sm font-semibold text-slate-700">{{ __('Meta Description') }}</label>
                            <textarea name="meta_description" rows="3" 
                                class="w-full rounded-xl border border-slate-300 focus:border-slate-500 focus:ring focus:ring-slate-200 px-4 py-3 transition-all">{{ old('meta_description') }}</textarea>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right Sidebar -->
            <div class="lg:col-span-1 space-y-8">

                <!-- Media Assets -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 bg-slate-50 text-center"> <!-- محاذاة في المنتصف -->
                        <h3 class="font-semibold text-xl text-slate-800">{{ __('Media Assets') }}</h3>
                    </div>
                    <div class="p-6 space-y-7">

                        <!-- Image -->
                        <div>
                            <label class="block mb-2 text-sm font-medium text-slate-700">{{ __('Image') }}</label>
                            <input type="file" name="image" accept="image/*" id="image-input"
                                class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-slate-100 file:text-slate-700 cursor-pointer">
                            <img id="preview-image" class="mt-3 hidden h-12 w-12 rounded-xl object-cover border border-slate-200 shadow-sm" alt="Preview"> <!-- ربع الحجم -->
                        </div>

                        <!-- Banner -->
                        <div>
                            <label class="block mb-2 text-sm font-medium text-slate-700">{{ __('Banner') }}</label>
                            <input type="file" name="banner" accept="image/*" id="banner-input"
                                class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-slate-100 file:text-slate-700 cursor-pointer">
                            <img id="preview-banner" class="mt-3 hidden h-12 w-20 rounded-xl object-cover border border-slate-200 shadow-sm" alt="Preview"> <!-- ربع الحجم -->
                        </div>

                        <!-- Icon -->
                        <div>
                            <label class="block mb-2 text-sm font-medium text-slate-700">{{ __('Icon') }}</label>
                            <input type="file" name="icon" accept="image/*" id="icon-input"
                                class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-slate-100 file:text-slate-700 cursor-pointer">
                            <img id="preview-icon" class="mt-3 hidden h-12 w-12 rounded-xl object-cover border border-slate-200 shadow-sm" alt="Preview"> <!-- ربع الحجم -->
                        </div>

                    </div>
                </div>

                <!-- Status -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                    <label class="inline-flex items-center gap-3 cursor-pointer">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', true)) 
                            class="w-5 h-5 rounded border-slate-300 text-slate-900 focus:ring-slate-500">
                        <span class="font-semibold text-slate-700">{{ __('Active') }}</span>
                    </label>
                    <p class="mt-2 text-xs text-slate-500 pl-8">{{ __('Visible on the storefront.') }}</p>
                </div>

                <!-- Actions -->
                <div class="space-y-3 pt-2">
                    <button type="submit" 
                        class="w-full bg-slate-900 hover:bg-slate-800 transition-all text-white font-semibold py-4 text-lg rounded-2xl shadow-sm">
                        {{ __('Create Category') }}
                    </button>
                    
                    <a href="{{ route('admin.categories.index') }}" 
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

    // Initialize all previews
    wireImagePreview('image-input', 'preview-image');
    wireImagePreview('banner-input', 'preview-banner');
    wireImagePreview('icon-input', 'preview-icon');
</script>
@endpush