<?php

namespace Spatie\Analytics\Facade;

use Illuminate\Support\Facades\Facade;

/**
 * Analytics facade.
 *
 * @method static \Spatie\Analytics\Analytics setPropertyId(string $propertyId)
 * @method static string getPropertyId()
 * @method static \Spatie\Analytics\Analytics get()
 * @method static \Illuminate\Support\Collection fetchVisitorsAndPageViews(\Spatie\Analytics\Period $period)
 */
class Analytics extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'laravel-analytics';
    }
}
