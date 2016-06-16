<?php

namespace Spatie\Analytics;

use Google_Client;
use Google_Service_Analytics;
use Illuminate\Contracts\Cache\Repository;

class AnalyticsFactory
{
    public static function createForConfig(array $config)
    {
        $authenticatedClient = self::getAuthenticatedGoogleClient($config);

        $googleService = new Google_Service_Analytics($authenticatedClient);

        $service = new Service($googleService, app(Repository::class));

        return new Analytics($service, $config['view_id']);
    }

    public static function getAuthenticatedGoogleClient($config): Google_Client
    {
        $client = new Google_Client();

        $credentials = $client->loadServiceAccountJson($config['client_secret_json'], 'https://www.googleapis.com/auth/analytics.readonly');

        $client->setAssertionCredentials($credentials);

        return $client;
    }
}
