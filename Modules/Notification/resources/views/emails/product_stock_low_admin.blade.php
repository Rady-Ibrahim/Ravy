<div style="font-family: Arial, Helvetica, sans-serif; color: #0f172a;">
    <h1 style="font-size: 1.25rem; margin-bottom: 0.75rem;">{{ __('Low stock alert') }}</h1>
    <p style="margin-bottom: 1rem;">
        {{ __('A product variant has dropped below the stock threshold and needs attention.') }}</p>
    <ul style="margin: 0 0 1rem 1rem; padding: 0; list-style-type: disc;">
        <li><strong>{{ __('Product:') }}</strong> {{ $variant->product?->name ?? __('Unknown product') }}</li>
        <li><strong>{{ __('Variant SKU:') }}</strong> {{ $variant->sku }}</li>
        <li><strong>{{ __('Current stock:') }}</strong> {{ $variant->stock }}</li>
    </ul>
    <p>{{ __('Please restock this product or update inventory levels.') }}</p>
</div>
