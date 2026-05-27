<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'google_id',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
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
    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function setRoleAttribute($value)
    {
        $this->attributes['role'] = ($value === 'user') ? 'customer' : $value;
    }

    protected static function booted()
    {
        static::saved(function ($user) {
            if ($user->wasChanged('role') || $user->wasRecentlyCreated) {
                $role = $user->role === 'user' ? 'customer' : $user->role;
                if (in_array($role, ['admin', 'seller', 'customer', 'visitor'])) {
                    try {
                        $user->syncRoles($role);
                    } catch (\Throwable $e) {
                        // Suppress errors during early seeders/migrations when tables do not exist
                    }
                }
            }
        });
    }
}
