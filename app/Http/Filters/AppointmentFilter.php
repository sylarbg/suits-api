<?php

namespace App\Http\Filters;

class AppointmentFilter extends Filters
{
    protected $filters = ['lawyer', 'status'];

    public function lawyer($value)
    {
        return $this->builder->whereHas('lawyer', function ($query) use ($value) {
            $query->where('name', 'like', "$value%");
        });
    }

    public function status($value)
    {
        return is_array($value) ? $this->builder->whereIn('status', $value) : $this->builder->where('status', $value);
    }
}
