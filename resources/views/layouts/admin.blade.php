<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — {{ config('app.name', 'Ravy') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/admin.css'])
    @endif
    @stack('head')
</head>
<body class="h-full bg-slate-100 text-slate-900">
    <div class="admin-shell flex min-h-full">
        {{-- Mobile overlay --}}
        <div
            id="admin-sidebar-backdrop"
            class="fixed inset-0 z-40 bg-slate-900/60 opacity-0 pointer-events-none transition-opacity duration-200 lg:hidden"
            aria-hidden="true"
        ></div>

        @include('partials.admin.sidebar')

        <div class="flex min-w-0 flex-1 flex-col lg:pl-0">
            @include('partials.admin.topbar')

            <main class="flex-1 overflow-y-auto px-4 py-6 sm:px-6 lg:px-8">
                @if (session('status'))
                    <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900" role="status">
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
            var openBtn = document.getElementById('admin-sidebar-open');
            var closeBtn = document.getElementById('admin-sidebar-close');
            var backdrop = document.getElementById('admin-sidebar-backdrop');

            function openSidebar() {
                if (!sidebar || !backdrop) return;
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('opacity-0', 'pointer-events-none');
                document.body.classList.add('overflow-hidden', 'lg:overflow-auto');
            }

            function closeSidebar() {
                if (!sidebar || !backdrop) return;
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('opacity-0', 'pointer-events-none');
                document.body.classList.remove('overflow-hidden', 'lg:overflow-auto');
            }

            openBtn && openBtn.addEventListener('click', openSidebar);
            closeBtn && closeBtn.addEventListener('click', closeSidebar);
            backdrop && backdrop.addEventListener('click', closeSidebar);
        })();
    </script>
    @stack('scripts')
</body>
</html>
