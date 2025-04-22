<?php

use Spatie\Analytics\Facades\Analytics;

it('can fake analytics class', function () {
    // Act
    Analytics::fake();

    // Assert
    expect(Analytics::isFake())->toBeTrue();
});