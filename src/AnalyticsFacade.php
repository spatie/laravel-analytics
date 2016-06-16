<?php

namespace Spatie\Analytics;

use Illuminate\Support\Facades\Facade;

class AnalyticsFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-analytics';
    }
}
