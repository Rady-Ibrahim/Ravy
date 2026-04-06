@extends('layouts.admin')

@section('title', __('Add permission'))

@section('page_title', __('Add permission'))
@section('page_subtitle', __('Use dot notation, e.g. module.action'))

@section('content')
    <div class="mx-auto max-w-xl">
        <form action="{{ route('admin.permissions.store') }}" method="post" class="admin-card border-0 shadow-md shadow-slate-200/50 space-y-5">
            @csrf

            <div>
                <label for="name" class="block text-sm font-medium text-slate-700">{{ __('Permission name') }}</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 font-mono text-sm shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500" placeholder="admin.reports.view">
                <p class="mt-1 text-xs text-slate-500">{{ __('Guard') }}: <strong>web</strong> ({{ __('fixed for admin panel') }})</p>
                @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="flex gap-3 border-t border-slate-100 pt-5">
                <button type="submit" class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">{{ __('Save') }}</button>
                <a href="{{ route('admin.permissions.index') }}" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">{{ __('Cancel') }}</a>
            </div>
        </form>
    </div>
@endsection
