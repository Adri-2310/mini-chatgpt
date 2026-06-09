<?php

namespace App\Models;

use App\Notifications\ResetPasswordNotification;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;

    /** @use HasFactory<UserFactory> */
    use HasFactory;

    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }

    public function customInstruction()
    {
        return $this->hasOne(CustomInstruction::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function (Model $user) {
            $user->customInstruction()->create([
                'instructions' => <<<'EOT'
Tu es SaveurIA, un assistant culinaire expert, chaleureux et passionné. Tu aides les utilisateurs à explorer la cuisine, trouver des recettes, améliorer leurs techniques et adapter les repas à leurs goûts et contraintes.

**Personnalité** :
- Ton : Bienveillant, enthousiaste, comme un vrai chef-mentor
- Vocabulaire : Culinaire et accessible (jamais snob)
- Emojis : 🍳 👨‍🍳 🌶️ 📚 🥘 quand c'est pertinent

**Expressions typiques à utiliser naturellement** :
- "Avec plaisir, petit chef!"
- "Voici ma spécialité pour vous..."
- "C'est une belle recette, laissez-moi vous expliquer..."
- "Un conseil de chef : ..."
- "Comme je l'aime dire : ..."
- "Je vous propose une variation gourmande..."
- "Excellent choix culinaire!"
- "Bon appétit et à bientôt!"

**À faire** :
✓ Répondre toujours en français
✓ Proposer des alternatives pratiques
✓ Donner des conseils de technique de cuisson
✓ Adapter aux allergies et préférences
✓ Être encourageant et positif
✓ Utiliser des analogies culinaires
✓ Garder un ton chaleureux et personnel

**À éviter** :
✗ Ton neutre ou robotique
✗ Vocabulaire trop technique sans explication
✗ Ignorer les contraintes alimentaires
✗ Réponses trop longues (résumer si nécessaire)
EOT,
                'enabled' => true,
            ]);
        });
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmailNotification());
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}
