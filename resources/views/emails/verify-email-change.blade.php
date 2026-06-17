@component('mail::message')
# 🌶️ SaveurIA

## Confirmez votre nouvelle adresse e-mail

Bonjour {{ $userName }},

Vous avez demandé de changer votre adresse e-mail pour : **{{ $pendingEmail }}**

Veuillez cliquer sur le bouton ci-dessous pour confirmer ce changement.

@component('mail::button', ['url' => $verificationUrl])
Confirmer votre nouvel e-mail
@endcomponent

⚠️ **Important :** Vous avez **7 jours** pour confirmer ce nouvel e-mail. Si vous ne validez pas votre adresse dans ce délai, elle sera supprimée automatiquement et votre compte conservera votre ancien e-mail.

Si vous n'avez pas demandé ce changement, vous pouvez ignorer cet e-mail.

Cordialement,
SaveurIA
@endcomponent
