<?php

namespace Modules\Auth\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $code,
        public string $purpose
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('Your verification code')
        );
    }

    public function content(): Content
    {
        return new Content(
            text: 'auth::emails.otp_code_text'
        );
    }
}
