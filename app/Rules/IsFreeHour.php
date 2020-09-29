<?php

namespace App\Rules;

use App\Models\Lawyer;
use App\Models\Appointment;
use App\ConvertToServerTimeZone;
use Illuminate\Contracts\Validation\Rule;

class IsFreeHour implements Rule
{
    /**
     * @var Lawyer
     */
    protected $lawyer;
    /**
     * @var Appointment|null
     */
    protected $appointment;

    /**
     * Create a new rule instance.
     *
     * @param Lawyer $lawyer
     * @param Appointment|null $appointment
     */
    public function __construct(Lawyer $lawyer, Appointment $appointment = null)
    {
        $this->lawyer = $lawyer;
        $this->appointment = $appointment;
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
        return $this->isAvailableForBooking(ConvertToServerTimeZone::convert($value));
    }

    protected function isAvailableForBooking($value)
    {
        $builder = $this->lawyer->appointments()
            ->where('status', Appointment::APPROVED_STATUS)
            ->when($this->appointment, function ($q) {
                $q->where('id', '!=', $this->appointment->id);
            })
            ->where('scheduled_for', $value);

        return $builder->count() == 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Sorry, not available';
    }
}
