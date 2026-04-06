<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;

#[Fillable(['name', 'email', 'password', 'phone', 'role', 'avatar', 'points', 'is_available', 'current_lat', 'current_lng'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

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
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function wastes()
    {
        return $this->hasMany(Waste::class);
    }

    public function missionsCollected()
    {
        return $this->hasMany(Waste::class, 'collector_id');
    }

    public function missionsDelivered()
    {
        return $this->hasMany(Order::class, 'collector_id');
    }

    public function collectionsAssigned()
    {
        return $this->hasMany(Collection::class, 'collector_id');
    }

    public function holdsRole($role)
    {
        return $this->role === $role;
    }

    public function isClient()
    {
        return $this->holdsRole('client');
    }

    public function isRamasseur()
    {
        return $this->holdsRole('ramasseur');
    }

    public function isLivreur()
    {
        return $this->holdsRole('livreur');
    }

    public function isStaff()
    {
        return in_array($this->role, ['ramasseur', 'livreur']);
    }

    public function isAdmin()
    {
        return $this->holdsRole('admin');
    }
}
