<?php

namespace App\Actions\Jetstream;

use App\Mail\AccountDeletedNotification;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Laravel\Jetstream\Contracts\DeletesUsers;

class DeleteUser implements DeletesUsers
{
    /**
     * Delete the given user.
     */
    public function delete(User $user): void
    {
        // Sauvegarde des informations avant suppression
        $userEmail = $user->email;
        $userName = $user->name;

        $user->deleteProfilePhoto();
        $user->tokens->each->delete();
        $user->delete();

        // Envoi de l'email de confirmation après suppression
        Mail::to($userEmail)->send(new AccountDeletedNotification($userName));
    }
}
