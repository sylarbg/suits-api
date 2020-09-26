<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    const TYPE_CITIZEN = 1;
    const TYPE_LAWYER = 2;

    public static $TYPES_LOOKUP = [
        self::TYPE_CITIZEN => [
            'id' => self::TYPE_CITIZEN,
            'name' => "Citizen"
        ],
        self::TYPE_LAWYER => [
            'id' => self::TYPE_LAWYER,
            'name' => "Lawyer"
        ]
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'type',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getTypeAttribute($value)
    {
        return self::$TYPES_LOOKUP[$value];
    }
}
