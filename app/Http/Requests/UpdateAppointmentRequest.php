<?php

namespace App\Http\Requests;

use App\Rules\IsFreeHour;
use App\Rules\AppointmentInitialStatus;

class UpdateAppointmentRequest extends CreateAppointmentRequest
{
    public function rules()
    {
        return [
            'status' => [
                'nullable',
                new AppointmentInitialStatus($this->user()),
            ],
            'datetime' => [
                'date',
                'bail',
                new IsFreeHour($this->route('lawyer'), $this->route('appointment')),
            ]
        ];
    }
}
