@extends('layouts.admin')

@section('title', $pageTitle ?? __('Attributes'))
@section('page_title', $pageTitle ?? __('Attributes'))
@section('page_subtitle', __('Manage attributes like color and size.'))

@section('content')
    <div class="mx-auto max-w-7xl">
        <div class="mb-4 flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.attributes.create') }}" class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800">{{ __('Add attribute') }}</a>
            <a href="{{ route('admin.attributes.colors') }}" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm">{{ __('Colors') }}</a>
            <a href="{{ route('admin.attributes.sizes') }}" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm">{{ __('Sizes') }}</a>
        </div>
        <div class="admin-card overflow-hidden border-0 p-0 shadow-md shadow-slate-200/50">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[780px] text-left text-sm">
                    <thead class="border-b border-slate-200 bg-slate-50/80 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr><th class="px-4 py-3">{{ __('Name') }}</th><th class="px-4 py-3">{{ __('Code') }}</th><th class="px-4 py-3">{{ __('Category') }}</th><th class="px-4 py-3">{{ __('Values') }}</th><th class="px-4 py-3 text-end">{{ __('Actions') }}</th></tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($attributes as $attribute)
                            <tr class="bg-white hover:bg-slate-50/50">
                                <td class="px-4 py-3">{{ $attribute->name }}</td>
                                <td class="px-4 py-3">{{ $attribute->code }}</td>
                                <td class="px-4 py-3">{{ $attribute->category?->name }}</td>
                                <td class="px-4 py-3">{{ $attribute->values_count }}</td>
                                <td class="px-4 py-3 text-end">
                                    <a href="{{ route('admin.attributes.values.index', $attribute) }}" class="rounded-lg px-2 py-1 text-xs font-semibold text-indigo-700 hover:bg-indigo-50">{{ __('Values') }}</a>
                                    <a href="{{ route('admin.attributes.edit', $attribute) }}" class="rounded-lg px-2 py-1 text-xs font-semibold text-amber-700 hover:bg-amber-50">{{ __('Edit') }}</a>
                                    <form action="{{ route('admin.attributes.destroy', $attribute) }}" method="post" class="inline" onsubmit="return confirm(@json(__('Delete this attribute?')));">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="rounded-lg px-2 py-1 text-xs font-semibold text-red-600 hover:bg-red-50">{{ __('Delete') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-4 py-8 text-center text-slate-500">{{ __('No attributes found.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($attributes->hasPages())<div class="border-t border-slate-100 px-4 py-3">{{ $attributes->withQueryString()->links() }}</div>@endif
        </div>
    </div>
@endsection
