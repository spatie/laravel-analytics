<?php

use Google\Analytics\Data\V1beta\Client\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\DimensionValue;
use Google\Analytics\Data\V1beta\MetricValue;
use Google\Analytics\Data\V1beta\Row;
use Google\Analytics\Data\V1beta\RunReportResponse;
use Google\Auth\Credentials\InsecureCredentials;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\Repository as CacheRepository;
use Spatie\Analytics\AnalyticsClient;
use Spatie\Analytics\Period;

$makeResponse = function (): RunReportResponse {
    $row = new Row;
    $row->setDimensionValues([new DimensionValue(['value' => '20260329'])]);
    $row->setMetricValues([new MetricValue(['value' => '42'])]);

    $response = new RunReportResponse;
    $response->setRows([$row]);

    return $response;
};

$arrayStoreSupportsSerializableClasses = fn (): bool => (new ReflectionMethod(ArrayStore::class, '__construct'))
    ->getNumberOfParameters() >= 2;

it('returns __PHP_Incomplete_Class when caching raw protobuf objects with serializable_classes false', function () use ($makeResponse) {
    $original = $makeResponse();
    $store = new ArrayStore(serializesValues: true, serializableClasses: false);
    $cache = new CacheRepository($store);

    $cache->put('analytics.test', $original, 3600);
    $cached = $cache->get('analytics.test');

    expect($cached)->toBeInstanceOf(__PHP_Incomplete_Class::class);
    expect($cached)->not->toBeInstanceOf(RunReportResponse::class);
})->skip(
    fn () => ! $arrayStoreSupportsSerializableClasses(),
    'Requires ArrayStore with serializableClasses parameter (Laravel 12.53+ or 13+)',
);

it('fetches and caches reports through the Google REST client', function (string $method, string $endpoint) {
    $httpHandler = new MockHandler([
        new Response(200, ['Content-Type' => 'application/json'], json_encode([
            'rows' => [[
                'dimensionValues' => [['value' => 'Belgium'], ['value' => 'Ghent']],
                'metricValues' => [['value' => '42'], ['value' => '73']],
            ]],
        ])),
    ]);

    $service = new BetaAnalyticsDataClient([
        'credentials' => new InsecureCredentials,
        'transport' => 'rest',
        'transportConfig' => ['rest' => ['httpHandler' => $httpHandler]],
    ]);

    $client = new AnalyticsClient($service, new CacheRepository(new ArrayStore(serializesValues: true)));
    $client->setCacheLifeTimeInMinutes(60);

    $period = Period::create(new DateTimeImmutable('2026-03-29 10:00'), new DateTimeImmutable('2026-03-29 10:05'));
    $metrics = ['activeUsers', 'screenPageViews'];
    $dimensions = ['country', 'city'];

    $result = $client->$method('1234567', $period, $metrics, $dimensions, 3);

    expect($result->all())->toBe([[
        'country' => 'Belgium',
        'city' => 'Ghent',
        'activeUsers' => 42,
        'screenPageViews' => 73,
    ]]);

    expect($httpHandler->getLastRequest()->getUri()->getPath())->toBe("/v1beta/properties/1234567:{$endpoint}");

    $request = json_decode((string) $httpHandler->getLastRequest()->getBody(), true);

    expect($request['metrics'])->toBe([['name' => 'activeUsers'], ['name' => 'screenPageViews']])
        ->and($request['dimensions'])->toBe([['name' => 'country'], ['name' => 'city']])
        ->and((int) $request['limit'])->toBe(3);

    expect($client->$method('1234567', $period, $metrics, $dimensions, 3)->all())->toBe($result->all());
})->with([
    'standard' => ['get', 'runReport'],
    'realtime' => ['getRealtime', 'runRealtimeReport'],
]);

it('restores a valid RunReportResponse when caching protobuf binary string with serializable_classes false', function () use ($makeResponse) {
    $original = $makeResponse();
    $store = new ArrayStore(serializesValues: true, serializableClasses: false);
    $cache = new CacheRepository($store);

    $cache->put('analytics.test', $original->serializeToString(), 3600);
    $cached = $cache->get('analytics.test');

    $restored = new RunReportResponse;
    $restored->mergeFromString($cached);

    expect($cached)->toBeString();
    expect($restored)->toBeInstanceOf(RunReportResponse::class);
    expect($restored->getRows())->toHaveCount(1);
    expect($restored->getRows()[0]->getDimensionValues()[0]->getValue())->toBe('20260329');
    expect($restored->getRows()[0]->getMetricValues()[0]->getValue())->toBe('42');
})->skip(
    fn () => ! $arrayStoreSupportsSerializableClasses(),
    'Requires ArrayStore with serializableClasses parameter (Laravel 12.53+ or 13+)',
);
