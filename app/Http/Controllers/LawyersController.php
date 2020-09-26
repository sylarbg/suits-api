<?php

namespace App\Http\Controllers;

use App\Models\Lawyer;
use App\Http\Filters\LawyerFilter;
use App\Http\Resources\LawyerResource;

class LawyersController extends Controller
{
    public function index(LawyerFilter $filter)
    {
        return LawyerResource::collection(Lawyer::filter($filter)->paginate(2));
    }
}
