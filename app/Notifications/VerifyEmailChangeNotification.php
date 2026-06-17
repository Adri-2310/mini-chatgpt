<?php

namespace App\Notifications;

use App\Mail\VerifyEmailChange;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class VerifyEmailChangeNotification extends Notification
{
    use Queueable;

    public function __construct(private string $pendingEmail)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable)
    {
        $token = \Illuminate\Support\Str::random(60);
        $notifiable->update(['pending_email_token' => hash('sha256', $token)]);

        $verificationUrl = route('verification.email-change', ['token' => $token], absolute: true);

        return new VerifyEmailChange(
            $this->pendingEmail,
            $notifiable->name,
            $verificationUrl
        );
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
