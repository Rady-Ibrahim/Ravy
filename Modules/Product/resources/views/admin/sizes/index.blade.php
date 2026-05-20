@extends('layouts.admin')

@section('title', __('Sizes'))

@section('page_title', __('Sizes'))
@section('page_subtitle', __('Manage product sizes and variants.'))

@section('content')
    <div class="mx-auto max-w-7xl">
        <div class="mb-4">
            <a href="{{ route('admin.sizes.create') }}"
                class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800">
                <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                    aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 4.5v15m7.5-7.5L12 12m0 0l7.5 7.5M12 12v-7.5" />
                </svg>
                {{ __('Add Size') }}
            </a>
        </div>

        <div class="admin-card overflow-hidden border-0 p-0 shadow-md shadow-slate-200/50">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[680px] text-left text-sm">
                    <thead
                        class="border-b border-slate-200 bg-slate-50/80 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-4 py-3">{{ __('Image') }}</th>
                            <th class="px-4 py-3">{{ __('Name') }}</th>
                            <th class="px-4 py-3">{{ __('Code') }}</th>
                            <th class="px-4 py-3">{{ __('Code Range') }}</th>
                            <th class="px-4 py-3 text-end">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($sizes as $size)
                            <tr class="bg-white hover:bg-slate-50/50">
                                <td class="px-4 py-3">
                                    @if ($size->image)
                                        <img src="{{ asset('storage/' . $size->image) }}" alt="{{ $size->name }}"
                                            class="h-10 w-10 rounded-lg object-cover">
                                    @else
                                        <div class="h-10 w-10 rounded-lg bg-slate-200 flex items-center justify-center">
                                            <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 01-1.125-1.125M3.375 4.5h17.25M3.375 4.5a1.125 1.125 0 011.125-1.125m0 3.75h17.25a1.125 1.125 0 001.125-1.125m-17.25 0a1.125 1.125 0 011.125-1.125m0 3.75v-4.5m0 4.5a1.125 1.125 0 001.125-1.125m-1.125-1.125v-4.5" />
                                            </svg>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 font-medium text-slate-900">{{ $size->name }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $size->code }}</td>
                                <td class="px-4 py-3 text-slate-600">
                                    @if ($size->code_from || $size->code_to)
                                        {{ $size->code_from }} - {{ $size->code_to }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-end">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.sizes.edit', $size) }}"
                                            class="rounded-lg px-2 py-1 text-xs font-semibold text-amber-700 hover:bg-amber-50">{{ __('Edit') }}</a>
                                        <form action="{{ route('admin.sizes.destroy', $size) }}" method="post"
                                            class="inline" onsubmit="return confirm(@json(__('Delete this size?')));">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="rounded-lg px-2 py-1 text-xs font-semibold text-red-600 hover:bg-red-50">{{ __('Delete') }}</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-slate-500">{{ __('No sizes found.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($sizes->hasPages())
                <div class="border-t border-slate-100 px-4 py-3">
                    {{ $sizes->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
