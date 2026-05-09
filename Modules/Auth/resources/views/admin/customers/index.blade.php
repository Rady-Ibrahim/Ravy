@extends('layouts.admin')

@section('title', __('Customers'))

@section('page_title', __('Customers'))
@section('page_subtitle', __('Manage customer accounts and view their order history.'))

@section('content')
    <div class="mx-auto max-w-7xl">
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-2">
                <div class="h-8 w-1 rounded-full bg-blue-600"></div>
                <h2 class="text-lg font-semibold text-slate-900">{{ __('All Customers') }}</h2>
            </div>
        </div>

        <div class="admin-card overflow-hidden border-0 p-0 shadow-md shadow-slate-200/50">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[640px] text-left text-sm">
                    <thead class="border-b border-slate-200 bg-slate-50/80 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-4 py-3">{{ __('Name') }}</th>
                            <th class="px-4 py-3">{{ __('Email') }}</th>
                            <th class="px-4 py-3">{{ __('Phone') }}</th>
                            <th class="px-4 py-3">{{ __('Status') }}</th>
                            <th class="px-4 py-3">{{ __('Orders') }}</th>
                            <th class="px-4 py-3 text-end">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($customers as $customer)
                            <tr class="bg-white hover:bg-slate-50/50">
                                <td class="px-4 py-3 font-medium text-slate-900">
                                    {{ $customer->first_name }} {{ $customer->last_name }}
                                </td>
                                <td class="px-4 py-3 text-slate-600">{{ $customer->email }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $customer->phone ?: '—' }}</td>
                                <td class="px-4 py-3 capitalize">
                                    <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium ring-1 ring-inset {{ $customer->status === 'active' ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20' : 'bg-slate-100 text-slate-600 ring-slate-500/10' }}">
                                        {{ $customer->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-slate-600">
                                    <span class="inline-flex items-center rounded-full bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700">
                                        {{ $customer->orders_count }} {{ __('Orders') }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-end">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.customers.show', $customer) }}" class="rounded-lg px-2 py-1 text-xs font-semibold text-blue-700 hover:bg-blue-50">{{ __('View') }}</a>
                                        @can('admin.customers.edit')
                                            <a href="{{ route('admin.customers.edit', $customer) }}" class="rounded-lg px-2 py-1 text-xs font-semibold text-amber-700 hover:bg-amber-50">{{ __('Edit') }}</a>
                                        @endcan
                                        @can('admin.customers.delete')
                                            <form action="{{ route('admin.customers.destroy', $customer) }}" method="post" class="inline" onsubmit="return confirm(@json(__('Delete this customer?')));">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="rounded-lg px-2 py-1 text-xs font-semibold text-red-600 hover:bg-red-50">{{ __('Delete') }}</button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-slate-500">{{ __('No customers found.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($customers->hasPages())
                <div class="border-t border-slate-100 px-4 py-3">
                    {{ $customers->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
