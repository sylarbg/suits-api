<?php

namespace App\Models;

use App\Models\Support\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, Searchable;

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

    public function getTypeAsObjectAttribute($value)
    {
        return self::$TYPES_LOOKUP[$this->type];
    }

    public function isLawyer()
    {
        return $this->type == self::TYPE_LAWYER;
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, $this->isLawyer() ? 'lawyer_id' : 'citizen_id');
    }
}
