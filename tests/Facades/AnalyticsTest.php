<?php

use Spatie\Analytics\Facades\Analytics;
use Spatie\Analytics\Fakes\Analytics as AnalyticsFake;

it('can fake analytics class', function () {
    // Act
    Analytics::fake();

    // Assert
    expect(Analytics::getFacadeRoot())->toBeInstanceOf(AnalyticsFake::class);
});