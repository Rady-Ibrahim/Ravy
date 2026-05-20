@extends('layouts.admin')

@section('title', __('Brands'))
@section('page_title', __('Brands'))
@section('page_subtitle', __('Manage product brands.'))

@section('content')
    <div class="mx-auto max-w-6xl">
        <div class="mb-4">
            <a href="{{ route('admin.brands.create') }}"
                class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800">{{ __('Add brand') }}</a>
        </div>
        <div class="admin-card overflow-hidden border-0 p-0 shadow-md shadow-slate-200/50">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[560px] text-left text-sm">
                    <thead
                        class="border-b border-slate-200 bg-slate-50/80 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-4 py-3">{{ __('Logo') }}</th>
                            <th class="px-4 py-3">{{ __('Name') }}</th>
                            <th class="px-4 py-3">{{ __('Slug') }}</th>
                            <th class="px-4 py-3">{{ __('Status') }}</th>
                            <th class="px-4 py-3 text-end">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($brands as $brand)
                            <tr class="bg-white hover:bg-slate-50/50">
                                <td class="px-4 py-3">
                                    @if ($brand->logo)
                                        <img src="{{ asset('public/storage/' . $brand->logo) }}" alt=""
                                            class="h-10 w-10 rounded-lg object-cover ring-1 ring-slate-200">
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">{{ $brand->name }}</td>
                                <td class="px-4 py-3">{{ $brand->slug }}</td>
                                <td class="px-4 py-3">{{ $brand->is_active ? __('Active') : __('Inactive') }}</td>
                                <td class="px-4 py-3 text-end">
                                    <a href="{{ route('admin.brands.edit', $brand) }}"
                                        class="rounded-lg px-2 py-1 text-xs font-semibold text-amber-700 hover:bg-amber-50">{{ __('Edit') }}</a>
                                    <form action="{{ route('admin.brands.destroy', $brand) }}" method="post"
                                        class="inline" onsubmit="return confirm(@json(__('Delete this brand?')));">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="rounded-lg px-2 py-1 text-xs font-semibold text-red-600 hover:bg-red-50">{{ __('Delete') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-slate-500">
                                    {{ __('No brands found.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($brands->hasPages())
                <div class="border-t border-slate-100 px-4 py-3">{{ $brands->withQueryString()->links() }}</div>
            @endif
        </div>
    </div>
@endsection
