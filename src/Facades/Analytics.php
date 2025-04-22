<?php

namespace Spatie\Analytics\Facades;

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

    public static function fake()
    {
        return static::swap(new AnalyticsFake());
    }
}
