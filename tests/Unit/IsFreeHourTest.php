<?php

namespace Tests\Unit;

use App\Domain\Appointment\BookingBuilder;
use App\Models\Appointment;
use App\Models\Lawyer;
use App\Models\User;
use App\Rules\IsFreeHour;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IsFreeHourTest extends TestCase
{
    use RefreshDatabase;

    /** @test **/
    public function new_appointment_cannot_overlap_with_already_approved()
    {
        $datetime = Carbon::tomorrow()->setHour('12')->setMinutes('0');
        $lawyer = Lawyer::factory()->create();

        Appointment::factory()->create([
            'lawyer_id' => $lawyer->id,
            'status' => Appointment::APPROVED_STATUS,
            'scheduled_for' => $datetime
        ]);

        $rule = new IsFreeHour($lawyer);

        $this->assertFalse($rule->passes('datetime', $datetime));
    }

    /** @test **/
    public function new_appointment_can_overlap_with_not_approved_one()
    {
        $lawyer = Lawyer::factory()->create();
        $datetime = Carbon::tomorrow()->setHour('12')->setMinutes('0');
        Appointment::factory()->create([
            'lawyer_id' => $lawyer->id,
            'status' => Appointment::PENDING_STATUS,
            'scheduled_for' => $datetime
        ]);

        $rule = new IsFreeHour($lawyer);

        $this->assertTrue($rule->passes('datetime', $datetime));
    }
}
