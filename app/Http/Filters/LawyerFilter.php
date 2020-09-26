<?php

namespace App\Http\Filters;

class LawyerFilter extends Filters
{
    protected $filters = ['name'];

    protected $sort = [
        'name'
    ];

    public function name($value)
    {
        $this->builder->where(function ($query) use ($value) {
            $query->where('name', 'like', "$value%");
        });
        return $this->builder;
    }
}
