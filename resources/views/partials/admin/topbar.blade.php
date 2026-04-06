@php
    $authUser = auth()->user();
    $dashActive = request()->routeIs('admin.dashboard');
@endphp

<header class="sticky top-0 z-30 shrink-0 border-b border-brand-navy/10 bg-white shadow-[0_1px_0_rgba(0,27,54,0.06)]">
    <div class="mx-auto flex h-14 w-full max-w-[1920px] items-center gap-2 px-3 sm:h-[3.75rem] sm:gap-3 sm:px-4 lg:px-6">
        <button
            type="button"
            id="admin-sidebar-toggle"
            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#F4F6F9] text-brand-navy/70 transition hover:bg-[#E8ECF2] hover:text-brand-navy sm:h-10 sm:w-10"
            aria-label="{{ __('Toggle menu') }}"
            aria-controls="admin-sidebar"
            aria-expanded="false"
        >
            <svg class="h-[1.15rem] w-[1.15rem]" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
        </button>

        {{-- أيقونات سريعة (واجهة فقط — اربطها لاحقاً) --}}
        <div class="hidden items-center gap-1 sm:flex" dir="ltr">
            <button
                type="button"
                class="flex h-9 w-9 items-center justify-center rounded-full bg-[#F4F6F9] text-brand-navy/70 transition hover:bg-[#E8ECF2] hover:text-brand-navy"
                aria-label="{{ __('Language') }}"
            >
                <svg class="h-[1.15rem] w-[1.15rem]" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m10.5 21 5.25-11.25L21 21m-9-3h7.5M3 5.621a48.474 48.474 0 016-.371A48.474 48.474 0 0112 5.25c2.702 0 5.255.202 7.5.621m-9 9.75h9m-9-9.75h9m-9 9.75c0 1.657.895 3 2 3s2-1.343 2-3m-4-9.75v9.75" />
                </svg>
            </button>
            <button
                type="button"
                class="flex h-9 w-9 items-center justify-center rounded-full bg-[#F4F6F9] text-brand-navy/70 transition hover:bg-[#E8ECF2] hover:text-brand-navy"
                aria-label="{{ __('Storefront') }}"
            >
                <svg class="h-[1.15rem] w-[1.15rem]" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z" />
                </svg>
            </button>
            <button
                type="button"
                class="flex h-9 w-9 items-center justify-center rounded-full bg-[#F4F6F9] text-brand-navy/70 transition hover:bg-[#E8ECF2] hover:text-brand-navy"
                aria-label="{{ __('Clear cache') }}"
            >
                <svg class="h-[1.15rem] w-[1.15rem]" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                </svg>
            </button>
            <button
                type="button"
                class="flex h-9 w-9 items-center justify-center rounded-full bg-[#F4F6F9] text-brand-navy/70 transition hover:bg-[#E8ECF2] hover:text-brand-navy"
                aria-label="{{ __('Shortcuts') }}"
            >
                <svg class="h-[1.15rem] w-[1.15rem]" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                </svg>
            </button>
        </div>

        <div class="min-w-0 flex-1 md:hidden">
            <h1 class="truncate text-base font-semibold tracking-tight text-brand-navy sm:text-lg" style="font-family: Outfit, sans-serif;">
                @yield('page_title', __('Dashboard'))
            </h1>
        </div>

        <nav class="mx-auto hidden min-w-0 flex-1 items-center justify-center gap-1 md:flex" aria-label="{{ __('Primary admin navigation') }}">
            <a
                href="{{ route('admin.dashboard') }}"
                @class([
                    'inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-semibold transition',
                    'bg-brand-navy text-white shadow-sm shadow-brand-navy/20' => $dashActive,
                    'text-brand-navy hover:bg-[#F4F6F9]' => ! $dashActive,
                ])
            >
                <svg class="h-[1.05rem] w-[1.05rem] shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                </svg>
                {{ __('Dashboard') }}
            </a>
            <span class="inline-flex cursor-not-allowed items-center gap-2 rounded-full px-3 py-2 text-sm font-medium text-brand-navy/35" title="{{ __('Coming soon') }}">
                <svg class="h-[1.05rem] w-[1.05rem] shrink-0 opacity-60" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.495 3.255 8.25 4.21 8.25 5.25v.75H12M8.25 5.25v-.75A2.25 2.25 0 0110.5 3h1.5m-6 18.75v-1.5a2.25 2.25 0 012.25-2.25h7.5A2.25 2.25 0 0121 20.25v1.5" />
                </svg>
                {{ __('Orders') }}
            </span>
            <span class="inline-flex cursor-not-allowed items-center gap-2 rounded-full px-3 py-2 text-sm font-medium text-brand-navy/35" title="{{ __('Coming soon') }}">
                <svg class="h-[1.05rem] w-[1.05rem] shrink-0 opacity-60" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zm9 0c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v6.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125v-6.75zM3 4.125C3 3.504 3.504 3 4.125 3h2.25C7.004 3 7.5 3.504 7.5 4.125v6.75C7.5 11.496 7.004 12 6.375 12h-2.25A1.125 1.125 0 013 10.875v-6.75zm9 0c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v6.75c0 .621-.504 1.125-1.125 1.125h-2.25A1.125 1.125 0 019 10.875v-6.75z" />
                </svg>
                {{ __('Sales report') }}
            </span>
        </nav>

        <div class="flex shrink-0 items-center gap-1.5 sm:gap-2" dir="ltr">
            <button
                type="button"
                class="relative flex h-9 w-9 items-center justify-center rounded-full bg-[#F4F6F9] text-brand-navy/70 transition hover:bg-[#E8ECF2] hover:text-brand-navy sm:h-10 sm:w-10"
                aria-label="{{ __('Notifications') }}"
            >
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                </svg>
                <span class="absolute end-1.5 top-1.5 h-2 w-2 rounded-full bg-red-500 ring-2 ring-[#F4F6F9]" aria-hidden="true"></span>
            </button>

            <button
                type="button"
                class="flex h-9 w-9 items-center justify-center rounded-full bg-[#F4F6F9] text-[10px] font-bold tracking-wide text-brand-navy/80 transition hover:bg-[#E8ECF2] sm:h-10 sm:w-10 sm:text-[11px]"
                aria-label="{{ __('English') }}"
            >
                EN
            </button>

            <div class="relative ms-0.5 min-w-0 max-w-[min(100%,15rem)] sm:max-w-[18rem]" data-admin-user-menu>
                <button
                    type="button"
                    id="admin-user-menu-btn"
                    class="flex w-full min-w-0 items-center gap-2.5 rounded-xl border border-transparent py-1 pe-1 ps-1.5 text-start transition hover:bg-[#F4F6F9] sm:gap-3 sm:ps-2"
                    aria-expanded="false"
                    aria-haspopup="true"
                    aria-controls="admin-user-menu-panel"
                >
                    <div class="min-w-0 flex-1 overflow-hidden">
                        <p class="truncate text-sm font-semibold text-brand-navy">{{ $authUser->name ?: $authUser->email }}</p>
                        <p class="truncate text-xs capitalize text-brand-navy/50">{{ $authUser->type }}</p>
                    </div>
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center overflow-hidden rounded-lg bg-gradient-to-br from-brand-navy/8 to-brand-navy/4 ring-1 ring-brand-navy/10 sm:h-10 sm:w-10">
                        <svg class="h-5 w-5 text-brand-navy/35" fill="none" viewBox="0 0 24 24" stroke-width="1.25" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                    </span>
                    <svg class="hidden h-4 w-4 shrink-0 text-brand-navy/35 sm:block" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                    </svg>
                </button>

                <div
                    id="admin-user-menu-panel"
                    class="absolute end-0 top-full z-50 mt-2 hidden min-w-[13rem] rounded-xl border border-brand-navy/10 bg-white py-1 shadow-lg shadow-brand-navy/10 ring-1 ring-black/5"
                    role="menu"
                    aria-labelledby="admin-user-menu-btn"
                >
                    @can('admin.users.edit')
                        <a
                            href="{{ route('admin.users.edit', $authUser) }}"
                            class="flex items-center gap-2.5 px-4 py-2.5 text-sm font-medium text-brand-navy transition hover:bg-brand-page"
                            role="menuitem"
                        >
                            <svg class="h-5 w-5 text-brand-navy/45" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                            {{ __('Profile') }}
                        </a>
                        <div class="border-t border-brand-navy/10" role="separator"></div>
                    @endcan

                    <form method="post" action="{{ route('admin.auth.logout') }}" role="none">
                        @csrf
                        <button
                            type="submit"
                            class="flex w-full items-center gap-2.5 px-4 py-2.5 text-start text-sm font-medium text-brand-navy transition hover:bg-brand-page"
                            role="menuitem"
                        >
                            <svg class="h-5 w-5 text-brand-navy/45" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
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
        (function () {
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

            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                toggle();
            });

            document.addEventListener('click', function (e) {
                if (!root.contains(e.target)) close();
            });

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') close();
            });
        })();
    </script>
@endpush
