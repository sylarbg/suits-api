<?php

namespace App\Http\Controllers;

use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;
use App\Models\Lawyer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class LawyerAppointmentsController extends Controller
{
    public function store(Lawyer $lawyer, Request $request)
    {
        $date =  $request->get('datetime');
        return Appointment::book(auth()->user())->withLawyer($lawyer)->send($date);
    }

    public function reschedule(Lawyer $lawyer, Appointment $appointment, Request $request)
    {
        if (Gate::allows('reschedule', [$appointment, $lawyer])) {
            $appointment->reschedule($request->get('datetime'));
        }

        return new AppointmentResource($appointment);
    }

    public function delete(Lawyer $lawyer, Appointment $appointment, Request $request)
    {
        $this->authorize('delete', [$appointment, $lawyer]);
        $appointment->delete();
    }
}
