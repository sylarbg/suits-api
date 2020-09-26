<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Domain\Auth\Actions\CreateNewUser;
use App\Http\Resources\UserResource;

class RegisterController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function register(RegisterRequest $request, CreateNewUser $creator)
    {
        return new UserResource($creator->fromRequest($request));
    }
}
