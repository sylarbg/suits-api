<?php

namespace App\Rules;

use App\ConvertToServerTimeZone;
use Illuminate\Contracts\Validation\Rule;

class BookingDateIsInFuture implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
        return ConvertToServerTimeZone::convert($value)->isFuture();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The date must be in the future';
    }
}
