@extends('layouts.admin')

@section('title', __('Dashboard'))

@section('page_title', __('Dashboard'))
@section('page_subtitle', __('Overview and quick status for your workspace.'))

@section('content')
    @php
        $user = auth()->user();
    @endphp

    <div class="mx-auto max-w-7xl">
        {{-- Welcome --}}
        <div class="mb-8 overflow-hidden rounded-2xl border border-slate-200/80 bg-gradient-to-br from-slate-900 via-slate-900 to-slate-800 p-6 text-white shadow-lg shadow-slate-900/10 sm:p-8">
            <p class="text-sm font-medium text-amber-400/90">{{ __('Welcome back') }}</p>
            <h2 class="mt-1 text-2xl font-bold tracking-tight sm:text-3xl" style="font-family: Outfit, sans-serif;">
                {{ $user->name ?: $user->email }}
            </h2>
            <p class="mt-2 max-w-xl text-sm text-slate-400">
                {{ __('You are signed in as an administrator. Use the sidebar to navigate; more modules will appear here as the project grows.') }}
            </p>
            <dl class="mt-6 grid gap-4 sm:grid-cols-3">
                <div class="rounded-xl bg-white/5 px-4 py-3 ring-1 ring-white/10">
                    <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{{ __('Role') }}</dt>
                    <dd class="mt-1 text-sm font-semibold capitalize text-white">{{ $user->type }}</dd>
                </div>
                <div class="rounded-xl bg-white/5 px-4 py-3 ring-1 ring-white/10">
                    <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{{ __('Status') }}</dt>
                    <dd class="mt-1 text-sm font-semibold capitalize text-emerald-400">{{ $user->status }}</dd>
                </div>
                <div class="rounded-xl bg-white/5 px-4 py-3 ring-1 ring-white/10">
                    <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{{ __('Email') }}</dt>
                    <dd class="mt-1 truncate text-sm font-semibold text-white">{{ $user->email }}</dd>
                </div>
            </dl>
        </div>

        {{-- Stat cards (placeholders) --}}
        <div class="mb-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="admin-card border-0 shadow-md shadow-slate-200/50">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Total users') }}</p>
                        <p class="mt-2 text-3xl font-bold tabular-nums text-slate-900" style="font-family: Outfit, sans-serif;">—</p>
                        <p class="mt-1 text-xs text-slate-500">{{ __('Connect analytics or a query when ready.') }}</p>
                    </div>
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                        </svg>
                    </span>
                </div>
            </div>
            <div class="admin-card border-0 shadow-md shadow-slate-200/50">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('API') }}</p>
                        <p class="mt-2 text-3xl font-bold tabular-nums text-slate-900" style="font-family: Outfit, sans-serif;">Sanctum</p>
                        <p class="mt-1 text-xs text-slate-500">{{ __('Token auth for clients is enabled.') }}</p>
                    </div>
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-500/10 text-amber-700">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.847a4.5 4.5 0 003.09 3.09L15.75 12l-2.847.813a4.5 4.5 0 00-3.09 3.09z" />
                        </svg>
                    </span>
                </div>
            </div>
            <div class="admin-card border-0 shadow-md shadow-slate-200/50">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Documentation') }}</p>
                        <p class="mt-2 text-lg font-bold text-slate-900" style="font-family: Outfit, sans-serif;">OpenAPI</p>
                        <p class="mt-1 text-xs text-slate-500">{{ __('Swagger UI at /api/documentation') }}</p>
                    </div>
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-sky-100 text-sky-700">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                    </span>
                </div>
            </div>
            <div class="admin-card border-0 shadow-md shadow-slate-200/50">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Environment') }}</p>
                        <p class="mt-2 text-lg font-bold capitalize text-slate-900" style="font-family: Outfit, sans-serif;">{{ app()->environment() }}</p>
                        <p class="mt-1 text-xs text-slate-500">{{ __('Laravel') }} {{ app()->version() }}</p>
                    </div>
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-500/10 text-emerald-700">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                </div>
            </div>
        </div>

        {{-- Two columns --}}
        <div class="grid gap-6 lg:grid-cols-3">
            <div class="admin-card lg:col-span-2 border-0 shadow-md shadow-slate-200/50">
                <h3 class="text-base font-semibold text-slate-900" style="font-family: Outfit, sans-serif;">{{ __('Quick actions') }}</h3>
                <p class="mt-1 text-sm text-slate-500">{{ __('Shortcuts for common tasks.') }}</p>
                <ul class="mt-5 space-y-2">
                    <li>
                        <a href="{{ url('/api/documentation') }}" target="_blank" rel="noopener noreferrer" class="group flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm font-medium text-slate-700 transition hover:border-amber-200 hover:bg-amber-50/50">
                            <span>{{ __('Open API documentation') }}</span>
                            <svg class="h-4 w-4 text-slate-400 transition group-hover:text-amber-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                            </svg>
                        </a>
                    </li>
                    <li>
                        <span class="flex items-center justify-between rounded-xl border border-dashed border-slate-200 px-4 py-3 text-sm text-slate-400">
                            {{ __('More actions when modules are added') }}
                        </span>
                    </li>
                </ul>
            </div>
            <div class="admin-card border-0 shadow-md shadow-slate-200/50">
                <h3 class="text-base font-semibold text-slate-900" style="font-family: Outfit, sans-serif;">{{ __('Session') }}</h3>
                <p class="mt-1 text-sm text-slate-500">{{ __('Your admin session is active.') }}</p>
                <dl class="mt-4 space-y-3 text-sm">
                    <div class="flex justify-between gap-2 border-b border-slate-100 pb-3">
                        <dt class="text-slate-500">{{ __('Guard') }}</dt>
                        <dd class="font-medium text-slate-800">web</dd>
                    </div>
                    <div class="flex justify-between gap-2 border-b border-slate-100 pb-3">
                        <dt class="text-slate-500">{{ __('User ID') }}</dt>
                        <dd class="font-mono text-xs text-slate-800">{{ $user->id }}</dd>
                    </div>
                    <div class="pt-1">
                        <form method="post" action="{{ route('admin.auth.logout') }}">
                            @csrf
                            <button type="submit" class="w-full rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800">
                                {{ __('Sign out') }}
                            </button>
                        </form>
                    </div>
                </dl>
            </div>
        </div>
    </div>
@endsection
