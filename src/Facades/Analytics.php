<?php

namespace Spatie\Analytics\Facades;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;
use Spatie\Analytics\Fakes\Analytics as AnalyticsFake;

/**
 * @mixin \Spatie\Analytics\Analytics
 */

class Analytics extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'laravel-analytics';
    }

    /**
     * @param array|Collection $result
     */
    public static function fake($result = [])
    {
        return static::swap(new AnalyticsFake($result));
    }
}
