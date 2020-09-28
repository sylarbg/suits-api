<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Tests\TestCase;
use App\Rules\BookingDateIsInFuture;

class BookingDateIsInFutureTest extends TestCase
{
    /** @test **/
    public function booking_hour_cannot_be_in_past()
    {
        $rule = new BookingDateIsInFuture();
        Carbon::setTestNow('2020-09-28 12:00:00');
        $this->assertFalse($rule->passes('datetime', Carbon::now()->subDay()));
    }

    /** @test **/
    public function booking_hour_must_be_in_future()
    {
        $rule = new BookingDateIsInFuture();
        Carbon::setTestNow('2020-09-28 12:00:00');
        $this->assertTrue($rule->passes('datetime', Carbon::now()->addDay()));
    }
}
