<?php

namespace Spatie\Analytics\Tests\Integration;

use Analytics;
use Carbon\Carbon;
use Spatie\Analytics\Exceptions\InvalidConfiguration;

class AnalyticsServiceProviderTest extends TestCase
{
    /** @test */
    public function it_will_throw_an_exception_if_the_view_id_is_not_set()
    {
        $this->app['config']->set('laravel-analytics.view_id', '');

        $this->setExpectedException(InvalidConfiguration::class);
        
        Analytics::fetchVisitorsAndPageViews(Carbon::now()->subDay(), Carbon::now());
    }
}
