<?php

namespace Botble\Analytics\Facades;

use Botble\Analytics\Abstracts\AnalyticsAbstract;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \Illuminate\Support\Collection|\Google\Service\Analytics\GaData|array|null performQuery(\Botble\Analytics\Period $period, string $metrics, array $others = [])
 * @method static \Illuminate\Support\Collection fetchMostVisitedPages(\Botble\Analytics\Period $period, int $maxResults = 20)
 * @method static \Illuminate\Support\Collection fetchTopReferrers(\Botble\Analytics\Period $period, int $maxResults = 20)
 * @method static \Illuminate\Support\Collection fetchUserTypes(\Botble\Analytics\Period $period)
 * @method static \Illuminate\Support\Collection fetchTopBrowsers(\Botble\Analytics\Period $period, int $maxResults = 10)
 * @method static \Google_Service_Analytics getAnalyticsService()
 * @method static string getPropertyId()
 * @method static static setPropertyId(string $propertyId)
 * @method static void macro(string $name, object|callable $macro)
 * @method static void mixin(object $mixin, bool $replace = true)
 * @method static bool hasMacro(string $name)
 * @method static void flushMacros()
 *
 * @see \Botble\Analytics\Analytics
 */
class Analytics extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return AnalyticsAbstract::class;
    }
}
