@extends('layouts.admin')

@section('title', __('Add role'))

@section('page_title', __('Add role'))
@section('page_subtitle', __('Choose a unique name and attach permissions.'))

@section('content')
    <div class="mx-auto max-w-2xl">
        <form action="{{ route('admin.roles.store') }}" method="post" class="admin-card border-0 shadow-md shadow-slate-200/50 space-y-5">
            @csrf

            <div>
                <label for="name" class="block text-sm font-medium text-slate-700">{{ __('Role name') }}</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500" placeholder="e.g. content-manager">
                @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <span class="block text-sm font-medium text-slate-700">{{ __('Permissions') }}</span>
                <div class="mt-2 max-h-64 space-y-2 overflow-y-auto rounded-xl border border-slate-200 p-3">
                    @foreach ($permissions as $permission)
                        <label class="flex items-center gap-2 text-sm text-slate-700">
                            <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" @checked(in_array($permission->name, old('permissions', []), true)) class="rounded border-slate-300 text-amber-600 focus:ring-amber-500">
                            <code class="rounded bg-slate-100 px-1.5 py-0.5 text-xs">{{ $permission->name }}</code>
                        </label>
                    @endforeach
                </div>
                @error('permissions')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="flex gap-3 border-t border-slate-100 pt-5">
                <button type="submit" class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">{{ __('Save') }}</button>
                <a href="{{ route('admin.roles.index') }}" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">{{ __('Cancel') }}</a>
            </div>
        </form>
    </div>
@endsection
