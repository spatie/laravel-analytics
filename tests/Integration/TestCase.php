<?php

namespace Spatie\Analytics\Tests\Integration;

use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\Analytics\AnalyticsFacade;
use Spatie\Analytics\AnalyticsServiceProvider;

abstract class TestCase extends Orchestra
{
    public function setUp()
    {
        parent::setUp();
    }

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

    protected function getPackageAliases($app)
    {
        return [
            'Analytics' => AnalyticsFacade::class,
        ];
    }

    /**
     * Set up the environment.
     *
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        //$app['config']->set('view.paths', [__DIR__.'/resources/views']);
    }
}
