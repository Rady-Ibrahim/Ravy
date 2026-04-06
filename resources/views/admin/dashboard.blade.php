@extends('layouts.admin')

@section('page_title', __('Dashboard'))

@section('content')
    @php
        // Placeholders until e-commerce modules ship
        $kpi = [
            'customers' => null,
            'products' => null,
            'orders' => null,
            'revenue' => null,
        ];
        $orders = [
            'total' => null,
            'pending' => null,
            'confirmed' => null,
            'processed' => null,
            'shipped' => null,
        ];
        $categories = [
            ['label' => __('Fashion'), 'value' => null, 'pct' => 0],
            ['label' => __('Accessories'), 'value' => null, 'pct' => 0],
            ['label' => __('Home'), 'value' => null, 'pct' => 0],
        ];
        $dashShadow = 'shadow-[0_10px_40px_-12px_rgba(0,27,54,0.14),0_4px_14px_-4px_rgba(0,27,54,0.08)]';
    @endphp

    <div class="mx-auto min-w-0 max-w-7xl space-y-6">
        {{-- KPI row: مربّعين في الصف حتى xl، ثم 4 في صف واحد على الشاشات العريضة --}}
        <div class="grid grid-cols-2 gap-3 sm:gap-4 xl:grid-cols-4">
            <div class="admin-card group border border-brand-navy/[0.07] !p-4 sm:!p-5 {{ $dashShadow }} transition hover:shadow-[0_14px_44px_-12px_rgba(0,27,54,0.18),0_6px_16px_-4px_rgba(0,27,54,0.1)]">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-brand-navy/45">{{ __('Total customers') }}</p>
                        <p class="mt-2 text-3xl font-bold tabular-nums text-brand-navy" style="font-family: Outfit, sans-serif;">{{ $kpi['customers'] ?? '—' }}</p>
                        <p class="mt-1 text-xs text-brand-navy/40">{{ __('CRM module') }}</p>
                    </div>
                    <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-brand-navy/8 text-brand-navy">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                        </svg>
                    </span>
                </div>
            </div>
            <div class="admin-card group border border-brand-navy/[0.07] !p-4 sm:!p-5 {{ $dashShadow }} transition hover:shadow-[0_14px_44px_-12px_rgba(0,27,54,0.18),0_6px_16px_-4px_rgba(0,27,54,0.1)]">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-brand-navy/45">{{ __('Products') }}</p>
                        <p class="mt-2 text-3xl font-bold tabular-nums text-brand-navy" style="font-family: Outfit, sans-serif;">{{ $kpi['products'] ?? '—' }}</p>
                        <p class="mt-1 text-xs text-brand-navy/40">{{ __('Catalog module') }}</p>
                    </div>
                    <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-[#e8dfd4]/80 text-brand-navy">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                        </svg>
                    </span>
                </div>
            </div>
            <div class="admin-card group border border-brand-navy/[0.07] !p-4 sm:!p-5 {{ $dashShadow }} transition hover:shadow-[0_14px_44px_-12px_rgba(0,27,54,0.18),0_6px_16px_-4px_rgba(0,27,54,0.1)]">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-brand-navy/45">{{ __('Orders') }}</p>
                        <p class="mt-2 text-3xl font-bold tabular-nums text-brand-navy" style="font-family: Outfit, sans-serif;">{{ $kpi['orders'] ?? '—' }}</p>
                        <p class="mt-1 text-xs text-brand-navy/40">{{ __('Sales pipeline') }}</p>
                    </div>
                    <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-[#dfe8e4]/90 text-brand-navy">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.495 3.255 8.25 4.21 8.25 5.25v.75H12M8.25 5.25v-.75A2.25 2.25 0 0110.5 3h1.5m-6 18.75v-1.5a2.25 2.25 0 012.25-2.25h7.5A2.25 2.25 0 0121 20.25v1.5" />
                        </svg>
                    </span>
                </div>
            </div>
            <div class="admin-card group relative overflow-hidden border border-brand-navy/[0.07] !p-4 sm:!p-5 {{ $dashShadow }} transition hover:shadow-[0_14px_44px_-12px_rgba(0,27,54,0.18),0_6px_16px_-4px_rgba(0,27,54,0.1)]">
                <div class="relative flex items-start justify-between gap-3">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-brand-navy/45">{{ __('Revenue') }}</p>
                        <p class="mt-2 text-2xl font-bold tabular-nums text-brand-navy sm:text-3xl" style="font-family: Outfit, sans-serif;">
                            {{ $kpi['revenue'] ?? '—' }}@if ($kpi['revenue']) <span class="text-lg font-semibold text-brand-navy/50">ج.م</span>@else <span class="text-sm font-medium text-brand-navy/35">ج.م</span>@endif
                        </p>
                        <p class="mt-1 text-xs text-brand-navy/40">{{ __('All channels') }}</p>
                    </div>
                    <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-brand-navy text-brand-cream shadow-md shadow-brand-navy/25">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                </div>
            </div>
        </div>

        {{-- Orders + categories: عمودان من sm --}}
        <div class="grid min-w-0 grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-5">
            <div class="min-w-0 space-y-3 sm:space-y-4">
                <div class="flex flex-wrap items-end justify-between gap-2">
                    <div>
                        <h3 class="admin-section-head">{{ __('Order overview') }}</h3>
                        <p class="admin-section-sub">{{ __('Status mix at a glance') }}</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 sm:items-start sm:gap-4">
                    <div class="self-start rounded-2xl border border-brand-navy/10 bg-white p-4 sm:p-5 {{ $dashShadow }}">
                        <p class="text-xs font-medium text-brand-navy/50">{{ __('Total orders') }}</p>
                        <p class="mt-2 min-h-[2.5rem] text-3xl font-bold leading-tight tabular-nums text-brand-navy sm:text-4xl" style="font-family: Outfit, sans-serif;">{{ $orders['total'] ?? '—' }}</p>
                        <a href="#" class="mt-4 inline-flex w-full items-center justify-center rounded-full bg-brand-navy px-4 py-2.5 text-xs font-semibold text-brand-cream shadow-sm shadow-brand-navy/20 transition hover:bg-brand-navy-mid" onclick="return false;">
                            {{ __('All orders') }}
                        </a>
                    </div>
                    <div class="grid grid-cols-1 gap-3">
                        <div class="flex items-center justify-between gap-3 rounded-2xl border border-rose-200/80 bg-gradient-to-r from-rose-50 to-[#fff5f5] px-4 py-3 shadow-sm">
                            <div class="flex items-center gap-2">
                                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-rose-100 text-rose-700">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                </span>
                                <span class="text-sm font-medium text-brand-navy">{{ __('Pending') }}</span>
                            </div>
                            <span class="text-xl font-bold tabular-nums text-brand-navy" style="font-family: Outfit, sans-serif;">{{ $orders['pending'] ?? '—' }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-3 rounded-2xl border border-emerald-200/80 bg-gradient-to-r from-emerald-50 to-[#f0faf4] px-4 py-3 shadow-sm">
                            <span class="text-sm font-medium text-brand-navy">{{ __('Confirmed') }}</span>
                            <span class="text-xl font-bold tabular-nums text-brand-navy" style="font-family: Outfit, sans-serif;">{{ $orders['confirmed'] ?? '—' }}</span>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="rounded-2xl border border-fuchsia-200/70 bg-fuchsia-50/80 px-3 py-3 text-center shadow-sm">
                                <p class="text-[10px] font-semibold uppercase tracking-wide text-brand-navy/50">{{ __('Processed') }}</p>
                                <p class="mt-1 text-lg font-bold text-brand-navy" style="font-family: Outfit, sans-serif;">{{ $orders['processed'] ?? '—' }}</p>
                            </div>
                            <div class="rounded-2xl border border-amber-200/80 bg-amber-50/90 px-3 py-3 text-center shadow-sm">
                                <p class="text-[10px] font-semibold uppercase tracking-wide text-brand-navy/50">{{ __('Shipped') }}</p>
                                <p class="mt-1 text-lg font-bold text-brand-navy" style="font-family: Outfit, sans-serif;">{{ $orders['shipped'] ?? '—' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="min-w-0">
                <div class="admin-card flex min-h-0 min-w-0 flex-col border border-brand-navy/[0.07] !p-4 sm:!p-5 {{ $dashShadow }}">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <h3 class="admin-section-head">{{ __('Top categories') }}</h3>
                            <p class="admin-section-sub">{{ __('By revenue (sample)') }}</p>
                        </div>
                    </div>
                    <ul class="mt-5 space-y-4">
                        @foreach ($categories as $cat)
                            <li>
                                <div class="flex items-center justify-between gap-2 text-sm">
                                    <span class="font-medium text-brand-navy">{{ $cat['label'] }}</span>
                                    <span class="tabular-nums text-brand-navy/55">{{ $cat['value'] ?? '—' }} @if ($cat['value'])<span class="text-xs">ج.م</span>@endif</span>
                                </div>
                                <div class="mt-2 h-2 overflow-hidden rounded-full bg-brand-navy/8">
                                    <div class="h-full rounded-full bg-gradient-to-l from-brand-navy to-brand-navy-soft transition-all" style="width: {{ max(8, $cat['pct']) }}%"></div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                    <p class="mt-4 border-t border-brand-navy/10 pt-3 text-center text-xs text-brand-navy/40">{{ __('Live rankings will populate from reports.') }}</p>
                </div>
            </div>
        </div>

        {{-- Sales spotlight + actions: عمودان من sm --}}
        <div class="grid min-w-0 grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-5">
            <div class="admin-card min-w-0 border border-brand-navy/[0.07] !p-4 sm:!p-5 {{ $dashShadow }}">
                <h3 class="admin-section-head">{{ __('In-house performance') }}</h3>
                <p class="admin-section-sub">{{ __('Snapshot for your boutique channel') }}</p>
                <div class="mt-6 grid min-w-0 grid-cols-1 gap-6 sm:grid-cols-[10rem_minmax(0,1fr)] sm:gap-5">
                    <div class="relative isolate flex justify-center sm:justify-start">
                        <div
                            class="relative h-36 w-36 shrink-0 sm:h-40 sm:w-40"
                            role="img"
                            aria-label="{{ __('Revenue share chart placeholder') }}"
                        >
                            <div
                                class="absolute inset-0 rounded-full shadow-inner shadow-brand-navy/10"
                                style="background: conic-gradient(#001b36 0deg 260deg, #c4a574 260deg 318deg, #dfe8e4 318deg 360deg);"
                            ></div>
                            <div class="absolute inset-[14%] z-10 flex items-center justify-center rounded-full bg-white text-center shadow-md ring-1 ring-brand-navy/5">
                                <div>
                                    <p class="text-[10px] font-semibold uppercase tracking-wide text-brand-navy/45">{{ __('Share') }}</p>
                                    <p class="text-lg font-bold tabular-nums text-brand-navy" style="font-family: Outfit, sans-serif;">—</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="min-w-0">
                        <div class="grid grid-cols-2 gap-2">
                            <div class="min-w-0 rounded-xl border border-brand-navy/10 bg-white px-3 py-2.5 shadow-sm sm:px-4 sm:py-3">
                                <p class="text-[10px] font-semibold uppercase leading-snug text-brand-navy/45">{{ __('In-house products') }}</p>
                                <p class="mt-1 text-lg font-bold tabular-nums text-brand-navy sm:text-xl" style="font-family: Outfit, sans-serif;">—</p>
                            </div>
                            <div class="min-w-0 rounded-xl border border-brand-navy/10 bg-white px-3 py-2.5 shadow-sm sm:px-4 sm:py-3">
                                <p class="text-[10px] font-semibold uppercase leading-snug text-brand-navy/45">{{ __('Avg. rating') }}</p>
                                <p class="mt-1 text-lg font-bold tabular-nums text-brand-navy sm:text-xl" style="font-family: Outfit, sans-serif;">—</p>
                            </div>
                            <div class="col-span-2 min-w-0 rounded-xl border border-brand-navy/10 bg-white px-3 py-2.5 shadow-sm sm:px-4 sm:py-3">
                                <p class="text-[10px] font-semibold uppercase leading-snug text-brand-navy/45">{{ __('Orders (30d)') }}</p>
                                <p class="mt-1 text-lg font-bold tabular-nums text-brand-navy sm:text-xl" style="font-family: Outfit, sans-serif;">—</p>
                            </div>
                        </div>
                    </div>
                </div>
                <a href="#" class="mt-6 flex w-full items-center justify-center rounded-full bg-brand-navy px-4 py-2.5 text-sm font-semibold text-brand-cream transition hover:bg-brand-navy-mid" onclick="return false;">
                    {{ __('In-house orders') }}
                </a>
            </div>

            <div class="admin-card min-w-0 border border-brand-navy/[0.07] !p-4 sm:!p-5 {{ $dashShadow }}">
                <h3 class="admin-section-head">{{ __('Quick actions') }}</h3>
                <p class="admin-section-sub">{{ __('Shortcuts for administrators') }}</p>
                <ul class="mt-5 space-y-2">
                    @can('admin.users.view')
                        <li>
                            <a href="{{ route('admin.users.index') }}" class="group flex items-center justify-between rounded-xl border border-brand-navy/10 bg-brand-page/80 px-4 py-3 text-sm font-medium text-brand-navy transition hover:border-brand-navy/25 hover:bg-brand-cream/50">
                                <span>{{ __('Manage users') }}</span>
                                <svg class="h-4 w-4 text-brand-navy/35 transition group-hover:text-brand-navy" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                </svg>
                            </a>
                        </li>
                    @endcan
                    @can('admin.roles.view')
                        <li>
                            <a href="{{ route('admin.roles.index') }}" class="group flex items-center justify-between rounded-xl border border-brand-navy/10 bg-brand-page/80 px-4 py-3 text-sm font-medium text-brand-navy transition hover:border-brand-navy/25 hover:bg-brand-cream/50">
                                <span>{{ __('Manage roles') }}</span>
                                <svg class="h-4 w-4 text-brand-navy/35 transition group-hover:text-brand-navy" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                                </svg>
                            </a>
                        </li>
                    @endcan
                    @can('admin.matrix.manage')
                        <li>
                            <a href="{{ route('admin.roles.matrix') }}" class="group flex items-center justify-between rounded-xl border border-brand-navy/10 bg-brand-page/80 px-4 py-3 text-sm font-medium text-brand-navy transition hover:border-brand-navy/25 hover:bg-brand-cream/50">
                                <span>{{ __('Permission matrix') }}</span>
                                <svg class="h-4 w-4 text-brand-navy/35 transition group-hover:text-brand-navy" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25A2.25 2.25 0 0113.5 8.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25v-2.25z" />
                                </svg>
                            </a>
                        </li>
                    @endcan
                    <li>
                        <a href="{{ url('/api/documentation') }}" target="_blank" rel="noopener noreferrer" class="group flex items-center justify-between rounded-xl border border-brand-navy/10 bg-brand-page/80 px-4 py-3 text-sm font-medium text-brand-navy transition hover:border-brand-navy/25 hover:bg-brand-cream/50">
                            <span>{{ __('Open API documentation') }}</span>
                            <svg class="h-4 w-4 text-brand-navy/35 transition group-hover:text-brand-navy" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                            </svg>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
@endsection
