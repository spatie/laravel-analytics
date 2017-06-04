<?php

namespace Spatie\Analytics;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Spatie\Analytics\Analytics
 */
class AnalyticsFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-analytics';
    }
}
