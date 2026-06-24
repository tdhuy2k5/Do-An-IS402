<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    protected $table = 'users';

    protected $primaryKey = 'user_id';

    protected $fillable = [
        'email',
        'password_hash',
        'full_name',
        'phone',
        'avatar_url',
        'date_of_birth',
        'gender',
        'email_verified',
        'reward_points',
        'verification_code',
        'membership_tier_id',
    ];

    protected $casts = [
        'email_verified' => 'boolean',
        'gender' => 'string',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles', 'user_id', 'role_id');
    }

    public function membershipTier()
    {
        return $this->belongsTo(MembershipTier::class, 'membership_tier_id');
    }

    public function addresses()
    {
        return $this->hasMany(Address::class, 'user_id');
    }

    public function cart()
    {
        return $this->hasOne(Cart::class, 'user_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'user_id');
    }

    public function refreshtokens()
    {
        return $this->hasMany(RefreshToken::class, 'user_id');
    }


    public function getAuthPassword()
    {
        return $this->password_hash;
    }


    public function getJWTIdentifier()
    {
        return $this->getKey();
    }


    public function getJWTCustomClaims()
    {

        $roles = $this->roles->pluck('role_id')->toArray();

        return [
            'roles' => $roles,

        ];
    }


    public function hasAnyRole(array $roles)
    {
        return $this->roles()->whereIn('roles.role_id', $roles)->exists();
    }
}
