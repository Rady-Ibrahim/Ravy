@extends('layouts.admin')

@section('title', __('Notification Recipients'))

@section('content')
<div class="mx-auto max-w-7xl">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">{{ __('Notification Recipients') }}</h1>
            <p class="mt-1 text-sm text-slate-500">{{ __('Emails that receive alerts when new orders arrive from the website') }}</p>
        </div>
        @can('admin.notifications.create')
            <a
                href="{{ route('admin.notification-recipients.create') }}"
                class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800"
            >
                {{ __('Add Recipient') }}
            </a>
        @endcan
    </div>

    @if (session('status'))
        <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            {{ session('status') }}
        </div>
    @endif

    <div class="admin-card overflow-hidden border-0 shadow-md shadow-slate-200/50">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[760px] text-left text-sm">
                <thead class="border-b border-slate-200 bg-slate-50/80 text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-3">{{ __('Label') }}</th>
                        <th class="px-4 py-3">{{ __('Email') }}</th>
                        <th class="px-4 py-3">{{ __('Event') }}</th>
                        <th class="px-4 py-3">{{ __('Source filter') }}</th>
                        <th class="px-4 py-3 text-center">{{ __('Status') }}</th>
                        <th class="px-4 py-3 text-end">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recipients as $recipient)
                        <tr class="bg-white hover:bg-slate-50/50">
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $recipient->label ?: '—' }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $recipient->address }}</td>
                            <td class="px-4 py-3 text-slate-600">
                                {{ \Modules\Notification\Support\NotificationEvents::labels()[$recipient->event] ?? $recipient->event }}
                            </td>
                            <td class="px-4 py-3 text-slate-600">
                                {{ $recipient->filters['source'] ?? __('All sources') }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @can('admin.notifications.edit')
                                    <form action="{{ route('admin.notification-recipients.toggle-status', $recipient) }}" method="POST" class="inline">
                                        @csrf
                                        <button
                                            type="submit"
                                            class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium ring-1 ring-inset transition {{ $recipient->is_active
                                                ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 hover:bg-emerald-100'
                                                : 'bg-slate-100 text-slate-600 ring-slate-500/10 hover:bg-slate-200' }}"
                                        >
                                            {{ $recipient->is_active ? __('Active') : __('Inactive') }}
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-slate-500">{{ $recipient->is_active ? __('Active') : __('Inactive') }}</span>
                                @endcan
                            </td>
                            <td class="px-4 py-3 text-end">
                                <div class="flex justify-end gap-2">
                                    @can('admin.notifications.edit')
                                        <a href="{{ route('admin.notification-recipients.edit', $recipient) }}" class="text-sm font-medium text-slate-700 hover:text-slate-900">{{ __('Edit') }}</a>
                                    @endcan
                                    @can('admin.notifications.delete')
                                        <form action="{{ route('admin.notification-recipients.destroy', $recipient) }}" method="POST" class="inline" onsubmit="return confirm(@json(__('Delete this recipient?')))">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-800">{{ __('Delete') }}</button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-slate-500">
                                {{ __('No notification recipients yet.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
