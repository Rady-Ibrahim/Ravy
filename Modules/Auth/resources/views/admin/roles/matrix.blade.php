@extends('layouts.admin')

@section('title', __('Permission matrix'))

@section('page_title', __('Permission matrix'))

@section('content')
    @php
        $tabKeys = collect($modules)->pluck('key')->filter()->values()->all();
        $firstTab = $tabKeys[0] ?? 'all';
    @endphp

    <div id="matrix-page-root" class="mx-auto max-w-[100rem] space-y-6">
        @if ($catalogPermissions)
            <div class="flex flex-wrap gap-2" role="tablist" aria-label="{{ __('Matrix page sections') }}">
                <button type="button" data-page-mode="matrix" data-active="{{ $catalogPanelOpen ? 'false' : 'true' }}"
                    class="rounded-xl border px-3 py-2 text-sm font-semibold transition data-[active=true]:border-amber-500 data-[active=true]:bg-amber-50 data-[active=true]:text-amber-950 data-[active=false]:border-slate-200 data-[active=false]:bg-white data-[active=false]:text-slate-600 data-[active=false]:hover:border-slate-300">
                    {{ __('Permission matrix') }}
                </button>
                <button type="button" data-page-mode="catalog" data-active="{{ $catalogPanelOpen ? 'true' : 'false' }}"
                    class="rounded-xl border px-3 py-2 text-sm font-semibold transition data-[active=true]:border-amber-500 data-[active=true]:bg-amber-50 data-[active=true]:text-amber-950 data-[active=false]:border-slate-200 data-[active=false]:bg-white data-[active=false]:text-slate-600 data-[active=false]:hover:border-slate-300">
                    {{ __('Permission catalog') }}
                </button>
            </div>
        @endif

        <div id="matrix-shell" @class([
            'space-y-6',
            'hidden' => $catalogPanelOpen && $catalogPermissions,
        ])>
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div class="flex flex-wrap gap-2" role="tablist" aria-label="{{ __('Matrix modules') }}">
                    @foreach ($modules as $module)
                        <button type="button"
                            class="matrix-tab rounded-xl border px-3 py-2 text-sm font-semibold transition data-[active=true]:border-amber-500 data-[active=true]:bg-amber-50 data-[active=true]:text-amber-950 data-[active=false]:border-slate-200 data-[active=false]:bg-white data-[active=false]:text-slate-600 data-[active=false]:hover:border-slate-300"
                            data-matrix-tab="{{ $module['key'] }}"
                            data-active="{{ $module['key'] === $firstTab ? 'true' : 'false' }}">
                            {{ __($module['label']) }}
                        </button>
                    @endforeach
                </div>
                <div class="w-full max-w-md">
                    <label for="matrix-search" class="sr-only">{{ __('Filter permissions') }}</label>
                    <input id="matrix-search" type="search"
                        class="block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500"
                        placeholder="{{ __('Filter by label or permission name…') }}" autocomplete="off">
                </div>
            </div>

            <form action="{{ route('admin.roles.matrix.update') }}" method="post" id="permission-matrix-form">
                @csrf
                @method('PUT')

                <div class="admin-card border-0 p-0 shadow-md shadow-slate-200/50">
                    <div class="max-h-[min(70vh,720px)] overflow-auto rounded-2xl border border-slate-100">
                        <table class="w-full min-w-[720px] border-collapse text-left text-sm">
                            <thead
                                class="sticky top-0 z-30 bg-slate-50/95 text-xs font-semibold uppercase tracking-wide text-slate-500 backdrop-blur">
                                <tr>
                                    <th scope="col"
                                        class="sticky left-0 z-40 min-w-[11rem] border-b border-r border-slate-200 bg-slate-50 px-3 py-3 align-bottom text-slate-600 shadow-[4px_0_12px_-4px_rgba(15,23,42,0.12)]">
                                        {{ __('Role') }}
                                    </th>
                                    @foreach ($modules as $module)
                                        @foreach ($module['groups'] ?? [] as $group)
                                            @foreach ($group['permissions'] ?? [] as $perm)
                                                @php
                                                    $modLabel = (string) ($module['label'] ?? '');
                                                    $grpLabel = (string) ($group['label'] ?? '');
                                                    $searchBlob = \Illuminate\Support\Str::lower(
                                                        $modLabel .
                                                            ' ' .
                                                            $grpLabel .
                                                            ' ' .
                                                            ($perm['label'] ?? '') .
                                                            ' ' .
                                                            ($perm['name'] ?? ''),
                                                    );
                                                @endphp
                                                <th scope="col"
                                                    class="matrix-col-head border-b border-slate-200 bg-slate-50 px-1 py-2 text-center align-bottom font-medium normal-case text-slate-700"
                                                    data-matrix-module="{{ $module['key'] }}"
                                                    data-matrix-perm="{{ $perm['name'] }}"
                                                    data-matrix-search="{{ e($searchBlob) }}">
                                                    <div class="flex min-w-[4.25rem] flex-col items-center gap-1">
                                                        @if ($modLabel !== '' && $grpLabel !== '' && $modLabel !== $grpLabel)
                                                            <span
                                                                class="text-[10px] font-bold uppercase tracking-wide text-slate-400">{{ __($modLabel) }}</span>
                                                            <span
                                                                class="text-[10px] font-semibold leading-tight text-slate-500">{{ __($grpLabel) }}</span>
                                                        @else
                                                            <span
                                                                class="text-[10px] font-bold uppercase tracking-wide text-slate-400">{{ __($modLabel !== '' ? $modLabel : $grpLabel) }}</span>
                                                        @endif
                                                        <span
                                                            class="text-xs font-semibold text-slate-800">{{ __($perm['label']) }}</span>
                                                        <button type="button"
                                                            class="matrix-col-toggle mx-auto mt-0.5 flex h-7 min-w-[4.75rem] items-center justify-center rounded-lg border border-slate-200 bg-white px-1.5 text-[10px] font-bold uppercase tracking-wide text-slate-600 shadow-sm transition hover:border-amber-300 hover:bg-amber-50/80 hover:text-amber-900 disabled:cursor-not-allowed disabled:opacity-40 matrix-col-state-none"
                                                            data-matrix-perm="{{ $perm['name'] }}"
                                                            title="{{ __('Toggle this permission for all roles (visible rows)') }}">
                                                            {{ __('Column') }}
                                                        </button>
                                                    </div>
                                                </th>
                                            @endforeach
                                        @endforeach
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                @foreach ($roles as $role)
                                    @php
                                        $isSuperAdmin = $role->name === 'super-admin';
                                        $rolePermissionNames = $role->permissions->pluck('name')->all();
                                    @endphp
                                    <tr class="hover:bg-slate-50/60" data-role-row="{{ $role->id }}">
                                        <th scope="row"
                                            class="sticky left-0 z-20 border-r border-slate-100 bg-white px-3 py-2.5 text-left font-medium text-slate-900 shadow-[4px_0_12px_-4px_rgba(15,23,42,0.08)]">
                                            <div class="flex flex-col gap-2">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <span class="truncate">{{ $role->name }}</span>
                                                    @if ($isSuperAdmin)
                                                        <span
                                                            class="inline-flex items-center rounded-full bg-amber-500/15 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-amber-800">{{ __('All permissions') }}</span>
                                                    @endif
                                                </div>
                                                @unless ($isSuperAdmin)
                                                    <button type="button"
                                                        class="matrix-row-select w-fit rounded-lg border border-slate-200 bg-slate-50 px-2 py-1 text-[11px] font-semibold text-slate-700 hover:border-amber-200 hover:bg-amber-50/60"
                                                        data-role-id="{{ $role->id }}">
                                                        {{ __('Select all in tab') }}
                                                    </button>
                                                @endunless
                                            </div>
                                        </th>
                                        @foreach ($modules as $module)
                                            @foreach ($module['groups'] ?? [] as $group)
                                                @foreach ($group['permissions'] ?? [] as $perm)
                                                    @php
                                                        $checked = $isSuperAdmin
                                                            ? true
                                                            : in_array($perm['name'], $rolePermissionNames, true);
                                                    @endphp
                                                    <td class="matrix-cell border-slate-50 px-1 py-1.5 text-center align-middle"
                                                        data-matrix-module="{{ $module['key'] }}"
                                                        data-matrix-perm="{{ $perm['name'] }}">
                                                        @if ($isSuperAdmin)
                                                            <input type="checkbox" checked disabled
                                                                class="h-4 w-4 rounded border-slate-300 text-amber-600 opacity-60"
                                                                aria-label="{{ $perm['name'] }}">
                                                        @else
                                                            <input type="checkbox"
                                                                name="roles_permissions[{{ $role->id }}][]"
                                                                value="{{ $perm['name'] }}" @checked($checked)
                                                                class="matrix-perm-input h-4 w-4 rounded border-slate-300 text-amber-600 focus:ring-amber-500"
                                                                data-role-id="{{ $role->id }}"
                                                                data-matrix-module="{{ $module['key'] }}"
                                                                data-matrix-perm="{{ $perm['name'] }}">
                                                        @endif
                                                    </td>
                                                @endforeach
                                            @endforeach
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-6 flex flex-wrap items-center justify-between gap-3">
                    <p class="max-w-xl text-xs text-slate-500">
                        {{ __('Super-admin always holds every web permission on save. Other roles are synced exactly as shown for all columns (including hidden tabs).') }}
                    </p>
                    <button type="submit"
                        class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800">
                        {{ __('Save changes') }}
                    </button>
                </div>
            </form>
        </div>

        @if ($catalogPermissions)
            <div id="catalog-workspace" @class(['space-y-6', 'hidden' => !$catalogPanelOpen])>
                <div class="grid gap-6 lg:grid-cols-3 lg:items-start">
                    <div class="admin-card overflow-hidden border-0 p-0 shadow-md shadow-slate-200/50 lg:col-span-2">
                        <div class="border-b border-slate-100 px-4 py-3">
                            <h2 class="text-sm font-semibold text-slate-900" style="font-family: Outfit, sans-serif;">
                                {{ __('Registered permissions') }}</h2>
                            <p class="mt-0.5 text-xs text-slate-500">
                                {{ __('Web guard — used by the admin panel and matrix.') }}</p>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full min-w-[480px] text-left text-sm">
                                <thead
                                    class="border-b border-slate-200 bg-slate-50/80 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    <tr>
                                        <th class="px-4 py-3">{{ __('Name') }}</th>
                                        <th class="px-4 py-3">{{ __('Guard') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse ($catalogPermissions as $permission)
                                        <tr class="bg-white hover:bg-slate-50/50">
                                            <td class="px-4 py-3 font-mono text-xs text-slate-900">{{ $permission->name }}
                                            </td>
                                            <td class="px-4 py-3 text-slate-600">{{ $permission->guard_name }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="px-4 py-8 text-center text-slate-500">
                                                {{ __('No permissions found.') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if ($catalogPermissions->hasPages())
                            <div class="border-t border-slate-100 px-4 py-3">
                                {{ $catalogPermissions->withQueryString()->links() }}
                            </div>
                        @endif
                    </div>
                    @can('admin.permissions.create')
                        <div id="permission-catalog-form" class="admin-card border-0 shadow-md shadow-slate-200/50">
                            <h2 class="text-base font-semibold text-slate-900" style="font-family: Outfit, sans-serif;">
                                {{ __('Add permission') }}</h2>
                            <p class="mt-1 text-xs text-slate-500">
                                {{ __('Dot notation, e.g. module.action. Guard is fixed to web.') }}</p>
                            <form action="{{ route('admin.permissions.store') }}" method="post" class="mt-4 space-y-4">
                                @csrf
                                <div>
                                    <label for="catalog-permission-name"
                                        class="block text-sm font-medium text-slate-700">{{ __('Permission name') }}</label>
                                    <input type="text" name="name" id="catalog-permission-name"
                                        value="{{ old('name') }}" required
                                        class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 font-mono text-sm shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500"
                                        placeholder="admin.reports.view" autocomplete="off">
                                    <p class="mt-1 text-xs text-slate-500">{{ __('Guard') }}: <strong>web</strong>
                                        ({{ __('fixed for admin panel') }})</p>
                                    @error('name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <button type="submit"
                                    class="w-full rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">
                                    {{ __('Save') }}
                                </button>
                            </form>
                        </div>
                    @endcan
                </div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        (function() {
            var shell = document.getElementById('matrix-shell');
            var form = document.getElementById('permission-matrix-form');
            if (!form || !shell) return;

            var tabs = shell.querySelectorAll('[data-matrix-tab]');
            var searchInput = document.getElementById('matrix-search');
            var activeModule = @json($firstTab);

            function applyVisibility() {
                var q = (searchInput && searchInput.value ? searchInput.value : '').trim().toLowerCase();
                form.querySelectorAll('.matrix-col-head').forEach(function(th) {
                    var mod = th.getAttribute('data-matrix-module');
                    var blob = (th.getAttribute('data-matrix-search') || '').toLowerCase();
                    var modOk = !activeModule || mod === activeModule;
                    var searchOk = !q || blob.indexOf(q) !== -1;
                    var show = modOk && searchOk;
                    th.classList.toggle('hidden', !show);
                });
                form.querySelectorAll('.matrix-cell').forEach(function(td) {
                    var perm = td.getAttribute('data-matrix-perm') || '';
                    var head = form.querySelector('.matrix-col-head[data-matrix-perm="' + perm + '"]');
                    var headHidden = head ? head.classList.contains('hidden') : false;
                    td.classList.toggle('hidden', headHidden);
                });
                refreshAllColStates();
            }

            function inputsForPerm(perm) {
                return Array.prototype.slice.call(
                    form.querySelectorAll('input.matrix-perm-input[data-matrix-perm="' + perm + '"]')
                );
            }

            function visibleInputsForPerm(perm) {
                return inputsForPerm(perm).filter(function(input) {
                    var cell = input.closest('.matrix-cell');
                    return cell && !cell.classList.contains('hidden');
                });
            }

            function refreshColState(perm) {
                var master = form.querySelector('.matrix-col-toggle[data-matrix-perm="' + perm + '"]');
                if (!master) return;
                var vis = visibleInputsForPerm(perm);
                master.classList.remove('matrix-col-state-all', 'matrix-col-state-partial', 'matrix-col-state-none');
                if (!vis.length) {
                    master.disabled = true;
                    master.classList.add('matrix-col-state-none');
                    return;
                }
                master.disabled = false;
                var on = vis.filter(function(i) {
                    return i.checked;
                }).length;
                if (on === vis.length) {
                    master.classList.add('matrix-col-state-all');
                } else if (on === 0) {
                    master.classList.add('matrix-col-state-none');
                } else {
                    master.classList.add('matrix-col-state-partial');
                }
            }

            function refreshAllColStates() {
                var seen = {};
                form.querySelectorAll('.matrix-col-toggle[data-matrix-perm]').forEach(function(m) {
                    var p = m.getAttribute('data-matrix-perm');
                    if (!p || seen[p]) return;
                    seen[p] = true;
                    refreshColState(p);
                });
            }

            tabs.forEach(function(btn) {
                btn.addEventListener('click', function() {
                    activeModule = btn.getAttribute('data-matrix-tab') || '';
                    tabs.forEach(function(b) {
                        b.setAttribute('data-active', b === btn ? 'true' : 'false');
                    });
                    applyVisibility();
                });
            });

            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    applyVisibility();
                });
            }

            form.querySelectorAll('.matrix-col-toggle').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var perm = btn.getAttribute('data-matrix-perm');
                    if (!perm) return;
                    var vis = visibleInputsForPerm(perm);
                    if (!vis.length) return;
                    var allOn = vis.every(function(i) {
                        return i.checked;
                    });
                    var next = !allOn;
                    vis.forEach(function(input) {
                        input.checked = next;
                    });
                    refreshColState(perm);
                });
            });

            form.querySelectorAll('.matrix-row-select').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var rid = btn.getAttribute('data-role-id');
                    if (!rid) return;
                    var row = form.querySelector('tr[data-role-row="' + rid + '"]');
                    if (!row) return;
                    var inputs = row.querySelectorAll('input.matrix-perm-input');
                    var vis = Array.prototype.filter.call(inputs, function(input) {
                        var cell = input.closest('.matrix-cell');
                        return cell && !cell.classList.contains('hidden');
                    });
                    if (!vis.length) return;
                    var allOn = vis.every(function(i) {
                        return i.checked;
                    });
                    var next = !allOn;
                    vis.forEach(function(input) {
                        input.checked = next;
                    });
                    refreshAllColStates();
                });
            });

            form.addEventListener('change', function(e) {
                if (e.target && e.target.classList && e.target.classList.contains('matrix-perm-input')) {
                    var perm = e.target.getAttribute('data-matrix-perm');
                    if (perm) refreshColState(perm);
                }
            });

            applyVisibility();
        })();

        (function() {
            var root = document.getElementById('matrix-page-root');
            var matrixShell = document.getElementById('matrix-shell');
            var catalogWs = document.getElementById('catalog-workspace');
            if (!root || !matrixShell || !catalogWs) return;

            var modeButtons = root.querySelectorAll('[data-page-mode]');
            if (!modeButtons.length) return;

            function setMode(mode) {
                var isCat = mode === 'catalog';
                matrixShell.classList.toggle('hidden', isCat);
                catalogWs.classList.toggle('hidden', !isCat);
                modeButtons.forEach(function(btn) {
                    var m = btn.getAttribute('data-page-mode');
                    btn.setAttribute('data-active', m === mode ? 'true' : 'false');
                });
                try {
                    var url = new URL(window.location.href);
                    if (isCat) {
                        url.searchParams.set('show', 'catalog');
                    } else {
                        url.searchParams.delete('show');
                    }
                    url.searchParams.delete('new');
                    window.history.replaceState({}, '', url.pathname + url.search + url.hash);
                } catch (e) {}
            }

            modeButtons.forEach(function(btn) {
                btn.addEventListener('click', function() {
                    setMode(btn.getAttribute('data-page-mode') || 'matrix');
                });
            });

            try {
                if (new URLSearchParams(window.location.search).get('new') === '1') {
                    var el = document.getElementById('permission-catalog-form');
                    if (el) {
                        el.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                        var inp = el.querySelector('#catalog-permission-name');
                        if (inp) {
                            inp.focus();
                        }
                    }
                }
            } catch (e) {}
        })();
    </script>
@endpush
