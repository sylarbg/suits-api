<?php

namespace App\Models;

use App\Http\Filters\Filters;
use App\Models\Support\Searchable;

class Lawyer extends User
{
    use Searchable;

    protected $table = 'users';

    public static function boot()
    {
        parent::boot();

        static::addGlobalScope(function ($query) {
            $query->where('type', self::TYPE_LAWYER);
        });
    }
}
