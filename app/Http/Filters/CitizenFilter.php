<?php

namespace App\Http\Filters;

use App\Models\User;

class CitizenFilter extends LawyerFilter
{
    public function apply($builder)
    {
        $builder->where('type', User::TYPE_CITIZEN);
        return parent::apply($builder);
    }

    public function name($value)
    {
        $this->builder->where(function ($query) use ($value) {
            $query->where('name', 'like', "$value%");
        });
        return $this->builder;
    }
}
