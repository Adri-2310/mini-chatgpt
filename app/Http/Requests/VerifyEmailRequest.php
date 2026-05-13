<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Auth\Events\Verified;

class VerifyEmailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [];
    }

    public function fulfill(): void
    {
        $user = User::findOrFail($this->route('id'));

        if (! hash_equals(
            (string) $user->getKey(),
            (string) $this->route('id')
        )) {
            return;
        }

        if (! hash_equals(
            sha1($user->getEmailForVerification()),
            (string) $this->route('hash')
        )) {
            return;
        }

        if ($user->hasVerifiedEmail()) {
            return;
        }

        $user->markEmailAsVerified();

        event(new Verified($user));
    }
}
