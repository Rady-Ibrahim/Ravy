@extends('layouts.admin')

@section('title', __('Categories'))
@section('page_title', __('Categories'))
@section('page_subtitle', __('Manage catalog categories and tree hierarchy.'))

@section('content')
    <div class="mx-auto max-w-7xl">
        <div class="mb-6 flex items-center justify-between">
            @can('admin.categories.create')
                <a href="{{ route('admin.categories.create') }}"
                    class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800">
                    {{ __('Add category') }}
                </a>
            @endcan
        </div>

        <div class="admin-card overflow-hidden border-0 p-0 shadow-md shadow-slate-200/50">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[640px] text-left text-sm">
                    <thead
                        class="border-b border-slate-200 bg-slate-50/80 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-4 py-3">{{ __('Name') }}</th>
                            <th class="px-4 py-3">{{ __('Image') }}</th>
                            <th class="px-4 py-3">{{ __('Parent') }}</th>
                            <th class="px-4 py-3">{{ __('Menu order') }}</th>
                            <th class="px-4 py-3">{{ __('Sort order') }}</th>
                            <th class="px-4 py-3">{{ __('In sidebar') }}</th>
                            <th class="px-4 py-3">{{ __('Products') }}</th>
                            <th class="px-4 py-3">{{ __('Status') }}</th>
                            <th class="px-4 py-3 text-end">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($categories as $category)
                            <tr class="bg-white hover:bg-slate-50/50">
                                <td class="px-4 py-3 font-medium text-slate-900">{{ $category->name }}</td>
                                <td class="px-4 py-3">
                                    @if ($category->image)
                                        <img src="{{ asset('storage/' . $category->image) }}" alt=""
                                            class="h-10 w-10 rounded-lg object-cover ring-1 ring-slate-200">
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-slate-600">{{ $category->parent?->name ?? '—' }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $category->menu_order ?? '—' }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $category->sort_order ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    @if ($category->show_in_sidebar)
                                        <span
                                            class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-emerald-100 text-emerald-700">
                                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </span>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-slate-600 font-medium">{{ $category->products_count }}</td>
                                <td class="px-4 py-3">
                                    <span
                                        class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium ring-1 ring-inset {{ $category->is_active ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20' : 'bg-slate-100 text-slate-600 ring-slate-500/10' }}">
                                        {{ $category->is_active ? __('Active') : __('Inactive') }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-end">
                                    @can('admin.categories.edit')
                                        <a href="{{ route('admin.categories.edit', $category) }}"
                                            class="rounded-lg px-2 py-1 text-xs font-semibold text-amber-700 hover:bg-amber-50">{{ __('Edit') }}</a>
                                    @endcan
                                    @can('admin.categories.delete')
                                        <form action="{{ route('admin.categories.destroy', $category) }}" method="post"
                                            class="inline" onsubmit="return confirm(@json(__('Delete this category?')));">
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
                                <td colspan="9" class="px-4 py-8 text-center text-slate-500">
                                    {{ __('No categories found.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($categories->hasPages())
                <div class="border-t border-slate-100 px-4 py-3">
                    {{ $categories->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
