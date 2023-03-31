<?php

namespace Spatie\Analytics;

use Illuminate\Support\Carbon;

class TypeCaster
{
    public function castValue(string $key, string $value): mixed
    {
        return match ($key) {
            'date' => Carbon::createFromFormat('Ymd', $value),
            'visitors', 'pageViews', 'activeUsers', 'newUsers', 'screenPageViews',
            'active1DayUsers', 'active7DayUsers', 'active28DayUsers' => (int) $value,
            default => $value,
        };
    }
}
