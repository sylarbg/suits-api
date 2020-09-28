<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Lawyer;
use App\Models\Appointment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ListAppointmentsTest extends TestCase
{
    use RefreshDatabase;

    /** @test **/
    public function citizen_can_see_only_his_appointments()
    {
        $jane = User::factory()->create();
        $john = User::factory()->create();

        $janeAppointment = $this->appointmentFactory(['citizen_id' => $jane->id, 'scheduled_for' => now()]);
        $johnAppointment = $this->appointmentFactory(['citizen_id' => $john->id, 'scheduled_for' => now()->addDay()]);

        $this->assertEquals(2, Appointment::count());

        $this->actingAs($jane);

        $data = $this->getJson(route('appointments.index'))->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals($data[0]['id'], $janeAppointment->id);

        $this->actingAs($john);

        $data = $this->getJson(route('appointments.index'))->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals($data[0]['id'], $johnAppointment->id);
    }

    /** @test **/
    public function lawyer_can_see_only_his_appointments()
    {
        $jane = Lawyer::factory()->create();
        $john = Lawyer::factory()->create();

        $janeAppointment = $this->appointmentFactory(['lawyer_id' => $jane->id, 'scheduled_for' => now()]);
        $johnAppointment = $this->appointmentFactory(['lawyer_id' => $john->id, 'scheduled_for' => now()]);

        $this->assertEquals(2, Appointment::count());

        $this->actingAs($jane);

        $data = $this->getJson(route('appointments.index'))->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals($data[0]['id'], $janeAppointment->id);

        $this->actingAs($john);

        $data = $this->getJson(route('appointments.index'))->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals($data[0]['id'], $johnAppointment->id);
    }

    /** @test **/
    public function appointments_can_be_filtered_by_lawyer()
    {
        $this->actingAs($jane = User::factory()->create());

        $lawyerBob    = Lawyer::factory()->create(['name' => 'Bob']);
        $lawyerMartin = Lawyer::factory()->create(['name' => 'Martin']);

       $withLawyerBob =  $this->appointmentFactory([
            'citizen_id' => $jane->id,
            'lawyer_id' => $lawyerBob->id,
            'scheduled_for' => now()
        ]);

        $this->appointmentFactory([
            'citizen_id' => $jane->id,
            'lawyer_id' => $lawyerMartin->id,
            'scheduled_for' => now()
        ]);

        $this->assertEquals(2, Appointment::count());

        $data = $this
            ->getJson(route('appointments.index', ['name' => $lawyerBob->name]))->json('data');

        $this->assertCount(1, $data);
        $this->assertEquals($data[0]['id'], $withLawyerBob->id);
    }

    /** @test **/
    public function appointments_can_be_filtered_by_status()
    {
        $this->actingAs($jane = User::factory()->create());

        $pending = Appointment::factory()->create(['citizen_id' => $jane->id, 'scheduled_for' => now() ]);
        $approved = Appointment::factory()->create(['citizen_id' => $jane->id, 'scheduled_for' => now(), 'status' => Appointment::APPROVED_STATUS ]);

        $this->assertEquals(2, Appointment::count());

        // Only Pending
        $data = $this->getJson(route('appointments.index', ['status' => [$pending->status]]))->json('data');

        $this->assertCount(1, $data);
        $this->assertEquals($data[0]['id'], $pending->id);

        // Only Approved
        $data = $this
            ->getJson(route('appointments.index', ['status' => [$approved->status]]))
            ->json('data');

        $this->assertCount(1, $data);
        $this->assertEquals($data[0]['id'], $approved->id);

        //Pending and Approved
        $data = $this
            ->getJson(route('appointments.index', ['status' => [$approved->status, $pending->status]]))
            ->json('data');

        $this->assertCount(2, $data);
    }

    protected function appointmentFactory($attrs)
    {
        return Appointment::factory()->create($attrs);
    }
}
