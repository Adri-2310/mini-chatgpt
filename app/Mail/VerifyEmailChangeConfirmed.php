<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerifyEmailChangeConfirmed extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $userName,
        public string $email,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            to: [$this->email],
            subject: 'Votre adresse e-mail a été confirmée',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.verify-email-change-confirmed',
            with: [
                'userName' => $this->userName,
                'email' => $this->email,
            ],
        );
    }
}
