<?php

namespace App\Domain\Auth\Actions;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CreateNewUser
{
    public function fromRequest(Request $request)
    {
        return $this->create($request->only(['name', 'email', 'password', 'type']));
    }

    protected function create($data)
    {
        $data['password'] = Hash::make($data['password']);
        return User::create($data);
    }
}
