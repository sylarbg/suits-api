<?php

namespace App\Models;

use App\Http\Filters\Filters;

class Lawyer extends User
{
    protected $table = 'users';

    public static function boot()
    {
        parent::boot();

        static::addGlobalScope(function ($query) {
            $query->where('type', self::TYPE_LAWYER);
        });
    }

    public function scopeFilter($query, Filters $filters)
    {
        return $filters->apply($query);
    }
}
