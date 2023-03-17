<?php

namespace Spatie\Analytics\Tests\Integration;

use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\Analytics\AnalyticsServiceProvider;
use Spatie\Analytics\Facades\Analytics;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            AnalyticsServiceProvider::class,
        ];
    }
}
