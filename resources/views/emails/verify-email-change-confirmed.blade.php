@component('mail::message')
# 🌶️ SaveurIA

## Votre adresse e-mail a été confirmée ✅

Bonjour {{ $userName }},

Votre nouvelle adresse e-mail **{{ $email }}** a été confirmée avec succès !

Vous pouvez maintenant utiliser cette adresse pour vous connecter à votre compte SaveurIA.

Si vous n'avez pas effectué ce changement, veuillez nous contacter immédiatement.

Cordialement,
SaveurIA
@endcomponent
