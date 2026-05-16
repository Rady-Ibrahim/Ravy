<?php

namespace Modules\Notification\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Modules\Orders\Models\Order;

class NewOrderAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('New order #:number', ['number' => $this->order->order_number])
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'notification::emails.new_order_admin',
            text: 'notification::emails.new_order_admin_text',
            with: [
                'orderUrl' => route('admin.orders.show', $this->order->order_number),
            ],
        );
    }
}
