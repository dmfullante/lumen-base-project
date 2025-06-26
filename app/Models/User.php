<?php

namespace App\Models;

use App\Traits\HasPersonName;
use App\Traits\HasRoleTypes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Authenticatable as AuthenticatableTrait;
use Illuminate\Contracts\Auth\Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Model implements Authenticatable, JWTSubject
{
    use AuthenticatableTrait, Notifiable, HasRoles, HasRoleTypes, HasPersonName;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'pin',
        'otp',
        'otp_expires_at',
        'email',
        'contact_no'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var string[]
     */
    protected $hidden = [
        'pin',
        'otp'
    ];

    protected $guard_name = 'api';

    // The getJWTIdentifier method returns the primary key (id) of the user.
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    // The getJWTCustomClaims method allows you to add custom claims to the JWT (optional).
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function assignUserRole($role)
    {
        $this->assignRole($role);
    }

    public function syncUserRoles($roles)
    {
        parent::syncRoles($roles);
    }

    public function getRoleNameAttribute()
    {
        return $this->role->name ?? '';
    }
}
