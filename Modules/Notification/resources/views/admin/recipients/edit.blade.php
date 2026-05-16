@extends('layouts.admin')

@section('title', __('Edit Notification Recipient'))

@section('content')
<div class="mx-auto max-w-7xl">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">{{ __('Edit Notification Recipient') }}</h1>
            <p class="mt-1 text-sm text-slate-500">{{ $recipient->address }}</p>
        </div>
        <a href="{{ route('admin.notification-recipients.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
            {{ __('Back') }}
        </a>
    </div>

    <div class="admin-card border-0 p-6 shadow-md shadow-slate-200/50">
        <form action="{{ route('admin.notification-recipients.update', $recipient) }}" method="POST">
            @csrf
            @method('PUT')
            @include('notification::admin.recipients.partials.form', ['events' => $events, 'recipient' => $recipient])

            <div class="mt-8 flex gap-3 border-t border-slate-200 pt-6">
                <button type="submit" class="rounded-lg bg-slate-900 px-6 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
                    {{ __('Save Changes') }}
                </button>
                <a href="{{ route('admin.notification-recipients.index') }}" class="rounded-lg border border-slate-300 px-6 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                    {{ __('Cancel') }}
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
