@extends('layouts.admin')

@section('title', __('Products'))
@section('page_title', __('Products'))
@section('page_subtitle', __('Manage catalog products.'))

@section('content')
    <div class="mx-auto max-w-7xl">
        <div class="mb-6 flex items-center justify-between">
            @can('admin.products.create')
                <a href="{{ route('admin.products.create') }}"
                    class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800">
                    {{ __('Add product') }}
                </a>
            @endcan
        </div>

        <!-- Filters bar -->
        <div class="admin-card mb-6 border-0 p-0 shadow-md shadow-slate-200/50">
            <div class="border-b border-slate-100 px-4 py-4 sm:px-6">
                <form method="GET" action="{{ route('admin.products.index') }}" class="space-y-4">
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-6">
                        <!-- Search -->
                        <div class="lg:col-span-2">
                            <input type="text" name="q" value="{{ request('q') }}"
                                placeholder="{{ __('Search by name or slug...') }}"
                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:ring focus:ring-slate-200 transition-all">
                        </div>

                        <!-- Status filter -->
                        <div>
                            <select name="is_active"
                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:ring focus:ring-slate-200 transition-all">
                                <option value="">{{ __('All statuses') }}</option>
                                <option value="1" @selected(request('is_active') === '1')>{{ __('Active') }}</option>
                                <option value="0" @selected(request('is_active') === '0')>{{ __('Inactive') }}</option>
                            </select>
                        </div>

                        <!-- Featured filter -->
                        <div>
                            <select name="is_featured"
                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:ring focus:ring-slate-200 transition-all">
                                <option value="">{{ __('All products') }}</option>
                                <option value="1" @selected(request('is_featured') === '1')>{{ __('Featured') }}</option>
                                <option value="0" @selected(request('is_featured') === '0')>{{ __('Not featured') }}</option>
                            </select>
                        </div>

                        <!-- Category filter -->
                        <div>
                            <select name="category_id"
                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:ring focus:ring-slate-200 transition-all">
                                <option value="">{{ __('All categories') }}</option>
                                @php $categories = \Modules\Category\Models\Category::orderBy('name')->get(); @endphp
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}" @selected(request('category_id') == $cat->id)>{{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Apply button -->
                        <div class="flex gap-2">
                            <button type="submit"
                                class="flex-1 rounded-lg bg-slate-900 px-3 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
                                {{ __('Filter') }}
                            </button>
                            @if (request()->anyFilled(['q', 'is_active', 'is_featured', 'category_id']))
                                <a href="{{ route('admin.products.index') }}"
                                    class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                                    {{ __('Clear') }}
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="admin-card overflow-hidden border-0 p-0 shadow-md shadow-slate-200/50">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[760px] text-left text-sm">
                    <thead
                        class="border-b border-slate-200 bg-slate-50/80 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-4 py-3">{{ __('Name') }}</th>
                            <th class="px-4 py-3">{{ __('Image') }}</th>
                            <th class="px-4 py-3">{{ __('Brand') }}</th>
                            <th class="px-4 py-3">{{ __('Primary category') }}</th>
                            <th class="px-4 py-3">{{ __('Price range') }}</th>
                            <th class="px-4 py-3">{{ __('Variants') }}</th>
                            <th class="px-4 py-3">{{ __('Views') }}</th>
                            <th class="px-4 py-3">{{ __('Score') }}</th>
                            <th class="px-4 py-3">{{ __('Status') }}</th>
                            <th class="px-4 py-3 text-end">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($products as $product)
                            <tr class="bg-white hover:bg-slate-50/50">
                                <td class="px-4 py-3 font-medium text-slate-900">{{ $product->name }}</td>
                                <td class="px-4 py-3">
                                    @php $coverImage = $product->images->firstWhere('is_primary', true) ?? $product->images->first(); @endphp
                                    @if ($coverImage)
                                        <img src="{{ asset('storage/' . $coverImage->path) }}" alt=""
                                            class="h-10 w-10 rounded-lg object-cover ring-1 ring-slate-200">
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-slate-600">{{ $product->brand?->name ?? '—' }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $product->primaryCategory?->name ?? '—' }}</td>
                                <td class="px-4 py-3 text-slate-600">
                                    @if ($product->min_price !== null || $product->max_price !== null)
                                        {{ $product->min_price ?? '—' }} - {{ $product->max_price ?? '—' }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-slate-600">{{ $product->variants_count }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $product->views_count ?? 0 }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $product->score }}</td>
                                <td class="px-4 py-3">
                                    <span
                                        class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium ring-1 ring-inset {{ $product->is_active ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20' : 'bg-slate-100 text-slate-600 ring-slate-500/10' }}">
                                        {{ $product->is_active ? __('Active') : __('Inactive') }}
                                    </span>
                                    @if ($product->is_featured)
                                        <span
                                            class="ms-1 inline-flex rounded-full px-2 py-0.5 text-xs font-medium ring-1 ring-inset bg-amber-50 text-amber-700 ring-amber-600/20">{{ __('Featured') }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-end">
                                    @can('admin.products.edit')
                                        <a href="{{ route('admin.products.edit', $product) }}"
                                            class="rounded-lg px-2 py-1 text-xs font-semibold text-amber-700 hover:bg-amber-50">{{ __('Edit') }}</a>
                                        <a href="{{ route('admin.products.variants.index', $product) }}"
                                            class="rounded-lg px-2 py-1 text-xs font-semibold text-indigo-700 hover:bg-indigo-50">{{ __('Variants') }}</a>
                                    @endcan
                                    @can('admin.products.delete')
                                        <form action="{{ route('admin.products.destroy', $product) }}" method="post"
                                            class="inline" onsubmit="return confirm(@json(__('Delete this product?')));">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="rounded-lg px-2 py-1 text-xs font-semibold text-red-600 hover:bg-red-50">{{ __('Delete') }}</button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-4 py-8 text-center text-slate-500">
                                    {{ __('No products found.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($products->hasPages())
                <div class="border-t border-slate-100 px-4 py-3">
                    {{ $products->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
