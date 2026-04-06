@extends('layouts.admin')

@section('title', __('Edit role'))

@section('page_title', __('Edit role'))
@section('page_subtitle', $role->name)

@section('content')
    <div class="mx-auto max-w-2xl">
        <form action="{{ route('admin.roles.update', $role) }}" method="post" class="admin-card border-0 shadow-md shadow-slate-200/50 space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="block text-sm font-medium text-slate-700">{{ __('Role name') }}</label>
                @if ($role->name === 'super-admin')
                    <input type="hidden" name="name" value="super-admin">
                    <p class="mt-1 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-800">super-admin</p>
                    <p class="mt-1 text-xs text-slate-500">{{ __('This system role cannot be renamed.') }}</p>
                @else
                    <input type="text" name="name" id="name" value="{{ old('name', $role->name) }}" required class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                @endif
                @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <span class="block text-sm font-medium text-slate-700">{{ __('Permissions') }}</span>
                @php
                    $selected = old('permissions', $role->permissions->pluck('name')->all());
                @endphp
                <div class="mt-2 max-h-64 space-y-2 overflow-y-auto rounded-xl border border-slate-200 p-3">
                    @foreach ($permissions as $permission)
                        <label class="flex items-center gap-2 text-sm text-slate-700">
                            <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" @checked(in_array($permission->name, $selected, true)) class="rounded border-slate-300 text-amber-600 focus:ring-amber-500">
                            <code class="rounded bg-slate-100 px-1.5 py-0.5 text-xs">{{ $permission->name }}</code>
                        </label>
                    @endforeach
                </div>
                @error('permissions')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="flex gap-3 border-t border-slate-100 pt-5">
                <button type="submit" class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">{{ __('Update') }}</button>
                <a href="{{ route('admin.roles.index') }}" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">{{ __('Cancel') }}</a>
            </div>
        </form>
    </div>
@endsection
