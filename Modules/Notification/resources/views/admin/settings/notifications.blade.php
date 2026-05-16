@extends('layouts.admin')

@section('title', __('Notification Settings'))

@section('content')
<div class="mx-auto max-w-3xl">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900">{{ __('Settings') }}</h1>
        <p class="mt-1 text-sm text-slate-500">{{ __('Configure store notifications') }}</p>
    </div>

    @if (session('status'))
        <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            {{ session('status') }}
        </div>
    @endif

    <div class="admin-card border-0 p-6 shadow-md shadow-slate-200/50">
        <h2 class="text-lg font-semibold text-slate-900">{{ __('Order notifications') }}</h2>
        <p class="mt-1 text-sm text-slate-500">
            {{ __('This email receives an alert when a new order is placed on the website.') }}
        </p>

        <form action="{{ route('admin.settings.notifications.update') }}" method="POST" class="mt-6">
            @csrf
            @method('PUT')

            <div>
                <label for="order_notification_email" class="block text-sm font-medium text-slate-700">
                    {{ __('Notification email') }}
                </label>
                <input
                    type="email"
                    id="order_notification_email"
                    name="order_notification_email"
                    value="{{ old('order_notification_email', $orderNotificationEmail) }}"
                    placeholder="orders@example.com"
                    class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:ring focus:ring-slate-200 transition-all"
                >
                @error('order_notification_email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-2 text-xs text-slate-500">
                    {{ __('Leave empty to disable order email notifications.') }}
                </p>
            </div>

            <div class="mt-8 flex flex-wrap items-center gap-3 border-t border-slate-200 pt-6">
                <button
                    type="submit"
                    class="rounded-lg bg-slate-900 px-6 py-2 text-sm font-semibold text-white transition hover:bg-slate-800"
                >
                    {{ __('Save settings') }}
                </button>
                @can('admin.notifications.view')
                    <a
                        href="{{ route('admin.notification-recipients.index') }}"
                        class="text-sm font-medium text-slate-600 hover:text-slate-900"
                    >
                        {{ __('Manage all recipients') }} →
                    </a>
                @endcan
            </div>
        </form>
    </div>
</div>
@endsection
