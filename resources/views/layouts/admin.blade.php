<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if (file_exists(public_path('images/brand/ravy-logo.png')))
        <link rel="icon" type="image/png" href="{{ asset('images/brand/ravy-logo.png') }}">
    @endif
    @hasSection('title')
        <title>@yield('title') — {{ config('app.name', 'Ravy Boutique') }}</title>
    @else
        <title>{{ config('app.name', 'Ravy Boutique') }}</title>
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700&family=Outfit:wght@500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Tajawal:wght@400;500;600;700&display=swap" rel="stylesheet">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/admin.css'])
    @endif
    @stack('head')
</head>
<body class="h-full bg-brand-page text-slate-900" style="font-family: 'Tajawal', 'Plus Jakarta Sans', system-ui, sans-serif;">
    <div class="admin-shell flex min-h-full">
        {{-- Mobile overlay --}}
        <div
            id="admin-sidebar-backdrop"
            class="fixed inset-0 z-40 bg-brand-navy/55 opacity-0 pointer-events-none transition-opacity duration-200 lg:hidden"
            aria-hidden="true"
        ></div>

        @include('partials.admin.sidebar')

        <div id="admin-main" class="flex min-w-0 flex-1 flex-col transition-[margin] duration-200 ease-out lg:ml-[17.5rem]">
            @include('partials.admin.topbar')

            @hasSection('page_subtitle')
                <div class="border-b border-brand-navy/5 bg-white/95 px-4 py-2 sm:px-6 lg:px-8">
                    <p class="text-sm text-brand-navy/50">@yield('page_subtitle')</p>
                </div>
            @endif

            <main class="min-w-0 flex-1 overflow-x-hidden overflow-y-auto px-4 py-6 sm:px-6 lg:px-8">
                @if (session('status'))
                    <div class="mb-6 rounded-xl border border-brand-navy/15 bg-brand-cream/80 px-4 py-3 text-sm text-brand-navy" role="status">
                        {{ session('status') }}
                    </div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        (function () {
            var sidebar = document.getElementById('admin-sidebar');
            var toggleBtn = document.getElementById('admin-sidebar-toggle');
            var closeBtn = document.getElementById('admin-sidebar-close');
            var backdrop = document.getElementById('admin-sidebar-backdrop');
            var STORAGE_KEY = 'adminSidebarCollapsed';

            function isLg() {
                return window.matchMedia('(min-width: 1024px)').matches;
            }

            function openMobileSidebar() {
                if (!sidebar || !backdrop) return;
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('opacity-0', 'pointer-events-none');
                document.body.classList.add('overflow-hidden', 'lg:overflow-auto');
            }

            function closeMobileSidebar() {
                if (!sidebar || !backdrop) return;
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('opacity-0', 'pointer-events-none');
                document.body.classList.remove('overflow-hidden', 'lg:overflow-auto');
            }

            function applyDesktopCollapsed(collapsed) {
                var root = document.documentElement;
                if (collapsed) root.classList.add('admin-sidebar-collapsed');
                else root.classList.remove('admin-sidebar-collapsed');
                try {
                    localStorage.setItem(STORAGE_KEY, collapsed ? '1' : '0');
                } catch (e) {}
            }

            function syncAfterResize() {
                if (!sidebar || !backdrop) return;
                if (isLg()) {
                    closeMobileSidebar();
                    sidebar.classList.remove('-translate-x-full');
                    try {
                        if (localStorage.getItem(STORAGE_KEY) === '1') {
                            document.documentElement.classList.add('admin-sidebar-collapsed');
                        } else {
                            document.documentElement.classList.remove('admin-sidebar-collapsed');
                        }
                    } catch (e) {}
                } else {
                    document.documentElement.classList.remove('admin-sidebar-collapsed');
                    sidebar.classList.add('-translate-x-full');
                }
            }

            if (toggleBtn && sidebar) {
                toggleBtn.addEventListener('click', function () {
                    if (isLg()) {
                        var collapsed = document.documentElement.classList.contains('admin-sidebar-collapsed');
                        applyDesktopCollapsed(!collapsed);
                    } else {
                        if (sidebar.classList.contains('-translate-x-full')) openMobileSidebar();
                        else closeMobileSidebar();
                    }
                });
            }

            closeBtn && closeBtn.addEventListener('click', closeMobileSidebar);
            backdrop && backdrop.addEventListener('click', closeMobileSidebar);

            window.addEventListener('resize', function () {
                clearTimeout(window.__adminSidebarResizeT);
                window.__adminSidebarResizeT = setTimeout(syncAfterResize, 120);
            });

            if (isLg() && localStorage.getItem(STORAGE_KEY) === '1') {
                document.documentElement.classList.add('admin-sidebar-collapsed');
            }
        })();
    </script>
    @stack('scripts')
</body>
</html>
