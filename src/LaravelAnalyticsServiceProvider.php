<?php

namespace Spatie\Analytics;

use Google_Client;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Spatie\Analytics\Exceptions\InvalidConfiguration;
use Spatie\Analytics\LaravelAnalytics;

class LaravelAnalyticsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/laravel-analytics.php' => config_path('laravel-analytics.php'),
        ]);
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->app->bind(LaravelAnalytics::class, function (Application $app) {

            $googleApiHelper = $this->getGoogleApiHelperClient();

            $laravelAnalytics = new LaravelAnalytics($googleApiHelper, config('laravel-analytics.siteId'));

            return $laravelAnalytics;
        });

        $this->app->alias(LaravelAnalytics::class, 'laravel-analytics');
    }

    /**
     * Return a GoogleApiHelper with given configuration.
     *
     * @return GoogleApiHelper
     *
     * @throws \Exception
     */
    protected function getGoogleApiHelperClient()
    {
        $this->guardAgainstInvalidConfiguration(config('laravel-analytics'));

        $client = $this->getGoogleClient();

        $googleApiHelper = (new GoogleApiHelper($client, app()->make('Illuminate\Contracts\Cache\Repository')))
            ->setCacheLifeTimeInMinutes(config('laravel-analytics.cacheLifetime'))
            ->setRealTimeCacheLifeTimeInMinutes(config('laravel-analytics.realTimeCacheLifetimeInSeconds'));

        return $googleApiHelper;
    }

    protected function guardAgainstInvalidConfiguration(array $analyticsConfig)
    {
        if (empty($analyticsConfig['site_id'])) {
            throw InvalidConfiguration::siteIdNotSpecified();
        }

        if (! starts_with($analyticsConfig['site_id'], 'ga:')) {
            throw InvalidConfiguration::siteIdNotValid($analyticsConfig['site_id']);
        }
        
        if (! file_exists($analyticsConfig['client_secret_json'])) {
            throw InvalidConfiguration::clientSecretJsonFileDoesNotExist();
        }
    }

    /**
     * Get a configured GoogleClient.
     *
     * @return Google_Client
     */
    protected function getGoogleClient()
    {
        $client = new Google_Client(
            [
                'oauth2_client_id' => config('laravel-analytics.clientId'),
                'use_objects' => true,
            ]
        );

        $client->setClassConfig('Google_Cache_File', 'directory', storage_path('app/laravel-analytics-cache'));

        $client->setAccessType('offline');

        $client->setAssertionCredentials(
            new \Google_Auth_AssertionCredentials(
                config('laravel-analytics.serviceEmail'),
                ['https://www.googleapis.com/auth/analytics.readonly'],
                file_get_contents(config('laravel-analytics.certificatePath'))
            )
        );

        return $client;
    }
}
