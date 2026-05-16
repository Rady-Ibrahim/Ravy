<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ __('New order') }} #{{ $order->order_number }}</title>
</head>
<body style="font-family: system-ui, sans-serif; color: #0f172a; line-height: 1.5;">
    <h1 style="font-size: 1.25rem;">{{ __('New order from website') }}</h1>
    <p>{{ __('Order') }} <strong>#{{ $order->order_number }}</strong></p>

    <table style="border-collapse: collapse; width: 100%; max-width: 32rem;">
        <tr>
            <td style="padding: 0.25rem 0; color: #64748b;">{{ __('Status') }}</td>
            <td style="padding: 0.25rem 0;">{{ $order->status }}</td>
        </tr>
        <tr>
            <td style="padding: 0.25rem 0; color: #64748b;">{{ __('Payment') }}</td>
            <td style="padding: 0.25rem 0;">{{ $order->payment_status }} ({{ $order->payment_method ?? '—' }})</td>
        </tr>
        <tr>
            <td style="padding: 0.25rem 0; color: #64748b;">{{ __('Grand total') }}</td>
            <td style="padding: 0.25rem 0;"><strong>{{ number_format((float) $order->grand_total, 2) }} {{ $order->currency }}</strong></td>
        </tr>
        @php $shipping = $order->shipping_address_snapshot ?? []; @endphp
        <tr>
            <td style="padding: 0.25rem 0; color: #64748b;">{{ __('Customer') }}</td>
            <td style="padding: 0.25rem 0;">
                {{ trim(($shipping['first_name'] ?? '').' '.($shipping['last_name'] ?? '')) ?: ($order->user?->name ?? '—') }}
            </td>
        </tr>
        <tr>
            <td style="padding: 0.25rem 0; color: #64748b;">{{ __('Email') }}</td>
            <td style="padding: 0.25rem 0;">{{ $shipping['email'] ?? $order->user?->email ?? '—' }}</td>
        </tr>
        <tr>
            <td style="padding: 0.25rem 0; color: #64748b;">{{ __('Phone') }}</td>
            <td style="padding: 0.25rem 0;">{{ $shipping['phone'] ?? '—' }}</td>
        </tr>
    </table>

    @if ($order->relationLoaded('items') && $order->items->isNotEmpty())
        <h2 style="font-size: 1rem; margin-top: 1.5rem;">{{ __('Items') }}</h2>
        <ul style="padding-left: 1.25rem;">
            @foreach ($order->items as $item)
                <li>{{ $item->name_snapshot }} × {{ $item->qty }} — {{ number_format((float) $item->line_total, 2) }} {{ $order->currency }}</li>
            @endforeach
        </ul>
    @endif

    <p style="margin-top: 1.5rem;">
        <a href="{{ $orderUrl }}" style="color: #0f172a;">{{ __('View order in admin') }}</a>
    </p>
</body>
</html>
