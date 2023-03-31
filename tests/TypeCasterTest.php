<?php

use Illuminate\Support\Carbon;
use Spatie\Analytics\TypeCaster;

it('should cast a date to a Carbon object', function () {
    $typeCaster = resolve(TypeCaster::class);
    $value = $typeCaster->castValue('date', '20210101');
    expect($value)->toBeInstanceOf(Carbon::class);
});

it('should cast integers', function () {
    $typeCaster = resolve(TypeCaster::class);

    $value = $typeCaster->castValue('visitors', '4');
    expect($value)->toBeInt()->toBe(4);

    $value = $typeCaster->castValue('pageViews', '8');
    expect($value)->toBeInt()->toBe(8);

    $value = $typeCaster->castValue('activeUsers', '15');
    expect($value)->toBeInt()->toBe(15);

    $value = $typeCaster->castValue('newUsers', '16');
    expect($value)->toBeInt()->toBe(16);

    $value = $typeCaster->castValue('screenPageViews', '23');
    expect($value)->toBeInt()->toBe(23);

    $value = $typeCaster->castValue('active1DayUsers', '42');
    expect($value)->toBeInt()->toBe(42);
});

it('should return a string as a default', function () {
    $typeCaster = resolve(TypeCaster::class);
    $value = $typeCaster->castValue('foo', 'bar');
    expect($value)->toBeString()->toBe('bar');
});
