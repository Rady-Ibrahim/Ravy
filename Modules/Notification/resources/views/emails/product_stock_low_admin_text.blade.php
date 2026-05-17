{{ __('Low stock alert') }}

{{ __('A product variant has dropped below the stock threshold and needs attention.') }}

- {{ __('Product:') }} {{ $variant->product?->name ?? __('Unknown product') }}
- {{ __('Variant SKU:') }} {{ $variant->sku }}
- {{ __('Current stock:') }} {{ $variant->stock }}

{{ __('Please restock this product or update inventory levels.') }}
