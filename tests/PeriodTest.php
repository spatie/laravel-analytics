<?php

use Carbon\Carbon;
use Spatie\Analytics\Exceptions\InvalidPeriod;
use Spatie\Analytics\Period;

it('can_create_a_period_for_a_given_amount_of_days', function () {
    $expectedDate = Carbon::create(2016, 1, 1);
    Carbon::setTestNow($expectedDate);

    $period = Period::days(10);

    expect($period)
        ->endDate->toEqual($expectedDate)
        ->startDate->toEqual($expectedDate->subDays(10));
});

it('can_create_a_period_for_a_given_amount_of_months', function () {
    $expectedDate = Carbon::create(2016, 1, 10);
    Carbon::setTestNow($expectedDate);

    $period = Period::months(10);

    expect($period)
        ->endDate->toEqual($expectedDate)
        ->startDate->toEqual($expectedDate->subMonths(10));
});

it('can_create_a_period_for_a_given_amount_of_years', function () {
    $expectedDate = Carbon::create(2016, 1, 12);
    Carbon::setTestNow($expectedDate);

    $period = Period::years(2);

    expect($period)
        ->endDate->toEqual($expectedDate)
        ->startDate->toEqual($expectedDate->subYears(2));
});

it('provides_a_create_method', function () {
    $startDate = Carbon::create(2015, 12, 22);
    $endDate = Carbon::create(2016, 1, 1);

    $period = Period::create($startDate, $endDate);

    expect($period)
        ->startDate->toBe($startDate)
        ->endDate->toBe($endDate);
});

it('accepts_datetime_immutable_instances', function () {
    $startDate = Carbon::create(2015, 12, 22);
    $startDateImmutable = new DateTimeImmutable($startDate->toIso8601String());

    $endDate = Carbon::create(2016, 1, 1);
    $endDateImmutable = new DateTimeImmutable($endDate->toIso8601String());

    $period = Period::create($startDateImmutable, $endDateImmutable);

    expect($period)
        ->startDate->toEqual($startDate)
        ->endDate->toEqual($endDate);
});

it('will_throw_an_exception_if_the_start_date_comes_after_the_end_date', function () {
    $startDate = Carbon::create(2016, 1, 1);
    $endDate = Carbon::create(2015, 1, 1);

    Period::create($startDate, $endDate);
})->throws(InvalidPeriod::class);
