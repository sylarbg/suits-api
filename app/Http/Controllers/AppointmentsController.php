<?php

namespace App\Http\Controllers;

use App\Http\Filters\AppointmentFilter;
use App\Http\Resources\AppointmentResource;

class AppointmentsController extends Controller
{
    public function index(AppointmentFilter $filter)
    {
        return AppointmentResource::collection(auth()->user()->appointments()->filter($filter)->with('lawyer')->paginate(2));
    }
}
