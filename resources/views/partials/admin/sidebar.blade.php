@php
    $mainNav = [
        ['label' => __('Dashboard'), 'route' => 'admin.dashboard', 'icon' => 'home', 'permission' => null],
        ['label' => __('Users'), 'route' => 'admin.users.index', 'icon' => 'users', 'permission' => 'admin.users.view'],
        ['label' => __('Roles'), 'route' => 'admin.roles.index', 'icon' => 'shield', 'permission' => 'admin.roles.view'],
        ['label' => __('Permission matrix'), 'route' => 'admin.roles.matrix', 'icon' => 'grid', 'permission' => 'admin.matrix.manage'],
    ];
@endphp

<aside
    id="admin-sidebar"
    class="fixed inset-y-0 left-0 z-50 flex w-[17.5rem] -translate-x-full flex-col border-r border-white/10 bg-brand-navy transition-transform duration-200 ease-out will-change-transform lg:translate-x-0"
    aria-label="{{ __('Admin navigation') }}"
>
    <div class="flex h-[4.25rem] shrink-0 items-center gap-3 border-b border-white/10 px-4">
        @if (file_exists(public_path('images/brand/ravy-logo.png')))
            <img src="{{ asset('images/brand/ravy-logo.png') }}" alt="" class="h-10 w-auto max-w-[7rem] object-contain object-left opacity-95" width="112" height="40">
        @else
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-cream/12 text-sm font-bold text-brand-cream" style="font-family: Cinzel, serif;">R</span>
        @endif
        <div class="min-w-0">
            <p class="truncate text-[0.7rem] font-semibold uppercase leading-tight tracking-[0.14em] text-brand-cream sm:text-xs" style="font-family: Cinzel, serif;">
                {{ config('app.name', 'Ravy Boutique') }}
            </p>
            <p class="truncate text-[11px] text-white/45">{{ __('Admin panel') }}</p>
        </div>
        <button
            type="button"
            id="admin-sidebar-close"
            class="ml-auto rounded-lg p-2 text-white/50 hover:bg-white/10 hover:text-brand-cream lg:hidden"
            aria-label="{{ __('Close menu') }}"
        >
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4">
        <p class="mb-2 px-3 text-[10px] font-semibold uppercase tracking-widest text-white/35">{{ __('Main') }}</p>
        @foreach ($mainNav as $item)
            @if ($item['permission'] && ! auth()->user()->can($item['permission']))
                @continue
            @endif
            @php $active = $item['route'] === 'admin.dashboard' ? request()->routeIs('admin.dashboard') : request()->routeIs(\Illuminate\Support\Str::beforeLast($item['route'], '.').'.*'); @endphp
            <a
                href="{{ route($item['route']) }}"
                @class(['admin-nav-link', 'admin-nav-link--active' => $active])
            >
                @if ($item['icon'] === 'home')
                    <svg class="h-5 w-5 shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>
                @elseif ($item['icon'] === 'users')
                    <svg class="h-5 w-5 shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                    </svg>
                @elseif ($item['icon'] === 'shield')
                    <svg class="h-5 w-5 shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                    </svg>
                @elseif ($item['icon'] === 'grid')
                    <svg class="h-5 w-5 shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25A2.25 2.25 0 0113.5 8.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25v-2.25z" />
                    </svg>
                @endif
                {{ $item['label'] }}
            </a>
        @endforeach
    </nav>

    <div class="border-t border-white/10 p-3">
        <div class="rounded-xl border border-white/10 bg-black/20 px-3 py-3">
            <p class="truncate text-xs font-medium text-brand-cream/90">{{ auth()->user()->name ?? auth()->user()->email }}</p>
            <p class="truncate text-[11px] text-white/40">{{ auth()->user()->email }}</p>
            <form method="post" action="{{ route('admin.auth.logout') }}" class="mt-3">
                @csrf
                <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-lg border border-brand-cream/25 bg-brand-cream/10 px-3 py-2 text-xs font-semibold text-brand-cream transition hover:bg-brand-cream/18">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                    </svg>
                    {{ __('Sign out') }}
                </button>
            </form>
        </div>
    </div>
</aside>
