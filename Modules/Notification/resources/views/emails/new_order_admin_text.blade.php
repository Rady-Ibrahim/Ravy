{{ __('New order from website') }}

{{ __('Order') }} #{{ $order->order_number }}
{{ __('Status') }}: {{ $order->status }}
{{ __('Payment') }}: {{ $order->payment_status }} ({{ $order->payment_method ?? '—' }})
{{ __('Grand total') }}: {{ number_format((float) $order->grand_total, 2) }} {{ $order->currency }}

@php $shipping = $order->shipping_address_snapshot ?? []; @endphp
{{ __('Customer') }}: {{ trim(($shipping['first_name'] ?? '').' '.($shipping['last_name'] ?? '')) ?: ($order->user?->name ?? '—') }}
{{ __('Email') }}: {{ $shipping['email'] ?? $order->user?->email ?? '—' }}
{{ __('Phone') }}: {{ $shipping['phone'] ?? '—' }}

@if ($order->relationLoaded('items') && $order->items->isNotEmpty())
{{ __('Items') }}:
@foreach ($order->items as $item)
- {{ $item->name_snapshot }} × {{ $item->qty }} — {{ number_format((float) $item->line_total, 2) }} {{ $order->currency }}
@endforeach
@endif

{{ __('View order in admin') }}: {{ $orderUrl }}
