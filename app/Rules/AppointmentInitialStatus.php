<?php

namespace App\Rules;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Contracts\Validation\Rule;

class AppointmentInitialStatus implements Rule
{
    /**
     * @var User
     */
    protected $user;

    /**
     * Create a new rule instance.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (!$this->user->isLawyer() && (Appointment::PENDING_STATUS != $value)) {
            return false;
        }

        return in_array($value, array_keys(Appointment::$STATUS_LOOKUP));
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Invalid status';
    }
}
