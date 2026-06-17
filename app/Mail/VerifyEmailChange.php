<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerifyEmailChange extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $pendingEmail,
        public string $userName,
        public string $verificationUrl,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            to: [$this->pendingEmail],
            subject: 'Confirmez votre nouvelle adresse e-mail',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.verify-email-change',
            with: [
                'userName' => $this->userName,
                'pendingEmail' => $this->pendingEmail,
                'verificationUrl' => $this->verificationUrl,
            ],
        );
    }
}
