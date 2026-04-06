@extends('layouts.admin')

@section('title', __('Roles'))

@section('page_title', __('Roles'))
@section('page_subtitle', __('Map permissions to roles for the admin panel.'))

@section('content')
    <div class="mx-auto max-w-7xl">
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex flex-wrap gap-2">
                @can('admin.roles.create')
                    <a href="{{ route('admin.roles.create') }}" class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800">
                        {{ __('Add role') }}
                    </a>
                @endcan
                @can('admin.matrix.manage')
                    <a href="{{ route('admin.roles.matrix') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-800 shadow-sm transition hover:border-amber-200 hover:bg-amber-50/50">
                        {{ __('Permission matrix') }}
                    </a>
                @endcan
            </div>
        </div>

        <div class="admin-card overflow-hidden border-0 p-0 shadow-md shadow-slate-200/50">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[640px] text-left text-sm">
                    <thead class="border-b border-slate-200 bg-slate-50/80 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-4 py-3">{{ __('Name') }}</th>
                            <th class="px-4 py-3">{{ __('Users') }}</th>
                            <th class="px-4 py-3">{{ __('Permissions') }}</th>
                            <th class="px-4 py-3 text-end">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($roles as $role)
                            <tr class="bg-white hover:bg-slate-50/50">
                                <td class="px-4 py-3 font-medium text-slate-900">{{ $role->name }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $role->users_count }}</td>
                                <td class="px-4 py-3 text-slate-600">
                                    <span class="line-clamp-2" title="{{ $role->permissions->pluck('name')->join(', ') }}">
                                        {{ $role->permissions->pluck('name')->take(4)->join(', ') }}{{ $role->permissions->count() > 4 ? '…' : '' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-end">
                                    <div class="flex justify-end gap-2">
                                        @can('admin.roles.edit')
                                            <a href="{{ route('admin.roles.edit', $role) }}" class="rounded-lg px-2 py-1 text-xs font-semibold text-amber-700 hover:bg-amber-50">{{ __('Edit') }}</a>
                                        @endcan
                                        @can('admin.roles.delete')
                                            @if ($role->name !== 'super-admin' && $role->users_count === 0)
                                                <form action="{{ route('admin.roles.destroy', $role) }}" method="post" class="inline" onsubmit="return confirm(@json(__('Delete this role?')));">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="rounded-lg px-2 py-1 text-xs font-semibold text-red-600 hover:bg-red-50">{{ __('Delete') }}</button>
                                                </form>
                                            @endif
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
