<?php

namespace Spatie\Analytics;

use Illuminate\Support\Facades\Facade;

class LaravelAnalyticsFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-analytics';
    }
}
