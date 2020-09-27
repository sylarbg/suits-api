<?php

namespace App;

use Illuminate\Support\Carbon;

class ConvertToServerTimeZone
{
    /**
     * @param $value
     * @return Carbon
     */
    public static function convert($value)
    {
        return Carbon::parse($value)->setTimezone(config('app.timezone'));
    }
}
