<?php

namespace Tests\Feature;

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
    public function lawyer_cannot_create_appointmnts_for_another_lawyer()
    {

    }

    /** @test **/
    public function reschedule_appointment()
    {

    }
}
