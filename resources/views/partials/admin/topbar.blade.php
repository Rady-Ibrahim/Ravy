<header class="sticky top-0 z-30 flex h-16 shrink-0 items-center gap-4 border-b border-slate-200/80 bg-white/90 px-4 backdrop-blur-md sm:px-6 lg:px-8">
    <button
        type="button"
        id="admin-sidebar-open"
        class="rounded-lg p-2 text-slate-600 hover:bg-slate-100 lg:hidden"
        aria-label="{{ __('Open menu') }}"
    >
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
        </svg>
    </button>

    <div class="min-w-0 flex-1">
        <h1 class="truncate text-lg font-semibold tracking-tight text-slate-900 sm:text-xl" style="font-family: Outfit, sans-serif;">
            @yield('page_title', __('Dashboard'))
        </h1>
        @hasSection('page_subtitle')
            <p class="truncate text-sm text-slate-500">@yield('page_subtitle')</p>
        @endif
    </div>

    <div class="hidden items-center gap-2 sm:flex">
        <span class="rounded-full bg-emerald-500/10 px-2.5 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-500/20">
            {{ __('Online') }}
        </span>
    </div>
</header>
