<?php

namespace App\Models\Support;

use App\Models\User;

trait Ownable
{
    public function isOwnedBy(User $user, $column = 'citizen_id')
    {
        return $this->$column == $user->id;
    }
}
