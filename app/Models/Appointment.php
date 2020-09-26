<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domain\Appointment\BookingBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Appointment extends Model
{
    const PENDING_STATUS  = 1;
    const APPROVED_STATUS = 2;
    const REJECTED_STATUS = 3;

    public static $STATUS_LOOKUP = [
        self::PENDING_STATUS  => 'Pending',
        self::APPROVED_STATUS => 'Approved',
        self::REJECTED_STATUS => 'Rejected',
    ];


    protected $guarded = [];

    use HasFactory;

    public function citizen()
    {
        return $this->belongsTo(User::class, 'citizen_id');
    }

    public function lawyer()
    {
        return $this->belongsTo(User::class, 'lawyer_id');
    }

    public static function book(User $user)
    {
        return BookingBuilder::bookForCitizen($user);
    }

    public function getStatusNameAttribute()
    {
        return self::$STATUS_LOOKUP[$this->status];
    }
}
