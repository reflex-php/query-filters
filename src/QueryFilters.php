<?php

namespace Reflex\QueryFiltering;

use ReflectionClass;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

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
     * The reflection instance.
     *
     * @var ReflectionClass
     */
    protected $reflector;

    /**
     * Filter namespace
     *
     * @var string
     */
    protected $namespace;

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
        $this->builder   = $builder;
        $this->reflector = new ReflectionClass($this);

        $this->filters()->map(function ($value, $key) {
            // $methodName = $this->buildMethodName($key);
            $className = $this->buildClassName($key);

            if (! $this->isFilterClass($className)) {
                return;
            }

            // if (! $this->isFilterableMethod($methodName)) {
            //     return;
            // }

            if (strlen($value)) {
                with(new $className)->filterOn($value);
                // call_user_func([$this, $methodName], $value);
                return;
            }

            with(new $className)->filterOn();
            // call_user_func([$this, $methodName]);
        });

        return $this->builder;
    }

    /**
     * Is method filterable?
     *
     * @var string $methodName
     * @return boolean
     */
    protected function isFilterableMethod($methodName)
    {
        try {
            return $this->reflector->getMethod($methodName)->isPublic();
        } catch (\ReflectionException $e) {
            return false;
        }
    }

    /**
     * Check if class is a filterable instance
     * @param  string  $name Name of class to check
     * @return boolean
     */
    protected function isFilterClass($name)
    {
        return class_exists($name) && $name instanceof Filterable;
    }

    /**
     * Build class name
     * @param  string $key
     * @return string
     */
    protected function buildClassName($key)
    {
        return $this->namespace . studly_case($key);
    }

    /**
     * Create method name from key
     *
     * @var string $key
     * @return string
     */
    protected function buildMethodName($key)
    {
        return 'filtersOn' . studly_case($key);
    }

    /**
     * Get all request filters data.
     *
     * @return array
     */
    public function filters()
    {
        return collect($this->request->all());
    }
}
