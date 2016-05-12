<?php

use Carbon\Carbon;
use Illuminate\Support\Collection;

class LaravelAnalyticsTest extends PHPUnit_Framework_TestCase
{
    protected $client;
    protected $laravelAnalytics;
    protected $siteId;

    public function setUp()
    {
        $this->client = Mockery::mock('\Spatie\LaravelAnalytics\GoogleApiHelper');
        $this->siteId = '12345';

        $this->laravelAnalytics = new \Spatie\LaravelAnalytics\LaravelAnalytics($this->client, $this->siteId);
    }

    /**
     * Test method getVisitorsAndPageViews().
     */
    public function testGetVisitorsAndPageViews()
    {
        $startDate = Carbon::now()->subDays('365')->format('Y-m-d');

        $endDate = Carbon::now()->format('Y-m-d');

        $this->client
            ->shouldReceive('performQuery')
            ->with($this->siteId, $startDate, $endDate, 'ga:visits,ga:pageviews', ['dimensions' => 'ga:date'])
            ->andReturn((object) ['rows' => [['20140101', 2, 3]]]);

        $googleResult = $this->laravelAnalytics->getVisitorsAndPageViews();

        $resultProperties = ['date', 'visitors', 'pageViews'];

        $this->assertTrue(count($googleResult) === 1);

        foreach ($resultProperties as $property) {
            $this->assertArrayHasKey($property, $googleResult[0]);
        }
    }

    /**
     * Test method getTopKeywords().
     */
    public function testGetTopKeywords()
    {
        $startDate = Carbon::now()->subDays('365')->format('Y-m-d');

        $endDate = Carbon::now()->format('Y-m-d');

        $this->client
            ->shouldReceive('performQuery')
            ->with($this->siteId, $startDate, $endDate, 'ga:sessions', ['dimensions' => 'ga:keyword', 'sort' => '-ga:sessions', 'max-results' => 30, 'filters' => 'ga:keyword!=(not set);ga:keyword!=(not provided)'])
            ->andReturn((object) ['rows' => [['first', 'second']]]);

        $googleResult = $this->laravelAnalytics->getTopKeyWords();

        $this->assertEquals($googleResult, new Collection([['keyword' => 'first', 'sessions' => 'second']]));
    }

    /**
     * Test method getTopReferrers().
     */
    public function testGetTopReferrers()
    {
        $startDate = Carbon::now()->subDays('365')->format('Y-m-d');

        $endDate = Carbon::now()->format('Y-m-d');

        $this->client
            ->shouldReceive('performQuery')
            ->with($this->siteId, $startDate, $endDate, 'ga:pageviews', ['dimensions' => 'ga:fullReferrer', 'sort' => '-ga:pageviews', 'max-results' => 20])
            ->andReturn((object) ['rows' => [['foundUrl', '123']]]);

        $googleResult = $this->laravelAnalytics->getTopReferrers();

        $this->assertEquals($googleResult, new Collection([['url' => 'foundUrl', 'pageViews' => '123']]));
    }

    /**
     * Test method getTopReferrers().
     */
    public function testGetTopBrowsers()
    {
        $startDate = Carbon::now()->subDays('365')->format('Y-m-d');

        $endDate = Carbon::now()->format('Y-m-d');

        $this->client
            ->shouldReceive('performQuery')
            ->with($this->siteId, $startDate, $endDate, 'ga:sessions', ['dimensions' => 'ga:browser', 'sort' => '-ga:sessions'])
            ->andReturn((object) ['rows' => [['Google Chrome', '123']]]);

        $googleResult = $this->laravelAnalytics->getTopBrowsers();

        $this->assertEquals($googleResult, new Collection([['browser' => 'Google Chrome', 'sessions' => '123']]));
    }

    /**
     * Test method getTopReferrers().
     */
    public function testGetMostVisitedPages()
    {
        $startDate = Carbon::now()->subDays('365')->format('Y-m-d');

        $endDate = Carbon::now()->format('Y-m-d');

        $this->client
            ->shouldReceive('performQuery')
            ->with($this->siteId, $startDate, $endDate, 'ga:pageviews', ['dimensions' => 'ga:pagePath', 'sort' => '-ga:pageviews', 'max-results' => 20])
            ->andReturn((object) ['rows' => [['visited url', '123']]]);

        $googleResult = $this->laravelAnalytics->getMostVisitedPages();

        $this->assertEquals($googleResult, new Collection([['url' => 'visited url', 'pageViews' => '123']]));
    }

    /**
     * Test method getSiteIdByUrl().
     */
    public function testGetSiteIdByUrl()
    {
        $testUrl = 'www.google.com';
        $siteId = 12345;

        $this->client->shouldReceive('getSiteIdByUrl')->with($testUrl)->andReturn($siteId);

        $result = $this->laravelAnalytics->getSiteIdByUrl($testUrl);

        $this->assertEquals($result, $siteId);
    }

    /**
     * Test method performQuery().
     */
    public function testPerformQuery()
    {
        $startDate = Carbon::now()->subDays('365');

        $endDate = Carbon::now();

        $metrics = 'ga:somedummymetric';
        $others = ['first', 'second'];

        $queryResult = 'result';

        $this->client
            ->shouldReceive('performQuery')
            ->with($this->siteId, $startDate->format('Y-m-d'), $endDate->format('Y-m-d'), $metrics, $others)
            ->andReturn($queryResult);

        $googleResult = $this->laravelAnalytics->performQuery($startDate, $endDate, $metrics, $others);

        $this->assertSame($googleResult, $queryResult);
    }

    /*
     * Test method isEnabled()
     */
    public function testIsEnabled()
    {
        $enabledAnalytics = new \Spatie\LaravelAnalytics\LaravelAnalytics($this->client, $this->siteId);
        $this->assertTrue($enabledAnalytics->isEnabled());

        $disabledAnalytics = new \Spatie\LaravelAnalytics\LaravelAnalytics($this->client);
        $this->assertFalse($disabledAnalytics->isEnabled());
    }

    /**
     * Test method performRealTimeQuery().
     */
    public function testPerformRealTimeQuery()
    {
        $metrics = 'rt:somedummymetric';
        $others = ['first', 'second'];

        $queryResult = 'result';

        $this->client
            ->shouldReceive('performRealTimeQuery')
            ->with($this->siteId, $metrics, $others)
            ->andReturn($queryResult);

        $googleResult = $this->laravelAnalytics->performRealTimeQuery($metrics, $others);

        $this->assertSame($googleResult, $queryResult);
    }

    /**
     * Test method getActiveUsers().
     */
    public function testGetActiveUsers()
    {
        $others = ['first', 'second'];
        $metrics = 'rt:activeUsers';

        $this->client
            ->shouldReceive('performRealTimeQuery')
            ->with($this->siteId, $metrics, $others)
            ->andReturn((object) ['rows' => [[0, '500']]]);

        $googleResult = $this->laravelAnalytics->getActiveUsers($others);

        $this->assertInternalType('int', $googleResult);
    }
}
