<?php
namespace Reflex\QueryFiltering;

use Illuminate\Database\Eloquent\Builder;

trait FilterableScope
{
    /**
     * Filter a result set.
     *
     * @param  Builder      $query
     * @param  QueryFilters $filters
     * @return Builder
     */
    public function scopeFilter(Builder $query, QueryFilters $filters)
    {
        return $filters->apply($query);
    }
}
