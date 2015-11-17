<?php

namespace Spatie\LaravelAnalytics;

use Illuminate\Support\Facades\Facade;

class LaravelAnalyticsFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravelAnalytics';
    }
}
