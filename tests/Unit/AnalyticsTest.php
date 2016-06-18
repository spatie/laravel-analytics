<?php

namespace Spatie\Analytics\Tests;

use Carbon\Carbon;
use Illuminate\Support\Collection;
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

    /** @var \Spatie\Analytics\Analytics */
    protected $analytics;

    /** @var \Carbon\Carbon */
    protected $startDate;

    /** @var \Carbon\Carbon */
    protected $endDate;

    public function setUp()
    {
        $this->analyticsClient = Mockery::mock(AnalyticsClient::class);

        $this->viewId = '1234567';

        $this->analytics = new Analytics($this->analyticsClient, $this->viewId);

        $this->startDate = Carbon::now()->subDays(7);

        $this->endDate = Carbon::now();
    }

    public function tearDown()
    {
        Mockery::close();
    }

    /** @test */
    public function it_can_retrieve_the_visitor_and_page_views()
    {
        $expectedArguments = [
            $this->viewId,
            $this->expectCarbon($this->startDate),
            $this->expectCarbon($this->endDate),
            'ga:users,ga:pageviews', ['dimensions' => 'ga:date'],
        ];

        $this->analyticsClient
            ->shouldReceive('performQuery')->withArgs($expectedArguments)
            ->once()
            ->andReturn([
                'rows' => [['20160101', '1', '2']],
            ]);

        $response = $this->analytics->getVisitorsAndPageViews($this->startDate, $this->endDate);

        $this->assertInstanceOf(Collection::class, $response);
        $this->assertEquals('2016-01-01', $response->first()['date']->format('Y-m-d'));
        $this->assertEquals(1, $response->first()['visitors']);
        $this->assertEquals(2, $response->first()['pageViews']);
    }

    protected function expectCarbon(Carbon $carbon)
    {
        return Mockery::on(function (Carbon $argument) use ($carbon) {
            return $argument->format('Y-m-d') == $carbon->format('Y-m-d');
        });
    }
}
