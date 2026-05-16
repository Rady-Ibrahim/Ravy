@php
    $recipient = $recipient ?? null;
    $defaultSource = config('notification.default_order_source', 'website');
@endphp

<div class="grid gap-6 lg:grid-cols-2">
    <div>
        <label for="label" class="block text-sm font-medium text-slate-700">{{ __('Label') }}</label>
        <input
            type="text"
            id="label"
            name="label"
            value="{{ old('label', $recipient?->label) }}"
            placeholder="{{ __('e.g. Sales team') }}"
            class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:ring focus:ring-slate-200 transition-all"
        >
        @error('label')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="address" class="block text-sm font-medium text-slate-700">{{ __('Email') }}</label>
        <input
            type="email"
            id="address"
            name="address"
            value="{{ old('address', $recipient?->address) }}"
            required
            class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:ring focus:ring-slate-200 transition-all"
        >
        @error('address')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="event" class="block text-sm font-medium text-slate-700">{{ __('Event') }}</label>
        <select
            id="event"
            name="event"
            required
            class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:ring focus:ring-slate-200 transition-all"
        >
            @foreach ($events as $eventKey => $eventLabel)
                <option value="{{ $eventKey }}" @selected(old('event', $recipient?->event) === $eventKey)>{{ $eventLabel }}</option>
            @endforeach
        </select>
        @error('event')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="channel" class="block text-sm font-medium text-slate-700">{{ __('Channel') }}</label>
        <select
            id="channel"
            name="channel"
            required
            class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:ring focus:ring-slate-200 transition-all"
        >
            <option value="email" @selected(old('channel', $recipient?->channel ?? 'email') === 'email')>{{ __('Email') }}</option>
        </select>
        @error('channel')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="filter_source" class="block text-sm font-medium text-slate-700">{{ __('Order source filter') }}</label>
        <input
            type="text"
            id="filter_source"
            name="filter_source"
            value="{{ old('filter_source', $recipient?->filters['source'] ?? '') }}"
            placeholder="{{ $defaultSource }}"
            class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:ring focus:ring-slate-200 transition-all"
        >
        <p class="mt-1 text-xs text-slate-500">{{ __('Leave empty to notify for all order sources. Use :source when the storefront sends source on checkout.', ['source' => $defaultSource]) }}</p>
        @error('filter_source')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="lg:col-span-2">
        <label class="flex items-center">
            <input
                type="checkbox"
                name="is_active"
                value="1"
                {{ old('is_active', $recipient?->is_active ?? true) ? 'checked' : '' }}
                class="h-4 w-4 rounded border-slate-300 bg-slate-100 text-slate-900 focus:ring-slate-500"
            >
            <span class="ml-2 text-sm text-slate-700">{{ __('Active') }}</span>
        </label>
    </div>
</div>
