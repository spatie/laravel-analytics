<?php

use Google\Analytics\Data\V1beta\DimensionValue;
use Google\Analytics\Data\V1beta\MetricValue;
use Google\Analytics\Data\V1beta\Row;
use Google\Analytics\Data\V1beta\RunReportResponse;
use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\Repository as CacheRepository;

function createFakeRunReportResponse(): RunReportResponse
{
    $row = new Row();
    $row->setDimensionValues([new DimensionValue(['value' => '20260329'])]);
    $row->setMetricValues([new MetricValue(['value' => '42'])]);

    $response = new RunReportResponse();
    $response->setRows([$row]);

    return $response;
}

it('returns __PHP_Incomplete_Class when caching raw protobuf objects with serializable_classes false', function () {
    $original = createFakeRunReportResponse();
    $store = new ArrayStore(serializesValues: true, serializableClasses: false);
    $cache = new CacheRepository($store);

    $cache->put('analytics.test', $original, 3600);
    $cached = $cache->get('analytics.test');

    expect($cached)->toBeInstanceOf(__PHP_Incomplete_Class::class);
    expect($cached)->not->toBeInstanceOf(RunReportResponse::class);
});

it('restores a valid RunReportResponse when caching protobuf binary string with serializable_classes false', function () {
    $original = createFakeRunReportResponse();
    $store = new ArrayStore(serializesValues: true, serializableClasses: false);
    $cache = new CacheRepository($store);

    $cache->put('analytics.test', $original->serializeToString(), 3600);
    $cached = $cache->get('analytics.test');

    $restored = new RunReportResponse();
    $restored->mergeFromString($cached);

    expect($cached)->toBeString();
    expect($restored)->toBeInstanceOf(RunReportResponse::class);
    expect($restored->getRows())->toHaveCount(1);
    expect($restored->getRows()[0]->getDimensionValues()[0]->getValue())->toBe('20260329');
    expect($restored->getRows()[0]->getMetricValues()[0]->getValue())->toBe('42');
});
