<?php
namespace App\Domain\Appointment;

use App\Models\User;
use App\Models\Lawyer;
use App\Models\Appointment;
use Illuminate\Validation\ValidationException;

class BookingBuilder
{
    private $citizen = null;
    private $lawyer = null;
    private $status = Appointment::PENDING_STATUS;

    private function __construct()
    {
    }

    public static function bookForCitizen($user)
    {
        $instance = new self();
        $instance->citizen = $user;

        return $instance;
    }

    public function withLawyer(Lawyer $lawyer)
    {
        $this->lawyer = $lawyer;
        return $this;
    }

    public function withStatus($status)
    {
        if ($status) {
            $this->status = $status;
        }
        return $this;
    }

    public function send($date)
    {
        if (!$this->lawyer) {
            throw ValidationException::withMessages([
                'general' => "Lawyer is not provied",
            ]);
        }

        return Appointment::create([
            'lawyer_id' => $this->lawyer->id,
            'citizen_id' => $this->citizen->id,
            'scheduled_for' => $date,
            'status' => $this->status,
        ]);
    }
}
