@component('mail::message')
# Vérification de votre Email

Bonjour,

Veuillez cliquer sur le bouton ci-dessous pour vérifier votre adresse email :

@component('mail::button', ['url' => $actionUrl])
{{ $actionText }}
@endcomponent

Ce lien de vérification expirera dans 60 minutes.

Si vous n'avez pas créé de compte, aucune action n'est nécessaire.

Cordialement,
Mini-ChatGPT
@endcomponent
