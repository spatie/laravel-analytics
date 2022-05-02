<?php

use Carbon\Carbon;
use Spatie\Analytics\Exceptions\InvalidPeriod;
use Spatie\Analytics\Period;

it('can_create_a_period_for_a_given_amount_of_days', function () {
    Carbon::setTestNow(Carbon::create(2016, 1, 1));

    $period = Period::days(10);

    expect($period->startDate->format('Y-m-d'))->toBe('2015-12-22');
    expect($period->endDate->format('Y-m-d'))->toBe('2016-01-01');
});

it('can_create_a_period_for_a_given_amount_of_months', function () {
    Carbon::setTestNow(Carbon::create(2016, 1, 10));

    $period = Period::months(10);

    expect($period->startDate->format('Y-m-d'))->toBe('2015-03-10');
    expect($period->endDate->format('Y-m-d'))->toBe('2016-01-10');
});

it('can_create_a_period_for_a_given_amount_of_years', function () {
    Carbon::setTestNow(Carbon::create(2016, 1, 12));

    $period = Period::years(2);

    expect($period->startDate->format('Y-m-d'))->tobe('2014-01-12');
    expect($period->endDate->format('Y-m-d'))->tobe('2016-01-12');
});

it('provides_a_create_method', function () {
    $startDate = Carbon::create(2015, 12, 22);
    $endDate = Carbon::create(2016, 1, 1);

    $period = Period::create($startDate, $endDate);

    expect($period->startDate)->toBe($startDate);
    expect($period->endDate)->toBe($endDate);
});

it('accepts_datetime_immutable_instances', function () {
    $startDate = Carbon::create(2015, 12, 22)->toIso8601String();
    $startDateImmutable = new DateTimeImmutable($startDate);
    $endDate = Carbon::create(2016, 1, 1)->toIso8601String();
    $endDateImmutable = new DateTimeImmutable($endDate);

    $period = Period::create($startDateImmutable, $endDateImmutable);

    expect($period->startDate->format('Y-m-d'))->toBe('2015-12-22');
    expect($period->endDate->format('Y-m-d'))->toBe('2016-01-01');
});

it('will_throw_an_exception_if_the_start_date_comes_after_the_end_date', function () {
    $startDate = Carbon::create(2016, 1, 1);
    $endDate = Carbon::create(2015, 1, 1);

    Period::create($startDate, $endDate);
})->throws(InvalidPeriod::class);
