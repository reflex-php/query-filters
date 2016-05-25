<?php

namespace Reflex\QueryFiltering;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

abstract class QueryFilters
{
    /**
     * The request object.
     *
     * @var Request
     */
    protected $request;

    /**
     * The builder instance.
     *
     * @var Builder
     */
    protected $builder;

    /**
     * Create a new QueryFilters instance.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Apply the filters to the builder.
     *
     * @param  Builder $builder
     * @return Builder
     */
    public function apply(Builder $builder)
    {
        $this->builder = $builder;

        collect($this->filters)->map(function ($name, $value) {
            $methodName = $this->buildMethodName($name);

            if (! method_exists($this, $methodName)) {
                return;
            }

            call_user_method_array($methodName, $this, [$value]);

            // if (strlen($value)) {
            //     $this->$methodName($value);
            // } else {
            //     $this->$methodName();
            // }
        });

        return $this->builder;
    }

    protected function buildMethodName($key)
    {
        return 'filter' . studly_case($key);
    }

    /**
     * Get all request filters data.
     *
     * @return array
     */
    public function filters()
    {
        return $this->request->all();
    }
}
