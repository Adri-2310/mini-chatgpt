<?php

namespace App\Models;

use App\Notifications\ResetPasswordNotification;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;
    use HasApiTokens;
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

    protected $hidden = [
        'password',
        'remember_token',
    ];

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

    public function messages()
    {
        return $this->hasManyThrough(Message::class, Conversation::class);
    }

    public function userStats(): HasOne
    {
        return $this->hasOne(UserStats::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function (Model $user) {
            $user->customInstruction()->create([
                'instructions' => null,
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

    public function getDashboardStats()
    {
        $stats = UserStats::firstOrCreate(['user_id' => $this->id]);

        return [
            'monthly_cost' => (float) $stats->monthly_cost ?? 0,
            'conversations_total' => (int) $stats->total_conversations ?? 0,
            'monthly_messages' => (int) $stats->monthly_messages ?? 0,
        ];
    }

    public function recordUsage($tokens, $cost, $model, $date = null)
    {
        $date = $date ?? now();
        $firstDayOfMonth = $date->copy()->startOfMonth();

        $stats = UserStats::firstOrCreate(['user_id' => $this->id]);

        // Incrémenter si c'est du mois courant
        if ($date >= $firstDayOfMonth) {
            $stats->increment('monthly_messages');
            $stats->increment('monthly_cost', $cost);
        }

        $stats->update(['last_activity_at' => $date]);
    }
}
