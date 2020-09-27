<?php

namespace App\Http\Filters;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;

abstract class Filters
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * The Eloquent builder.
     *
     * @var Builder
     */
    protected $builder;

    /**
     * Registered filters to operate upon.
     *
     * @var array
     */
    protected $filters = [];

    /**
     * Registered sort options
     *
     * @var array
     */
    protected $sort = [];

    /**
     * Create a new Filter instance.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Apply the filters.
     *
     * @param Builder $builder
     * @return Builder
     * @throws \Exception
     */
    public function apply($builder)
    {
        $this->builder = $builder;

        foreach ($this->getFilters() as $filter => $value) {
            $methodName = Str::camel($filter);
            if (method_exists($this, $methodName)) {
                $this->$methodName($value);
            }
        }

        $this->sorting();
        return $this->builder;
    }

    /**
     * Fetch all relevant filters from the request.
     *
     * @param bool $skipEmpty
     * @return array
     */
    public function getFilters($skipEmpty = true)
    {
        $filters = $this->request->only($this->filters);

        return $skipEmpty ? array_filter($filters) : $filters;
    }

    /**
     * Fetch order param from the request.
     *
     * @return Builder
     * @throws \Exception
     */
    public function sorting()
    {
        $sort = $this->request->get('order', null);
        $field = $this->sanitize(ltrim($sort, '-'));

        $isAllowed = in_array($field, $this->sort);
        if (!$isAllowed) {
            return;
        }

        $status = preg_match('/^\-.*/', $sort);
        $direction = $status ? 'DESC' : 'ASC';

        return $this->order($field, $direction);
    }

    protected function order($field, $direction)
    {
        $this->builder->orderBy($field, $direction);

        return $this->builder;
    }

    protected function sanitize($value)
    {
        if (! preg_match("/^(?![0-9])[A-Za-z0-9_.-]*$/", $value)) {
            throw new \Exception(
                "Column name may contain only alphanumerics or underscores, and may not begin with a digit."
            );
        }
        return $value;
    }
}
