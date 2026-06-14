@component('mail::message')
# 🌶️ SaveurIA

## Réinitialiser votre mot de passe

Bonjour,

Vous recevez cet email car nous avons reçu une demande de réinitialisation de mot de passe pour votre compte.

@component('mail::button', ['url' => $actionUrl])
{{ $actionText }}
@endcomponent

Ce lien de réinitialisation expirera dans 60 minutes.

Si vous n'avez pas demandé une réinitialisation de mot de passe, aucune action n'est nécessaire.

Cordialement,
SaveurIA
@endcomponent
