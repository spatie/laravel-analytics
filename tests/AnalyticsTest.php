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
        10,
        [],
        0,
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

it('can fetch the visitor and page views by date', function () {
    $period = Period::create($this->startDate, $this->endDate);

    $expectedArguments = [
        $this->propertyId,
        $period,
        ['activeUsers', 'screenPageViews'],
        ['pageTitle', 'date'],
        10,
        [
            OrderBy::dimension('date', true),
        ],
        0,
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
                'date' => Carbon::createFromFormat('Ymd', '20230101'),
            ],
        ]));

    $response = $this
        ->analytics
        ->fetchVisitorsAndPageViewsByDate($period);

    expect($response)->toBeInstanceOf(Collection::class)
        ->and($response->first()['pageTitle'])->toBe('pageTitle')
        ->and($response->first()['activeUsers'])->toBe(1)
        ->and($response->first()['screenPageViews'])->toBe(2)
        ->and($response->first()['date']->format('Y-m-d'))->toBe(Carbon::parse('2023-01-01')->format('Y-m-d'));
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
        0,
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
    $maxResults = 20;
    $period = Period::create($this->startDate, $this->endDate);

    $expectedArguments = [
        $this->propertyId,
        $period,
        ['screenPageViews'],
        ['pageTitle', 'fullPageUrl'],
        $maxResults,
        [
            OrderBy::metric('screenPageViews', true),
        ],
        0,
    ];

    $this
        ->analyticsClient
        ->shouldReceive('get')
        ->withArgs($expectedArguments)
        ->once()
        ->andReturn(collect([
            [
                'pageTitle' => 'Page title',
                'fullPageUrl' => 'https://test.com',
                'screenPageViews' => 123,
            ],
        ]));

    $response = $this
        ->analytics
        ->fetchMostVisitedPages(
            $period,
            $maxResults
        );

    expect($response)->toBeInstanceOf(Collection::class)
        ->and($response->first()['fullPageUrl'])->toBe('https://test.com')
        ->and($response->first()['pageTitle'])->toBe('Page title')
        ->and($response->first()['screenPageViews'])->toBe(123);
});

it('can fetch the top referrers', function () {
    $maxResults = 10;
    $period = Period::create($this->startDate, $this->endDate);

    $expectedArguments = [
        $this->propertyId,
        $period,
        ['screenPageViews'],
        ['pageReferrer'],
        $maxResults,
        [
            OrderBy::metric('screenPageViews', true),
        ],
        0,
    ];

    $this
        ->analyticsClient
        ->shouldReceive('get')
        ->withArgs($expectedArguments)
        ->once()
        ->andReturn(
            collect([
                [
                    'pageReferrer' => 'https://referrer.com',
                    'screenPageViews' => 123,
                ],
            ])
        );

    $response = $this
        ->analytics
        ->fetchTopReferrers($period, $maxResults);

    expect($response)->toBeInstanceOf(Collection::class)
        ->and($response->first()['pageReferrer'])->toBe('https://referrer.com')
        ->and($response->first()['screenPageViews'])->toBe(123);
});

it('can fetch the top browsers', function () {
    $period = Period::create($this->startDate, $this->endDate);

    $expectedArguments = [
        $this->propertyId,
        $period,
        ['screenPageViews'],
        ['browser'],
        3,
        [
            OrderBy::metric('screenPageViews', true),
        ],
        0,
    ];

    $this
        ->analyticsClient
        ->shouldReceive('get')
        ->withArgs($expectedArguments)
        ->once()
        ->andReturn(collect(
            [
                [
                    'browser' => 'Browser 1',
                    'screenPageViews' => 100,
                ],
                [
                    'browser' => 'Browser 2',
                    'screenPageViews' => 90,
                ],
                [
                    'browser' => 'Browser 3',
                    'screenPageViews' => 60,
                ],
            ]
        ));

    $response = $this
        ->analytics
        ->fetchTopBrowsers($period, 3);

    expect($response)->toBeInstanceOf(Collection::class)
        ->and($response->toArray())->toBe([
            ['browser' => 'Browser 1', 'screenPageViews' => 100],
            ['browser' => 'Browser 2', 'screenPageViews' => 90],
            ['browser' => 'Browser 3', 'screenPageViews' => 60],
        ]);
});

function expectCarbon(Carbon $carbon)
{
    return Mockery::on(function (Carbon $argument) use ($carbon) {
        return $argument->format('Y-m-d') === $carbon->format('Y-m-d');
    });
}
