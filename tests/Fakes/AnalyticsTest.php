<?php

use Illuminate\Support\Collection;
use Spatie\Analytics\Fakes\Analytics;
use Spatie\Analytics\Period;

it('can intercept method calls and return specified result', function () {
    // Arrange
    $expectedResult = Collection::make([
        ['pageTitle' => 'Test Page', 'activeUsers' => 10, 'screenPageViews' => 20],
    ]);

    $instance = new Analytics($expectedResult);

    // Act
    $result = $instance->fetchVisitorsAndPageViews(Period::days(7));

    // Assert
    expect($result)->toEqual($expectedResult);
});
