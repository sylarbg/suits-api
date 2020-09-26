<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Lawyer;
use Illuminate\Http\Request;

class LawyerAppointmentsController extends Controller
{
    public function store(Lawyer $lawyer, Request $request)
    {
        $date = '2020-01-01 13:50';
        return Appointment::book(auth()->user())->withLawyer($lawyer)->send($date);
    }
}
