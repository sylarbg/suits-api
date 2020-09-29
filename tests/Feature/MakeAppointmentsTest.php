<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Lawyer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MakeAppointmentsTest extends TestCase
{
    use RefreshDatabase;

    /** @test **/
    public function guest_cannot_make_an_appointment()
    {
        $this->postJson(route('appointments.store', ['lawyer' => 1 ]))->assertUnauthorized();
    }

    /** @test **/
    public function cannot_make_appointment_for_non_existing_lawyer()
    {
        $this->actingAs(User::factory()->create());
        $this->makeAppointment('non-existing-lawyer')->assertNotFound();
    }

    /** @test **/
    public function appointment_time_must_valid_date_time()
    {
        $this->actingAs(User::factory()->create());
        $lawyer = Lawyer::factory()->create();

        // required
        $this->makeAppointment($lawyer->id, ['datetime' => '' ])->assertJsonValidationErrors('datetime');

        // invalid format
        $this->makeAppointment($lawyer->id, ['datetime' => 'foobar' ])->assertJsonValidationErrors('datetime');

        // The date must be in the future
        Carbon::setTestNow('2020-09-28 15:00:00');

        $this->makeAppointment($lawyer->id, ['datetime' => Carbon::yesterday()->format('Y-m-d H:i:s') ])
            ->assertJsonValidationErrors('datetime');
    }

    /** @test **/
    public function as_citizen_make_appointment_with_valid_data()
    {
        $this->actingAs($citizen = User::factory()->create());
        $lawyer = Lawyer::factory()->create();

        $this->assertEquals(0, $citizen->appointments()->count());
        $this->assertEquals(0, $lawyer->appointments()->count());

        $this->makeAppointment($lawyer->id, [
            'datetime' => Carbon::tomorrow()->format('Y-m-d H:i'),
        ]);

        $this->assertEquals(1, $citizen->appointments()->count());
        $this->assertEquals(1, $lawyer->appointments()->count());
    }

    protected function makeAppointment($lawyerId, $data = [])
    {
        return $this->postJson(route('appointments.store', ['lawyer' => $lawyerId]), $data);
    }

    /** @test **/
    public function lawyer_cannot_create_appointment_for_another_lawyer()
    {
        $lawyerBob    = Lawyer::factory()->create(['name' => 'Bob']);
        $lawyerMartin = Lawyer::factory()->create(['name' => 'Martin']);
        $citizen = User::factory()->create();

        $this->actingAs($lawyerBob);
        $this->makeAppointment(
            $lawyerMartin->id,
            ['user_id' => $citizen->id, 'datetime' => Carbon::tomorrow()->format('Y-m-d H:i') ]
        )->assertForbidden();

        $this->assertEquals(0, Appointment::count());
    }

    /** @test **/
    public function lawyer_cannot_create_appointment_for_non_existing_citizen()
    {
        $this->actingAs($lawyerBob = Lawyer::factory()->create(['name' => 'Bob']));

        $this->makeAppointment(
            $lawyerBob->id,
            ['user_id' => 999999, 'datetime' => Carbon::tomorrow()->format('Y-m-d H:i')]
        )->assertNotFound();
    }

    /** @test **/
    public function lawyer_can_delete_his_appointment()
    {
        $lawyerBob    = Lawyer::factory()->create(['name' => 'Bob']);
        $appointment = Appointment::factory()->create([
            'lawyer_id' => $lawyerBob->id,
            'scheduled_for' => Carbon::now(),
        ]);

        $this->actingAs($lawyerBob);

        $this->deleteJson(
            route('appointments.delete', ['lawyer' => $lawyerBob->id, 'appointment' => $appointment->id])
        )->assertNoContent(200);
    }

    /** @test **/
    public function lawyer_cannot_delete_appointment_which_dont_belong_to_him()
    {
        $this->actingAs($citizen = User::factory()->create());
        $appointment = Appointment::factory()->create([
            'citizen_id' => $citizen->id,
            'status' => Appointment::PENDING_STATUS,
            'scheduled_for' => Carbon::now(),
        ]);

        $this->put(
            route('appointments.reschedule', ['lawyer' => $appointment->lawyer_id, 'appointment' => $appointment->id ]),
            ['datetime' => Carbon::now()->addDay()]
        )->assertForbidden();
    }

    /** @test **/
    public function citizen_cannot_reschedule_appointment_if_is_not_in_rejected_status()
    {
        $this->actingAs($citizen = User::factory()->create());
        $appointment = Appointment::factory()->create([
            'citizen_id' => $citizen->id,
            'status' => Appointment::PENDING_STATUS,
            'scheduled_for' => Carbon::now(),
        ]);

        $this->put(
            route('appointments.reschedule', ['lawyer' => $appointment->lawyer_id, 'appointment' => $appointment->id ]),
            ['datetime' => Carbon::now()->addDay()]
        )->assertForbidden();
    }

    /** @test **/
    public function citizen_can_reschedule_rejected_appointment()
    {
        $this->actingAs($citizen = User::factory()->create());
        $appointment = Appointment::factory()->create([
            'citizen_id' => $citizen->id,
            'status' => Appointment::REJECTED_STATUS,
            'scheduled_for' => Carbon::now(),
        ]);

        $this->put(
            route('appointments.reschedule', ['lawyer' => $appointment->lawyer_id, 'appointment' => $appointment->id ]),
            ['datetime' => $newDate = Carbon::now()->addDay()]
        )->assertOk();

        $this->assertEquals($appointment->fresh()->status, Appointment::PENDING_STATUS);
        $this->assertTrue($newDate->isSameAs($appointment->fresh()->scheduled_for));
    }

    /** @test **/
    public function citizen_cannot_reschedule_which_dont_belong_to_him()
    {
        $john = User::factory()->create();
        $johnAppointment = Appointment::factory()->create([
            'citizen_id' => $john->id,
            'status' => Appointment::REJECTED_STATUS,
            'scheduled_for' => $johnAppointmentDate = Carbon::now(),
        ]);

        $this->actingAs($jane = User::factory()->create());

        $this->put(
            route('appointments.reschedule', ['lawyer' => $johnAppointment->lawyer_id, 'appointment' => $johnAppointment->id ]),
            ['datetime' => $newDate = Carbon::now()->addDay()]
        )->assertForbidden();

        $this->assertEquals($johnAppointment->fresh()->status, Appointment::REJECTED_STATUS);
        $this->assertTrue($johnAppointmentDate->isSameAs($johnAppointment->fresh()->scheduled_for));
    }
}
