<?php

namespace Tests\Unit;

use App\Models\Appointment;
use App\Models\Lawyer;
use App\Models\User;
use Tests\TestCase;
use App\Rules\AppointmentInitialStatus;

class AppointmentInitialStatusTest extends TestCase
{
    /** @test **/
    public function initial_status_for_appointment_created_by_citizen_is_pending()
    {
        $rule = new AppointmentInitialStatus(User::factory()->make());

        $this->assertFalse($rule->passes('status', Appointment::APPROVED_STATUS));
    }

    /** @test **/
    public function lawyer_can_set_every_status()
    {
        $rule = new AppointmentInitialStatus(Lawyer::factory()->make());

        $this->assertTrue($rule->passes('status', Appointment::PENDING_STATUS));
        $this->assertTrue($rule->passes('status', Appointment::REJECTED_STATUS));
        $this->assertTrue($rule->passes('status', Appointment::APPROVED_STATUS));
    }

    /** @test **/
    public function appointment_cannot_has_invalid_status()
    {
        $rule = new AppointmentInitialStatus(Lawyer::factory()->make());

        $this->assertFalse($rule->passes('status', 'invalid status'));
    }
}
