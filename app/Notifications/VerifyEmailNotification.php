<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmailNotification;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyEmailNotification extends BaseVerifyEmailNotification
{
    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Vérifier votre adresse email')
            ->greeting('Bonjour !')
            ->line('Veuillez cliquer sur le bouton ci-dessous pour vérifier votre adresse email.')
            ->action('Vérifier Email', $verificationUrl)
            ->line('Ce lien de vérification expirera dans 60 minutes.')
            ->line('Si vous n\'avez pas créé de compte, aucune action n\'est nécessaire.')
            ->salutation('Cordialement,')
            ->markdown('notifications.verify-email');
    }
}
