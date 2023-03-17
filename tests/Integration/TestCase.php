<?php

namespace Spatie\Analytics\Tests\Integration;

use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\Analytics\AnalyticsServiceProvider;
use Spatie\Analytics\Facade\Analytics;

abstract class TestCase extends Orchestra
{
    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            AnalyticsServiceProvider::class,
        ];
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
            'Analytics' => Analytics::class,
        ];
    }
}
