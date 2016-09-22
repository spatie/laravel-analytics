<?php

namespace Spatie\Analytics;

use Google_Client;
use Google_Service_Analytics;
use Illuminate\Contracts\Cache\Repository;

class AnalyticsClientFactory
{
    public static function createForConfig(Google_Client $client, array $analyticsConfig): AnalyticsClient
    {
        $googleService = new Google_Service_Analytics($client);

        return self::createAnalyticsClient($analyticsConfig, $googleService);
    }

    protected static function createAnalyticsClient(array $analyticsConfig, Google_Service_Analytics $googleService): AnalyticsClient
    {
        $client = new AnalyticsClient($googleService, app(Repository::class));

        $client->setCacheLifeTimeInMinutes($analyticsConfig['cache_lifetime_in_minutes']);

        return $client;
    }
}
