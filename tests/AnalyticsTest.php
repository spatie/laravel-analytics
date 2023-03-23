<?php

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Spatie\Analytics\Analytics;
use Spatie\Analytics\AnalyticsClient;
use Spatie\Analytics\OrderBy;
use Spatie\Analytics\Period;

beforeEach(function () {
    $this->analyticsClient = Mockery::mock(AnalyticsClient::class);

    $this->propertyId = '1234567';

    $this->analytics = new Analytics($this->analyticsClient, $this->propertyId);

    $this->startDate = Carbon::now()->subDays(7);

    $this->endDate = Carbon::now();
});

afterEach(fn () => Mockery::close());

it('can fetch the visitor and page views', function () {
    $period = Period::create($this->startDate, $this->endDate);

    $expectedArguments = [
        $this->propertyId,
        $period,
        ['activeUsers', 'screenPageViews'],
        ['pageTitle'],
    ];

    $this
        ->analyticsClient
        ->shouldReceive('get')
        ->withArgs($expectedArguments)
        ->once()
        ->andReturn(collect([
            [
                'pageTitle' => 'pageTitle',
                'activeUsers' => 1,
                'screenPageViews' => 2,
            ],
        ]));

    $response = $this
        ->analytics
        ->fetchVisitorsAndPageViews($period);

    expect($response)->toBeInstanceOf(Collection::class)
        ->and($response->first()['pageTitle'])->toBe('pageTitle')
        ->and($response->first()['activeUsers'])->toBe(1)
        ->and($response->first()['screenPageViews'])->toBe(2);
});

it('can fetch the total visitor and page views', function () {
    $period = Period::create($this->startDate, $this->endDate);

    $expectedArguments = [
        $this->propertyId,
        $period,
        ['activeUsers', 'screenPageViews'],
        ['date'],
        20,
        [
            OrderBy::dimension('date', true),
        ],
    ];

    $this
        ->analyticsClient
        ->shouldReceive('get')
        ->withArgs($expectedArguments)
        ->once()
        ->andReturn(collect([
            [
                'date' => Carbon::createFromFormat('Ymd', '20160101'),
                'activeUsers' => 1,
                'screenPageViews' => 2,
            ],
        ]));

    $response = $this
        ->analytics
        ->fetchTotalVisitorsAndPageViews($period);

    $firstItem = $response->first();

    expect($response)->toBeInstanceOf(Collection::class)
        ->and($firstItem['date']->format('Y-m-d'))->toBe(Carbon::parse('2016-01-01')->format('Y-m-d'))
        ->and($firstItem['activeUsers'])->toBe(1)
        ->and($firstItem['screenPageViews'])->toBe(2);
});

it('can fetch the most visited pages', function () {
    $maxResults = 10;

    $expectedArguments = [
        $this->viewId,
        expectCarbon($this->startDate),
        expectCarbon($this->endDate),
        'ga:pageviews',
        [
            'dimensions' => 'ga:pagePath,ga:pageTitle',
            'sort' => '-ga:pageviews',
            'max-results' => $maxResults,
        ],
    ];

    $this
        ->analyticsClient
        ->shouldReceive('performQuery')
        ->withArgs($expectedArguments)
        ->once()
        ->andReturn([
            'rows' => [['https://test.com', 'Page title', '123']],
        ]);

    $response = $this
        ->analytics
        ->fetchMostVisitedPages(
            Period::create($this->startDate, $this->endDate),
            $maxResults
        );

    expect($response)->toBeInstanceOf(Collection::class);
    expect($response->first()['url'])->toBe('https://test.com');
    expect($response->first()['pageTitle'])->toBe('Page title');
    expect($response->first()['pageViews'])->toBe(123);
});

it('can fetch the top referrers', function () {
    $maxResults = 10;

    $expectedArguments = [
        $this->viewId,
        expectCarbon($this->startDate),
        expectCarbon($this->endDate),
        'ga:pageviews',
        [
            'dimensions' => 'ga:fullReferrer',
            'sort' => '-ga:pageviews',
            'max-results' => $maxResults,
        ],
    ];

    $this
        ->analyticsClient
        ->shouldReceive('performQuery')
        ->withArgs($expectedArguments)
        ->once()
        ->andReturn([
            'rows' => [['https://referrer.com', '123']],
        ]);

    $response = $this
        ->analytics
        ->fetchTopReferrers(
            Period::create($this->startDate, $this->endDate),
            $maxResults
        );

    expect($response)->toBeInstanceOf(Collection::class);
    expect($response->first()['url'])->toBe('https://referrer.com');
    expect($response->first()['pageViews'])->toBe(123);
});

it('can fetch the top browsers', function () {
    $expectedArguments = [
        $this->viewId,
        expectCarbon($this->startDate),
        expectCarbon($this->endDate),
        'ga:sessions',
        ['dimensions' => 'ga:browser', 'sort' => '-ga:sessions'],
    ];

    $this
        ->analyticsClient
        ->shouldReceive('performQuery')
        ->withArgs($expectedArguments)
        ->once()
        ->andReturn([
            'rows' => [
                ['Browser 1', '100'],
                ['Browser 2', '90'],
                ['Browser 3', '30'],
                ['Browser 4', '20'],
                ['Browser 1', '10'],
            ],
        ]);

    $response = $this
        ->analytics
        ->fetchTopBrowsers(
            Period::create($this->startDate, $this->endDate),
            3
        );

    expect($response)->toBeInstanceOf(Collection::class);
    expect($response->toArray())->toBe([
        ['browser' => 'Browser 1', 'sessions' => 100],
        ['browser' => 'Browser 2', 'sessions' => 90],
        ['browser' => 'Others', 'sessions' => 60],
    ]);
});

function expectCarbon(Carbon $carbon)
{
    return Mockery::on(function (Carbon $argument) use ($carbon) {
        return $argument->format('Y-m-d') === $carbon->format('Y-m-d');
    });
}
