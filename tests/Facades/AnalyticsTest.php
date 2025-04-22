<?php

use Spatie\Analytics\Facades\Analytics;
use Illuminate\Support\Facades\Http;

beforeEach()->only();

it('can fake analytics class', function () {
    // Act
    Analytics::fake();

    // Assert
    expect(Analytics::isFake())->toBeTrue();
});