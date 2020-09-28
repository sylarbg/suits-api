<?php

namespace App\Http\Filters;

class AppointmentFilter extends Filters
{
    protected $filters = ['name', 'status'];

    protected $sort = [
        'scheduled_for'
    ];

    public function name($value)
    {
        $relation = $this->request->user()->isLawyer() ? 'citizen' : 'lawyer';

        return $this->builder->whereHas($relation, function ($query) use ($value) {
            $query->where('name', 'like', "$value%");
        });
    }

    public function status($value)
    {
        return is_array($value) ? $this->builder->whereIn('status', $value) : $this->builder->where('status', $value);
    }
}
