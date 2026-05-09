@extends('layouts.admin')

@section('title', __('Add Size'))

@section('page_title', __('Add Size'))
@section('page_subtitle', __('Create new product size.'))

@section('content')
    <div class="mx-auto max-w-4xl">
        <div class="admin-card">
            <form action="{{ route('admin.sizes.store') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="p-6">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label for="name" class="block text-sm font-medium text-slate-700">{{ __('Name') }}</label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" 
                                   class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="code" class="block text-sm font-medium text-slate-700">{{ __('Code') }}</label>
                            <input type="text" id="code" name="code" value="{{ old('code') }}" 
                                   class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                            @error('code')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="image" class="block text-sm font-medium text-slate-700">{{ __('Image') }}</label>
                            <input type="file" id="image" name="image" accept="image/*"
                                   class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            @error('image')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-slate-500">{{ __('Optional: Upload an image for this size.') }}</p>
                        </div>

                        <div>
                            <label for="sort_order" class="block text-sm font-medium text-slate-700">{{ __('Sort Order') }}</label>
                            <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order') }}" min="0"
                                   class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            @error('sort_order')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-end gap-3 border-t border-slate-200 pt-6">
                        <a href="{{ route('admin.sizes.index') }}" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50">
                            {{ __('Cancel') }}
                        </a>
                        <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">
                            {{ __('Create Size') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
