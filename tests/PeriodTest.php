<?php

use Carbon\Carbon;
use Spatie\Analytics\Exceptions\InvalidPeriod;
use Spatie\Analytics\Period;

it('can create a period for a given amount of days', function () {
    $expectedDate = Carbon::create(2016, 1, 1);
    Carbon::setTestNow($expectedDate);

    $period = Period::days(10);

    expect($period)
        ->endDate->toEqual($expectedDate)
        ->startDate->toEqual($expectedDate->subDays(10));
});

it('can create a period for a given amount of months', function () {
    $expectedDate = Carbon::create(2016, 1, 10);
    Carbon::setTestNow($expectedDate);

    $period = Period::months(10);

    expect($period)
        ->endDate->toEqual($expectedDate)
        ->startDate->toEqual($expectedDate->subMonths(10));
});

it('can create a period for a given amount of years', function () {
    $expectedDate = Carbon::create(2016, 1, 12);
    Carbon::setTestNow($expectedDate);

    $period = Period::years(2);

    expect($period)
        ->endDate->toEqual($expectedDate)
        ->startDate->toEqual($expectedDate->subYears(2));
});

it('provides a create method', function () {
    $startDate = Carbon::create(2015, 12, 22);
    $endDate = Carbon::create(2016, 1, 1);

    $period = Period::create($startDate, $endDate);

    expect($period)
        ->startDate->toBe($startDate)
        ->endDate->toBe($endDate);
});

it('accepts datetime immutable instances', function () {
    $startDate = Carbon::create(2015, 12, 22);
    $startDateImmutable = new DateTimeImmutable($startDate->toIso8601String());

    $endDate = Carbon::create(2016, 1, 1);
    $endDateImmutable = new DateTimeImmutable($endDate->toIso8601String());

    $period = Period::create($startDateImmutable, $endDateImmutable);

    expect($period)
        ->startDate->toEqual($startDate)
        ->endDate->toEqual($endDate);
});

it('will throw an exception if the start date comes after the end date', function () {
    $startDate = Carbon::create(2016, 1, 1);
    $endDate = Carbon::create(2015, 1, 1);

    Period::create($startDate, $endDate);
})->throws(InvalidPeriod::class);
