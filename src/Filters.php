<?php

namespace Spatie\Analytics;

use Spatie\Analytics\Exceptions\InvalidFilter;

class Filters
{
    private $filters;

    public static function create($filters = []): Filters
    {
        return new static($filters);
    }

    public function __construct($filters = [])
    {
        foreach ($filters as $filter) {
            $this->checkFilterSyntax($filter);
        }

        $this->filters = $filters;
    }

    public function addFilter($filter)
    {
        $this->checkFilterSyntax($filter);
        $this->filters[] = $filter;
    }

    private function checkFilterSyntax($filter)
    {
        if (strpos($filter, 'ga:') !== 0) {
            throw InvalidFilter::filterMustContainGA($filter);
        }
    }

    public function __toString()
    {
        return implode(';', $this->filters);
    }
}
