<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Lawyer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AppointmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Appointment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'status' => Appointment::PENDING_STATUS,
            'citizen_id'  => function () {
                return User::factory()->create()->id;
            },
            'lawyer_id'  => function () {
                return Lawyer::factory()->create()->id;
            },
        ];
    }
}
