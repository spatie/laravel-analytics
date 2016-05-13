<?php

namespace Spatie\LaravelAnalytics;

use Google_Client;
use Illuminate\Support\ServiceProvider;

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
        $this->app->bind('Spatie\LaravelAnalytics\LaravelAnalytics', function ($app) {

            $googleApiHelper = $this->getGoogleApiHelperClient();

            $laravelAnalytics = new LaravelAnalytics($googleApiHelper, config('laravel-analytics.siteId'));

            return $laravelAnalytics;
        });

        $this->app->alias('Spatie\LaravelAnalytics\LaravelAnalytics', 'laravelAnalytics');
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
        $this->guardAgainstMissingP12();

        $client = $this->getGoogleClient();

        $googleApiHelper = (new GoogleApiHelper($client, app()->make('Illuminate\Contracts\Cache\Repository')))
            ->setCacheLifeTimeInMinutes(config('laravel-analytics.cacheLifetime'))
            ->setRealTimeCacheLifeTimeInMinutes(config('laravel-analytics.realTimeCacheLifetimeInSeconds'));

        return $googleApiHelper;
    }

    /**
     * Throw exception if .p12 file is not present in specified folder.
     *
     * @throws \Exception
     */
    protected function guardAgainstMissingP12()
    {
        if (!$this->app['files']->exists(config('laravel-analytics.certificatePath'))) {
            throw new \Exception("Can't find the .p12 certificate in: ".config('laravel-analytics.certificatePath'));
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
