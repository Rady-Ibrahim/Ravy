<?php

namespace Modules\Notification\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Modules\Product\Models\Variant;

class ProductStockLowAdminMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public Variant $variant,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('Low stock alert: :product', ['product' => $this->variant->product?->name ?? __('Product')]),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'notification::emails.product_stock_low_admin',
            text: 'notification::emails.product_stock_low_admin_text',
            with: [
                'variant' => $this->variant,
            ],
        );
    }
}
