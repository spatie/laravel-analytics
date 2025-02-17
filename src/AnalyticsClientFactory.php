<?php

namespace Spatie\Analytics;

use Google\Analytics\Data\V1beta\Client\BetaAnalyticsDataClient;
use Illuminate\Contracts\Cache\Repository;

class AnalyticsClientFactory
{
    public static function createForConfig(array $analyticsConfig): AnalyticsClient
    {
        $authenticatedClient = self::createAuthenticatedGoogleClient($analyticsConfig);

        return self::createAnalyticsClient($analyticsConfig, $authenticatedClient);
    }

    public static function createAuthenticatedGoogleClient(array $config): BetaAnalyticsDataClient
    {
        return new BetaAnalyticsDataClient([
            'credentials' => $config['service_account_credentials_json'],
        ]);
    }

    protected static function createAnalyticsClient(
        array $analyticsConfig,
        BetaAnalyticsDataClient $googleService
    ): AnalyticsClient {
        $client = new AnalyticsClient($googleService, app(Repository::class));

        $client->setCacheLifeTimeInMinutes($analyticsConfig['cache_lifetime_in_minutes']);

        return $client;
    }
}
