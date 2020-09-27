<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Rules\IsFreeHour;
use App\Rules\BookingDateIsInFuture;
use App\Rules\AppointmentInitialStatus;
use Illuminate\Foundation\Http\FormRequest;

class CreateAppointmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
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
                new BookingDateIsInFuture(),
                new IsFreeHour($this->route('lawyer'), $this->route('appointment')),
            ]
        ];
    }

    public function resolveCitizen()
    {
        if (!auth()->user()->isLawyer()) {
            return auth()->user();
        }

        return $user = User::findOrFail($this->get('user_id'));
    }
}
