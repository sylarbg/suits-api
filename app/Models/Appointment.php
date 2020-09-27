<?php

namespace App\Models;

use App\ConvertToServerTimeZone;
use App\Models\Support\Ownable;
use App\Models\Support\Searchable;
use Illuminate\Database\Eloquent\Model;
use App\Domain\Appointment\BookingBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Appointment extends Model
{
    use Searchable, Ownable;

    const PENDING_STATUS  = 1;
    const APPROVED_STATUS = 2;
    const REJECTED_STATUS = 3;
    const CONFIRMED_STATUS = 4;

    public static $STATUS_LOOKUP = [
        self::PENDING_STATUS  => 'Pending',
        self::APPROVED_STATUS => 'Approved',
        self::REJECTED_STATUS => 'Rejected',
        self::CONFIRMED_STATUS => 'Confirmed',
    ];

    protected $casts = [
        'scheduled_for' => 'datetime',
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

    public function setScheduledForAttribute($value)
    {
        $this->attributes['scheduled_for'] = ConvertToServerTimeZone::convert($value);
    }

    public function reschedule($datetime)
    {
        $this->update([
            'scheduled_for' => $datetime,
            'status' => self::PENDING_STATUS
        ]);
    }
}
