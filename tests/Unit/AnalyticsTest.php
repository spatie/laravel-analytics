<?php

namespace Spatie\Analytics\Tests;

use Carbon\Carbon;
use Mockery;
use PHPUnit_Framework_TestCase;
use Spatie\Analytics\Analytics;
use Spatie\Analytics\AnalyticsClient;

class AnalyticsTest extends PHPUnit_Framework_TestCase
{
    /** @var \Spatie\Analytics\AnalyticsClient|\Mockery\Mock */
    protected $analyticsClient;

    /** @var string */
    protected $viewId;

    /** @var Analytics */
    protected $analytics;

    public function setUp()
    {
        Carbon::setTestNow(Carbon::create(2016, 1, 1));

        $this->analyticsClient = Mockery::mock(AnalyticsClient::class);

        $this->viewId = '1234567';

        $this->analytics = new Analytics($this->analyticsClient, $this->viewId);
    }

    public function tearDown()
    {
        Mockery::close();
    }

    /** @test */
    public function it_can_retrieve()
    {
        $expectedArguments = [
            $this->viewId,
            Carbon::now()->subDay(365)->startOfDay(),
          //  Carbon::now()->startOfDay(),
          //  'ga:users,ga:pageviews',
          //  ['dimensions' => 'ga:date']

        ];

        $this->analyticsClient
            ->shouldReceive('performQuery')->withArgs($expectedArguments)
            ->andReturnNull();

        $this->analytics->getVisitorsAndPageViews();
    }
}
