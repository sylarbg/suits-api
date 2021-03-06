<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateAppointmentRequest;
use App\Models\Lawyer;
use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Rules\BookingDateIsInFuture;
use App\Http\Resources\AppointmentResource;
use App\Http\Requests\CreateAppointmentRequest;

class LawyerAppointmentsController extends Controller
{
    public function store(Lawyer $lawyer, CreateAppointmentRequest $request)
    {
        $this->authorize('owns', [$request->resolveCitizen(), $lawyer]);

        $appointment = Appointment::book($request->resolveCitizen())
            ->withLawyer($lawyer)
            ->withStatus($request->get('status'))
            ->send($request->get('datetime'));

        return new AppointmentResource($appointment->load(['citizen', 'lawyer']));
    }

    public function update(Lawyer $lawyer, Appointment $appointment, UpdateAppointmentRequest $request)
    {
        $this->authorize('update', [$appointment, $lawyer]);

        $appointment->update([
            'status' => $request->get('status'),
            'scheduled_for' => $request->get('datetime')
        ]);

        return new AppointmentResource($appointment);
    }

    public function reschedule(Lawyer $lawyer, Appointment $appointment, Request $request)
    {
        $request->validate([
            'datetime' => [
                'date',
                'bail',
                new BookingDateIsInFuture()
            ]
        ]);

        $this->authorize('reschedule', [$appointment, $lawyer]);
        $appointment->reschedule($request->get('datetime'));
        return new AppointmentResource($appointment->load(['lawyer', 'citizen']));
    }

    public function delete(Lawyer $lawyer, Appointment $appointment)
    {
        $this->authorize('delete', [$appointment, $lawyer]);
        $appointment->delete();
    }
}
