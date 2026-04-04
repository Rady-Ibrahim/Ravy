@php
    $nav = [
        [
            'label' => __('Dashboard'),
            'route' => 'admin.dashboard',
            'icon' => 'home',
        ],
    ];
@endphp

<aside
    id="admin-sidebar"
    class="fixed inset-y-0 left-0 z-50 flex w-[17.5rem] -translate-x-full flex-col border-r border-white/5 bg-slate-950 transition-transform duration-200 ease-out lg:static lg:translate-x-0"
    aria-label="{{ __('Admin navigation') }}"
>
    <div class="flex h-16 shrink-0 items-center gap-3 border-b border-white/5 px-5">
        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-500/15 text-lg font-bold text-amber-400" style="font-family: Outfit, sans-serif;">R</span>
        <div class="min-w-0">
            <p class="truncate text-sm font-semibold tracking-tight text-white" style="font-family: Outfit, sans-serif;">{{ config('app.name', 'Ravy') }}</p>
            <p class="truncate text-xs text-slate-500">{{ __('Admin') }}</p>
        </div>
        <button
            type="button"
            id="admin-sidebar-close"
            class="ml-auto rounded-lg p-2 text-slate-400 hover:bg-white/5 hover:text-white lg:hidden"
            aria-label="{{ __('Close menu') }}"
        >
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4">
        <p class="mb-2 px-3 text-[10px] font-semibold uppercase tracking-widest text-slate-600">{{ __('Main') }}</p>
        @foreach ($nav as $item)
            @php $active = request()->routeIs($item['route']); @endphp
            <a
                href="{{ route($item['route']) }}"
                @class(['admin-nav-link', 'admin-nav-link--active' => $active])
            >
                @if ($item['icon'] === 'home')
                    <svg class="h-5 w-5 shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>
                @endif
                {{ $item['label'] }}
            </a>
        @endforeach

        <p class="mb-2 mt-8 px-3 text-[10px] font-semibold uppercase tracking-widest text-slate-600">{{ __('Soon') }}</p>
        <span class="admin-nav-link cursor-not-allowed opacity-40 hover:bg-transparent hover:text-slate-400">
            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
            </svg>
            {{ __('Catalog') }}
        </span>
        <span class="admin-nav-link cursor-not-allowed opacity-40 hover:bg-transparent hover:text-slate-400">
            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
            </svg>
            {{ __('Users') }}
        </span>
    </nav>

    <div class="border-t border-white/5 p-3">
        <div class="rounded-xl bg-white/5 px-3 py-3">
            <p class="truncate text-xs font-medium text-slate-300">{{ auth()->user()->name ?? auth()->user()->email }}</p>
            <p class="truncate text-[11px] text-slate-500">{{ auth()->user()->email }}</p>
            <form method="post" action="{{ route('admin.auth.logout') }}" class="mt-3">
                @csrf
                <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-lg bg-white/10 px-3 py-2 text-xs font-semibold text-white transition hover:bg-white/15">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                    </svg>
                    {{ __('Sign out') }}
                </button>
            </form>
        </div>
    </div>
</aside>
