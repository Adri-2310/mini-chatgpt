<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Notifications\VerifyEmailChangeNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    public function update(User $user, array $input): void
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'photo' => ['nullable', 'mimes:jpg,jpeg,png', 'max:1024'],
        ])->validateWithBag('updateProfileInformation');

        if (isset($input['photo'])) {
            $user->updateProfilePhoto($input['photo']);
        }

        if ($input['email'] !== $user->email && $user instanceof MustVerifyEmail) {
            $this->handleEmailChange($user, $input);
        } else {
            $user->forceFill([
                'name' => $input['name'],
                'email' => $input['email'],
            ])->save();
        }
    }

    protected function handleEmailChange(User $user, array $input): void
    {
        // Vérifier si une vérification d'email est déjà en attente
        if (!is_null($user->pending_email)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'email' => 'Une vérification d\'email est déjà en attente. Veuillez attendre 7 jours ou cliquer sur le lien dans votre email.',
            ]);
        }

        // Rate limiting : minimum 5 minutes entre les demandes
        if ($user->pending_email_sent_at && $user->pending_email_sent_at->addMinutes(5) > now()) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'email' => 'Veuillez attendre 5 minutes avant de demander un nouveau changement d\'email.',
            ]);
        }

        $user->forceFill([
            'name' => $input['name'],
            'pending_email' => $input['email'],
            'pending_email_sent_at' => now(),
        ])->save();

        $user->notify(new VerifyEmailChangeNotification($input['email']));
    }
}
