<?php

namespace Spatie\Analytics;

use Google_Client;
use Google_Service_Analytics;

class AnalyticsFactory
{
    public static function createForConfig(array $config)
    {
        $authenticatedClient = self::getAuthenticatedGoogleClient($config);

        $service = new Google_Service_Analytics($authenticatedClient);
        
        

        return new Analytics($service, $config['calendarId']);
    }

    public static function getAuthenticatedGoogleClient($config): Google_Client
    {
        $client = new Google_Client();

        $credentials = $client->loadServiceAccountJson($config['client_secret_json'], 'https://www.googleapis.com/auth/analytics.readonly');

        $client->setAssertionCredentials($credentials);
        
        return $client;
    }

}