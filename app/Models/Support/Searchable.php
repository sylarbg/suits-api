<?php

namespace App\Models\Support;

use App\Http\Filters\Filters;

trait Searchable
{
    public function scopeFilter($query, Filters $filters)
    {
        return $filters->apply($query);
    }
}
