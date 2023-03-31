<?php

use Google\Analytics\Data\V1beta\OrderBy as GoogleOrderBy;
use Spatie\Analytics\OrderBy;

it('should create GoogleOrderBy objects for dimensions', function () {
    $result = OrderBy::dimension('dimension', true);

    expect(get_class($result))->toBe(GoogleOrderBy::class)
        ->and($result->getDimension()->getDimensionName())->toBe('dimension')
        ->and($result->getDesc())->toBeTrue();
});

it('should create GoogleOrderBy objects for metrics', function () {
    $result = OrderBy::metric('metric', true);

    expect(get_class($result))->toBe(GoogleOrderBy::class)
        ->and($result->getMetric()->getMetricName())->toBe('metric')
        ->and($result->getDesc())->toBeTrue();
});
