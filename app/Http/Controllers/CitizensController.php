<?php

namespace App\Http\Controllers;

use App\Http\Filters\CitizenFilter;
use App\Http\Resources\LawyerResource;
use App\Http\Resources\UserResource;
use App\Models\Lawyer;
use App\Models\User;

class CitizensController extends Controller
{
    public function index(CitizenFilter $filter)
    {
        return UserResource::collection(User::filter($filter)->paginate(2));
    }
}
