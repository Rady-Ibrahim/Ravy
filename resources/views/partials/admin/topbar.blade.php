@php
    $authUser = auth()->user();
    $dashActive = request()->routeIs('admin.dashboard');
    $dashboardNotifications = app(
        \Modules\Notification\Services\Admin\DashboardNotificationService::class,
    )->getDashboardNotifications();
@endphp

<header class="sticky top-0 z-30 shrink-0 border-b border-brand-navy/10 bg-white shadow-[0_1px_0_rgba(0,27,54,0.06)]">
    <div class="mx-auto flex h-14 w-full max-w-[1920px] items-center gap-2 px-3 sm:h-[3.75rem] sm:gap-3 sm:px-4 lg:px-6">
        <button type="button" id="admin-sidebar-toggle"
            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#F4F6F9] text-brand-navy/70 transition hover:bg-[#E8ECF2] hover:text-brand-navy sm:h-10 sm:w-10"
            aria-label="{{ __('Toggle menu') }}" aria-controls="admin-sidebar" aria-expanded="false">
            <svg class="h-[1.15rem] w-[1.15rem]" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
        </button>



        <div class="min-w-0 flex-1 md:hidden">
            <h1 class="truncate text-base font-semibold tracking-tight text-brand-navy sm:text-lg"
                style="font-family: Outfit, sans-serif;">
                @yield('page_title', __('Dashboard'))
            </h1>
        </div>

        <nav class="mx-auto hidden min-w-0 flex-1 items-center justify-center gap-1 md:flex"
            aria-label="{{ __('Primary admin navigation') }}">
            <a href="{{ route('admin.dashboard') }}" @class([
                'inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-semibold transition',
                'bg-brand-navy text-white shadow-sm shadow-brand-navy/20' => $dashActive,
                'text-brand-navy hover:bg-[#F4F6F9]' => !$dashActive,
            ])>
                <svg class="h-[1.05rem] w-[1.05rem] shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.75"
                    stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                </svg>
                {{ __('Dashboard') }}
            </a>
            <a href="{{ route('admin.orders.index') }}"
                class="inline-flex items-center gap-2 rounded-full px-3 py-2 text-sm font-medium text-brand-navy hover:bg-[#F4F6F9]"
                title="{{ __('Orders') }}">
                <svg class="h-[1.05rem] w-[1.05rem] shrink-0 opacity-60" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.495 3.255 8.25 4.21 8.25 5.25v.75H12M8.25 5.25v-.75A2.25 2.25 0 0110.5 3h1.5m-6 18.75v-1.5a2.25 2.25 0 012.25-2.25h7.5A2.25 2.25 0 0121 20.25v1.5" />
                </svg>
                {{ __('Orders') }}
            </a>
        </nav>

        <div class="flex shrink-0 items-center gap-1.5 sm:gap-2" dir="ltr">
            <div class="relative" data-admin-notifications>
                <button type="button" id="admin-notifications-btn"
                    class="relative flex h-9 w-9 items-center justify-center rounded-full bg-[#F4F6F9] text-brand-navy/70 transition hover:bg-[#E8ECF2] hover:text-brand-navy sm:h-10 sm:w-10"
                    aria-label="{{ __('Notifications') }}" aria-expanded="false"
                    aria-controls="admin-notifications-panel">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                        aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                    </svg>
                    @if ($dashboardNotifications['total'] > 0)
                        <span
                            class="absolute end-1.5 top-1.5 flex h-5 min-w-[1.25rem] items-center justify-center rounded-full bg-red-500 px-1.5 text-[10px] font-semibold text-white ring-2 ring-[#F4F6F9]">
                            {{ $dashboardNotifications['total'] }}
                        </span>
                    @endif
                </button>

                <div id="admin-notifications-panel"
                    class="absolute end-0 top-full z-50 mt-2 hidden min-w-[18rem] max-w-[22rem] rounded-2xl border border-brand-navy/10 bg-white p-3 shadow-lg shadow-brand-navy/10 ring-1 ring-black/5"
                    role="menu" aria-labelledby="admin-notifications-btn">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-sm font-semibold text-brand-navy">{{ __('Notifications') }}</p>
                            <p class="text-xs text-slate-500">{{ __('Dashboard alerts for orders and stock') }}</p>
                        </div>
                        <span
                            class="rounded-full bg-slate-100 px-2 py-1 text-[11px] font-semibold uppercase tracking-[0.12em] text-slate-600">
                            {{ $dashboardNotifications['total'] }}
                        </span>
                    </div>
                    <div class="mt-3 space-y-2">
                        @foreach ($dashboardNotifications['items'] as $item)
                            <a href="{{ $item['url'] ?? '#' }}"
                                class="block rounded-2xl border border-brand-navy/10 bg-[#f8fafc] px-3 py-3 text-sm text-slate-700 transition hover:bg-white"
                                @if (!$item['url']) onclick="return false;" @endif>
                                <p class="font-medium text-slate-900">{{ $item['title'] }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $item['description'] }}</p>
                            </a>
                        @endforeach
                    </div>
                    @can('admin.notifications.view')
                        <a href="{{ route('admin.settings.notifications') }}"
                            class="mt-3 inline-flex w-full items-center justify-center rounded-full bg-brand-navy px-3 py-2 text-sm font-semibold text-brand-cream transition hover:bg-brand-navy-mid">
                            {{ __('Notification settings') }}
                        </a>
                    @endcan
                </div>
            </div>


            <div class="relative ms-0.5 min-w-0 max-w-[min(100%,15rem)] sm:max-w-[18rem]" data-admin-user-menu>
                <button type="button" id="admin-user-menu-btn"
                    class="flex w-full min-w-0 items-center gap-2.5 rounded-xl border border-transparent py-1 pe-1 ps-1.5 text-start transition hover:bg-[#F4F6F9] sm:gap-3 sm:ps-2"
                    aria-expanded="false" aria-haspopup="true" aria-controls="admin-user-menu-panel">
                    <div class="min-w-0 flex-1 overflow-hidden">
                        <p class="truncate text-sm font-semibold text-brand-navy">
                            {{ $authUser->name ?: $authUser->email }}</p>
                        <p class="truncate text-xs capitalize text-brand-navy/50">{{ $authUser->type }}</p>
                    </div>
                    <span
                        class="flex h-9 w-9 shrink-0 items-center justify-center overflow-hidden rounded-lg bg-gradient-to-br from-brand-navy/8 to-brand-navy/4 ring-1 ring-brand-navy/10 sm:h-10 sm:w-10">
                        <svg class="h-5 w-5 text-brand-navy/35" fill="none" viewBox="0 0 24 24" stroke-width="1.25"
                            stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                    </span>
                    <svg class="hidden h-4 w-4 shrink-0 text-brand-navy/35 sm:block" fill="none" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                    </svg>
                </button>

                <div id="admin-user-menu-panel"
                    class="absolute end-0 top-full z-50 mt-2 hidden min-w-[13rem] rounded-xl border border-brand-navy/10 bg-white py-1 shadow-lg shadow-brand-navy/10 ring-1 ring-black/5"
                    role="menu" aria-labelledby="admin-user-menu-btn">
                    @can('admin.users.edit')
                        <a href="{{ route('admin.users.edit', $authUser) }}"
                            class="flex items-center gap-2.5 px-4 py-2.5 text-sm font-medium text-brand-navy transition hover:bg-brand-page"
                            role="menuitem">
                            <svg class="h-5 w-5 text-brand-navy/45" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                            {{ __('Profile') }}
                        </a>
                        <div class="border-t border-brand-navy/10" role="separator"></div>
                    @endcan

                    <form method="post" action="{{ route('admin.auth.logout') }}" role="none">
                        @csrf
                        <button type="submit"
                            class="flex w-full items-center gap-2.5 px-4 py-2.5 text-start text-sm font-medium text-brand-navy transition hover:bg-brand-page"
                            role="menuitem">
                            <svg class="h-5 w-5 text-brand-navy/45" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                            </svg>
                            {{ __('Sign out') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>

@push('scripts')
    <script>
        (function() {
            var root = document.querySelector('[data-admin-user-menu]');
            if (!root) return;
            var btn = document.getElementById('admin-user-menu-btn');
            var panel = document.getElementById('admin-user-menu-panel');
            if (!btn || !panel) return;

            function open() {
                panel.classList.remove('hidden');
                btn.setAttribute('aria-expanded', 'true');
            }

            function close() {
                panel.classList.add('hidden');
                btn.setAttribute('aria-expanded', 'false');
            }

            function toggle() {
                if (panel.classList.contains('hidden')) open();
                else close();
            }

            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                toggle();
            });

            document.addEventListener('click', function(e) {
                if (!root.contains(e.target)) close();
            });

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') close();
            });
        })();

        (function() {
            var root = document.querySelector('[data-admin-notifications]');
            if (!root) return;
            var btn = document.getElementById('admin-notifications-btn');
            var panel = document.getElementById('admin-notifications-panel');
            if (!btn || !panel) return;

            function open() {
                panel.classList.remove('hidden');
                btn.setAttribute('aria-expanded', 'true');
            }

            function close() {
                panel.classList.add('hidden');
                btn.setAttribute('aria-expanded', 'false');
            }

            function toggle() {
                if (panel.classList.contains('hidden')) open();
                else close();
            }

            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                toggle();
            });

            document.addEventListener('click', function(e) {
                if (!root.contains(e.target)) close();
            });

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') close();
            });
        })();
    </script>
@endpush
