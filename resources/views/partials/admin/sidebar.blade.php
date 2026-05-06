@php
    $registeredPermissions = \Spatie\Permission\Models\Permission::query()
        ->where('guard_name', 'web')
        ->pluck('name')
        ->flip();

    $mainNav = [
        ['label' => __('Dashboard'), 'route' => 'admin.dashboard', 'icon' => 'home', 'permission' => null],
        ['label' => __('Users'), 'route' => 'admin.users.index', 'icon' => 'users', 'permission' => 'admin.users.view'],
        ['label' => __('Orders'), 'route' => 'admin.orders.index', 'icon' => 'shopping-cart', 'permission' => null],
        ['label' => __('Shipping Rates'), 'route' => 'admin.governorates.index', 'icon' => 'truck', 'permission' => null],

        [
            'label' => __('Roles'),
            'icon' => 'shield',
            'permission' => null,
            'children' => [
                ['label' => __('Roles'), 'route' => 'admin.roles.index', 'permission' => 'admin.roles.view'],
                ['label' => __('Permission matrix'), 'route' => 'admin.roles.matrix', 'permission' => 'admin.matrix.manage'],
            ],
        ],
        ['label' => __('Categories'), 'route' => 'admin.categories.index', 'icon' => 'layers', 'permission' => 'admin.categories.view'],

        [
            'label' => __('Products'),
            'icon' => 'box',
            'permission' => null,
            'children' => [
                ['label' => __('All products'), 'route' => 'admin.products.index', 'permission' => 'admin.products.view'],
                ['label' => __('Brands'), 'route' => 'admin.brands.index', 'permission' => 'admin.products.view'],
                ['label' => __('Attributes'), 'route' => 'admin.attributes.index', 'permission' => 'admin.products.view'],
                ['label' => __('Colors'), 'route' => 'admin.attributes.colors', 'permission' => 'admin.products.view'],
                ['label' => __('Sizes'), 'route' => 'admin.attributes.sizes', 'permission' => 'admin.products.view'],
            ],
        ],

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
            @php
                $canAccess = function (?string $permission) use ($registeredPermissions): bool {
                    if (! $permission) {
                        return true;
                    }

                    return $registeredPermissions->has($permission) && auth()->user()->can($permission);
                };
            @endphp

            @if (!empty($item['children']))
                @php
                    $visibleChildren = collect($item['children'])->filter(fn ($child) => $canAccess($child['permission'] ?? null))->values();
                @endphp
                @if ($visibleChildren->isEmpty())
                    @continue
                @endif
                @php
                    $groupOpen = ($item['label'] === __('Roles') && request()->routeIs('admin.roles.*'))
                        || ($item['label'] === __('Products') && (request()->routeIs('admin.products.*') || request()->routeIs('admin.brands.*') || request()->routeIs('admin.attributes.*')));
                @endphp
                <details class="group" @if($groupOpen) open @endif>
                    <summary class="admin-nav-link cursor-pointer list-none">
                        @if ($item['icon'] === 'shield')
                            <svg class="h-5 w-5 shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                            </svg>
                        @else
                            <svg class="h-5 w-5 shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-8.25 4.5-8.25-4.5m16.5 0L12 3 3.75 7.5m16.5 0v9l-8.25 4.5-8.25-4.5v-9m8.25 4.5v9" />
                            </svg>
                        @endif
                        <span>{{ $item['label'] }}</span>
                        <svg class="ms-auto h-4 w-4 opacity-60 transition group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </summary>
                    <div class="mt-1 space-y-1 ps-7">
                        @foreach ($visibleChildren as $child)
                            @php
                                $childActive = request()->routeIs(\Illuminate\Support\Str::beforeLast($child['route'], '.').'.*');
                            @endphp
                            <a href="{{ route($child['route']) }}" @class(['admin-nav-link text-sm', 'admin-nav-link--active' => $childActive])>
                                {{ $child['label'] }}
                            </a>
                        @endforeach
                    </div>
                </details>
                @continue
            @endif

            @if (! $canAccess($item['permission'] ?? null))
                @continue
            @endif

            @php $active = $item['route'] === 'admin.dashboard' ? request()->routeIs('admin.dashboard') : request()->routeIs(\Illuminate\Support\Str::beforeLast($item['route'], '.').'.*'); @endphp
            <a href="{{ route($item['route']) }}" @class(['admin-nav-link', 'admin-nav-link--active' => $active])>
                @if ($item['icon'] === 'home')
                    <svg class="h-5 w-5 shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>
                @elseif ($item['icon'] === 'users')
                    <svg class="h-5 w-5 shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                    </svg>
                @elseif ($item['icon'] === 'layers')
                    <svg class="h-5 w-5 shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3.75 3.75 8.25 12 12.75l8.25-4.5L12 3.75Zm0 9 8.25-4.5M12 12.75v7.5m0-7.5-8.25-4.5m8.25 12 8.25-4.5m-8.25 4.5-8.25-4.5" />
                    </svg>
                @elseif ($item['icon'] === 'box')
                    <svg class="h-5 w-5 shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-8.25 4.5-8.25-4.5m16.5 0L12 3 3.75 7.5m16.5 0v9l-8.25 4.5-8.25-4.5v-9m8.25 4.5v9" />
                    </svg>
                @elseif ($item['icon'] === 'shopping-cart')
                    <svg class="h-5 w-5 shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                    </svg>
                @elseif ($item['icon'] === 'truck')
                    <svg class="h-5 w-5 shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0V15.75a1.5 1.5 0 013 0v3zM13.5 18.75a1.5 1.5 0 01-3 0V15.75a1.5 1.5 0 013 0v3zM18.75 18.75a1.5 1.5 0 01-3 0V15.75a1.5 1.5 0 013 0v3z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8.25v-1.5m0 1.5c-1.355 0-2.5.42-3.375 1.125m1.875-1.125c1.355 0 2.5.42 3.375 1.125m-6.75 0c.375.375.75.75 1.125 1.125m4.5 0c.375-.375.75-.75 1.125-1.125M12 8.25v-1.5m0 1.5c.375 0 .75.075 1.125.225M12 8.25c.375 0 .75-.075 1.125-.225M12 8.25c.375.375.75.75 1.125 1.125m-2.25 0C10.5 9.75 9.75 10.5 9.75 12v1.5m0-1.5c0-1.5 1.5-3 3-3m0 3c1.5 0 3 1.5 3 3v1.5m0-1.5c0-1.5-1.5-3-3-3" />
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
