@extends('layouts.admin')

@section('title', __('Permissions'))

@section('page_title', __('Permissions'))
@section('page_subtitle', __('Registered permission names for the web guard (admin UI).'))

@section('content')
    <div class="mx-auto max-w-7xl">
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            @can('admin.permissions.create')
                <a href="{{ route('admin.permissions.create') }}" class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800">
                    {{ __('Add permission') }}
                </a>
            @endcan
        </div>

        <div class="admin-card overflow-hidden border-0 p-0 shadow-md shadow-slate-200/50">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[480px] text-left text-sm">
                    <thead class="border-b border-slate-200 bg-slate-50/80 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-4 py-3">{{ __('Name') }}</th>
                            <th class="px-4 py-3">{{ __('Guard') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($permissions as $permission)
                            <tr class="bg-white hover:bg-slate-50/50">
                                <td class="px-4 py-3 font-mono text-xs text-slate-900">{{ $permission->name }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $permission->guard_name }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-4 py-8 text-center text-slate-500">{{ __('No permissions found.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($permissions->hasPages())
                <div class="border-t border-slate-100 px-4 py-3">
                    {{ $permissions->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
