<?php

namespace Spatie\Analytics;

use Google_Client;
use Illuminate\Support\ServiceProvider;
use Spatie\Analytics\Exceptions\InvalidConfiguration;

class AnalyticsServiceProvider extends ServiceProvider
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
        $analyticsConfig = config('laravel-analytics');

        $this->app->bind(AnalyticsClient::class, function () use ($analyticsConfig) {
            $client = app(Google_Client::class);

            return AnalyticsClientFactory::createForConfig($client, $analyticsConfig);
        });

        $this->app->bind(Analytics::class, function () use ($analyticsConfig) {
            $this->guardAgainstInvalidConfiguration($analyticsConfig);

            $client = app(AnalyticsClient::class);

            return new Analytics($client, $analyticsConfig['view_id']);
        });

        $this->app->alias(Analytics::class, 'laravel-analytics');
    }

    /**
     * @param array|null $analyticsConfig
     *
     * @throws \Spatie\Analytics\Exceptions\InvalidConfiguration
     */
    protected function guardAgainstInvalidConfiguration($analyticsConfig)
    {
        if (empty($analyticsConfig['view_id'])) {
            throw InvalidConfiguration::viewIdNotSpecified();
        }
    }
}
